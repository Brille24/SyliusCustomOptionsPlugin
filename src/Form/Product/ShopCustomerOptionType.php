<?php

/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form\Product;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValuePriceRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Services\ConstraintCreator;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ShopCustomerOptionType extends AbstractType
{
    private ChannelContextInterface $channelContext;

    private CurrencyContextInterface $currencyContext;

    private MoneyFormatterInterface $moneyFormatter;

    private LocaleContextInterface $localeContext;

    private CustomerOptionValuePriceRepositoryInterface $customerOptionValuePriceRepository;

    public function __construct(
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext,
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeContext,
        CustomerOptionValuePriceRepositoryInterface $customerOptionValuePriceRepository,
    ) {
        $this->channelContext = $channelContext;
        $this->currencyContext = $currencyContext;
        $this->moneyFormatter = $moneyFormatter;
        $this->localeContext = $localeContext;
        $this->customerOptionValuePriceRepository = $customerOptionValuePriceRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $product = $options['product'];
        $channel = $options['channel'];

        if (!$product instanceof ProductInterface) {
            return;
        }

        // Add a form field for every customer option
        $customerOptions = $product->getCustomerOptions();
        $customerOptionTypesByCode = [];

        foreach ($customerOptions as $customerOption) {
            $customerOptionType = $customerOption->getType();
            $fieldName = $customerOption->getCode();

            [$class, $formOptions] = CustomerOptionTypeEnum::getFormTypeArray()[$customerOptionType];

            $fieldConfig = $this->getFormConfiguration($formOptions, $customerOption, $product, $channel);
            $fieldConfig['mapped'] = $options['mapped'];

            $builder->add($fieldName, $class, $fieldConfig);

            $customerOptionTypesByCode[$fieldName] = $customerOptionType;
        }

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            static function (FormEvent $event): void {
                $data = $event->getData();
                $form = $event->getForm();
                if (!is_array($data)) {
                    return;
                }
                foreach ($data as $key => $value) {
                    $form->get($key)->setData($value);
                }
            },
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            static function (FormEvent $event) use ($customerOptionTypesByCode): void {
                $data = $event->getData();
                if (!is_array($data)) {
                    return;
                }
                foreach ($data as $customerOptionCode => $customerOptionValue) {
                    if (CustomerOptionTypeEnum::FILE === $customerOptionTypesByCode[$customerOptionCode]) {
                        $data[$customerOptionCode] = $customerOptionValue['data'];
                    }
                }

                $event->setData($data);
            },
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(['product', 'channel'])
            ->setAllowedTypes('product', ProductInterface::class)
            ->setDefault('mapped', false)
            ->setDefault('channel', $this->channelContext->getChannel())
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_product_shop_customer_option';
    }

    /**
     * Gets the settings for the form type based on the type that the form field is for
     */
    private function getFormConfiguration(
        array $formOptions,
        CustomerOptionInterface $customerOption,
        ProductInterface $product,
        ChannelInterface $channel,
    ): array {
        $defaultOptions = [
            'label' => $customerOption->getName(),
            'required' => $customerOption->isRequired(),
        ];

        $configuration = [];

        // Adding choices if it is a select (or multi-select)
        $customerOptionType = $customerOption->getType();
        if (CustomerOptionTypeEnum::isSelect($customerOptionType)) {
            $configuration = [
                'choices' => $customerOption->getValues()->toArray(),
                'choice_label' => fn (CustomerOptionValueInterface $value): string => $this->buildValueString($value, $product, $channel),
                'choice_value' => 'code',
            ];
        }

        $constraint = ConstraintCreator::createFromConfiguration(
            $customerOptionType,
            $customerOption->getConfiguration(),
        );

        if ($constraint !== null) {
            $constraint->groups = ['sylius'];
            $configuration = ['constraints' => [$constraint]];
        }

        if ($customerOption->isRequired() && $customerOptionType !== CustomerOptionTypeEnum::FILE) {
            /** @var NotBlank $requiredConstraint */
            $requiredConstraint = ConstraintCreator::createRequiredConstraint();
            $requiredConstraint->message = 'brille24.form.customer_options.required';

            $requiredConstraint->groups = ['sylius'];
            $configuration['constraints'][] = $requiredConstraint;
        }

        if ($customerOptionType === CustomerOptionTypeEnum::FILE) {
            /*
             * Here we give the Customer Option File Type a special block name to override it in a form theme.
             *
             * The reason for this is when submitting the form on the "add to cart" button files are not transmitted.
             * Therefore we added a hidden field and transmit the base64 encoding of the file.
             */
            $configuration['block_name'] = 'file_type';
        }

        return array_merge($formOptions, $defaultOptions, $configuration);
    }

    /**
     * @throws \Exception
     */
    private function buildValueString(
        CustomerOptionValueInterface $value,
        ProductInterface $product,
        ChannelInterface $channel,
    ): string {
        $price = $this->customerOptionValuePriceRepository->getPriceForChannel($channel, $product, $value);

        // No price was found for the current channel, probably because the values weren't updated after adding a new channel
        if ($price === null) {
            throw new \Exception(
                sprintf(
                    'CustomerOptionValue (%s) has no price defined for Channel (%s)',
                    $value->getCode(),
                    $channel->getCode(),
                ),
            );
        }

        $valueString = $price->getValueString(
            $this->currencyContext->getCurrencyCode(),
            $this->localeContext->getLocaleCode(),
            $this->moneyFormatter,
        );
        $name = $value->getName();

        return "{$name} ($valueString)";
    }
}

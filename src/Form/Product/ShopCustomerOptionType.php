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
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
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
    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var CurrencyContextInterface */
    private $currencyContext;

    /** @var MoneyFormatterInterface */
    private $moneyFormatter;

    /** @var LocaleContextInterface */
    private $localeContext;

    public function __construct(
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext,
        MoneyFormatterInterface $moneyFormatter,
        LocaleContextInterface $localeContext
    ) {
        $this->channelContext  = $channelContext;
        $this->currencyContext = $currencyContext;
        $this->moneyFormatter  = $moneyFormatter;
        $this->localeContext   = $localeContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $product = $options['product'];

        if (!$product instanceof ProductInterface) {
            return;
        }

        // Add a form field for every customer option
        $customerOptions = $product->getCustomerOptions();

        foreach ($customerOptions as $customerOption) {
            $customerOptionType = $customerOption->getType();
            $fieldName          = $customerOption->getCode();

            [$class, $formOptions] = CustomerOptionTypeEnum::getFormTypeArray()[$customerOptionType];

            $fieldConfig           = $this->getFormConfiguration($formOptions, $customerOption, $product);
            $fieldConfig['mapped'] = $options['mapped'];

            $builder->add($fieldName, $class, $fieldConfig);
        }

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                if (!is_array($data)) {
                    return;
                }
                foreach ($data as $key => $value) {
                    $form->get($key)->setData($value);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(['product'])
            ->setAllowedTypes('product', ProductInterface::class)
            ->setDefault('mapped', false);
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_product_shop_customer_option';
    }

    /**
     * Gets the settings for the form type based on the type that the form field is for
     *
     * @param array                   $formOptions
     * @param CustomerOptionInterface $customerOption
     * @param ProductInterface        $product
     *
     * @return array
     */
    private function getFormConfiguration(
        array $formOptions,
        CustomerOptionInterface $customerOption,
        ProductInterface $product
    ): array {
        $defaultOptions = [
            'label'    => $customerOption->getName(),
            'required' => $customerOption->isRequired(),
        ];

        $configuration = [];

        // Adding choices if it is a select (or multi-select)
        $customerOptionType = $customerOption->getType();
        if (CustomerOptionTypeEnum::isSelect($customerOptionType)) {
            $configuration = [
                'choices'      => $customerOption->getValues()->toArray(),
                'choice_label' => function (CustomerOptionValueInterface $value) use ($product) {
                    return $this->buildValueString($value, $product);
                },
                'choice_value' => 'code',
            ];
        } else {
            $constraint = ConstraintCreator::createFromConfiguration(
                $customerOptionType,
                $customerOption->getConfiguration()
            );

            if ($constraint !== null) {
                $constraint->groups = ['sylius'];
                $configuration      = ['constraints' => [$constraint]];
            }

            if ($customerOption->isRequired()) {
                /** @var NotBlank $requiredConstraint */
                $requiredConstraint          = ConstraintCreator::createRequiredConstraint();
                $requiredConstraint->message = 'brille24.form.customer_options.required';

                $requiredConstraint->groups     = ['sylius'];
                $configuration['constraints'][] = $requiredConstraint;
            }
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
     * @param CustomerOptionValueInterface $value
     * @param ProductInterface             $product
     *
     * @return string
     *
     * @throws \Exception
     */
    private function buildValueString(CustomerOptionValueInterface $value, ProductInterface $product)
    {
        /** @var CustomerOptionValuePriceInterface $price */
        $price = null;

        /** @var CustomerOptionValuePriceInterface $productPrice */
        foreach ($product->getCustomerOptionValuePrices() as $productPrice) {
            if (
                $productPrice->getCustomerOptionValue() === $value
                && $productPrice->getChannel() === $this->channelContext->getChannel()
            ) {
                $price = $productPrice;

                break;
            }
        }

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $price   = $price ?? $value->getPriceForChannel($channel);

        // No price was found for the current channel, probably because the values weren't updated after adding a new channel
        if ($price === null) {
            throw new \Exception(
                sprintf(
                    'CustomerOptionValue (%s) has no price defined for Channel (%s)',
                    $value->getCode(),
                    $this->channelContext->getChannel()->getCode()
                )
            );
        }

        $valueString = $price->getValueString(
            $this->currencyContext->getCurrencyCode(),
            $this->localeContext->getLocaleCode(),
            $this->moneyFormatter
        );
        $name        = $value->getName();

        return "{$name} ($valueString)";
    }
}

<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 14.02.18
 * Time: 10:16
 */

namespace Brille24\CustomerOptionsPlugin\Form\Product;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShopCustomerOptionType extends AbstractType
{
    private $channelContext;

    public function __construct(ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Brille24\CustomerOptionsPlugin\Entity\ProductInterface $product */
        $product = $options['product'];

        if (!$product instanceof ProductInterface) {
            return;
        }

        // Add a form field for every customer option
        foreach ($product->getCustomerOptions() as $customerOption) {
            $customerOptionType = $customerOption->getType();
            $fieldName          = $customerOption->getCode();

            [$class, $formOptions] = CustomerOptionTypeEnum::getFormTypeArray()[$customerOptionType];

            $builder->add($fieldName, $class, $this->getFormConfiguration($formOptions, $customerOption, $product));
        }
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
     * @param $formOptions
     * @param $customerOption
     *
     * @return array
     */
    private function getFormConfiguration(array $formOptions, CustomerOptionInterface $customerOption, ProductInterface $product): array
    {
        $defaultOptions = [
            'mapped' => false,
            'required' => $customerOption->isRequired(),
        ];

        // Adding choices if it is a select (or multi-select)
        $choices = [];
        if (CustomerOptionTypeEnum::isSelect($customerOption->getType())) {
            $choices = [
                'choices' => $customerOption->getValues()->toArray(),
                'choice_label' => function (CustomerOptionValueInterface $value) use ($product) {
                    return $this->buildValueString($value, $product);
                },
                'choice_value' => 'code',
            ];
        }

        return array_merge($formOptions, $defaultOptions, $choices);
    }

    private function buildValueString(CustomerOptionValueInterface $value, ProductInterface $product){
        /** @var CustomerOptionValuePriceInterface $price */
        $price = null;

        /** @var CustomerOptionValuePriceInterface $productPrice */
        foreach ($product->getCustomerOptionValuePrices() as $productPrice){
            if(
                $productPrice->getCustomerOptionValue() === $value &&
                $productPrice->getChannel() === $this->channelContext->getChannel()
            ){
                $price = $productPrice;
                break;
            }
        }

        if($price === null) {
            $prices = $value->getPrices();

            foreach ($prices as $defaultPrice) {
                if ($defaultPrice->getChannel() === $this->channelContext->getChannel()) {
                    $price = $defaultPrice;
                    break;
                }
            }
        }

        return "{$value} ({$price})";
    }
}

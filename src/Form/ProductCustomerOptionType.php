<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 14.02.18
 * Time: 10:16
 */

namespace Brille24\CustomerOptionsPlugin\Form;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductCustomerOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Brille24\CustomerOptionsPlugin\Entity\ProductInterface $product */
        $product = $options['product'];

        if (!$product instanceof ProductInterface) {
            return;
        }

        // Add a form field for every customer option
        foreach ($product->getCustomerOptions() as $customerOption) {
            $customerOptionType = $customerOption->getType();
            $fieldName          = $this->generateFieldName($customerOption);

            list($class, $formOptions) = CustomerOptionTypeEnum::getFormTypeArray()[$customerOptionType];

            $builder->add($fieldName, $class, $this->getFormConfiguration($formOptions, $customerOption));

        }
    }

    private function generateFieldName(CustomerOptionInterface $customerOption): string
    {
        return 'customer_option_' . $customerOption->getCode();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefined(['product'])
            ->setAllowedTypes('product', ProductInterface::class)
            ->setDefault('mapped', false);
    }

    public function getBlockPrefix()
    {
        return 'brille24_product_customer_option';
    }

    /**
     * @param $formOptions
     * @param $customerOption
     *
     * @return array
     */
    private function getFormConfiguration(array $formOptions, CustomerOptionInterface $customerOption): array
    {
        $defaultOptions = [
            'mapped'   => false,
            'required' => $customerOption->isRequired(),
        ];

        // Adding choices if it is a select (or multi-select)
        $choices = [];
        if (CustomerOptionTypeEnum::isSelect($customerOption->getType())) {
          $choices = [
              'choices' => $customerOption->getValues()->toArray(),
              'choice_label' => 'name',
              'choice_value' => 'code',
          ];
        }

        return array_merge($formOptions, $defaultOptions, $choices);
    }
}
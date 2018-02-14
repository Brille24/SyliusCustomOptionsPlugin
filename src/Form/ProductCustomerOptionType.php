<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 14.02.18
 * Time: 10:16
 */

namespace Brille24\CustomerOptionsPlugin\Form;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
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

        if($product !== null && $product->getCustomerOptionGroup() instanceof CustomerOptionGroupInterface){
            foreach ($product->getCustomerOptionGroup()->getOptionAssociations() as $customerOptionAssociation)
            {
                $customerOption = $customerOptionAssociation->getOption();
                $builder->add(
                    'customer_option_' . $customerOption->getCode(),
                    CustomerOptionTypeEnum::getFormTypeArray()[$customerOption->getType()][0],
                    array_merge(
                        CustomerOptionTypeEnum::getFormTypeArray()[$customerOption->getType()][1],
                        [
                            'mapped' => false,
                            'required' => $customerOption->isRequired(),
                        ]
                    )
                );
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefined([
                'product',
            ])
            ->setAllowedTypes('product', ProductInterface::class)
            ->setDefault('mapped', false)
        ;
    }

    public function getBlockPrefix()
    {
        return 'brille24_product_customer_option';
    }
}
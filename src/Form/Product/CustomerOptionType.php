<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 14.02.18
 * Time: 11:36
 */

namespace Brille24\CustomerOptionsPlugin\Form\Product;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['readonly' => true],
                'label' => false,
            ])
            ->add('values', CollectionType::class, [
                'entry_type' => CustomerOptionValueType::class,
                'label' => false,
            ])
        ;

//        /** @var CustomerOptionInterface[] $customerOptions */
//        $customerOptions = $options['data'];
//
//        foreach ($customerOptions as $option){
//            if($option->getType() === CustomerOptionTypeEnum::SELECT || $option->getType() === CustomerOptionTypeEnum::MULTI_SELECT){
//                $builder
//                    ->add('customerOptionValues' . $option->getCode(), CollectionType::class, [
//                        'entry_type' => CustomerOptionValueType::class,
//                    ])
//                ;
//            }
//        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerOption::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'brille24_product_customer_option';
    }
}
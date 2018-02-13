<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 13.02.18
 * Time: 09:40
 */

namespace Brille24\CustomerOptionsPlugin\Form;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerOptionAssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('option', CustomerOptionChoiceType::class, [
//                'by_reference' => false,
            ])
//            ->add('option', EntityType::class, [
//                'class' => CustomerOption::class,
//                'placeholder' => 'brille24.form.customer_option_groups.select',
//                'choice_label' => function($option){
//                    return $option->getName();
//                },
//                'by_reference' => false,
//            ])
            ->add('position', IntegerType::class, [
                'data' => 0,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'error_bubbling' => true,
            'data_class' => CustomerOptionAssociation::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'brille24_customer_option_association_choice';
    }
}
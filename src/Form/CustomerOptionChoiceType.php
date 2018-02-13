<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 13.02.18
 * Time: 09:40
 */

namespace Brille24\CustomerOptionsPlugin\Form;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerOptionChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $customerOptionRepository;

    public function __construct(RepositoryInterface $customerOptionRepository)
    {
        $this->customerOptionRepository = $customerOptionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => CustomerOption::class,
                'choices' => $this->customerOptionRepository->findAll(),
                'choice_label' => function($customerOption){
                    return $customerOption->getName();
                },
                'choice_value' => function($customerOption){
                    if($customerOption === null){
                        return '';
                    }

                    return $customerOption->getId();
                },
                'placeholder' => 'brille24.form.customer_option_groups.select',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'brille24_customer_option_choice';
    }
}
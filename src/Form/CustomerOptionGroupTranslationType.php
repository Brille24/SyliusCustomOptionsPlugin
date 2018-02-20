<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;


use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CustomerOptionGroupTranslationType extends AbstractResourceType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, ['label' => 'sylius.form.product.name',]);

    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_option_groups_translation';
    }
}
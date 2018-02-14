<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;


use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerOptionValueTranslationType extends AbstractResourceType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class);
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_option_value_translation';
    }

}
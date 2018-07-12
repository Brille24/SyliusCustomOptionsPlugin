<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ErrorMessage;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ErrorMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('translations', ResourceTranslationsType::class, [
            'entry_type' => ErrorMessageTranslationType::class,
            'label'      => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ErrorMessage::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_option_error_message';
    }
}

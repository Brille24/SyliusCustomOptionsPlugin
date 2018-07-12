<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form\Validator;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ErrorMessageTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('message', TextType::class, [
            'label' => 'brille24.form.validators.fields.message',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_option_error_message_translation';
    }
}

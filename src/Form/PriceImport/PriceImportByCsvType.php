<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PriceImportByCsvType extends AbstractType
{
    private DataMapperInterface $dataMapper;

    public function __construct(DataMapperInterface $dataMapper)
    {
        $this->dataMapper = $dataMapper;
    }

    /** @inheritdoc */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'sylius.ui.choose_file',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'brille24.form.customer_options.import',
                'attr' => [
                    'class' => 'ui primary button',
                ],
            ])
        ;

        $builder->setDataMapper($this->dataMapper);
    }
}

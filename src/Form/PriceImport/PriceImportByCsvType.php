<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport;

use Brille24\SyliusCustomerOptionsPlugin\Reader\CsvReaderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PriceImportByCsvType extends AbstractType
{
    /** @var CsvReaderInterface */
    protected $csvReader;

    public function __construct(CsvReaderInterface $csvReader)
    {
        $this->csvReader = $csvReader;
    }

    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'sylius.ui.choose_file',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'brille24.form.customer_options.import',
                'attr'  => [
                    'class' => 'ui primary button',
                ],
            ])
        ;

        $builder->addModelTransformer(new CallbackTransformer(
            static function() {
                return null;
            },
            static function($formData) {
                return $this->csvReader->readCsv($formData['file']->getRealPath());
            }
        ));
    }
}

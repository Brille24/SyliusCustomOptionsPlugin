<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\DataMapper;

use Brille24\SyliusCustomerOptionsPlugin\Reader\CsvReaderInterface;
use Symfony\Component\Form\DataMapperInterface;

class CsvPriceImportDataMapper implements DataMapperInterface
{
    protected CsvReaderInterface $csvReader;

    public function __construct(CsvReaderInterface $csvReader)
    {
        $this->csvReader = $csvReader;
    }

    /** {@inheritdoc} */
    public function mapDataToForms($viewData, $forms): void
    {
    }

    /** {@inheritdoc} */
    public function mapFormsToData($forms, &$viewData): void
    {
        /** @var \Traversable $forms */
        $formData = iterator_to_array($forms);
        $viewData = $this->csvReader->readCsv($formData['file']->getData()->getRealPath());
    }
}

<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

use Symfony\Component\Form\FormInterface;

interface CustomerOptionPriceFormImporterInterface
{
    /**
     * @param FormInterface $form
     *
     * @return array
     */
    public function importForProductListForm(
        FormInterface $form
    ): array;
}

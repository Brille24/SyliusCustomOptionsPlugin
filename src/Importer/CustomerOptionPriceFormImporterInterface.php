<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRange;
use Sylius\Component\Core\Model\ChannelInterface;
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

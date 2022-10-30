<?php

/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Form\Product\ProductCustomerOptionValuePriceTypeTrait;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerOptionValuePriceType extends AbstractType
{
    use ProductCustomerOptionValuePriceTypeTrait;

    protected CustomerOptionRepositoryInterface $customerOptionRepository;

    public function __construct(CustomerOptionRepositoryInterface $customerOptionRepository)
    {
        $this->customerOptionRepository = $customerOptionRepository;
    }

    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $values = [];
        /** @var CustomerOptionInterface $customerOption */
        foreach ($this->customerOptionRepository->findAll() as $customerOption) {
            if (CustomerOptionTypeEnum::isSelect($customerOption->getType())) {
                $values = [...$values, ...$customerOption->getValues()->getValues()];
            }
        }

        $this->addValuePriceFields($builder, $values);

        $builder->remove('customerOptionValue');
        $builder
            ->add('customerOptionValues', ChoiceType::class, [
                'choices'      => $values,
                'choice_label' => 'name',
                'group_by'     => static function (CustomerOptionValueInterface $customerOptionValue): ?string {

                    /** @var CustomerOptionInterface $customerOption */
                    $customerOption = $customerOptionValue->getCustomerOption();

                    return $customerOption->getName();
                },
                'multiple' => true,
            ])
        ;
    }

    /** {@inheritdoc} */
    public function getBlockPrefix(): string
    {
        return 'brille24_product_value_price';
    }
}

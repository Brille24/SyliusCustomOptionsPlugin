<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport;

use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductListType extends AbstractType
{
    /** @var ProductRepositoryInterface */
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('products', TextType::class)

        ;
    }
}

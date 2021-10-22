<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Twig;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CustomerOptionInUseFunction extends AbstractExtension
{
    /** @var RepositoryInterface */
    private $orderItemOptionRepository;

    public function __construct(RepositoryInterface $orderItemOptionRepository)
    {
        $this->orderItemOptionRepository = $orderItemOptionRepository;
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('customer_option_in_use', [$this, 'customerOptionIsInUse'])];
    }

    public function customerOptionIsInUse(CustomerOptionInterface $customerOption): bool
    {
        $orderItems = $this->orderItemOptionRepository
            ->findBy(['customerOption' => $customerOption], null, 1);

        return count($orderItems) > 0;
    }
}

<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Repository;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

class CustomerOptionValuePriceRepository extends EntityRepository implements CustomerOptionValuePriceRepositoryInterface
{
    /**
     * @param ChannelInterface $channel
     * @param ProductInterface $product
     * @param CustomerOptionValueInterface $customerOptionValue
     *
     * @throws NonUniqueResultException
     */
    public function getPriceForChannel(
        ChannelInterface $channel,
        ProductInterface $product,
        CustomerOptionValueInterface $customerOptionValue
    ): ?CustomerOptionValuePriceInterface {
        $qb = $this->createQueryBuilder('price');
        $qb->where('price.channel = :channel');
        $qb->andWhere('price.customerOptionValue = :customerOptionValue');

        $qb->andWhere('(price.product IS NOT NULL AND price.product = :product) OR price.product IS NULL');

        // If a product price overwrite exists the result could contain multiple entries. Therefore we order the
        // overwritten price to the top to get it as the only result.
        $qb->orderBy('price.product', 'DESC');
        $qb->setMaxResults(1);

        $qb->setParameter('channel', $channel);
        $qb->setParameter('customerOptionValue', $customerOptionValue);
        $qb->setParameter('product', $product);

        $query = $qb->getQuery();

        /** @var CustomerOptionValuePriceInterface|null $result */
        $result = $query->getOneOrNullResult();
        Assert::nullOrIsInstanceOf($result, CustomerOptionValuePriceInterface::class);

        return $result;
    }
}

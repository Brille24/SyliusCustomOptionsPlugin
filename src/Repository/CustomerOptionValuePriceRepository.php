<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Repository;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

class CustomerOptionValuePriceRepository extends EntityRepository implements CustomerOptionValuePriceRepositoryInterface
{
    /**
     * @throws NonUniqueResultException
     *
     * @param ChannelInterface $channel
     * @param ProductInterface $product
     * @param CustomerOptionValueInterface $customerOptionValue
     * @param bool $ignoreActive
     */
    public function getPriceForChannel(
        ChannelInterface $channel,
        ProductInterface $product,
        CustomerOptionValueInterface $customerOptionValue,
        bool $ignoreActive = false
    ): ?CustomerOptionValuePriceInterface {
        $qb = $this->createQueryBuilder('price');
        $qb->where('price.channel = :channel');
        $qb->andWhere('price.customerOptionValue = :customerOptionValue');

        if (!$ignoreActive) {
            $qb->leftJoin('price.dateValid', 'dr');
            $qb->andWhere('price.dateValid IS NULL OR (:now >= dr.start AND :now <= dr.end)');
            $qb->setParameter('now', new DateTime());
        }

        $qb->andWhere('(price.product IS NOT NULL AND price.product = :product) OR price.product IS NULL');

        $qb->orderBy('price.product', 'DESC');
        $qb->setMaxResults(1);

        $qb->setParameter('channel', $channel);
        $qb->setParameter('customerOptionValue', $customerOptionValue);
        $qb->setParameter('product', $product);

        $query = $qb->getQuery();

        $sql = $query->getSQL();

        /** @var CustomerOptionValuePriceInterface|null $result */
        $result = $query->getOneOrNullResult();
        Assert::nullOrIsInstanceOf($result, CustomerOptionValuePriceInterface::class);

        return $result;
    }
}

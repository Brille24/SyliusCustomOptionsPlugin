<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

class CustomerOptionValuePrice implements CustomerOptionValuePriceInterface
{
    /** @var int|null */
    private $id;

    /** @var float */
    private $percent = 0;

    /** @var int */
    private $amount = 0;

    /** @var string */
    private $type = CustomerOptionValuePriceInterface::TYPE_FIXED_AMOUNT;

    /** @var CustomerOptionValueInterface|null */
    private $customerOptionValue;

    /** @var Collection */
    private $product;

    public function __construct()
    {
        $this->percent = 0;
        $this->amount = 0;
        $this->type = self::getAllTypes()[0];
    }

    /** {@inheritdoc} */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    public function getPercent(): float
    {
        return $this->percent;
    }

    /** {@inheritdoc} */
    public function setPercent(float $percent): void
    {
        $this->percent = $percent;
    }

    /** {@inheritdoc} */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /** {@inheritdoc} */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /** {@inheritdoc} */
    public function getType(): ?string
    {
        return $this->type;
    }

    /** {@inheritdoc} */
    public function setType(string $type): void
    {
        $allTypes = $this->getAllTypes();

        if (in_array($type, $allTypes)) {
            $this->type = $type;
        } else {
            throw new InvalidTypeException('Invalid type. Possible types are ' . join(', ', $allTypes));
        }
    }

    /** {@inheritdoc} */
    public static function getAllTypes(): array
    {
        return [
            CustomerOptionValuePriceInterface::TYPE_FIXED_AMOUNT,
            CustomerOptionValuePriceInterface::TYPE_PERCENT,
        ];
    }

    /** {@inheritdoc} */
    public function getCustomerOptionValue(): ?CustomerOptionValueInterface
    {
        return $this->customerOptionValue;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionValue(?CustomerOptionValueInterface $customerOptionValue): void
    {
        $this->customerOptionValue = $customerOptionValue;
    }

    public function __toString(): string
    {
        if ($this->getType() === CustomerOptionValuePriceInterface::TYPE_FIXED_AMOUNT) {
            return "{$this->getAmount()} EUR";
        }
        return "{$this->getPercent()}%";
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerOptionValueName(): ?string
    {
        return $this->customerOptionValue->getName();
    }

    public function getProducts(): ?Collection
    {
        return $this->product;
    }

    public function setProducts(?Collection $product): void
    {
        $this->product = $product;
    }

}
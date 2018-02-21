<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

class CustomerOptionValuePrice implements CustomerOptionValuePriceInterface
{
    /** @var int|null */
    private $id;

    /** @var float */
    private $percent;

    /** @var int */
    private $amount;

    /** @var string */
    private $type;

    /** @var CustomerOptionValueInterface|null */
    private $customerOptionValue;

    /** @var ChannelInterface */
    private $channel;

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
            CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
            CustomerOptionValuePrice::TYPE_PERCENT,
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

    /** {@inheritdoc} */
    public function getCustomerOptionValueName(): ?string
    {
        return $this->customerOptionValue->getName();
    }

    /** {@inheritdoc} */
    public function setChannel(ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    /** {@inheritdoc} */
    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

}
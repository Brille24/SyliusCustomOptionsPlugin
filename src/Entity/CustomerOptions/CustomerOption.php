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

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class CustomerOption implements CustomerOptionInterface
{
    use TranslatableTrait {
        __construct as protected initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var int|null */
    private $id;

    /** @var string */
    private $type = CustomerOptionTypeEnum::SELECT;

    /** @var string */
    private $code = '';

    /** @var bool|null */
    private $required = false;

    /** @var Collection|CustomerOptionValueInterface[] */
    private $values;

    /** @var array */
    private $configuration = [];

    /** @var ArrayCollection */
    private $groupAssociations;

    /** @var OrderItemOptionInterface */
    private $orders;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->values            = new ArrayCollection();
        $this->groupAssociations = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(?string $type): void
    {
        if (!CustomerOptionTypeEnum::isValid($type) || $type === null) {
            throw new \Exception('Invalid type');
        }

        $this->type = $type;

        if (CustomerOptionTypeEnum::isSelect($type)) {
            $this->configuration = [];
        } else {
            $this->configuration = CustomerOptionTypeEnum::getConfigurationArray()[$type];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function getTypeCode(): ?string
    {
        $type         = $this->getType();
        $translations = CustomerOptionTypeEnum::getTranslateArray();
        if (array_key_exists($type, $translations)) {
            return $translations[$type];
        }

        return $this->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired(): ?bool
    {
        return $this->required;
    }

    //region Getter and setter for value

    /**
     * {@inheritdoc}
     */
    public function getValues(): Collection
    {
        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function addValue(CustomerOptionValueInterface $value): void
    {
        $this->values->add($value);
        $value->setCustomerOption($this);
    }

    /**
     * {@inheritdoc}
     */
    public function removeValue(CustomerOptionValueInterface $value): void
    {
        $this->values->removeElement($value);
        $value->setCustomerOption(null);
    }

    /**
     * {@inheritdoc}
     */
    public function setValues(array $values): void
    {
        $this->values->clear();
        foreach ($values as $value) {
            $this->addValue($value);
        }
    }

    //endregion

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $configuration): void
    {
        // Setting the new values
        foreach ($configuration as $key => $value) {
            $optionKey                                = str_replace(':', '.', $key);
            $this->configuration[$optionKey]['value'] = $value;
        }

        // Removing the configs of the previous type
        foreach ($this->configuration as $key => $configOption) {
            if (!isset($configOption['type'])) {
                unset($this->configuration[$key]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupAssociations(): ArrayCollection
    {
        return $this->groupAssociations;
    }

    /**
     * {@inheritdoc}
     */
    public function setGroupAssociations(ArrayCollection $assoc): void
    {
        $this->groupAssociations = $assoc;
    }

    /**
     * {@inheritdoc}
     */
    public function addGroupAssociation(CustomerOptionAssociationInterface $assoc): void
    {
        $this->groupAssociations->add($assoc);
        $assoc->setOption($this);
    }

    /**
     * {@inheritdoc}
     */
    public function removeGroupAssociation(CustomerOptionAssociationInterface $assoc): void
    {
        $this->groupAssociations->removeElement($assoc);
        $assoc->setOption(null);
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrices(): array
    {
        $prices = [];

        foreach ($this->values as $value) {
            $prices = array_merge($prices, $value->getPrices()->toArray());
        }

        return $prices;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrices(array $prices)
    {
    }

    /**
     * @param string|null $locale
     *
     * @return CustomerOptionTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var CustomerOptionTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    /**
     * @return OrderItemOptionInterface
     */
    public function getOrders(): OrderItemOptionInterface
    {
        return $this->orders;
    }

    /**
     * @return CustomerOptionTranslationInterface
     */
    protected function createTranslation(): CustomerOptionTranslationInterface
    {
        return new CustomerOptionTranslation();
    }
}

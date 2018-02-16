<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 10:03
 */

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
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

    /** @var null|int */
    private $id;

    /** @var null|string */
    private $type;

    /** @var null|string */
    private $code;

    /** @var null|bool */
    private $required;

    /** @var Collection|CustomerOptionValueInterface[] */
    private $values;

    /** @var array */
    private $configuration = [];

    /** @var ArrayCollection */
    private $groupAssociations;


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
    public function setType(?string $type)
    {
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
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): ?string
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
    }

    /**
     * {@inheritdoc}
     */
    public function setValues(array $values): void
    {
        $this->values = new ArrayCollection($values);
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        // Setting the new values
        foreach ($configuration as $key => $value) {
            $optionKey                                = str_replace('_', '.', $key);
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
    public function setGroupAssociations(CustomerOptionAssociationInterface $assoc): void
    {
        $this->groupAssociations = $assoc;
        $assoc->setOption($this);
    }

    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
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
     * @return CustomerOptionTranslationInterface
     */
    protected function createTranslation(): CustomerOptionTranslationInterface
    {
        return new CustomerOptionTranslation();
    }
}
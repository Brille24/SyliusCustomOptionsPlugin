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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ValidatorInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerOptionGroupRepository::class)]
#[ORM\Table(name: 'brille24_customer_option_group')]
class CustomerOptionGroup implements CustomerOptionGroupInterface, \Stringable
{
    use TranslatableTrait {
        __construct as protected initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var int */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    protected $id;

    /** @var string|null */
    #[ORM\Column(type: 'string', nullable: true)]
    protected $code;

    /** @var Collection */
    #[ORM\OneToMany(mappedBy: 'group', targetEntity: CustomerOptionAssociationInterface::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    protected $optionAssociations;

    /** @var ArrayCollection */
    #[ORM\OneToMany(mappedBy: 'customerOptionGroup', targetEntity: ProductInterface::class)]
    protected $products;

    /** @var ArrayCollection */
    #[ORM\OneToMany(mappedBy: 'customerOptionGroup', targetEntity: ValidatorInterface::class, cascade: ['persist', 'remove'])]
    protected $validators;

    public function __construct()
    {
        $this->optionAssociations = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->validators = new ArrayCollection();
        $this->initializeTranslationsCollection();
    }

    /** @inheritdoc */
    public function getId(): ? int
    {
        return $this->id;
    }

    /** @inheritdoc */
    public function getCode(): ? string
    {
        return $this->code;
    }

    /** @inheritdoc */
    public function setCode(? string $code): void
    {
        $this->code = $code;
    }

    /** @inheritdoc */
    public function getName(): ? string
    {
        /** @var CustomerOptionGroupTranslationInterface $translations */
        $translations = $this->getTranslation();

        return $translations->getName();
    }

    /** @inheritdoc */
    public function setName(? string $name): void
    {
        /** @var CustomerOptionGroupTranslationInterface $translations */
        $translations = $this->getTranslation();
        $translations->setName($name);
    }

    //<editor-fold "Customer option association">

    /** @inheritdoc */
    public function getOptionAssociations(): Collection
    {
        return $this->optionAssociations;
    }

    /** @inheritdoc */
    public function addOptionAssociation(CustomerOptionAssociationInterface $association): void
    {
        $this->optionAssociations->add($association);
        $association->setGroup($this);
    }

    /** @inheritdoc */
    public function removeOptionAssociation(CustomerOptionAssociationInterface $association): void
    {
        $this->optionAssociations->removeElement($association);
        $association->setGroup(null);
    }

    /** @inheritdoc */
    public function hasOptionAssociations(): bool
    {
        return !$this->optionAssociations->isEmpty();
    }

    /**
     * Returns the first options of the group
     *
     * @return CustomerOptionInterface[]
     */
    public function getOptions(): array
    {
        return $this->optionAssociations
            ->map(static fn (CustomerOptionAssociationInterface $association): ?CustomerOptionInterface => $association->getOption())->toArray();
    }

    //</editor-fold>

    /** @inheritdoc */
    public function getProducts(): array
    {
        return $this->products->getValues();
    }

    /** @inheritdoc */
    public function setProducts(array $products): void
    {
        $this->products->clear();
        foreach ($products as $product) {
            $this->addProduct($product);
        }
    }

    /** @inheritdoc */
    public function addProduct(ProductInterface $product): void
    {
        $this->products->add($product);
        $product->setCustomerOptionGroup($this);
    }

    /** @inheritdoc */
    public function getValidators(): Collection
    {
        return $this->validators;
    }

    /** @inheritdoc */
    public function addValidator(ValidatorInterface $validator): void
    {
        $this->validators->add($validator);
        $validator->setCustomerOptionGroup($this);
    }

    /** @inheritdoc */
    public function removeValidator(ValidatorInterface $validator): void
    {
        $this->validators->removeElement($validator);
        $validator->setCustomerOptionGroup(null);
    }

    //<editor-fold "Translations">

    public function getTranslation(? string $locale = null): TranslationInterface
    {
        /** TranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    public function createTranslation(): CustomerOptionGroupTranslationInterface
    {
        return new CustomerOptionGroupTranslation();
    }

    //</editor-fold>
    public function __toString(): string
    {
        return (string) $this->getName();
    }
}

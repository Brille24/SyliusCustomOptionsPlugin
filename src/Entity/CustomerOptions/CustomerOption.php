<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 10:03
 */

namespace Brille24\CustomerOptionsBundle\Entity\CustomerOptions;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TranslatableTrait;

class CustomerOption implements CustomerOptionInterface
{
    use TranslatableTrait{
        __construct as protected initializeTranslationsCollection;
    }

    /** @var null|int */
    protected $id;

    /** @var null|string */
    protected $type;

    /** @var null|string */
    protected $code;

    /** @var bool */
    protected $required;

    /** @var Collection|CustomerOptionValueInterface[] */
    protected $values;

    /** @var CustomerOptionAssociationInterface */
    protected $groupAssociation;


    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->values = new ArrayCollection();
        $this->groups = new ArrayCollection();
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
    public function setRequired(bool $required)
    {
        $this->required = $required;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function addValue($value)
    {
        $this->values->add($value);
    }

    /**
     * {@inheritdoc}
     */
    public function removeValue($value)
    {
        $this->values->removeElement($value);
    }

    /**
     * {@inheritdoc}
     */
    public function setValues($values): void
    {
        $this->values = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupAssociation()
    {
        return $this->groupAssociation;
    }

    /**
     * {@inheritdoc}
     */
    public function setGroupAssociation($assoc): void
    {
        $this->groupAssociation = $assoc;
    }

    /**
     * @return CustomerOptionTranslationInterface
     */
    protected function createTranslation(): CustomerOptionTranslationInterface
    {
        return new CustomerOptionTranslation();
    }
}
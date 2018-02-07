<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 10:03
 */

namespace Brille24\CustomerOptionsBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

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

    /** @var null|string */
    protected $name;

    /** @var bool */
    protected $required;

    /** @var Collection */
    protected $values;

    /** @var Collection */
    protected $groups;


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
    public function setName(?string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
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
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup($group)
    {
        $this->groups->add($group);
    }

    /**
     * {@inheritdoc}
     */
    public function removeGroup($group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * {@inheritdoc}
     */
    public function setGroups($groups): void
    {
        $this->groups = $groups;
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation(): TranslationInterface
    {
        return new CustomerOptionTranslation();
    }
}
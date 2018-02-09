<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 13:15
 */

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Sylius\Component\Resource\Model\AbstractTranslation;

class CustomerOptionValueTranslation extends AbstractTranslation implements CustomerOptionValueTranslationInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
}
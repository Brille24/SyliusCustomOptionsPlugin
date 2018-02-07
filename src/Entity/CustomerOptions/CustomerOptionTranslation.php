<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 10:31
 */

namespace Brille24\CustomerOptionsBundle\Entity;


use Sylius\Component\Resource\Model\AbstractTranslation;

class CustomerOptionTranslation extends AbstractTranslation implements CustomerOptionTranslationInterface
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
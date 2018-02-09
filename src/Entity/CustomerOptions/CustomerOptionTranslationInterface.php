<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 10:28
 */

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface CustomerOptionTranslationInterface extends ResourceInterface, TranslationInterface
{
    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @param string $name
     */
    public function setName(string $name);
}
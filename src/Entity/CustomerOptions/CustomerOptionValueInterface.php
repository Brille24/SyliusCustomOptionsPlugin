<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 10:46
 */

namespace Brille24\CustomerOptionsBundle\Entity;


use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface CustomerOptionValueInterface extends ResourceInterface, TranslatableInterface
{
    public function setCode(string $code);

    public function getCode() : string ;

    public function setValue(string $value);

    public function getValue() : string ;
}
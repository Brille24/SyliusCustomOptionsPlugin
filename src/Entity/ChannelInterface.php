<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 21.02.18
 * Time: 09:39
 */

namespace Brille24\CustomerOptionsPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface as BaseChannelInterface;

interface ChannelInterface extends BaseChannelInterface
{
    public function getCustomerOptionValuePrices(): Collection;

    public function setCustomerOptionValuePrices(Collection $prices): void;
}
<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Context\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;

class CustomerOptionContext implements Context
{
    /** @var ShowPageInterface  */
    private $showPage;

    public function __construct(
        ShowPageInterface $showPage
    )
    {
        $this->showPage = $showPage;
    }
}

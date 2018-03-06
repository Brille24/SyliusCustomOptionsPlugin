<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Page\CustomerOption;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BasePage;

class UpdatePage extends BasePage
{
    public function hasConfiguration(string $config){
        return $this->getDocument()->hasField($config);
    }
}
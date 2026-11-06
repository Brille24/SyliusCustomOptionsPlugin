<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Product;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Product\CreateSimpleProductPage as BaseCreatePage;

class CreateSimpleProductPage extends BaseCreatePage
{
    use CustomerOptionsFormTrait;

    /**
     * @throws ElementNotFoundException
     */
    public function nameItIn(string $name, string $localeCode): void
    {
        $this->expandTranslation($localeCode);

        $this->requireField(sprintf('[data-test-name="%s"]', $localeCode))->setValue($name);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function specifySlugIn(string $slug, string $localeCode): void
    {
        $this->expandTranslation($localeCode);

        $this->requireField(sprintf('[data-test-slug="%s"]', $localeCode))->setValue($slug);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function specifyPrice(string $price): void
    {
        $this->getElement('side_navigation_tab', ['%name%' => 'channel-pricing'])->click();

        $accordion = $this->getDocument()->find('css', '[data-test-product-channel-pricings-accordion]');
        if ($accordion instanceof NodeElement && $accordion->hasClass('collapsed')) {
            $accordion->click();
        }

        $this->requireField('[data-test-price-in-channel]')->setValue($price);
    }

    /**
     * @throws ElementNotFoundException
     */
    private function expandTranslation(string $localeCode): void
    {
        $this->getElement('side_navigation_tab', ['%name%' => 'translations'])->click();

        $accordion = $this->getElement('product_translation_accordion', ['%localeCode%' => $localeCode]);
        if ($accordion->getAttribute('aria-expanded') !== 'true') {
            $accordion->click();
        }
    }

    /**
     * @throws ElementNotFoundException
     */
    private function requireField(string $selector): NodeElement
    {
        $field = $this->getDocument()->find('css', $selector);
        if (!$field instanceof NodeElement) {
            throw new ElementNotFoundException($this->getSession(), 'input', 'css', $selector);
        }

        return $field;
    }
}

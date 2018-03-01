<?php

declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\PHPUnit\Fixture;

use Brille24\CustomerOptionsPlugin\Fixture\Factory\ProductFactory;
use Brille24\CustomerOptionsPlugin\Fixture\ProductFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ProductFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function customer_option_group_is_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[]],
        ]], 'custom.*.customer_option_group');
    }

    /**
     * @test
     */
    public function customer_option_value_prices_are_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[]],
        ]], 'custom.*.customer_option_value_prices');
    }

    /**
     * @test
     */
    public function value_price_value_code_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'customer_option_value_prices' => [[]],
            ]],
        ]], 'custom.*.customer_option_value_prices.*.value_code');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'customer_option_value_prices' => [[
                    'value_code' => '',
                ]],
            ]],
        ]], 'custom.*.customer_option_value_prices.*.value_code');
    }

    /**
     * @test
     */
    public function value_price_type_is_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'customer_option_value_prices' => [[]],
            ]],
        ]], 'custom.*.customer_option_value_prices.*.type');
    }

    /**
     * @test
     */
    public function value_price_amount_is_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'customer_option_value_prices' => [[]],
            ]],
        ]], 'custom.*.customer_option_value_prices.*.amount');
    }

    /**
     * @test
     */
    public function value_price_percent_is_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'customer_option_value_prices' => [[]],
            ]],
        ]], 'custom.*.customer_option_value_prices.*.percent');
    }

    /**
     * @test
     */
    public function value_price_channel_is_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'customer_option_value_prices' => [[]],
            ]],
        ]], 'custom.*.customer_option_value_prices.*.channel');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ProductFixture(
            $this->createMock(ObjectManager::class),
            $this->createMock(ProductFactory::class)
        );
    }
}

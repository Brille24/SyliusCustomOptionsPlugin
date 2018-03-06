<?php

declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\PHPUnit\Fixture;

use Brille24\CustomerOptionsPlugin\Factory\CustomerOptionFactory;
use Brille24\CustomerOptionsPlugin\Fixture\CustomerOptionFixture;
use Doctrine\ORM\EntityManagerInterface;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class CustomerOptionFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function can_be_randomly_generated()
    {
        $this->assertConfigurationIsValid([['amount' => 4]], 'amount');
    }

    /**
     * @test
     */
    public function customer_options_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function code_is_required()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'code' => 'CustomerOption',
            ]],
        ]], 'custom.*.code');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
            ]],
        ]], 'custom.*.code');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'code' => '',
            ]],
        ]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function at_least_one_translation_is_required()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'translations' => [
                    'en_US' => 'Customer Option',
                ],
            ]],
        ]], 'custom.*.translations.*');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'translations' => [],
            ]],
        ]], 'custom.*.translations.*');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [
                [],
            ],
        ]], 'custom.*.translations');
    }

    /**
     * @test
     */
    public function type_is_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'type' => 'some type',
            ]],
        ]], 'custom.*.type');
    }

    /**
     * @test
     */
    public function required_is_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'required' => false,
            ]],
        ]], 'custom.*.required');
    }

    /**
     * @test
     */
    public function customer_option_values_are_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'values' => [],
            ]],
        ]], 'custom.*.values.*');
    }

    /**
     * @test
     */
    public function customer_option_value_code_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'values' => [[]],
            ]],
        ]], 'custom.*.values.*.code');

        $this->assertConfigurationIsValid([[
            'custom' => [[
                'values' => [[
                    'code' => 'some_value',
                ]],
            ]],
        ]], 'custom.*.values.*.code');
    }

    /**
     * @test
     */
    public function customer_option_values_need_at_least_one_translation()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'values' => [[
                    'translations' => [
                        'en_US' => 'Some Value',
                    ],
                ]],
            ]],
        ]], 'custom.*.values.*.translations.*');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'values' => [[]],
            ]],
        ]], 'custom.*.values.*.translations');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'values' => [[
                    'translations' => [],
                ]],
            ]],
        ]], 'custom.*.values.*.translations.*');
    }

    /**
     * @test
     */
    public function customer_option_value_prices_are_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'values' => [[
                ]],
            ]],
        ]], 'custom.*.values.*.prices');
    }

    /**
     * @test
     */
    public function value_prices_type_is_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'values' => [[
                    'prices' => [[]],
                ]],
            ]],
        ]], 'custom.*.values.*.prices.*.type');
    }

    /**
     * @test
     */
    public function value_prices_amount_is_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'values' => [[
                    'prices' => [[]],
                ]],
            ]],
        ]], 'custom.*.values.*.prices.*.amount');
    }

    /**
     * @test
     */
    public function value_prices_percent_is_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'values' => [[
                    'prices' => [[]],
                ]],
            ]],
        ]], 'custom.*.values.*.prices.*.percent');
    }

    /**
     * @test
     */
    public function value_prices_channel_is_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'values' => [[
                    'prices' => [[]],
                ]],
            ]],
        ]], 'custom.*.values.*.prices.*.channel');
    }

    /**
     * @test
     */
    public function customer_option_groups_are_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
            ]],
        ]], 'custom.*.groups');

        $this->assertConfigurationIsValid([[
            'custom' => [[
                'groups' => [],
            ]],
        ]], 'custom.*.groups');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): CustomerOptionFixture
    {
        return new CustomerOptionFixture(
            $this->createMock(CustomerOptionFactory::class),
            $this->createMock(EntityManagerInterface::class)
        );
    }
}

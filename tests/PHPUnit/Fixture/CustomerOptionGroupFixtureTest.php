<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Fixture;

use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionGroupFactory;
use Brille24\SyliusCustomerOptionsPlugin\Fixture\CustomerOptionGroupFixture;
use Doctrine\ORM\EntityManagerInterface;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class CustomerOptionGroupFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function customer_option_groups_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

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
    public function code_is_required()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'code' => 'group',
            ]],
        ]], 'custom.*.code');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
            ]],
        ]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function needs_at_least_one_translation()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'translations' => [
                    'en_US' => 'Group',
                ],
            ]],
        ]], 'custom.*.translations.*');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'translations' => [],
            ]],
        ]], 'custom.*.translations.*');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[]],
        ]], 'custom.*.translations');
    }

    /**
     * @test
     */
    public function options_are_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'options' => [
                    'option_1',
                    'option_2',
                ],
            ]],
        ]], 'custom.*.options');

        $this->assertConfigurationIsValid([[
            'custom' => [[
                'options' => [],
            ]],
        ]], 'custom.*.options');

        $this->assertConfigurationIsValid([[
            'custom' => [[]],
        ]], 'custom.*.options');
    }

    /**
     * @test
     */
    public function products_are_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'products' => [
                    'prod_1',
                    'prod_2',
                ],
            ]],
        ]], 'custom.*.products');

        $this->assertConfigurationIsValid([[
            'custom' => [[
                'products' => [],
            ]],
        ]], 'custom.*.products');

        $this->assertConfigurationIsValid([[
            'custom' => [[]],
        ]], 'custom.*.products');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new CustomerOptionGroupFixture(
            $this->createMock(CustomerOptionGroupFactory::class),
            $this->createMock(EntityManagerInterface::class)
        );
    }
}

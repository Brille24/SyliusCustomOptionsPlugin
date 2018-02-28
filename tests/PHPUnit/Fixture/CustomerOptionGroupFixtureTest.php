<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\PHPUnit\Fixture;


use Brille24\CustomerOptionsPlugin\Fixture\CustomerOptionGroupFixture;
use Brille24\CustomerOptionsPlugin\Fixture\Factory\CustomerOptionGroupFactory;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class CustomerOptionGroupFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function customer_option_groups_are_optional(){
        $this->assertConfigurationIsValid([[]], 'customer_option_groups');
    }

    /**
     * @test
     */
    public function can_be_randomly_generated(){
        $this->assertConfigurationIsValid([['amount' => 4]], 'amount');
    }

    /**
     * @test
     */
    public function code_is_required(){
        $this->assertConfigurationIsValid([[
            'customer_option_groups' => [[
                'code' => 'group'
            ]]
        ]], 'customer_option_groups.*.code');

        $this->assertPartialConfigurationIsInvalid([[
            'customer_option_groups' => [[

            ]]
        ]], 'customer_option_groups.*.code');
    }

    /**
     * @test
     */
    public function needs_at_least_one_translation(){
        $this->assertConfigurationIsValid([[
            'customer_option_groups' => [[
                'translations' => [
                    'en_US' => 'Group'
                ]
            ]]
        ]], 'customer_option_groups.*.translations.*');

        $this->assertPartialConfigurationIsInvalid([[
            'customer_option_groups' => [[
                'translations' => []
            ]]
        ]], 'customer_option_groups.*.translations.*');

        $this->assertPartialConfigurationIsInvalid([[
            'customer_option_groups' => [[]]
        ]], 'customer_option_groups.*.translations');
    }

    /**
     * @test
     */
    public function options_are_optional(){
        $this->assertConfigurationIsValid([[
            'customer_option_groups' => [[
                'options' => [
                    'option_1',
                    'option_2'
                ]
            ]]
        ]], 'customer_option_groups.*.options');

        $this->assertConfigurationIsValid([[
            'customer_option_groups' => [[
                'options' => []
            ]]
        ]], 'customer_option_groups.*.options');

        $this->assertConfigurationIsValid([[
            'customer_option_groups' => [[]]
        ]], 'customer_option_groups.*.options');
    }

    /**
     * @test
     */
    public function products_are_optional(){
        $this->assertConfigurationIsValid([[
            'customer_option_groups' => [[
                'products' => [
                    'prod_1',
                    'prod_2'
                ]
            ]]
        ]], 'customer_option_groups.*.products');

        $this->assertConfigurationIsValid([[
            'customer_option_groups' => [[
                'products' => []
            ]]
        ]], 'customer_option_groups.*.products');

        $this->assertConfigurationIsValid([[
            'customer_option_groups' => [[]]
        ]], 'customer_option_groups.*.products');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new CustomerOptionGroupFixture(
            $this->createMock(CustomerOptionGroupFactory::class)
        );
    }
}
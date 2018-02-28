<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\PHPUnit\Fixture;


use Brille24\CustomerOptionsPlugin\Fixture\CustomerOptionFixture;
use Brille24\CustomerOptionsPlugin\Fixture\Factory\CustomerOptionFactory;
use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class CustomerOptionFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function can_be_randomly_generated(){
        $this->assertConfigurationIsValid([['amount' => 4]], 'amount');
    }

    /**
     * @test
     */
    public function customer_options_are_optional(){
        $this->assertConfigurationIsValid([[]], 'customer_options');
    }

    /**
     * @test
     */
    public function code_is_required(){
        $this->assertConfigurationIsValid([[
            'customer_options' => [[
                'code' => 'CustomerOption'
            ]]
        ]], 'customer_options.*.code');

        $this->assertPartialConfigurationIsInvalid([[
            'customer_options' => [[
            ]]
        ]], 'customer_options.*.code');

        $this->assertPartialConfigurationIsInvalid([[
            'customer_options' => [[
                'code' => ''
            ]]
        ]], 'customer_options.*.code');
    }

    /**
     * @test
     */
    public function at_least_one_translation_is_required(){
        $this->assertConfigurationIsValid([[
            'customer_options' => [[
                'translations' => [
                    'en_US' => 'Customer Option',
                ],
            ]],
        ]], 'customer_options.*.translations.*');

        $this->assertPartialConfigurationIsInvalid([[
            'customer_options' => [[
                'translations' => [],
            ]],
        ]], 'customer_options.*.translations.*');

        $this->assertPartialConfigurationIsInvalid([[
            'customer_options' => [
                []
            ]
        ]], 'customer_options.*.translations');
    }

    /**
     * @test
     */
    public function type_is_optional(){
        $this->assertConfigurationIsValid([[
            'customer_options' => [[
                'type' => 'some type',
            ]],
        ]], 'customer_options.*.type');
    }

    /**
     * @test
     */
    public function required_is_optional(){
        $this->assertConfigurationIsValid([[
            'customer_options' => [[
                'required' => false,
            ]],
        ]], 'customer_options.*.required');
    }

    /**
     * @test
     */
    public function customer_option_values_are_optional(){
        $this->assertConfigurationIsValid([[
            'customer_options' => [[
                'values' => []
            ]]
        ]], 'customer_options.*.values.*');
    }

    /**
     * @test
     */
    public function customer_option_value_code_is_required(){
        $this->assertPartialConfigurationIsInvalid([[
            'customer_options' => [[
                'values' => [[]]
            ]]
        ]], 'customer_options.*.values.*.code');

        $this->assertConfigurationIsValid([[
            'customer_options' => [[
                'values' => [[
                    'code' => 'some_value'
                ]]
            ]]
        ]], 'customer_options.*.values.*.code');
    }

    /**
     * @test
     */
    public function customer_option_values_need_at_least_one_translation(){
        $this->assertConfigurationIsValid([[
            'customer_options' => [[
                'values' => [[
                    'translations' => [
                        'en_US' => 'Some Value'
                    ]
                ]]
            ]]
        ]], 'customer_options.*.values.*.translations.*');

        $this->assertPartialConfigurationIsInvalid([[
            'customer_options' => [[
                'values' => [[]]
            ]]
        ]], 'customer_options.*.values.*.translations');

        $this->assertPartialConfigurationIsInvalid([[
            'customer_options' => [[
                'values' => [[
                    'translations' => []
                ]]
            ]]
        ]], 'customer_options.*.values.*.translations.*');
    }

    /**
     * @test
     */
    public function customer_option_value_prices_are_optional(){
        $this->assertConfigurationIsValid([[
            'customer_options' => [[
                'values' => [[
                ]]
            ]]
        ]], 'customer_options.*.values.*.prices');
    }

    /**
     * @test
     */
    public function customer_option_groups_are_optional(){
        $this->assertConfigurationIsValid([[
            'customer_options' => [[
            ]]
        ]], 'customer_options.*.groups');

        $this->assertConfigurationIsValid([[
            'customer_options' => [[
                'groups' => []
            ]]
        ]], 'customer_options.*.groups');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): CustomerOptionFixture
    {
        return new CustomerOptionFixture(
            $this->createMock(CustomerOptionFactory::class)
        );
    }
}
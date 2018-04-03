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
     * @test
     */
    public function validators_are_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[]],
        ]], 'custom.*.validators');
    }

    /**
     * @test
     */
    public function validators_need_at_least_one_element()
    {
        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [],
            ]],
        ]], 'custom.*.validators');
    }

    /**
     * @test
     */
    public function validator_conditions_are_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'validators' => [[]],
            ]],
        ]], 'custom.*.validators.*.conditions');
    }

    /**
     * @test
     */
    public function validator_conditions_need_at_least_one_element()
    {
        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'conditions' => [],
                ]],
            ]],
        ]], 'custom.*.validators.*.conditions');
    }

    /**
     * @test
     */
    public function validator_constraints_are_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'validators' => [[]],
            ]],
        ]], 'custom.*.validators.*.constraints');
    }

    public function validator_constraints_need_at_least_one_element()
    {
        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'constraints' => [],
                ]],
            ]],
        ]], 'custom.*.validators.*.constraints');
    }

    /**
     * @test
     */
    public function validator_condition_customer_option_is_required()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'validators' => [[
                    'conditions' => [[
                        'customer_option' => 'some_option',
                    ]],
                ]],
            ]],
        ]], 'custom.*.validators.*.conditions.*.customer_option');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'conditions' => [[]],
                ]],
            ]],
        ]], 'custom.*.validators.*.conditions.*.customer_option');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'conditions' => [[
                        'customer_option' => '',
                    ]],
                ]],
            ]],
        ]], 'custom.*.validators.*.conditions.*.customer_option');
    }

    /**
     * @test
     */
    public function validator_condition_comparator_is_required()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'validators' => [[
                    'conditions' => [[
                        'comparator' => 'greater',
                    ]],
                ]],
            ]],
        ]], 'custom.*.validators.*.conditions.*.comparator');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'conditions' => [[]],
                ]],
            ]],
        ]], 'custom.*.validators.*.conditions.*.comparator');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'conditions' => [[
                        'comparator' => '',
                    ]],
                ]],
            ]],
        ]], 'custom.*.validators.*.conditions.*.comparator');
    }

    /**
     * @test
     */
    public function validator_condition_value_is_required()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'validators' => [[
                    'conditions' => [[
                        'value' => '5',
                    ]],
                ]],
            ]],
        ]], 'custom.*.validators.*.conditions.*.value');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'conditions' => [[]],
                ]],
            ]],
        ]], 'custom.*.validators.*.conditions.*.value');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'conditions' => [[
                        'value' => '',
                    ]],
                ]],
            ]],
        ]], 'custom.*.validators.*.conditions.*.value');
    }

    /**
     * @test
     */
    public function validator_constraint_customer_option_is_required()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'validators' => [[
                    'constraints' => [[
                        'customer_option' => 'some_option',
                    ]],
                ]],
            ]],
        ]], 'custom.*.validators.*.constraints.*.customer_option');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'constraints' => [[]],
                ]],
            ]],
        ]], 'custom.*.validators.*.constraints.*.customer_option');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'constraints' => [[
                        'customer_option' => '',
                    ]],
                ]],
            ]],
        ]], 'custom.*.validators.*.constraints.*.customer_option');
    }

    /**
     * @test
     */
    public function validator_constraint_comparator_is_required()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'validators' => [[
                    'constraints' => [[
                        'comparator' => 'greater',
                    ]],
                ]],
            ]],
        ]], 'custom.*.validators.*.constraints.*.comparator');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'constraints' => [[]],
                ]],
            ]],
        ]], 'custom.*.validators.*.constraints.*.comparator');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'constraints' => [[
                        'comparator' => '',
                    ]],
                ]],
            ]],
        ]], 'custom.*.validators.*.constraints.*.comparator');
    }

    /**
     * @test
     */
    public function validator_constraint_value_is_required()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'validators' => [[
                    'constraints' => [[
                        'value' => '5',
                    ]],
                ]],
            ]],
        ]], 'custom.*.validators.*.constraints.*.value');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'constraints' => [[]],
                ]],
            ]],
        ]], 'custom.*.validators.*.constraints.*.value');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'constraints' => [[
                        'value' => '',
                    ]],
                ]],
            ]],
        ]], 'custom.*.validators.*.constraints.*.value');
    }

    /**
     * @test
     */
    public function validator_error_messages_are_optional()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'validators' => [[]],
            ]],
        ]], 'custom.*.validators.*.error_messages');
    }

    /**
     * @test
     */
    public function validator_error_messages_need_at_least_one_element()
    {
        $this->assertConfigurationIsValid([[
            'custom' => [[
                'validators' => [[
                    'error_messages' => [
                        'en_US' => 'Test',
                    ],
                ]],
            ]],
        ]], 'custom.*.validators.*.error_messages');

        $this->assertPartialConfigurationIsInvalid([[
            'custom' => [[
                'validators' => [[
                    'error_messages' => [],
                ]],
            ]],
        ]], 'custom.*.validators.*.error_messages');
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

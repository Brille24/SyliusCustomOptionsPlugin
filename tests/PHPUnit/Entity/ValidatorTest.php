<?php

declare(strict_types = 1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Entity;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\Condition;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\Constraint;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\Validator;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /** @var ValidatorInterface */
    private $validator;

    public function setUp()
    {
        $this->validator = new Validator();
        $this->validator->getErrorMessage()->setCurrentLocale('en_US');
    }

    /**
     * @test
     */
    public function testDefaultValues()
    {
        self::assertCount(0, $this->validator->getConditions());
        self::assertCount(0, $this->validator->getConstraints());
        self::assertEquals(Validator::DEFAULT_ERROR_MESSAGE, $this->validator->getErrorMessage()->getMessage());
    }

    /**
     * @test
     */
    public function testAddConditions()
    {
        $num = 5;

        for ($i = 0; $i < $num; ++$i) {
            $condition = self::createMock(Condition::class);

            $this->validator->addCondition($condition);

            self::assertCount($i + 1, $this->validator->getConditions());
        }

        return $this->validator;
    }

    /**
     * @test
     * @depends testAddConditions
     *
     * @param Validator $validator
     */
    public function testRemoveConditions(Validator $validator)
    {
        $conditions = $validator->getConditions();
        $count = count($conditions);
        $condition = reset($conditions);

        $validator->removeCondition($condition);

        self::assertCount($count - 1, $validator->getConditions());
    }

    /**
     * @test
     */
    public function testAddConstraints()
    {
        $num = 5;

        for ($i = 0; $i < $num; ++$i) {
            $constraint = self::createMock(Constraint::class);

            $this->validator->addConstraint($constraint);

            self::assertCount($i + 1, $this->validator->getConstraints());
        }

        return $this->validator;
    }

    /**
     * @test
     * @depends testAddConstraints
     *
     * @param Validator $validator
     */
    public function testRemoveConstraint(Validator $validator)
    {
        $constraints = $validator->getConstraints();
        $count = count($constraints);
        $constraint = reset($constraints);

        $validator->removeConstraint($constraint);

        self::assertCount($count - 1, $validator->getConstraints());
    }
}

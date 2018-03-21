<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\PHPUnit\Validator;


use Brille24\CustomerOptionsPlugin\Validator\Constraints\ConditionalConstraintValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

class ConditionalConstraintValidatorTest extends TestCase
{
    private $conditionalConstraintValidator;

    public function setUp()
    {
        $this->conditionalConstraintValidator = new ConditionalConstraintValidator(
            $this->createMock(RequestStack::class)
        );
    }
}
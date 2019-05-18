<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Service;

use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Services\ConstraintCreator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class ConstraintCreatorTest extends TestCase
{
    private function createValue(string $key, $value)
    {
        return [$key => ['value' => $value]];
    }

    public function testInvalidType(): void
    {
        $constraint = ConstraintCreator::createFromConfiguration('does_not_exist', []);

        self::assertNull($constraint);
    }

    /** @dataProvider dataMissingConfigurationOptions */
    public function testMissingConfigurationOptions(string $type, string $message): void
    {
        self::expectException(MissingOptionsException::class);
        self::expectExceptionMessage($message);

        $constraint = ConstraintCreator::createFromConfiguration($type, []);

        self::assertNull($constraint);
    }

    public function dataMissingConfigurationOptions(): array
    {
        return [
            'integer' => [
                CustomerOptionTypeEnum::NUMBER,
                'Either option "min" or "max" must be given for constraint Symfony\Component\Validator\Constraints\Range',
            ],
            'string' => [
                CustomerOptionTypeEnum::TEXT,
                'Either option "min" or "max" must be given for constraint Symfony\Component\Validator\Constraints\Length',
            ],
        ];
    }

    /** @dataProvider dataCreateSuccessful */
    public function testCreateSuccessful(string $type, array $configuration, Constraint $expectedConstraint): void
    {
        $constraint = ConstraintCreator::createFromConfiguration($type, $configuration);

        self::assertEquals($expectedConstraint, $constraint);
    }

    public function dataCreateSuccessful(): array
    {
        return [
            // STRING
            'string only min' => [
                CustomerOptionTypeEnum::TEXT,
                $this->createValue('brille24.form.config.min.length', 10),
                new Length(['min' => 10]),
            ],
            'string min, max' => [
                CustomerOptionTypeEnum::TEXT,
                array_merge(
                    $this->createValue('brille24.form.config.min.length', 10),
                    $this->createValue('brille24.form.config.max.length', 50)
                ),
                new Length(['min' => 10, 'max' => 50]),
            ],
            // INTEGER
            'integer min only' => [
                CustomerOptionTypeEnum::NUMBER,
                $this->createValue('brille24.form.config.min.number', -1),
                new Range(['min' => -1]),
            ],
            'integer min, max' => [
                CustomerOptionTypeEnum::NUMBER,
                array_merge(
                    $this->createValue('brille24.form.config.min.number', 10),
                    $this->createValue('brille24.form.config.max.number', 25)
                ),
                new Range(['min' => 10, 'max' => 25]),
            ],
            // FILES
            'file with file size' => [
                CustomerOptionTypeEnum::FILE,
                array_merge(
                    $this->createValue('brille24.form.config.max.file_size', 10),
                    $this->createValue('brille24.form.config.min.file_size', 0),
                    $this->createValue('brille24.form.config.allowed_types', 'text/text')
                ),
                new File(['maxSize' => 10, 'mimeTypes' => ['text/text']]),
            ],
            //DATE
            'date with min' => [
                CustomerOptionTypeEnum::DATE,
                $this->createValue('brille24.form.config.min.date', ['date' => '2000-01-17']),
                new Range(['min' => '2000-01-17']),
            ],
            'date with min, max' => [
                CustomerOptionTypeEnum::DATE,
                array_merge(
                    $this->createValue('brille24.form.config.min.date', ['date' => '2000-01-17']),
                    $this->createValue('brille24.form.config.max.date', ['date' => '2010-01-18'])
                ),
                new Range(['min' => '2000-01-17', 'max' => '2010-01-18']),
            ],
            //DATE TIME
            'date time with min' => [
                CustomerOptionTypeEnum::DATETIME,
                $this->createValue('brille24.form.config.min.date', ['date' => '2001-17-01 17:00:21']),
                new Range(['min' => '2001-17-01 17:00:21']),
            ],
            'date time with min, max' => [
                CustomerOptionTypeEnum::DATETIME,
                array_merge(
                    $this->createValue('brille24.form.config.min.date', ['date' => '2001-17-01 17:00:21']),
                    $this->createValue('brille24.form.config.max.date', ['date' => '2010-01-17 17:11:21'])
                ),
                new Range(['min' => '2001-17-01 17:00:21', 'max' => '2010-01-17 17:11:21']),
            ],
        ];
    }
}

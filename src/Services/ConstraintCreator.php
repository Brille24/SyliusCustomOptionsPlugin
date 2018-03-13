<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Services;


use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class ConstraintCreator
{
    public static function getValueFromConfiguration(array $configuration, string $key)
    {
        if (!isset($configuration[$key]['value'])) {
            return null;
        }
        return $configuration[$key]['value'];
    }

    public static function createFromConfiguration(string $type, array $configuration): ?Constraint
    {
        $getFromConfiguration = function ($key) use ($configuration) {
            return self::getValueFromConfiguration($configuration, $key);
        };

        switch ($type) {
            case CustomerOptionTypeEnum::TEXT:
                $lengthRange = [
                    'min' => $getFromConfiguration('brille24.form.config.min.length'),
                    'max' => $getFromConfiguration('brille24.form.config.max.length'),
                ];
                return new Length($lengthRange);

            case CustomerOptionTypeEnum::FILE:
                return new File(['maxSize' => $getFromConfiguration('brille24.form.config.max.file_size')]);

            case CustomerOptionTypeEnum::DATE:
            case CustomerOptionTypeEnum::DATETIME:
                $dateRange = [
                    'min' => $getFromConfiguration('brille24.form.config.min.date')['date'],
                    'max' => $getFromConfiguration('brille24.form.config.max.date')['date'],
                ];
                return new Range($dateRange);

            case CustomerOptionTypeEnum::NUMBER:
                $dateRange = [
                    'min' => $getFromConfiguration('brille24.form.config.min.number'),
                    'max' => $getFromConfiguration('brille24.form.config.max.number'),
                ];
                return new Range($dateRange);
        }
        return null;
    }

    public static function createRequiredConstraint(): Constraint
    {
        return new NotBlank();
    }
}
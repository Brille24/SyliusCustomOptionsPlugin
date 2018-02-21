<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Services;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;

class CustomerOptionValueResolver implements CustomerOptionValueResolverInterface
{
    public function resolve(CustomerOptionInterface $customerOption, string $value): ?CustomerOptionValueInterface
    {
        $type = $customerOption->getType();
        if (!CustomerOptionTypeEnum::isSelect($type)) {
            return null;
        }

        foreach($customerOption->getValues() as $valueObject){
            if($valueObject->getCustomerOption() === $customerOption && $valueObject->getCode() === $value){
                return $valueObject;
            }
        }

        return null;
    }
}
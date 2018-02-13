<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 13.02.18
 * Time: 16:54
 */

namespace Brille24\CustomerOptionsPlugin\Twig;


use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CustomerOptionTypeExtension extends AbstractExtension
{
    public function getFilters(){
        return [
            new TwigFilter('type', [$this, 'typeFilter']),
        ];
    }

    public function typeFilter($type){
        $result = $type;

        switch($type){
            case CustomerOptionTypeEnum::MULTI_SELECT:
                $result = 'select multiple';
                break;
            case CustomerOptionTypeEnum::DATETIME:
                $result = 'datetime-local';
                break;
            case CustomerOptionTypeEnum::BOOLEAN:
                $result = 'checkbox';
                break;
        }

        return $result;
    }
}
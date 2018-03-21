<?php

/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;

use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use DateTime;
use DateTimeZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CustomerOptionConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form          = $event->getForm();
            $configuration = $event->getData();

            foreach ($configuration as $key => $configArray) {
                $type = $configArray['type'];
                $data = $configArray['value'];

                // Transforming Datetime objects
                if (CustomerOptionTypeEnum::isDate($type)) {
                    $data = new DateTime($data['date'], new DateTimeZone($data['timezone']));
                }

                [$formTypeClass, $formTypeConfig] = CustomerOptionTypeEnum::getFormTypeArray()[$type];

                // Adding form field for configuration option based on type
                $form->add(
                    str_replace('.', '_', $key), $formTypeClass,
                    array_merge(['data' => $data, 'label' => $key], $formTypeConfig)
                );
            }
        });
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_option_configuration';
    }
}

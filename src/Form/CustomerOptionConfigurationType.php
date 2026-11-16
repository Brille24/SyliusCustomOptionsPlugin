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

namespace Brille24\SyliusCustomerOptionsPlugin\Form;

use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use DateTime;
use DateTimeZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CustomerOptionConfigurationType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event): void {
            $form = $event->getForm();
            $configuration = $event->getData();

            foreach ($configuration as $key => $configArray) {
                $type = $configArray['type'];
                $data = $configArray['value'];

                [$formTypeClass, $formTypeConfig] = CustomerOptionTypeEnum::getFormTypeArray()[$type];

                if (CustomerOptionTypeEnum::isDate($type)) {
                    // Persisted configurations contain the serialized DateTime, new ones the object itself
                    if (is_array($data)) {
                        $data = new DateTime($data['date'], new DateTimeZone($data['timezone']));
                    }

                    $formTypeConfig['widget'] = 'single_text';
                }

                // Adding form field for configuration option based on type
                $form->add(
                    str_replace('.', ':', $key),
                    $formTypeClass,
                    array_merge(['data' => $data, 'label' => $key], $formTypeConfig),
                );
            }
        });
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix(): string
    {
        return 'brille24_customer_option_configuration';
    }
}

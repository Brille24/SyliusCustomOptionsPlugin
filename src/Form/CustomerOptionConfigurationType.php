<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;


use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use DateTime;
use DateTimeZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType, DateTimeType, DateType, NumberType, TextType
};
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
                if (in_array($type, [CustomerOptionTypeEnum::DATE, CustomerOptionTypeEnum::DATETIME])) {
                    $data = new DateTime($data['date'], new DateTimeZone($data['timezone']));
                }

                // Adding form field for configuration option based on type
                $form->add(str_replace('.', '_', $key), CustomerOptionConfigurationType::getTypeFromString($type), [
                    'data'  => $data,
                    'label' => $key
                ]);
            }
        });
    }

    private static function getTypeFromString(string $type): string
    {
        switch ($type) {
            case 'integer':
            case 'int':
                return NumberType::class;
            case 'boolean':
            case 'bool':
                return CheckboxType::class;
            case 'date':
                return DateType::class;
            case 'datetime':
                return DateTimeType::class;

            default:
                return TextType::class;
        }
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_option_configuration';
    }
}
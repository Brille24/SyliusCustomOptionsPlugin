<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\ConditionComparatorEnum;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValueRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValueType extends AbstractType
{
    /** @var string */
    const DEFAULT_LABEL = 'brille24.form.validators.fields.value.default';

    /** @var CustomerOptionValueRepositoryInterface */
    private $customerOptionValueRepository;

    public function __construct(CustomerOptionValueRepositoryInterface $customerOptionValueRepository)
    {
        $this->customerOptionValueRepository = $customerOptionValueRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $options['field_options']['label'] = $options['field_options']['label'] ?? self::DEFAULT_LABEL;

        $builder->add('value', $options['field_type'], $options['field_options']);

        $builder->get('value')->addModelTransformer(new CallbackTransformer(
            function ($modelData) use ($options) {
                $result = null;

                $newConfig = ConditionComparatorEnum::getValueConfig($options['option_type']);

                if ($modelData !== null
                    && is_array($modelData)
                    && array_key_exists('type', $modelData)
                    && $modelData['type'] === $newConfig['type']
                ) {
                    $modelData = $modelData['value'];
                    $result = $modelData;
                }

                if ($modelData !== null) {
                    if (CustomerOptionTypeEnum::isSelect($options['option_type'])) {
                        $result = [];

                        foreach ($modelData as $data) {
                            $result[] = $this->customerOptionValueRepository->findOneByCode($data);
                        }
                    } elseif (CustomerOptionTypeEnum::isDate($options['option_type'])) {
                        $result = new \DateTime($modelData['date'] ?? 'now');

                        if (isset($modelData['timezone'])) {
                            $result->setTimezone(new \DateTimeZone($modelData['timezone']));
                        }
                    } else {
                        $result = $modelData;
                    }
                }

                return $result;
            },
            function ($viewData) use ($options) {
                $result = $viewData;

                if (CustomerOptionTypeEnum::isSelect($options['option_type'])) {
                    $result = [];

                    foreach ($viewData as $data) {
                        if ($data instanceof CustomerOptionValueInterface) {
                            $result[] = $data->getCode();
                        }
                    }
                }

                return $result;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined([
            'field_type',
            'field_options',
            'option_type',
        ]);

        $resolver->setAllowedTypes('field_type', 'string');
        $resolver->setAllowedTypes('field_options', 'array');
        $resolver->setAllowedTypes('option_type', 'string');

        $resolver->setDefaults([
            'field_type'    => NumberType::class,
            'field_options' => [],
            'option_type'   => CustomerOptionTypeEnum::TEXT,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_options_plugin_validator_test';
    }
}

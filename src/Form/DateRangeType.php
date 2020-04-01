<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form;

use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start', DateTimeType::class, $options['field_options'])
            ->add('end', DateTimeType::class, $options['field_options'])
        ;

        $this->addModelTransformer($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function addModelTransformer(FormBuilderInterface $builder): void
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                static function (?DateRange $dateRange) {
                    if ($dateRange === null) {
                        return [];
                    }

                    return [
                        'start' => $dateRange->getStart(),
                        'end'   => $dateRange->getEnd(),
                    ];
                },
                static function (array $dateTime) {
                    if ($dateTime['start'] === null || $dateTime['end'] === null) {
                        return null;
                    }

                    return new DateRange($dateTime['start'], $dateTime['end']);
                }
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'field_options' => [
                'required' => false,
            ],
        ]);
    }
}

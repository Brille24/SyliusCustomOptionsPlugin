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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class CustomerOptionType extends AbstractResourceType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Array keys are the constants and the values are the translations
        $possibleTypes = CustomerOptionTypeEnum::getTranslateArray();

        $builder
            ->add('code', TextType::class, [
                'label' => 'sylius.ui.code',
                'empty_data' => '',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'sylius.ui.type',
                'choices' => array_flip($possibleTypes),
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'brille24.ui.required',
                'required' => false,
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => CustomerOptionTranslationType::class,
                'label' => 'brille24.form.customer_options.translations',
            ])
            ->add('values', LiveCollectionType::class, [
                'entry_type' => CustomerOptionValueType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'by_reference' => false,
                'button_add_options' => [
                    'label' => 'sylius.ui.add_value',
                ],
                'button_delete_options' => [
                    'label' => 'sylius.ui.delete',
                ],
            ])
            ->add('configuration', CustomerOptionConfigurationType::class, [
                'label' => false,
            ])
        ;

        $builder->get('values')->addModelTransformer(new CallbackTransformer(
            function ($a) {
                if ($a instanceof Collection) {
                    return $a->toArray();
                }

                return $a;
            },
            fn ($a) => $a,
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            $form = $event->getForm();
            $customerOption = $form->getData();

            if (!is_array($data) || !$customerOption instanceof CustomerOptionInterface) {
                return;
            }

            $type = $data['type'] ?? null;
            if (is_string($type) && CustomerOptionTypeEnum::isValid($type) && $type !== $customerOption->getType()) {
                $form->add('configuration', CustomerOptionConfigurationType::class, [
                    'label' => false,
                    'data' => CustomerOptionTypeEnum::getConfigurationArray()[$type] ?? [],
                ]);
            }

            $data['configuration'] = $this->completeConfiguration(
                $form->get('configuration'),
                $data['configuration'] ?? [],
            );
            $event->setData($data);
        });
    }

    /**
     * Keeps the submitted values of the configuration fields for the current type and falls back to the
     * field data for fields that were not rendered yet (e.g. right after the type was changed).
     */
    private function completeConfiguration(FormInterface $configurationForm, mixed $submittedConfiguration): array
    {
        $submittedConfiguration = is_array($submittedConfiguration) ? $submittedConfiguration : [];

        // The fields are added on PRE_SET_DATA, which is deferred for a child with locked data
        $configurationForm->getData();

        $configuration = [];
        foreach ($configurationForm as $name => $field) {
            if (array_key_exists($name, $submittedConfiguration)) {
                $configuration[$name] = $submittedConfiguration[$name];
            } elseif (!$field->getConfig()->getType()->getInnerType() instanceof CheckboxType) {
                $configuration[$name] = $field->getViewData();
            }
        }

        return $configuration;
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix(): string
    {
        return 'brille24_customer_option';
    }
}

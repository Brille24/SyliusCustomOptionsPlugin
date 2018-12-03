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

namespace Brille24\SyliusCustomerOptionsPlugin\Form\Extensions;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ErrorMessageTranslationInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ValidatorInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Form\Product\ShopCustomerOptionType;
use Brille24\SyliusCustomerOptionsPlugin\Services\ConstraintCreator;
use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\ConditionalConstraint;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AddToCartTypeExtension extends the add to cart action in the front-end and adds customerOptions
 */
final class AddToCartTypeExtension extends AbstractTypeExtension
{
    /** @var LocaleContextInterface */
    private $localeContext;

    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('customer_options', ShopCustomerOptionType::class, [
            'product' => $options['product'],
        ]);

        $itemOptions = $builder->get('cartItem')->getOptions();
        $itemType    = get_class($builder->get('cartItem')->getFormConfig()->getType()->getInnerType());

        $builder->add('cartItem', $itemType, array_merge($itemOptions, [
            'constraints' => $this->getConstraints($options['product']),
        ]));
    }

    private function getConstraints(ProductInterface $product): array
    {
        $constraints = [];

        $customerOptionGroup = $product->getCustomerOptionGroup();

        if ($customerOptionGroup !== null) {
            /** @var ValidatorInterface $validator */
            foreach ($customerOptionGroup->getValidators() as $validator) {
                /** @var ConditionalConstraint $constraint */
                $constraint = ConstraintCreator::createConditionalConstraint(
                    $validator->getConditions(),
                    $validator->getConstraints()
                );

                $constraint->groups = ['sylius'];

                /** @var ErrorMessageTranslationInterface|null $errorMessage */
                $errorMessage        = $validator->getErrorMessage();
                $constraint->message = $errorMessage !== null ? ($errorMessage->getMessage() ?? '') : '';

                $constraints[] = $constraint;
            }
        }

        return $constraints;
    }

    public function getExtendedType(): string
    {
        return AddToCartType::class;
    }
}

<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ConditionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\ConditionalConstraint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConditionalConstraintValidator extends ConstraintValidator
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ConditionalConstraint) {
            return;
        }

        if ($value instanceof OrderItemInterface) {
            /** @var Request $request */
            $request = $this->requestStack->getCurrentRequest();
            /** @var ProductInterface $product */
            $product = $value->getProduct();

            $configuration = $this->getCustomerOptionsFromRequest($request, $product);
        } else {
            $configuration = is_array($value) ? $value : [$value];
        }

        $allConditionsMet   = $this->allConditionsMet($constraint->conditions ?? [], $configuration);
        $allConstraintsMet  = $this->allConditionsMet($constraint->constraints ?? [], $configuration);

        if ($allConditionsMet && !$allConstraintsMet) {
            $this->context->addViolation($constraint->message);
        }
    }

    private function getCustomerOptionsFromRequest(Request $request, ProductInterface $product): array
    {
        /** @var array $addToCart */
        $addToCart = $request->request->get('sylius_add_to_cart');

        if (!isset($addToCart['customer_options'])) {
            return [];
        }

        $customerOptions = $product->getCustomerOptions();

        foreach ($customerOptions as $customerOption) {
            if (!array_key_exists($customerOption->getCode(), $addToCart['customer_options'])) {
                $addToCart['customer_options'][$customerOption->getCode()] = '0';
            }
        }

        return $addToCart['customer_options'];
    }

    /**
     * Checks if all of the conditions are met
     *
     * @param array $conditions
     * @param array $customerOptionConfig
     *
     * @return bool
     */
    private function allConditionsMet(array $conditions, array $customerOptionConfig): bool
    {
        $result = true;

        /** @var ConditionInterface $condition */
        foreach ($conditions as $condition) {
            $customerOption = $condition->getCustomerOption();

            if ($customerOption === null) {
                continue;
            }

            $counter = 0;

            /** @var OrderItemOptionInterface $optionValue */
            foreach ($customerOptionConfig as $optionCode => $optionValue) {
                if ($optionCode === $customerOption->getCode()) {
                    if (!$condition->isMet($optionValue, $customerOption->getType())) {
                        $result = false;
                    }

                    break;
                }

                ++$counter;
            }

            if ($counter >= count($customerOptionConfig)) {
                $result = false;
            }
        }

        return $result;
    }
}

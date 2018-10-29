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
    /** @var RequestStack */
    private $requestStack;

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
        if ($constraint instanceof ConditionalConstraint) {
            if ($value instanceof OrderItemInterface) {
                $configuration = $this->getCustomerOptionsFromRequest($this->requestStack->getCurrentRequest(), $value->getProduct());
            } else {
                $configuration = is_array($value) ? $value : [$value];
            }

            $allConditionsMet   = $this->allConditionsMet($constraint->conditions, $configuration);
            $allConstraintsMet  = $this->allConditionsMet($constraint->constraints, $configuration);

            if ($allConditionsMet) {
                if (!$allConstraintsMet) {
                    $this->context->addViolation($constraint->message);
                }
            }
        }
    }

    private function getCustomerOptionsFromRequest(Request $request, ProductInterface $product): array
    {
        $addToCart = $request->request->get('sylius_add_to_cart');

        if (!isset($addToCart['customer_options'])) {
            return [];
        }

        $customerOptions = $product->getCustomerOptions();

        foreach ($customerOptions as $customerOption) {
            if (!in_array($customerOption->getCode(), array_keys($addToCart['customer_options']), true)) {
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

            /** @var OrderItemOptionInterface $optionConfig */
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

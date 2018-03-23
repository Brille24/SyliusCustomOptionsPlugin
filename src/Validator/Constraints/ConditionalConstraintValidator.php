<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Validator\Constraints;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\Validator\ConditionInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConditionalConstraintValidator extends ConstraintValidator
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof OrderItemInterface && $constraint instanceof ConditionalConstraint) {
            $configuration = $this->getCustomerOptionsFromRequest($this->requestStack->getCurrentRequest(), $value->getProduct());

            $allConditionsMet = $this->allConditionsMet($constraint->conditions, $configuration);

            if ($allConditionsMet) {
                if (!$this->allConditionsMet($constraint->constraints, $configuration)) {
                    $this->context->addViolation($constraint->message);
                }
            }
        }
    }

    private function getCustomerOptionsFromRequest(Request $request, ProductInterface $product)
    {
        $addToCart = $request->request->get('sylius_add_to_cart');

        if (!isset($addToCart['customer_options'])) {
            return [];
        }

        $customerOptions = $product->getCustomerOptions();

        foreach ($customerOptions as $customerOption) {
            if (!in_array($customerOption->getCode(), array_keys($addToCart['customer_options']))) {
                $addToCart['customer_options'][$customerOption->getCode()] = '0';
            }
        }

        return $addToCart['customer_options'];
    }

    private function allConditionsMet(array $conditions, array $customerOptionConfig)
    {
        $result = true;

        /** @var ConditionInterface $condition */
        foreach ($conditions as $condition) {
            $customerOption = $condition->getCustomerOption();

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

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

namespace Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints;

use Brille24\SyliusCustomerOptionsPlugin\Validator\CustomerOptionAssociationConstraintValidator;
use Symfony\Component\Validator\Constraint;

class CustomerOptionAssociationConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.ui.code';

    /** {@inheritdoc} */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy(): string
    {
        return CustomerOptionAssociationConstraintValidator::class;
    }
}

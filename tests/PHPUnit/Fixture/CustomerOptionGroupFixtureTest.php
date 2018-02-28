<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\PHPUnit\Fixture;


use Brille24\CustomerOptionsPlugin\Fixture\CustomerOptionGroupFixture;
use Brille24\CustomerOptionsPlugin\Fixture\Factory\CustomerOptionGroupFactory;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class CustomerOptionGroupFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;


    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new CustomerOptionGroupFixture(
            $this->createMock(CustomerOptionGroupFactory::class)
        );
    }
}
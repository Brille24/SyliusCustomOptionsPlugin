<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230615072300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Support doctrine/dbal >= 3.0';
    }

    public function up(Schema $schema): void
    {
        if ('postgresql' === $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang PostgreSQL */ 'COMMENT ON COLUMN brille24_customer_option.configuration IS NULL');
            $this->addSql(/* @lang PostgreSQL */ 'COMMENT ON COLUMN brille24_customer_option_group_validator_condition.value IS NULL');
            $this->addSql(/* @lang PostgreSQL */ 'COMMENT ON COLUMN brille24_customer_option_group_validator_constraint.value IS NULL');
        } elseif ('mysql' === $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option CHANGE configuration configuration LONGTEXT NOT NULL');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator_condition CHANGE value value LONGTEXT NULL');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator_constraint CHANGE value value LONGTEXT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        if ('postgresql' === $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang PostgreSQL */ 'COMMENT ON COLUMN brille24_customer_option.configuration IS \'(DC2Type:json_array)\'');
            $this->addSql(/* @lang PostgreSQL */ 'COMMENT ON COLUMN brille24_customer_option_group_validator_condition.value IS \'(DC2Type:json_array)\'');
            $this->addSql(/* @lang PostgreSQL */ 'COMMENT ON COLUMN brille24_customer_option_group_validator_constraint.value IS \'(DC2Type:json_array)\'');
        } elseif ('mysql' === $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option CHANGE configuration configuration LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\'');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator_condition CHANGE value value LONGTEXT NULL COMMENT \'(DC2Type:json_array)\'');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator_constraint CHANGE value value LONGTEXT NULL COMMENT \'(DC2Type:json_array)\'');
        }
    }
}

<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210317090200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if ('postgresql' !== $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option ALTER COLUMN optionValue TYPE VARCHAR(255)');
        } elseif ('mysql' !== $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option CHANGE optionValue optionValue VARCHAR(255) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        if ('postgresql' !== $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option ALTER COLUMN optionValue TYPE TEXT ');
        } elseif ('mysql' !== $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option CHANGE optionValue optionValue LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
        }
    }
}

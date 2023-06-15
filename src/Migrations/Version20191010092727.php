<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191010092727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(!in_array($this->connection->getDatabasePlatform()->getName(), ['mysql', 'postgresql']), 'Migration can only be executed safely on \'mysql\' and \'postgresql\'.');

        if ('postgresql' !== $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option ADD COLUMN customerOptionType VARCHAR(255) NOT NULL DEFAULT \'\'');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option ALTER COLUMN optionValue TYPE TEXT');
        } elseif ('mysql' !== $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option ADD customerOptionType VARCHAR(255) NOT NULL, CHANGE optionValue optionValue LONGTEXT DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(!in_array($this->connection->getDatabasePlatform()->getName(), ['mysql', 'postgresql']), 'Migration can only be executed safely on \'mysql\' and \'postgresql\'.');

        if ('postgresql' !== $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option DROP COLUMN customerOptionType');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option ALTER COLUMN optionValue TYPE VARCHAR(255)');
        } elseif ('mysql' !== $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option DROP customerOptionType, CHANGE optionValue optionValue VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        }
    }
}

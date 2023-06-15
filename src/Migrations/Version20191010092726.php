<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191010092726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if ('postgresql' === $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE BRILLE24_CUSTOMER_OPTION (id SERIAL NOT NULL, type VARCHAR(255) NOT NULL, configuration JSONB NOT NULL, code VARCHAR(255) NOT NULL, required BOOLEAN NOT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE UNIQUE INDEX UNIQ_1E7F7D0677153098 ON BRILLE24_CUSTOMER_OPTION(code)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_association (id SERIAL NOT NULL, option_id INT NOT NULL, group_id INT NOT NULL, position INT NOT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_1af36ed0a7c41d6f ON brille24_customer_option_association(option_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_1af36ed0fe54d947 ON brille24_customer_option_association(group_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE UNIQUE INDEX UNIQ_1AF36ED0A7C41D6FFE54D947 ON brille24_customer_option_association(option_id, group_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_date_range (id SERIAL NOT NULL, start TIMESTAMP NOT NULL, "end" TIMESTAMP NOT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_group (id SERIAL NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_group_translation (id SERIAL NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL,  PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_dd9f6eb32c2ac5d3 ON brille24_customer_option_group_translation(translatable_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE UNIQUE INDEX brille24_customer_option_group_translation_uniq_trans ON brille24_customer_option_group_translation(translatable_id, locale)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_group_validator (id SERIAL NOT NULL, errorMessage_id INT DEFAULT NULL, customerOptionGroup_id INT DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE UNIQUE INDEX UNIQ_1C13475874E6266C ON brille24_customer_option_group_validator(errorMessage_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_1c1347586dcf05ec ON brille24_customer_option_group_validator(customerOptionGroup_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_group_validator_condition (id SERIAL NOT NULL, validator_id INT DEFAULT NULL, comparator VARCHAR(255) NOT NULL, value jsonb DEFAULT NULL, customerOption_id INT DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_b230415eb0644aec ON brille24_customer_option_group_validator_condition(validator_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_b230415e27309983 ON brille24_customer_option_group_validator_condition(customerOption_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_group_validator_constraint (id SERIAL NOT NULL, validator_id INT DEFAULT NULL, comparator VARCHAR(255) NOT NULL, value jsonb DEFAULT NULL, customerOption_id INT DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_5a4304e2b0644aec ON brille24_customer_option_group_validator_constraint(validator_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_5a4304e227309983 ON brille24_customer_option_group_validator_constraint(customerOption_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_order_item_option (id SERIAL NOT NULL, customerOptionCode VARCHAR(255) NOT NULL, customerOptionName VARCHAR(255) NOT NULL, customeroptionValueCode VARCHAR(255) DEFAULT NULL, customeroptionValueName VARCHAR(255) DEFAULT NULL, optionValue VARCHAR(255) DEFAULT NULL, fixedPrice INT NOT NULL, percent DOUBLE PRECISION NOT NULL, pricingType VARCHAR(255) NOT NULL, orderItem_id INT DEFAULT NULL, customerOption_id INT DEFAULT NULL, customerOptionValue_id INT DEFAULT NULL, customerOptionType VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_8b833ee4e76e9c94 ON brille24_customer_option_order_item_option(orderItem_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_8b833ee427309983 ON brille24_customer_option_order_item_option(customerOption_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_8b833ee46abb6709 ON brille24_customer_option_order_item_option(customerOptionValue_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_translation (id SERIAL NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_531f0a732c2ac5d3 ON brille24_customer_option_translation(translatable_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE UNIQUE INDEX brille24_customer_option_translation_uniq_trans ON brille24_customer_option_translation(translatable_id, locale)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_value (id SERIAL NOT NULL, code VARCHAR(255) NOT NULL, customerOption_id INT DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_65b04d7b27309983 ON brille24_customer_option_value(customerOption_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE UNIQUE INDEX UNIQ_65B04D7B2730998377153098 ON brille24_customer_option_value(customerOption_id, code)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_value_price (id SERIAL NOT NULL, channel_id INT DEFAULT NULL, product_id INT DEFAULT NULL, percent DOUBLE PRECISION NOT NULL, amount INT NOT NULL, type VARCHAR(12) NOT NULL, datevalid_id INT DEFAULT NULL, customerOptionValue_id INT DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE UNIQUE INDEX UNIQ_D218E8EE50552292 ON brille24_customer_option_value_price(dateValid_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_d218e8ee6abb6709 ON brille24_customer_option_value_price(customerOptionValue_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_d218e8ee72f5a1aa ON brille24_customer_option_value_price(channel_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_d218e8ee4584665a ON brille24_customer_option_value_price(product_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_value_translation (id SERIAL NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_3ad2c9512c2ac5d3 ON brille24_customer_option_value_translation(translatable_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE UNIQUE INDEX brille24_customer_option_value_translation_uniq_trans ON brille24_customer_option_value_translation(translatable_id, locale)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_validator_error_message (id SERIAL NOT NULL, validator_id INT DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE UNIQUE INDEX UNIQ_5535DA3B0644AEC ON brille24_validator_error_message(validator_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_validator_error_message_translation (id SERIAL NOT NULL, translatable_id INT DEFAULT NULL, message VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_4e22795e2c2ac5d3 ON brille24_validator_error_message_translation(translatable_id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE UNIQUE INDEX brille24_validator_error_message_translation_uniq_trans ON brille24_validator_error_message_translation(translatable_id, locale)');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_association ADD CONSTRAINT fk_1af36ed0a7c41d6f FOREIGN KEY (option_id) REFERENCES brille24_customer_option (id) ON DELETE CASCADE');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_association ADD CONSTRAINT fk_1af36ed0fe54d947 FOREIGN KEY (group_id) REFERENCES brille24_customer_option_group (id) ON DELETE CASCADE');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_translation ADD CONSTRAINT fk_dd9f6eb32c2ac5d3 FOREIGN KEY (translatable_id) REFERENCES brille24_customer_option_group (id)');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_validator ADD CONSTRAINT fk_1c13475874e6266c FOREIGN KEY (errorMessage_id) REFERENCES brille24_validator_error_message (id) ON DELETE SET NULL');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_validator ADD CONSTRAINT fk_1c1347586dcf05ec FOREIGN KEY (customerOptionGroup_id) REFERENCES brille24_customer_option_group (id)');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_validator_condition ADD CONSTRAINT fk_b230415eb0644aec FOREIGN KEY (validator_id) REFERENCES brille24_customer_option_group_validator (id) ON DELETE CASCADE');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_validator_condition ADD CONSTRAINT fk_b230415e27309983 FOREIGN KEY (customerOption_id) REFERENCES brille24_customer_option (id) ON DELETE SET NULL');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_validator_constraint ADD CONSTRAINT fk_5a4304e2b0644aec FOREIGN KEY (validator_id) REFERENCES brille24_customer_option_group_validator (id) ON DELETE CASCADE');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_validator_constraint ADD CONSTRAINT fk_5a4304e227309983 FOREIGN KEY (customerOption_id) REFERENCES brille24_customer_option (id) ON DELETE SET NULL');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option ADD CONSTRAINT fk_8b833ee4e76e9c94 FOREIGN KEY (orderItem_id) REFERENCES sylius_order_item (id)');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option ADD CONSTRAINT fk_8b833ee427309983 FOREIGN KEY (customerOption_id) REFERENCES brille24_customer_option (id) ON DELETE SET NULL');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option ADD CONSTRAINT fk_8b833ee46abb6709 FOREIGN KEY (customerOptionValue_id) REFERENCES brille24_customer_option_value (id) ON DELETE SET NULL');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_translation ADD CONSTRAINT fk_531f0a732c2ac5d3 FOREIGN KEY (translatable_id) REFERENCES brille24_customer_option (id)');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_value ADD CONSTRAINT fk_65b04d7b27309983 FOREIGN KEY (customerOption_id) REFERENCES brille24_customer_option (id)');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_value_price ADD CONSTRAINT fk_d218e8ee50552292 FOREIGN KEY (datevalid_id) REFERENCES brille24_customer_option_date_range (id)');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_value_price ADD CONSTRAINT fk_d218e8ee6abb6709 FOREIGN KEY (customerOptionValue_id) REFERENCES brille24_customer_option_value (id)');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_value_price ADD CONSTRAINT fk_d218e8ee72f5a1aa FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_value_price ADD CONSTRAINT fk_d218e8ee4584665a FOREIGN KEY (product_id) REFERENCES sylius_product (id)');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_value_translation ADD CONSTRAINT fk_3ad2c9512c2ac5d3 FOREIGN KEY (translatable_id) REFERENCES brille24_customer_option_value (id) ON DELETE CASCADE');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_validator_error_message ADD CONSTRAINT fk_5535da3b0644aec FOREIGN KEY (validator_id) REFERENCES brille24_customer_option_group_validator (id) ON DELETE CASCADE');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_validator_error_message_translation ADD CONSTRAINT fk_4e22795e2c2ac5d3 FOREIGN KEY (translatable_id) REFERENCES brille24_validator_error_message (id) ON DELETE CASCADE');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE sylius_product ADD customerOptionGroup_id INT DEFAULT NULL');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE sylius_product ADD CONSTRAINT fk_677b9b746dcf05ec FOREIGN KEY (customerOptionGroup_id) REFERENCES brille24_customer_option_group (id) ON DELETE SET NULL');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE INDEX idx_677b9b746dcf05ec ON sylius_product (customerOptionGroup_id)');
        } elseif ('mysql' === $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, configuration LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', code VARCHAR(255) NOT NULL, required TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1E7F7D0677153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_association (id INT AUTO_INCREMENT NOT NULL, option_id INT NOT NULL, group_id INT NOT NULL, position INT NOT NULL, INDEX IDX_1AF36ED0A7C41D6F (option_id), INDEX IDX_1AF36ED0FE54D947 (group_id), UNIQUE INDEX UNIQ_1AF36ED0A7C41D6FFE54D947 (option_id, group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_date_range (id INT AUTO_INCREMENT NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_group (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_group_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_DD9F6EB32C2AC5D3 (translatable_id), UNIQUE INDEX brille24_customer_option_group_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_group_validator (id INT AUTO_INCREMENT NOT NULL, errorMessage_id INT DEFAULT NULL, customerOptionGroup_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_1C13475874E6266C (errorMessage_id), INDEX IDX_1C1347586DCF05EC (customerOptionGroup_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_group_validator_condition (id INT AUTO_INCREMENT NOT NULL, validator_id INT DEFAULT NULL, comparator VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', customerOption_id INT DEFAULT NULL, INDEX IDX_B230415EB0644AEC (validator_id), INDEX IDX_B230415E27309983 (customerOption_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_group_validator_constraint (id INT AUTO_INCREMENT NOT NULL, validator_id INT DEFAULT NULL, comparator VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', customerOption_id INT DEFAULT NULL, INDEX IDX_5A4304E2B0644AEC (validator_id), INDEX IDX_5A4304E227309983 (customerOption_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_order_item_option (id INT AUTO_INCREMENT NOT NULL, customerOptionCode VARCHAR(255) NOT NULL, customerOptionName VARCHAR(255) NOT NULL, customerOptionValueCode VARCHAR(255) DEFAULT NULL, customerOptionValueName VARCHAR(255) DEFAULT NULL, optionValue LONGTEXT DEFAULT NULL, fixedPrice INT NOT NULL, percent DOUBLE PRECISION NOT NULL, pricingType VARCHAR(255) NOT NULL, orderItem_id INT DEFAULT NULL, customerOption_id INT DEFAULT NULL, customerOptionValue_id INT DEFAULT NULL, INDEX IDX_8B833EE4E76E9C94 (orderItem_id), INDEX IDX_8B833EE427309983 (customerOption_id), INDEX IDX_8B833EE46ABB6709 (customerOptionValue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_531F0A732C2AC5D3 (translatable_id), UNIQUE INDEX brille24_customer_option_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_value (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, customerOption_id INT DEFAULT NULL, INDEX IDX_65B04D7B27309983 (customerOption_id), UNIQUE INDEX UNIQ_65B04D7B2730998377153098 (customerOption_id, code), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_value_price (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, product_id INT DEFAULT NULL, percent DOUBLE PRECISION NOT NULL, amount INT NOT NULL, type VARCHAR(12) NOT NULL, dateValid_id INT DEFAULT NULL, customerOptionValue_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_D218E8EE50552292 (dateValid_id), INDEX IDX_D218E8EE6ABB6709 (customerOptionValue_id), INDEX IDX_D218E8EE72F5A1AA (channel_id), INDEX IDX_D218E8EE4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_value_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_3AD2C9512C2AC5D3 (translatable_id), UNIQUE INDEX brille24_customer_option_value_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_validator_error_message (id INT AUTO_INCREMENT NOT NULL, validator_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_5535DA3B0644AEC (validator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_validator_error_message_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, message VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_4E22795E2C2AC5D3 (translatable_id), UNIQUE INDEX brille24_validator_error_message_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_association ADD CONSTRAINT FK_1AF36ED0A7C41D6F FOREIGN KEY (option_id) REFERENCES brille24_customer_option (id) ON DELETE CASCADE');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_association ADD CONSTRAINT FK_1AF36ED0FE54D947 FOREIGN KEY (group_id) REFERENCES brille24_customer_option_group (id) ON DELETE CASCADE');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_translation ADD CONSTRAINT FK_DD9F6EB32C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES brille24_customer_option_group (id)');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator ADD CONSTRAINT FK_1C13475874E6266C FOREIGN KEY (errorMessage_id) REFERENCES brille24_validator_error_message (id) ON DELETE SET NULL');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator ADD CONSTRAINT FK_1C1347586DCF05EC FOREIGN KEY (customerOptionGroup_id) REFERENCES brille24_customer_option_group (id)');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator_condition ADD CONSTRAINT FK_B230415EB0644AEC FOREIGN KEY (validator_id) REFERENCES brille24_customer_option_group_validator (id) ON DELETE CASCADE');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator_condition ADD CONSTRAINT FK_B230415E27309983 FOREIGN KEY (customerOption_id) REFERENCES brille24_customer_option (id) ON DELETE SET NULL');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator_constraint ADD CONSTRAINT FK_5A4304E2B0644AEC FOREIGN KEY (validator_id) REFERENCES brille24_customer_option_group_validator (id) ON DELETE CASCADE');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator_constraint ADD CONSTRAINT FK_5A4304E227309983 FOREIGN KEY (customerOption_id) REFERENCES brille24_customer_option (id) ON DELETE SET NULL');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option ADD CONSTRAINT FK_8B833EE4E76E9C94 FOREIGN KEY (orderItem_id) REFERENCES sylius_order_item (id)');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option ADD CONSTRAINT FK_8B833EE427309983 FOREIGN KEY (customerOption_id) REFERENCES brille24_customer_option (id) ON DELETE SET NULL');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option ADD CONSTRAINT FK_8B833EE46ABB6709 FOREIGN KEY (customerOptionValue_id) REFERENCES brille24_customer_option_value (id) ON DELETE SET NULL');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_translation ADD CONSTRAINT FK_531F0A732C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES brille24_customer_option (id)');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_value ADD CONSTRAINT FK_65B04D7B27309983 FOREIGN KEY (customerOption_id) REFERENCES brille24_customer_option (id)');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_value_price ADD CONSTRAINT FK_D218E8EE50552292 FOREIGN KEY (dateValid_id) REFERENCES brille24_customer_option_date_range (id)');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_value_price ADD CONSTRAINT FK_D218E8EE6ABB6709 FOREIGN KEY (customerOptionValue_id) REFERENCES brille24_customer_option_value (id)');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_value_price ADD CONSTRAINT FK_D218E8EE72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_value_price ADD CONSTRAINT FK_D218E8EE4584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id)');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_value_translation ADD CONSTRAINT FK_3AD2C9512C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES brille24_customer_option_value (id) ON DELETE CASCADE');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_validator_error_message ADD CONSTRAINT FK_5535DA3B0644AEC FOREIGN KEY (validator_id) REFERENCES brille24_customer_option_group_validator (id) ON DELETE CASCADE');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_validator_error_message_translation ADD CONSTRAINT FK_4E22795E2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES brille24_validator_error_message (id) ON DELETE CASCADE');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE sylius_product ADD customerOptionGroup_id INT DEFAULT NULL');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE sylius_product ADD CONSTRAINT FK_677B9B746DCF05EC FOREIGN KEY (customerOptionGroup_id) REFERENCES brille24_customer_option_group (id) ON DELETE SET NULL');
            $this->addSql(/* @lang MySQL */ 'CREATE INDEX IDX_677B9B746DCF05EC ON sylius_product (customerOptionGroup_id)');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        if ('postgresql' === $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_association DROP CONSTRAINT fk_1af36ed0a7c41d6f');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_validator_condition DROP CONSTRAINT fk_b230415e27309983');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_validator_constraint DROP CONSTRAINT fk_5a4304e227309983');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option DROP CONSTRAINT fk_8b833ee427309983');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_translation DROP CONSTRAINT fk_531f0a732c2ac5d3');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_value DROP CONSTRAINT fk_65b04d7b27309983');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_value_price DROP CONSTRAINT fk_d218e8ee50552292');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_association DROP CONSTRAINT fk_1af36ed0fe54d947');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_translation DROP CONSTRAINT fk_dd9f6eb32c2ac5d3');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_validator DROP CONSTRAINT fk_1c1347586dcf05ec');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE sylius_product DROP CONSTRAINT fk_677b9b746dcf05ec');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_validator_condition DROP CONSTRAINT fk_b230415eb0644aec');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_validator_constraint DROP CONSTRAINT fk_5a4304e2b0644aec');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_validator_error_message DROP CONSTRAINT fk_5535da3b0644aec');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option DROP CONSTRAINT fk_8b833ee46abb6709');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_value_price DROP CONSTRAINT fk_d218e8ee6abb6709');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_value_translation DROP CONSTRAINT fk_3ad2c9512c2ac5d3');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_group_validator DROP CONSTRAINT fk_1c13475874e6266c');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_validator_error_message_translation DROP CONSTRAINT fk_4e22795e2c2ac5d3');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_association');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_date_range');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_group');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_group_translation');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_group_validator');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_group_validator_condition');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_group_validator_constraint');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_order_item_option');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_translation');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_value');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_value_price');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_value_translation');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_validator_error_message');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_validator_error_message_translation');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_file_content');
            $this->addSql(/* @lang PostgreSQL */ 'DROP INDEX idx_677b9b746dcf05ec');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE sylius_product DROP customerOptionGroup_id');
        } elseif ('mysql' === $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_association DROP FOREIGN KEY FK_1AF36ED0A7C41D6F');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator_condition DROP FOREIGN KEY FK_B230415E27309983');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator_constraint DROP FOREIGN KEY FK_5A4304E227309983');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option DROP FOREIGN KEY FK_8B833EE427309983');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_translation DROP FOREIGN KEY FK_531F0A732C2AC5D3');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_value DROP FOREIGN KEY FK_65B04D7B27309983');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_value_price DROP FOREIGN KEY FK_D218E8EE50552292');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_association DROP FOREIGN KEY FK_1AF36ED0FE54D947');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_translation DROP FOREIGN KEY FK_DD9F6EB32C2AC5D3');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator DROP FOREIGN KEY FK_1C1347586DCF05EC');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE sylius_product DROP FOREIGN KEY FK_677B9B746DCF05EC');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator_condition DROP FOREIGN KEY FK_B230415EB0644AEC');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator_constraint DROP FOREIGN KEY FK_5A4304E2B0644AEC');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_validator_error_message DROP FOREIGN KEY FK_5535DA3B0644AEC');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option DROP FOREIGN KEY FK_8B833EE46ABB6709');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_value_price DROP FOREIGN KEY FK_D218E8EE6ABB6709');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_value_translation DROP FOREIGN KEY FK_3AD2C9512C2AC5D3');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_group_validator DROP FOREIGN KEY FK_1C13475874E6266C');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_validator_error_message_translation DROP FOREIGN KEY FK_4E22795E2C2AC5D3');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_association');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_date_range');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_group');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_group_translation');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_group_validator');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_group_validator_condition');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_group_validator_constraint');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_order_item_option');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_translation');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_value');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_value_price');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_value_translation');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_validator_error_message');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_validator_error_message_translation');
            $this->addSql(/* @lang MySQL */ 'DROP INDEX IDX_677B9B746DCF05EC ON sylius_product');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE sylius_product DROP customerOptionGroup_id');
        }
    }
}

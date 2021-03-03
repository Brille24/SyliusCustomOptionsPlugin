<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210303184909 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brille24_customer_option_file_content (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE brille24_customer_option_order_item_option ADD fileContent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE brille24_customer_option_order_item_option ADD CONSTRAINT FK_8B833EE486EBE56 FOREIGN KEY (fileContent_id) REFERENCES brille24_customer_option_file_content (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8B833EE486EBE56 ON brille24_customer_option_order_item_option (fileContent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE brille24_customer_option_order_item_option DROP FOREIGN KEY FK_8B833EE486EBE56');
        $this->addSql('DROP TABLE brille24_customer_option_file_content');
        $this->addSql('DROP INDEX UNIQ_8B833EE486EBE56 ON brille24_customer_option_order_item_option');
        $this->addSql('ALTER TABLE brille24_customer_option_order_item_option DROP fileContent_id');
    }
}

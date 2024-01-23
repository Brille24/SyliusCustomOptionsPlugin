<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191010092728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(!in_array($this->connection->getDatabasePlatform()->getName(), ['mysql', 'postgresql']), 'Migration can only be executed safely on \'mysql\' and \'postgresql\'.');

        $this->addSql('UPDATE sylius_adjustment SET type = "customer_option" WHERE type = "CUSTOMER_OPTION_ADJUSTMENT"');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(!in_array($this->connection->getDatabasePlatform()->getName(), ['mysql', 'postgresql']), 'Migration can only be executed safely on \'mysql\' and \'postgresql\'.');

        $this->addSql('UPDATE sylius_adjustment SET type = "CUSTOMER_OPTION_ADJUSTMENT" WHERE type = "customer_option"');
    }
}

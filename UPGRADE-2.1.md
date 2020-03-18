# Upgrade from 2.0 to 2.1
The name of the Customer Option Adjustment constant has changed. Please create a new migration with the following content:
```php
final class Version20191010092721 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE sylius_adjustment SET type = "customer_option" WHERE type = "CUSTOMER_OPTION_ADJUSTMENT"');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE sylius_adjustment SET type = "CUSTOMER_OPTION_ADJUSTMENT" WHERE type = "customer_option"');
    }
}
```
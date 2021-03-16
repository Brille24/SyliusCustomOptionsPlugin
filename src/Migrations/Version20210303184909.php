<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Migrations;

use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210303184909 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ?ContainerInterface */
    private $container;

    public function setContainer(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }

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

        $id = 1;
        foreach ($this->getOrderItemOptionsWithValues() as $orderItemOption) {
            $fileContent = $orderItemOption['optionValue'];

            $this->addSql('INSERT INTO brille24_customer_option_file_content (id, content) VALUES(:id, :content)', ['id' => $id, 'content' => $fileContent]);
            $this->addSql('UPDATE brille24_customer_option_order_item_option SET fileContent_id = :file_content_id, optionValue = "file-content" WHERE id = :id', ['file_content_id' => $id, 'id' => $orderItemOption['id']]);

            $id++;
        }
    }

    public function down(Schema $schema) : void
    {
        foreach ($this->getOrderItemOptionsWithFileContent() as $orderItemOption) {
            $fileContent = $orderItemOption['content'];
            $id = $orderItemOption['id'];

            $this->addSql('UPDATE brille24_customer_option_order_item_option SET optionValue = :content WHERE id = :id', ['content' => $fileContent, 'id' => $id]);
        }

        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE brille24_customer_option_order_item_option DROP FOREIGN KEY FK_8B833EE486EBE56');
        $this->addSql('DROP TABLE brille24_customer_option_file_content');
        $this->addSql('DROP INDEX UNIQ_8B833EE486EBE56 ON brille24_customer_option_order_item_option');
        $this->addSql('ALTER TABLE brille24_customer_option_order_item_option DROP fileContent_id');
    }

    private function getOrderItemOptionsWithValues(): array
    {
        $productAttributeClass = $this->container->getParameter('brille24.model.order_item_option.class');

        $entityManager = $this->getEntityManager($productAttributeClass);

        return $entityManager->createQueryBuilder()
            ->select('o.id, o.optionValue')
            ->from($productAttributeClass, 'o')
            ->where('o.customerOptionType = :type')
            ->setParameter('type', CustomerOptionTypeEnum::FILE)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    private function getOrderItemOptionsWithFileContent(): array
    {
        $productAttributeClass = $this->container->getParameter('brille24.model.order_item_option.class');

        $entityManager = $this->getEntityManager($productAttributeClass);

        return $entityManager->createQueryBuilder()
            ->select('o.id, f.content')
            ->from($productAttributeClass, 'o')
            ->join('o.fileContent', 'f')
            ->where('o.customerOptionType = :type')
            ->setParameter('type', CustomerOptionTypeEnum::FILE)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    private function getEntityManager(string $class): EntityManagerInterface
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = $this->container->get('doctrine');

        /** @var EntityManagerInterface $manager */
        $manager = $managerRegistry->getManagerForClass($class);

        return $manager;
    }
}

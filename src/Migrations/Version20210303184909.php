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
    private ?\Symfony\Component\DependencyInjection\ContainerInterface $container = null;

    public function setContainer(?ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        if ('postgresql' !== $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang PostgreSQL */ 'CREATE TABLE brille24_customer_option_file_content (id SERIAL NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option ADD fileContent_id INT DEFAULT NULL');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option ADD CONSTRAINT FK_8B833EE486EBE56 FOREIGN KEY (fileContent_id) REFERENCES brille24_customer_option_file_content (id)');
            $this->addSql(/* @lang PostgreSQL */ 'CREATE UNIQUE INDEX UNIQ_8B833EE486EBE56 ON brille24_customer_option_order_item_option (fileContent_id)');
        } elseif ('mysql' !== $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang MySQL */ 'CREATE TABLE brille24_customer_option_file_content (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option ADD fileContent_id INT DEFAULT NULL');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option ADD CONSTRAINT FK_8B833EE486EBE56 FOREIGN KEY (fileContent_id) REFERENCES brille24_customer_option_file_content (id)');
            $this->addSql(/* @lang MySQL */ 'CREATE UNIQUE INDEX UNIQ_8B833EE486EBE56 ON brille24_customer_option_order_item_option (fileContent_id)');
        }

        $id = 1;
        foreach ($this->getOrderItemOptionsWithValues() as $orderItemOption) {
            $fileContent = $orderItemOption['optionValue'];

            $this->addSql('INSERT INTO brille24_customer_option_file_content (id, content) VALUES(:id, :content)', ['id' => $id, 'content' => $fileContent]);
            $this->addSql('UPDATE brille24_customer_option_order_item_option SET fileContent_id = :file_content_id, optionValue = "file-content" WHERE id = :id', ['file_content_id' => $id, 'id' => $orderItemOption['id']]);

            ++$id;
        }
    }

    public function down(Schema $schema): void
    {


        foreach ($this->getOrderItemOptionsWithFileContent() as $orderItemOption) {
            $fileContent = $orderItemOption['content'];
            $id          = $orderItemOption['id'];

            $this->addSql('UPDATE brille24_customer_option_order_item_option SET optionValue = :content WHERE id = :id', ['content' => $fileContent, 'id' => $id]);
        }

        // this down() migration is auto-generated, please modify it to your needs
        if ('postgresql' !== $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option DROP CONSTRAINT FK_8B833EE486EBE56');
            $this->addSql(/* @lang PostgreSQL */ 'DROP TABLE brille24_customer_option_file_content');
            $this->addSql(/* @lang PostgreSQL */ 'DROP INDEX UNIQ_8B833EE486EBE56');
            $this->addSql(/* @lang PostgreSQL */ 'ALTER TABLE brille24_customer_option_order_item_option DROP fileContent_id');
        } elseif ('mysql' !== $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option DROP FOREIGN KEY FK_8B833EE486EBE56');
            $this->addSql(/* @lang MySQL */ 'DROP TABLE brille24_customer_option_file_content');
            $this->addSql(/* @lang MySQL */ 'DROP INDEX UNIQ_8B833EE486EBE56 ON brille24_customer_option_order_item_option');
            $this->addSql(/* @lang MySQL */ 'ALTER TABLE brille24_customer_option_order_item_option DROP fileContent_id');
        }
    }

    private function getOrderItemOptionsWithValues(): array
    {
        if ($this->container === null) {
            throw new \InvalidArgumentException('This migration needs the container to be set: '.self::class);
        }

        /** @var string $orderItemClass */
        $orderItemClass = $this->container->getParameter('brille24.model.order_item_option.class');

        $entityManager = $this->getEntityManager($orderItemClass);

        return $entityManager->createQueryBuilder()
            ->select('o.id, o.optionValue')
            ->from($orderItemClass, 'o')
            ->where('o.customerOptionType = :type')
            ->setParameter('type', CustomerOptionTypeEnum::FILE)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    private function getOrderItemOptionsWithFileContent(): array
    {
        if ($this->container === null) {
            throw new \InvalidArgumentException('This migration needs the container to be set: '.self::class);
        }

        /** @var string $orderItemClass */
        $orderItemClass = $this->container->getParameter('brille24.model.order_item_option.class');

        $entityManager = $this->getEntityManager($orderItemClass);

        return $entityManager->createQueryBuilder()
            ->select('o.id, f.content')
            ->from($orderItemClass, 'o')
            ->join('o.fileContent', 'f')
            ->where('o.customerOptionType = :type')
            ->setParameter('type', CustomerOptionTypeEnum::FILE)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    private function getEntityManager(string $class): EntityManagerInterface
    {
        if ($this->container === null) {
            throw new \InvalidArgumentException('This migration needs the container to be set: '.self::class);
        }

        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = $this->container->get('doctrine');

        /** @var EntityManagerInterface $manager */
        $manager = $managerRegistry->getManagerForClass($class);

        return $manager;
    }
}

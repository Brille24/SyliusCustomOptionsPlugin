<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Command;


use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class UpdateOrderItemOptionTypeCommand extends Command
{
    /** @var RepositoryInterface */
    private $orderItemOptionRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(RepositoryInterface $orderItemOptionRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->orderItemOptionRepository = $orderItemOptionRepository;
        $this->entityManager             = $entityManager;
    }

    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('b24:customer-options:update-order-item-options-type')
            ->setDescription('Updates the CustomerOption type on all OrderItemOptions if possible.')
        ;
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $i = 0;

        /** @var OrderItemOptionInterface $orderItemOption */
        foreach ($this->orderItemOptionRepository->findAll() as $orderItemOption) {
            $customerOption = $orderItemOption->getCustomerOption();

            if (null !== $customerOption) {
                $orderItemOption->setCustomerOptionType($customerOption->getType());

                $this->entityManager->persist($orderItemOption);

                ++$i;
                if (0 === $i % 100) {
                    $this->entityManager->flush();
                }
            }
        }
        $this->entityManager->flush();

        $output->writeln(sprintf('Updated %d OrderItemOptions', $i));
    }
}

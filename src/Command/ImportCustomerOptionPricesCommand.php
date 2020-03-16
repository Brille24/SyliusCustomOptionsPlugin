<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Command;

use Brille24\SyliusCustomerOptionsPlugin\Importer\CustomerOptionPriceCsvImporterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCustomerOptionPricesCommand extends Command
{
    /** @var CustomerOptionPriceCsvImporterInterface */
    protected $importer;

    public function __construct(CustomerOptionPriceCsvImporterInterface $importer)
    {
        parent::__construct();

        $this->importer = $importer;
    }

    protected function configure(): void
    {
        $this
            ->setName('b24:customer-options:import-prices')
            ->addArgument('source', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $source = $input->getArgument('source');
        $result = $this->importer->import($source);

        if (0 < $result['imported']) {
            $output->writeln(sprintf('Imported %s prices', $result['imported']));
        }
        if (0 < $result['failed']) {
            $output->writeln(sprintf('Failed to import %s prices', $result['failed']));
        }
    }
}

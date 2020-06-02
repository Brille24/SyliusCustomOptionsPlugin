<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Command;

use Brille24\SyliusCustomerOptionsPlugin\Importer\CustomerOptionPriceImporterInterface;
use Brille24\SyliusCustomerOptionsPlugin\Reader\CsvReaderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportCustomerOptionPricesCommand extends Command
{
    /** @var CsvReaderInterface */
    private $csvReader;

    /** @var CustomerOptionPriceImporterInterface */
    protected $importer;

    public function __construct(CsvReaderInterface $csvReader, CustomerOptionPriceImporterInterface $importer)
    {
        parent::__construct();

        $this->csvReader = $csvReader;
        $this->importer  = $importer;
    }

    protected function configure(): void
    {
        $this
            ->setName('b24:customer-options:import-prices')
            ->addArgument('source', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
        $data   = $this->csvReader->readCsv($source);
        $result = $this->importer->import($data);

        if (0 < $result->getImported()) {
            $output->writeln(sprintf('Imported %s prices', $result->getImported()));
        }
        if (0 < $result->getFailed()) {
            $output->writeln(sprintf('Failed to import %s prices', $result->getFailed()));
        }

        return 0;
    }
}

<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Controller;

use Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport\PriceImportByCsvType;
use Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport\PriceImportByProductListType;
use Brille24\SyliusCustomerOptionsPlugin\Importer\CustomerOptionPriceImporterInterface;
use Brille24\SyliusCustomerOptionsPlugin\Reader\CsvReaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class PriceImportController extends AbstractController
{
    /** @var CustomerOptionPriceImporterInterface */
    protected $priceImporter;

    /** @var string */
    protected $exampleFilePath;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var CsvReaderInterface */
    private $csvReader;

    public function __construct(
        CustomerOptionPriceImporterInterface $priceImporter,
        string $csvExampleFilePath,
        CsvReaderInterface $csvReader,
        TranslatorInterface $translator
    ) {
        $this->priceImporter   = $priceImporter;
        $this->exampleFilePath = $csvExampleFilePath;
        $this->translator      = $translator;
        $this->csvReader = $csvReader;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $csvForm = $this->createForm(PriceImportByCsvType::class);
        $csvForm->handleRequest($request);

        $productListForm = $this->createForm(PriceImportByProductListType::class);
        $productListForm->handleRequest($request);

        $this->handleCsvForm($csvForm);
        $this->handleProductListForm($productListForm);

        return $this->render('@Brille24SyliusCustomerOptionsPlugin/PriceImport/import.html.twig', ['csvForm' => $csvForm->createView(), 'byProductListForm' => $productListForm->createView()]);
    }

    /**
     * @return Response
     */
    public function downloadExampleFileAction(): Response
    {
        return $this->file($this->exampleFilePath);
    }

    /**
     * @param FormInterface $form
     */
    protected function handleCsvForm(FormInterface $form): void
    {
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            /** @var string $path */
            $path = $file->getRealPath();

            try {
                $data         = $this->csvReader->readCsv($path);
                $importResult = $this->priceImporter->import($data);

                // Handle flash messages
                if (0 < $importResult['imported']) {
                    $this->addFlash('success', $this->translator->trans(
                        'brille24.flashes.customer_option_prices_csv_imported',
                        ['%count%' => $importResult['imported']]
                    ));
                }
                if (0 < $importResult['failed']) {
                    $this->addFlash('error', $this->translator->trans(
                        'brille24.flashes.customer_option_prices_csv_import_failed',
                        ['%count%' => $importResult['failed']]
                    ));
                }
            } catch (\Throwable $exception) {
                $this->addFlash('error', 'brille24.flashes.customer_option_price_import_error');
            }
        }
    }

    /**
     * @param FormInterface $form
     */
    protected function handleProductListForm(FormInterface $form): void
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $products = $form->get('products')->getData();

            /** @var array $valuePriceData */
            $valuePriceData = $form->get('customer_option_value_price')->getData();

            $dateRange = $valuePriceData['dateValid'];
            $channel   = $valuePriceData['channel'];

            $importResult = $this->priceImporter->import($form->getData());

            // Build flash message parameters
            $flashParameters = [
                '%channel%'  => $channel->getCode(),
                '%products%' => implode(', ', $products),
            ];

            if (null !== $dateRange) {
                $flashParameters = array_merge($flashParameters, [
                    '%from%' => $dateRange->getStart()->format(DATE_ATOM),
                    '%to%'   => $dateRange->getEnd()->format(DATE_ATOM),
                ]);
            }

            // Handle flash messages
            if (0 < $importResult['imported']) {
                $messageId = 'brille24.flashes.customer_option_prices_product_list_imported';
                if (null !== $dateRange) {
                    $messageId .= '_with_date';
                }

                $this->addFlash('success', $this->translator->trans(
                    $messageId,
                    array_merge($flashParameters, ['%count%' => $importResult['imported']]),
                    'flashes'
                ));
            }

            if (0 < $importResult['failed']) {
                $messageId = 'brille24.flashes.customer_option_prices_product_list_import_failed';
                if (null !== $dateRange) {
                    $messageId .= '_with_date';
                }

                $this->addFlash('error', $this->translator->trans(
                    $messageId,
                    array_merge($flashParameters, ['%count%' => $importResult['failed']]),
                    'flashes'
                ));
            }
        }
    }
}

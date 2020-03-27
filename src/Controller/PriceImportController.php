<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Controller;

use Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport\PriceImportByCsvType;
use Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport\PriceImportByProductListType;
use Brille24\SyliusCustomerOptionsPlugin\Importer\CustomerOptionPriceByExampleImporterInterface;
use Brille24\SyliusCustomerOptionsPlugin\Importer\CustomerOptionPriceCsvImporterInterface;
use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class PriceImportController extends AbstractController
{
    /** @var CustomerOptionPriceCsvImporterInterface */
    protected $csvPriceImporter;

    /** @var string */
    protected $exampleFilePath;

    /** @var CustomerOptionPriceUpdaterInterface */
    protected $priceUpdater;

    /** @var CustomerOptionPriceByExampleImporterInterface */
    protected $priceByExampleImporter;

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(
        CustomerOptionPriceCsvImporterInterface $csvPriceImporter,
        CustomerOptionPriceByExampleImporterInterface $priceByExampleImporter,
        string $csvExampleFilePath,
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        TranslatorInterface $translator
    ) {
        $this->csvPriceImporter       = $csvPriceImporter;
        $this->priceByExampleImporter = $priceByExampleImporter;
        $this->exampleFilePath        = $csvExampleFilePath;
        $this->priceUpdater           = $priceUpdater;
        $this->translator             = $translator;
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
                $importResult = $this->csvPriceImporter->import($path);

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

            $importResult = $this->priceByExampleImporter->importForProducts(
                $products,
                $valuePriceData['customerOptionValues'],
                $dateRange,
                $channel,
                $valuePriceData['type'],
                $valuePriceData['amount'],
                $valuePriceData['percent']
            );

            // Handle flash messages
            if ($importResult['imported'] > 0) {
                if (null === $dateRange) {
                    $this->addFlash('success', $this->translator->trans(
                        'brille24.flashes.customer_option_prices_product_list_imported',
                        [
                            '%count%'    => $importResult['imported'],
                            '%channel%'  => $channel->getCode(),
                            '%products%' => implode(', ', $products),
                        ]
                    ));
                } else {
                    $this->addFlash('success', $this->translator->trans(
                        'brille24.flashes.customer_option_prices_product_list_imported_with_date',
                        [
                            '%count%'    => $importResult['imported'],
                            '%channel%'  => $channel->getCode(),
                            '%products%' => implode(', ', $products),
                            '%from%'     => $dateRange->getStart()->format(DATE_ATOM),
                            '%to%'       => $dateRange->getEnd()->format(DATE_ATOM),
                        ]
                    ));
                }
            }

            if ($importResult['failed'] > 0) {
                if (null === $dateRange) {
                    $this->addFlash('error', $this->translator->trans(
                        'brille24.flashes.customer_option_prices_product_list_import_failed',
                        [
                            '%count%'    => $importResult['failed'],
                            '%channel%'  => $channel->getCode(),
                            '%products%' => implode(', ', $products),
                        ]
                    ));
                } else {
                    $this->addFlash('error', $this->translator->trans(
                        'brille24.flashes.customer_option_prices_product_list_import_failed_with_date',
                        [
                            '%count%'    => $importResult['failed'],
                            '%channel%'  => $channel->getCode(),
                            '%products%' => implode(', ', $products),
                            '%from%'     => $dateRange->getStart()->format(DATE_ATOM),
                            '%to%'       => $dateRange->getEnd()->format(DATE_ATOM),
                        ]
                    ));
                }
            }
        }
    }
}

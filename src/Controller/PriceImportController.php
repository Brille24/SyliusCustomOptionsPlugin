<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Controller;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport\PriceImportByCsvType;
use Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport\PriceImportByProductListType;
use Brille24\SyliusCustomerOptionsPlugin\Handler\ImportErrorHandlerInterface;
use Brille24\SyliusCustomerOptionsPlugin\Importer\CustomerOptionPriceImporterInterface;
use Brille24\SyliusCustomerOptionsPlugin\Object\PriceImportResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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

    /** @var ImportErrorHandlerInterface */
    protected $csvImportErrorHandler;

    /** @var ImportErrorHandlerInterface */
    protected $productListImportErrorHandler;

    public function __construct(
        CustomerOptionPriceImporterInterface $priceImporter,
        string $csvExampleFilePath,
        TranslatorInterface $translator,
        ImportErrorHandlerInterface $csvImportErrorHandler,
        ImportErrorHandlerInterface $productListImportErrorHandler
    ) {
        $this->priceImporter                 = $priceImporter;
        $this->exampleFilePath               = $csvExampleFilePath;
        $this->translator                    = $translator;
        $this->csvImportErrorHandler         = $csvImportErrorHandler;
        $this->productListImportErrorHandler = $productListImportErrorHandler;
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

        try {
            $this->handleCsvForm($csvForm);
            $this->handleProductListForm($productListForm);
        } catch (\Throwable $exception) {
            $this->addFlash('error', 'brille24.flashes.customer_option_price_import_error');
        }

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
            $importResult = $this->priceImporter->import($form->getData());

            $this->handleFlashes($importResult, 'csv');

            $this->csvImportErrorHandler->handleErrors($importResult->getErrors(), []);
        }
    }

    /**
     * @param FormInterface $form
     */
    protected function handleProductListForm(FormInterface $form): void
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $productCodes = $form->get('products')->getData();

            /** @var array $valuePriceData */
            $valuePriceData = $form->get('customer_option_value_price')->getData();

            $dateRange = $valuePriceData['dateValid'];
            $channel   = $valuePriceData['channel'];

            $importResult = $this->priceImporter->import($form->getData());

            // Build error handler extra data
            $extraData = [
                'productCodes'         => $productCodes,
                'customerOptionValues' => array_map(static function (CustomerOptionValueInterface $customerOptionValue): string {
                    return $customerOptionValue->getCode();
                }, $valuePriceData['customerOptionValues']),
                'validFrom'            => null !== $dateRange ? $dateRange->getStart() : null,
                'validTo'              => null !== $dateRange ? $dateRange->getEnd() : null,
                'channel'              => $channel->getCode(),
                'type'                 => $valuePriceData['type'],
                'amount'               => $valuePriceData['amount'],
                'percent'              => $valuePriceData['percent'],
            ];

            // Build flash message parameters
            $flashParameters = [
                '%channel%'  => $channel->getCode(),
                '%products%' => implode(', ', $productCodes),
            ];

            if (null !== $dateRange) {
                $flashParameters = array_merge($flashParameters, [
                    '%from%' => $dateRange->getStart()->format(DATE_ATOM),
                    '%to%'   => $dateRange->getEnd()->format(DATE_ATOM),
                ]);

                $this->handleFlashes($importResult, 'product_list_with_date', $flashParameters);
            } else {
                $this->handleFlashes($importResult, 'product_list', $flashParameters);
            }

            $this->productListImportErrorHandler->handleErrors($importResult->getErrors(), $extraData);
        }
    }

    /**
     * @param PriceImportResult $importResult
     * @param string $type
     * @param array $flashParameters
     */
    protected function handleFlashes(
        PriceImportResult $importResult,
        string $type,
        array $flashParameters = []
    ): void {
        // Handle flash messages
        if (0 < $importResult->getImported()) {
            $this->addFlash('success', $this->translator->trans(
                'brille24.flashes.customer_option_prices_imported.'.$type,
                array_merge($flashParameters, ['%count%' => $importResult->getImported()])
            ));
        }
        if (0 < $importResult->getFailed()) {
            $this->addFlash('error', $this->translator->trans(
                'brille24.flashes.customer_option_prices_import_failed.'.$type,
                array_merge($flashParameters, ['%count%' => $importResult->getFailed()])
            ));
        }
    }
}

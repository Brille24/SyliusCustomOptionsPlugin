<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Controller;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport\PriceImportByCsvType;
use Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport\PriceImportByProductListType;
use Brille24\SyliusCustomerOptionsPlugin\Handler\ImportErrorHandlerInterface;
use Brille24\SyliusCustomerOptionsPlugin\Importer\CustomerOptionPriceImporterInterface;
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
    private $importErrorHandler;

    public function __construct(
        CustomerOptionPriceImporterInterface $priceImporter,
        string $csvExampleFilePath,
        TranslatorInterface $translator,
        ImportErrorHandlerInterface $importErrorHandler
    ) {
        $this->priceImporter   = $priceImporter;
        $this->exampleFilePath = $csvExampleFilePath;
        $this->translator      = $translator;
        $this->importErrorHandler = $importErrorHandler;
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

            $this->handleImportResult($importResult, 'csv');
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
                'customerOptionValues' => array_map(static function (CustomerOptionValueInterface $customerOptionValue) {
                    return $customerOptionValue->getCode();
                }, $valuePriceData['customerOptionValues']),
                'validFrom'            => $dateRange ? $dateRange->getStart() : null,
                'validTo'              => $dateRange ? $dateRange->getEnd() : null,
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

                $this->handleImportResult($importResult, 'product_list', $flashParameters, '_with_date', $extraData);
            } else {
                $this->handleImportResult($importResult, 'product_list', $flashParameters, '', $extraData);
            }
        }
    }

    /**
     * @param array $importResult
     * @param string $type
     * @param array $flashParameters
     * @param string $flashSuffix
     * @param array $extraData
     */
    protected function handleImportResult(
        array $importResult,
        string $type,
        array $flashParameters = [],
        string $flashSuffix = '',
        array $extraData = []
    ): void {
        // Handle errors
        $this->importErrorHandler->handleErrors($type, $importResult['failed'], $extraData);

        // Handle flash messages
        if (0 < $importResult['imported']) {
            $this->addFlash('success', $this->translator->trans(
                'brille24.flashes.customer_option_prices_imported.'.$type.$flashSuffix,
                array_merge($flashParameters, ['%count%' => $importResult['imported']])
            ));
        }
        if (0 < count($importResult['failed'])) {
            $this->addFlash('error', $this->translator->trans(
                'brille24.flashes.customer_option_prices_import_failed.'.$type.$flashSuffix,
                array_merge($flashParameters, ['%count%' => $importResult['failed']])
            ));
        }
    }
}

<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Controller;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport\PriceImportType;
use Brille24\SyliusCustomerOptionsPlugin\Importer\CustomerOptionPriceByExampleImporterInterface;
use Brille24\SyliusCustomerOptionsPlugin\Importer\CustomerOptionPriceCsvImporterInterface;
use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        $csvForm = $this->createFormBuilder()
            ->add('file', FileType::class, [
                'label' => 'sylius.ui.choose_file',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'brille24.form.customer_options.import',
                'attr'  => [
                    'class' => 'ui primary button',
                ],
            ])
            ->getForm()
        ;

        $csvForm->handleRequest($request);

        $byProductListForm = $this->createForm(PriceImportType::class);
        $byProductListForm->handleRequest($request);

        $importResult = ['imported' => 0, 'failed' => 0];
        if ($csvForm->isSubmitted() && $csvForm->isValid()) {
            /** @var UploadedFile $file */
            $file = $csvForm->get('file')->getData();

            /** @var string $path */
            $path = $file->getRealPath();

            try {
                $importResult = $this->csvPriceImporter->import($path);
            } catch (\Throwable $exception) {
                $this->addFlash('error', 'brille24.flashes.customer_option_price_import_error');
            }
        }

        if ($byProductListForm->isSubmitted() && $byProductListForm->isValid()) {
            $products = $byProductListForm->get('products')->getData();

            /** @var CustomerOptionValuePriceInterface $customerOptionValuePrice */
            $customerOptionValuePrice = $byProductListForm->get('customer_option_value_price')->getData();

            $importResult = $this->priceByExampleImporter->importForProducts($products, $customerOptionValuePrice);
        }

        if (0 < $importResult['imported']) {
            $this->addFlash('success', $this->translator->trans(
                'brille24.flashes.customer_option_prices_imported',
                ['%count%' => $importResult['imported']]
            ));
        }
        if (0 < $importResult['failed']) {
            $this->addFlash('error', $this->translator->trans(
                'brille24.flashes.customer_option_prices_import_failed',
                ['%count%' => $importResult['failed']]
            ));
        }

        return $this->render('@Brille24SyliusCustomerOptionsPlugin/PriceImport/import.html.twig', ['csvForm' => $csvForm->createView(), 'byProductListForm' => $byProductListForm->createView()]);
    }

    /**
     * @return Response
     */
    public function downloadExampleFileAction(): Response
    {
        return $this->file($this->exampleFilePath);
    }
}

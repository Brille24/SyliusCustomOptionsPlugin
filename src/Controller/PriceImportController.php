<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Controller;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport\PriceImportType;
use Brille24\SyliusCustomerOptionsPlugin\Importer\CustomerOptionPricesImporterInterface;
use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PriceImportController extends AbstractController
{
    /** @var CustomerOptionPricesImporterInterface */
    protected $pricesImporter;

    /** @var string */
    protected $exampleFilePath;
    /** @var CustomerOptionPriceUpdaterInterface */
    protected $priceUpdater;
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(
        CustomerOptionPricesImporterInterface $pricesImporter,
        string $exampleFilePath,
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager
    ) {
        $this->pricesImporter  = $pricesImporter;
        $this->exampleFilePath = $exampleFilePath;
        $this->priceUpdater = $priceUpdater;
        $this->entityManager = $entityManager;
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

        if ($csvForm->isSubmitted() && $csvForm->isValid()) {
            /** @var UploadedFile $file */
            $file = $csvForm->get('file')->getData();

            /** @var string $path */
            $path = $file->getRealPath();

            try {
                $result = $this->pricesImporter->importCustomerOptionPrices($path);

                if (0 < $result['imported']) {
                    $this->addFlash('success', sprintf('Imported %s prices', $result['imported']));
                }
                if (0 < $result['failed']) {
                    $this->addFlash('error', sprintf('Failed to import %s prices', $result['failed']));
                }

                return $this->redirectToRoute('brille24_admin_customer_option_index');
            } catch (\Throwable $exception) {
                $this->addFlash('error', 'Could not update customer option prices');
            }
        }

        if ($byProductListForm->isSubmitted() && $byProductListForm->isValid()) {
            $products = $byProductListForm->get('products')->getData();

            /** @var CustomerOptionValuePriceInterface $customerOptionValuePrice */
            $customerOptionValuePrice = $byProductListForm->get('customer_option_value_price')->getData();

            $i = 0;
            foreach ($products as $product) {

                $dateFrom = null;
                $dateTo = null;
                if ($customerOptionValuePrice->getDateValid() !== null) {
                    $dateFrom = $customerOptionValuePrice->getDateValid()->getStart()->format(DATE_ATOM);
                    $dateTo = $customerOptionValuePrice->getDateValid()->getEnd()->format(DATE_ATOM);
                }

                $price = $this->priceUpdater->updateForProduct(
                    $customerOptionValuePrice->getCustomerOptionValue()->getCustomerOption()->getCode(),
                    $customerOptionValuePrice->getCustomerOptionValue()->getCode(),
                    $customerOptionValuePrice->getChannel()->getCode(),
                    $product,
                    $dateFrom,
                    $dateTo,
                    $customerOptionValuePrice->getType(),
                    $customerOptionValuePrice->getAmount(),
                    $customerOptionValuePrice->getPercent()
                );

                $this->entityManager->persist($price);

                if (++$i % 10 === 0) {
                    $this->entityManager->flush();
                }
            }

            $this->entityManager->flush();
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

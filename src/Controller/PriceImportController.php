<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Controller;

use Brille24\SyliusCustomerOptionsPlugin\Importer\CustomerOptionPricesImporterInterface;
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

    public function __construct(
        CustomerOptionPricesImporterInterface $pricesImporter,
        string $exampleFilePath
    ) {
        $this->pricesImporter  = $pricesImporter;
        $this->exampleFilePath = $exampleFilePath;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, [
                'label' => 'sylius.ui.choose_file',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'brille24.form.customer_options.import',
                'attr' => [
                    'class' => 'ui primary button',
                ],
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
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

        return $this->render('@Brille24SyliusCustomerOptionsPlugin/PriceImport/import.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @return Response
     */
    public function downloadExampleFileAction(): Response
    {
        return $this->file($this->exampleFilePath);
    }
}

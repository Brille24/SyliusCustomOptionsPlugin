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

    public function __construct(CustomerOptionPricesImporterInterface $pricesImporter) {
        $this->pricesImporter = $pricesImporter;
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, [
                'label' => 'sylius.ui.choose_file',
                'attr' => [
                    'hidden' => 'hidden',
                ],
                'label_attr' => [
                    'class' => 'ui button',
                ],
            ])
            ->add('submit', SubmitType::class, [
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
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('@Brille24SyliusCustomerOptionsPlugin/PriceImport/_form.html.twig', ['form' => $form->createView()]);
    }
}

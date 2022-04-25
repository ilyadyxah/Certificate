<?php

namespace App\Controller\Admin;

use App\Form\ImportFromXlsFormType;
use App\Service\FileUploader;
use App\Service\ImportFromXlsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImportFromXlsController extends AbstractController
{
    private FileUploader $fileUploader;
    private ImportFromXlsService $importFromXls;

    public function __construct(
        FileUploader $fileUploader,
        ImportFromXlsService $importFromXls
    )
    {
        $this->fileUploader = $fileUploader;
        $this->importFromXls = $importFromXls;
    }

    /**
     * @Route("/admin/certificates/import", name="app_admin_certificates_import")
     */

    public function index(
        Request $request
    )
    {
        $form = $this->createForm(ImportFromXlsFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $file */
            $file = $form->get('file')->getData();

            $fileName = $this->fileUploader->uploadFile($file);

            if ($file->guessExtension() == 'xlsx') {
                $this->importFromXls->import($fileName);
            }
            return $this->redirectToRoute('app_admin_certificates');
        }
        return $this->render('admin/certificate/import.twig', [
            'importForm' => $form->createView(),
            'showError' => $form->isSubmitted()
        ]);
    }
}
<?php

namespace App\Controller\Admin;

use App\Entity\Certificate;
use App\Form\CertificateFormType;
use App\Repository\CertificateRepository;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CertificateController extends AbstractController
{
    /**
     * @Route("/admin/certificates", name="app_admin_certificates")
     */
    public function index(Request $request, CertificateRepository $certificateRepository, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $certificateRepository->findAllWithSearchQuery($request->query->get('title')),
            $request->query->getInt('page', 1),
            $request->query->get('itemOnPage') ?? 10
        );

        return $this->render('admin/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/certificates/create", name="app_admin_certificates_create")
     */
    public function create(ManagerRegistry $doctrine, Request $request, FileUploader $fileUploader)
    {
        $form = $this->createForm(CertificateFormType::class, new Certificate());

        if ($this->handleFormRequest($form, $doctrine, $request, $fileUploader)) {

            return $this->redirectToRoute('app_admin_certificates');
        }

        return $this->render('admin/certificate/create.html.twig', [
            'certificateForm' => $form->createView(),
            'showError' => $form->isSubmitted(),
        ]);
    }

    /**
     * @Route("/admin/certificates/{id}/edit", name="app_admin_certificates_edit")
     */
    public function edit(Certificate $certificate, ManagerRegistry $doctrine, Request $request, FileUploader $fileUploader)
    {
        $form = $this->createForm(CertificateFormType::class, $certificate);

        if ($certificate = $this->handleFormRequest($form, $doctrine, $request, $fileUploader)) {

            return $this->redirectToRoute('app_admin_certificates', [
                'id' => $certificate->getId(),
            ]);
        }

        return $this->render('admin/certificate/edit.html.twig', [
            'certificateForm' => $form->createView(),
            'showError' => $form->isSubmitted(),
        ]);
    }

    public function handleFormRequest(FormInterface $form, ManagerRegistry $doctrine, Request $request, FileUploader $fileUploader): ?Certificate
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Certificate $certificate */
            $certificate = $form->getData();

            /** @var UploadedFile|null $file */
            $file = $form->get('file')->getData();

            if ($file) {
                $certificate->setFilename($fileUploader->uploadFile($file, $certificate->getFilename()));
            }

            $em = $doctrine->getManager();
            $em->persist($certificate);
            $em->flush();

            return $certificate;
        }

        return null;
    }
}
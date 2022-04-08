<?php

namespace App\Controller\Admin;

use App\Entity\Certificate;
use App\Form\CertificateFormType;
use App\Repository\CertificateRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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
            $certificateRepository->findAllWithSearchQuery($request->query->get('name')),
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
    public function create(ManagerRegistry $doctrine, Request $request)
    {
        $form = $this->createForm(CertificateFormType::class, new Certificate());

        if ($this->handleFormRequest($form, $doctrine, $request)) {

            $this->addFlash('flash_message', 'Статья создана');
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
    public function edit(Certificate $certificate, ManagerRegistry $doctrine, Request $request)
    {
        $form = $this->createForm(CertificateFormType::class, $certificate);

        if ($certificate = $this->handleFormRequest($form, $doctrine, $request)) {

            $this->addFlash('flash_message', 'Шаблон изменён');
            return $this->redirectToRoute('app_admin_certificate_edit', [
                'id' => $certificate->getId(),
            ]);
        }

        return $this->render('admin/certificate/edit.html.twig', [
            'certificateForm' => $form->createView(),
            'showError' => $form->isSubmitted(),
        ]);
    }

    public function handleFormRequest(FormInterface $form, ManagerRegistry $doctrine, Request $request): ?Certificate
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Certificate $certificate */
            $certificate = $form->getData();

            $em = $doctrine->getManager();
            $em->persist($certificate);
            $em->flush();

            return $certificate;
        }

        return null;
    }
}
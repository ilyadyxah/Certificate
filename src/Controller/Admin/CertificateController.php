<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CertificateController
{
    /**
     * @Route("/admin/certificates", name="app_admin_certificates")
     */
    public function index()
    {
        return new Response('dsad');
    }
}
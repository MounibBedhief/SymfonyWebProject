<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/prescriptions')]
class PrescriptionController extends AbstractController
{
    #[Route('/', name: 'prescriptions_list')]
    public function list(): Response
    {
        return $this->render('pages/prescriptions_doctor.html.twig');
    }
}

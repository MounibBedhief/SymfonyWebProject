<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/doctors')]
class DoctorController extends AbstractController
{
    #[Route('/', name: 'doctors_search')]
    public function search(): Response
    {
        return $this->render('pages/rechercher_docteurs.html.twig');
    }

    #[Route('/{id}/profile', name: 'doctors_profile')]
    public function profile(int $id): Response
    {
        return $this->render('pages/doctor-profile.html.twig', [
            'doctorId' => $id
        ]);
    }

    #[Route('/{id}/calendar', name: 'doctors_calendar')]
    public function calendar(int $id): Response
    {
        return $this->render('pages/doctor_calendar.html.twig', [
            'doctorId' => $id
        ]);
    }

    #[Route('/{id}/view-profile', name: 'doctors_view_profile')]
    public function viewProfile(int $id): Response
    {
        return $this->render('pages/view-profile.html.twig', [
            'doctorId' => $id
        ]);
    }
}

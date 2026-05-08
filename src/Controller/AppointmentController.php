<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/appointments')]
class AppointmentController extends AbstractController
{
    #[Route('/', name: 'appointments_list')]
    public function list(): Response
    {
        return $this->render('pages/patient.html.twig');
    }

    #[Route('/book', name: 'appointments_book')]
    public function book(): Response
    {
        return $this->render('pages/book.html.twig');
    }

    #[Route('/reschedule/{id}', name: 'appointments_reschedule')]
    public function reschedule(int $id): Response
    {
        return $this->render('pages/reschedule_appointment.html.twig', [
            'appointmentId' => $id
        ]);
    }

    #[Route('/update-status/{id}', name: 'appointments_update_status')]
    public function updateStatus(int $id): Response
    {
        return $this->render('pages/update_appointment_status.html.twig', [
            'appointmentId' => $id
        ]);
    }
}

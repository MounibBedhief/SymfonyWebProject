<?php

namespace App\Controller;

use App\Repository\AppointmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PatientRepository;

final class PrescriptionsPatientsController extends AbstractController
{
    #[Route('/patient/prescriptions', name: 'app_prescriptions_patients')]
    #[IsGranted('ROLE_PATIENT')]
    public function history(AppointmentRepository $repository,PatientRepository $patient): Response
    {

        $testpatient=$this->getUser();
        $appointments = $repository->findAppointmentsForPatient($this->getUser()->getId());
        return $this->render('prescriptions_patients/patient.html.twig', [
            'appointments' => $appointments,
            'patient' => $testpatient,
        ]);
    }
}

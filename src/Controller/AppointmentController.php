<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Repository\AppointmentRepository;
use App\Repository\DoctorRepository;
use App\Repository\PatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppointmentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private AppointmentRepository $appointmentRepo,
        private DoctorRepository $doctorRepo,
        private PatientRepository $patientRepo,
    ) {}

    /**
     * Patient booking form — GET shows the form, POST creates the appointment.
     * TODO: Replace hardcoded patient ID with $this->getUser() once Security bundle is done.
     */
    #[Route('/patient/book', name: 'appointments_book', methods: ['GET', 'POST'])]
    #[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_PATIENT')]
    public function book(Request $request): Response
    {
        $doctors = $this->doctorRepo->findAll();

        if ($request->isMethod('POST')) {
            $doctorId = $request->request->getInt('doctor_id');
            $dateStr  = $request->request->get('appointment_date');
            $timeStr  = $request->request->get('appointment_time');
            $reason   = $request->request->get('reason', '');
            $notes    = $request->request->get('notes', '');

            // Validate required fields
            if (!$doctorId || !$dateStr || !$timeStr) {
                $this->addFlash('warning', 'Please fill in all required fields.');
                return $this->redirectToRoute('appointments_book');
            }

            $doctor  = $this->doctorRepo->find($doctorId);

            // TODO: Hardcoded patient ID = 1 until Security bundle is ready
            $patient = $this->getUser();

            if (!$doctor || !$patient) {
                $this->addFlash('danger', 'Doctor or patient not found.');
                return $this->redirectToRoute('appointments_book');
            }

            try {
                $appointment = new Appointment();
                $appointment->setPatient($patient);
                $appointment->setDoctor($doctor);
                $appointment->setAppointmentDate(new \DateTimeImmutable($dateStr));
                $appointment->setAppointmentTime(new \DateTimeImmutable($timeStr));
                $appointment->setStatus('Pending');
                $appointment->setReason($reason ?: 'General Consultation');
                $appointment->setNotes($notes ?: null);

                $this->em->persist($appointment);
                $this->em->flush();

                $this->addFlash('success', 'Your appointment has been booked successfully!');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Failed to book the appointment. Please try again.');
            }

            return $this->redirectToRoute('appointments_book');
        }

        return $this->render('pages/book.html.twig', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Doctor's appointment dashboard — lists all appointments for the logged-in doctor.
     * TODO: Replace hardcoded doctor ID with $this->getUser() once Security bundle is done.
     * JSON endpoint — Update appointment status (Accept / Decline / Complete).
     */
    #[Route('/doctor/appointments/update-status/{id}', name: 'appointments_update_status', methods: ['POST'])]
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $newStatus = $data['status'] ?? null;

        $validStatuses = ['Scheduled', 'Completed', 'Cancelled', 'No-Show', 'Pending'];

        if (!$newStatus || !in_array($newStatus, $validStatuses)) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid status'], 400);
        }

        $appointment = $this->appointmentRepo->findOneBy([]);
        if (!$appointment) {
            return new JsonResponse(['success' => false, 'message' => 'Appointment not found'], 404);
        }

        $appointment->setStatus($newStatus);
        $this->em->flush();

        return new JsonResponse(['success' => true, 'message' => 'Status updated successfully']);
    }

    /**
     * JSON endpoint — Reschedule an appointment (update date + time, reset status to Pending).
     */
    #[Route('/doctor/appointments/reschedule/{id}', name: 'appointments_reschedule', methods: ['POST'])]
    public function reschedule(int $id, Request $request): JsonResponse
    {
        $data    = json_decode($request->getContent(), true);
        $newDate = $data['new_date'] ?? null;
        $newTime = $data['new_time'] ?? null;

        if (!$newDate || !$newTime) {
            return new JsonResponse(['success' => false, 'message' => 'Missing required fields'], 400);
        }

        $appointment = $this->appointmentRepo->find($id);
        if (!$appointment) {
            return new JsonResponse(['success' => false, 'message' => 'Appointment not found'], 404);
        }

        try {
            $appointment->setAppointmentDate(new \DateTimeImmutable($newDate));
            $appointment->setAppointmentTime(new \DateTimeImmutable($newTime));
            $appointment->setStatus('Pending');
            $this->em->flush();

            return new JsonResponse(['success' => true, 'message' => 'Appointment rescheduled successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid date/time format'], 400);
        }
    }
    #[Route('/doctor/appointments', name: 'appointments_list', methods: ['GET'])]
    public function list(): Response
    {
        // TODO: Hardcoded doctor ID = 1 until Security bundle is ready
        $doctor = $this->doctorRepo->findOneBy([]);

        if (!$doctor) {
            throw $this->createNotFoundException('Doctor not found.');
        }

        $appointments = $this->appointmentRepo->getAppointmentsForDoctor($doctor);

        // Calculate quick stats
        $stats = ['total' => count($appointments), 'pending' => 0, 'scheduled' => 0, 'completed' => 0];
        foreach ($appointments as $appt) {
            $status = strtolower($appt->getStatus());
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }

        // Serialize appointments to JSON for the client-side detail views
        $appointmentsJson = [];
        foreach ($appointments as $appt) {
            $appointmentsJson[] = [
                'id'               => $appt->getId(),
                'patient_name'     => $appt->getPatient()->getName(),
                'patient_email'    => $appt->getPatient()->getEmail(),
                'appointment_date' => $appt->getAppointmentDate()->format('Y-m-d'),
                'appointment_time' => $appt->getAppointmentTime()->format('H:i:s'),
                'status'           => $appt->getStatus(),
                'reason'           => $appt->getReason(),
                'notes'            => $appt->getNotes(),
            ];
        }

        return $this->render('pages/appointments.html.twig', [
            'doctor'           => $doctor,
            'appointments'     => $appointments,
            'stats'            => $stats,
            'appointmentsJson' => json_encode($appointmentsJson),
        ]);
    }

    /**
     * JSON endpoint — Update appointment status (Accept / Decline / Complete).
     */
}

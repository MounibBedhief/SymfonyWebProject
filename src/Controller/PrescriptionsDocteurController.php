<?php

namespace App\Controller;

use App\Repository\AppointmentRepository;
use App\Repository\DoctorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PrescriptionsDocteurController extends AbstractController
{
    #[Route('/doctor/prescriptions', name: 'app_doctor_prescriptions', methods: ['GET', 'POST'])]
    public function prescriptions(
        Request $request,
        AppointmentRepository $appointmentRepo,
        DoctorRepository $doctorRepo, // Utilisé pour simuler le login
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {

        // --- MODE TEST : On simule un docteur connecté (ID 1 par exemple) ---
        // TODO: Une fois le login fini, utilise $doctor = $this->getUser();
        $doctor = $doctorRepo->findOneBy([]) ?? $doctorRepo->findAll()[0];

        if (!$doctor) {
            return new Response("Aucun docteur trouvé dans la base. Vérifie tes fixtures !");
        }

        // --- GESTION DE L'UPLOAD DU PDF ---
        if ($request->isMethod('POST') && $request->files->get('prescription_pdf')) {
            $appointmentId = $request->request->get('appointment_id');
            $pdfFile = $request->files->get('prescription_pdf');

            $appointment = $appointmentRepo->find($appointmentId);

            // Vérification de sécurité : l'appointment doit appartenir à ce docteur
            if ($appointment && $appointment->getDoctor() === $doctor) {

                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                // On nettoie le nom du fichier pour éviter les espaces et accents
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

                try {
                    // On déplace le fichier dans le dossier public/uploads/prescriptions
                    $pdfFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/prescriptions',
                        $newFilename
                    );

                    // Mise à jour de l'entité via Doctrine
                    $appointment->setPrescriptionPath($newFilename);
                    $appointment->setStatus('Completed'); // On passe le statut en complété automatiquement
                    $entityManager->flush();

                    $this->addFlash('success', 'La prescription a été envoyée avec succès.');

                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de la sauvegarde du fichier : ' . $e->getMessage());
                }
            } else {
                $this->addFlash('danger', 'Action non autorisée ou rendez-vous introuvable.');
            }

            return $this->redirectToRoute('app_doctor_prescriptions');
        }

        // --- RÉCUPÉRATION DES DONNÉES ---
        // On utilise ta propre méthode définie dans le repository
        $appointments = $appointmentRepo->getAppointmentsForDoctor($doctor);

        return $this->render('prescriptions_docteur/prescriptions_doctor.html.twig', [
            'appointments' => $appointments,
            'doctor'       => $doctor,
        ]);
    }
}

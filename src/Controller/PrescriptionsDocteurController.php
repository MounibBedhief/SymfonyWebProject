<?php

namespace App\Controller;

use App\Form\PrescriptionUploadType;
use App\Repository\AppointmentRepository;
use App\Repository\DoctorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PrescriptionsDocteurController extends AbstractController
{
    #[Route('doctor/prescriptions/', name: 'app_doctor_prescriptions', methods: ['GET', 'POST'])]
    public function prescriptions(
        Request $request,
        AppointmentRepository $appointmentRepo,
        DoctorRepository $doctorRepo,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {

        // --- MODE TEST : On récupère un docteur pour l'affichage initial ---
        $doctor = $this->getUser();

        if (!$doctor) {
            return new Response("Aucun docteur trouvé en BDD. Ajoute s'en un pour les tests !");
        }

        // --- INITIATION DU FORMULAIRE SYMFONY ---
        $form = $this->createForm(PrescriptionUploadType::class);
        $form->handleRequest($request);

        // Vérification de la soumission et du jeton CSRF de sécurité automatiquement injecté
        if ($form->isSubmitted() && $form->isValid()) {

            $appointmentId = $request->request->get('appointment_id');
            $appointment = $appointmentRepo->find($appointmentId);

            // MODE TEST : On valide juste si le rendez-vous existe
            if ($appointment) {

                // Extraction manuelle nécessaire car l'input HTML est dupliqué dans une boucle Twig
                $uploadedFiles = $request->files->all();
                $formName = $form->getName();

                /** @var UploadedFile|null $pdfFile */
                $pdfFile = $uploadedFiles[$formName]['prescription_pdf'] ?? null;

                if ($pdfFile) {
                    $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

                    try {
                        $pdfFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads/prescriptions',
                            $newFilename
                        );

                        // Mise à jour de la ligne en BDD
                        $appointment->setPrescriptionPath($newFilename);
                        $appointment->setStatus('Completed');
                        $entityManager->flush();

                        $this->addFlash('success', 'La prescription a été enregistrée avec succès (Mode Test).');

                    } catch (FileException $e) {
                        $this->addFlash('danger', 'Erreur d\'écriture sur le disque : ' . $e->getMessage());
                    }
                } else {
                    $this->addFlash('danger', 'Aucun fichier reçu.');
                }
            } else {
                $this->addFlash('danger', 'Rendez-vous introuvable avec l\'ID ' . $appointmentId);
            }

            return $this->redirectToRoute('app_doctor_prescriptions');
        }

        // Récupération des données pour alimenter ton tableau
        $appointments = $appointmentRepo->getAppointmentsForDoctor($doctor);

        return $this->render('prescriptions_docteur/prescriptions_doctor.html.twig', [
            'appointments' => $appointments,
            'doctor'       => $doctor,
            'form'         => $form->createView(),
        ]);
    }
}

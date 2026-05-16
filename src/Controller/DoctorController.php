<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Repository\DoctorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/doctors')]
class DoctorController extends AbstractController
{
    #[Route('/', name: 'doctors_search')]
    public function search(Request $request, DoctorRepository $repo): Response
    {
        $name = trim($request->query->get('name', ''));
        $location = trim($request->query->get('location', ''));
        $specialization = trim($request->query->get('specialization', ''));

        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 12;

        $qb = $repo->searchQuery($name, $location, $specialization);
        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        $paginator = new Paginator($qb, true);
        $total = count($paginator);
        $pages = (int) ceil($total / $limit);

        $doctors = iterator_to_array($paginator);

        $all = $repo->findAll();
        $specializations = array_values(array_unique(array_filter(array_map(fn($d) => $d->getSpecialization(), $all))));
        $locations = array_values(array_unique(array_filter(array_map(fn($d) => $d->getOfficePlace(), $all))));
        sort($specializations);
        sort($locations);

        return $this->render('pages/rechercher_docteurs.html.twig',
            compact('doctors', 'name', 'location', 'specialization', 'specializations', 'locations', 'page', 'pages', 'total')
        );
    }

    #[Route('/{id}/profile', name: 'doctors_profile')]
    public function profile(int $id, DoctorRepository $repo, EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {
        $doctor = $repo->find($id);
        
        if (!$doctor) {
            throw $this->createNotFoundException('Doctor not found');
        }

        $successMsg = '';
        $errorMsg = '';

        // Handle form submission
        if ($request->isMethod('POST')) {
            $name = trim($request->request->get('name', ''));
            $specialization = trim($request->request->get('specialization', ''));
            $experience = trim($request->request->get('experience', ''));
            $consultationFee = trim($request->request->get('consultation_fee', ''));
            $hospital = trim($request->request->get('hospital', ''));
            $phone = trim($request->request->get('phone', ''));
            $email = trim($request->request->get('email', ''));
            $about = trim($request->request->get('about', ''));
            $officePlace = trim($request->request->get('office_place', ''));

            // Handle image upload
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $request->files->get('profile_image');
            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = 'doctor_' . $id . '.' . $uploadedFile->guessExtension();

                try {
                    $uploadedFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $doctor->setImagePath('uploads/' . $newFilename);
                } catch (\Exception $e) {
                    $errorMsg = 'Failed to upload image.';
                }
            }

            // Update doctor properties
            $doctor->setName($name);
            $doctor->setSpecialization($specialization);
            $doctor->setExperience($experience);
            $doctor->setConsultationFee($consultationFee);
            $doctor->setHospital($hospital);
            $doctor->setPhone($phone);
            $doctor->setEmail($email);
            $doctor->setAbout($about);
            $doctor->setOfficePlace($officePlace);

            try {
                $em->flush();
                $successMsg = 'Profile updated successfully!';
            } catch (\Exception $e) {
                $errorMsg = 'Failed to update profile: ' . $e->getMessage();
            }
        }

        return $this->render('pages/doctor-profile.html.twig', [
            'doctor' => $doctor,
            'successMsg' => $successMsg,
            'errorMsg' => $errorMsg,
        ]);
    }

    #[Route('/{id}/calendar', name: 'doctors_calendar')]
    public function calendar(int $id, DoctorRepository $repo, Request $request): Response
    {
        $doctor = $repo->find($id);
        
        if (!$doctor) {
            throw $this->createNotFoundException('Doctor not found');
        }

        // Fetch appointments for this doctor
        $appointments = $doctor->getAppointments();
        
        // Format appointments for JS calendar
        $dbEvents = [];
        foreach ($appointments as $appointment) {
            if ($appointment->getStatus() === 'Cancelled') {
                continue;
            }

            $startTime = $appointment->getAppointmentTime()->format('H:i');
            
            // Calculate end time (1 hour after start)
            $endDateTime = clone $appointment->getAppointmentTime();
            $endDateTime = $endDateTime->modify('+1 hour');
            $endTime = $endDateTime->format('H:i');
            
            $jsStatus = ($appointment->getStatus() === 'Completed') ? 'completed' : 'planned';
            
            $dbEvents[] = [
                'id' => 'db_' . $appointment->getId(),
                'title' => $appointment->getReason() ?: 'Consultation',
                'type' => 'consultation',
                'status' => $jsStatus,
                'date' => $appointment->getAppointmentDate()->format('Y-m-d'),
                'start' => $startTime,
                'end' => $endTime,
                'patient' => $appointment->getPatient()->getName(),
                'notes' => $appointment->getNotes() ?? ''
            ];
        }

        return $this->render('pages/doctor_calendar.html.twig', [
            'doctor' => $doctor,
            'dbEvents' => json_encode($dbEvents),
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
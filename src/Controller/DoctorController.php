<?php

namespace App\Controller;

use App\Repository\DoctorRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

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
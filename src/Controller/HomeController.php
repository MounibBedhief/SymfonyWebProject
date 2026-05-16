<?php

namespace App\Controller;

use App\Repository\DoctorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(DoctorRepository $repo): Response
    {
        $doctors = $repo->findBy([], ['name' => 'ASC'], 4); // show 4 on homepage
        return $this->render('pages/index.html.twig', [
            'doctors' => $doctors,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(Request $request): Response
    {
        $request->getSession()->invalidate();
        return $this->redirectToRoute('dashboard');
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(DoctorRepository $repo): Response
    {
        $doctors = $repo->findBy([], ['name' => 'ASC'], 3);
        return $this->render('pages/connected.html.twig', [
            'doctorsList' => $doctors,
        ]);
    }
}

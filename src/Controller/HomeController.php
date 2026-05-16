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
        // Si l'utilisateur est connecté, on le redirige directement vers le traitement du dashboard
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        $doctors = $repo->findBy([], ['name' => 'ASC'], 4); // show 4 on homepage
        return $this->render('pages/index.html.twig', [
            'doctors' => $doctors,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(Request $request): Response
    {
        $request->getSession()->invalidate();
        return $this->redirectToRoute('home');
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(DoctorRepository $repo): Response
    {
        $user = $this->getUser();

        // 1. Si aucun utilisateur n'est connecté, retour à la page d'accueil publique
        if (!$user) {
            return $this->redirectToRoute('home');
        }
        $doctors = $repo->findBy([], ['name' => 'ASC'], 4);

        // 2. Si c'est un Patient connecté
        if (in_array('ROLE_PATIENT', $user->getRoles())) {
            $doctors = $repo->findBy([], ['name' => 'ASC'], 3);
            return $this->render('pages/connected.html.twig', [
                'doctors' => $doctors,
                'patient'     => $user, // Optionnel : si tu as besoin d'afficher son nom dans connected.html.twig
            ]);
        }

        // 3. Si c'est un Docteur connecté
        if (in_array('ROLE_DOCTOR', $user->getRoles())) {
            return $this->redirectToRoute('appointments_list');
        }

        // Cas par défaut au cas où (ex: admin)
        return $this->redirectToRoute('home');
    }
}

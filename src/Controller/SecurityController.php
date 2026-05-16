<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Form\RegistrationFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // ✅ CORRECTION : On accepte explicitement GET et POST pour que le form_login fonctionne avec check_path: login
    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, on évite qu'il revienne sur le login
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // Récupération de l'erreur de connexion
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('pages/login-register.html.twig', [
            'last_username'    => $authenticationUtils->getLastUsername(),
            // ✅ CORRECTION : On envoie un simple booléen à Twig pour éviter les conflits d'objets avec le pare-feu
            'has_error'        => $error !== null,
            'registerForm'     => $this->createForm(RegistrationFormType::class)->createView(),
            'showRegisterForm' => false,
        ]);
    }

    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $data['role'] === 'doctor' ? new Doctor() : new Patient();

            $user->setName($data['full_name']);
            $user->setEmail($data['email']);
            $user->setPassword(
                $hasher->hashPassword($user, $form->get('plainPassword')->getData())
            );
            $user->setCreatedAt(new DateTimeImmutable());

            // Valeurs par défaut pour le docteur
            if ($user instanceof Doctor) {
                $user->setPhone('TBD');
                $user->setSpecialization('General');
                $user->setLicenseNumber('Lic-' . bin2hex(random_bytes(4)));
                $user->setRoles(["ROLE_DOCTOR"]);
                $user->setExperience(0);
                $user->setHospital('TBD');
                $user->setOfficePlace('TBD');
            } else {
                $user->setRoles(["ROLE_PATIENT"]);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Account created! You can now login.');
            return $this->redirectToRoute('login');
        }

        return $this->render('pages/login-register.html.twig', [
            'last_username'    => '',
            'has_error'        => false,
            'registerForm'     => $form->createView(),
            'showRegisterForm' => true,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): never
    {
        throw new \LogicException('Intercepted by firewall.');
    }
}

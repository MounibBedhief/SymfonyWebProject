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
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('pages/login-register.html.twig', [
            'last_username'    => $authenticationUtils->getLastUsername(),
            'error'            => $authenticationUtils->getLastAuthenticationError(),
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

            // Doctor-specific defaults
            if ($user instanceof Doctor) {
                $user->setPhone('TBD');
                $user->setSpecialization('General');
                $user->setLicenseNumber('Lic-' . bin2hex(random_bytes(4)));
                $user->setRoles(["ROLE_DOCTOR"]);
                $user->setExperience(0);
                $user->setHospital('TBD');
                $user->setOfficePlace('TBD');
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Account created! You can now login.');
            return $this->redirectToRoute('login');
        }

        return $this->render('pages/login-register.html.twig', [
            'last_username'    => '',
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

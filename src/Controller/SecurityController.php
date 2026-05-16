<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Connection;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function login(Request $request, Connection $db): Response
    {
        $error = null;
        $success = null;
        $showRegisterForm = $request->request->has('show_register');

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('auth_token', $request->request->get('csrf_token'))) {
                $error = "Invalid security token";
            } else {
                // ─── LOGIN ───────────────────────────────────────────────
                if ($request->request->has('login_submit')) {
                    $email    = $request->request->get('email');
                    $password = $request->request->get('password');

                    $doctor = $db->fetchAssociative(
                        "SELECT * FROM doctor WHERE email = ?",
                        [$email]
                    );

                    if ($doctor && password_verify($password, $doctor['password'])) {
                        $session = $request->getSession();
                        $session->set('role',      'doctor');
                        $session->set('user_id',   $doctor['id']);
                        $session->set('user_name', $doctor['name']);

                        // Redirect to doctor's own profile page
                        return $this->redirectToRoute('doctors_profile', [
                            'id' => $doctor['id']
                        ]);
                    }

                    // Patient check — you'll fill this in yourself
                    $patient = $db->fetchAssociative(
                        "SELECT * FROM patient WHERE email = ?",
                        [$email]
                    );

                    if ($patient && password_verify($password, $patient['password'])) {
                        $session = $request->getSession();
                        $session->set('role',      'patient');
                        $session->set('user_id',   $patient['id']);
                        $session->set('user_name', $patient['name']);

                        // TODO: replace with your real patient route
                        return $this->redirectToRoute('patient_dashboard');
                    }

                    $error = "Invalid email or password";
                }

                // ─── REGISTER ────────────────────────────────────────────
                if ($request->request->has('register_submit')) {
                    $fullName = $request->request->get('Rfull_name');
                    $email    = $request->request->get('Remail');
                    $pass     = $request->request->get('Rpassword');
                    $role     = $request->request->get('Rrole', 'patient');
                    $hashed   = password_hash($pass, PASSWORD_BCRYPT);

                    try {
                        if ($role === 'doctor') {
                            if ($role === 'doctor') {
                                $db->executeStatement(
                                    "INSERT INTO doctor
            (name, email, password, phone, specialization, license_number, roles, experience, hospital, office_place, created_at)
         VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                                    [
                                        $fullName,
                                        $email,
                                        $hashed,
                                        'TBD',
                                        'General',
                                        'LIC-' . bin2hex(random_bytes(4)),
                                        '["ROLE_DOCTOR"]',
                                        '0',
                                        'TBD',
                                        'TBD',
                                        (new \DateTime())->format('Y-m-d H:i:s'),
                                    ]
                                );
                            }
                        } else {
                            $db->executeStatement(
                                "INSERT INTO patient (name, email, password) VALUES (?, ?, ?)",
                                [$fullName, $email, $hashed]
                            );
                        }

                        $success = "Account created! You can now login.";
                        $showRegisterForm = false;
                    } catch (\Exception $e) {
                        $error = "Email already exists or database error.";
                    }
                }
            }
        }

        return $this->render('pages/login-register.html.twig', [
            'error'            => $error,
            'success'          => $success,
            'showRegisterForm' => $showRegisterForm,
        ]);
    }
}

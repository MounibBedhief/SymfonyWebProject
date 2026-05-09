<?php

namespace App\DataFixtures;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Appointment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $doctors = [];
        $patients = [];

        // 1. CREATING BRITISH DOCTORS
        $doctorData = [
            ['Dr. Alistair Cook', 'Cardiology', 'NHS-12345', 'Royal London Hospital'],
            ['Dr. Eleanor Rigby', 'Pediatrics', 'NHS-67890', 'St Thomas\' Hospital'],
            ['Dr. James Watson', 'Neurology', 'NHS-11223', 'Guy\'s Hospital'],
            ['Dr. Rose Tyler', 'Dermatology', 'NHS-44556', 'Chelsea and Westminster'],
            ['Dr. Arthur Shelby', 'General Practice', 'NHS-99887', 'Birmingham City Hospital']
        ];

        foreach ($doctorData as $index => $data) {
            $doctor = new Doctor();
            $doctor->setName($data[0]);
            $doctor->setEmail(strtolower(str_replace([' ', '.'], '', $data[0])) . '@nhs.uk');
            $doctor->setSpecialization($data[1]);
            $doctor->setLicenseNumber($data[2]);
            $doctor->setHospital($data[3]);
            $doctor->setExperience('1' . $index . ' years'); // 10, 11, 12 years...
            $doctor->setPhone('07700 900' . rand(100, 999));
            $doctor->setOfficePlace('Room ' . ($index + 101));
            $doctor->setCreatedAt(new \DateTimeImmutable());

            // Default password: password123
            $doctor->setPassword($this->hasher->hashPassword($doctor, 'password123'));
            $doctor->setRoles(['ROLE_DOCTOR']);

            $manager->persist($doctor);
            $doctors[] = $doctor;
        }

        // 2. CREATING BRITISH PATIENTS
        $patientNames = ['Oliver Twist', 'Jane Eyre', 'Winston Churchill', 'Diana Prince', 'Sherlock Holmes'];

        foreach ($patientNames as $name) {
            $patient = new Patient();
            $patient->setName($name);
            $patient->setEmail(strtolower(str_replace(' ', '.', $name)) . '@gmail.co.uk');
            $patient->setCreatedAt(new \DateTimeImmutable());

            // Default password: password123
            $patient->setPassword($this->hasher->hashPassword($patient, 'password123'));

            $manager->persist($patient);
            $patients[] = $patient;
        }

        // 3. CREATING COHERENT APPOINTMENTS
        $reasons = [
            'General health check-up',
            'Follow-up on blood test results',
            'Chronic back pain consultation',
            'Prescription renewal for hypertension',
            'Initial assessment for migraines',
            'Post-surgery review'
        ];

        $statuses = ['Confirmed', 'Pending', 'Completed'];

        for ($i = 0; $i < 12; $i++) {
            $appointment = new Appointment();

            // Logical dates in 2026
            $date = new \DateTimeImmutable('2026-06-' . rand(1, 28));
            $time = new \DateTimeImmutable(rand(9, 16) . ':00:00'); // Appointments between 9am and 4pm

            $appointment->setAppointmentDate($date);
            $appointment->setAppointmentTime($time);
            $appointment->setReason($reasons[array_rand($reasons)]);
            $appointment->setStatus($statuses[array_rand($statuses)]);
            $appointment->setNotes('Patient requested a morning slot if possible.');

            // Randomly link to existing doctors and patients
            $appointment->setDoctor($doctors[array_rand($doctors)]);
            $appointment->setPatient($patients[array_rand($patients)]);

            $manager->persist($appointment);
        }

        $manager->flush();
    }
}

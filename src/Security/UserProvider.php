<?php

namespace App\Security;

use App\Repository\DoctorRepository;
use App\Repository\PatientRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(
        private DoctorRepository $doctors,
        private PatientRepository $patients,
    ) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->doctors->findOneBy(['email' => $identifier])
            ?? $this->patients->findOneBy(['email' => $identifier])
            ?? throw new UserNotFoundException("No user found for email: $identifier");
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        // Si l'utilisateur en session est un Docteur, on va DIRECTEMENT dans la table doctor
        if ($user instanceof \App\Entity\Doctor) {
            return $this->doctors->find($user->getId())
                ?? throw new UserNotFoundException();
        }

        // Si c'est un Patient, on va DIRECTEMENT dans la table patient (sans passer par les docteurs !)
        if ($user instanceof \App\Entity\Patient) {
            return $this->patients->find($user->getId())
                ?? throw new UserNotFoundException();
        }

        throw new \Symfony\Component\Security\Core\Exception\UnsupportedUserException();
    }

    public function supportsClass(string $class): bool
    {
        return in_array($class, [\App\Entity\Doctor::class, \App\Entity\Patient::class]);
    }
}

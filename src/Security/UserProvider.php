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
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return in_array($class, [\App\Entity\Doctor::class, \App\Entity\Patient::class]);
    }
}

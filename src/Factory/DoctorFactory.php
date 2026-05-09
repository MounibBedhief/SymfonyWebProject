<?php

namespace App\Factory;

use App\Entity\Doctor;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Doctor>
 */
final class DoctorFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Doctor::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'created_at' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'email' => self::faker()->text(180),
            'experience' => self::faker()->text(50),
            'hospital' => self::faker()->text(255),
            'license_number' => self::faker()->text(50),
            'name' => self::faker()->text(255),
            'office_place' => self::faker()->text(255),
            'password' => self::faker()->text(),
            'phone' => self::faker()->text(15),
            'roles' => [],
            'specialization' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Doctor $doctor): void {})
        ;
    }
}

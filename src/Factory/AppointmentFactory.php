<?php

namespace App\Factory;

use App\Entity\Appointment;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Appointment>
 */
final class AppointmentFactory extends PersistentObjectFactory
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
        return Appointment::class;
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
            'appointment_date' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'appointment_time' => \DateTimeImmutable::createFromMutable(self::faker()->datetime()),
            'doctor' => DoctorFactory::new(),
            'patient' => PatientFactory::new(),
            'reason' => self::faker()->text(255),
            'status' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Appointment $appointment): void {})
        ;
    }
}

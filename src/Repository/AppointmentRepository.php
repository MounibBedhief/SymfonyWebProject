<?php

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    //    /**
    //     * @return Appointment[] Returns an array of Appointment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Appointment
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findAppointmentsForPatient($patient): array
    {
        return $this->createQueryBuilder('a')
            ->addSelect('d') // Optimisation : on joint le docteur pour éviter les requêtes N+1
            ->join('a.doctor', 'd')
            ->where('a.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('a.appointment_date', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function getAppointmentsForDoctor(User $doctor):array{
        return $this->createQueryBuilder('a')
            ->where('a.doctor = :doctor')
            ->setParameter('doctor', $doctor)
            ->orderBy('a.appointment_date', 'ASC')
            ->getQuery()->getResult();
    }


}

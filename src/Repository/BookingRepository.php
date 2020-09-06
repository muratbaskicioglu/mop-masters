<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function findByCleanerIdBetweenDateTime($cleaner, $date, $startTime, $endTime)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.cleaner = :cleaner')
            ->andWhere('b.date = :date')
            ->andWhere('(b.startTime BETWEEN :startTime AND :startTime) OR (b.endTime BETWEEN :startTime AND :endTime)')
            ->setParameter('cleaner', $cleaner)
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('startTime', $startTime->format('H:i:s'))
            ->setParameter('endTime', $endTime->format('H:i:s'))
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY)
        ;
    }

    public function create($cleaner, $date, $startTime, $endTime)
    {
        $newBooking = new Booking();
        $newBooking->setCleaner($cleaner);
        $newBooking->setDate($date);
        $newBooking->setStartTime($startTime);
        $newBooking->setEndTime($endTime);

        $this->getEntityManager()->persist($newBooking);
        $this->getEntityManager()->flush();

        return $newBooking;
    }

    public function update($booking, $date, $startTime, $endTime)
    {
        $booking->setDate($date);
        $booking->setStartTime($startTime);
        $booking->setEndTime($endTime);

        $this->getEntityManager()->flush();

        return $booking;
    }
    /*
    public function findOneBySomeField($value): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

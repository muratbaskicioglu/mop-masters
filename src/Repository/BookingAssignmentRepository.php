<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\BookingAssignment;
use App\Entity\Cleaner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookingAssignment|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookingAssignment|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookingAssignment[]    findAll()
 * @method BookingAssignment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingAssignment::class);
    }

    /**
     * Get bookings that related with assignments of a cleaner.
     *
     * @param Cleaner $cleaner
     * @return int|mixed|string|array
     */
    public function getBookingsOfCleanerAssignments(Cleaner $cleaner): ?array
    {
        return $this->createQueryBuilder('ba')
            ->select('b.startDate, b.endDate')
            ->innerJoin(Booking::class, 'b', Join::WITH, 'ba.booking = b.id')
            ->andWhere('ba.cleaner = :cleaner')
            ->setParameter('cleaner', $cleaner)
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\BookingAssignment;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    /**
     * Get there is any assigned cleaner between the dates.
     *
     * @param string $bookingId
     * @param array $cleanerIds
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @return int|mixed|string
     */
    public function getAssignedCleaners(
        string $bookingId,
        array $cleanerIds,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): ?array
    {
        $qb = $this->createQueryBuilder('b')
            ->innerJoin(BookingAssignment::class, 'ba', Join::WITH, 'ba.booking = b.id');

        if ($bookingId) {
            $qb->andWhere('b.id != :bookingId');
        }

        $qb->andWhere(':startDate <= b.endDate')
            ->andWhere(':endDate >= b.startDate')
            ->andWhere('ba.cleaner IN (:cleanerIds)');

        if ($bookingId) {
            $qb->setParameter('bookingId', $bookingId);
        }

        return $qb->setParameter('endDate', $endDate)
            ->setParameter('startDate', $startDate)
            ->setParameter('cleanerIds', $cleanerIds)
            ->getQuery()
            ->getResult();
    }

    /**
     * Return created booking.
     *
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @param array $cleaners
     * @return Booking
     */
    public function create(DateTimeInterface $startDate, DateTimeInterface $endDate, array $cleaners): Booking
    {
        $booking = new Booking();
        $booking->setStartDate($startDate);
        $booking->setEndDate($endDate);

        $this->getEntityManager()->persist($booking);

        for ($i = 0; $i < count($cleaners); $i++) {
            $bookingAssignment = new BookingAssignment();
            $bookingAssignment->setBooking($booking);
            $bookingAssignment->setCleaner($cleaners[$i]);

            $this->getEntityManager()->persist($bookingAssignment);
        }

        $this->getEntityManager()->flush();

        return $booking;
    }

    /**
     * Returns updated booking.
     *
     * @param Booking $booking
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @return Booking
     */
    public function update(
        Booking $booking,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): Booking
    {
        $booking->setStartDate($startDate);
        $booking->setEndDate($endDate);

        $this->getEntityManager()->flush();

        return $booking;
    }
}

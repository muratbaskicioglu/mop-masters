<?php

namespace App\Repository;

use App\Entity\Cleaner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cleaner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cleaner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cleaner[]    findAll()
 * @method Cleaner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CleanerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cleaner::class);
    }

    /**
     * @param array $cleanerIds
     * @return int|mixed|string
     */
    public function getCompanyCountsOfCleaners(array $cleanerIds): ?array
    {
        return $this->createQueryBuilder('cl')
            ->select('COUNT(DISTINCT cl.company) as distinct_count, COUNT(cl.company) as normal_count')
            ->andWhere('cl.id IN (:cleanerIds)')
            ->setParameter('cleanerIds', $cleanerIds)
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
    }
}

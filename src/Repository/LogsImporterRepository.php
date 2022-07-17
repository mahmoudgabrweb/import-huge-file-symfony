<?php

namespace App\Repository;

use App\Entity\LogsImporter;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogsImporter>
 *
 * @method LogsImporter|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogsImporter|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogsImporter[]    findAll()
 * @method LogsImporter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogsImporterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogsImporter::class);
    }

    public function add(LogsImporter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LogsImporter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function filterByFields(array $filters): array
    {
        $queryBuilder = $this->createQueryBuilder('logs');

        if ($filters['serviceName']) {
            $queryBuilder = $queryBuilder->where('logs.service_name LIKE :serviceName')
                ->setParameter('serviceName', '%' . $filters['serviceName'] . '%');
        }

        if ($filters['startDate']) {
            if (!strtotime($filters['startDate'])) {
                return [];
            }
            $queryBuilder = $queryBuilder->orWhere('logs.triggered_at >= :startDate')
                ->setParameter('startDate', $filters['startDate']);
        }

        if ($filters['endDate']) {
            if (!strtotime($filters['endDate'])) {
                return [];
            }
            $queryBuilder = $queryBuilder->orWhere('logs.triggered_at <= :endDate')
                ->setParameter('endDate', $filters['endDate']);
        }

        if ($filters['statusCode']) {
            $queryBuilder = $queryBuilder->andWhere('logs.status_code = :statusCode')
                ->setParameter('statusCode', $filters['statusCode']);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    //    /**
    //     * @return LogsImporter[] Returns an array of LogsImporter objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?LogsImporter
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

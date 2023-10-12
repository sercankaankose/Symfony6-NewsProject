<?php

namespace App\Repository;

use App\Entity\EditRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EditRequest>
 *
 * @method EditRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditRequest[]    findAll()
 * @method EditRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditRequest::class);
    }

//    /**
//     * @return EditRequest[] Returns an array of EditRequest objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EditRequest
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     *
     *
     * @return EditRequest[]
     */
    public function findLatestRequests(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.request_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
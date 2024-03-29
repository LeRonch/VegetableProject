<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Buyer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findAllYours($buyer):array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT 
                orderClass
            FROM 
                App\Entity\Order orderClass
            JOIN
                orderClass.buyer buyer
            WHERE 
                buyer.id = :buyer
            '
        )->setParameter("buyer", $buyer->getId());
        return $query->getResult();
    }

    // /**
    //  * Finds carts that have not been modified since the given date.
    //  *
    //  * @param \DateTime $limitDate
    //  * @param int $limit
    //  *
    //  * @return int|mixed|string
    //  */
    // public function findCartsNotModifiedSince(\DateTime $limitDate, int $limit = 10): array
    // {
    //     return $this->createQueryBuilder('o')
    //         ->andWhere('o.status = :status')
    //         ->andWhere('o.updatedAt < :date')
    //         ->setParameter('status', Order::STATUS_CART)
    //         ->setParameter('date', $limitDate)
    //         ->setMaxResults($limit)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
}
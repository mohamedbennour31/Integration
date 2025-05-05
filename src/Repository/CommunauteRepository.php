<?php

namespace App\Repository;

use App\Entity\Communaute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommunauteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Communaute::class);
    }

    public function save(Communaute $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Communaute $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.id_hackathon', 'h')
            ->innerJoin('App\Entity\Participation', 'p', 'WITH', 'p.id_hackathon = h')
            ->innerJoin('p.id_participant', 'u')
            ->where('u.id_user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
}
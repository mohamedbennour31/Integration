<?php

namespace App\Repository;

use App\Entity\Poll_votes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Poll_votesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poll_votes::class);
    }

    // Add custom methods as needed
}
<?php

namespace App\Repository;

use App\Entity\Poll_options;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Poll_optionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poll_options::class);
    }

    // Add custom methods as needed
}
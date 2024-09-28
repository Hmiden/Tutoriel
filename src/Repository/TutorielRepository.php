<?php
// src/Repository/TutorielRepository.php

namespace App\Repository;

use App\Entity\Tutoriel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TutorielRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tutoriel::class);
    }
    public function searchTutorials(?string $titre = '', ?string $format = '', $duree = '', ?string $langue = ''): array
    {
        $qb = $this->createQueryBuilder('t');
    
        if ($titre) {
            $qb->andWhere('t.titre LIKE :titre')
               ->setParameter('titre', '%' . $titre . '%');
        }
    
        if ($format) {
            $qb->andWhere('t.format = :format')
               ->setParameter('format', $format);
        }
    
        if ($duree !== '' && $duree !== null) {
            if ($duree <= 30) {
                $qb->andWhere('t.duree <= :duree')
                   ->setParameter('duree', $duree);
            } else {
                $qb->andWhere('t.duree > :duree')
                   ->setParameter('duree', 30);
            }
        }
    
        if ($langue) {
            $qb->andWhere('t.langue = :langue')
               ->setParameter('langue', $langue);
        }
    
        return $qb->getQuery()->getResult();
    }
    
    
    /**
     * Find the most viewed tutorials
     *
     * @param int $limit
     * @return Tutoriel[]
     */
    public function findMostViewed(int $limit = 10): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.views', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    public function findFavorisByUser($userId)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.favoris', 'f') // Assuming 'favoris' is a relation in your Tutoriel entity
            ->where('f.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
}

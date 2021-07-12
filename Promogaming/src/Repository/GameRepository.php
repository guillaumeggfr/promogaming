<?php

namespace App\Repository;
use App\Entity\Tag;
use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    // /**
    //  * @return Game[] Returns an array of Game objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
   
    /*
    public function findOneBySomeField($value): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
  /*
    * I want to retrieve all the games present in platform_game, whose tag_id corresponds to tag.id
    *
    */
    public function findGamesByTag(Tag $tag)
    {
        // I sepcify the alias for the entity
        $qb = $this->createQueryBuilder('g')
        // I join the game with tags
        ->leftJoin('g.tags','gt')
        // I add the condition for precise that i want tags in the result
        ->where('gt = :tag')
        // I precise the parameter
        ->setParameter('tag', $tag);
         // I take back the QueryBuilder and the result
        return $qb->getQuery()->getResult();
    }
}
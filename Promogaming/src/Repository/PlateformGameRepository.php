<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\PlateformGame;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlateformGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlateformGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlateformGame[]    findAll()
 * @method PlateformGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlateformGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlateformGame::class);
    }

    // /**
    //  * @return PlateformGame[] Returns an array of PlateformGame objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


  /*
    * I want to retrieve all the games present in platform_game, whose tag_id corresponds to tag.id
    *
    */
   
   public function getGamesByPlateform($id)
    {
        // I take back the QueryBuilder and i sepcify the alias for the entity
        return $this->createQueryBuilder('pg')
        // I precise that i want the plateform with the id $id
        ->Where('pg.plateform = :id')
        // I precise the parameter
        ->setParameter('id', $id)
        // I transform my object QuerryBuilder on object query
        ->getQuery()
        ;

    }

    public function getGamesByIdGame($id)
    {
        // I take back the QueryBuilder and i sepcify the alias for the entity
        return $this->createQueryBuilder('pg')
        // I precise that i want the plateform with the id $id
        ->Where('pg.game = :id')
        // I precise the parameter
        ->setParameter('id', $id)
        // I transform my object QuerryBuilder on object query
        ->getQuery()

        // On demande à cet objet objet Query d'exécuter la requete et nous fournir les résultats
            // Ici on accepte un seul ou aucun résultat
            // https://www.doctrine-project.org/projects/doctrine-orm/en/2.8/reference/dql-doctrine-query-language.html#query-result-formats
            ->getResult()
            ;
    }

    public function findByGameAndPlateform(Game $game, $plateform)
    {
        return $this->createQueryBuilder('pg')
        ->where('pg.game = '.$game->getId())
        ->andWhere('pg.plateform = '.$plateform)
        ->getQuery()
        ->getResult()
        ;
    }
    
    /*
    * I want to recover all the games whose reduction is greater than $ value
    *
    */
     
   public function getGamesByReduce($value)
   {
       // I take back the QueryBuilder and i sepcify the alias for the entity
       return $this->createQueryBuilder('pg')
       // I precise that i want the reduce with the value are better than $value
       ->Where('pg.reduce >= :value')
       // I precise the parameter
       ->setParameter('value', $value)
       // I transform my object QuerryBuilder on object query
       ->getQuery()
       // I return the result
       ->getResult();
   }
}
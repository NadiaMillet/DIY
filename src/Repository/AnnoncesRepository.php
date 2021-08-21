<?php

namespace App\Repository;

use App\Entity\Annonces;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonces|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonces|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonces[]    findAll()
 * @method Annonces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnoncesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonces::class);
    }

    /**
     * Gestion formulaire de recherche
     * @return void
     */
    public function search($mots = null, $categorie = null)
    {
        # $mots = mots de la recherche reçue

        # QueryBuilder fournit une API conçue pour la construction conditionnelle d'une requête DQL en plusieurs étapes. Il fournit un ensemble de classes et de méthodes capables de créer des requêtes par programmation, ainsi qu'une API fluide.
        # 'a' = annonces
        $query = $this->createQueryBuilder('a');

        # requête where, ici pour viser seulement les requête active (1 / true)
        $query->where('a.active = 1');


        if ($mots != null) {

            # andwhere = where supplémentaire
            # MATCH_AGAINST = appel de la méthode configurée dans doctrine.yaml > Indication des champs > Indicatio termes de recherche et si réponse à la requête (>0 / true)
            $query->andWhere('MATCH_AGAINST(a.title , a.content) AGAINST(:mots boolean)>0')
                ->setParameter('mots', $mots);
        }
        if ($categorie != null) {
            #  commande slq leftJoin est un type de jointure entre 2 tables, ici colonne categories_id de la table annonces et la table categories
            $query->leftJoin('a.categories', 'c');
            # vérifie si les id de la catégorie se correspondent
            $query->andWhere('c.id = :id')
                #setparameter protège des injections SQL        
                ->setParameter('id', $categorie);
        }

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Annonces[] Returns an array of Annonces objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annonces
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

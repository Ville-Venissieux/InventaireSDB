<?php

namespace Venissieux\InventaireSDB\FrontBundle\Repository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends \Doctrine\ORM\EntityRepository {

    public function search($data, $page = 0, $max = NULL, $sortColumn = null, $sortDirection = null, $getResult = true) {
        $qb = $this->_em->createQueryBuilder();
        $query = isset($data['query']) && $data['query'] ? $data['query'] : null;

        $qb
                ->select('a')
                ->from('VenissieuxInventaireSDBFrontBundle:Article', 'a');

        //On ajoute un tri si demandé
        if (isset($sortColumn) && isset($sortDirection)) {
            $qb->orderBy('a.' . $sortColumn, $sortDirection);
        }

        if ($query) {
            $qb
                    ->andWhere('UPPER(a.nom) like UPPER(:query)')
                    //->orWhere('UPPER(a.categorie.libelle) like UPPER(:query)')
                    ->orWhere('UPPER(a.commentaire) like UPPER(:query)')
                    ->setParameter('query', "%" . $query . "%")
            ;
            //Si la valeur recherchée est un entier, on ajoute une recherche sur l'id de l'article
            if (is_integer($query)) {
                $qb->orWhere('a.id = :queryInt')
                        ->setParameter('queryInt', (int) $query);
            }
        }

        if ($max) {
            $preparedQuery = $qb->getQuery()
                    ->setMaxResults($max)
                    ->setFirstResult($page * $max)
            ;
        } 
        else {
            $preparedQuery = $qb->getQuery();
        }

        return $getResult ? $preparedQuery->getResult() : $preparedQuery;
    }

}

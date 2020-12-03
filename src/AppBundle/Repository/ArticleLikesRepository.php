<?php

namespace AppBundle\Repository;

/**
 * ArticleLikesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleLikesRepository extends \Doctrine\ORM\EntityRepository
{
    public function getArticlesLikes($id)
    {
        $qb = $this->createQueryBuilder('a')
                 ->where('a.article = :article')
                 ->andWhere('a.user  IS NOT NULL')
                 ->setParameter('article', $id);
        return $qb->getQuery()->getResult();
    }
}

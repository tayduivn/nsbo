<?php

namespace AppBundle\Repository;

/**
 * SurveyResponseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SurveyResponseRepository extends \Doctrine\ORM\EntityRepository
{

    public function count($id)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('COUNT(c)');
        $qb->innerJoin('c.response', 'cc');
        $qb->innerJoin('cc.question', 'cq');
        $qb->innerJoin('c.user', 'cu');
        $qb->where('cq.id = :id')->setParameter('id', $id);
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function search($id, $page, $order)
    {
        $qb = $this->createQueryBuilder('c');
        if (is_array($order)) {
            foreach ($order as $orderName => $orderType) {
                $qb->orderBy($orderName, $orderType);
            }
        }

        $qb->innerJoin('c.response', 'cc');
        $qb->innerJoin('cc.question', 'cq');
        $qb->innerJoin('cq.survey', 'cs');
        $qb->innerJoin('c.user', 'cu');
        $qb->where('cq.id = :id')->setParameter('id', $id);

        $qb->setMaxResults(25)
            ->setFirstResult($page * 25);

        return $qb->getQuery()->getResult();
    }

    public function getResponseSurvey($user,$response)
    {
        $qb = $this->createQueryBuilder('s')
            ->join('s.response','sr')
            ->select('sr.id')
            ->andWhere('s.user = :user')->setParameter('user', $user)
            ->andWhere('s.response = :response')->setParameter('response', $response);

        return $qb->getQuery()->getResult();
    }

}

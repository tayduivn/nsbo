<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ReportingCategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReportingCategoryRepository extends EntityRepository
{
    public function count()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('COUNT(c)');

        return $qb->getQuery()->getSingleScalarResult();
    }


    /**
     * @param $page
     * @param $order
     * @return ReportingCategory[]
     */
    public function search($page, $order)
    {
        $qb = $this->createQueryBuilder('c');
        if (is_array($order)) {
            foreach ($order as $orderName => $orderType) {
                $qb->orderBy('c.' . $orderName, $orderType);
            }
        }
        $qb->setMaxResults(25)
                ->setFirstResult($page * 25);

        return $qb->getQuery()->getResult();
    }
}

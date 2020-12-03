<?php

namespace AppBundle\Repository;

/**
 * ReportingCategoryHeadingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReportingObjectHeadingRepository extends \Doctrine\ORM\EntityRepository
{
    public function findObjectsByHeading($heading)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c.id, c.objet')
            ->where('c.reportingHeading = :reportingHeading')->setParameter('reportingHeading',$heading);
        return $qb->getQuery()->getResult();
    }
}
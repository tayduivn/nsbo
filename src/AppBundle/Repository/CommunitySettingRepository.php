<?php

namespace AppBundle\Repository;

/**
 * CommunitySettingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommunitySettingRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllSettings()
    {
        $qb = $this->createQueryBuilder('s');
        return $qb;
    }
}
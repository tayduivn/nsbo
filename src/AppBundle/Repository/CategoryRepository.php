<?php

namespace AppBundle\Repository;

use AppBundle\Entity\CommunityUsers;
use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
    public function count($community)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('COUNT(c)');
        if($community)
        {
            $qb->where('c.community = :community')->setParameter('community', $community);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function search($page, $order,$community)
    {
        $qb = $this->createQueryBuilder('c');
        if($community)
        {
            $qb->where('c.community = :community')->setParameter('community', $community);
        }
        if (is_array($order)) {
            foreach ($order as $orderName => $orderType) {
                $qb->orderBy('c.' . $orderName, $orderType);
            }
        }
        $qb->setMaxResults(25)
                ->setFirstResult($page * 25);

        return $qb->getQuery()->getResult();
    }
    
    public function findAll()
    {
        return $this->findBy(array(), array('name' => 'ASC'));
    }
    public function findCitizenCategories()
    {
        $qb = $this->createQueryBuilder('c')
                ->where('c.name != :game')
                ->setParameter('game', 'Jeux');
        return $qb->getQuery()->getResult();
    }

    public function findCatMerchantByCommunity($community)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.community = :community')->setParameter('community', $community)
            ->andWhere('c.type = :type')->setParameter('type','Thème commerce / partenaire');
        return $qb;
    }

    public function findCatAssociationByCommunity($community)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.community = :community')->setParameter('community', $community)
            ->andWhere('c.type = :type')->setParameter('type','Activité groupe / association');
        return $qb;
    }

     public function findCatByCommunity($community)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.community = :community')->setParameter('community', $community);
            
        return $qb;
    }

    public function findCatByUser($user)
    {
        /** @var CommunityUsers $communities */
        $communities = $user->getCommunities();
        $comm = [];

        foreach ($communities as $city) {
            if($city->getType() == 'approved'){
                $comm[]=$city->getCommunity();
            }


        }
        $qb = $this->createQueryBuilder('c')
            ->where('c.community in (:communities)')->setParameter('communities', $comm);

        return $qb->getQuery()->getResult();
    }

    public function findByCatAssoNameAnCommunities($catName, $followedCommunities)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.community in (:communities)')->setParameter('communities', $followedCommunities)
            ->andWhere('c.type = :type')->andWhere('c.name = :name')->setParameter('type','Activité groupe / association')->setParameter('name', $catName);

        return $qb->getQuery()->getResult();
    }

    public function findByCatMerchNameAnCommunities($catName, $followedCommunities)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.community in (:communities)')->setParameter('communities', $followedCommunities)
            ->andWhere('c.type = :type')->andWhere('c.name = :name')->setParameter('type','Thème commerce / partenaire')->setParameter('name', $catName);

        return $qb->getQuery()->getResult();
    }




}

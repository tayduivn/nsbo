<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Association;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use UserBundle\Entity\User;

/**
 * AssociationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AssociationRepository extends EntityRepository
{
    public function count($cityhall = null, $dateBefore = null, $dateAfter = null, $name = null, $enabled = null, $moderate = null, $wait = null)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('COUNT(c)');
        if ($cityhall) {
            $qb->andWhere('c.community = :cityhall')->setParameter('cityhall', $cityhall);
        }
        if ($dateBefore) {
            $dateBefore = substr($dateBefore, 6, 4) . '-' . substr($dateBefore, 3, 2) . '-' . substr($dateBefore, 0, 2) . ' ' . substr($dateBefore, 11, 8);
            $qb->andwhere('c.createAt >= :dateBefore')->setParameter('dateBefore', $dateBefore);
        }
        if ($dateAfter) {
            $dateAfter = substr($dateAfter, 6, 4) . '-' . substr($dateAfter, 3, 2) . '-' . substr($dateAfter, 0, 2) . ' ' . substr($dateAfter, 11, 8);
            $qb->andWhere('c.createAt <= :dateAfter')->setParameter('dateAfter', $dateAfter);
        }
        if ($name) {
            $qb->andWhere('c.name LIKE :name')->setParameter('name', '%' . $name . '%');
        }
        if ($enabled != '' && $enabled != null) {
            $qb->andWhere('c.enabled = :enabled')->setParameter('enabled', $enabled);
        }
        if ($wait != '' && $wait == 'true') {
            $qb->andWhere('c.moderate = :moderate')->setParameter('moderate', 'wait');
        } else {
            if ($moderate != '' && $moderate != null) {
                $qb->andWhere('c.moderate = :moderate')->setParameter('moderate', $moderate);
            }
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function search($page, $order, $cityhall, $dateBefore, $dateAfter, $name, $enabled, $moderate, $wait, $limit = 25, $categories = null)
    {
        $qb = $this->createQueryBuilder('c');
        if ($cityhall) {
            $qb->andWhere('c.community = :cityhall')->setParameter('cityhall', $cityhall);
        }
        if ($dateBefore) {
            $dateBefore = substr($dateBefore, 6, 4) . '-' . substr($dateBefore, 3, 2) . '-' . substr($dateBefore, 0, 2) . ' ' . substr($dateBefore, 11, 8);
            $qb->andwhere('c.createAt >= :dateBefore')->setParameter('dateBefore', $dateBefore);
        }
        if ($dateAfter) {
            $dateAfter = substr($dateAfter, 6, 4) . '-' . substr($dateAfter, 3, 2) . '-' . substr($dateAfter, 0, 2) . ' ' . substr($dateAfter, 11, 8);
            $qb->andWhere('c.createAt <= :dateAfter')->setParameter('dateAfter', $dateAfter);
        }
        if ($name) {
            $qb->andWhere('c.name LIKE :name')->setParameter('name', '%' . $name . '%');
        }
        if ($enabled != '' && $enabled != null) {
            $qb->andWhere('c.enabled = :enabled')->setParameter('enabled', $enabled);
        }
        if ($wait != '' && $wait == 'true') {
            $qb->andWhere('c.moderate = :moderate')->setParameter('moderate', 'wait');
        } else {
            if ($moderate != '' && $moderate != null) {
                $qb->andWhere('c.moderate = :moderate')->setParameter('moderate', $moderate);
            }
        }

        if ($categories) {
            $qb->where('c.category IN (:categories)')->setParameter('categories', $categories);
        }

        if (is_array($order)) {
            foreach ($order as $orderName => $orderType) {
                $qb->orderBy('c.' . $orderName, $orderType);
            }
        }
        if ($page !== false) {
            $qb->setMaxResults($limit)
                    ->setFirstResult($page * $limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllByCommunity($community)
    {
        $qb = $this->createQueryBuilder('u');
        if ($community != '') {
            $qb->andWhere('u.community = :community')->setParameter('community', $community);
            $qb->andWhere('u.moderate = :moderate')->setParameter('moderate', 'accepted');
        }
        return $qb;
    }

    public function findUserAssociations($user)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere('c.suAdmin = :user OR :user MEMBER OF c.admins')->setParameter('user', $user)
            ->andWhere('c.enabled = :enabled')->setParameter('enabled', true)
                ->andWhere('c.moderate = :accepted OR c.moderate = :wait')->setParameter('accepted', 'accepted')->setParameter('wait', 'wait');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @return Association[]
     */
    public function getJoinedAssociations($user)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->join("a.users", "u")

            ->andWhere('u.user = :user')->setParameter('user', $user)
            ->andWhere('u.type = :status')->setParameter('status', 'approved')
            ->andWhere('a.enabled = :enabled')->setParameter('enabled', true)
            ->andWhere('a.moderate = :accepted ')->setParameter('accepted', 'accepted');

        return $qb->getQuery()->getResult();
    }

    public function findAllByCities($user)
    {
        $cities = $user->getCitiesIds();
        $qb = $this->createQueryBuilder('a');
        $qb->join("a.city", "ac");
        $qb->where("ac.id IN (:cities)")->setParameter("cities", $cities);
        $qb->orderBy('a.name');

        return $qb->getQuery()->getResult();
    }

    public function appSearch($user, $key, $theme, $city)
    {
        if ($city == "all") {
            $cities = $user->getCommunities();
        } else {
            $cities = $city;
        }

        $comm = [];

        foreach ($cities as $city) {
            if($city->getType() == 'approved'){
                $comm[]=$city->getCommunity();
            }

        }




        $qb = $this->createQueryBuilder('a')
                        ->select("a.id, a.name")
                        ->leftJoin('a.image', 'img')
                        ->addSelect("img.id As image")
                        ->where("a.community IN (:cities)")
                        ->setParameter('cities', $comm)
                        ->andWhere('a.enabled = :enabled')->setParameter('enabled', true)
                        ->andWhere('a.moderate = :accepted OR a.moderate = :wait')->setParameter('accepted', 'accepted')->setParameter('wait', 'wait');


        if ($key) {
            $qb->andWhere("a.name LIKE :name")
                    ->setParameter("name", '%' . $key . '%');
        }

        if ($theme) {
            $qb->andWhere("a.category = :category")
                    ->setParameter("category", $theme);
        }

        return $qb->getQuery()->getResult();
    }

    public function findBySuAdmin($user)
    {
        $qb = $this->createQueryBuilder('a')
                ->select("a.id, a.name")
                ->leftJoin('a.image', 'img')
                ->addSelect("img.id As image")
                ->join("a.suAdmin", "u")
                ->where("u.id = :user")
                ->setParameter("user", $user->getId());
        return $qb->getQuery()->getResult();
    }

    public function getMembershipDemands($association)
    {
        $qb = $this->createQueryBuilder('a')
            ->select("a.id, a.name")
            ->leftJoin('a.users', 'demand')

            ->leftJoin('demand.user','u')
            ->addSelect("u.id As user_id, u.firstname as firstname, u.lastname as lastname, u.email as email")
            ->leftJoin('u.image','img')
            ->addSelect('img.filename as userImg')
            ->where('demand.association = :asso')->setParameter('asso',$association)
            ->andWhere('demand.type = :type')->setParameter('type','pending');
        return $qb->getQuery()->getResult();
    }

    public function getMemberships($association)
    {
        $qb = $this->createQueryBuilder('a')
            ->select("a.id, a.name")
            ->leftJoin('a.users', 'demand')

            ->leftJoin('demand.user','u')
            ->addSelect("u.id As user_id, u.firstname as firstname, u.lastname as lastname, u.email as email")
            ->leftJoin('u.image','img')
            ->addSelect('img.filename as userImg')
            ->where('demand.association = :asso')->setParameter('asso',$association)
            ->andWhere('demand.type = :type')->setParameter('type','approved');
        return $qb->getQuery()->getResult();
    }

    /**
     * @param Association $association
     * @param $role
     * @param $em
     * @param null $volunteers
     * @return array
     */
    public function formatAssocition($association,$role,$em,$volunteers = null)
    {
        $asso = array(
            "id" => $association->getId(),
            "createAt" => $association->getCreateAt(),
            "name" => $association->getName(),
            "address" => $association->getAddress(),
            "codePostal" => $association->getCodePostal(),
            "description" => $association->getDescription(),

            "email" => $association->getEmail(),
            "phone" => $association->getPhone(),
            "moderate" => $association->getModerate(),
            "role" => $role,
            "timing" => $association->getTiming(),
            "image_url" => $association->getImageUrl(),
            "community" => array(
                "id" => $association->getCommunity()->getId(),
                "categories" => $association->getCommunity()->getCategories()),
            "city" => $association->getCity()? array(
                "id" => $association->getCity()->getId(),
                "name" => $association->getCity()->getName(),
                'code' => $association->getCity()->getZipcode()): null,
            "type" => 'association'
        );

        if($association->getCategory()) {
            $asso['category'] = array(
                "id" => $association->getCategory()->getId(),
                "name" => $association->getCategory()->getName(),
                "type" => $association->getCategory()->getType(),
            );
        }

        if ($volunteers){
            $asso['volunteers'] =  $em->getRepository("AppBundle:EventVolunteer")->findVolunteers('association', $association);
        }

        return $asso;
    }
}

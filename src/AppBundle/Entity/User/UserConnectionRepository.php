<?php

namespace AppBundle\Entity\User;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * UserConnectionRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class UserConnectionRepository extends EntityRepository
{
    public function getFollowee(User $followee)
    {
        $sql = "SELECT followee_id
                FROM photogram_new.user_connections
                INNER JOIN photogram_new.user
                ON user_connections.followee_id = user.id;";

        $types = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter("followee", $followee)
            ->getResult();

        return count($types) ? $types[0] : null;
    }

    public function getFollower(User $follower)
    {
        $sql = "SELECT follower_id
                FROM photogram_new.user_connections
                INNER JOIN photogram_new.user
                ON user_connections.follower_id = user.id;";

        $types = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter("follower", $follower)
            ->getResult();

        return count($types) ? $types[0] : null;
    }


}

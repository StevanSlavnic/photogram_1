<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 7/17/16
 * Time: 4:38 PM
 */

namespace AppBundle\Manager;


use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Profile;
use AppBundle\Entity\User\UserConnection;
use AppBundle\Event\UserWasFollowedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserConnectionManager
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EntityManager   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Follow user
     *
     * Returns Connection if ok, or false if user already following other user
     *
     * @param User $follower
     * @param User $followee
     *
     * @return bool|UserConnection
     */
    public function follow(User $follower, User $followee)
    {
        if($follower->getId() === $followee->getId()) {
            return true;
        }

        $userConnectionRepo = $this->em->getRepository('AppBundle:User\UserConnection');

        // Check if there is connection between users
        $userConnection = $userConnectionRepo->findOneBy(array(
            'follower' => $follower->getId(),
            'followee' => $followee->getId()
        ));

        if(!$userConnection) {
            $connection = new UserConnection();

            $connection->setFollower($follower);
            
            $connection->setFollowee($followee);

            // Check if this follower is followed by followee, and if so, set isFollowedBack to true
            /** @var UserConnection $inverseConnection*/
            $inverseConnection = $userConnectionRepo->findOneBy(array('follower' => $followee,'followee' => $follower));

            if($inverseConnection) {
                $connection->setIsFollowedBack(true);
                $inverseConnection->setIsFollowedBack(true);
                $this->em->persist($inverseConnection);
            }

            $this->em->persist($connection);
            $this->em->flush();

            $this->eventDispatcher->dispatch('user_was_followed_event', new UserWasFollowedEvent($followee, $follower));

            return $connection;
        }

        return true;
    }

    /**
     * Is user following another one
     *
     * @param User $follower
     * @param User $followee
     *
     * @return bool
     */
    public function isFollowing(User $follower, User $followee)
    {
        $userConnectionRepo = $this->em->getRepository('AppBundle:User\UserConnection');

        $userConnection = $userConnectionRepo->findBy(array(
            'follower' => $follower->getId(),
            'followee' => $followee->getId()
        ));

    //        dump($userConnection);die();
        if(!empty($userConnection)) {
            return true;
        }

        return false;
    }


    /**
     * Unfollow user
     *
     * @param User $follower
     * @param User $followee
     *
     * @return bool
     */
    public function unfollow(User $follower, User $followee)
    {
        if($follower->getId() === $followee->getId()) {
            return false;
        }

        $userConnectionRepo = $this->em->getRepository('AppBundle:User\UserConnection');

        // Check if there is connection between users
        $userConnection = $userConnectionRepo->findOneBy(array(
            'follower' => $follower->getId(),
            'followee' => $followee->getId()
        ));

        // Return false if follower does not following followee
        if(!$userConnection) {
            return false;
        }

        // Remove connection from follower
        $this->em->remove($userConnection);
        $this->em->flush();


        // Check if followee follows follower and if so, remove isFollowedBack form followee
        $inverseConnection = $userConnectionRepo->findOneBy(array('follower' => $followee,'followee' => $follower));

        if($inverseConnection) {
            $inverseConnection->setIsFollowedBack(false);
            $this->em->persist($inverseConnection);
        }

        return true;
    }


    /**
     * Get followers for given user
     *
     * @param User $user
     *
     * @return \Doctrine\ORM\Query
     */
    public function getFollowers(User $user)
    {
        return $this->em->getRepository('AppBundle:User\UserConnection')
            ->createQueryBuilder('user\userConnection')
            ->where('user\userConnection.followee = :user_id')
            ->setParameter('user_id', $user->getId())
            ->getQuery();
    }

    /**
     * Get users that given user follows
     *
     * @param User $user
     *
     * @return \Doctrine\ORM\Query
     */
    public function getFollowing(User $user)
    {
        return $this->em->getRepository('AppBundle:User\UserConnection')
            ->createQueryBuilder('user\userConnection')
            ->where('user\userConnection.follower = :user_id')
            ->setParameter('user_id', $user->getId())
            ->getQuery();
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getStats(User $user)
    {
        return array(
            'followers' => $following = count($this->getFollowers($user)->getResult()),
        );
    }


}
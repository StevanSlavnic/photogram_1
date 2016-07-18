<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 7/17/16
 * Time: 4:14 PM
 */

namespace AppBundle\Event;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class UserWasFollowed
 *
 * @package AppBundle\Event\User
 */
class UserWasFollowedEvent extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var User
     */
    private $follower;

    /**
     * UserWasFollowed constructor.
     *
     * @param User $user
     * @param User $follower
     */
    public function __construct(User $user, User $follower)
    {
        $this->user     = $user;
        $this->follower = $follower;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return User
     */
    public function getFollower()
    {
        return $this->follower;
    }
}
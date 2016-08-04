<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 7/16/16
 * Time: 12:54 AM
 */

namespace AppBundle\Entity;

namespace AppBundle\Entity\User;
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user_connections")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="UserConnectionRepository")
 */
class UserConnection
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="follower_id", referencedColumnName="id")
     */
    private $follower;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="followee_id", referencedColumnName="id")
     */
    private $followee;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_followed_back", type="boolean")
     */
    private $isFollowedBack = false;

    /**
     * @return User
     */
    public function getFollower()
    {
        return $this->follower;
    }

    /**
     * @param User $follower
     *
     * @return UserConnection
     */
    public function setFollower($follower)
    {
        $this->follower = $follower;

        return $this;
    }

    /**
     * @return User
     */
    public function getFollowee()
    {
        return $this->followee;
    }

    /**
     * @param User $followee
     *
     * @return UserConnection
     */
    public function setFollowee($followee)
    {
        $this->followee = $followee;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIsFollowedBack()
    {
        return $this->isFollowedBack;
    }

    /**
     * @param boolean $isFollowedBack
     *
     * @return UserConnection
     */
    public function setIsFollowedBack($isFollowedBack)
    {
        $this->isFollowedBack = $isFollowedBack;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
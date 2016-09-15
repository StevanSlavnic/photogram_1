<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use AppBundle\Entity\Profile;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\MessageBundle\Model\ParticipantInterface;
use AppBundle\Entity\User\UserConnection;
use FOS\ElasticaBundle\Configuration\Search;


/**
 * @ORM\Entity
 * @Search(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser implements ParticipantInterface
{
//    const TYPE_USER = ;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Profile", inversedBy="user", cascade={"remove", "persist"})
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    private $profile;

    /**
     * @Gedmo\Slug(fields={"fullName"}, unique=true, updatable=false, separator="-")
     */
    protected $username;

    /**
     * @ORM\Column(name="full_name", type="string", nullable=false)
     */
    protected $fullName;


    private $connections;

    /**
     * User connection
     */
    public function __construct()
    {
        parent::__construct();
        $this->connections = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param Profile $profile
     */
    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this|\FOS\UserBundle\Model\UserInterface|void
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param ArrayCollection $connections
     */
    public function setConnections($connections)
    {
        $this->connections = $connections;
    }

    /**
     * @return ArrayCollection
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * @return int
     */
    public function getProfileId()
    {
        return $this->getProfile()->getId();
    }

    /**
     * Is the given User the author of this Post?
     *
     * @return bool
     */
    public function isProfileOwner(User $user = null)
    {
        return $user && $user->getProfile() == $this->getProfileId();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 8/15/16
 * Time: 10:24 PM
 */

namespace AppBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="like_posts")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 */
class LikePost
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
     * @ORM\JoinColumn(name="liker_id", referencedColumnName="id")
     */
    private $liker;

    /**
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post")
     * @ORM\JoinColumn(name="liked_post_id", referencedColumnName="id")
     */
    private $liked_post;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getLiker()
    {
        return $this->liker;
    }

    /**
     * @param \AppBundle\Entity\User $liker
     */
    public function setLiker($liker)
    {
        $this->liker = $liker;
    }

    /**
     * @return Post
     */
    public function getLikedPost()
    {
        return $this->liked_post;
    }

    /**
     * @param Post $liked_post
     */
    public function setLikedPost($liked_post)
    {
        $this->liked_post = $liked_post;
    }

}
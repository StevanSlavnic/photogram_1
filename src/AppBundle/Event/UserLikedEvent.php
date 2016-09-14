<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 8/16/16
 * Time: 9:14 PM
 */

namespace AppBundle\Event;


use AppBundle\Entity\LikePost;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;

class UserLikedEvent
{

    /**
     * @var Post
     */
    private $post;

    /**
     * @var User
     */
    private $liker;

    /**
     * UserLikedPost constructor.
     *
     * @param Post $post
     * @param User $liker
     */
    public function __construct(Post $post, User $liker)
    {
        $this->post     = $post;
        $this->liker = $liker;
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return User
     */
    public function getLiker()
    {
        return $this->liker;
    }
}
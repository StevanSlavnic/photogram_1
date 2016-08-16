<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 8/15/16
 * Time: 10:15 PM
 */

namespace AppBundle\Manager;


use AppBundle\Entity\LikePost;
use AppBundle\Entity\User;
use AppBundle\Event\UserLikedEvent;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Post;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LikePostManager
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
     * Like posts
     *
     * Returns Like if ok, or false if user already like post
     *
     * @param User $liker
     * @param Post $post
     * @return bool|LikePost
     */
    public function like(User $liker, Post $post)
    {
        if($liker->getId()) {
            return true;
        }

        $likePostsRepo = $this->em->getRepository('AppBundle:LikePost');

        // Check if there is liked posts
        $likePosts = $likePostsRepo->findOneBy(array(
            'liker' => $liker->getId(),
            'liked_post' => $post->getId()
        ));

        if(!$likePosts) {
            $like = new LikePost();

            $like->setLiker($liker);
            $like->setLikedPost($post);

            $this->em->persist($like);
            $this->em->flush();

            $this->eventDispatcher->dispatch('user_was_liked_post_event', new UserLikedEvent($post, $liker));

            return $likePosts;
        }

        return true;
    }

    /**
     * Is user following another one
     *
     * @param User $liker
     * @param Post $likedPost
     * @return bool
     * @internal param Post $post
     *
     */
    public function isLiked(User $liker, Post $likedPost)
    {
        $likePosts = $this->em->getRepository('AppBundle:LikePost');

        $likePosts = $likePostsRepo->findBy(array(
            'liker' => $liker->getId(),
            'liked_post' => $likedPost->getId()
        ));

        //        dump($userConnection);die();
        if(!empty($likePosts)) {
            return true;
        }

        return false;
    }
}
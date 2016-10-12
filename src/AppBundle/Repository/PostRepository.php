<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Post;
use AppBundle\Entity\Profile;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 * See http://symfony.com/doc/current/book/doctrine.html#custom-repository-classes
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class PostRepository extends EntityRepository
{
    /**
     * @return Query
     */
    public function queryLatest()
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT p
                FROM AppBundle:Post p
                WHERE p.publishedAt <= :now
                ORDER BY p.publishedAt DESC
            ')
            ->setParameter('now', new \DateTime())
        ;
    }

    /**
     * Get latest post for given user
     *
     * @param User $user
     *
     * @return Query
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatestForUserQuery(User $user)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('posts')
            ->from('AppBundle:Post', 'post')
            ->where('post.user = :user')
            ->orderBy('post.id', 'DESC')
            ->setParameter('user', $user)
            ->getQuery();
    }

    /**
     * @param User $user
     * @param Post $post
     * @param Profile $profile
     * @return Query
     * @internal param Profile $profile
     */
    public function getUserPosts(User $user, Post $post, Profile $profile)
    {
        return $this->getEntityManager()->createQuery(
            'SELECT * FROM photogram_new.post as p
            INNER JOIN photogram_new.profile as prof ON prof.id = p.user_id
            INNER JOIN photogram_new.user as us ON us.profile_id = prof.id 
            ORDER BY prof.id DESC
            ;'
        )
            ->setParameter('now', new \DateTime())
        ->getResult();
    }


    public function findOneByUsernameOrEmail($username)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param int $page
     *
     * @return Pagerfanta
     */
    public function findLatest($page = 1)
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($this->queryLatest(), false));
        $paginator->setMaxPerPage(Post::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}

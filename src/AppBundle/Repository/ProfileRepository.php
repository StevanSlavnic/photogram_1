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

use AppBundle\Entity\Profile;
use AppBundle\Entity\User;
use AppBundle\Entity\Post;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query;


/**
 * This custom Doctrine repository is empty because so far we don't need any custom
 * method to query for application user information. But it's always a good practice
 * to define a custom repository that will be used when the application grows.
 * See http://symfony.com/doc/current/book/doctrine.html#custom-repository-classes
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class ProfileRepository extends EntityRepository
{
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
            ->orderBy('post.publishedAt', 'DESC')
            ->setParameter('user', $user)
            ->getQuery();
    }
}

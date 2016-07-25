<?php
/**
 * Created by Eton Digital.
 * User: Djordje Bakic <djordje.bakic@etondigital.com>
 * Date: 11/20/15 2:34 PM
 */

namespace AppBundle\Twig;


use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Manager\UserConnectionManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppExtension extends \Twig_Extension
{

    private $userConnectionManager;
    /**
     * @var Container
     */
    private $container;

    /**
     * AppExtension constructor.
     *
     * @param UserConnectionManager            $userConnectionManager
     * @param ContainerInterface               $container
     */
    public function __construct(UserConnectionManager $userConnectionManager, ContainerInterface $container)
    {
        $this->userConnectionManager = $userConnectionManager;
        $this->container = $container;

    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_twig_extension';
    }

    public function getFunctions()
    {
        return array_merge(parent::getFunctions(),[
            new \Twig_SimpleFunction('getConnectionStatsForUser', array($this, 'getConnectionStatsForUser')),
            new \Twig_SimpleFunction('isFollowing', array($this, 'isFollowing')),
            new \Twig_SimpleFunction('getMediaPublicUrl', array($this, 'getMediaPublicUrl')),
            new \Twig_SimpleFunction('isUserLikedPost', array($this, 'isUserLikedPost')),
            new \Twig_SimpleFunction('getLikeSentence', array($this, 'getLikeSentence')),
        ]);
    }

    public function getConnectionStatsForUser($user)
    {
        return $this->userConnectionManager->getStats($user);
    }

    public function isFollowing($requesterConnections, $profileConnection)
    {
        foreach ($requesterConnections as $requesterConnection) {
            if($requesterConnection->getFollowee()->getId() === $profileConnection->getId()) {
                return true;
            }
        }
        return false;
    }
}
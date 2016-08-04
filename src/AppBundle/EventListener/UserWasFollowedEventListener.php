<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 7/17/16
 * Time: 4:17 PM
 */

namespace AppBundle\EventListener;

use Doctrine\ORM\EntityManager;

use AppBundle\Event\UserWasFollowedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserWasFollowedEventListener implements EventSubscriberInterface
{

    private $em;

    /**
     * DrinkCreatedListener constructor.
     *
     * @param $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return array('user_was_followed_event');
    }

    /**
     * @param UserWasFollowedEvent $event
     */
    public function handle(UserWasFollowedEvent $event)
    {
        $user = $event->getUser();

        $follower = $event->getFollower();

//        $notification = new Notification();
//        $notification->setType(Notification::TYPE_FOLLOWING);
//        $notification->setStatus(Notification::STATUS_NEW);
//        $notification->setRecipient($user);
//        $notification->setByUser($follower);
//
//        $this->em->persist($notification);
        $this->em->flush();
    }

    public function user_was_followed_event()
    {
        return $this->getSubscribedEvents();
    }
}
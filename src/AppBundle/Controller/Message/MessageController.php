<?php

namespace AppBundle\Controller\Message;

use AppBundle\Entity\Profile;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\MessageBundle\Provider\ProviderInterface;
use Symfony\Component\Routing\Annotation\Route;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\ModelManager\ThreadManagerInterface;


class MessageController extends \FOS\MessageBundle\Controller\MessageController
{
    /**
     * Displays the authenticated participant inbox
     * @Route("/user/{username}/inbox/", name="photo_message_inbox")
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     * @return Response
     */
    public function inboxAction()
    {

        $threads = $this->getProvider()->getInboxThreads();

        /** @var Profile $username */
        $username = $this->getUser()->getProfile()->getProfileUsername();

        return $this->container->get('templating')->renderResponse('AppBundle:Message:inbox.html.twig', array(
            'threads' => $threads,
            'username' => $username
        ));

    }

    /**
     * Displays the authenticated participant messages sent
     * @Route("/user/{username}/inbox/sent", name="photo_message_sent")
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     * @return Response
     */
    public function sentAction()
    {
        $threads = $this->getProvider()->getSentThreads();

        return $this->container->get('templating')->renderResponse('@App/Message/sent.html.twig', array(
            'threads' => $threads,
            'username' => 'Milenko-Dolovac'
        ));
    }

    /**
     * Displays the authenticated participant deleted threads
     * @Route("/user/{username}/inbox/deleted", name="photo_message_delete")
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     * @return Response
     */
    public function deletedAction()
    {
        $threads = $this->getProvider()->getDeletedThreads();

        return $this->container->get('templating')->renderResponse('@App/Message/deleted.html.twig', array(
            'threads' => $threads,
            'username' => 'Milenko-Dolovac'
        ));
    }

    /**
     * Displays a thread, also allows to reply to it
     *
     * @param string $threadId the thread id
     * @Route("/user/{username}/inbox/message/{threadId}", name="photo_message_thread_id")
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     * @return Response
     */
    public function threadAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $form = $this->container->get('fos_message.reply_form.factory')->create($thread);
        $formHandler = $this->container->get('fos_message.reply_form.handler');

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('photo_message_sent', array(
                'threadId' => $message->getThread()->getId()
            )));
        }

        return $this->container->get('templating')->renderResponse('@App/Message/thread.html.twig', array(
            'form' => $form->createView(),
            'thread' => $thread,
            'username' => 'Milenko-Dolovac'
        ));
    }

    /**
     * Create a new message thread
     * @Route("/user/{username}/inbox/new-message", name="photo_message_new_thread")
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     * @return Response
     */
    public function newThreadAction()
    {
        $form = $this->container->get('fos_message.new_thread_form.factory')->create();
        $formHandler = $this->container->get('fos_message.new_thread_form.handler');

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('photo_message_sent', array(
                'threadId' => $message->getThread()->getId(),
                'username' => 'Slavnić-Stevan'
            )));
        }

        return $this->container->get('templating')->renderResponse('@App/Message/newThread.html.twig', array(
            'form' => $form->createView(),
            'data' => $form->getData()
        ));
    }

    /**
     * Deletes a thread
     *
     * @param string $threadId the thread id
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     * @return RedirectResponse
     */
    public function deleteAction($threadId)
    {

        $thread = $this->getProvider()->getThread($threadId);
        $this->container->get('fos_message.deleter')->markAsDeleted($thread);
        $this->container->get('fos_message.thread_manager')->saveThread($thread);

        return new RedirectResponse($this->container->get('router')->generate('photo_message_inbox', array(
            'username' => 'Milenko-Dolovac'
        )));
    }

    /**
     * Undeletes a thread
     *
     * @param string $threadId
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     * @return RedirectResponse
     */
    public function undeleteAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $this->container->get('fos_message.deleter')->markAsUndeleted($thread);
        $this->container->get('fos_message.thread_manager')->saveThread($thread);

        return new RedirectResponse($this->container->get('router')->generate('photo_message_inbox', array(
            'username' => 'Milenko-Dolovac'
        )));
    }

    /**
     * Searches for messages in the inbox and sentbox
     * @Route("/user/{username}/inbox/search-message", name="photo_message_search")
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     * @return Response
     */
    public function searchAction()
    {
        $query = $this->container->get('fos_message.search_query_factory')->createFromRequest();
        $threads = $this->container->get('fos_message.search_finder')->find($query);

        return $this->container->get('templating')->renderResponse('@App/Message/search.html.twig', array(
            'query' => $query,
            'threads' => $threads
        ));
    }

    /**
     * Gets the provider service
     *
     * @return ProviderInterface
     */
    protected function getProvider()
    {
        return $this->container->get('fos_message.provider');
    }

    /**
     * @return User $user
     */
    private function getUser()
    {
        return $this;
    }


}

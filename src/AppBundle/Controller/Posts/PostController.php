<?php

namespace AppBundle\Controller\Posts;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Entity\Profile;
use Doctrine\ORM\Mapping\Id;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Controller\BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class PostController
 * @package AppBundle\Controller
 *
 */

//* @Route("/posts")

class PostController extends Controller
{
    /**
     * @Route("/posts/", defaults={"page": 1}, name="blog_index")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="blog_index_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     */

    // @Route("/posts") can be used as global route

    public function indexAction(Request $request, $page)
    {
        $loggedUser = $this->getLoggedUser();
        $user = $this->getUser();
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findLatest($page);
        $profiles = $this->getDoctrine()->getRepository('AppBundle:Profile')->findAll();
        $profile = $this->getDoctrine()->getRepository('AppBundle:Profile')->findOneBy(array());
        $form = $this->createForm('AppBundle\Form\CommentType');
//        $deleteForm = $this->createForm('')


        $form->handleRequest($request);
        $userConnectionManager = $this->get('app.manager.user_connection_manager');

        return $this->render('@App/PostsList/index.html.twig', array(
            'posts' => $posts,
            'user' => $user,
            'profile' => $profile,
            'profiles' => $profiles,
            'form' => $form->createView(),
            'is_following' => $userConnectionManager->isFollowing($loggedUser, $user),
        ));
    }

    /**
     * Creates a new Post entity.
     *
     * @Route("/post/new", name="post_new")
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction(Request $request)
    {

        if (null === $this->getUser() ) {
            return $this->render('@App/Homepage/index.html.twig');
        }

        $post = new Post();
        $post->setAuthorEmail($this->getUser()->getEmail());
        $post->setUser($this->getUser());

        // See http://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm('AppBundle\Form\PostType', $post)
            ->add('saveAndCreateNew', 'Symfony\Component\Form\Extension\Core\Type\SubmitType');

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See http://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {
//            $post->setSlug($this->get('slugger')->slugify($post->getTitle()));


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See http://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'post.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('post_new');
            }

            return $this->redirectToRoute('blog_index');
        }

        return $this->render('@App/Posts/new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/post/{id}/edit", requirements={"id": "\d+"}, name="post_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Post $post, Request $request)
    {
        if (null === $this->getUser() || !$post->isAuthor($this->getUser())) {
            throw $this->createAccessDeniedException('Posts can only be edited by their authors.');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('AppBundle\Form\PostType', $post);
        $deleteForm = $this->createDeleteForm($post);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
//            $post->setSlug($this->get('slugger')->slugify($post->getTitle()));
            $entityManager->flush();

            $this->addFlash('success', 'post.updated_successfully');

//            return $this->redirectToRoute('post_edit', array('id' => $post->getId()));
//            return $this->redirectToRoute('blog_post', array('posts' => $post));
            return $this->redirectToRoute('blog_index', array('posts' => $post));
        }

        return $this->render('@App/Posts/edit.html.twig', array(
            'post'        => $post,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Post entity.
     *
     * @Route("/post/{id}/delete", name="post_delete")
     * @Method("DELETE")
     * @Security("post.isAuthor(user)")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     * The isAuthor() method is defined in the AppBundle\Entity\Post entity.
     */
    public function deleteAction(Request $request, Post $post)
    {
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($post);
            $entityManager->flush();

            $this->addFlash('success', 'post.deleted_successfully');
        }

        return $this->redirectToRoute('blog_index', array('posts' => $post));
    }

    /**
     * Creates a form to delete a Post entity by id.
     *
     * This is necessary because browsers don't support HTTP methods different
     * from GET and POST. Since the controller that removes the blog posts expects
     * a DELETE method, the trick is to create a simple form that *fakes* the
     * HTTP DELETE method.
     * See http://symfony.com/doc/current/cookbook/routing/method_parameters.html.
     *
     * @param Post $post The post object
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('POST')
            ->getForm()
            ;
    }

    /**
     * @Route("/post/{id}", name="blog_post")
     * @Method("GET")
     *
     * NOTE: The $post controller argument is automatically injected by Symfony
     * after performing a database query looking for a Post with the 'slug'
     * value given in the route.
     * See http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html
     * @param Post $post
     * @return Response
     */
    public function postShowAction(Post $post)
    {
        return $this->render('@App/Posts/post_show.html.twig', array(
            'post' => $post,
//            'profile' => $profile
        ));
    }

    /**
     * @Route("/comment/{postId}/new", name="comment_new")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @ParamConverter("post", options={"mapping": {"postId": "id"}})
     *
     * NOTE: The ParamConverter mapping is required because the route parameter
     * (postId) doesn't match any of the Doctrine entity properties (slug).
     * See http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#doctrine-converter
     */
    public function commentNewAction(Request $request, Post $post)
    {
        $form = $this->createForm('AppBundle\Form\CommentType');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setAuthorEmail($this->getUser()->getEmail());
            $comment->setUsername($this->getUser()->getfullName());
            $comment->setPost($post);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('blog_post', array('id' => $post->getId()));

        }

        return $this->render('@App/Posts/comment_form_error.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * This controller is called directly via the render() function in the
     * blog/post_show.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     *
     * @param Post $post
     *
     * @return Response
     */
    public function commentFormAction(Post $post)
    {
        $form = $this->createForm('AppBundle\Form\CommentType');

        return $this->render('@App/Posts/_comment_form.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    private function getLoggedUser()
    {
        $loggedUser = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!is_object($loggedUser) || !$loggedUser instanceof UserInterface) {
            throw new AccessDeniedException('Please login first.');
        }

        return $loggedUser;
    }
}

<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\Profile;
use AppBundle\Entity\User;
use AppBundle\Entity\User\UserConnection;
use AppBundle\Repository\UserConnectionRepository;
use Doctrine\ORM\Mapping\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Controller\BaseController;
use AppBundle\Manager\UserConnectionManager;


class UserProfileController extends BaseController
{
    /**
     * @Route("/user/{username}/", name="profile_index")
     *
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     *
     * @Method("GET")
     *
     * @param Profile $profile
     *
     * @param User $followers
     * @return Response
     */
    public function showAction(Profile $profile)
    {
        $loggedUser = $this->getLoggedUser();
        $user = $profile->getUser();
        $userConnectionManager = $this->get('app.manager.user_connection_manager');

//        $likePostManager = $this->get('app.manager.like_post_manager');

        $followers = $userConnectionManager->getFollowers($profile->getUser())->getResult();
        $following = $userConnectionManager->getFollowing($profile->getUser())->getResult();


        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(array(
            'user' => $profile->getUser()
        ));

       return $this->render('AppBundle:User:profile.html.twig', array(
            'profile' => $profile,
            'user' => $user,
            'posts' => $posts,
            'followers' => $followers,
            'following' => $following,
            'is_following' => $userConnectionManager->isFollowing($loggedUser, $user),
            'is_followed_back' => $userConnectionManager->isFollowing($user, $loggedUser),
//            'is_liked' => $likePostManager->isLiked($user, $likedPost)
        ));
    }

    /**
     * @Route("/users-list/{page}", defaults={"page": 1}, name="photo_users_list")
     *
     * @param Request $request
     * @param $page
     *
     * @return Response
     * @internal param Post $post
     *
     */
    public function showUsersAction(Request $request, $page)
    {
        /** @var User $loggedUser */
        $loggedUser = $this->getLoggedUser();
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $profiles = $this->getDoctrine()->getRepository('AppBundle:Profile')->findAll();
        $profile = $this->getDoctrine()->getRepository('AppBundle:Profile')->findOneBy(array());

        return $this->render("@App/UsersList/usersList.html.twig", array(
            'user' => $users,
            'profile' => $profile,
            'profiles' => $profiles,
//            'is_following' => $userConnectionManager->isFollowing($loggedUser, $user),
        ));
    }

    /**
     * Follow button
     *
     * @param Profile $profile
     * @param Request $request
     * @return Response
     * @Route("/user/{username}/follow", name="app_profile_follow", condition="request.isXmlHttpRequest()")
     * @Method("POST");
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     */
    public function follow(Profile $profile, Request $request)
    {
//        $this->handleInactiveProfiles($profile);

        $user = $this->getLoggedUser();

        $userConnectionManager = $this->get('app.manager.user_connection_manager');

        $type = $_POST['type'];

        if($userConnectionManager->follow($user, $profile->getUser())) {
            return new JsonResponse(array(
                'success' => true,
//                'response' => $this->renderView('AppBundle:User:profile.html.twig', array(
                'response' => $this->renderView('AppBundle:User:follow_button_'. $type .'.html.twig', array(
                    'profile' => $profile,
                    'is_following' => true
                ))
            ));
        }

        return new JsonResponse([
            'success' => false,
//            'response' => $this->renderView('AppBundle:User:profile.html.twig', array(
            'response' => $this->renderView('AppBundle:User:follow_button_'.$type.'.html.twig', array(
                'profile' => $profile,
                'is_following' => true
            ))
        ]);
    }

    /**
     * Unfollow button
     *
     * @param Profile $profile
     * @param Request $request
     * @return Response
     * @Route("/user/{username}/unfollow", name="app_profile_unfollow", condition="request.isXmlHttpRequest()")
     * @Method("POST");
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     */
    public function unfollow(Profile $profile, Request $request)
    {
//        $this->handleInactiveProfiles($profile);

        $user = $this->getLoggedUser();

        $userConnectionManager = $this->get('app.manager.user_connection_manager');

        $type = $_POST['type'];

        if($userConnectionManager->unfollow($user, $profile->getUser())) {
            return new JsonResponse(array(
                'success' => true,
                'response' => $this->renderView('AppBundle:User:follow_button_'. $type .'.html.twig', array(
                    'profile' => $profile,
                    'is_following' => false
                ))
            ));
        }

        return new JsonResponse(array(
            'success' => false,
            'response' => $this->renderView('AppBundle:User:follow_button_'. $type .'.html.twig', array(
                'profile' => $profile,
                'is_following' => true
            ))
        ));
    }

    /**
     * @Route("/user/{username}/edit", name="profile_index_edit")
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     * @param Profile $profile
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Profile $profile, User $user, Request $request, $username)
    {
//         $post = $this->getDoctrine()->getRepository('AppBundle:Profile')->findOneBy(array());

        if (!$profile->isOwner($this->getUser())) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('AppBundle\Form\Type\ProfileEditType', $profile);
//        $deleteForm = $this->createDeleteForm($post);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($profile);
            $entityManager->flush();

            $this->addFlash('success', 'profile.updated_successfully');

            return $this->redirectToRoute('profile_index', array(
                'username' => $username,
            ));
        }

        return $this->render('@App/User/profile_edit.html.twig', array(
            'profile'        => $profile,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Like button
     *
     * @param Post $post
     * @param Request $request
     * @return Response
     * @Route("/user/{username}/like", name="app_post_like", condition="request.isXmlHttpRequest()")
     * @Method("POST");
     */
    public function like(Post $post, Request $request)
    {
//        $this->handleInactiveProfiles($profile);

        $user = $this->getLoggedUser();

        $likePostManager = $this->get('app.manager.user_connection_manager');

        $type = $_POST['type'];

        if($likePostManager->like($user, $post->getId())) {
            return new JsonResponse(array(
                'success' => true,
//                'response' => $this->renderView('AppBundle:User:profile.html.twig', array(
                'response' => $this->renderView('AppBundle:User:like_button_'. $type .'.html.twig', array(
                    'post' => $post,
                    'is_liked' => true
                ))
            ));
        }

        return new JsonResponse([
            'success' => false,
//            'response' => $this->renderView('AppBundle:User:profile.html.twig', array(
            'response' => $this->renderView('AppBundle:User:like_button_'.$type.'.html.twig', array(
                'post' => $post,
                'is_liked' => true
            ))
        ]);
    }

    /**
     * Unlike button
     *
     * @param Post $post
     * @param Request $request
     * @return Response
     * @Route("/user/{username}/unlike", name="app_post_unlike", condition="request.isXmlHttpRequest()")
     * @Method("POST");
     */
    public function unlike(Post $post, Request $request)
    {
//        $this->handleInactiveProfiles($profile);

        $user = $this->getLoggedUser();

        $likePostManager = $this->get('app.manager.user_connection_manager');

        $type = $_POST['type'];

        if($likePostManager->unlike($user, $post->getId())) {
            return new JsonResponse(array(
                'success' => true,
                'response' => $this->renderView('AppBundle:User:follow_button_'. $type .'.html.twig', array(
                    'post' => $post,
                    'is_liked' => false
                ))
            ));
        }

        return new JsonResponse(array(
            'success' => false,
            'response' => $this->renderView('AppBundle:User:follow_button_'. $type .'.html.twig', array(
                'post' => $post,
                'is_liked' => true
            ))
        ));
    }





}
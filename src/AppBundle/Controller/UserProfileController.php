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
//use Doctrine\ORM\Mapping\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sluggable\Fixture\Issue1058\Page;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Controller\BaseController;
use AppBundle\Manager\UserConnectionManager;
use AppBundle\Repository\PostRepository;
use AppBundle\Entity\UserRepository;


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


//var_dump($followers);die();

        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(
            array(
                'user' => $profile->getUser(),
            ),
            array(
                'publishedAt' => 'DESC'
            ));

       return $this->render('AppBundle:User:profile.html.twig', array(
            'profile' => $profile,
            'user' => $user,
            'posts' => $posts,
            'is_following' => $userConnectionManager->isFollowing($loggedUser, $user),
            'is_followed_back' => $userConnectionManager->isFollowing($user, $loggedUser),
//            'is_liked' => $likePostManager->isLiked($user, $likedPost)
        ));
    }

    /**
     * @Route("/users-list/", defaults={"page": 1}, name="photo_users_list")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="photo_users_paginated")
     * @param Request $request
     * @param Page $page
     * @Method("GET")
     * @Cache(smaxage="10")
     * @return Response
     * @internal param Post $post
     *
     */
    public function showUsersAction(Request $request, $page)
    {

        $loggedUser = $this->getLoggedUser();
        function shuffle_assoc($array)
        {
            // Initialize
            $shuffled_array = array();

            // Get array's keys and shuffle them.
            $shuffled_keys = array_keys($array);
            shuffle($shuffled_keys);

            // Create same array, but in shuffled order.
            foreach ( $shuffled_keys AS $shuffled_key )
            {
                $shuffled_array[  $shuffled_key  ] = $array[  $shuffled_key  ];
            }
            // Return
            return $shuffled_array;
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array());
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $profiles = $this->getDoctrine()->getRepository('AppBundle:Profile')->findAll();
        $sprofiles = shuffle_assoc($profiles); //randomize
        $profile = $this->getDoctrine()->getRepository('AppBundle:Profile')->findOneBy(array(
            'id' => $user
        ));
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(array(
            'id' => $user
        ));
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(array(
            'user' => $profile->getUser()
            ));
//        $userConnectionManager = $this->get('app.manager.user_connection_manager');



        return $this->render("@App/UsersList/usersList.html.twig", array(
            'user' => $user,
            'users' => $users,
            'profile' => $profile,
            'profiles' => $sprofiles,
            'post' => $post,
            'posts' => $posts,
//            'is_following' => $userConnectionManager->isFollowing($loggedUser, $users)
        ));
    }

    /**
     * @Route("/users-list-one/", name="photo_users_list_one")
     *
     * @return Response
     */
    public function listUserPostsAction($userId)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userId);

        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(array(
            'user' => $user
        ));
        $profile = $this->getDoctrine()->getRepository('AppBundle:Profile')->findOneBy(array(
            'id' => $user
        ));

        return $this->render('@App/UsersList/single-post-list.html.twig', array(
            'posts' => $posts,
            'profile' => $profile
        ));

    }

    /**
     * @Route("/followee/", name="photo_followee_list")
     *
     * @return Response
     */

    public function showFolloweeAction()
    {
        /** @var User $loggedUser */
        $id = $this->getUser();

        /** @var User $id */

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array(
            'profile' => $id,

        ));

        $userFollowers = $this->getDoctrine()->getRepository('AppBundle:User\UserConnection')->findBy(array(
            'follower' => $user,
        ));

        return $this->render('@App/User/follows.html.twig', array(
            'user' => $id,
            'userFollowers' => $userFollowers

        ));

    }

    /**
     * @Route("/follower/", name="photo_followee_list")
     *
     * @return Response
     */

    public function showFollowerAction()
    {
        /** @var User $id */
        $id = $this->getUser();

        /** @var User $id */

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array(
            'id' => $id
        ));
        $userFollowers = $this->getDoctrine()->getRepository('AppBundle:User\UserConnection')->findBy(array(
            'followee' => $user,
        ));

        return $this->render('@App/User/following.html.twig', array(
            'user' => $user,
            'userFollowers' => $userFollowers,
        ));

    }

    /**
     * @Route("/followee-one/", name="photo_followee_one")
     *
     * @return Response
     */
    public function listUserFolloweeAction($userId)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userId);

        $userFollowers = $this->getDoctrine()->getRepository('AppBundle:User\UserConnection')->findBy(array(
            'user' => $user
        ));
        $profile = $this->getDoctrine()->getRepository('AppBundle:Profile')->findOneBy(array(
            'id' => $user
        ));

        return $this->render('AppBundle::dql-single.html.twig', array(
            'userFollowers' => $userFollowers,
            'profile' => $profile
        ));

    }

    /**
     * @Route("/follower-one/", name="photo_follower_one")
     *
     * @return Response
     */
    public function listUserFollowerAction($userId)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($userId);

        $userFollowers = $this->getDoctrine()->getRepository('AppBundle:User\UserConnection')->findBy(array(
            'user' => $user
        ));
        $profile = $this->getDoctrine()->getRepository('AppBundle:Profile')->findOneBy(array(
            'id' => $user
        ));

        return $this->render('AppBundle::dql-single.html.twig', array(
            'userFollowers' => $userFollowers,
            'profile' => $profile
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
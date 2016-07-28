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
use AppBundle\Manager\UserConnectionManager;
use Doctrine\ORM\Mapping\Id;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserProfileController extends BaseController
{
    /**
     *
     *
     * @Route("/user/{username}", name="profile_index")
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     *
     * @Method("GET")
     *
     * @param Profile $profile
     *
     * @return Response
     */
    public function showAction(Profile $profile, User $user)
    {

        $userConnectionManager = $this->get('app.manager.user_connection_manager');

        $followers = $userConnectionManager->getFollowers($profile->getUser())->getResult();
        $following = $userConnectionManager->getFollowing($profile->getUser())->getResult();

        if (!empty($posts)) {
//            /** @var Post $posts */
//            $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->getLatestForUserQuery();
        }
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(array(
            'user' => $profile->getUser()
        ));

        return $this->render('AppBundle:User:profile.html.twig', array(
            'profile' => $profile,
            'user' => $user,
            'posts' => $posts,
            'followers' => $followers,
            'following' => $following,
            'is_following' => $userConnectionManager->isFollowing($user, $profile->getUser()),
            'is_followed_back' => $userConnectionManager->isFollowing($profile->getUser(), $user)
        ));
    }

    /**
     * @Route("/user/{username}/edit", name="profile_index_edit")
     * @ParamConverter("profile", class="AppBundle\Entity\Profile", options={"mapping" : {"username" : "profileUsername"} } )
     * @param Profile $profile
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Profile $profile, Request $request, $username)
    {
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
        $user = $this->getLoggedUser();

        $userConnectionManager = $this->get('app.manager.user_connection_manager');

        $type = $_POST['type'];

        if($userConnectionManager->follow($user, $profile->getUser())) {
            return new JsonResponse(array(
                'success' => true,
                'response' => $this->renderView('AppBundle:User:follow_button_large.html.twig', array(
                    'profile' => $profile,
                    'is_following' => true
                ))
            ));
        }

        return new JsonResponse([
            'success' => false,
            'response' => $this->renderView('AppBundle:User:follow_button_large.html.twig', array(
                'profile' => $profile,
                'is_following' => false
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
        $user = $this->getLoggedUser();

        $userConnectionManager = $this->get('app.manager.user_connection_manager');

        $type = $_POST['type'];

        if($userConnectionManager->unfollow($user, $profile->getUser())) {
            return new JsonResponse(array(
                'success' => true,
                'response' => $this->renderView('AppBundle:User:follow_button_large.html.twig', array(
                    'profile' => $profile,
                    'is_following' => false
                ))
            ));
        }

        return new JsonResponse(array(
            'success' => false,
            'response' => $this->renderView('AppBundle:User:follow_button_large.html.twig', array(
                'profile' => $profile,
                'is_following' => true
            ))
        ));
    }




}
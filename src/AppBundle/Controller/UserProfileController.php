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
use Doctrine\ORM\Mapping\Id;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserProfileController extends Controller
{
    /**
     *
     * @Route("/user/{firstname}-{lastname}", name="profile_index")
     * @Method("GET")
     * @param Profile $profile
     * @param User $user
     * @return Response
     * @internal param Post $posts
     * @internal param Post $post
     * @return array
     */
    public function showAction(Profile $profile, User $user, Post $post, Post $posts)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findBy(array(
            'user' => $user
        ));

        return $this->render('AppBundle:User:profile.html.twig', array(
            'profile' => $profile,
            'user' => $user,
            'posts' => $posts,
            'post' => $post
        ));
    }

    /**
     * @Route("/user/{firstname}-{lastname}/edit", name="profile_index_edit")
     * @param Profile $profile
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(Profile $profile, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('AppBundle\Form\ProfileType', $profile);
//        $deleteForm = $this->createDeleteForm($post);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $entityManager->flush();

            $this->addFlash('success', 'profile.updated_successfully');

            return $this->redirectToRoute('profile_index');
        }

        return $this->render('@App/User/profile_edit.html.twig', array(
            'profile'        => $profile,
            'profile_edit_form'   => $editForm->createView(),
        ));
    }


}
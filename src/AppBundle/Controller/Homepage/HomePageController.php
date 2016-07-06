<?php

namespace AppBundle\Controller\Homepage;

use AppBundle\Entity\Post;
use AppBundle\Entity\Profile;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomePageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param Profile $profile
     * @internal param User $user
     * @internal param Post $post
     * @internal param Post $posts
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Homepage:index.html.twig');
    }
}

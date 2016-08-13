<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AutocompleteController extends Controller
{
    /**
     * @Route("/user/inbox/new-message/autocomplete")
     *
     * @return Response
     */
    public function indexAction()
    {
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:User')
        ;

        $listUsers = $repository->findBy(
            array(),                      // Critere
            array('id' => 'desc'),        // Tri
            null,                         // Limite
            null                          // Offset
        );


        return $this->render('@App/Message/newThread.html.twig', array(
            'listUsers' => $listUsers,
        ));

    }
}

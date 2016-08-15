<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class searchUserController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("/user/inbox/new-message/ajax/autocomplete/update/data", condition="request.isXmlHttpRequest()")
     */
    public function updateDataAction(Request $request)
    {
        $data = $request->get('input');

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(''
            . 'SELECT u.id, u.username '
            . 'FROM AppBundle:User u '
            . 'WHERE u.username LIKE :data '
            . 'ORDER BY u.username ASC'
        )
            ->setParameter('data', '%' . $data . '%');
        $results = $query->getResult();

        $usernameList = '<ul id="matchList">';
        foreach ($results as $result) {
            $matchStringBold = preg_replace('/('.$data.')/i', '<strong>$1</strong>', $result['username']); // Replace text field input by bold one
            $usernameList .= '<li id="'.$result['username'].'">'.$matchStringBold.'</li>'; // Create the matching list - we put maching name in the ID too
        }
        $usernameList .= '</ul>';

        $response = new JsonResponse();
        $response->setData(array('usernameList' => $usernameList));
        return $response;
    }
//    public function searchUserAction(Request $request, User $username, Response $response)
//    {
//        $q = $request->query->get('q');
//        $results = $this->getDoctrine()->getRepository('AppBundle:User')->findAll($q);
//
//
//        return $this->render('AppBundle:Message:newThread.html.twig', array(
//            'results' => $results
//        ));
//    }
//
//    public function getUserAction($id = null)
//    {
//        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
//
//        return new Response($user->getUsername());
//    }
}

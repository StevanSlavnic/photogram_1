<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Profile;
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
        )->setParameter('data', '%' . $data . '%');

        $results = $query->getResult();

        $usernameList = '<ul id="matchList" class="list-unstyled">';
        foreach ($results as $result) {
            $matchStringBold = preg_replace('/('.$data.')/i', '<strong>$1</strong>', $result['username']); // Replace text field input by bold one
            $usernameList .= '<li id="'.$result['username'].'" class="recipient">'.$matchStringBold.'</li>'; // Create the matching list - we put maching name in the ID too
        }
        $usernameList .= '</ul>';

        $response = new JsonResponse();
        $response->setData(array('usernameList' => $usernameList));
        return $response;
    }
}

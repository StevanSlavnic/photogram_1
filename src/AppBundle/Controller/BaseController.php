<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Profile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class BaseController
 *
 * @package AppBundle\Controller
 */
abstract class BaseController extends Controller
{
    /**
     * Get logged in user
     *
     * @return User
     */
    protected function getLoggedUser()
    {
        $loggedUser = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!is_object($loggedUser) || !$loggedUser instanceof UserInterface) {
            throw new AccessDeniedException('Please login first.');
        }

        return $loggedUser;
    }



}

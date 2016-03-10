<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $user = $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY');

        if ($user === true) {
            return $this->redirect('/overview');
        } else {
            return $this->redirect('/login');
        }


    }
}

<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller {

    /**
    * @Route("/login", name="login")
    */
    public function loginAction(Request $request) {
        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect('/overview');
        } else {
            return $this->render(
                'UserAuth/Login.html.twig',
                array(
                    'error'         => $error,
                    'lastUsername'  => $authenticationUtils->getLastUsername()
                )
            );
        }
    }

    /**
    * @Route("/logout")
    */
    public function logoutAction() {
        $this->container->get('security.authorization_checker')->setToken(null);
        $this->get('request')->getSession()->invalidate();
    }
}

?>

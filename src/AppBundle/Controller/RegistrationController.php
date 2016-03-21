<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\UserType;
use AppBundle\Entity\Member;
use AppBundle\Entity\Store;
use AppBundle\Entity\Cart;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller {
    /**
    * @Route("/register")
    */
    public function registerAction(Request $request) {
        $user = new Member();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                -> encodePassword($user, $user->getPlainPassword());            
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);

            $em->flush();

            return $this->render(
                'UserAuth/RegistrationSuccess.html.twig'
            );
        }

        return $this->render(
            'UserAuth/Registration.html.twig',
            array('form' => $form->createView(), 'errors' => null)
        );
    }
}

?>

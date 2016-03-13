<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use AppBundle\Form\InfoType;
use AppBundle\Form\ChangePasswordType;

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

    /**
    * @Route("/changeinfo")
    */
    public function changeInfoAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $this->getDoctrine()->getRepository("AppBundle:User");
        $oldUserInfo = $userRepository->findOneByid($this->getUser()->getId());

        $user = new User();
        $form = $this->createForm(InfoType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());

            if (password_verify($user->getPlainPassword(), $oldUserInfo->getPassword())) {
                $oldUserInfo->setFirstName($user->getFirstName());
                $oldUserInfo->setLastName($user->getLastName());
                $oldUserInfo->setMobileNumber($user->getMobileNumber());
                $oldUserInfo->setHomeAddress($user->getHomeAddress());
                $oldUserInfo->setEmail($user->getEmail());

                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'notice',
                    "Your details were successfully updated!"
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    "Confirmation password mismatch, please enter current password correctly!"
                );
            }

            return $this->render(
                'UserAuth/EditAccount.html.twig',
                array(
                    'form' => $form->createView(),
                    'errors' => null,
                    'firstname' => $user->getFirstName(),
                    'lastname' => $user->getLastName(),
                    'mobilenumber' => $user->getMobileNumber(),
                    'homeaddress' => $user->getHomeAddress(),
                    'email' => $user->getEmail()
                    )
            );
        }

        $userP = new User();
        $passwordForm = $this->createForm(ChangePasswordType::class, $userP);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($userP, $userP->getPlainPassword());

            if (password_verify($userP->getPassword(), $oldUserInfo->getPassword())) {
                $oldUserInfo->setPassword($password);

                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'notice',
                    "Your password was successfully changed!"
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    "Current password mismatch, please enter current password correctly!"
                );
            }

            return $this->redirect('/changeinfo');
        }


        return $this->render(
            'UserAuth/EditAccount.html.twig',
            array(
                'form' => $form->createView(),
                'passwordForm' => $passwordForm->createView(),
                'errors' => null,
                'firstname' => $oldUserInfo->getFirstName(),
                'lastname' => $oldUserInfo->getLastName(),
                'mobilenumber' => $oldUserInfo->getMobileNumber(),
                'homeaddress' => $oldUserInfo->getHomeAddress(),
                'email' => $oldUserInfo->getEmail()
                )
        );
    }
}

?>

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use AppBundle\Entity\Store;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller {
    /**
    * @Route("/register")
    */
    public function registerAction(Request $request) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                -> encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setStoreId(0);
            $user->setMemberType("ROLE_MEMBER");

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            $store = new Store();
            $store->setStoreName("Tindahan ni " . $user->getFirstName());
            $store->setIsStoreStatusOpen(false);
            $store->setVendorId($user->getId());
            $store->setStoreDescription("Please, configure your store name and description by clicking on EDIT STORE NAME!");
            $store->setStoreImage('storeDefault.jpeg');

            $em->persist($store);
            $em->flush();

            $user->setStoreId($store->getStoreId());

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

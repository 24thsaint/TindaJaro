<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;
use AppBundle\Entity\Store;
use AppBundle\Entity\Member;

class DeliveryGuyController extends Controller {

    /**
    * @Route("/shipments/deliver/{customer}")
    */
    public function deliveredAction(Member $customer) {
        $em = $this->getDoctrine()->getManager();

        $this->getUser()->deliverOrders($customer);

        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'All Orders have been marked as successfully shipped, thank you!'
        );

        return $this->redirect('/shipments/view');
    }

}

?>

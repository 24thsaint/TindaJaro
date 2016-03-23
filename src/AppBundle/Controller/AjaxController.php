<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Message;
use AppBundle\Entity\Rating;
use AppBundle\Entity\Store;
use AppBundle\Entity\Product;

class AjaxController extends Controller {

    /**
    * @Route("/product/view/update/{product}")
    */
    public function updateDetailAction(Product $product) {

        return $this->render(
            'Includables/ProductDetails.html.twig',
            array(
                'product' => $product
            )
        );
    }

    /**
    * @Route("/browse/stores/update/{store}")
    */
    public function updateProductsAction(Store $store) {
        $productRepository = $this->getDoctrine()->getRepository("AppBundle:Product");
        $products = $productRepository->findAllActiveProductsInASpecificStore($store);

        return $this->render(
            'Includables/ProductList.html.twig',
            array(
                'products' => $products
            )
        );
    }

    /**
    * @Route("/chatMessages")
    */
    public function getChatMessagesAction() {
        $repository = $this->getDoctrine()->getRepository("AppBundle:Message");
        $messages = $repository->get50LatestMessages();
        return $this->render('Includables/ChatMessage.html.twig', array('messages' => $messages));
    }

    /**
    * @Route("/submitMessage/{content}")
    */
    public function sendChatMessage($content) {
        if ($content=='') {
            return false;
        }
        $message = new Message($this->getUser(), $content);
        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        return $this->getChatMessagesAction();
    }

    /**
    * @Route("/notifications")
    */
    public function notificationsAction() {
        $notifications = array();

        $notification = array();
        $notification['type'] = "REJECTED";
        $data = $this->getUser()->getRejectedOrderCount();
        $notification['count'] = $data;
        if ($data > 0 && $data != null) {
            $notifications[] = $notification;
        }

        $notification = array();
        $notification['type'] = "DELIVERED";
        $data = $this->getUser()->getStore()->getAllDeliveredOrders();
        $notification['count'] = count($data);
        if ($data > 0 && $data != null) {
            $notifications[] = $notification;
        }

        $notification = array();
        $notification['type'] = "CHECKED-OUT";
        $data = $this->getUser()->getStore()->getAllCheckedOutOrders();
        $notification['count'] = count($data);
        if ($data > 0 && $data != null) {
            $notifications[] = $notification;
        }

        $notification = array();
        $notification['type'] = "IN-CART";
        if (null !== $this->getUser()->getCart()) {
            $data = $this->getUser()->getCart()->getOrderCount();
        }
        $notification['count'] = count($data);
        if ($data > 0 && $data != null) {
            $notifications[] = $notification;
        }

        return $this->render('Includables/Notifications.html.twig', array('notifications' => $notifications));
    }
}

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Message;
use AppBundle\Entity\Rating;

class AjaxController extends Controller {

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

    private function notificationCounter($query, $type) {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery($query)->setParameter("user", $type);

        $result = $query->getResult();
        $resultCount = count($result);

        return $resultCount;
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

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
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
        '
        SELECT m FROM AppBundle:Message m ORDER BY m.messageDate DESC
        '
        )->setMaxResults(50);
        $messages = $query->getResult();

        $response = "";

        foreach ($messages as $message) {
            $response .= "<div style=\"background-color: rgb(".$message->getMessageColor().")\">";
            $response .= "<span class=\"pull-right\">(<i>" . date_format($message->getMessageDate(), "Y-m-d H:i:s") . "</i>)</span>";
            $response .= "<br />";
            $response .= "<b>" . $message->getMessageSender() . "</b>";
            $response .= " : ";
            $response .= $message->getMessageContent();
            $response .= "</div>";
        }

        return new Response($response);
    }

    protected function getRandomColor() {
        $r = rand(155, 255);
        $g = rand(155, 255);
        $b = rand(155, 255);
        $rgb = $r . "," . $g . "," . $b;
        return $rgb;
    }

    /**
    * @Route("/submitMessage/{content}")
    */
    public function sendChatMessage($content) {

        $message = new Message();
        $now = new \DateTime();
        $message->setMessageDate($now);
        $message->setMessageSender($this->getUser()->getFirstName() . ' ' . $this->getUser()->getLastName());
        $message->setMessageContent($content);
        $message->setMessageColor($this->getRandomColor());
        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        return $this->getChatMessagesAction();
    }

    private function notificationCounter($query) {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery($query)->setParameter("userId", $this->getUser()->getId());

        $result = $query->getResult();
        $resultCount = count($result);

        return $resultCount;
    }

    /**
    * @Route("/notifications")
    */
    public function overviewNotificationsAction() {
        $response = "";

        $rejectedQuery = "SELECT o FROM AppBundle:Order o WHERE o.customerId=:userId AND o.orderStatus='REJECTED'";
        $rejectedOrderCount = $this->notificationCounter($rejectedQuery);

        if ($rejectedOrderCount > 0) {
            $response .= "<div class=\"alert alert-danger\">You have ". $rejectedOrderCount ." rejected orders! <a href=\"/transaction-history\">See Transaction History</a></div>";
        }

        $deliveredQuery = "SELECT o FROM AppBundle:Order o WHERE o.vendorId=:userId AND o.orderStatus='DELIVERED'";
        $deliveredQueryCount = $this->notificationCounter($deliveredQuery);

        if ($deliveredQueryCount > 0) {
            $response .= "<div class=\"alert alert-success\">". $deliveredQueryCount ." products were sucessfully shipped! <a href=\"/shipments/view\">View Shipped Orders</a></div>";
        }

        $pendingQuery = "SELECT o FROM AppBundle:Order o WHERE o.vendorId=:userId AND o.orderStatus='CHECKED-OUT'";
        $pendingQueryCount = $this->notificationCounter($pendingQuery);

        if ($pendingQueryCount > 0) {
            $response .= "<div class=\"alert alert-warning\">You have ". $pendingQueryCount ." pending product shipments! <a href=\"/vendor/view-pending\">View Pending Orders</a></div>";
        }

        $cartQuery = "SELECT o FROM AppBundle:Order o WHERE o.customerId=:userId AND o.orderStatus='IN-CART'";
        $cartQueryCount = $this->notificationCounter($cartQuery);

        if ($cartQueryCount > 0) {
            $response .= "<div class=\"alert alert-warning\">You have ". $cartQueryCount ." reserved products in cart! <a href=\"/cart\">View Cart</a></div>";
        }

        return new Response($response);
    }
}

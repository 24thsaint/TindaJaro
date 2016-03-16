<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Order;
use Symfony\Component\Validator\Constraints\Date;

class CheckoutController extends Controller
{
    /**
    * @Route("/cart")
    */
    public function orderAction() {
        $orderRepository = $this->getDoctrine()->getRepository("AppBundle:Order");
        $order = $orderRepository->findBycustomerId($this->getUser()->getId());

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            '
            SELECT c FROM AppBundle:Order c WHERE c.customerId=:customerId AND c.orderStatus=\'IN-CART\'
            '
        )->setParameter('customerId', $this->getUser()->getId());

        $checkoutEnabled = "disabled";

        $order = $query->getResult();

        if ($order != null) {
            $checkoutEnabled = "";
        }

        $productRepository = $this->getDoctrine()->getRepository("AppBundle:Product");
        $orderItems = array();
        $total = 0;

        foreach ($order as $orderData) {
            $product = $productRepository->findOneByproductId($orderData->getProductId());
            $productName = $product->getProductName();
            $productDescription = $product->getProductDescription();
            $productPrice = $product->getProductPrice();
            $productId = $product->getProductId();
            $quantity = $orderData->getQuantity();

            $arrayData = array();
            $arrayData['productName'] = $productName;
            $arrayData['productDescription'] = $productDescription;
            $arrayData['productPrice'] = $productPrice;
            $arrayData['productQuantity'] = $quantity;
            $arrayData['productId'] = $productId;
            $orderItems[] = $arrayData;
            $total += $productPrice * $quantity;
        }

        return $this->render(
            'Homepage/Cart.html.twig',
            array(
                'orderItems' => $orderItems,
                'total' => $total,
                'checkoutEnabled' => $checkoutEnabled
            )
        );
    }

    /**
    * @Route("/cart/remove/{productId}")
    */
    public function removeOrderAction($productId) {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            '
            SELECT c FROM AppBundle:Order c WHERE c.customerId = :customerId AND c.productId = :productId AND c.orderStatus = \'IN-CART\'
            '
        )->setParameter('customerId', $userId)->setParameter('productId', $productId);

        $cart = $query->getSingleResult();

        $productRepository = $this->getDoctrine()->getRepository("AppBundle:Product");
        $product = $productRepository->findOneByproductId($productId);
        $product->setProductQuantity($product->getProductQuantity() + $cart->getQuantity());
        $em->remove($cart);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Cart has been updated!'
        );

        return $this->redirect('/cart');
    }

    /**
    * @Route("/cart/add/{vendorId}/{productId}")
    */
    public function addOrderAction(Request $request, $productId, $vendorId) {
        $data = $request->request->all();
        $quantity = $data['quantity'];
        $customerId = $this->getUser()->getId();
        $productRepository = $this->getDoctrine()->getRepository("AppBundle:Product");
        $product = $productRepository->findOneByproductId($productId);

        $postQuantity = $product->getProductQuantity() - $quantity;

        if ($postQuantity < 0) {
            $errorMessage = "Sorry, there are only ".$product->getProductQuantity()." stocks left for this product!";

            $this->get('session')->getFlashBag()->add(
                'notice',
                $errorMessage
            );

            return $this->redirect('/product/view/'.$product->getProductId());

            throw new \Exception($errorMessage);
        }

        // persistence and some checks for quantity
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            '
            SELECT c FROM AppBundle:Order c WHERE c.customerId = :customerId AND c.productId = :productId AND c.orderStatus=\'IN-CART\'
            '
        )->setParameter('customerId', $customerId)->setParameter('productId', $productId);

        try {
            $cartItem = $query->getSingleResult();
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } catch(\Doctrine\ORM\NoResultException $e) {
            $order = new Order();
            $order->setCustomerId($customerId);
            $order->setVendorId($vendorId);
            $order->setProductId($productId);
            $order->setQuantity($quantity);
            $order->setDeliveryGuy($vendorId);
            $now = new \DateTime();
            $order->setTransactionDate($now);
            $order->setOrderStatus("IN-CART");
            $em->persist($order);
        }

        $product->setProductQuantity($postQuantity);

        $em->flush();

        return $this->redirect('/cart');
    }

    /**
    * @Route("/cart/checkout")
    */
    public function checkoutAction() {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            '
            SELECT c FROM AppBundle:Order c WHERE c.customerId=:customerId AND c.orderStatus=\'IN-CART\'
            '
        )->setParameter('customerId', $this->getUser()->getId());

        $orders = $query->getResult();

        if ($orders == null) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Unable to checkout, cart is empty!'
            );
            return $this->redirect('/cart');
        }

        foreach ($orders as $order) {
            $order->setOrderStatus("CHECKED-OUT");
            $now = new \DateTime();
            $order->setTransactionDate($now);
            $em->flush();
        }

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Checkout Successful, transactions have been added to your transaction history for easy reference.'
        );

        return $this->redirect("/overview");
    }
}

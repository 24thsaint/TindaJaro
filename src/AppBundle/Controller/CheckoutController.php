<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Order;
use AppBundle\Entity\Product;
use AppBundle\Entity\Member;
use AppBundle\Entity\Cart;
use Symfony\Component\Validator\Constraints\Date;

class CheckoutController extends Controller
{

    /**
    * @Route("/cart")
    */
    public function orderAction() {
        $cart = $this->getUser()->getCart();
        $cartItems = $cart->getOrders();
        $total = $cart->getTotal();
        $unmetRequirements = $cart->getUnmetMinimumPurchaseRequirements();

        $checkoutEnabled = 'disabled';
        if (empty($unmetRequirements)) {
            $checkoutEnabled = '';
        }

        return $this->render(
            'Homepage/Cart.html.twig',
            array(
                'cart' => $cartItems,
                'total' => $total,
                'unmetRequirements' => $unmetRequirements,
                'checkoutEnabled' => $checkoutEnabled
            )
        );
    }

    /**
    * @Route("/cart/remove/{order}")
    */
    public function removeOrderAction(Order $order) {
        $em = $this->getDoctrine()->getManager();
        $this->getUser()->getCart()->returnReservedOrder($order);
        $em->remove($order);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Cart has been updated!'
        );

        return $this->redirect('/cart');
    }

    /**
    * @Route("/cart/add/{vendor}/{product}")
    */
    public function addOrderAction(Request $request, Product $product, Member $vendor) {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $quantity = $data['quantity'];

        if ($quantity <= 0) {
            $errorMessage = "Sorry, the quantity you entered is invalid, please try again!";

            $this->get('session')->getFlashBag()->add(
                'notice',
                $errorMessage
            );
            return $this->redirect('/product/view/'.$product->getId());
            exit();
        }        

        try {
            // if order exists, update the quantity
            $orderRepository = $this->getDoctrine()->getRepository("AppBundle:Order");
            $order = $orderRepository->findOneOrderThatExistsInCart($this->getUser(), $product);
            $order->setQuantity($order->getQuantity() + $quantity);
        } catch(\Doctrine\ORM\NoResultException $e) {
            // if it does not exist, create new order
            $order = new Order();
            $order->setCustomer($this->getUser());
            $order->setProduct($product);
            $order->setQuantity($quantity);
            $order->setDeliveryGuy(null);
            $now = new \DateTime();
            $order->setTransactionDate($now);
            $order->setStatus("IN-CART");
            $em->persist($order);
            $this->getUser()->getCart()->addOrder($order);
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Cart has been updated!'
        );

        return $this->redirect('/cart');
    }

    /**
    * @Route("/cart/checkout")
    */
    public function checkoutAction() {
        $em = $this->getDoctrine()->getManager();
        if (!empty($this->getUser()->getCart()->getUnmetMinimumPurchaseRequirements())) {
            return $this->redirect("/cart");
            exit();
        }
        if (empty($this->getUser()->getCart()->getOrders())) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Unable to checkout, cart is empty!'
            );
            return $this->redirect('/cart');
            exit();
        }
        if ($this->getUser()->getCart()->checkout()) {
            $em->flush();
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                'There was an error during checkout, please try again.'
            );
            return $this->redirect('/cart');
            exit();
        }

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Checkout Successful, transactions have been added to your transaction history for easy reference.'
        );

        return $this->redirect("/overview");
    }
}

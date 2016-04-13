<?php

namespace Tests\AppBundle\Util;

use AppBundle\Entity\Member;
use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\Order;
use AppBundle\Entity\Store;

class UserTest extends \PHPUnit_Framework_TestCase {

    private $user;
    private $vendor;
    private $product;

    public function setUp() {
        $this->user = new Member();
        $this->user->setFirstName('Rave');
        $this->user->setLastName('Arevalo');
        $this->user->setEmail('nironjin@gmail.com');
        $this->user->setUsername('rave');
        $this->user->setPlainPassword('123');
        $this->user->setHomeaddress('161 San Jose Street, Jaro, Iloilo');
        $this->user->setMemberType('MEMBER');
        $cart = new Cart();
        $this->user->setCart($cart);
        $cart->setCustomer($this->user);

        $this->vendor = new Member();
        $this->vendor->setFirstName('Mariel');
        $this->vendor->setLastName('Arevalo');
        $this->vendor->setEmail('mariel@gmail.com');
        $this->vendor->setUsername('mariel');
        $this->vendor->setPlainPassword('123');
        $this->vendor->setHomeaddress('161 San Jose Street, Jaro, Iloilo');
        $this->vendor->setMemberType('MEMBER');
        $this->vendor->createStore();
        $this->vendor->getStore()->setId(1);
        $store = $this->vendor->getStore();

        $product = new Product();
        $product->setName('Fish');
        $product->setPrice(89.99);
        $product->setQuantity('10');
        $product->setDescription('Fresh fish!');
        $product->setStore($store);
        $store->addProduct($product);
        $this->product = $product;
    }

    public function testNameConcatenation() {
        $this->assertEquals('Rave Arevalo', $this->user->getFullName());
    }

    public function testAddToCart() {
        $order = new Order();
        $order->setProduct($this->product);
        $order->setCustomer($this->user);
        $order->setQuantity(3);
        $order->setStatus("IN-CART");

        $this->user->getCart()->addOrder($order);
        $this->assertContains($order, $this->user->getCart()->getOrders());
        return $this->product;
    }

    /**
    * @depends testAddToCart
    */
    public function testProductQuantityUpdateOnAddToCart($product) {
        $this->assertEquals(7, $product->getQuantity());
    }

    public function testCheckout() {
        $order = new Order();
        $order->setProduct($this->product);
        $order->setCustomer($this->user);
        $order->setQuantity(3);
        $order->setStatus("IN-CART");

        $this->user->getCart()->addOrder($order);
        $this->user->getCart()->checkout();

        $this->assertNotContains($order, $this->user->getCart()->getOrders());
        $this->assertContains($order, $this->user->getOrders());
    }

}

?>

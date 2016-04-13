<?php

namespace Tests\AppBundle\Util;

use AppBundle\Entity\Member;
use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\Order;
use AppBundle\Entity\Store;

class StoreTest extends \PHPUnit_Framework_TestCase {
    private $vendor;

    public function setUp() {
        $this->vendor = new Member();
        $this->vendor->setFirstName('Mariel');
        $this->vendor->setLastName('Arevalo');
        $this->vendor->setEmail('mariel@gmail.com');
        $this->vendor->setUsername('mariel');
        $this->vendor->setPlainPassword('123');
        $this->vendor->setHomeaddress('161 San Jose Street, Jaro, Iloilo');
        $this->vendor->setMemberType('MEMBER');
    }

    public function testStoreCreation() {
        $this->vendor->createStore();
        $this->assertEquals("Tindahan ni Mariel", $this->vendor->getStore()->getName());
        return $this->vendor->getStore();
    }

    /**
    * @depends testStoreCreation
    */
    public function testStoreOpen($store) {
        $store->open();
        $this->assertTrue($store->isOpen());
        return $store;
    }

    /**
    * @depends testStoreOpen
    */
    public function testStoreClose($store) {
        $store->close();
        $this->assertFalse($store->isOpen());
    }

    /**
    * @depends testStoreCreation
    */
    public function testAddProductToStore($store) {
        $product = new Product();
        $product->setName('Fish');
        $product->setPrice(89.99);
        $product->setQuantity('10');
        $product->setDescription('Fresh fish!');
        $product->setStore($store);

        $store->addProduct($product);
        $this->assertContains($product, $store->getProducts());
        return $store;
    }

}

?>

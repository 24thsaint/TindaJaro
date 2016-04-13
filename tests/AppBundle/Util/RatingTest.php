<?php

namespace Tests\AppBundle\Util;

use AppBundle\Entity\Member;
use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\Rating;
use AppBundle\Entity\Store;

class RatingTest extends \PHPUnit_Framework_TestCase {

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

        $this->vendor = new Member();
        $this->vendor->setFirstName('Mariel');
        $this->vendor->setLastName('Arevalo');
        $this->vendor->setEmail('mariel@gmail.com');
        $this->vendor->setUsername('mariel');
        $this->vendor->setPlainPassword('123');
        $this->vendor->setHomeaddress('161 San Jose Street, Jaro, Iloilo');
        $this->vendor->setMemberType('MEMBER');
        $this->vendor->createStore();
    }

    public function testRate() {
        $store = $this->vendor->getStore();

        $rating = new Rating();
        $rating->setCustomer($this->user);
        $rating->setStore($store);
        $rating->setRating(4);
        $store->addRating($rating);

        $this->assertEquals(4, $store->getAverageRatings());
    }
}

?>

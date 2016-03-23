<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
* @ORM\Entity
* @ORM\Table(name="app_members")
*/
class Member extends User
{

    /**
    * @ORM\OneToOne(targetEntity="Store", mappedBy="vendor", cascade="ALL", orphanRemoval=true)
    */
    private $store;

    /**
    * @ORM\OneToOne(targetEntity="Cart", mappedBy="customer", cascade="ALL", orphanRemoval=true)
    */
    private $cart;

    /**
    * @ORM\OneToMany(targetEntity="Order", mappedBy="customer", cascade="ALL", orphanRemoval=true)
    */
    private $orders;

    /**
    * @ORM\OneToMany(targetEntity="DeliveryGuy", mappedBy="owner", cascade="ALL", orphanRemoval=true)
    */
    private $deliveryGuys;

    /**
    * @ORM\Column(type="string", nullable=true)
    * @Assert\NotBlank()
    */
    private $chatColor;

    public function generateRandomChatColor() {
        $r = rand(155, 255);
        $g = rand(155, 255);
        $b = rand(155, 255);
        $rgb = $r . "," . $g . "," . $b;
        $this->setChatColor($rgb);
        return $rgb;
    }

    public function getChatColor() {
        return $this->chatColor;
    }

    public function setChatColor($color) {
        $this->chatColor = $color;
        return $this;
    }

    public function __construct()
    {
        $this->generateRandomChatColor();
        $this->setMemberType("ROLE_MEMBER");
        $cart = new Cart();
        $cart->setCustomer($this);
        $this->setCart($cart);        
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->deliveryGuys = new \Doctrine\Common\Collections\ArrayCollection();
    }

    // other properties and methods

    /**
    * Set store
    *
    * @param \AppBundle\Entity\Store $store
    *
    * @return User
    */
    public function setStore(\AppBundle\Entity\Store $store = null)
    {
        $this->store = $store;

        return $this;
    }

    /**
    * Get store
    *
    * @return \AppBundle\Entity\Store
    */
    public function getStore()
    {
        return $this->store;
    }

    public function createStore() {
        $store = new Store();
        $store->setName("Tindahan ni " . $this->getFirstName());
        $store->close();
        $store->setVendor($this);
        $store->setDescription("Please, configure your store name and description by clicking on EDIT STORE NAME!");
        $store->setMinimumPurchasePrice(0);
        $store->setImage('storeDefault.jpeg');
        $this->setStore($store);
    }

    /**
    * Set cart
    *
    * @param \AppBundle\Entity\Cart $cart
    *
    * @return User
    */
    public function setCart(\AppBundle\Entity\Cart $cart = null)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
    * Get cart
    *
    * @return \AppBundle\Entity\Cart
    */
    public function getCart()
    {
        return $this->cart;
    }


    /**
    * Add order
    *
    * @param \AppBundle\Entity\Order $order
    *
    * @return User
    */
    public function addOrder(\AppBundle\Entity\Order $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
    * Remove order
    *
    * @param \AppBundle\Entity\Order $order
    */
    public function removeOrder(\AppBundle\Entity\Order $order)
    {
        $this->orders->removeElement($order);
    }

    /**
    * Get orders
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Add deliveryGuy
     *
     * @param \AppBundle\Entity\DeliveryGuy $deliveryGuy
     *
     * @return User
     */
    public function addDeliveryGuy(\AppBundle\Entity\DeliveryGuy $deliveryGuy)
    {
        $this->deliveryGuys[] = $deliveryGuy;

        return $this;
    }

    /**
     * Remove deliveryGuy
     *
     * @param \AppBundle\Entity\DeliveryGuy $deliveryGuy
     */
    public function removeDeliveryGuy(\AppBundle\Entity\DeliveryGuy $deliveryGuy)
    {
        $this->deliveryGuys->removeElement($deliveryGuy);
    }

    /**
     * Get deliveryGuys
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeliveryGuys()
    {
        return $this->deliveryGuys;
    }
}

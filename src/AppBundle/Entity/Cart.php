<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
* @ORM\Entity(repositoryClass="AppBundle\Entity\CartRepository")
* @ORM\Table(name="app_cart")
*/
class Cart
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\OneToOne(targetEntity="Member", inversedBy="cart")
    */
    private $customer;

    /**
    * @ORM\ManyToMany(targetEntity="Order")
    * @ORM\JoinTable(name="users_cart",
    *      joinColumns={@ORM\JoinColumn(name="cart_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id", unique=true)}
    *      )
    */
    private $orders;

    public function __construct() {
        $this->orders = new ArrayCollection();
    }

    public function getTotal() {
        $total = 0;
        foreach ($this->getOrders() as $order) {
            $subtotal = $order->getQuantity() * $order->getProduct()->getPrice();
            $total += $subtotal;
        }
        return $total;
    }

    public function getUnmetMinimumPurchaseRequirements() {
        $orders = array();
        $stores = array();

        foreach ($this->getOrders() as $order) {
            if (isset($orders[$order->getStore()->getId()])) {
                $orders[$order->getStore()->getId()] += ($order->getQuantity() * $order->getProduct()->getPrice());
            } else {
                $orders[$order->getStore()->getId()] = ($order->getQuantity() * $order->getProduct()->getPrice());
            }
            $stores[$order->getStore()->getId()] = $order->getStore();
        }

        $unmetRequirements = array();

        foreach ($orders as $key => $value) {
            $store = $stores[$key];
            if ($value < $store->getMinimumPurchasePrice()) {
                $temp = array();
                $temp['store'] = $store;
                $temp['value'] = $value;
                $unmetRequirements[] = $temp;
            }
        }

        return $unmetRequirements;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set customer
     *
     * @param \AppBundle\Entity\Member $customer
     *
     * @return Cart
     */
    public function setCustomer(\AppBundle\Entity\Member $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \AppBundle\Entity\Member
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Add order
     *
     * @param \AppBundle\Entity\Order $order
     *
     * @return Cart
     */
    public function addOrder(\AppBundle\Entity\Order $order)
    {
        $order->getProduct()->setQuantity($order->getProduct()->getQuantity() - $order->getQuantity());
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

    public function returnReservedOrder(\AppBundle\Entity\Order $order) {
        $quantity = $order->getQuantity();
        $order->getProduct()->setQuantity($order->getProduct()->getQuantity() + $quantity);
        $this->removeOrder($order);
    }

    public function checkout() {
        if ($this->getOrders() == null || !empty($this->getUnmetMinimumPurchaseRequirements())) {
            return false;
        }

        foreach ($this->getOrders() as $order) {
            $order->setStatus("CHECKED-OUT");
            $now = new \DateTime();
            $order->setTransactionDate($now);
            $this->removeOrder($order);
            $this->getCustomer()->addOrder($order);

            $store = $order->getStore();
            $store->addCustomer($order->getCustomer());
        }
        return $this;
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

    public function getOrderCount() {
        $count = 0;
        foreach ($this->getOrders() as $order) {
            $count++;
        }
        return $count;
    }

}

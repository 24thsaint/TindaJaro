<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
* @ORM\Entity
* @ORM\Table(name="app_orders")
*/
class Order
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $orderId;

    /**
    * @ORM\Column(type="integer")
    * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
    */
    private $customerId;

    /**
    * @ORM\Column(type="integer")
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product")
    */
    private $productId;

    /**
    * @ORM\Column(type="integer")
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
    */
    private $vendorId;

    /**
    * @ORM\Column(type="integer")
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
    */
    private $deliveryGuy;

    /**
    * @ORM\Column(type="integer")
    * @Assert\NotBlank()
    * @Assert\GreaterThan(value=0)
    */
    private $quantity;

    /**
    * @ORM\Column(type="datetime")
    * @Assert\NotBlank()
    */
    private $transactionDate;

    /**
    * @ORM\Column(type="string")
    * @Assert\NotBlank()
    */
    private $orderStatus;

    /**
     * Get orderId
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set customerId
     *
     * @param integer $customerId
     *
     * @return Order
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set productId
     *
     * @param integer $productId
     *
     * @return Order
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Order
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set transactionDate
     *
     * @param \DateTime $transactionDate
     *
     * @return Order
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;

        return $this;
    }

    /**
     * Get transactionDate
     *
     * @return \DateTime
     */
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }

    /**
     * Set vendorId
     *
     * @param integer $vendorId
     *
     * @return Order
     */
    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;

        return $this;
    }

    /**
     * Get vendorId
     *
     * @return integer
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     * Set orderStatus
     *
     * @param string $orderStatus
     *
     * @return Order
     */
    public function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    /**
     * Get orderStatus
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * Set deliveryGuy
     *
     * @param integer $deliveryGuy
     *
     * @return Order
     */
    public function setDeliveryGuy($deliveryGuy)
    {
        $this->deliveryGuy = $deliveryGuy;

        return $this;
    }

    /**
     * Get deliveryGuy
     *
     * @return integer
     */
    public function getDeliveryGuy()
    {
        return $this->deliveryGuy;
    }
}

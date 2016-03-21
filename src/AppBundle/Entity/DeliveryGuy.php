<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Member\MemberInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_delivery_guys")
 */
class DeliveryGuy extends Member
{

    /**
    * @ORM\ManyToOne(targetEntity="member", inversedBy="deliveryGuys")
    */
    private $owner;

    public function __construct(\AppBundle\Entity\Member $owner) {
        $this->setMemberType("ROLE_DELIVERY_GUY");
        $this->setOwner($owner);
    }

    public function deliverOrders(Member $customer) {

        $orders = $this->getStore()->getAllShippingOrdersOfCustomer($customer);

        foreach ($orders as $order) {
            $now = new \DateTime();
            $order->setTransactionDate($now);
            $order->setStatus('DELIVERED');
            $order->setDeliveryGuy($this);
        }
    }

    public function getStore() {
        return $this->getOwner()->getStore();
    }

    /**
     * Set owner
     *
     * @param \AppBundle\Entity\member $owner
     *
     * @return DeliveryGuy
     */
    public function setOwner(\AppBundle\Entity\Member $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \AppBundle\Entity\member
     */
    public function getOwner()
    {
        return $this->owner;
    }
}

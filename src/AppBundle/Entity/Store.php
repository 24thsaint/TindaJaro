<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Member;
use AppBundle\Entity\GroupHelper;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\StoreRepository")
 * @ORM\Table(name="app_stores")
 */
class Store
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Product", cascade="ALL", orphanRemoval=true)
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Member", inversedBy="store", cascade="ALL", orphanRemoval=true)
     */
    private $vendor;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
    * @ORM\Column(type="string")
    */
    private $description;

    /**
    * @ORM\Column(type="boolean")
    */
    private $isOpen;

    /**
    * @ORM\Column(type="string", length=255)
    */
    private $image;

    /**
    * @ORM\Column(type="decimal", precision=10, scale=2)
    * @Assert\GreaterThanOrEqual(value=0)
    */
    private $minimumPurchasePrice;

    /**
    * @ORM\OneToMany(targetEntity="Product", mappedBy="store", cascade="ALL", orphanRemoval=true)
    */
    private $products;

    /**
    * @ORM\OneToMany(targetEntity="Rating", mappedBy="store", cascade="ALL", orphanRemoval=true)
    */
    private $ratings;

    /**
    * @ORM\ManyToMany(targetEntity="Member")
    * @ORM\JoinTable(name="store_users",
    *      joinColumns={@ORM\JoinColumn(name="store_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="customer_id", referencedColumnName="id", unique=true)}
    *      )
    */
    private $customers;

    public function __construct() {
       $this->products = new ArrayCollection();
       $this->ratings = new ArrayCollection();
       $this->customers = new ArrayCollection();
    }

    public function getAllOrdersOfCustomerByStatus($customer, $status) {
        $orders = array();
        foreach ($customer->getOrders() as $order) {
            if ($status == null) {
                if ($order->getStore()->getId() == $this->getId()) {
                    $orders[] = $order;
                }
            } else {
                if ($order->getStore()->getId() == $this->getId() && $order->getStatus() == $status) {
                    $orders[] = $order;
                }
            }

        }
        return $orders;
    }

    public function getAllShippingOrdersOfCustomer($customer) {
        return $this->getAllOrdersOfCustomerByStatus($customer, 'ACCEPTED BY VENDOR: Being Shipped');
    }

    public function getAllCheckedOutOrders() {
        return $this->getAllOrdersByStatusGroupedByCustomers('CHECKED-OUT', true, false);
    }

    public function getAllShippedOrders() {
        return $this->getAllOrdersByStatusGroupedByCustomers('ACCEPTED BY VENDOR: Being Shipped', true, true);
    }

    public function getAllDeliveredOrders() {
        return $this->getAllOrdersByStatusGroupedByCustomers('DELIVERED', true, false);
    }

    public function getAllOrders() {
        return $this->getAllOrdersByStatusGroupedByCustomers(null, false, true);
    }

    public function getAllOrdersByStatusGroupedByCustomers($status, $removeEmptyRecords, $extendRecords) {
        $customers = array();
        foreach ($this->getCustomers() as $customer) {
            $orders = array();
            $totalCost = 0;
            if ($extendRecords) {
                $lastOrderStatus = null;
                $lastTransactionDate = null;
                $lastDeliveryGuy = null;
            }
            foreach ($this->getAllOrdersOfCustomerByStatus($customer, $status) as $order) {
                $orders[] = $order;
                $totalCost += $order->getQuantity() * $order->getProduct()->getPrice();
                if ($extendRecords) {
                    $lastOrderStatus = $order->getStatus();
                    $lastTransactionDate = $order->getTransactionDate();
                    $lastDeliveryGuy = $order->getDeliveryGuy();
                }
            }
            if (empty($orders) && $removeEmptyRecords) {
                continue;
            }
            $data = array();
            $data['customer'] = $customer;
            $data['orders'] = array_reverse($orders);
            $data['total'] = $totalCost;
            if ($extendRecords) {
                $data['lastOrderStatus'] = $lastOrderStatus;
                $data['lastTransactionDate'] = date_format($lastTransactionDate, DATE_RFC850);
                $data['lastDeliveryGuy'] = $lastDeliveryGuy;
            }
            $customers[] = $data;
        }
        return $customers;
    }

    public function shipPendingOrders($customer) {
        foreach ($this->getAllOrdersOfCustomerByStatus($customer, 'CHECKED-OUT') as $order) {
            $order->setStatus("ACCEPTED BY VENDOR: Being Shipped");
            $now = new \DateTime();
            $order->setTransactionDate($now);
        }
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
     * Set vendor
     *
     * @param string $vendor
     *
     * @return Store
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Store
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Store
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isOpen
     *
     * @param boolean $isOpen
     *
     * @return Store
     */
    public function setOpen($isOpen)
    {
        $this->isOpen = $isOpen;

        return $this;
    }

    /**
     * Get isOpen
     *
     * @return boolean
     */
    public function isOpen()
    {
        return $this->isOpen;
    }

    public function open() {
        $this->setOpen(true);
    }

    public function close() {
        $this->setOpen(false);
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Store
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function overwriteImage($directory, $file) {
        $fileName = md5(uniqid()).'.'.$file->guessExtension(); // we do not want to save the same filenames -_-
        if ($this->getImage() == 'storeDefault.jpeg') {
            // omg do not delete the default image, other new stores need it.
        } else {
            $this->deleteFile($directory.'/'.$this->getImage());
        }
        $this->setImage($fileName);
        $file->move($directory, $fileName);

        return $this;
    }

    protected function deleteFile($filePath) {
        $fs = new Filesystem();
        if ($fs->exists($filePath)) {
            $fs->remove($filePath);
        }
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set minimumPurchasePrice
     *
     * @param string $minimumPurchasePrice
     *
     * @return Store
     */
    public function setMinimumPurchasePrice($minimumPurchasePrice)
    {
        $this->minimumPurchasePrice = $minimumPurchasePrice;

        return $this;
    }

    /**
     * Get minimumPurchasePrice
     *
     * @return string
     */
    public function getMinimumPurchasePrice()
    {
        return $this->minimumPurchasePrice;
    }

    /**
     * Add product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return Store
     */
    public function addProduct(\AppBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \AppBundle\Entity\Product $product
     */
    public function removeProduct(\AppBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function getActiveProducts() {

    }

    /**
     * Add rating
     *
     * @param \AppBundle\Entity\Rating $rating
     *
     * @return Store
     */
    public function addRating(\AppBundle\Entity\Rating $rating)
    {
        $this->ratings[] = $rating;

        return $this;
    }

    /**
     * Remove rating
     *
     * @param \AppBundle\Entity\Rating $rating
     */
    public function removeRating(\AppBundle\Entity\Rating $rating)
    {
        $this->ratings->removeElement($rating);
    }

    /**
     * Get ratings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRatings()
    {
        $this->ratings;
    }

    public function getAverageRatings() {
        $count = 0;
        $total = 0;

        foreach ($this->ratings as $rating) {
            $total += $rating->getRating();
            $count++;
        }

        if ($count == 0) {
            return 0;
        }

        return ($total / $count);
    }

    /**
     * Add customer
     *
     * @param \AppBundle\Entity\Member $customer
     *
     * @return Store
     */
    public function addCustomer(\AppBundle\Entity\Member $customer)
    {
        foreach ($this->getCustomers() as $_customer) {
            if ($customer == $_customer) {
                return $this;
            }
        }
        $this->customers[] = $customer;

        return $this;
    }

    /**
     * Remove customer
     *
     * @param \AppBundle\Entity\Member $customer
     */
    public function removeCustomer(\AppBundle\Entity\Member $customer)
    {
        $this->customers->removeElement($customer);
    }

    /**
     * Get customers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomers()
    {
        return $this->customers;
    }
}

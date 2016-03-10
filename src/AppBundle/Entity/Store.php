<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_stores")
 */
class Store
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", cascade="ALL", orphanRemoval=true)
     */
    private $storeId;

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\OneToOne(targetEntity="/AppBundle/Entity/User", inversedBy="storeId")
     */
    private $vendorId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $storeName;

    /**
    * @ORM\Column(type="string")
    */
    private $storeDescription;

    /**
    * @ORM\Column(type="boolean")
    */
    private $isStoreStatusOpen;

    /**
    * @ORM\Column(type="string", length=255)
    */
    private $storeImage;

    // other properties and methods
    //* @Assert\File(mimeTypes={ "image/jpeg" })
    //* @Assert\File(maxSize="6000000")


    /**
     * Get storeId
     *
     * @return integer
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * Set vendorId
     *
     * @param integer $vendorId
     *
     * @return Store
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
     * Set storeName
     *
     * @param string $storeName
     *
     * @return Store
     */
    public function setStoreName($storeName)
    {
        $this->storeName = $storeName;

        return $this;
    }

    /**
     * Get storeName
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->storeName;
    }

    /**
     * Set isStoreStatusOpen
     *
     * @param boolean $isStoreStatusOpen
     *
     * @return Store
     */
    public function setIsStoreStatusOpen($isStoreStatusOpen)
    {
        $this->isStoreStatusOpen = $isStoreStatusOpen;

        return $this;
    }

    /**
     * Get isStoreStatusOpen
     *
     * @return boolean
     */
    public function getIsStoreStatusOpen()
    {
        return $this->isStoreStatusOpen;
    }

    /**
     * Set storeDescription
     *
     * @param string $storeDescription
     *
     * @return Store
     */
    public function setStoreDescription($storeDescription)
    {
        $this->storeDescription = $storeDescription;

        return $this;
    }

    /**
     * Get storeDescription
     *
     * @return string
     */
    public function getStoreDescription()
    {
        return $this->storeDescription;
    }

    /**
     * Set storeImage
     *
     * @param string $storeImage
     *
     * @return Store
     */
    public function setStoreImage($storeImage)
    {
        $this->storeImage = $storeImage;

        return $this;
    }

    /**
     * Get storeImage
     *
     * @return string
     */
    public function getStoreImage()
    {
        return $this->storeImage;
    }
}

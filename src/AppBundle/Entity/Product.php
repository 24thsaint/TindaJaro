<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
* @ORM\Entity
* @ORM\Table(name="app_products")
*/
class Product
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $productId;

    /**
    * @ORM\Column(type="integer")
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Store")
    */
    private $storeId;

    /**
    * @ORM\Column(type="string", length=255)
    * @Assert\NotBlank(message="This is important, Product Name should not be blank!")
    * @Assert\Length(max = "255", maxMessage="Your description must not exceed 255 characters.")
    */
    private $productName;

    /**
    * @ORM\Column(type="decimal", precision=10, scale=2)
    * @Assert\GreaterThanOrEqual(value=0)
    * @Assert\NotBlank(message="Product Price should not be blank!")
    */
    private $productPrice;

    /**
    * @ORM\Column(type="string", length=255)
    * @Assert\NotBlank(message="Please enter a product description.")
    * @Assert\Length(max = "255", maxMessage="Your description must not exceed 255 characters.")
    */
    private $productDescription;

    /**
    * @ORM\Column(type="integer")
    * @Assert\GreaterThanOrEqual(value=0)
    * @Assert\NotBlank(message="Product Quantity should not be blank!")
    */
    private $productQuantity;

    /**
    * @ORM\Column(type="string", length=255)
    * @Assert\File(mimeTypes={ "image/jpeg" })
    * @Assert\File(maxSize="6000000")
    */
    private $productImage;

    /**
    * @ORM\Column(type="boolean")
    */
    private $isActive;

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
    * Set productName
    *
    * @param string $productName
    *
    * @return Product
    */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
    * Get productName
    *
    * @return string
    */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
    * Set productPrice
    *
    * @param string $productPrice
    *
    * @return Product
    */
    public function setProductPrice($productPrice)
    {
        $this->productPrice = $productPrice;

        return $this;
    }

    /**
    * Get productPrice
    *
    * @return string
    */
    public function getProductPrice()
    {
        return $this->productPrice;
    }

    /**
    * Set productDescription
    *
    * @param string $productDescription
    *
    * @return Product
    */
    public function setProductDescription($productDescription)
    {
        $this->productDescription = $productDescription;

        return $this;
    }

    /**
    * Get productDescription
    *
    * @return string
    */
    public function getProductDescription()
    {
        return $this->productDescription;
    }

    /**
    * Set productQuantity
    *
    * @param integer $productQuantity
    *
    * @return Product
    */
    public function setProductQuantity($productQuantity)
    {
        $this->productQuantity = $productQuantity;

        return $this;
    }

    /**
    * Get productQuantity
    *
    * @return integer
    */
    public function getProductQuantity()
    {
        return $this->productQuantity;
    }

    /**
     * Set productImage
     *
     * @param string $productImage
     *
     * @return Product
     */
    public function setProductImage($productImage)
    {
        $this->productImage = $productImage;

        return $this;
    }

    /**
     * Get productImage
     *
     * @return string
     */
    public function getProductImage()
    {
        return $this->productImage;
    }

    /**
     * Set storeId
     *
     * @param integer $storeId
     *
     * @return Product
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Product
     */
    public function setActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Product
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}

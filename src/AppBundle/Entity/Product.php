<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Filesystem\Filesystem;

/**
* @ORM\Entity(repositoryClass="AppBundle\Entity\ProductRepository")
* @ORM\Table(name="app_products")
*/
class Product
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="Store", inversedBy="products", cascade="ALL")
    */
    private $store;

    /**
    * @ORM\Column(type="string", length=255)
    * @Assert\NotBlank(message="This is important, Product Name should not be blank!")
    * @Assert\Length(max = "255", maxMessage="Your description must not exceed 255 characters.")
    */
    private $name;

    /**
    * @ORM\Column(type="decimal", precision=10, scale=2)
    * @Assert\GreaterThanOrEqual(value=0)
    * @Assert\NotBlank(message="Product Price should not be blank!")
    */
    private $price;

    /**
    * @ORM\Column(type="string", length=255)
    * @Assert\NotBlank(message="Please enter a product description.")
    * @Assert\Length(max = "255", maxMessage="Your description must not exceed 255 characters.")
    */
    private $description;

    /**
    * @ORM\Column(type="integer")
    * @Assert\GreaterThanOrEqual(value=0)
    * @Assert\NotBlank(message="Product Quantity should not be blank!")
    */
    private $quantity;

    /**
    * @ORM\Column(type="string", length=255)
    * @Assert\File(mimeTypes={ "image/jpeg" })
    * @Assert\File(maxSize="6000000")
    */
    private $image;

    /**
    * @ORM\Column(type="string", nullable=true)
    */
    private $imagePath;

    /**
    * @ORM\Column(type="boolean")
    */
    private $isActive;

    public function overwriteImage($directory, $file) {
        $fileName = md5(uniqid()).'.'.$file->guessExtension(); // we do not want to save the same filenames -_-
        if ($this->getImage() == 'productDefault.jpeg') {
            // omg do not delete the default image, other new stores need it.
        } else {
            $this->deleteFile($directory.'/'.$this->getImage());
        }
        $this->setImage($fileName);
        $this->setImagePath($directory);
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
    * Get id
    *
    * @return integer
    */
    public function getId()
    {
        return $this->id;
    }

    /**
    * Set name
    *
    * @param string $name
    *
    * @return Product
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
    * Set price
    *
    * @param string $price
    *
    * @return Product
    */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
    * Get price
    *
    * @return string
    */
    public function getPrice()
    {
        return $this->price;
    }

    /**
    * Set description
    *
    * @param string $description
    *
    * @return Product
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
    * Set quantity
    *
    * @param integer $quantity
    *
    * @return Product
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
     * Set image
     *
     * @param string $image
     *
     * @return Product
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
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

    /**
     * Set store
     *
     * @param \AppBundle\Entity\Store $store
     *
     * @return Product
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

    public function remove($orders) {
        if ($orders == null) {
            $this->setActive(false);

            if ($this->getImage() != 'productDefault.jpeg') {
                $this->deleteFile($this->getImagePath().'/'.$this->getImage());
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Set imagePath
     *
     * @param string $imagePath
     *
     * @return Product
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * Get imagePath
     *
     * @return string
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }
}

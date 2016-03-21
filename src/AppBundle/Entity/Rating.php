<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
* @ORM\Entity(repositoryClass="AppBundle\Entity\RatingRepository")
* @ORM\Table(name="app_ratings")
*/
class Rating
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\OneToOne(targetEntity="Member")
    */
    private $customer;

    /**
    * @ORM\ManyToOne(targetEntity="Store", inversedBy="ratings", cascade="ALL")
    */
    private $store;

    /**
    * @ORM\Column(type="integer")
    * @Assert\GreaterThanOrEqual(value=0)
    * @Assert\LessThanOrEqual(value=5)
    */
    private $rating;


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
     * Set rating
     *
     * @param integer $rating
     *
     * @return Rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set customerId
     *
     * @param \AppBundle\Entity\Member $customerId
     *
     * @return Rating
     */
    public function setCustomerId(\AppBundle\Entity\Member $customerId = null)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return \AppBundle\Entity\Member
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set storeId
     *
     * @param \AppBundle\Entity\Store $storeId
     *
     * @return Rating
     */
    public function setStoreId(\AppBundle\Entity\Store $storeId = null)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     * Get storeId
     *
     * @return \AppBundle\Entity\Store
     */
    public function getStoreId()
    {
        return $this->storeId;
    }
}

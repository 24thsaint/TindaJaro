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
     * Set customer
     *
     * @param \AppBundle\Entity\Member $customer
     *
     * @return Rating
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
     * Set store
     *
     * @param \AppBundle\Entity\Store $store
     *
     * @return Rating
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
}

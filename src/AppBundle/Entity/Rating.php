<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
* @ORM\Entity
* @ORM\Table(name="app_ratings")
*/
class Rating
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $ratingId;

    /**
    * @ORM\Column(type="integer")
    * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
    */
    private $customerId;

    /**
    * @ORM\Column(type="integer")
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Store")
    */
    private $storeId;

    /**
    * @ORM\Column(type="integer")
    * @Assert\GreaterThanOrEqual(value=0)
    * @Assert\LessThanOrEqual(value=5)
    */
    private $rating;

    /**
     * Get ratingId
     *
     * @return integer
     */
    public function getRatingId()
    {
        return $this->ratingId;
    }

    /**
     * Set customerId
     *
     * @param integer $customerId
     *
     * @return Rating
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
     * Set storeId
     *
     * @param integer $storeId
     *
     * @return Rating
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
}

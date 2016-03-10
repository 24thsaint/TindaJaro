<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 * @UniqueEntity(fields="mobilenumber", message="Mobile number is already linked to an account!")
 * @ORM\Table(name="app_users")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="6", max = "32", maxMessage="Password should only contain 6 to 32 characters.", minMessage="Password should only contain 6 to 32 characters.")
     */
    private $plainPassword;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min="10", max = "12", maxMessage="Please enter a valid mobile number.", minMessage="Please enter a valid mobile number.")
     */
    private $mobilenumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $homeaddress;

    /**
    * @ORM\Column(type="integer")
    * @ORM\OneToOne(targetEntity="/AppBundle/Entity/Store", mappedBy="storeId", cascade="ALL", orphanRemoval=true)
    */
    private $storeId;

    /**
    * @ORM\Column(type="string")
    */
    private $memberType;

    // other properties and methods

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRoles() {
        return array($this->memberType);
    }

    public function eraseCredentials() {
        //do nothing yet
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        // The bcrypt algorithm don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    // other methods, including security methods like getRoles()

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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set mobilenumber
     *
     * @param string $mobilenumber
     *
     * @return User
     */
    public function setmobilenumber($mobilenumber)
    {
        $this->mobilenumber = $mobilenumber;

        return $this;
    }

    /**
     * Get mobilenumber
     *
     * @return string
     */
    public function getmobilenumber()
    {
        return $this->mobilenumber;
    }

    /**
     * Set homeaddress
     *
     * @param string $homeaddress
     *
     * @return User
     */
    public function sethomeaddress($homeaddress)
    {
        $this->homeaddress = $homeaddress;

        return $this;
    }

    /**
     * Get homeaddress
     *
     * @return string
     */
    public function gethomeaddress()
    {
        return $this->homeaddress;
    }

    /**
     * Set storeId
     *
     * @param integer $storeId
     *
     * @return User
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
     * Set memberType
     *
     * @param string $memberType
     *
     * @return User
     */
    public function setMemberType($memberType)
    {
        $this->memberType = $memberType;

        return $this;
    }

    /**
     * Get memberType
     *
     * @return string
     */
    public function getMemberType()
    {
        return $this->memberType;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"user" = "User", "member" = "Member", "deliveryGuy" = "DeliveryGuy"})
 * @UniqueEntity(fields="email", message="Email already taken", groups={"registration"})
 * @UniqueEntity(fields="username", message="Username already taken", groups={"registration"})
 * @UniqueEntity(fields="mobilenumber", message="Mobile number is already linked to an account!", groups={"registration"})
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
     * @Assert\NotBlank(groups={"userinfo"})
     * @Assert\Email(groups={"userinfo"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(groups={"registration"})
     */
    private $username;

    /**
     * @Assert\NotBlank(groups={"registration", "passwordinfo"})
     * @Assert\Length(min="6", max = "32", maxMessage="Password should only contain 6 to 32 characters.", minMessage="Password should only contain 6 to 32 characters.", groups={"registration", "passwordinfo"})
     */
    private $plainPassword;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"userinfo"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"userinfo"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(groups={"userinfo"})
     * @Assert\Length(min="10", max = "12", maxMessage="Please enter a valid mobile number.", minMessage="Please enter a valid mobile number.", groups={"userinfo"})
     */
    private $mobilenumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"userinfo"})
     */
    private $homeaddress;

    /**
    * @ORM\Column(type="string")
    */
    private $memberType;

    // other properties and methods

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
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

    public function getRejectedOrderCount() {
        return $this->getOrderCountByStatus("REJECTED");
    }

    protected function getOrderCountByStatus($status) {
        $count = 0;
        foreach ($this->getOrders() as $order) {
            if ($order->getStatus() == $status) {
                $count++;
            }
        }
        return $count;
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
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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

    public function getFullName() {
        return $this->getFirstName() .' '. $this->getLastName();
    }

    /**
     * Set mobilenumber
     *
     * @param string $mobilenumber
     *
     * @return User
     */
    public function setMobilenumber($mobilenumber)
    {
        $this->mobilenumber = $mobilenumber;

        return $this;
    }

    /**
     * Get mobilenumber
     *
     * @return string
     */
    public function getMobilenumber()
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
    public function setHomeaddress($homeaddress)
    {
        $this->homeaddress = $homeaddress;

        return $this;
    }

    /**
     * Get homeaddress
     *
     * @return string
     */
    public function getHomeaddress()
    {
        return $this->homeaddress;
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

    public function setPlainPassword($plainPassword) {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getPlainPassword() {
        return $this->plainPassword;
    }

    /**
     * Set cart
     *
     * @param \AppBundle\Entity\Cart $cart
     *
     * @return User
     */
    public function setCart(\AppBundle\Entity\Cart $cart = null)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Get cart
     *
     * @return \AppBundle\Entity\Cart
     */
    public function getCart()
    {
        return $this->cart;
    }


    /**
     * Add order
     *
     * @param \AppBundle\Entity\Order $order
     *
     * @return User
     */
    public function addOrder(\AppBundle\Entity\Order $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \AppBundle\Entity\Order $order
     */
    public function removeOrder(\AppBundle\Entity\Order $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }
}

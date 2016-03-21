<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
* @ORM\Entity(repositoryClass="AppBundle\Entity\MessageRepository")
* @ORM\Table(name="app_messages")
*/
class Message
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(type="datetime")
    * @Assert\NotBlank()
    */
    private $date;

    /**
    * @ORM\ManyToOne(targetEntity="Member")
    */
    private $sender;

    /**
    * @ORM\Column(type="string", length=255)
    * @Assert\NotBlank(message="Message should not be blank")
    */
    private $content;

    public function __construct($sender, $content) {
        $now = new \DateTime();
        $this->setDate($now);
        $this->setContent($content);
        $this->setSender($sender);
    }

    public function getChatColor() {
        return $this->getSender()->getChatColor();
    }

    public function getFormattedMessageDate() {
        return date_format($this->getDate(), "Y-m-d H:i:s");
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Message
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Message
     */
    public function setContent($content)
    {
        $c = htmlspecialchars($content);
        $c = chop($c);
        $this->content = $c;
        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set sender
     *
     * @param \AppBundle\Entity\Member $sender
     *
     * @return Message
     */
    public function setSender(\AppBundle\Entity\Member $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \AppBundle\Entity\Member
     */
    public function getSender()
    {
        return $this->sender;
    }
}

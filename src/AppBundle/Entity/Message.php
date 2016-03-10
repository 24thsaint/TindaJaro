<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
* @ORM\Entity
* @ORM\Table(name="app_messages")
*/
class Message
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $messageId;

    /**
    * @ORM\Column(type="datetime")
    * @Assert\NotBlank()
    */
    private $messageDate;

    /**
    * @ORM\Column(type="string")
    * @Assert\NotBlank()
    */
    private $messageSender;

    /**
    * @ORM\Column(type="string")
    * @Assert\NotBlank()
    */
    private $messageColor;

    /**
    * @ORM\Column(type="string", length=255)
    * @Assert\NotBlank(message="Message should not be blank")
    */
    private $messageContent;

    /**
     * Get messageId
     *
     * @return integer
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * Set messageDate
     *
     * @param \DateTime $messageDate
     *
     * @return Message
     */
    public function setMessageDate($messageDate)
    {
        $this->messageDate = $messageDate;

        return $this;
    }

    /**
     * Get messageDate
     *
     * @return \DateTime
     */
    public function getMessageDate()
    {
        return $this->messageDate;
    }

    /**
     * Set messageSender
     *
     * @param string $messageSender
     *
     * @return Message
     */
    public function setMessageSender($messageSender)
    {
        $this->messageSender = $messageSender;

        return $this;
    }

    /**
     * Get messageSender
     *
     * @return string
     */
    public function getMessageSender()
    {
        return $this->messageSender;
    }

    /**
     * Set messageContent
     *
     * @param string $messageContent
     *
     * @return Message
     */
    public function setMessageContent($messageContent)
    {
        $this->messageContent = $messageContent;

        return $this;
    }

    /**
     * Get messageContent
     *
     * @return string
     */
    public function getMessageContent()
    {
        return $this->messageContent;
    }

    /**
     * Set messageColor
     *
     * @param string $messageColor
     *
     * @return Message
     */
    public function setMessageColor($messageColor)
    {
        $this->messageColor = $messageColor;

        return $this;
    }

    /**
     * Get messageColor
     *
     * @return string
     */
    public function getMessageColor()
    {
        return $this->messageColor;
    }

    /**
     * Set messageContents
     *
     * @param string $messageContents
     *
     * @return Message
     */
    public function setMessageContents($messageContents)
    {
        $this->messageContents = $messageContents;

        return $this;
    }

    /**
     * Get messageContents
     *
     * @return string
     */
    public function getMessageContents()
    {
        return $this->messageContents;
    }
}

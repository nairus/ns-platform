<?php

namespace Com\Nairus\CoreBundle\Entity;

use Com\Nairus\CoreBundle\Entity\IpTraceable;
use Com\Nairus\CoreBundle\Validator\Constraints as NSAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContactMessage entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Table(name="ns_contact_message")
 * @ORM\Entity(repositoryClass="Com\Nairus\CoreBundle\Repository\ContactMessageRepository")
 * @NSAssert\Antiflood(seconds = 86400)
 */
class ContactMessage implements IpTraceable {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150)
     * @Assert\NotBlank()
     * @Assert\Length(max = 150)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=50, nullable=true)
     * @Assert\Length(max = 50)
     * @Assert\Regex("/^(\+\d+\s)?\d+[\d\s]+?\d+$/")
     * @Assert\Expression("this.getPhone() || this.getEmail()",
     *                    message="ns_core.form.contact.errors.phone")
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     * @Assert\Length(max = 100)
     * @Assert\Email(strict = true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min = 50)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=39)
     */
    private $ip;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="dateRequest", type="datetime")
     */
    private $requestDate;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return ContactMessage
     */
    public function setName(string $name): ContactMessage {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     *
     * @return ContactMessage
     */
    public function setPhone(string $phone = null): ContactMessage {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string|null
     */
    public function getPhone(): ?string {
        return $this->phone;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return ContactMessage
     */
    public function setEmail(string $email = null): ContactMessage {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * Define the contact message.
     *
     * @param string $message The contact message.
     *
     * @return ContactMessage
     */
    public function setMessage(string $message): ContactMessage {
        $this->message = $message;
        return $this;
    }

    /**
     * Return the contact message.
     *
     * @return string|null
     */
    public function getMessage(): ?string {
        return $this->message;
    }

    /**
     * Set ip.
     *
     * @param string $ip
     *
     * @return ContactMessage
     */
    public function setIp(string $ip): ContactMessage {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip.
     *
     * @return string
     */
    public function getIp(): string {
        return $this->ip;
    }

    /**
     * Set the request date.
     *
     * @param \DateTimeInterface $requestDate
     *
     * @return ContactMessage
     */
    public function setRequestDate(\DateTimeInterface $requestDate): ContactMessage {
        $this->requestDate = $requestDate;

        return $this;
    }

    /**
     * Get the request date.
     *
     * @return \DateTimeInterface
     */
    public function getRequestDate(): \DateTimeInterface {
        return new \DateTimeImmutable($this->requestDate->format("Y-m-d H:i:s"));
    }

}

<?php

namespace Com\Nairus\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlacklistedIp entity.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Table(name="ns_blacklisted_ip")
 * @ORM\Entity(repositoryClass="Com\Nairus\CoreBundle\Repository\BlacklistedIpRepository")
 */
class BlacklistedIp {

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
     * @ORM\Column(name="ip", type="string", length=39, unique=true)
     */
    private $ip;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="blacklistedAt", type="datetime")
     */
    private $blacklistedAt;

    /**
     * The constructor.
     */
    public function __construct() {
        $this->blacklistedAt = new \DateTimeImmutable();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Set ip.
     *
     * @param string $ip
     *
     * @return BlacklistedIp
     */
    public function setIp(string $ip): BlacklistedIp {
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
     * Set blacklistedAt.
     *
     * @param \DateTimeInterface $blacklistedAt
     *
     * @return BlacklistedIp
     */
    public function setBlacklistedAt(\DateTimeInterface $blacklistedAt): BlacklistedIp {
        $this->blacklistedAt = $blacklistedAt;

        return $this;
    }

    /**
     * Get blacklistedAt.
     *
     * @return \DateTimeInterface
     */
    public function getBlacklistedAt(): \DateTimeInterface {
        return new \DateTimeImmutable($this->blacklistedAt->format("Y-m-d H:i:s"));
    }

}

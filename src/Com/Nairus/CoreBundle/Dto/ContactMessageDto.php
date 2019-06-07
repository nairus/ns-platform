<?php

namespace Com\Nairus\CoreBundle\Dto;

/**
 * ContactMessageDto paginated for views.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ContactMessageDto {

    use Traits\PaginatorTrait;

    /**
     * The collection of blacklisted ip.
     *
     * Key: unique ip / value: array <int> (contact message entity).
     * Example of map:
     * [
     *   "127.0.0.1" => `blacklisted date`,
     *   "127.0.0.2" => `blacklisted date`,
     * ]
     *
     * @var array
     */
    private $blacklistedIps;

    /**
     * Return the collection of blacklisted ips.
     *
     * @return array
     */
    public function getBlacklistedIps(): array {
        return $this->blacklistedIps;
    }

    /**
     * Return <code>true</code> if the ip is blacklisted.
     *
     * @param string $ip The ip to find.
     *
     * @return bool
     */
    public function isBlacklisted(string $ip): bool {
        return array_key_exists($ip, $this->blacklistedIps);
    }

    /**
     * Define the blacklited ips collection.
     *
     * @param array $blacklistedIps The blacklisted ips collection.
     *
     * @return ContactMessageDto
     */
    public function setBlacklistedIps(array $blacklistedIps): ContactMessageDto {
        $this->blacklistedIps = $blacklistedIps;
        return $this;
    }

}

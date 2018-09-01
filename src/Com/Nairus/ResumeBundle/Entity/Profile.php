<?php

namespace Com\Nairus\ResumeBundle\Entity;

use Com\Nairus\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @ORM\Table(name="ns_profile")
 * @ORM\Entity(repositoryClass="Com\Nairus\ResumeBundle\Repository\ProfileRepository")
 */
class Profile {

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
     * @ORM\Column(name="firstName", type="string", length=100)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=100)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=50)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="cell", type="string", length=50)
     */
    private $cell;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text")
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="addressAddition", type="text")
     */
    private $addressAddition;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=100)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=50)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=100)
     */
    private $country;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="Com\Nairus\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Profile
     */
    public function setPhone(string $phone): Profile {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone(): string {
        return $this->phone;
    }

    /**
     * Set cell
     *
     * @param string $cell
     *
     * @return Profile
     */
    public function setCell(string $cell): Profile {
        $this->cell = $cell;

        return $this;
    }

    /**
     * Get cell
     *
     * @return string
     */
    public function getCell(): string {
        return $this->cell;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Profile
     */
    public function setAddress(string $address): Profile {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress(): string {
        return $this->address;
    }

    /**
     * Set addressAddition
     *
     * @param string $addressAddition
     *
     * @return Profile
     */
    public function setAddressAddition(string $addressAddition): Profile {
        $this->addressAddition = $addressAddition;

        return $this;
    }

    /**
     * Get addressAddition
     *
     * @return string
     */
    public function getAddressAddition(): string {
        return $this->addressAddition;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Profile
     */
    public function setCity(string $city): Profile {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity(): string {
        return $this->city;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Profile
     */
    public function setZip(string $zip): Profile {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip(): string {
        return $this->zip;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Profile
     */
    public function setCountry(string $country): Profile {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry(): string {
        return $this->country;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Profile
     */
    public function setUser(User $user): Profile {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Profile
     */
    public function setFirstName(string $firstName): Profile {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName(): string {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Profile
     */
    public function setLastName(string $lastName): Profile {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName(): string {
        return $this->lastName;
    }

}

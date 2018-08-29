<?php

namespace Com\Nairus\CoreBundle\Entity\Traits;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait for internationalized entities.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait ContentI18nTrait {

    /**
     * Title.
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 50
     * )
     * @ORM\Column(type="string", length=50)
     */
    private $title;

    /**
     * Descriptions.
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * Current locale.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=2)
     */
    private $locale;

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return News
     */
    public function setTitle(?string $title = "") {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return News
     */
    public function setDescription(?string $description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * Set locale.
     *
     * @param string $locale
     */
    public function setLocale(string $locale) {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale(): string {
        return $this->locale;
    }

}

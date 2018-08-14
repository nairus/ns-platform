<?php

namespace Com\Nairus\CoreBundle\Entity\Traits;

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
     * @ORM\Column(type="string", length=50)
     */
    private $title;

    /**
     * Descriptions.
     *
     * @var string
     *
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
    public function setTitle(string $title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale(): string {
        return $this->locale;
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
     * Set description.
     *
     * @param string $description
     *
     * @return News
     */
    public function setDescription(string $description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

}

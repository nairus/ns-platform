<?php

namespace Com\Nairus\CoreBundle\Entity\Traits;

/**
 * Published entity Trait => usable with PHP >= 5.4
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait PublishedTrait {

    /**
     * News publication date.
     *
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * News publication status.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $published = false;

    /**
     * Set publishedAt.
     *
     * @param \DateTimeInterface|null $publishedAt
     *
     * @return News
     */
    public function setPublishedAt(\DateTimeInterface $publishedAt = null) {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt.
     *
     * @return \DateTimeInterface|null
     */
    public function getPublishedAt(): ?\DateTimeInterface {
        return $this->publishedAt;
    }

    /**
     * Set published.
     *
     * @param bool $published
     *
     * @return News
     */
    public function setPublished(bool $published) {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published.
     *
     * @return bool
     */
    public function getPublished(): bool {
        return $this->published;
    }

    /**
     * @ORM\PrePersist
     */
    public function preInsert() {
        $this->publish();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate() {
        $this->publish();
    }

    /**
     * Add published date if the status is TRUE.
     */
    private function publish() {
        if ($this->getPublished()) {
            $this->setPublishedAt(new \DateTime());
        }
    }

}

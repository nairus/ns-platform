<?php

namespace Com\Nairus\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * News
 *
 * @ORM\Table(name="ns_news")
 * @ORM\Entity(repositoryClass="Com\Nairus\CoreBundle\Repository\NewsRepository")
 * @ORM\HaslifecycleCallbacks()
 */
class News {

    /**
     * News unique id.
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * News creation date.
     *
     * @var datetime_immutable
     *
     * @ORM\Column(name="createdAt", type="datetime_immutable")
     */
    private $createdAt;

    /**
     * News update date.
     *
     * @var \DateTime|null
     *
     * @Gedmo\Timestampable
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * News publication date.
     *
     * @var \DateTime|null
     *
     * @ORM\Column(name="publishedAt", type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * News publication status.
     *
     * @var bool
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *   targetEntity="Com\Nairus\CoreBundle\Entity\NewsContent",
     *   mappedBy="news",
     *   cascade={"persist", "remove"}
     * )
     */
    private $contents;

    /**
     * Constructor
     */
    public function __construct() {
        $this->contents = new ArrayCollection();
        $this->published = false;
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
     * Set createdAt.
     *
     * @param DateTimeInterface $createdAt
     *
     * @return News
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): News {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return DateTimeInterface
     */
    public function getCreatedAt(): ?\DateTimeInterface {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTimeInterface|null $updatedAt
     *
     * @return News
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt = null): News {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface {
        return $this->updatedAt;
    }

    /**
     * Set publishedAt.
     *
     * @param \DateTimeInterface|null $publishedAt
     *
     * @return News
     */
    public function setPublishedAt(\DateTimeInterface $publishedAt = null): News {
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
    public function setPublished(bool $published): News {
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
     * Add content.
     *
     * @param NewsContent $content
     *
     * @return News
     */
    public function addContent(NewsContent $content) {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Remove content.
     *
     * @param NewsContent $content
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeContent(NewsContent $content) {
        return $this->contents->removeElement($content);
    }

    /**
     * Get contents.
     *
     * @return Collection
     */
    public function getContents(): Collection {
        return $this->contents;
    }

    /**
     * @ORM\Prepersist
     */
    public function preInsert() {
        $this->setCreatedAt(new \DateTimeImmutable());
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

    /**
     * {@inheritDoc}
     */
    public function __toString() {
        return "[News] id: " . $this->getId();
    }

}

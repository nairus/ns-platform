<?php

namespace Com\Nairus\CoreBundle\Entity;

use Com\Nairus\CoreBundle\Entity\Traits\PublishedTrait;
use Com\Nairus\CoreBundle\Entity\Traits\IsNewTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * News
 *
 * @ORM\Table(name="ns_news")
 * @ORM\Entity(repositoryClass="Com\Nairus\CoreBundle\Repository\NewsRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

/**
     * Published behavior.
     */
    use PublishedTrait;

    /**
     * Constructor
     */
    public function __construct() {
        $this->contents = new ArrayCollection();
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

    use IsNewTrait;

    /**
     * {@inheritDoc}
     */
    public function __toString() {
        return "[News] id: " . $this->id . " (isNew=" . $this->isNew() . ")";
    }

}

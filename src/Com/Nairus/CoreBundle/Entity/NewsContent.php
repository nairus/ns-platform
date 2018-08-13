<?php

namespace Com\Nairus\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Content for News Entity
 *
 * @ORM\Table(name="ns_news_content",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *         "locale", "news_id"
 *     })})
 * @ORM\Entity(repositoryClass="Com\Nairus\CoreBundle\Repository\NewsContentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class NewsContent {

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
     * News title.
     *
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=50)
     */
    private $title;

    /**
     * News current locale.
     *
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=2)
     */
    private $locale;

    /**
     * News descriptions.
     *
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * News link.
     *
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;

    /**
     *
     * @var News
     *
     * @ORM\ManyToOne(targetEntity="Com\Nairus\CoreBundle\Entity\News", inversedBy="contents")
     * @ORM\JoinColumn(name="news_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $news;

    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return News
     */
    public function setTitle(string $title): NewsContent {
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
    public function setLocale(string $locale): NewsContent {
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
    public function setDescription(string $description): NewsContent {
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

    /**
     * Set link.
     *
     * @param string $link
     *
     * @return News
     */
    public function setLink(string $link): NewsContent {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link.
     *
     * @return string
     */
    public function getLink(): string {
        return $this->link;
    }

    /**
     * Set news.
     *
     * @param News $news
     *
     * @return NewsContent
     */
    public function setNews(News $news): NewsContent {
        $this->news = $news;

        return $this;
    }

    /**
     * Get news.
     *
     * @return News
     */
    public function getNews(): News {
        return $this->news;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString() {
        return "[NewsContent] id: " . $this->getId() . " - " . $this->getTitle() . " - " . $this->getLocale();
    }

}

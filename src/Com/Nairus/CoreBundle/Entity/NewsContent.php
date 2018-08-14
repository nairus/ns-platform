<?php

namespace Com\Nairus\CoreBundle\Entity;

use Com\Nairus\CoreBundle\Entity\Traits\ContentI18nTrait;
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
     * News link.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
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
     * I18n content fields behaviors.
     */
    use ContentI18nTrait;

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
    public function getLink(): ?string {
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

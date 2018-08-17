<?php

namespace Com\Nairus\CoreBundle\Service;

use Com\Nairus\CoreBundle\Dto\NewsPaginatorDto;
use Com\Nairus\CoreBundle\Entity\News;
use Com\Nairus\CoreBundle\Entity\NewsContent;
use Com\Nairus\CoreBundle\Exception\LocaleError;
use Com\Nairus\CoreBundle\Exception\PaginatorException;

/**
 * News service.
 *
 * @author nairus
 */
interface NewsServiceInterface {

    /**
     * Find the last news published
     *
     * @param int    $limit    The limit of news to find.
     * @param string $language The language of the news.
     *
     * @return array <NewsContent>
     *
     * @throws LocaleError In case of bad language parameter.
     */
    public function findLastNewsPublished(int $limit, string $language);

    /**
     * Find a content for the current news.
     *
     * @param News   $news   The current News entity.
     * @param string $locale The language of content seeked.
     *
     * @return NewsContent|null
     */
    public function findContentForNewsId(News $news, string $locale): ?NewsContent;

    /**
     * Find the news and dependencies for the current page.
     *
     * @param int $page  The current page.
     * @param int $limit The limit of entities per page.
     *
     * @return NewsPaginatorDto
     *
     * @throws PaginatorException
     */
    public function findNewsForPage(int $page, int $limit): NewsPaginatorDto;
}

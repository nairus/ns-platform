<?php

namespace Com\Nairus\CoreBundle\Service;

use Com\Nairus\CoreBundle\Exception\LocaleError;

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
}

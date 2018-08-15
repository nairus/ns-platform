<?php

namespace Com\Nairus\CoreBundle\Dto;

use Com\Nairus\CoreBundle\Dto\Traits\PaginatorTrait;

/**
 * Paginator the news collection.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class NewsPaginatorDto {

    /**
     * Map of missing translation for news collection.
     *
     * Key: news_id / Value: array <string>
     * Exemple of map:
     * [
     *   1 => ["fr", "en"],
     *   2 => ["en"],
     *   3 => [],
     * ]
     *
     * @var array
     */
    private $missingTranslations;

    /**
     * Behavior the paginator dto.
     */
    use PaginatorTrait;

    /**
     * Return the missing translation map for the news collection.
     *
     * @return array
     */
    public function getMissingTranslations(): array {
        return $this->missingTranslations;
    }

    /**
     * Defines the missing translation map.
     *
     * @param array $missingTranslations The missing translation map.
     *
     * @return NewsPaginatorDto
     */
    public function setMissingTranslations(array $missingTranslations): NewsPaginatorDto {
        $this->missingTranslations = $missingTranslations;

        return $this;
    }

}

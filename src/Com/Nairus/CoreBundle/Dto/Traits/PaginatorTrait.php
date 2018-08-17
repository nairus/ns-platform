<?php

namespace Com\Nairus\CoreBundle\Dto\Traits;

/**
 * Trait for paginated collection entities DTO.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait PaginatorTrait {

    /**
     * The number of pages.
     *
     * @var int
     */
    private $pages;

    /**
     * The number of the current page.
     *
     * @var int
     */
    private $currentPage;

    /**
     * The collection of entities for the current page.
     *
     * @var array
     */
    private $entities;

    /**
     * Return the number of pages.
     *
     * @return int
     */
    public function getPages(): int {
        return $this->pages;
    }

    /**
     * Return the current page.
     *
     * @return int
     */
    public function getCurrentPage(): int {
        return $this->currentPage;
    }

    /**
     * Return the collection of entities for the current page.
     *
     * @return array
     */
    public function getEntities() {
        return $this->entities;
    }

    /**
     * Defines the number of pages.
     *
     * @param int $pages The number of pages.
     */
    public function setPages(int $pages) {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Defines the current page
     *
     * @param int $currentPage The current page.
     */
    public function setCurrentPage(int $currentPage) {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Defines the collection of entities for the current page.
     *
     * @param array $entities The collection of entities for the current page.
     */
    public function setEntities(array $entities) {
        $this->entities = $entities;

        return $this;
    }

}

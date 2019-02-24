<?php

namespace Com\Nairus\ResumeBundle\Collection;

use Com\Nairus\ResumeBundle\Entity\Resume;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Resume entities collection.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeCollection extends ArrayCollection {

    /**
     * {@inheritDoc}
     */
    public function add($element): bool {
        $this->validType($element);
        return parent::add($element);
    }

    /**
     * Return the current element.
     *
     * @return Resume|bool
     */
    public function current() {
        return parent::current();
    }

    /**
     * Return the first element.
     *
     * @return Resume|bool
     */
    public function first() {
        return parent::first();
    }

    /**
     * Return the element for the current key.
     *
     * @param mixed $key
     *
     * @return Resume
     */
    public function get($key): ?Resume {
        return parent::get($key);
    }

    /**
     * Return the last element.
     *
     * @return Resume|bool
     */
    public function last() {
        return parent::last();
    }

    /**
     * Surcharge de l'accesseur de la collection.
     *
     * @param mixed $offset Offset de l'élément.
     *
     * @return Resume
     */
    public function offsetGet($offset): ?Resume {
        $entity = parent::offsetGet($offset);
        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value): ?bool {
        $this->validType($value);
        return parent::offsetSet($offset, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value): void {
        $this->validType($value);
        parent::set($key, $value);
    }

    /**
     * Valid the type of the element.
     *
     * @codeCoverageIgnore
     *
     * @param Resume $entity The entity to verify.
     *
     * @throws \TypeError If this is the wrong type.
     */
    private function validType($entity) {
        if (!$entity instanceof Resume) {
            throw new \TypeError("Entité [Resume] attendue.");
        }
    }

}

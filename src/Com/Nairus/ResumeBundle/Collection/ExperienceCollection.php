<?php

namespace Com\Nairus\ResumeBundle\Collection;

use Com\Nairus\ResumeBundle\Entity\Experience;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Experience entities collection.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ExperienceCollection extends ArrayCollection {

    /**
     * {@inheritDoc}
     */
    public function __construct(array $elements = array()) {
        foreach ($elements as $entity) {
            $this->validType($entity);
        }

        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     */
    public function add($element): bool {
        $this->validType($element);
        return parent::add($element);
    }

    /**
     * Return the current element or FALSE.
     *
     * @return Experience
     */
    public function current() {
        return parent::current();
    }

    /**
     * Return the first element or FALSE.
     *
     * @return Com\Nairus\ResumeBundle\Entity\Experience|bool
     */
    public function first() {
        return parent::first();
    }

    /**
     * @return Com\Nairus\ResumeBundle\Entity\Experience|null
     */
    public function get($key): ?Experience {
        return parent::get($key);
    }

    /**
     * Return the last element or FALSE.
     *
     * @return Com\Nairus\ResumeBundle\Entity\Experience|bool
     */
    public function last() {
        return parent::last();
    }

    /**
     * @return Com\Nairus\ResumeBundle\Entity\Experience|null
     */
    public function offsetGet($offset): ?Experience {
        return parent::offsetGet($offset);
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
     * @param Experience $entity The entity to verify.
     *
     * @throws \TypeError If this is the wrong type.
     */
    private function validType($entity) {
        if (!$entity instanceof Experience) {
            throw new \TypeError("[Experience] entity expected.");
        }
    }

}

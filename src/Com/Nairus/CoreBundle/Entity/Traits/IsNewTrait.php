<?php

namespace Com\Nairus\CoreBundle\Entity\Traits;

/**
 * Trait for entity with isNew behavior.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait IsNewTrait {

    /**
     * Return if the entity is new.
     *
     * @return bool TRUE if the entity is new.
     */
    public function isNew(): bool {
        return null === $this->id;
    }

}

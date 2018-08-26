<?php

namespace Com\Nairus\CoreBundle\Tests\Entity\Mock;

/**
 * Mock for translation entities tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class BadTranslatableEntity extends \Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity {

    public function addTranslation(\Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation $translation) {
        return $this;
    }

    public function removeTranslation(\Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation $translation): bool {
        return false;
    }

}

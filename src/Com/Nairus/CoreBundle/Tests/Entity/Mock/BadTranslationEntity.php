<?php

namespace Com\Nairus\CoreBundle\Tests\Entity\Mock;

use Com\Nairus\CoreBundle\Entity\AbstractTranslationEntity;

/**
 * Mock for Translatable entities tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class BadTranslationEntity extends AbstractTranslationEntity {

    protected function validObjectClass(\Com\Nairus\CoreBundle\Entity\TranslatableEntityInterface $object): void {

    }

}

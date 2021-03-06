<?php

namespace Com\Nairus\CoreBundle\Tests\Entity\Mock;

/**
 * Mock for translation entities tests.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class BadTranslatableEntity extends \Com\Nairus\CoreBundle\Entity\AbstractTranslatableEntity {

    protected function validateTranslationEntity(\Com\Nairus\CoreBundle\Entity\TranslationEntityInterface $translation): void {

    }

    public static function getTranslationEntityClass(): string {
        return 'Com\Nairus\NSCoreBundle\Test\Entity\BadTranslationEntity';
    }

}

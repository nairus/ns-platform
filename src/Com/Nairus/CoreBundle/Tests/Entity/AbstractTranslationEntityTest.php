<?php

namespace Com\Nairus\CoreBundle\Tests\Entity;

use Com\Nairus\CoreBundle\Tests\Entity\Mock\BadTranslationEntity;
use PHPUnit\Framework\TestCase;

/**
 * Test of AbstractTranslationEntity class.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class AbstractTranslationEntityTest extends TestCase {

    /**
     * Test the implementation of the entity.
     *
     * @return void
     */
    public function testImplementation(): void {
        $entity = new BadTranslationEntity();
        $this->assertInstanceOf("Com\Nairus\CoreBundle\Entity\TranslationEntityInterface", $entity, "1. The entity musts be a type of [TranslationEntityInterface] interface.");
        $this->assertInstanceOf("Prezent\Doctrine\Translatable\Entity\AbstractTranslation", $entity, "2. The entity musts be an instance of [AbstractTranslation] Prezent mapped class.");
    }

}

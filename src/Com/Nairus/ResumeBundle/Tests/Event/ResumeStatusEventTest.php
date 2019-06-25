<?php

namespace Com\Nairus\ResumeBundle\Event;

use Com\Nairus\ResumeBundle\Entity\Resume;
use PHPUnit\Framework\TestCase;

/**
 * Test of ResumeStatusEvent.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeStatusEventTest extends TestCase {

    /**
     * Test the constructor.
     *
     * @covers Com\Nairus\ResumeBundle\Event\ResumeStatusEvent::__construct
     *
     * @return void
     */
    public function testConstructor(): void {
        $resumeStatusEvent = new ResumeStatusEvent(new Resume());
        $this->assertInstanceOf(\Symfony\Component\EventDispatcher\Event::class, $resumeStatusEvent, "1. The object has to extend [Event] class.");
    }

    /**
     * Test the `getResume` method.
     *
     * @covers Com\Nairus\ResumeBundle\Event\ResumeStatusEvent::getResume
     *
     * @return void
     */
    public function testGetResume(): void {
        $resumeStatusEvent = new ResumeStatusEvent(new Resume());
        $this->assertNotNull($resumeStatusEvent->getResume(), "1. The getter has to return a Resume entity.");
    }

}

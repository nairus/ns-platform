<?php

namespace Com\Nairus\UserBundle\Listener;

use PHPUnit\Framework\TestCase;

/**
 * Test of UserProfileListener.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class UserProfileListenerTest extends TestCase {

    /**
     * Test <code>getSubscribedEvents</code> method.
     *
     * @covers Com\Nairus\UserBundle\Listener\UserProfileListener::getSubscribedEvents
     *
     * @return void
     */
    public function testGetSubscribedEvents(): void {
        $subscribedEvents = UserProfileListener::getSubscribedEvents();
        $this->assertCount(2, $subscribedEvents, "1. The listener has to contain two events.");
        $this->assertContains(\FOS\UserBundle\FOSUserEvents::CHANGE_PASSWORD_COMPLETED, $subscribedEvents, "2. The [CHANGE_PASSWORD_COMPLETED] has to be in the list.");
        $this->assertContains(\FOS\UserBundle\FOSUserEvents::PROFILE_EDIT_COMPLETED, $subscribedEvents, "3. The [PROFILE_EDIT_COMPLETED] has to be in the list.");
    }

}

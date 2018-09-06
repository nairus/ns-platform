<?php

namespace Com\Nairus\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Event listener for user bundle events (edit profile, change password ...).
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class UserProfileListener implements EventSubscriberInterface {

    /**
     * The translator service.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator The translator service.
     */
    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array {
        return [
            FOSUserEvents::CHANGE_PASSWORD_COMPLETED,
            FOSUserEvents::PROFILE_EDIT_COMPLETED
        ];
    }

    /**
     * Add flashes messages on Change password completed event.
     *
     * @param FilterUserResponseEvent $event
     *
     * @return void
     */
    public function onChangePasswordCompleted(FilterUserResponseEvent $event): void {
        $this->addFlashMessage($event->getRequest()->getSession(), "info", "flashes.info.email-sent");
    }

    /**
     * Add flashes messages on profile edit completed event.
     *
     * @param FilterUserResponseEvent $event
     *
     * @return void
     */
    public function onProfileEditCompleted(FilterUserResponseEvent $event): void {
        $this->addFlashMessage($event->getRequest()->getSession(), "info", "flashes.info.email-sent");
    }

    /**
     * Add a flash message in the session.
     *
     * @param SessionInterface $session The current session.
     * @param string           $type    The type of flash message.
     * @param string           $message The message to display.
     *
     * @return void
     */
    private function addFlashMessage(SessionInterface $session, string $type, string $message): void {
        $session->getBag("flashes")->add($type, $this->translator->trans($message, [], "NSUserBundle"));
    }

}

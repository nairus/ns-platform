<?php

namespace Com\Nairus\CoreBundle\Controller;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Entity\ContactMessage;
use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\CoreBundle\Service\ContactServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Front contact controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ContactController extends Controller {

    use \Com\Nairus\CoreBundle\Traits\CommonComponentsTrait;

    private const NAME = NSCoreBundle::NAME . ":Contact";

    /**
     * Contact service.
     *
     * @var ContactServiceInterface
     */
    private $contactService;

    /**
     * Mailer service.
     *
     * @var \Swift_Mailer $mailer
     */
    private $mailer;

    /**
     * Constructor.
     *
     * @param ContactServiceInterface $contactService Contact service.
     * @param LoggerInterface         $logger         Logger service.
     * @param TranslatorInterface     $translator     Translator service.
     * @param \Swift_Mailer           $mailer         Mailer service.
     */
    public function __construct(ContactServiceInterface $contactService, LoggerInterface $logger, TranslatorInterface $translator, \Swift_Mailer $mailer) {
        $this->contactService = $contactService;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->mailer = $mailer;
    }

    /**
     * The form action.
     *
     * @param Request $request The current request.
     *
     * @return Response
     */
    public function formAction(Request $request): Response {
        $contactMessage = new ContactMessage();
        $contactMessage->setIp($request->getClientIp());
        $form = $this->createForm(\Com\Nairus\CoreBundle\Form\ContactMessageType::class, $contactMessage);
        $form->handleRequest($request);

        // if the form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->sanitize($contactMessage);
                $this->contactService->handleContactMessage($request->getClientIp(), $contactMessage);
                $request->getSession()->getFlashBag()->add("success", $this->getTranslation("contact.message.success"));

                // send internal email
                $name = $contactMessage->getName();
                $contact = empty($contactMessage->getEmail()) ? $contactMessage->getPhone() : $contactMessage->getEmail();
                $this->sendNotification("New Contact message", "A contact message has been sent.\nName: $name, contact: $contact");

                return $this->redirectToRoute("ns_core_homepage");
            } catch (FunctionalException $exc) {
                $customMessage = "An error occured while saving the message: {contact} in `{class}:{method}`";
                $this->logError(static::NAME, "formAction", $exc, $customMessage, ["contact" => var_export($contactMessage, true)]);
                return $this->handleException($request, $exc);
            }
        }

        return $this->render(static::NAME . ":form.html.twig", [
                    'contactMessage' => $contactMessage,
                    'form' => $form->createView()
        ]);
    }

    /**
     * Handle service exception.
     *
     * @param Request             $request The current HTTP request.
     * @param FunctionalException $exc     The exception to handle.
     *
     * @return Response
     */
    public function handleException(Request $request, FunctionalException $exc): Response {
        // if the client is blacklisted, we fake a success.
        if ($exc->getCode() === ContactServiceInterface::IS_BLACKLITED_ERROR_CODE) {
            $clientIp = $request->getClientIp();
            $message = "The contact [$clientIp] is blacklisted";
            $request->getSession()->getFlashBag()->add("success", $this->getTranslation($exc->getTranslationKey()));
        } else {
            $message = "An unknown error occured";
            $request->getSession()->getFlashBag()->add("error", $this->getTranslation($exc->getTranslationKey()));
        }

        // Send internal email
        $this->sendNotification("Contact message failed", $message);

        return $this->redirectToRoute("ns_core_homepage");
    }

    /**
     * Send internal notification.
     *
     * @param string $subject
     * @param string $body
     *
     * @return void
     */
    private function sendNotification(string $subject, string $body): void {
        $senderEmail = $this->container->getParameter("mailer_user");
        $recipientEmail = $this->container->getParameter("mailer_user_to");

        $message = new \Swift_Message($subject, $body);
        $message->setFrom($senderEmail)
                ->setTo($recipientEmail);
        $result = $this->mailer->send($message);
        $this->logger->debug("Email send => from: `{from}, to: {to}, subject: {subject}, body: {body}",
                ["from" => $senderEmail, "to" => $recipientEmail, "subject" => $subject, "body" => $body]);

        if (0 === $result) {
            $this->logError(static::NAME, "sendNotification", null, "The email has not been sent for recipient: {recipient} in `{class}.{method}`, ",
                    ['recipient' => $recipientEmail]);
        }
    }

    /**
     * Sanitize the content.
     *
     * @param ContactMessage $contactMessage
     *
     * @return void
     */
    private function sanitize(ContactMessage $contactMessage): void {
        $contactMessage->setName(strip_tags($contactMessage->getName()))
                ->setMessage(strip_tags($contactMessage->getMessage()));
    }

}

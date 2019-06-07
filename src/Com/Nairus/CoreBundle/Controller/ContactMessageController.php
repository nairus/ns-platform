<?php

namespace Com\Nairus\CoreBundle\Controller;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Dto\ContactMessageDto;
use Com\Nairus\CoreBundle\Entity as NSEntity;
use Com\Nairus\CoreBundle\Exception\PaginatorException;
use Com\Nairus\CoreBundle\Service\ContactServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Super admin ContactMessage controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 */
class ContactMessageController extends Controller {

    use \Com\Nairus\CoreBundle\Traits\CommonComponentsTrait;

    private const NAME = NSCoreBundle::NAME . ":ContactMessage";

    /**
     * @var \Com\Nairus\CoreBundle\Service\ContactServiceInterface
     */
    private $contactService;

    /**
     * The constructor.
     *
     * @param ContactServiceInterface $contactService The contact service.
     * @param LoggerInterface         $logger         Logger service.
     * @param TranslatorInterface     $translator     Translator service.
     */
    public function __construct(ContactServiceInterface $contactService, LoggerInterface $logger, TranslatorInterface $translator) {
        $this->contactService = $contactService;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * Lists all contactMessage entities.
     *
     * @param int $page The current page.
     *
     * @return Response
     */
    public function indexAction(Request $request, $page): Response {
        // Bug chrome
        if ("" === $page) {
            $page = 1;
        }

        try {
            $limit = $this->getParameter("ns_core.contact_message_limit");
            $contactMessageDto = $this->contactService->findAllForPage($page, $limit);

            // If the number of page is wrong, we throw a 404 error
            if ($page > 1 && $page > $contactMessageDto->getPages()) {
                throw $this->createNotFoundException();
            }

            return $this->render(static::NAME . ':index.html.twig', array(
                        'items' => $this->buildItems($contactMessageDto),
                        'currentPage' => $page,
                        'pages' => $contactMessageDto->getPages()
            ));
        } catch (PaginatorException $exc) {
            throw new BadRequestHttpException("Bad page parameter", $exc);
        }
    }

    /**
     * Finds and displays a contactMessage entity.
     *
     * @param Request                 $request        The current request.
     * @param NSEntity\ContactMessage $contactMessage The entity to blacklist.
     *
     * @return Response
     *
     */
    public function showAction(Request $request, NSEntity\ContactMessage $contactMessage): Response {
        $datesFormat = $this->container->getParameter("dates_format");
        $viewParameters = [
            'contactMessage' => $contactMessage,
            'dateFormat' => $datesFormat[$request->getLocale()],
            'deleteForm' => $this->createDeleteForm($contactMessage)->createView()
        ];
        /* @var $blacklistRepository \Com\Nairus\CoreBundle\Repository\BlacklistedIpRepository */
        $blacklistRepository = $this->getDoctrine()->getRepository(NSEntity\BlacklistedIp::class);
        if (!$blacklistRepository->isBlackListed($contactMessage->getIp())) {
            $viewParameters['blacklistForm'] = $this->createBlacklistForm($contactMessage)->createView();
        }
        return $this->render(static::NAME . ':show.html.twig', $viewParameters);
    }

    /**
     * Blacklist a contact message ip.
     *
     * @param Request                 $request        The current request.
     * @param NSEntity\ContactMessage $contactMessage The entity to blacklist.
     *
     * @return Response
     */
    public function blacklistAction(Request $request, NSEntity\ContactMessage $contactMessage): Response {
        try {
            if ($this->contactService->blacklistContactMessage($contactMessage)) {
                $request->getSession()->getFlashBag()->add("success",
                        $this->getTranslation("flashes.success.contact-message.blacklist", ['%ip%' => $contactMessage->getIp()]));
            } else {
                $request->getSession()->getFlashBag()->add("error",
                        $this->getTranslation("flashes.error.contact-message.already-blacklisted", ['%ip%' => $contactMessage->getIp()]));
            }
        } catch (\Throwable $exc) {
            $this->logError(static::NAME, "blacklistAction", $exc);
            $request->getSession()->getFlashBag()->add("error",
                    $this->getTranslation("flashes.error.contact-message.blacklist", ['%ip%' => $contactMessage->getIp()]));
        } finally {
            return $this->redirectToRoute("sadmin_contact_index");
        }
    }

    /**
     * Delete an entity.
     *
     * @param Request                 $request        The current request.
     * @param NSEntity\ContactMessage $contactMessage The entity to delete.
     *
     * @return Response
     */
    public function deleteAction(Request $request, NSEntity\ContactMessage $contactMessage): Response {
        // save the entity id
        $id = $contactMessage->getId();
        try {
            $form = $this->createDeleteForm($contactMessage);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->contactService->deleteContactMessage($contactMessage);
                $request->getSession()->getFlashBag()->add("success", $this->getTranslation("flashes.success.contact-message.delete", ['%id%' => $id]));
            }
        } catch (\Throwable $exc) {
            $this->logError(static::NAME, "deleteAction", $exc);
            $request->getSession()->getFlashBag()->add("error", $this->getTranslation("flashes.error.contact-message.delete", ['%id%' => $id]));
        } finally {
            return $this->redirectToRoute("sadmin_contact_index");
        }
    }

    /**
     * Create delete form.
     *
     * @param NSEntity\ContactMessage $entity The current entity.
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(NSEntity\ContactMessage $entity): \Symfony\Component\Form\Form {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('sadmin_contact_delete', ['id' => $entity->getId()]))
                        ->setMethod(Request::METHOD_DELETE)
                        ->getForm();
    }

    /**
     * Create the blacklist form.
     *
     * @param \Com\Nairus\CoreBundle\Entity\ContactMessage $contactMessage
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createBlacklistForm(NSEntity\ContactMessage $contactMessage): \Symfony\Component\Form\Form {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('sadmin_contact_blacklist', ['id' => $contactMessage->getId()]))
                        ->setMethod(Request::METHOD_POST)
                        ->getForm();
    }

    /**
     * Build the items datas for the view.
     *
     * @param ContactMessageDto $contactMessageDto
     *
     * @return array
     */
    private function buildItems(ContactMessageDto $contactMessageDto): array {
        $items = [];
        foreach ($contactMessageDto->getEntities() as /* @var $entity NSEntity\ContactMessage */ $entity) {
            // create delete form
            $deleteForm = $this->createDeleteForm($entity)->createView();

            // create blacklist form
            $datas = ['entity' => $entity, 'deleteForm' => $deleteForm];
            if (!$contactMessageDto->isBlacklisted($entity->getIp())) {
                $datas['blacklistForm'] = $this->createBlacklistForm($entity)->createView();
            }
            array_push($items, $datas);
        }

        return $items;
    }

}

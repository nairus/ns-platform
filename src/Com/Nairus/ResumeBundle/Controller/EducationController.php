<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Education;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Form\EducationType;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Education restricted controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class EducationController extends Controller {

    private const NAME = NSResumeBundle::NAME . ":Education";

    /**
     * Traits for internationalization behaviors and security check.
     */
    use \Com\Nairus\CoreBundle\Traits\CommonComponentsTrait;
    use \Com\Nairus\ResumeBundle\Traits\ResumeSecurityTrait;
    use \Com\Nairus\ResumeBundle\Traits\ResumeUpdateStatusTrait;

    /**
     * Constructor.
     *
     * @param LoggerInterface          $logger          The logger service.
     * @param TranslatorInterface      $translator      The translatorService.
     * @param EventDispatcherInterface $eventDispatcher The kernel event dispatcher.
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator, EventDispatcherInterface $eventDispatcher) {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Creates a new education entity.
     *
     * @ParamConverter("resume", options={"mapping": {"resume_id": "id"}})
     *
     * @param Request $request The current request.
     * @param Resume  $resume  The parent resume.
     *
     * @return Response
     */
    public function newAction(Request $request, Resume $resume): Response {
        // Security check.
        $this->check($resume, $this->getUser());

        $education = new Education();
        $education->setResume($resume);
        $form = $this->createForm(EducationType::class, $education);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($education);
            $em->flush();

            // Dispatch event for updating the resume's status.
            $this->dispatchUpdateEvent($resume);

            $this->addFlash("success", $this->getTranslation("flashes.success.education.new", [], NSResumeBundle::NAME));
            return $this->redirectToRoute('education_show', array('id' => $education->getId()));
        }

        return $this->render(self::NAME . ':new.html.twig', array(
                    'education' => $education,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a education entity.
     *
     * @param Education $education The current entity.
     *
     * @return Response
     */
    public function showAction(Education $education): Response {
        // Security check.
        $this->check($education->getResume(), $this->getUser());

        $deleteForm = $this->createDeleteForm($education);

        // Get the defaultLocale
        $defaultLocale = $this->container->getParameter('kernel.default_locale');
        return $this->render(self::NAME . ':show.html.twig', array(
                    'education' => $education,
                    'delete_form' => $deleteForm->createView(),
                    'defaultLocale' => $defaultLocale,
        ));
    }

    /**
     * Displays a form to edit an existing education entity.
     *
     * @param Request   $request   The current request.
     * @param Education $education The current entity.
     *
     * @return Response
     */
    public function editAction(Request $request, Education $education): Response {
        // Security check.
        $this->check($education->getResume(), $this->getUser());

        $deleteForm = $this->createDeleteForm($education);
        $editForm = $this->createForm(EducationType::class, $education);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", $this->getTranslation("flashes.success.education.edit", ["%id%" => $education->getId()], NSResumeBundle::NAME));
            return $this->redirectToRoute('education_show', array('id' => $education->getId()));
        }

        return $this->render(self::NAME . ':edit.html.twig', array(
                    'education' => $education,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a education entity.
     *
     * @param Request   $request   The current request.
     * @param Education $education The current entity.
     *
     * @return Response
     */
    public function deleteAction(Request $request, Education $education): Response {
        // Security check.
        $this->check($education->getResume(), $this->getUser());

        $form = $this->createDeleteForm($education);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resume = $education->getResume();
            $educationId = $education->getId();
            $em = $this->getDoctrine()->getManager();
            $em->remove($education);
            $em->flush();

            // Dispatch the delete event to update the status.
            $this->dispatchDeleteEvent($resume);

            $this->addFlash("success", $this->getTranslation("flashes.success.education.delete", ["%id%" => $educationId], NSResumeBundle::NAME));
            return $this->redirectToRoute('resume_show', ['id' => $resume->getId()]);
        }

        // Get the defaultLocale
        $defaultLocale = $this->container->getParameter('kernel.default_locale');
        return $this->render(self::NAME . ':delete.html.twig', [
                    'education' => $education,
                    'delete_form' => $form->createView(),
                    'defaultLocale' => $defaultLocale,
        ]);
    }

    /**
     * Creates a form to delete a education entity.
     *
     * @param Education $education The education entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Education $education): \Symfony\Component\Form\Form {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('education_delete', array('id' => $education->getId())) . "#educations")
                        ->setMethod(Request::METHOD_DELETE)
                        ->getForm()
        ;
    }

}

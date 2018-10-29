<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\Experience;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Experience restricted controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ExperienceController extends Controller {

    private const NAME = NSResumeBundle::NAME . ":Experience";

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
     * Creates a new experience entity.
     *
     * @ParamConverter("resume", options={"mapping": {"resume_id": "id"}})
     *
     * @param Request $request The current request.
     * @param Resume  $resume  The parent resume.
     *
     * @return Response
     *
     */
    public function newAction(Request $request, Resume $resume): Response {
        // Security check.
        $this->check($resume, $this->getUser());

        $experience = new Experience();
        $experience->setResume($resume);
        $form = $this->createForm('Com\Nairus\ResumeBundle\Form\ExperienceType', $experience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($experience);
            $em->flush();

            // Dispatch event for updating the resume's status.
            $this->dispatch($resume);

            $this->addFlash("success", $this->getTranslation("flashes.success.experience.new", [], NSResumeBundle::NAME));

            return $this->redirectToRoute('experience_show', array('id' => $experience->getId()));
        }

        return $this->render(self::NAME . ':new.html.twig', array(
                    'experience' => $experience,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a experience entity.
     *
     * @param Experience $experience The current entity.
     *
     * @return Response
     *
     */
    public function showAction(Experience $experience): Response {
        // Security check.
        $this->check($experience->getResume(), $this->getUser());

        $deleteForm = $this->createDeleteForm($experience);

        // Get the defaultLocale
        $defaultLocale = $this->container->getParameter('kernel.default_locale');
        return $this->render(self::NAME . ':show.html.twig', [
                    'experience' => $experience,
                    'delete_form' => $deleteForm->createView(),
                    'defaultLocale' => $defaultLocale,
        ]);
    }

    /**
     * Displays a form to edit an existing experience entity.
     *
     * @param Request    $request    The current request.
     * @param Experience $experience The current entity.
     *
     * @return Response
     *
     */
    public function editAction(Request $request, Experience $experience): Response {
        // Security check.
        $this->check($experience->getResume(), $this->getUser());

        $deleteForm = $this->createDeleteForm($experience);
        $editForm = $this->createForm('Com\Nairus\ResumeBundle\Form\ExperienceType', $experience);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", $this->getTranslation("flashes.success.experience.edit", ["%id%" => $experience->getId()], NSResumeBundle::NAME));

            return $this->redirectToRoute('experience_show', array('id' => $experience->getId()));
        }

        return $this->render(self::NAME . ':edit.html.twig', array(
                    'experience' => $experience,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a experience entity.
     *
     * @param Request    $request    The current request.
     * @param Experience $experience The current entity.
     *
     * @return Response
     *
     */
    public function deleteAction(Request $request, Experience $experience): Response {
        // Security check.
        $resume = $experience->getResume();
        $this->check($resume, $this->getUser());

        $form = $this->createDeleteForm($experience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $experienceId = $experience->getId();
            $em = $this->getDoctrine()->getManager();
            $em->remove($experience);
            $em->flush();

            $this->addFlash("success", $this->getTranslation("flashes.success.experience.delete", ["%id%" => $experienceId], NSResumeBundle::NAME));

            return $this->redirectToRoute('resume_show', ['id' => $resume->getId()]);
        }

        // Go to the confirm form
        return $this->render(self::NAME . ':delete.html.twig', [
                    'experience' => $experience,
                    'delete_form' => $form->createView(),
        ]);
    }

    /**
     * Creates a form to delete a experience entity.
     *
     * @param Experience $experience The experience entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Experience $experience): Form {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('experience_delete', array('id' => $experience->getId())) . "#experiences")
                        ->setMethod(Request::METHOD_DELETE)
                        ->getForm()
        ;
    }

}

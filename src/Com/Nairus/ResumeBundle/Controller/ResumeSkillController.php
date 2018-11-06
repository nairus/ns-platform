<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\ResumeSkill;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * ResumeSkill restricted controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeSkillController extends Controller {

    private const NAME = NSResumeBundle::NAME . ":ResumeSkill";

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
     * Creates a new resumeSkill entity.
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

        // Get the defaultLocale
        $defaultLocale = $this->container->getParameter('kernel.default_locale');

        // Define the next rank.
        $rank = $resume->getResumeSkills()->count() + 1;

        $resumeSkill = new ResumeSkill();
        $resumeSkill->setResume($resume);
        $resumeSkill->setRank($rank);
        $form = $this->createForm(
                'Com\Nairus\ResumeBundle\Form\ResumeSkillType',
                $resumeSkill,
                ['currentLocale' => $request->getLocale(), 'defaultLocale' => $defaultLocale]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($resumeSkill);
            $em->flush();

            // Dispatch event for updating the resume's status.
            $this->dispatchUpdateEvent($resume);

            $this->addFlash("success", $this->getTranslation("flashes.success.resume-skill.new", [], NSResumeBundle::NAME));

            return $this->redirectToRoute('resume_show', array('id' => $resume->getId()));
        }

        return $this->render(self::NAME . ':new.html.twig', array(
                    'resumeSkill' => $resumeSkill,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing resumeSkill entity.
     *
     * @param Request     $request     The current request.
     * @param ResumeSkill $resumeSkill The current entity.
     *
     * @return Response
     */
    public function editAction(Request $request, ResumeSkill $resumeSkill): Response {
        // Check the credential.
        $this->check($resumeSkill->getResume(), $this->getUser());

        $deleteForm = $this->createDeleteForm($resumeSkill);

        // Get the defaultLocale
        $defaultLocale = $this->container->getParameter('kernel.default_locale');

        $editForm = $this->createForm(
                'Com\Nairus\ResumeBundle\Form\ResumeSkillType',
                $resumeSkill,
                ['currentLocale' => $request->getLocale(), 'defaultLocale' => $defaultLocale]);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", $this->getTranslation("flashes.success.resume-skill.edit", ["%id%" => $resumeSkill->getId()], NSResumeBundle::NAME));

            return $this->redirectToRoute('resume_show', array('id' => $resumeSkill->getResume()->getId()));
        }

        return $this->render(self::NAME . ':/edit.html.twig', array(
                    'resumeSkill' => $resumeSkill,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a resumeSkill entity.
     *
     * @param Request     $request     The current request.
     * @param ResumeSkill $resumeSkill The current entity.
     *
     * @return Response
     */
    public function deleteAction(Request $request, ResumeSkill $resumeSkill): Response {
        // Get the linked resume
        $resume = $resumeSkill->getResume();

        // Check the credential.
        $this->check($resume, $this->getUser());

        $form = $this->createDeleteForm($resumeSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resumeSkillId = $resumeSkill->getId();
            $em = $this->getDoctrine()->getManager();
            $em->remove($resumeSkill);
            $em->flush();

            // Dispatch the delete event to update the status.
            $this->dispatchDeleteEvent($resume);

            $this->addFlash("success", $this->getTranslation("flashes.success.resume-skill.delete", ["%id%" => $resumeSkillId], NSResumeBundle::NAME));

            return $this->redirectToRoute('resume_show', array('id' => $resume->getId()));
        }

        // Go to confirm form
        return $this->render(self::NAME . ':delete.html.twig', [
                    'resumeSkill' => $resumeSkill,
                    'delete_form' => $form->createView()
        ]);
    }

    /**
     * Creates a form to delete a resumeSkill entity.
     *
     * @param ResumeSkill $resumeSkill The resumeSkill entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ResumeSkill $resumeSkill): \Symfony\Component\Form\Form {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('resumeskill_delete', array('id' => $resumeSkill->getId())))
                        ->setMethod(Request::METHOD_DELETE)
                        ->getForm()
        ;
    }

}

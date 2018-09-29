<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Com\Nairus\ResumeBundle\Service\SkillLevelServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Skilllevel controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillLevelController extends Controller {

    private const NAME = NSResumeBundle::NAME . ":SkillLevel";

    /**
     * Skill level service.
     *
     * @var SkillLevelServiceInterface
     */
    private $skillLevelService;

    /**
     * Trait for internationalization behaviors.
     */
    use \Com\Nairus\CoreBundle\Traits\CommonComponentsTrait;

    /**
     * Constructor.
     *
     * @param SkillLevelServiceInterface $skillLevelService The skill level service.
     * @param LoggerInterface            $logger             The logger service.
     * @param TranslatorInterface        $translator         The translatorService.
     */
    public function __construct(SkillLevelServiceInterface $skillLevelService, LoggerInterface $logger, TranslatorInterface $translator) {
        $this->skillLevelService = $skillLevelService;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * Lists all skillLevel entities.
     *
     * @return Response
     */
    public function indexAction(): Response {
        // Get the collection of SkillLevel.
        $em = $this->getDoctrine()->getManager();
        $skillLevels = $em->getRepository(static::NAME)->findAll();

        // Foreach entity, we create a delete form.
        $items = [];
        foreach ($skillLevels as $skillLevel) {
            $deleteForm = $this->createDeleteForm($skillLevel)->createView();
            array_push($items, ['entity' => $skillLevel, 'deleteForm' => $deleteForm]);
        }
        return $this->render(static::NAME . ':index.html.twig', array(
                    'items' => $items,
        ));
    }

    /**
     * Creates a new skillLevel entity.
     *
     * @param Request $request The current request.
     *
     * @return Response
     */
    public function newAction(Request $request): Response {
        // Get the defaultLocale
        $defaultLocale = $this->container->getParameter('kernel.default_locale');

        $skillLevel = new Skilllevel();
        $form = $this->createForm('Com\Nairus\ResumeBundle\Form\SkillLevelType', $skillLevel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($skillLevel);
            $em->flush();

            $this->addFlash("success", $this->getTranslation("flashes.success.skill-level.new", [], NSResumeBundle::NAME));

            return $this->redirectToRoute('skilllevel_index');
        }

        return $this->render(static::NAME . ':new.html.twig', array(
                    'skillLevel' => $skillLevel,
                    'form' => $form->createView(),
                    'defaultLocale' => $defaultLocale,
        ));
    }

    /**
     * Finds and displays a skillLevel entity.
     *
     * @param SkillLevel $skillLevel The skill level to display.
     *
     * @return Response
     */
    public function showAction(SkillLevel $skillLevel): Response {
        $deleteForm = $this->createDeleteForm($skillLevel);

        return $this->render(static::NAME . ':show.html.twig', array(
                    'skillLevel' => $skillLevel,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing skillLevel entity.
     *
     * @param Request    $request    The current request.
     * @param SkillLevel $skillLevel The skill level to edit.
     *
     * @return Response
     */
    public function editAction(Request $request, SkillLevel $skillLevel): Response {
        // Get the defaultLocale
        $defaultLocale = $this->container->getParameter('kernel.default_locale');

        $deleteForm = $this->createDeleteForm($skillLevel);
        $editForm = $this->createForm('Com\Nairus\ResumeBundle\Form\SkillLevelType', $skillLevel);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", $this->getTranslation("flashes.success.skill-level.edit", ["%id%" => $skillLevel->getId()], NSResumeBundle::NAME));
            return $this->redirectToRoute('skilllevel_index');
        }

        return $this->render(static::NAME . ':edit.html.twig', array(
                    'skillLevel' => $skillLevel,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'defaultLocale' => $defaultLocale,
        ));
    }

    /**
     * Deletes a skillLevel entity.
     *
     * @param Request    $request    The current request.
     * @param SkillLevel $skillLevel The skill level to delete.
     *
     * @return Response
     */
    public function deleteAction(Request $request, SkillLevel $skillLevel): Response {
        $form = $this->createDeleteForm($skillLevel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id = $skillLevel->getId();
            try {
                $this->skillLevelService->removeSkillLevel($skillLevel);
                $this->addFlash("success", $this->getTranslation("flashes.success.skill-level.delete", ["%id%" => $id], NSResumeBundle::NAME));
            } catch (FunctionalException $exc) {
                $this->addFlash("error", $this->getTranslation($exc->getTranslationKey(), ["%id%" => $id], NSResumeBundle::NAME));
                $this->logError($exc, self::NAME . ":deleteAction");
            }
        }

        return $this->redirectToRoute('skilllevel_index');
    }

    /**
     * Creates a form to delete a skillLevel entity.
     *
     * @param SkillLevel $skillLevel The skillLevel entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SkillLevel $skillLevel): \Symfony\Component\Form\Form {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('skilllevel_delete', array('id' => $skillLevel->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}

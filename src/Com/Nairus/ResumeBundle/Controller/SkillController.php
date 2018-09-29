<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\CoreBundle\Exception\PaginatorException;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Com\Nairus\ResumeBundle\Service\SkillServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Skill controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillController extends Controller {

    private const NAME = NSResumeBundle::NAME . ":Skill";

    /**
     * The skill service.
     *
     * @var SkillServiceInterface
     */
    private $skillService;

    /**
     * Trait for internationalization behaviors.
     */
    use \Com\Nairus\CoreBundle\Traits\CommonComponentsTrait;

    /**
     * Constructor.
     *
     * @param SkillServiceInterface $skillService The skill service.
     * @param LoggerInterface       $logger       The logger service.
     * @param TranslatorInterface   $translator   The translatorService.
     */
    public function __construct(SkillServiceInterface $skillService, LoggerInterface $logger, TranslatorInterface $translator) {
        $this->skillService = $skillService;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * Lists all skill entities.
     *
     * @param integer $page The current page.
     *
     * @return Response
     */
    public function indexAction($page): Response {
        // Bug chrome
        if ("" === $page) {
            $page = 1;
        }

        try {
            // Get the limit from the config file.
            $limit = $this->container->getParameter("ns_resume.skills-limit");
            $skillPaginatorDto = $this->skillService->findAllForPage($page, $limit);
        } catch (PaginatorException $exc) {
            throw new BadRequestHttpException("Bad page parameter", $exc);
        }

        // If the number of pages is less than the current page (expect for page 1).
        if ($page > 1 && $skillPaginatorDto->getPages() < $page) {
            // We throw a NotFound Exception.
            throw $this->createNotFoundException("Page [$page] does not exist!");
        }

        $items = [];
        foreach ($skillPaginatorDto->getEntities() as $entity) {
            array_push($items, ['entity' => $entity, 'deleteForm' => $this->createDeleteForm($entity)->createView()]);
        }

        return $this->render(self::NAME . ':index.html.twig', array(
                    'items' => $items,
                    'pages' => $skillPaginatorDto->getPages(),
                    'currentPage' => $skillPaginatorDto->getCurrentPage()
        ));
    }

    /**
     * Creates a new skill entity.
     *
     * @param Request $request The current request.
     *
     * @return Response
     */
    public function newAction(Request $request): Response {
        $skill = new Skill();
        $form = $this->createForm('Com\Nairus\ResumeBundle\Form\SkillType', $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($skill);
            $em->flush();

            // Add flash message
            $this->addFlash("success", $this->getTranslation("flashes.success.skill.new", [], NSResumeBundle::NAME));
            return $this->redirectToRoute('skill_show', array('id' => $skill->getId()));
        }

        return $this->render(self::NAME . ':new.html.twig', array(
                    'skill' => $skill,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a skill entity.
     *
     * @param Skill $skill The skill to display.
     *
     * @return Response
     */
    public function showAction(Skill $skill): Response {
        $deleteForm = $this->createDeleteForm($skill);

        return $this->render(self::NAME . ':show.html.twig', array(
                    'skill' => $skill,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing skill entity.
     *
     * @param Request $request The current request.
     * @param Skill $skill The skill to display.
     *
     * @return Response
     */
    public function editAction(Request $request, Skill $skill): Response {
        $editForm = $this->createForm('Com\Nairus\ResumeBundle\Form\SkillType', $skill);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            // Add flash message
            $this->addFlash("success", $this->getTranslation("flashes.success.skill.edit", ["%id%" => $skill->getId()], NSResumeBundle::NAME));
            return $this->redirectToRoute('skill_show', array('id' => $skill->getId()));
        }

        return $this->render(self::NAME . ':edit.html.twig', array(
                    'skill' => $skill,
                    'form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a skill entity.
     *
     * @param Request $request The current request.
     * @param Skill $skill The skill to delete.
     *
     * @return Response
     */
    public function deleteAction(Request $request, Skill $skill): Response {
        $form = $this->createDeleteForm($skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idDeleted = $skill->getId();

            try {
                $this->skillService->removeSkill($skill);

                // Add flash message
                $this->addFlash("success", $this->getTranslation("flashes.success.skill.delete", ["%id%" => $idDeleted], NSResumeBundle::NAME));
            } catch (FunctionalException $exc) {
                $this->addFlash("error", $this->getTranslation($exc->getTranslationKey(), ["%id%" => $idDeleted], NSResumeBundle::NAME));
                $this->logError($exc, self::NAME . ":deleteAction");
                return $this->redirectToRoute('skill_show', ['id' => $skill->getId()]);
            }
        }

        return $this->redirectToRoute('skill_index');
    }

    /**
     * Creates a form to delete a skill entity.
     *
     * @param Skill $skill The skill entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Skill $skill): Form {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('skill_delete', array('id' => $skill->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}

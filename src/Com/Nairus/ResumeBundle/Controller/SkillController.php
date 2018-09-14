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
     * Logger service.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param SkillServiceInterface $skillService The skill service.
     * @parem LoggerInterface       $logger       The logger service.
     */
    public function __construct(SkillServiceInterface $skillService, LoggerInterface $logger) {
        $this->skillService = $skillService;
        $this->logger = $logger;
    }

    /**
     * Lists all skill entities.
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
            $this->addFlash("success", $this->getTranslation("flashes.success.skill.new"));
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
     * @return Response
     */
    public function editAction(Request $request, Skill $skill): Response {
        $editForm = $this->createForm('Com\Nairus\ResumeBundle\Form\SkillType', $skill);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            // Add flash message
            $this->addFlash("success", $this->getTranslation("flashes.success.skill.edit", ["%id%" => $skill->getId()]));
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
                $this->addFlash("success", $this->getTranslation("flashes.success.skill.delete", ["%id%" => $idDeleted]));
            } catch (FunctionalException $exc) {
                $this->addFlash("error", $this->getTranslation($exc->getTranslationKey(), ["%id%" => $idDeleted]));
                $this->logError($exc, NSResumeBundle::NAME . ":deleteAction");
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

    /**
     * Return the translation of a message.
     *
     * @param string $id     The id of the translation.
     * @param array  $params The parameters for the translation.
     * @param string $domain The file domain where the translations is stored.
     *
     * @return string
     */
    private function getTranslation($id, $params = [], $domain = NSResumeBundle::NAME): string {
        return $this->get("translator")->trans($id, $params, $domain);
    }

    /**
     * Log an error
     *
     * @param \Exception $exc The exception to log.
     */
    private function logError(\Exception $exc, string $context): void {
        $this->logger->error($exc->getMessage(), [$context => $exc]);
    }

}

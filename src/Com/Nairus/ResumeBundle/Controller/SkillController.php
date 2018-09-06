<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Skill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
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
     * Lists all skill entities.
     *
     * @return Response
     */
    public function indexAction(): Response {
        $em = $this->getDoctrine()->getManager();

        $skills = $em->getRepository('NSResumeBundle:Skill')->findAll();

        return $this->render(self::NAME . ':index.html.twig', array(
                    'skills' => $skills,
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
        $deleteForm = $this->createDeleteForm($skill);
        $editForm = $this->createForm('Com\Nairus\ResumeBundle\Form\SkillType', $skill);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('skill_edit', array('id' => $skill->getId()));
        }

        return $this->render(self::NAME . ':edit.html.twig', array(
                    'skill' => $skill,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
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
            $em = $this->getDoctrine()->getManager();
            $em->remove($skill);
            $em->flush();
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

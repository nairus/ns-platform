<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\SkillLevel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Skilllevel controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SkillLevelController extends Controller {

    private const NAME = NSResumeBundle::NAME . ":SkillLevel";

    /**
     * Lists all skillLevel entities.
     *
     * @return Response
     */
    public function indexAction(): Response {
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
     * @return Response
     */
    public function newAction(Request $request): Response {
        $skillLevel = new Skilllevel();
        $form = $this->createForm('Com\Nairus\ResumeBundle\Form\SkillLevelType', $skillLevel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($skillLevel);
            $em->flush();

            return $this->redirectToRoute('skilllevel_index');
        }

        return $this->render(static::NAME . ':new.html.twig', array(
                    'skillLevel' => $skillLevel,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a skillLevel entity.
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
     * @return Response
     */
    public function editAction(Request $request, SkillLevel $skillLevel): Response {
        $deleteForm = $this->createDeleteForm($skillLevel);
        $editForm = $this->createForm('Com\Nairus\ResumeBundle\Form\SkillLevelType', $skillLevel);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('skilllevel_index');
        }

        return $this->render(static::NAME . ':edit.html.twig', array(
                    'skillLevel' => $skillLevel,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a skillLevel entity.
     *
     * @return Response
     */
    public function deleteAction(Request $request, SkillLevel $skillLevel): Response {
        $form = $this->createDeleteForm($skillLevel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($skillLevel);
            $em->flush();
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

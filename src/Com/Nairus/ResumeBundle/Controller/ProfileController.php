<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Profile;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Form\ProfileType;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Profile controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ProfileController extends Controller {

    private const NAME = NSResumeBundle::NAME . ":Profile";

    /**
     * Traits for internationalization behaviors and security check.
     */
    use \Com\Nairus\CoreBundle\Traits\CommonComponentsTrait;
    use \Com\Nairus\ResumeBundle\Traits\ResumeSecurityTrait;

    /**
     * Constructor.
     *
     * @param LoggerInterface     $logger     The logger service.
     * @param TranslatorInterface $translator The translatorService.
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator) {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * Creates a new profile entity.
     *
     * @ParamConverter("resume", options={"mapping": {"resume_id": "id"}})
     */
    public function newAction(Request $request, Resume $resume): Response {
        $this->check($resume, $this->getUser());
        $profile = new Profile();
        $profile->setUser($this->getUser());
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($profile);
            $em->flush();

            $this->addFlash("success", $this->getTranslation("flashes.success.profile.new", [], NSResumeBundle::NAME));

            return $this->redirectToRoute('resume_show', [
                        'id' => $resume->getId()
            ]);
        }

        return $this->render(self::NAME . ':new.html.twig', [
                    'profile' => $profile,
                    'resume_id' => $resume->getId(),
                    'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing profile entity.
     *
     * @ParamConverter("resume", options={"mapping": {"resume_id": "id"}})
     */
    public function editAction(Request $request, Profile $profile, Resume $resume): Response {
        $this->check($resume, $this->getUser());
        $deleteForm = $this->createDeleteForm($profile, $resume);
        $editForm = $this->createForm(ProfileType::class, $profile);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", $this->getTranslation("flashes.success.profile.edit", [], NSResumeBundle::NAME));

            return $this->redirectToRoute('resume_show', ['id' => $resume->getId()]);
        }

        return $this->render(self::NAME . ':edit.html.twig', [
                    'profile' => $profile,
                    'resume_id' => $resume->getId(),
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a profile entity.
     *
     * @ParamConverter("resume", options={"mapping": {"resume_id": "id"}})
     */
    public function deleteAction(Request $request, Profile $profile, Resume $resume): Response {
        $this->check($resume, $this->getUser());
        $form = $this->createDeleteForm($profile, $resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($profile);
            $em->flush();

            $this->addFlash("success", $this->getTranslation("flashes.success.profile.delete", [], NSResumeBundle::NAME));
        }

        return $this->redirectToRoute('resume_show', ["id" => $resume->getId()]);
    }

    /**
     * Creates a form to delete a profile entity.
     *
     * @param Profile $profile The profile entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Profile $profile, Resume $resume): \Symfony\Component\Form\Form {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('profile_delete', [
                                    'id' => $profile->getId(),
                                    'resume_id' => $resume->getId()
                        ]))
                        ->setMethod(Request::METHOD_DELETE)
                        ->getForm()
        ;
    }

}

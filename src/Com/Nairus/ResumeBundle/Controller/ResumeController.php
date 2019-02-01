<?php

namespace Com\Nairus\ResumeBundle\Controller;

use Com\Nairus\CoreBundle\Exception\FunctionalException;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Resume;
use Com\Nairus\ResumeBundle\Entity\Profile;
use Com\Nairus\ResumeBundle\Enums\ResumeStatusEnum;
use Com\Nairus\ResumeBundle\Exception as NSResumeException;
use Com\Nairus\ResumeBundle\Form\ResumeType;
use Com\Nairus\ResumeBundle\Service\ResumeServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Resume restricted controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ResumeController extends Controller {

    private const NAME = NSResumeBundle::NAME . ":Resume";

    /**
     * Resume service.
     *
     * @var ResumeServiceInterface
     */
    private $resumeService;

    /**
     * Trait for internationalization behaviors.
     */
    use \Com\Nairus\CoreBundle\Traits\CommonComponentsTrait;

/**
     * Trait for security check.
     */
    use \Com\Nairus\ResumeBundle\Traits\ResumeSecurityTrait;

    /**
     * Constructor.
     *
     * @param ResumeServiceInterface $resumeService The resume service instance.
     * @param LoggerInterface        $logger        The logger service.
     * @param TranslatorInterface    $translator    The translatorService.
     */
    public function __construct(ResumeServiceInterface $resumeService, LoggerInterface $logger, TranslatorInterface $translator) {
        $this->resumeService = $resumeService;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * Lists all resume entities for an user.
     *
     * @param Request $request The current request.
     *
     * @return Response
     */
    public function indexAction(Request $request): Response {
        // Get the defaultLocale
        $defaultLocale = $this->container->getParameter('kernel.default_locale');
        // Get the dates format
        $datesFormat = $this->container->getParameter("dates_format");

        $em = $this->getDoctrine()->getManager();

        $resumes = $em->getRepository(self::NAME)->findBy(['author' => $this->getUser()]);
        $items = [];
        foreach ($resumes as /* @var $resume Resume */ $resume) {
            $datas = [
                'entity' => $resume,
                'statusIcon' => ResumeStatusEnum::getIconClass($resume->getStatus()),
                'statusKey' => ResumeStatusEnum::getLabelKey($resume->getStatus()),
                'delete_form' => $this->createDeleteForm($resume)->createView(),
            ];

            if (ResumeStatusEnum::ONLINE === $resume->getStatus()) {
                $datas['unpublish_form'] = $this->createUnpublishForm($resume)->createView();
            } else {
                $datas['publish_form'] = $this->createPublishForm($resume)->createView();
            }

            array_push($items, $datas);
        }

        return $this->render(self::NAME . ':index.html.twig', [
                    'items' => $items,
                    'dateFormat' => $datesFormat[$request->getLocale()],
                    'defaultLocale' => $defaultLocale
        ]);
    }

    /**
     * Creates a new resume entity.
     *
     * @param Request $request The current request.
     *
     * @return Response
     */
    public function newAction(Request $request): Response {
        $resume = new Resume();
        $resume
                ->setIp($request->getClientIp())
                ->setAuthor($this->getUser());
        $form = $this->createForm(ResumeType::class, $resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($resume);
            $em->flush();

            $this->addFlash("success", $this->getTranslation("flashes.success.resume.new", [], NSResumeBundle::NAME));

            return $this->redirectToRoute('resume_show', array('id' => $resume->getId()));
        }

        // Get the defaultLocale
        $defaultLocale = $this->container->getParameter('kernel.default_locale');
        return $this->render(self::NAME . ':new.html.twig', array(
                    'resume' => $resume,
                    'form' => $form->createView(),
                    'defaultLocale' => $defaultLocale,
        ));
    }

    /**
     * Finds and displays a resume entity.
     *
     * @param Request $request The current request.
     * @param Resume  $resume  The resume to display.
     *
     * @return Response
     */
    public function showAction(Request $request, Resume $resume): Response {
        // Check security.
        $this->check($resume, $this->getUser());

        // Get the defaultLocale
        $defaultLocale = $this->container->getParameter('kernel.default_locale');
        // Get the dates format
        $datesFormat = $this->container->getParameter("dates_format");

        $deleteForm = $this->createDeleteForm($resume);

        // If the resume is not anonymous, we try to get the profile.
        $profile = null;
        if (!$resume->getAnonymous()) {
            $em = $this->getDoctrine()->getManager();
            $profile = $em->getRepository(Profile::class)->findOneByUser($this->getUser());
        }

        $parameters = ['resume' => $resume,
            'delete_form' => $deleteForm->createView(),
            'statusIcon' => ResumeStatusEnum::getIconClass($resume->getStatus()),
            'statusKey' => ResumeStatusEnum::getLabelKey($resume->getStatus()),
            'defaultLocale' => $defaultLocale,
            'dateFormat' => $datesFormat[$request->getLocale()],
            'profile' => $profile
        ];

        // If a profile is found, we generate the delete form.
        if ($profile) {
            $parameters["profile_delete_form"] = $this->createFormBuilder()
                            ->setAction($this->generateUrl('profile_delete', [
                                        'id' => $profile->getId(),
                                        'resume_id' => $resume->getId()
                            ]))
                            ->setMethod(Request::METHOD_DELETE)
                            ->getForm()->createView();
        }

        if (ResumeStatusEnum::ONLINE === $resume->getStatus()) {
            $parameters['unpublish_form'] = $this->createUnpublishForm($resume)->createView();
        } else {
            $parameters['publish_form'] = $this->createPublishForm($resume)->createView();
        }

        return $this->render(self::NAME . ':show.html.twig', $parameters);
    }

    /**
     * Displays a form to edit an existing resume entity.
     *
     * @param Request $request The current request.
     * @param Resume  $resume  The resume to edit.
     *
     * @return Response
     */
    public function editAction(Request $request, Resume $resume): Response {
        $this->check($resume, $this->getUser());
        $deleteForm = $this->createDeleteForm($resume);
        $editForm = $this->createForm(ResumeType::class, $resume);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", $this->getTranslation("flashes.success.resume.edit", ["%id%" => $resume->getId()], NSResumeBundle::NAME));

            return $this->redirectToRoute('resume_show', array('id' => $resume->getId()));
        }

        return $this->render(self::NAME . ':edit.html.twig', array(
                    'resume' => $resume,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a resume entity.
     *
     * @param Request $request The current request.
     * @param Resume  $resume  The resume to delete.
     *
     * @return Response
     */
    public function deleteAction(Request $request, Resume $resume): Response {
        $this->check($resume, $this->getUser());
        $form = $this->createDeleteForm($resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resumeId = $resume->getId();
            try {
                $this->resumeService->removeWithDependencies($resume);
                $this->addFlash("success", $this->getTranslation("flashes.success.resume.delete", ["%id%" => $resumeId], NSResumeBundle::NAME));
            } catch (FunctionalException $exc) {
                $this->logError($exc, self::NAME . ":deleteAction");
                $this->addFlash("error", $this->getTranslation($exc->getTranslationKey(), ["%id%" => $resumeId], NSResumeBundle::NAME));
            } catch (\Throwable $exc) {
                $this->logError($exc, self::NAME . ':deleteAction');
                $this->addFlash("error", $this->getTranslation('flashes.error.unknown', ['%id%' => $resume->getId()], NSResumeBundle::NAME));
            }
        }

        return $this->redirectToRoute('resume_index');
    }

    /**
     * Publish a resume.
     *
     * @param Request $request The current request.
     * @param Resume  $resume  The resume to publish.
     *
     * @return Response
     */
    public function publishAction(Request $request, Resume $resume): Response {
        $this->check($resume, $this->getUser());
        $form = $this->createPublishForm($resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $force = array_key_exists('force', $request->get('form')) && '1' === $request->get('form')['force'];
                $this->resumeService->publish($resume, $force);
                $this->addFlash('success', $this->getTranslation('flashes.success.resume.published', ['%id%' => $resume->getId()], NSResumeBundle::NAME));
            } catch (NSResumeException\ResumePublicationException $exc) {
                return $this->manageResumePublicationException($resume, $exc);
            } catch (\Throwable $exc) {
                $this->logError($exc, self::NAME . ':publishAction');
                $this->addFlash("error", $this->getTranslation('flashes.error.unknown', ['%id%' => $resume->getId()], NSResumeBundle::NAME));
            }
        }

        // For other case we go the resume list.
        return $this->redirectToRoute('resume_index');
    }

    /**
     * Unpublish a resume.
     *
     * @param Request $request The current request.
     * @param Resume  $resume  The resume to publish.
     *
     * @return Response
     */
    public function unpublishAction(Request $request, Resume $resume): Response {
        $this->check($resume, $this->getUser());
        $form = $this->createUnpublishForm($resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->resumeService->unpublish($resume);
                $this->addFlash('success', $this->getTranslation('flashes.success.resume.unpublished', ['%id%' => $resume->getId()], NSResumeBundle::NAME));
            } catch (\Throwable $exc) {
                $this->logError($exc, self::NAME . ':unpublishAction');
                $this->addFlash("error", $this->getTranslation('flashes.error.unknown', ['%id%' => $resume->getId()], NSResumeBundle::NAME));
            }
        }

        // For other case we go the resume list.
        return $this->redirectToRoute('resume_index');
    }

    /**
     * Creates a form to delete a resume entity.
     *
     * @param Resume $resume The resume entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Resume $resume): Form {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('resume_delete', ['id' => $resume->getId()]))
                        ->setMethod(Request::METHOD_DELETE)
                        ->getForm()
        ;
    }

    /**
     * Create the publish resume form.
     *
     * @param Resume $resume
     *
     * @return Form
     */
    private function createPublishForm(Resume $resume): Form {
        return $this->createFormBuilder(null, ['allow_extra_fields' => true])
                        ->setAction($this->generateUrl('resume_publish', ['id' => $resume->getId()]))
                        ->setMethod(Request::METHOD_PATCH)
                        ->getForm();
    }

    /**
     * Create the unpublish resume form.
     *
     * @param Resume $resume
     *
     * @return Form
     */
    private function createUnpublishForm(Resume $resume): Form {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('resume_unpublish', ['id' => $resume->getId()]))
                        ->setMethod(Request::METHOD_PATCH)
                        ->getForm();
    }

    /**
     * Manage resume publication error.
     *
     * @param Resume $resume The current resume.
     * @param \Com\Nairus\ResumeBundle\Exception\ResumePublicationException $exc The exception to manage
     *
     * @return Reponse
     */
    private function manageResumePublicationException(Resume $resume, NSResumeException\ResumePublicationException $exc): Response {
        // Add error message.
        $this->addFlash("error", $this->getTranslation($exc->getTranslationKey(), ['%id%' => $resume->getId()], NSResumeBundle::NAME));

        // If the resume is incomplete, we render the page with "force" hidden extra field.
        if ($exc instanceof NSResumeException\ResumeIncompleteException) {
            $formForced = $this->createPublishForm($resume);
            $formForced->add("force", HiddenType::class, ['data' => true]);
            return $this->render(self::NAME . ':publish.html.twig', [
                        'resume' => $resume,
                        'publish_form' => $formForced->createView()]);
        }

        // For other case we redirect to show page.
        return $this->redirectToRoute('resume_show', ['id' => $resume->getId()]);
    }

}

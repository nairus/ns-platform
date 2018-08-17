<?php

namespace Com\Nairus\CoreBundle\Controller;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Entity\News;
use Com\Nairus\CoreBundle\Entity\NewsContent;
use Com\Nairus\CoreBundle\Service\NewsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * News controller.
 *
 */
class NewsController extends Controller {

    private const NAME = NSCoreBundle::NAME . ":News";

    /**
     * @var NewsServiceInterface
     */
    private $newsService;

    /**
     * Constructor.
     *
     * @param NewsServiceInterface $newsService News service.
     */
    public function __construct(NewsServiceInterface $newsService) {
        $this->newsService = $newsService;
    }

    /**
     * Lists all news entities.
     *
     */
    public function indexAction($page): Response {
        // Bug chrome
        if ("" === $page) {
            $page = 1;
        }

        // Get the news in database.
        $limit = $this->container->getParameter("ns_core.news_limit");
        $newsPaginationDto = $this->newsService->findNewsForPage($page, $limit);

        // Render the view.
        return $this->render(static::NAME . ':index.html.twig', [
                    'newsList' => $newsPaginationDto->getEntities(),
                    'missingTranslations' => $newsPaginationDto->getMissingTranslations(),
                    'currentPage' => $newsPaginationDto->getCurrentPage(),
                    'pages' => $newsPaginationDto->getPages()
        ]);
    }

    /**
     * Creates a new news entity.
     *
     */
    public function newAction(Request $request): Response {
        // Get the default locale.
        $defaultLocale = $this->container->getParameter('kernel.default_locale');
        $news = new News();
        $content = new NewsContent();
        $content->setLocale($defaultLocale);
        $content->setNews($news);
        $news->addContent($content);
        $form = $this->createForm('Com\Nairus\CoreBundle\Form\NewsContentType', $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($news);
                $em->flush();

                // Add success flash message
                $request->getSession()->getFlashBag()->add(
                        'success',
                        $this->getTranslator()->trans("flashes.success.news.add", [], NSCoreBundle::NAME)
                );
            } catch (\Exception $exc) {
                // Log the error.
                $this->logError($exc, self::NAME . ":add");

                // Add error flash message.
                $request->getSession()->getFlashBag()->add(
                        'error',
                        $this->getTranslator()->trans("flashes.error.news.add", [], NSCoreBundle::NAME)
                );
            } finally {
                return $this->redirectToRoute('news_show', ['id' => $news->getId()]);
            }
        }

        return $this->render(static::NAME . ':new.html.twig', [
                    'news' => $news,
                    'locale' => $defaultLocale,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a news entity.
     *
     */
    public function showAction(News $news): Response {
        // Get the default content.
        $locale = $this->container->getParameter('kernel.default_locale');
        $newsContent = $this->newsService->findContentForNewsId($news, $locale);
        $deleteForm = $this->createDeleteForm($news);

        return $this->render(static::NAME . ':show.html.twig', [
                    'news' => $news,
                    'newsContent' => $newsContent,
                    'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing news entity.
     *
     */
    public function editAction(Request $request, News $news): Response {
        // Get the default content.
        $locale = $this->container->getParameter('kernel.default_locale');
        $newsContent = $this->newsService->findContentForNewsId($news, $locale);

        if (null === $newsContent) {
            $newsContent = new NewsContent();
            $newsContent->setLocale($locale);
            $newsContent->setNews($news);
        }
        $news->addContent($newsContent);

        $editForm = $this->createForm('Com\Nairus\CoreBundle\Form\NewsContentType', $newsContent);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();

                // Add success flash message.
                $request->getSession()->getFlashBag()->add(
                        'success',
                        $this->getTranslator()->trans("flashes.success.news.edit", ["%news_id%" => $news->getId()], NSCoreBundle::NAME)
                );
            } catch (\Exception $exc) {
                $this->logError($exc, NSCoreBundle::NAME . ":edit");

                // Add error flash message.
                $request->getSession()->getFlashBag()->add(
                        'error',
                        $this->getTranslator()->trans("flashes.error.news.edit", ["%news_id%" => $news->getId()], NSCoreBundle::NAME)
                );
            } finally {
                return $this->redirectToRoute('news_show', ['id' => $news->getId()]);
            }
        }

        return $this->render(static::NAME . ':edit.html.twig', [
                    'news' => $news,
                    'locale' => $locale,
                    'form' => $editForm->createView(),
        ]);
    }

    /**
     * Deletes a news entity.
     *
     */
    public function deleteAction(Request $request, News $news): Response {
        $form = $this->createDeleteForm($news);
        $form->handleRequest($request);
        $news_id = $news->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($news);
                $em->flush();

                // Add success flash message.
                $request->getSession()->getFlashBag()->add(
                        'success',
                        $this->getTranslator()->trans("flashes.success.news.delete", ["%news_id%" => $news_id], NSCoreBundle::NAME)
                );
            } catch (\Exception $exc) {
                $this->logError($exc, static::NAME . ":delete");

                // Add error flash message.
                $request->getSession()->getFlashBag()->add(
                        'error',
                        $this->getTranslator()->trans("flashes.error.news.delete", ["%news_id%" => $news_id], NSCoreBundle::NAME)
                );
            }
        }

        return $this->redirectToRoute('news_index');
    }

    /**
     * Manage news translation action.
     *
     * @param News $news     The current news.
     * @param string $locale The locale to manage.
     *
     * @return Response
     */
    public function translationAction(Request $request, News $news, string $locale): Response {
        // Try to find the translation.
        try {
            $newsContent = $this->newsService->findContentForNewsId($news, $locale);
        } catch (\Com\Nairus\CoreBundle\Exception\LocaleError $exc) {
            $this->logError($exc, static::NAME . ":translation");
            throw $this->createNotFoundException();
        }

        // Init variables for edit mode.
        $pageTitle = "news.translation.title.edit";
        $successFlashMessage = "flashes.success.news.content.edit";
        $errorFlashMessage = "flashes.error.news.content.edit";
        $originalContent = null;

        // If this is add mode
        if (null === $newsContent) {
            // We get the first translation to display
            $originalContent = $news->getContents()->first();

            // We create a news content with the current locale.
            $newsContent = new NewsContent();
            $newsContent
                    ->setNews($news)
                    ->setLocale($locale);
            $pageTitle = "news.translation.title.add";
            $successFlashMessage = "flashes.success.news.content.add";
            $errorFlashMessage = "flashes.error.news.content.add";
        }
        $news->addContent($newsContent);

        $form = $this->createForm('Com\Nairus\CoreBundle\Form\NewsContentType', $newsContent);

        // Remove published field (to use in new and edit page).
        $form->get("news")->remove("published");

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();

                // Add success flash message.
                $request->getSession()->getFlashBag()->add(
                        'success',
                        $this->getTranslator()->trans($successFlashMessage,
                                ["%news_id%" => $news->getId(), "%locale%" => $locale], NSCoreBundle::NAME)
                );
            } catch (\Exception $exc) {
                $this->logError($exc, NSCoreBundle::NAME . ":translation");

                // Add error flash message.
                $request->getSession()->getFlashBag()->add(
                        'error',
                        $this->getTranslator()->trans($errorFlashMessage,
                                ["%news_id%" => $news->getId(), "%locale%" => $locale], NSCoreBundle::NAME)
                );
            } finally {
                return $this->redirectToRoute('news_show', ['id' => $news->getId()]);
            }
        }

        return $this->render(static::NAME . ":translation.html.twig", [
                    "news" => $news,
                    "originalContent" => $originalContent,
                    "locale" => $locale,
                    "form" => $form->createView(),
                    "pageTitle" => $pageTitle
        ]);
    }

    /**
     * Publish a news.
     *
     * @param Request $request The HTTP request.
     * @param News    $news    The current news.
     *
     * @return Response
     */
    public function publishAction(Request $request, News $news): Response {

        try {
            $news->setPublished(true);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("ns_core_homepage");
        } catch (\Exception $exc) {
            $this->logError($exc, NSCoreBundle::NAME . ":publish");

            // Add error flash message.
            $request->getSession()->getFlashBag()->add(
                    'error',
                    $this->getTranslator()->trans("flashes.error.news.edit",
                            ["%id%" => $news->getId()], NSCoreBundle::NAME)
            );
            return $this->redirectToRoute('news_index');
        }
    }

    /**
     * Creates a form to delete a news entity.
     *
     * @param News $news The news entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(News $news) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('news_delete', array('id' => $news->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * Return the translation service.
     *
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    private function getTranslator() {
        return $this->get("translator");
    }

    /**
     * Log an error
     *
     * @param \Exception $exc The exception to log.
     */
    private function logError(\Exception $exc, string $context): void {
        /* @var $logger \Psr\Log\LoggerInterface */
        $logger = $this->container->get("logger");
        $logger->error($exc->getMessage(), [$context => $exc]);
    }

}

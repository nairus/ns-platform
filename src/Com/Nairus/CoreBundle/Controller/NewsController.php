<?php

namespace Com\Nairus\CoreBundle\Controller;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Entity\News;
use Com\Nairus\CoreBundle\Entity\NewsContent;
use Com\Nairus\CoreBundle\Exception\PaginatorException;
use Com\Nairus\CoreBundle\Service\NewsServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * News controller.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class NewsController extends Controller {

    use \Com\Nairus\CoreBundle\Traits\CommonComponentsTrait;

    private const NAME = NSCoreBundle::NAME . ":News";

    /**
     * @var NewsServiceInterface
     */
    private $newsService;

    /**
     * Constructor.
     *
     * @param NewsServiceInterface $newsService News service.
     * @param LoggerInterface      $logger      Logger service.
     * @param TranslatorInterface  $translator  Translator service.
     */
    public function __construct(NewsServiceInterface $newsService, LoggerInterface $logger, TranslatorInterface $translator) {
        $this->newsService = $newsService;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * Lists all news entities.
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
            // Get the news in database.
            $limit = $this->container->getParameter("ns_core.news_limit");
            $newsPaginationDto = $this->newsService->findNewsForPage($page, $limit);

            // If the number of page is wrong, we throw a 404 error
            if ($page > 1 && $page > $newsPaginationDto->getPages()) {
                throw $this->createNotFoundException();
            }
        } catch (PaginatorException $exc) {
            throw new BadRequestHttpException("Bad page parameter", $exc);
        }

        $items = [];
        foreach ($newsPaginationDto->getEntities() as $news) {
            array_push($items, ['entity' => $news, 'deleteForm' => $this->createDeleteForm($news)->createView()]);
        }

        // Render the view.
        return $this->render(static::NAME . ':index.html.twig', [
                    'items' => $items,
                    'missingTranslations' => $newsPaginationDto->getMissingTranslations(),
                    'currentPage' => $newsPaginationDto->getCurrentPage(),
                    'pages' => $newsPaginationDto->getPages()
        ]);
    }

    /**
     * Creates a new news entity.
     *
     * @param Request $request The current request.
     *
     * @return Response
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
                $this->addFlash('success', $this->getTranslation("flashes.success.news.add"));
            } catch (\Throwable $exc) {
                // Log the error.
                $this->logError(static::NAME, "addAction", $exc);

                // Add error flash message.
                $this->addFlash('error', $this->getTranslation("flashes.error.news.add"));
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
     * @param News $news The news to display.
     *
     * @return Response
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
     * @param Request $request The current request.
     * @param News    $news    The news to edit.
     *
     * @return Response
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
            $transParams = ["%news_id%" => $news->getId()];
            try {
                $this->getDoctrine()->getManager()->flush();

                // Add success flash message.
                $this->addFlash('success', $this->getTranslation("flashes.success.news.edit", $transParams));
            } catch (\Throwable $exc) {
                $this->logError(static::NAME, "editAction", $exc);

                // Add error flash message.
                $this->addFlash('error', $this->getTranslation("flashes.error.news.edit", $transParams));
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
     * @param Request $request The current request.
     * @param News    $news    The news to delete.
     *
     * @return Response
     */
    public function deleteAction(Request $request, News $news): Response {
        $form = $this->createDeleteForm($news);
        $form->handleRequest($request);
        $news_id = $news->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $transParams = ["%news_id%" => $news_id];
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($news);
                $em->flush();

                // Add success flash message.
                $this->addFlash('success', $this->getTranslation("flashes.success.news.delete", $transParams));
            } catch (\Throwable $exc) {
                $this->logError(static::NAME, "deleteAction", $exc);

                // Add error flash message.
                $this->addFlash('error', $this->getTranslation("flashes.error.news.delete", $transParams));
            }
        }

        return $this->redirectToRoute('news_index');
    }

    /**
     * Manage news translation action.
     *
     * @param Request $request The current request.
     * @param News    $news    The current news.
     * @param string  $locale  The locale to manage.
     *
     * @return Response
     */
    public function translationAction(Request $request, News $news, string $locale): Response {
        // Try to find the translation.
        try {
            $newsContent = $this->newsService->findContentForNewsId($news, $locale);
        } catch (\Com\Nairus\CoreBundle\Exception\LocaleError $exc) {
            $this->logError(static::NAME, "translationAction", $exc);
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
        $form->remove("news");

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $transParams = ["%news_id%" => $news->getId(), "%locale%" => $locale];
            try {
                $this->getDoctrine()->getManager()->flush();

                // Add success flash message.
                $this->addFlash('success', $this->getTranslation($successFlashMessage, $transParams));
            } catch (\Throwable $exc) {
                $this->logError(static::NAME, "translationAction", $exc);

                // Add error flash message.
                $this->addFlash('error', $this->getTranslation($errorFlashMessage, $transParam));
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
     * @param News $news The current news.
     *
     * @return Response
     */
    public function publishAction(News $news): Response {

        try {
            $news->setPublished(true);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("ns_core_homepage");
        } catch (\Throwable $exc) {
            $this->logError(static::NAME, "publishAction", $exc);

            // Add error flash message.
            $this->addFlash('error', $this->getTranslation("flashes.error.news.edit", ["%id%" => $news->getId()]));
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
                        ->setMethod(Request::METHOD_DELETE)
                        ->getForm()
        ;
    }

}

<?php

namespace Com\Nairus\CoreBundle\Controller;

use Com\Nairus\CoreBundle\NSCoreBundle;
use Com\Nairus\CoreBundle\Entity\News;
use Com\Nairus\CoreBundle\Entity\NewsContent;
use Com\Nairus\CoreBundle\Service\NewsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $news = $em->getRepository(static::NAME)->findAll();

        return $this->render(static::NAME . ':index.html.twig', array(
                    'newsList' => $news,
        ));
    }

    /**
     * Creates a new news entity.
     *
     */
    public function newAction(Request $request) {
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();

            return $this->redirectToRoute('news_show', array('id' => $news->getId()));
        }

        return $this->render(static::NAME . ':new.html.twig', array(
                    'news' => $news,
                    'locale' => $defaultLocale,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a news entity.
     *
     */
    public function showAction(News $news) {
        $deleteForm = $this->createDeleteForm($news);

        return $this->render(static::NAME . ':show.html.twig', array(
                    'news' => $news,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing news entity.
     *
     */
    public function editAction(Request $request, News $news) {
        // Get the default content.
        $locale = $this->container->getParameter('kernel.default_locale');
        $newsContent = $this->newsService->findContentForNewsId($news, $locale);

        if (null === $newsContent) {
            $newsContent = new NewsContent();
            $newsContent->setLocale($locale);
            $newsContent->setNews($news);
        }
        $news->addContent($newsContent);

        // Create delete form
        $deleteForm = $this->createDeleteForm($news);
        $editForm = $this->createForm('Com\Nairus\CoreBundle\Form\NewsContentType', $newsContent);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('news_show', array('id' => $news->getId()));
        }

        return $this->render(static::NAME . ':edit.html.twig', array(
                    'news' => $news,
                    'locale' => $locale,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a news entity.
     *
     */
    public function deleteAction(Request $request, News $news) {
        $form = $this->createDeleteForm($news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($news);
            $em->flush();
        }

        return $this->redirectToRoute('news_index');
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

}

<?php

namespace App\Controller;

use App\Entity\Article;

use App\Form\ArticleType;

use App\Service\AuthorizationService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Repository\ArticleRepository;

/**
 * @Route("/articles", name="articles")
 */
class ArticleController extends AbstractController
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/", name="_list", methods={"GET"})
     */
    public function index(AuthorizationService $authorizationService): Response
    {
        $articles = $this->articleRepository->findAll();
        $isAllowedToDelete = $authorizationService->isUserAllowedToDeleteArticle($this->getUser());

        return $this->render('articles/list.html.twig', [
            'articles' => $articles,
            'canDelete' => $isAllowedToDelete
        ]);
    }

    /**
     * @Route("/show/{id}", name="_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/new", name="_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $this->articleRepository->save($article);

            return $this->redirectToRoute('articles_list');
        }

        return $this->render('articles/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="_edit", methods={"GET", "POST"})
     */
    public function edit(Article $article,
                         Request $request): Response
    {
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $this->articleRepository->save($article);

            return $this->redirectToRoute('articles_list');
        }

        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="_delete", methods={"GET"})
     */
    public function delete(Article $article, AuthorizationService $authorizationService): Response
    {
        if ($authorizationService->isUserAllowedToDeleteArticle($this->getUser())) {
            $this->articleRepository->delete($article);
        }
        return $this->redirectToRoute('articles_list');
    }
}

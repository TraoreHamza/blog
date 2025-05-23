<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ArticleController extends AbstractController
{
    #[Route('/list', name: 'article', methods: ['GET'])]
    public function index(
        ArticleRepository $ar, // Repository de l'entité Article
        PaginatorInterface $paginator, // Classe pour la fonctionnalité de pagination
        Request $request // Classe epour recuperer les parametres de la requete HTTP
    ): Response {
        // Récupération de tous les articles
        $all = $ar->findBy([
            'isPublished' => true, // On ne veut que les articles publiés
            'isArchived' => false// On ne veut pas les articles archivés
        ], ['title' => 'ASC'],);
        $pagination = $paginator->paginate(
            $all,
            $request->query->getInt('page', 1),
            12
        );

        // parameters to template
        return $this->render('article/index.html.twig', [
            'controller_name' => 'INDEX',
            'articles' => $pagination
        ]);
    }
    // Route "/article/{slug}" menent à la page d'un article
    #[Route('/{slug}', name: 'article', methods: ['GET'])]
    public function view(): Response
    {
        return $this->render('article/view.html.twig', [
            //'articles' => $article
        ]);
    }

    // Route "/article/{slug}/edit" menent à la page de modification d'un article
    #[Route('/{slug}/edit', name: 'article_edit', methods: ['GET', 'POST'])]
    public function edit(): Response
    {
        return $this->render('article/edit.html.twig', [
            //'articles' => $article
        ]);
    }

    // Route "/article/{slug}/delete" menent à la page de suppression d'un article
    #[Route('/{slug}/delete', name: 'article_delete', methods: ['GET'])]
    public function delete(): Response
    {
        return $this->render('article/delete.html.twig', [
            //'articles' => $article
        ]);
    }
}

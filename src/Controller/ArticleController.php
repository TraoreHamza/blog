<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ArticleController extends AbstractController
{
    /**
     * Le constructeur permet de déclarer les dépendances une fois 
     * et d'éviter la non-application du concept DRY (Don't Repeat Yourself)
     */
    public function __construct(
        private ArticleRepository $ar, // Repository de l'entité Article
        private EntityManagerInterface $em // Gestionnaire d'entité avec Doctrine
    ){}
    
    // Route "/article" menent à la liste des articles
    #[Route('s', name: 'article', methods: ['GET'])]
    public function index(
        PaginatorInterface $paginator, // Classe pour la fonctionnalité de pagination
        Request $request // Classe epour recuperer les parametres de la requete HTTP
    ): Response {
        // Récupération de tous les articles
        $all = $this->ar->findBy([
            'isPublished' => true, // On ne veut que les articles publiés
            'isArchived' => false// On ne veut pas les articles archivés
        ], ['id' => 'DESC'],);
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
    public function view(string $slug): Response {
        return $this->render('article/view.html.twig', [
            'article' => $this->ar->findOneBySlug($slug),
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

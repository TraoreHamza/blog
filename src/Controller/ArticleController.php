<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{
    #[Route('/list', name: 'article', methods: ['GET'])]
    public function index(
        ArticleController $ar, // Repository de l'entité Article
         $paginator, // Classe pour la fonctionnalité de pagination
        Request $request // Classe epour recuperer les parametres de la requete HTTP
    ): Response {
        $all   = $ar->findAll(); // Récupération de tous les articles
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
}

<?php

namespace App\Controller;

use App\Form\ArticleForm;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/article')]
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
    #[Route('s', name: 'articles', methods: ['GET'])]
    public function index(
        PaginatorInterface $paginator, // Classe pour la fonctionnalité de pagination
        Request $request // Classe epour recuperer les parametres de la requete HTTP
    ): Response {
        // Récupération de tous les articles
        $all = $this->ar->findBy([
            'is_published' => true, // On ne veut que les articles publiés
            'is_archived' => false// On ne veut pas les articles archivés
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

  // Route "/article/{slug}/edit" menant à la modification d'un article
    #[Route('/{slug}/edit', name: 'article_edit', methods: ['GET', 'POST'])]
    public function edit(string $slug, Request $request): Response
    {
        $article = $this->ar->findOneBySlug($slug); // Récupération de l'article

        if (!$article) {
            $this->addFlash('error', "L'article n'existe pas");
            return $this->redirectToRoute('articles');
        }

        $form = $this->createForm(ArticleForm::class, $article); // Mise en place du formulaire
        $form->handleRequest($request); // Traitement de la requête

        if ($form->isSubmitted() && $form->isValid()) // Si le form est soumis et valide
        {
            try {
                $this->em->persist($article); // Enregistrement de l'article (query SQL)
                $this->em->flush($article); // Exécution de l'enregistrement en BDD
                $this->addFlash('success', 'Modification bien prise en compte'); // Message Flash Success
            } catch (\Throwable $th) {
                $this->addFlash('error', 'La modification a rencontré une erreur'); // Message Flash Error
            }

            // Redirection vers l'article modifié
            return $this->redirectToRoute('article', ['slug' => $slug]);
        }

        return $this->render('article/edit.html.twig', [
            'articleForm' => $form, // Envoi du formulaire à la vue
            'article' => $article
        ]);
    }
       // Route "/article/{slug}/publish" pour publier un article
    #[Route('/{slug}/publish', name: 'article_publish', methods: ['GET'])]
    public function publish(string $slug): Response
    {
        $article = $this->ar->findOneBySlug($slug); // Récupération de l'article

        if (!$article) { // Ce sera ignorer si l'article existe
            $this->addFlash('error', "L'article n'existe pas");
            return $this->redirectToRoute('articles');
        }

        if ($article->isPublished()) { // Si l'article est déjà publié
            $article->setIsPublished(false); // On le met en brouillon
        } else { // Sinon
            $article->setIsPublished(true); // On le met en public
        }

        $this->em->persist($article); // Enregistrement de l'article (query SQL)
        $this->em->flush($article); // Exécution de l'enregistrement en BDD
        
        // On créer un message flash
        $this->addFlash('success', $article->isPublished() ? "Article publié" : "Mis en brouillon");
        
        // On redirige l'utilisateur vers l'article
        return $this->redirectToRoute('article', ['slug' => $slug]);
    }

    // Route "/article/{slug}/archive" pour publier un article
    #[Route('/{slug}/archive', name: 'article_archive', methods: ['GET'])]
    public function archive(string $slug,): Response
    {

        // Récupérer l'article
        // Vérifier que l'article existe
        // Vérifier que l'article est archivé
            // OUI : Le désarchiver
            // NON : L'archiver
        // Enregistrer les modifications
        // Rediriger vers l'article

    }

    // Route "/article/{slug}/status" pour publier ou archiver un article
    #[Route('/{slug}/status', name: 'article_status', methods: ['GET'])]
    public function status(string $slug, Request $request): Response
    {
        $article = $this->ar->findOneBySlug($slug); // Récupération de l'article

        if (!$article) { // Ce sera ignorer si l'article existe
            $this->addFlash('error', "L'article n'existe pas");
            return $this->redirectToRoute('articles');
        }

        if (!$article) { // Ce sera ignorer si l'article existe
            $this->addFlash('error', "L'article n'existe pas");
            return $this->redirectToRoute('articles');
        }

        $action = $request->query->get('s');

        if($action === 'publish') {
            $article->isPublished() ? $article->setIsPublished(false) : $article->setIsPublished(true);
        } else if ($action === 'archive') {
            $article->setIsArchived(!$article->isArchived());
        } else {
            $this->addFlash('error', "Action non reconnue");
            return $this->redirectToRoute('article', ['slug' => $slug]);
        }

        $this->em->persist($article); // Enregistrement de l'article (query SQL)
        $this->em->flush($article); // Exécution de l'enregistrement en BDD

        // On créer un message flash
        $this->addFlash('success', "Enregistré avec succès");

        // On redirige l'utilisateur vers l'article
        return $this->redirectToRoute('article', ['slug' => $slug]);
    }

    // Route "article/{slug}/delete" pour supprimer un article
    #[Route('/{slug}/delete', name: 'article_delete', methods: ['GET'])]
    public function delete(string $slug,): Response
    {
        $article = $this->ar->findOneBySlug($slug); // Récupération de l'article

        if (!$article) { // Ce sera ignorer si l'article existe
            $this->addFlash('error', "L'article n'existe pas");
            return $this->redirectToRoute('articles');
        }
        // l'endroit où l'on supprime un article est dans le repository

        $this->em->remove($article); // Suppression de l'article (query SQL)
        $this->em->flush($article); // Exécution de la suppression en BDD

        // On créer un message flash
        $this->addFlash('success', "Article supprimé avec succès");

        // On redirige l'utilisateur vers la liste des articles
        return $this->redirectToRoute('articles');
    }
}


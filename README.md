# Premier pas avec Symfony

## Présentation

Créons un blog pour apprendre à utiliser Symfony 7.

## Liste priorisée de user stories

| Priorité | Rôle                 | Action                                                                 | Bénéfice                                                             |
| :-------: | --------------------- | ---------------------------------------------------------------------- | ---------------------------------------------------------------------- |
|     1     | Visiteur              | Consulter la liste des articles                                        | Découvrir rapidement le contenu disponible                            |
|     2     | Visiteur              | Lire un article détaillé                                             | Approfondir un sujet d’intérêt                                      |
|     3     | Utilisateur           | S’inscrire / se connecter                                             | Participer (commentaires) et accéder aux fonctionnalités protégées |
|     4     | Utilisateur connecté | Poster un commentaire                                                  | Partager son avis et échanger                                         |
|     5     | Utilisateur connecté | Éditer ou supprimer son propre commentaire                            | Gérer et corriger son contenu                                         |
|     6     | Administrateur        | Créer / éditer / supprimer un article via EasyAdmin                  | Gérer le contenu du blog de manière structurée                      |
|     7     | Administrateur        | Gérer (activer/désactiver) et modérer les commentaires              | Maintenir un espace de discussion respectueux des CGU                  |
|     8     | Administrateur        | Gérer les utilisateurs (rôles, blocage)                              | Contrôler l’accès et la sécurité du site                          |
|     9     | Administrateur        | Configurer les blocs dynamiques du front (header, footer, sidebar)     | Personnaliser la mise en page sans recoder                             |
|    10    | Administrateur        | Consulter des statistiques basiques (nombre d’articles, commentaires) | Suivre l’activité du site                                            |

## 📘 Models (Database/Class Diagram)

```mermaid
classDiagram
  class Article {
    -int id
    -string title
    -string slug
    -string image
    -string keywords
    -string description
    -text content
    -bool published
    -bool archived
    -DateTime createdAt
    -DateTime updatedAt
    -User author
    -Comment comments
  }

  class SitemapLink {
    -int id
    -string name
    -string description
    -string url
    -DateTime createdAt
    -DateTime updatedAt
    -bool active
    -Article article
  }

  class Comment {
    -int id
    -text content
    -DateTime createdAt
    -bool moderated
    -bool published
    -User author
    -Article article
  }

  class User {
    -int id
    -string email
    -string password
    -string roles
    -DateTime createdAt
    -DateTime updatedAt
    -int warningCount
    -bool banned
    -bool active
  }

  class Block {
    -int id
    -string name
    -string content
    -DateTime createdAt
    -DateTime updatedAt
  }

  Article "1" -- "0..*" Comment : contains
  User "1" -- "0..*" Article : (admin) writes
  User "1" -- "0..*" Comment : (logged-in) writes
  Block "*" -- "*" Article : displayed_on
```

## 🧭 Controllers

```mermaid
classDiagram
  class ArticleController {
    -index()
    -view()
    -create()
    -edit()
    -delete()
    -publish()
    -archive()
  }

  class CommentController {
    -create()
    -delete()
    -report()
  }

  class SecurityController {
    -register()
    -login()
    -logout()
  }

  class UserController {
    -profile()
    -newPassword()
    -myData()
    -delete()
  }
```

## 🔧 Services

```mermaid
classDiagram
  class EmailService {
    -sendInfoMail(string subject, string message)
    -sendLoginNotification(User user, array info)
    -sendWarning(User user, array info)
    -sendRgpdExport(User user, array info)
    -sendAccountDeletion(array info)
  }

  class ModerationService {
    -string API_KEY
    -moderate(Comment comment)
    -approve(Comment comment)
    -reject(Comment comment)
  }

  class ImageService {
    -upload(UploadedFile image)
    -compress(UploadedFile image)
    -delete(string slug)
  }

  class OptimizationService {
    -applySeo(Article article)
    -generateSitemap()
    -generateRobots()
  }
```

## 🎧 Event Listeners

```mermaid
classDiagram
  class ArticleListener {
    -onArticleCreated(Article article)
    -onArticleUpdated(Article article)
    -onArticleDeleted(Article article)
  }

  class UserListener {
    -onUserLogin()
    -onUserValidated()
  }
```

## 📑 Sequence Diagrams

### Create an article

```mermaid

sequenceDiagram
    autonumber
    User->>ArticleController: POST /articles/new
    ArticleController->>ImageService: upload(image)
    ImageService-->>ArticleController: imageUrl
    ArticleController->>OptimizationService: applySeo(article)
    ArticleController->>ArticleController: save(article)
    ArticleController-->>User: Redirect to article created
```

### Edit an article

```mermaid
sequenceDiagram
    autonumber
    User->>ArticleController: POST /article/{slug}/edit
    ArticleController->>ArticleController: findOneBySlug(slug)
    ArticleController->>ImageService: upload(newImage)
    ImageService-->>ArticleController: imageUrl
    ArticleController->>ArticleController: persist(article)
    ArticleController-->>User: Redirect to article edited
```

### View an article

```mermaid
sequenceDiagram
    autonumber
    User->>ArticleController: GET /article/{slug}
    ArticleController->>ArticleController: fetchArticle(slug)
    ArticleController-->>User: Display article
```

### Delete an article

```mermaid
sequenceDiagram
    autonumber
    User->>ArticleController: POST /article/d/{slug}
    ArticleController->>ArticleController: fetchArticle(slug)
    ArticleController->>ImageService: delete(image)
    ArticleController->>ArticleController: delete(article)
    ArticleController-->>User: Redirect to article list
```

### Post a comment

```mermaid
sequenceDiagram
    autonumber
    User->>CommentController: POST /articles/{slug}/comments
    CommentController->>ModerationService: moderate(comment)
    ModerationService-->>CommentController: moderationResult
    CommentController->>CommentController: save(comment)
    CommentController-->>User: Redirect to article
```

### Delete a comment

```mermaid
sequenceDiagram
    autonumber
    User->>CommentController: POST /comment/{id}
    CommentController->>CommentController: delete(comment)
    CommentController-->>User: Redirect to article
```

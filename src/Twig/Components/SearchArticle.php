<?php

namespace App\Twig\Components;

use App\Repository\ArticleRepository;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('SearchArticle', template: 'components/SearchArticle.html.twig')]
final class SearchArticle
{
    use DefaultActionTrait;

    #[LiveProp(writable: true, url: true)]
    public ?string $query = null;

    public function __construct(private ArticleRepository $ar)
    {
    }

    public function getArticles(): array
    {
        if($this->query) { // S'il y a une requête, on cherche les articles correspondants
            return $this->ar->findAll($this->query);
        }
        return $this->ar->findBy([], ['created_at' => 'DESC'], 10); // Sinon, on retourne les articles publiés les plus récents
    }
}

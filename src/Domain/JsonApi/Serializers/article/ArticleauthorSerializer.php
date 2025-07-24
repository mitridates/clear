<?php

namespace App\Domain\JsonApi\Serializers\article;
use App\Entity\Article\Articleauthor;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleauthorSerializer extends AbstractSerializer
{

    protected $type = 'articleauthor';
    /**
     * @var UrlGeneratorInterface
     */
    protected $router;
    /**
     * @var string
     */
    protected $token;

    public function __construct($router= null, $token=null)
    {
        $this->router = $router;
        $this->token= $token;
    }

    /**
     * @param Articleauthor|array $model
     * @param array|null $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        return EntityReflectionHelper::serializeClassProperties($model, $fields);
    }

    /***
     * @param Articleauthor $model
     * @return array
     */
    public function getLinks($model): array
    {
        $r=[];
        if(!$this->router) return $r;
        return ['DELETE'=>$this->router->generate('dashboard_article_deleteauthor', [
            'id'=>$model->getArticle()->getId(),
            'sequence'=>$model->getSequence(),
            '_token'=>$this->token
            ])
        ];
    }

    /**
     * Get the id.
     * @param Articleauthor $model
     * @return int
     */
    public function getId($model): int
    {
        return $model->getSequence();
    }
}
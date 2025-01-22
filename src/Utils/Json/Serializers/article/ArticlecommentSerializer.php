<?php

namespace App\Utils\Json\Serializers\article;
use App\Entity\Cavern\Articlecomment;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticlecommentSerializer extends AbstractSerializer
{

    protected $type = 'articlecomment';
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
     * @param Articlecomment|array $model
     * @param array|null $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        return EntityReflectionHelper::serializeClassProperties($model, $fields);
    }

    /***
     * @param Articlecomment $model
     * @return array
     */
    public function getLinks($model): array
    {
        $r=[];
        if(!$this->router) return $r;
        return ['DELETE'=>$this->router->generate('dashboard_article_deletecomment', [
            'id'=>$model->getArticle()->getId(),
            'sequence'=>$model->getSequence(),
            '_token'=>$this->token
            ])
        ];

    }

    /**
     * Get the id.
     * @param Articlecomment $model
     * @return int
     */
    public function getId($model): int
    {
        return $model->getSequence();
    }
}
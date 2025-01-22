<?php

namespace App\Utils\Json\Serializers\article;
use App\Entity\Article\Article;
use App\Utils\Json\Serializers\Geonames\Admin1Serializer;
use App\Utils\Json\Serializers\Geonames\CountrySerializer;
use App\Utils\reflection\EntityReflectionHelper;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Relationship;
use App\vendor\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleSerializer extends AbstractSerializer
{

    protected $type = 'article';
    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    public function __construct($router= null)
    {
        $this->router = $router;
    }

    /**
     * @param Article|array $model
     * @param array|null $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['country', 'admin1'];
        $intersect= $fields?  array_intersect($fields ,$identity) : $identity;
        if(!$fields || !empty($intersect))
            foreach ($intersect as $v){
                $fn='get'.ucfirst(strtolower($v));
                $val= $model->$fn()? $model->$fn()->getId() : null;
                if($val){
                    $data[$v]=$val;
                }
            }
        return $data;
    }

    /**
     * Relationship country
     * @param Article $model
     * @return Relationship
     */
    public function country(Article $model): ?Relationship
    {
        return $model->getCountry()? new Relationship(new Resource($model->getCountry(), new CountrySerializer())) : null;
    }
    /**
     * Relationship Admin1
     * @param Article $model
     * @return Relationship
     */
    public function admin1(Article $model): ?Relationship
    {
        return $model->getAdmin1()? new Relationship(new Resource($model->getAdmin1(), new Admin1Serializer())) : null;
    }

    public function getLinks($model): array
    {
        if(!$this->router) return [];
        return ['self'=>$this->router->generate('dashboard_article_edit', ['id'=>$model->getId()])];
    }
}
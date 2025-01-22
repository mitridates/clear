<?php

namespace App\Utils\Json\Serializers\cave;
use App\Entity\Cave\Cavespecie;
use App\Utils\reflection\EntityReflectionHelper;
use App\Utils\Json\Serializers\article\ArticleSerializer;
use App\Utils\Json\Serializers\FieldValueCodeSerializer;
use App\Utils\Json\Serializers\SpecieSerializer;
use App\vendor\tobscure\jsonapi\AbstractSerializer;
use App\vendor\tobscure\jsonapi\Relationship;
use App\vendor\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CaveSpecieSerializer extends AbstractSerializer
{

    protected $type = 'cavespecie';

    protected ?UrlGeneratorInterface $router;

    protected ?string $csrfToken;

    protected ?string $locale;

    protected array $fields;

    public function __construct($router= null, $csrfToken= null, $locale=null)
    {
        $this->router = $router;
        $this->csrfToken= $csrfToken;
        $this->locale= $locale;
    }

    /**
     * @param Cavespecie $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $identity= ['cave', 'specie', 'article', 'specieconfidence', 'genusconfidence', 'speciesignificance'];
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
     * @param Cavespecie $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links=[];
        $links['self']= $this->router->generate('dashboard_cave_updatemanytoone',  [
            'cave'=>$model->getCave()->getId(),
            'name'=>'specie',
            'sequence'=>$model->getSequence()
        ]);
        if($this->csrfToken){

            $links['DELETE']= $this->router->generate('dashboard_cave_deletemanytoone', [
                'cave'=>$model->getCave()->getId(),
                'sequence'=>$model->getSequence(),
                'name'=>'specie',
                'deletetoken'=>$this->csrfToken
            ]);
        }
        $links['cave']= $this->router->generate('dashboard_cave_edit', ['id'=>$model->getCave()->getId()]);
        return $links;
    }


    /**
     * Get the id.
     *
     * @param Cavespecie $model
     *
     * @return int
     */
    public function getId($model): int
    {
        return $model->getSequence();
    }

    /**
     * Relationship cave
     * @param Cavespecie $model
     * @return Relationship
     */
    public function cave(Cavespecie $model): ?Relationship
    {
        return new Relationship(new Resource($model->getCave(), new CaveSerializer($this->router)));
    }

    /**
     * Relationship Fieldvaluecode
     * @param Cavespecie $model
     * @return Relationship
     */
    public function specie(Cavespecie $model): ?Relationship
    {
        return new Relationship(new Resource($model->getSpecie(), new SpecieSerializer()));
    }

    /**
     * Relationship Article
     * @param Cavespecie $model
     * @return Relationship
     */
    public function article(Cavespecie $model): ?Relationship
    {
        return new Relationship(new Resource($model->getArticle(), new ArticleSerializer()));
    }

    /**
     * Relationship Fieldvaluecode
     * @param Cavespecie $model
     * @return Relationship
     */
    public function specieconfidence(Cavespecie $model): ?Relationship
    {
        return new Relationship(new Resource($model->getSpecieconfidence(), new FieldValueCodeSerializer($this->locale)));
    }

    /**
     * Relationship Fieldvaluecode
     * @param Cavespecie $model
     * @return Relationship
     */
    public function genusconfidence(Cavespecie $model): ?Relationship
    {
        return new Relationship(new Resource($model->getGenusconfidence(), new FieldValueCodeSerializer($this->locale)));
    }

    /**
     * Relationship Fieldvaluecode
     * @param Cavespecie $model
     * @return Relationship
     */
    public function speciesignificance(Cavespecie $model): ?Relationship
    {
        return new Relationship(new Resource($model->getSpeciesignificance(), new FieldValueCodeSerializer($this->locale)));
    }
}
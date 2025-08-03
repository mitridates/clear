<?php

namespace App\Fielddefinition\Infrastructure\Serializer;
use App\Fielddefinition\Domain\Entity\Fielddefinition;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FieldDefinitionSerializer extends AbstractSerializer
{
    protected $type = 'fielddefinition';
    protected array $fields;

    public function __construct(protected readonly ?UrlGeneratorInterface $router= null)
    {
    }

    /**
     * @param Fielddefinition|array $model
     * @param array|null $fields
     * @return array
     */
    public function getAttributes($model, ?array $fields = null): array
    {
        $this->fields= $fields??[];
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        if(in_array('valuecode', $data)){
            $data['valuecode']=$data['valuecode']->getId();
        }
        if(array_key_exists('translations', $data)) unset($data['translations']);

        return $data;
    }

//    /**
//     * @inheritDoc
//     */
//    public function getLinks($model): array
//    {
//        if(!$this->router) return [];
//        $links['self']= $this->router->generate('dashboard_fielddefinition_edit', ['id'=>$model->getId()]);
//        return $links;
//    }

}
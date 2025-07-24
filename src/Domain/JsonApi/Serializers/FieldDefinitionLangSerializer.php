<?php

namespace App\Domain\JsonApi\Serializers;
use App\Entity\FieldDefinition\Fielddefinitionlang;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class FieldDefinitionLangSerializer extends AbstractSerializer
{
    protected $type = 'fielddefinitionlang';
    protected array $fields;

    public function __construct(protected readonly ?UrlGeneratorInterface $router= null, private readonly ?CsrfTokenManagerInterface $tokenManager=null)
    {
    }

    /**
     * @param Fielddefinitionlang $model
     */
    public function getAttributes(mixed $model, ?array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $data['id']= $model->getId()->getId();
        return $data;
    }

    /**
     * @param Fielddefinitionlang $model
     * @inheritDoc
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];

        $arr=  [
            'id'=>$model->getCode()->getCode(),
            'locale'=>$model->getLocale()
        ];

        $links= [
            'self'=> $this->router->generate('dashboard_fielddefinitionlang_edit', $arr),
            'fielddefinition'=> $this->router->generate('dashboard_fielddefinition_edit', ['id'=>$model->getCode()->getCode()])
        ];

        if($this->tokenManager){
            $links['DELETE']= $this->router->generate('dashboard_fielddefinitionlang_delete',
                array_merge($arr, ['_token'=>$this->tokenManager->getToken('delete'.$arr['id'].$arr['locale'])]));
        }

        return $links;
    }

    /**
     * @param Fielddefinitionlang $model
     */
    public function getId($model): string
    {
        return $model->getLocale();
    }
}
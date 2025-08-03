<?php

namespace App\Cave\Infrastructure\Serializer;
use App\Cave\Domain\Entity\Cavelink;
use App\Link\Infrastructure\Serializer\LinkSerializer;
use App\Shared\reflection\EntityReflectionHelper;
use App\Shared\tobscure\jsonapi\AbstractSerializer;
use App\Shared\tobscure\jsonapi\Relationship;
use App\Shared\tobscure\jsonapi\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CaveLinkSerializer extends AbstractSerializer
{

    protected $type = 'cavelink';

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
     * @param Cavelink $model
     * @param ?array $fields
     * @return array
     */
    public function getAttributes($model, array $fields = null): array
    {
        $data= EntityReflectionHelper::serializeClassProperties($model, $fields);
        $data['cave']= $model->getCave()->getId();
        $data['link']= $model->getLink()->getId();
        return $data;
    }


    /**
     * @param Cavelink $model
     * @return array
     */
    public function getLinks($model): array
    {
        if(!$this->router) return [];
        $links= [];
        $links['self']= $this->router->generate('dashboard_cave_updatemanytoone',  [
            'cave'=>$model->getCave()->getId(),
            'name'=>'link',
            'sequence'=>$model->getSequence()
        ]);
        if($this->csrfToken){
            $links['DELETE']= $this->router->generate('dashboard_cave_deletemanytoone', [
                'cave'=>$model->getCave()->getId(),
                'sequence'=>$model->getSequence(),
                'name'=>'link',
                'deletetoken'=>$this->csrfToken
            ]);
        }

        if($model->getLink()){
            $links['link']= $this->router->generate('dashboard_link_edit', ['id'=>$model->getLink()->getId()]);
        }
        $links['cave']= $this->router->generate('dashboard_cave_edit', ['id'=>$model->getCave()->getId()]);

        return $links;
    }


    /**
     * Get the id.
     *
     * @param Cavelink $model
     *
     * @return int
     */
    public function getId($model){
        return $model->getSequence();
    }

    /**
     * Relationship caved
     * @param Cavelink $model
     * @return Relationship
     */
    public function link(Cavelink $model): ?Relationship
    {
        return $model->getLink()? new Relationship(new Resource($model->getLink(), new LinkSerializer($this->router))) : null;
    }

    /**
     * Relationship cave
     * @param Cavelink $model
     * @return Relationship
     */
    public function cave(Cavelink $model): ?Relationship
    {
        return new Relationship(new Resource($model->getCave(), new CaveSerializer($this->router)));
    }
}
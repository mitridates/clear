<?php

namespace App\Map\Infrastructure\Serializer;

use App\Shared\tobscure\jsonapi\AbstractSerializer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MapSerializerFactory
{

    public function __construct(
        protected ?UrlGeneratorInterface $urlGenerator = null,
        protected ?CsrfTokenManagerInterface $tokenManager = null,
        protected ?string $locale = null
    ) {}

    /**
     * Crea una instancia de un serializer.
     *
     * @param string $type Nombre del serializer (por ejemplo: "MapCave", "MapCitation", etc.)
     * @return object
     * @throws \InvalidArgumentException
     */
    public function create(string $type): object
    {
        $className = "App\\Utils\\Json\\Serializers\\{$type}Serializer";

        if (!class_exists($className)) {
            throw new \InvalidArgumentException("El serializer {$className} no existe.");
        }

        return new $className($this->urlGenerator, $this->tokenManager, $this->locale);
    }


    /**
     * Crea un serializer para Map.
     */
    public function createPartial($name): AbstractSerializer
    {

        match ($name){
            'MapCave' => new MapCaveSerializer($this->urlGenerator, $this->tokenManager, $this->locale),
            'MapCitation' => new MapCitationSerializer($this->urlGenerator, $this->tokenManager, $this->locale),
            'MapComment' => new MapCommentSerializer($this->urlGenerator, $this->tokenManager, $this->locale),
            'MapController' => new MapControllerSerializer($this->urlGenerator, $this->tokenManager, $this->locale),
            'MapDrafter' => new MapDrafterSerializer($this->urlGenerator, $this->tokenManager, $this->locale),
            'MapFurthergc' => new MapFurthergcSerializer($this->urlGenerator, $this->tokenManager, $this->locale),
            'MapFurtherpc' => new MapFurtherpcSerializer($this->urlGenerator, $this->tokenManager, $this->locale),
            'MapImage' => new MapImageSerializer($this->urlGenerator, $this->tokenManager, $this->locale),
            'MapLink' => new MapLinkSerializer($this->urlGenerator, $this->tokenManager, $this->locale),
            'MapPublicationtext' => new MapPublicationtextSerializer($this->urlGenerator, $this->tokenManager, $this->locale),
            'MapSpecialmapsheet' => new MapSpecialmapsheetSerializer($this->urlGenerator, $this->tokenManager, $this->locale),
            'MapSurveyor' => new MapSurveyorSerializer($this->urlGenerator, $this->tokenManager, $this->locale),
            default => throw new \InvalidArgumentException("No existe un serializer para '{$type}'."),
        };
    }

}
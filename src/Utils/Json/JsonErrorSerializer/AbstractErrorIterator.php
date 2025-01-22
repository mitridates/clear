<?php
namespace App\Utils\Json\JsonErrorSerializer;
use App\vendor\tobscure\jsonapi\Document;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractErrorIterator
{
    /**
     * Get Json Api error/s array
     * @return JsonError[]
     */
    abstract public function getJsonErrors(): array;

    /**
     * Get serialized error/s
     */
    abstract public function getArray(): array;

    public function length(): int
    {
        return count($this->getArray());
    }

    public function getJsonResponse(int $status=400, array $headers=[]): JsonResponse
    {
         return new JsonResponse(['errors'=>$this->getArray()],$status, array_merge([
            'Content-Type'=>Document::MEDIA_TYPE
        ], $headers));
    }
}
<?php

namespace App\Shared\JsonApi\ErrorSerializer;

class JsonErrorMessages
{
    public static function invalidParameter(string $pointer, string $parameter): JsonError
    {
        return (new JsonError())
            ->setTitle('Invalid parameter Exception')
            ->setSource('pointer', $pointer)
            ->setSource('parameter', $parameter)
            ->setDetail(sprintf('Parameter required: %s',$parameter))
            ->setMeta('type','exception')
            ;
    }

    public static function formNotFound(string $pointer): JsonError
    {
        return (new JsonError())
            ->setTitle('Form Not Found Exception')
            ->setSource('pointer', $pointer)
            ->setMeta('type','exception')

            ;
    }

    public static function formNotSubmitted(string $pointer): JsonError
    {
        return (new JsonError())
            ->setTitle('Form Not Submitted Exception')
            ->setSource('pointer', $pointer)
            ->setMeta('type','exception')

            ;
    }
}
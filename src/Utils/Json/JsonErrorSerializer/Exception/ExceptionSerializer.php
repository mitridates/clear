<?php
namespace App\Utils\Json\JsonErrorSerializer\Exception;

use App\Utils\Http\HttpResponseCode;
use App\Utils\Json\JsonErrorSerializer\JsonError;
use Exception;

class ExceptionSerializer
{
    public $debug;

    /**
     * @param bool $debug
     */
    public function __construct(bool $debug= false)
    {
        $this->debug= $debug;
    }

    /**
     * @inheritDoc
     */
    public function serialize(Exception $e, ?int $status=null, ?bool $debug=null): JsonError
    {
        $j= new JsonError;
        $debug = $debug!==null ? $debug : $this->debug;
        $j->setTitle($e->getMessage())
            ->setCode($e->getCode())
            ->setDetail($debug? $e->getTraceAsString() : null)
            ->setStatus($status)
            ->setMeta('type','exception')
        ;

        if($debug){
            if($status) $j->setMeta('status_string', HttpResponseCode::toString($status));
            $j->setMeta('err_class', get_class($e))
                ->setMeta('debug', (string) $e )
            ;
        }

        return $j;
    }
}
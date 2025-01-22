<?php

namespace App\vendor\tobscure\jsonapi\Exception\Handler;

use Exception;

class ServerErrorExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * @var bool
     */
    private $debug;

    /**
     * @param bool $debug
     */
    public function __construct($debug=false)
    {
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function manages(Exception $e)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Exception $e)
    {
        $status = 500;
        $error = [
            'status' => $e->getCode()>=500? $e->getCode() : $status,
            'title' => 'Server Error',
            'detail'=> $this->debug ? (string) $e : $e->getMessage(),
            'meta'=>['type'=>'exception']
        ];
        return new ResponseBag($status, [$error]);
    }
}

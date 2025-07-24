<?php

namespace App\Shared\tobscure\jsonapi\Exception\Handler;

use Exception;

class BadRequestExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * @var bool
     */
    private $debug;

    /**
     * @param bool $debug
     */
    public function __construct(bool $debug=false)
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
    public function handle(Exception $e): ResponseBag
    {
        $status = 400;
        $error = $this->constructError($e, $status);

        return new ResponseBag($status, [$error]);
    }

    /**
     * @param \Exception $e
     * @param $status
     *
     * @return array
     */
    private function constructError(Exception $e, $status): array
    {
        $error = ['code' => $status, 'title' => 'Bad request'];
        $error['detail'] = $this->debug ? (string) $e : $e->getMessage();
        return $error;
    }
}

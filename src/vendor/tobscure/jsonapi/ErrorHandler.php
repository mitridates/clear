<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\vendor\tobscure\jsonapi;

use Tobscure\jsonapi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\jsonapi\Exception\Handler\ResponseBag;
use Exception;
use RuntimeException;

class ErrorHandler
{
    /**
     * Stores the valid handlers.
     *
     * @var ExceptionHandlerInterface[]
     */
    private $handlers = [];

    /**
     * Handle the exception provided.
     *
     * @param Exception $e
     *
     * @return ResponseBag
     */
    public function handle(Exception $e)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->manages($e)) {
                return $handler->handle($e);
            }
        }

        throw new RuntimeException('Exception handler for '.get_class($e).' not found.');
    }

    /**
     * Register a new exception handler.
     *
     * @param ExceptionHandlerInterface $handler
     *
     * @return $this
     */
    public function registerHandler(ExceptionHandlerInterface $handler)
    {
        $this->handlers[] = $handler;
        return $this;
    }
}

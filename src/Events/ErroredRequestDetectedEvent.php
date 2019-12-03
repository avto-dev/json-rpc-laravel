<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Events;

use AvtoDev\JsonRpc\Requests\ErroredRequestInterface;

class ErroredRequestDetectedEvent
{
    /**
     * @var string
     */
    public $message = 'Errored request detected';

    /**
     * @var ErroredRequestInterface
     */
    protected $error;

    /**
     * @param ErroredRequestInterface $error
     */
    public function __construct(ErroredRequestInterface $error)
    {
        $this->error = $error;
    }

    /**
     * @return ErroredRequestInterface
     */
    public function getError(): ErroredRequestInterface
    {
        return $this->error;
    }
}

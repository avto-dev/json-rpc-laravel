<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Events;

use Throwable;
use AvtoDev\JsonRpc\Requests\RequestInterface;

class RequestHandledExceptionEvent
{
    /**
     * @var string
     */
    public $message = 'RPC request handling exception';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Throwable
     */
    protected $error;

    /**
     * @param RequestInterface $request
     * @param Throwable        $error
     */
    public function __construct(RequestInterface $request, Throwable $error)
    {
        $this->request = $request;
        $this->error   = $error;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Throwable
     */
    public function getError()
    {
        return $this->error;
    }
}

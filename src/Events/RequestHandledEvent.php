<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Events;

use AvtoDev\JsonRpc\Requests\RequestInterface;

class RequestHandledEvent
{
    /**
     * @var RequestInterface
     */
    public $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }
}

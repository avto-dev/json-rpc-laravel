<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Events;

use AvtoDev\JsonRpc\Requests\RequestInterface;

class RequestHandledEvent
{
    /**
     * @var string
     */
    public $message = 'RPC request  handled successful';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @return int|string|null
     */
    public function getId()
    {
        return $this->request->getId();
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}

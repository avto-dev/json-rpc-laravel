<?php

namespace AvtoDev\JsonRpc;

use AvtoDev\JsonRpc\Requests\RequestsStackInterface;
use AvtoDev\JsonRpc\Responses\ResponsesStackInterface;

interface KernelInterface
{
    /**
     * Handle an incoming RPC request.
     *
     * @param RequestsStackInterface $requests
     *
     * @return ResponsesStackInterface
     */
    public function handle(RequestsStackInterface $requests): ResponsesStackInterface;
}

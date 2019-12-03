<?php

namespace AvtoDev\JsonRpc\Requests;

use AvtoDev\JsonRpc\Errors\ErrorInterface;

/**
 * @see ErroredRequest
 */
interface ErroredRequestInterface extends BasicRequestInterface
{
    /**
     * Get request error.
     *
     * @return ErrorInterface
     */
    public function getError(): ErrorInterface;
}

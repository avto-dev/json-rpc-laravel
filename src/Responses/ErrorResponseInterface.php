<?php

namespace AvtoDev\JsonRpc\Responses;

use AvtoDev\JsonRpc\Errors\ErrorInterface;

/**
 * @see ErrorResponse
 */
interface ErrorResponseInterface extends ResponseInterface
{
    /**
     * Get response error data.
     *
     * @return ErrorInterface
     */
    public function getError(): ErrorInterface;
}

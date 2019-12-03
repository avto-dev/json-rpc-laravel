<?php

namespace AvtoDev\JsonRpc\Responses;

/**
 * @see ErrorResponseInterface
 * @see SuccessResponseInterface
 */
interface ResponseInterface
{
    /**
     * Get response identifier.
     *
     * @return int|string|null
     */
    public function getId();
}

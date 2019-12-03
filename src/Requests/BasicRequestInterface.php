<?php

namespace AvtoDev\JsonRpc\Requests;

/**
 * @see RequestInterface
 * @see ErroredRequestInterface
 */
interface BasicRequestInterface
{
    /**
     * Get request identifier.
     *
     * @return int|string|null
     */
    public function getId();
}

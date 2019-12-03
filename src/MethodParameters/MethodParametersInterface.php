<?php

namespace AvtoDev\JsonRpc\MethodParameters;

/**
 * @see \AvtoDev\JsonRpc\Router\Router::call
 *
 * DI is allowed in constructors.
 */
interface MethodParametersInterface
{
    /**
     * Parse passed into method parameters.
     *
     * IMPORTANT: This method will be called automatically by RPC router before injecting into controller.
     *
     * @param array|object|null $params
     *
     * @return void
     */
    public function parse($params): void;
}

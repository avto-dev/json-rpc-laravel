<?php

namespace AvtoDev\JsonRpc\Router;

use AvtoDev\JsonRpc\Requests\RequestInterface as RPCRequest;
use InvalidArgumentException;
use RuntimeException;

interface RouterInterface
{
    /**
     * Register action for method.
     *
     * @param string          $method_name
     * @param callable|string $do_action
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function on(string $method_name, $do_action): void;

    /**
     * Determine if method is registered.
     *
     * @param string $method_name
     *
     * @return bool
     */
    public function methodExists(string $method_name): bool;

    /**
     * Make method call.
     *
     * @param RPCRequest $request
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public function call(RPCRequest $request);

    /**
     * Get registered method names.
     *
     * @return string[]
     */
    public function methods(): array;
}

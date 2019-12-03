<?php

namespace AvtoDev\JsonRpc\Router;

use RuntimeException;
use InvalidArgumentException;
use AvtoDev\JsonRpc\Requests\RequestInterface as RPCRequest;

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
     * Handle an RPC request.
     *
     * @param RPCRequest $request
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public function handle(RPCRequest $request);

    /**
     * Get registered method names.
     *
     * @return string[]
     */
    public function methods(): array;
}

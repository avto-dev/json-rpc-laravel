<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc;

use Illuminate\Support\Facades\Facade;
use AvtoDev\JsonRpc\Router\RouterInterface;
use AvtoDev\JsonRpc\Requests\RequestInterface as RPCRequest;

/**
 * @method static void on(string $method_name, $do_action)
 * @method static bool methodExists(string $method_name)
 * @method static mixed call(string $method_name, ?array $params = null, ?RPCRequest $request = null)
 * @method static string[] methods()
 *
 * @see Router
 */
class RpcRouter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return RouterInterface::class;
    }
}

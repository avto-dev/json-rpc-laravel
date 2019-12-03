<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc;

use AvtoDev\JsonRpc\Requests\RequestInterface as RPCRequest;
use AvtoDev\JsonRpc\Router\RouterInterface;
use Illuminate\Support\Facades\Facade;

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
    protected static function getFacadeAccessor()
    {
        return RouterInterface::class;
    }
}

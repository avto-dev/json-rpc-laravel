<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc;

use AvtoDev\JsonRpc\Router\Router;
use AvtoDev\JsonRpc\Router\RouterInterface;
use AvtoDev\JsonRpc\Factories\RequestFactory;
use AvtoDev\JsonRpc\Factories\FactoryInterface;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register RPC services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerJsonRpcRequestsFactory();
        $this->registerRpcKernel();
        $this->registerRpcRouter();
    }

    /**
     * Register Json RPC requests factory.
     *
     * @return void
     */
    protected function registerJsonRpcRequestsFactory(): void
    {
        $this->app->bindIf(FactoryInterface::class, RequestFactory::class);
    }

    /**
     * Register RPC kernel.
     *
     * @return void
     */
    protected function registerRpcKernel(): void
    {
        $this->app->singleton(KernelInterface::class, Kernel::class);
    }

    /**
     * Register RPC router.
     *
     * @return void
     */
    protected function registerRpcRouter(): void
    {
        $this->app->singleton(RouterInterface::class, Router::class);
    }
}

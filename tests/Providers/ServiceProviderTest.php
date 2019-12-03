<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Avtodev\JsonRpc;

use AvtoDev\JsonRpc\Kernel;
use AvtoDev\JsonRpc\Router\Router;
use AvtoDev\JsonRpc\KernelInterface;
use AvtoDev\JsonRpc\Router\RouterInterface;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;
use AvtoDev\JsonRpc\Factories\RequestFactory;
use AvtoDev\JsonRpc\Factories\FactoryInterface;

/**
 * @covers \AvtoDev\JsonRpc\ServiceProvider<extended>
 */
class ServiceProviderTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testDiRegistration(): void
    {
        $this->assertInstanceOf(RequestFactory::class, $this->app->make(FactoryInterface::class));
        $this->assertInstanceOf(Kernel::class, $this->app->make(KernelInterface::class));
        $this->assertInstanceOf(Router::class, $this->app->make(RouterInterface::class));
    }

    /**
     * @return void
     */
    public function testKernelBindedAsSingleton(): void
    {
        $instance_1 = $this->app->make(KernelInterface::class);
        $instance_2 = $this->app->make(KernelInterface::class);

        $this->assertSame($instance_1, $instance_2);
    }

    /**
     * @return void
     */
    public function testRouterBindedAsSingleton(): void
    {
        $instance_1 = $this->app->make(RouterInterface::class);
        $instance_2 = $this->app->make(RouterInterface::class);

        $this->assertSame($instance_1, $instance_2);
    }
}

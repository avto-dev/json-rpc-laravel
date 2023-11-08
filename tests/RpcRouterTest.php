<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests;

use AvtoDev\JsonRpc\RpcRouter;
use AvtoDev\JsonRpc\Router\RouterInterface;

/**
 * @covers \AvtoDev\JsonRpc\RpcRouter
 */
class RpcRouterTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testAccess(): void
    {
        $this->assertInstanceOf(RouterInterface::class, RpcRouter::getFacadeRoot());
    }
}

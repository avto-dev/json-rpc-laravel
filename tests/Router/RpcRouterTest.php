<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Router;

use AvtoDev\JsonRpc\RpcRouter;
use AvtoDev\JsonRpc\Router\RouterInterface;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;

/**
 * @covers \AvtoDev\JsonRpc\RpcRouter<extended>
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

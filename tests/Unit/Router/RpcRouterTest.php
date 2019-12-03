<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Router;

use AvtoDev\JsonRpc\RpcRouter;
use AvtoDev\JsonRpc\Router\RouterInterface;
use AvtoDev\JsonRpc\Tests\Unit\AbstractUnitTestCase;

/**
 * @covers \AvtoDev\JsonRpc\RpcRouter<extended>
 */
class RpcRouterTest extends AbstractUnitTestCase
{
    /**
     * @return void
     */
    public function testAccess(): void
    {
        $this->assertInstanceOf(RouterInterface::class, RpcRouter::getFacadeRoot());
    }
}

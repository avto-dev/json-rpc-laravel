<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Router;

use AvtoDev\JsonRpc\Router\RouterInterface;
use AvtoDev\JsonRpc\RpcRouter;
use AvtoDev\JsonRpc\Tests\Unit\AbstractUnitTestCase;

/**
 * @group  rpc
 *
 * @covers \AvtoDev\JsonRpc\Router\Facade<extended>
 */
class RpcRouterTest extends AbstractUnitTestCase
{
    /**
     * @small
     *
     * @return void
     */
    public function testAccess(): void
    {
        $this->assertInstanceOf(RouterInterface::class, RpcRouter::getFacadeRoot());
    }
}

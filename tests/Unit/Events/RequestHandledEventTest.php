<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Events;

use AvtoDev\JsonRpc\Events\RequestHandledEvent;
use AvtoDev\JsonRpc\Requests\Request;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;

/**
 * @covers \AvtoDev\JsonRpc\Events\RequestHandledEvent<extended>
 */
class RequestHandledEventTest extends AbstractTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testConstructor(): void
    {
        $event = new RequestHandledEvent(
            $request = new Request(
                $id = $this->faker()->uuid,
                $method = $this->faker()->word,
                $params = $this->faker()->randomElements()
            )
        );
        $this->assertRegExp('~RPC.*success~', $event->message);

        $this->assertSame($id, $event->getRequest()->getId());
        $this->assertSame($method, $event->getRequest()->getMethod());
        $this->assertSame($params, $event->getRequest()->getParams());
    }
}

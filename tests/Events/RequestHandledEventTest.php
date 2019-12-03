<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Events;

use AvtoDev\JsonRpc\Requests\Request;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;
use AvtoDev\JsonRpc\Events\RequestHandledEvent;

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

        $this->assertSame($request, $event->request);
    }
}

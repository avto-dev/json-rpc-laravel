<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Events;

use AvtoDev\JsonRpc\Requests\Request;
use AvtoDev\JsonRpc\Errors\InternalError;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;
use AvtoDev\JsonRpc\Events\RequestHandledExceptionEvent;

/**
 * @covers \AvtoDev\JsonRpc\Events\RequestHandledExceptionEvent<extended>
 */
class RequestHandledExceptionEventTest extends AbstractTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testConstructor(): void
    {
        $event = new RequestHandledExceptionEvent(
            $request = new Request(
                $id = $this->faker()->uuid,
                $method = $this->faker()->word,
                $params = $this->faker()->randomElements()
            ),
            $error = new InternalError
        );
        $this->assertRegExp('~RPC.*except~', $event->message);

        $this->assertSame($request, $event->getRequest());
        $this->assertSame($error, $event->getError());
    }
}

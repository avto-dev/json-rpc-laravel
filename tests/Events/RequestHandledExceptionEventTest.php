<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Events;

use Illuminate\Support\Str;
use AvtoDev\JsonRpc\Requests\Request;
use AvtoDev\JsonRpc\Errors\InternalError;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;
use AvtoDev\JsonRpc\Events\RequestHandledExceptionEvent;

/**
 * @covers \AvtoDev\JsonRpc\Events\RequestHandledExceptionEvent
 */
class RequestHandledExceptionEventTest extends AbstractTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testConstructor(): void
    {
        $event = new RequestHandledExceptionEvent(
            $request = new Request(Str::random(), Str::random(), []),
            $error = new InternalError
        );

        $this->assertSame($request, $event->request);
    }
}

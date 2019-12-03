<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Events;

use Illuminate\Support\Str;
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
            $request = new Request(Str::random(), Str::random(), [])
        );

        $this->assertSame($request, $event->request);
    }
}

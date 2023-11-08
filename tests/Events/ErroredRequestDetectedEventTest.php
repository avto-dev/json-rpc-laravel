<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Events;

use Illuminate\Support\Str;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;
use AvtoDev\JsonRpc\Requests\ErroredRequest;
use AvtoDev\JsonRpc\Errors\InvalidParamsError;
use AvtoDev\JsonRpc\Events\ErroredRequestDetectedEvent;

/**
 * @covers \AvtoDev\JsonRpc\Events\ErroredRequestDetectedEvent
 */
class ErroredRequestDetectedEventTest extends AbstractTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testConstructor(): void
    {
        $event = new ErroredRequestDetectedEvent($error = new ErroredRequest(
            new InvalidParamsError,
            $id = Str::random()
        ));

        $this->assertSame($error, $event->error);
    }
}

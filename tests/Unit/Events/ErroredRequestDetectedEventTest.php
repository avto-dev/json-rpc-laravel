<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Events;

use AvtoDev\JsonRpc\Errors\ErrorInterface;
use AvtoDev\JsonRpc\Errors\InvalidParamsError;
use AvtoDev\JsonRpc\Events\ErroredRequestDetectedEvent;
use AvtoDev\JsonRpc\Requests\ErroredRequest;
use AvtoDev\JsonRpc\Requests\ErroredRequestInterface;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;
use Illuminate\Support\Str;

/**
 * @covers \AvtoDev\JsonRpc\Events\ErroredRequestDetectedEvent<extended>
 */
class ErroredRequestDetectedEventTest extends AbstractTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testConstructor(): void
    {
        $event = new ErroredRequestDetectedEvent(new ErroredRequest(
            new InvalidParamsError,
            $id = Str::random()
        ));

        $this->assertSame('Errored request detected', $event->message);
        $this->assertInstanceOf(ErroredRequestInterface::class, $error = $event->getError());
        $this->assertSame($id, $event->getError()->getId());
        $this->assertInstanceOf(ErrorInterface::class, $error->getError());
        $this->assertSame('Invalid params', $error->getError()->getMessage());
    }
}

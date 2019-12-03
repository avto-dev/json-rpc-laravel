<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Errors;

use AvtoDev\JsonRpc\Errors\ServerError;

/**
 * @covers \AvtoDev\JsonRpc\Errors\ServerError<extended>
 */
class ServerErrorTest extends AbstractErrorTestCase
{
    /**
     * @return void
     */
    public function testDefaultMessage(): void
    {
        $this->assertSame('Server Error', $this->errorFactory(null)->getMessage());
    }

    /**
     * {@inheritdoc}
     *
     * @return ServerError
     */
    protected function errorFactory(...$arguments)
    {
        return new ServerError(...$arguments);
    }
}

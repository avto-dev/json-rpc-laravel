<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Errors;

use AvtoDev\JsonRpc\Errors\ServerError;

/**
 * @covers \AvtoDev\JsonRpc\Errors\ServerError
 */
class ServerErrorTest extends AbstractErrorTestCase
{
    /**
     * @return void
     */
    public function testDefaultMessage(): void
    {
        $this->assertSame('Server Error', ($error = $this->errorFactory(null))->getMessage());
        $this->assertInstanceOf(ServerError::class, $error);
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

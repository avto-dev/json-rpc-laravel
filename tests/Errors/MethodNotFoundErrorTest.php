<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Errors;

use AvtoDev\JsonRpc\Errors\MethodNotFoundError;

/**
 * @covers \AvtoDev\JsonRpc\Errors\MethodNotFoundError
 */
class MethodNotFoundErrorTest extends AbstractErrorTestCase
{
    /**
     * @return void
     */
    public function testDefaultMessage(): void
    {
        $this->assertSame('Method not found', ($error = $this->errorFactory(null))->getMessage());
        $this->assertInstanceOf(MethodNotFoundError::class, $error);
    }

    /**
     * {@inheritdoc}
     *
     * @return MethodNotFoundError
     */
    protected function errorFactory(...$arguments)
    {
        return new MethodNotFoundError(...$arguments);
    }
}

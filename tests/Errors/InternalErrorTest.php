<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Errors;

use AvtoDev\JsonRpc\Errors\InternalError;

/**
 * @covers \AvtoDev\JsonRpc\Errors\InternalError
 */
class InternalErrorTest extends AbstractErrorTestCase
{
    /**
     * @return void
     */
    public function testDefaultMessage(): void
    {
        $this->assertSame('Internal error', ($error = $this->errorFactory(null))->getMessage());
        $this->assertInstanceOf(InternalError::class, $error);
    }

    /**
     * {@inheritdoc}
     *
     * @return InternalError
     */
    protected function errorFactory(...$arguments)
    {
        return new InternalError(...$arguments);
    }
}

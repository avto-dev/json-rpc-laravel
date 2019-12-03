<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Errors;

use AvtoDev\JsonRpc\Errors\InternalError;

/**
 * @covers \AvtoDev\JsonRpc\Errors\InternalError<extended>
 */
class InternalErrorTest extends AbstractErrorTestCase
{
    /**
     * @return void
     */
    public function testDefaultMessage(): void
    {
        $this->assertSame('Internal error', $this->errorFactory(null)->getMessage());
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

<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Errors;

use AvtoDev\JsonRpc\Errors\InvalidRequestError;

/**
 * @group  rpc
 *
 * @covers \AvtoDev\JsonRpc\Errors\InvalidRequestError<extended>
 */
class InvalidRequestErrorTest extends AbstractErrorTestCase
{
    /**
     * @small
     *
     * @return void
     */
    public function testDefaultMessage(): void
    {
        $this->assertSame('Invalid Request', $this->errorFactory(null)->getMessage());
    }

    /**
     * {@inheritdoc}
     *
     * @return InvalidRequestError
     */
    protected function errorFactory(...$arguments)
    {
        return new InvalidRequestError(...$arguments);
    }
}

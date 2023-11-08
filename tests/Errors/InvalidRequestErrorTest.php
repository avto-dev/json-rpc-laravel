<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Errors;

use AvtoDev\JsonRpc\Errors\InvalidRequestError;

/**
 * @covers \AvtoDev\JsonRpc\Errors\InvalidRequestError
 */
class InvalidRequestErrorTest extends AbstractErrorTestCase
{
    /**
     * @return void
     */
    public function testDefaultMessage(): void
    {
        $this->assertSame('Invalid Request', ($error = $this->errorFactory(null))->getMessage());
        $this->assertInstanceOf(InvalidRequestError::class, $error);
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

<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Errors;

use AvtoDev\JsonRpc\Errors\InvalidParamsError;

/**
 * @covers \AvtoDev\JsonRpc\Errors\InvalidParamsError
 */
class InvalidParamsErrorTest extends AbstractErrorTestCase
{
    /**
     * @return void
     */
    public function testDefaultMessage(): void
    {
        $this->assertSame('Invalid params', ($error = $this->errorFactory(null))->getMessage());
        $this->assertInstanceOf(InvalidParamsError::class, $error);
    }

    /**
     * {@inheritdoc}
     *
     * @return InvalidParamsError
     */
    protected function errorFactory(...$arguments)
    {
        return new InvalidParamsError(...$arguments);
    }
}

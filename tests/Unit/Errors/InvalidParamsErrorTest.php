<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Unit\Errors;

use AvtoDev\JsonRpc\Errors\InvalidParamsError;

/**
 * @group  rpc
 *
 * @covers \AvtoDev\JsonRpc\Errors\InvalidParamsError<extended>
 */
class InvalidParamsErrorTest extends AbstractErrorTestCase
{
    /**
     * @small
     *
     * @return void
     */
    public function testDefaultMessage(): void
    {
        $this->assertSame('Invalid params', $this->errorFactory(null)->getMessage());
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

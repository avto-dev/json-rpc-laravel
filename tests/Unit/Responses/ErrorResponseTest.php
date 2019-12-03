<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Unit\Responses;

use Illuminate\Support\Str;
use InvalidArgumentException;
use AvtoDev\JsonRpc\Responses\ErrorResponse;
use AvtoDev\JsonRpc\Errors\MethodNotFoundError;
use AvtoDev\JsonRpc\Tests\Unit\AbstractUnitTestCase;
use AvtoDev\JsonRpc\Responses\ErrorResponseInterface;

/**
 * @group  rpc
 *
 * @covers \AvtoDev\JsonRpc\Responses\ErrorResponse<extended>
 */
class ErrorResponseTest extends AbstractUnitTestCase
{
    protected $response;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->response = new ErrorResponse(null, new MethodNotFoundError);
    }

    /**
     * @small
     *
     * @return void
     */
    public function testInterfaces(): void
    {
        $this->assertInstanceOf(ErrorResponseInterface::class, $this->response);
    }

    /**
     * @small
     *
     * @return void
     */
    public function testIdGetter(): void
    {
        foreach ([Str::random(), null, \random_int(1, 999)] as $id) {
            $this->assertSame($id, (new ErrorResponse($id, new MethodNotFoundError))->getId());
        }
    }

    /**
     * @small
     *
     * @return void
     */
    public function testInvalidIdPassingIntoConstructor(): void
    {
        foreach ([M_PI, [], new \stdClass, \tmpfile(), true] as $wrong_id) {
            $catch = false;

            try {
                new ErrorResponse($wrong_id, new MethodNotFoundError);
            } catch (InvalidArgumentException $e) {
                $catch = true;
            }

            $this->assertTrue($catch);
        }
    }

    /**
     * @small
     *
     * @return void
     */
    public function testErrorGetter(): void
    {
        $error = new MethodNotFoundError;

        $this->assertSame($error, (new ErrorResponse(null, $error))->getError());
    }
}

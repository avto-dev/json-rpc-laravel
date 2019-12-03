<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Requests;

use Illuminate\Support\Str;
use InvalidArgumentException;
use AvtoDev\JsonRpc\Requests\ErroredRequest;
use AvtoDev\JsonRpc\Errors\InvalidParamsError;
use AvtoDev\JsonRpc\Errors\InvalidRequestError;
use AvtoDev\JsonRpc\Tests\Unit\AbstractUnitTestCase;
use AvtoDev\JsonRpc\Requests\ErroredRequestInterface;

/**
 * @group  rpc
 *
 * @covers \AvtoDev\JsonRpc\Requests\ErroredRequest<extended>
 */
class ErroredRequestTest extends AbstractUnitTestCase
{
    /**
     * @var ErroredRequest
     */
    protected $request;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new ErroredRequest(new InvalidParamsError, Str::random());
    }

    /**
     * @coversNothing
     *
     * @small
     *
     * @return void
     */
    public function testInterfaces(): void
    {
        $this->assertInstanceOf(ErroredRequestInterface::class, $this->request);
    }

    /**
     * @small
     *
     * @return void
     */
    public function testIdGetter(): void
    {
        foreach ([Str::random(), null, \random_int(1, 999)] as $id) {
            $this->assertSame($id, (new ErroredRequest(new InvalidParamsError, $id))->getId());
        }
    }

    /**
     * @small
     *
     * @return void
     */
    public function testErrorGetter(): void
    {
        $error = new InvalidParamsError;

        $this->assertSame($error, (new ErroredRequest($error))->getError());
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
                new ErroredRequest(new InvalidRequestError, $wrong_id);
            } catch (InvalidArgumentException $e) {
                $catch = true;
            }

            $this->assertTrue($catch);
        }
    }
}

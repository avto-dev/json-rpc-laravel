<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Responses;

use Illuminate\Support\Str;
use InvalidArgumentException;
use AvtoDev\JsonRpc\Responses\SuccessResponse;
use AvtoDev\JsonRpc\Tests\Unit\AbstractUnitTestCase;
use AvtoDev\JsonRpc\Responses\SuccessResponseInterface;

/**
 * @group  rpc
 *
 * @covers \AvtoDev\JsonRpc\Responses\SuccessResponse<extended>
 */
class SuccessResponseTest extends AbstractUnitTestCase
{
    /**
     * @var SuccessResponse
     */
    protected $response;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->response = new SuccessResponse(Str::random(), Str::random());
    }

    /**
     * @small
     *
     * @return void
     */
    public function testInterfaces(): void
    {
        $this->assertInstanceOf(SuccessResponseInterface::class, $this->response);
    }

    /**
     * @small
     *
     * @return void
     */
    public function testIdGetter(): void
    {
        foreach ([Str::random(), null, \random_int(1, 999)] as $id) {
            $this->assertSame($id, (new SuccessResponse($id, null))->getId());
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
                new SuccessResponse($wrong_id, null);
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
    public function testResultGetter(): void
    {
        foreach ([[], true, M_PI, \random_int(1, 999), new \stdClass, Str::random(), null] as $result) {
            $this->assertSame($result, (new SuccessResponse(Str::random(), $result))->getResult());
        }
    }

    /**
     * @small
     *
     * @return void
     */
    public function testInvalidResultPassingIntoConstructor(): void
    {
        foreach ([\tmpfile()] as $wrong_id) {
            $catch = false;

            try {
                new SuccessResponse($wrong_id, null);
            } catch (InvalidArgumentException $e) {
                $catch = true;
            }

            $this->assertTrue($catch);
        }
    }
}

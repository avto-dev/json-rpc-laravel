<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Responses;

use Illuminate\Support\Str;
use AvtoDev\JsonRpc\Responses\ErrorResponse;
use AvtoDev\JsonRpc\Responses\ResponsesStack;
use AvtoDev\JsonRpc\Responses\SuccessResponse;
use AvtoDev\JsonRpc\Errors\MethodNotFoundError;
use AvtoDev\JsonRpc\Tests\Unit\AbstractUnitTestCase;
use AvtoDev\JsonRpc\Responses\ResponsesStackInterface;

/**
 * @group  rpc
 *
 * @covers \AvtoDev\JsonRpc\Responses\ResponsesStack<extended>
 */
class ResponsesStackTest extends AbstractUnitTestCase
{
    /**
     * @var ResponsesStack
     */
    protected $instance;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = $this->instanceFactory();
    }

    /**
     * @small
     *
     * @return void
     */
    public function testInterface(): void
    {
        $this->assertInstanceOf(ResponsesStackInterface::class, $this->instance);
    }

    /**
     * @return void
     */
    public function testMakeMethod(): void
    {
        $this->assertEquals(
            $this->instanceFactory(), $this->instance::make(true)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testStackAccessors(): void
    {
        $this->assertCount(0, $this->instance);

        $this->instance->push($value = new SuccessResponse(Str::random(), Str::random()));
        $this->instance->push($value2 = new ErrorResponse(Str::random(), new MethodNotFoundError));
        $this->instance->push('foo');
        $this->instance->push([]);

        $this->assertEquals([$value, $value2], $this->instance->all());
        $this->assertCount(2, $this->instance);

        $this->instance->forget([0, 1]);

        $this->assertCount(0, $this->instance);
        $this->assertEquals([], $this->instance->all());
    }

    /**
     * @return void
     */
    public function testEmptyAndNot(): void
    {
        $this->assertTrue($this->instance->isEmpty());
        $this->assertFalse($this->instance->isNotEmpty());

        $this->instance->push($item = new SuccessResponse(Str::random(), Str::random()));

        $this->assertFalse($this->instance->isEmpty());
        $this->assertTrue($this->instance->isNotEmpty());

        $this->assertSame($item, $this->instance->first());
    }

    /**
     * @small
     *
     * @return void
     */
    public function testIsBatch(): void
    {
        $this->assertTrue((new ResponsesStack(true))->isBatch());
        $this->assertFalse((new ResponsesStack(false))->isBatch());
    }

    /**
     * {@inheritdoc}
     *
     * @return ResponsesStack
     */
    protected function instanceFactory()
    {
        return new ResponsesStack(true);
    }
}

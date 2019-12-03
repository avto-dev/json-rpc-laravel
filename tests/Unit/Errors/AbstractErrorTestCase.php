<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Unit\Errors;

use AvtoDev\JsonRpc\Errors\ErrorInterface;
use AvtoDev\JsonRpc\Tests\Unit\AbstractUnitTestCase;
use Exception;
use Illuminate\Support\Str;

abstract class AbstractErrorTestCase extends AbstractUnitTestCase
{
    /**
     * @var ErrorInterface|Exception
     */
    protected $error;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->error = $this->errorFactory();
    }

    /**
     * @small
     *
     * @coversNothing
     *
     * @return void
     */
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(ErrorInterface::class, $this->error);
        $this->assertInstanceOf(Exception::class, $this->error);
    }

    /**
     * @small
     *
     * @return void
     */
    public function testDataGetter(): void
    {
        $this->error = $this->errorFactory(
            $message = Str::random(),
            $code = \random_int(1, 100),
            $data = new Exception,
            $exception = new Exception
        );

        $this->assertSame($message, $this->error->getMessage());
        $this->assertSame($code, $this->error->getCode());
        $this->assertSame($data, $this->error->getData());
        $this->assertSame($exception, $this->error->getPrevious());
    }

    /**
     * Create error instance.
     *
     * @param mixed ...$arguments
     *
     * @return ErrorInterface|mixed
     */
    abstract protected function errorFactory(...$arguments);
}

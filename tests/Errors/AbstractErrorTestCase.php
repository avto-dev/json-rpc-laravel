<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Tests\Errors;

use Exception;
use Illuminate\Support\Str;
use AvtoDev\JsonRpc\Errors\ErrorInterface;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;

abstract class AbstractErrorTestCase extends AbstractTestCase
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
     * @return void
     */
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(ErrorInterface::class, $this->error);
        $this->assertInstanceOf(Exception::class, $this->error);
    }

    /**
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

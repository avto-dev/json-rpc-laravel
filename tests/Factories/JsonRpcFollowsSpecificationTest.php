<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Factories;

use AvtoDev\JsonRpc\Kernel;
use Tarampampam\Wrappers\Json;
use AvtoDev\JsonRpc\Errors\ErrorInterface;
use AvtoDev\JsonRpc\Router\RouterInterface;
use AvtoDev\JsonRpc\Responses\ErrorResponse;
use AvtoDev\JsonRpc\Factories\FactoryInterface;
use AvtoDev\JsonRpc\Tests\AbstractTestCase;
use AvtoDev\JsonRpc\MethodParameters\BaseMethodParameters;

/**
 * @coversNothing
 *
 * @see   <https://www.jsonrpc.org/specification#id2>
 */
class JsonRpcFollowsSpecificationTest extends AbstractTestCase
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @return void
     */
    public function testRpcCallWithPositionalParameters1(): void
    {
        $this->router->on('subtract', function (BaseMethodParameters $parameters) {
            $params = $parameters->getParams();

            return $params[0] - $params[1];
        });

        $input  = '{"jsonrpc": "2.0", "method": "subtract", "params": [42, 23], "id": 1}';
        $output = '{"jsonrpc": "2.0", "result": 19, "id": 1}';

        $this->assertJsonStringEqualsJsonString($output, $this->processRawRequest($input));
    }

    /**
     * @param string $string
     *
     * @return string|null
     */
    protected function processRawRequest(string $string): ?string
    {
        try {
            return $this->factory->responsesStackToJsonString(
                $this->kernel->handle($this->factory->jsonStringToRequestsStack($string))
            );
        } catch (ErrorInterface $error) {
            return $this->factory->errorResponseToJsonString(new ErrorResponse(null, $error));
        }
    }

    /**
     * @return void
     */
    public function testRpcCallWithPositionalParameters2(): void
    {
        $this->router->on('subtract', function (BaseMethodParameters $parameters) {
            $params = $parameters->getParams();

            return $params[0] - $params[1];
        });

        $input  = '{"jsonrpc": "2.0", "method": "subtract", "params": [23, 42], "id": 2}';
        $output = '{"jsonrpc": "2.0", "result": -19, "id": 2}';

        $this->assertJsonStringEqualsJsonString($output, $this->processRawRequest($input));
    }

    /**
     * @return void
     */
    public function testRpcCallWithNamedParameters1(): void
    {
        $this->router->on('subtract', function (BaseMethodParameters $parameters) {
            $params = $parameters->getParams();

            return $params->minuend - $params->subtrahend;
        });

        $input  = '{"jsonrpc": "2.0", "method": "subtract", "params": {"subtrahend": 23, "minuend": 42}, "id": 3}';
        $output = '{"jsonrpc": "2.0", "result": 19, "id": 3}';

        $this->assertJsonStringEqualsJsonString($output, $this->processRawRequest($input));
    }

    /**
     * @return void
     */
    public function testRpcCallWithNamedParameters2(): void
    {
        $this->router->on('subtract', function (BaseMethodParameters $parameters) {
            $params = $parameters->getParams();

            return $params->minuend - $params->subtrahend;
        });

        $input  = '{"jsonrpc": "2.0", "method": "subtract", "params": {"minuend": 42, "subtrahend": 23}, "id": 4}';
        $output = '{"jsonrpc": "2.0", "result": 19, "id": 4}';

        $this->assertJsonStringEqualsJsonString($output, $this->processRawRequest($input));
    }

    /**
     * @return void
     */
    public function testNotification1(): void
    {
        $this->router->on('update', function () {
            return true;
        });

        $input = '{"jsonrpc": "2.0", "method": "update", "params": [1,2,3,4,5]}';

        $this->assertNull($this->processRawRequest($input));
    }

    /**
     * @return void
     */
    public function testNotification2(): void
    {
        $this->router->on('foobar', function () {
            return true;
        });

        $input = '{"jsonrpc": "2.0", "method": "foobar"}';

        $this->assertNull($this->processRawRequest($input));
    }

    /**
     * @return void
     */
    public function testRpcCallOfNonExistentMethod(): void
    {
        $input  = '{"jsonrpc": "2.0", "method": "foobar", "id": "1"}';
        $output = '{"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": "1"}';

        $this->assertJsonStringEqualsJsonString($output, $this->processRawRequest($input));
    }

    /**
     * @return void
     */
    public function testRpcCallWithInvalidJSON(): void
    {
        $input  = '{"jsonrpc": "2.0", "method": "foobar, "params": "bar", "baz]';
        $output = '{"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error"}, "id": null}';

        $this->assertJsonStringEqualsJsonString(
            $output,
            $this->modifyJson($this->processRawRequest($input), function (array $json): array {
                // Remove error additional data, if it presents
                if (isset($json['error']['data'])) {
                    unset($json['error']['data']);
                }

                return $json;
            })
        );
    }

    /**
     * Modify JSON string on the fly, using callback function. In callback will be passed encoded json string (in
     * array format).
     *
     * @param string   $string
     * @param callable $callback
     *
     * @return string
     */
    protected function modifyJson(string $string, callable $callback): string
    {
        return Json::encode($callback(Json::decode($string)));
    }

    /**
     * @return void
     */
    public function testRpcCallBatchInvalidJSON(): void
    {
        $input = <<<'JSON'
[
  {"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},
  {"jsonrpc": "2.0", "method"
]
JSON;

        $output = '{"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error"}, "id": null}';

        $this->assertJsonStringEqualsJsonString(
            $output,
            $this->modifyJson($this->processRawRequest($input), function (array $json): array {
                // Remove error additional data, if it presents
                if (isset($json['error']['data'])) {
                    unset($json['error']['data']);
                }

                return $json;
            })
        );
    }

    /**
     * @return void
     */
    public function testRpcCallWithAnEmptyArray(): void
    {
        $input  = '[]';
        $output = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}';

        $this->assertJsonStringEqualsJsonString($output, $this->processRawRequest($input));
    }

    /**
     * @return void
     */
    public function testRpcCallWithAnInvalidBatchButNotEmpty(): void
    {
        $input  = '[1]';
        $output = <<<'JSON'
[
  {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}
]
JSON;

        $this->assertJsonStringEqualsJsonString($output, $this->processRawRequest($input));
    }

    /**
     * @return void
     */
    public function testRpcCallWithInvalidBatch(): void
    {
        $input  = '[1,2,3]';
        $output = <<<'JSON'
[
  {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null},
  {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null},
  {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}
]
JSON;

        $this->assertJsonStringEqualsJsonString($output, $this->processRawRequest($input));
    }

    /**
     * @return void
     */
    public function testRpcCallBatch(): void
    {
        $this->router->on('sum', function (BaseMethodParameters $parameters): int {
            return \array_sum($parameters->getParams());
        });

        $this->router->on('subtract', function (BaseMethodParameters $parameters): int {
            $params = $parameters->getParams();

            return $params[0] - $params[1];
        });

        $this->router->on('get_data', function (): array {
            return ['hello', 5];
        });

        $input = <<<'JSON'
[
    {"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},
    {"jsonrpc": "2.0", "method": "notify_hello", "params": [7]},
    {"jsonrpc": "2.0", "method": "subtract", "params": [42,23], "id": "2"},
    {"foo": "boo"},
    {"jsonrpc": "2.0", "method": "foo.get", "params": {"name": "myself"}, "id": "5"},
    {"jsonrpc": "2.0", "method": "get_data", "id": "9"}
]
JSON;

        $output = <<<'JSON'
[
    {"jsonrpc": "2.0", "result": 7, "id": "1"},
    {"jsonrpc": "2.0", "result": 19, "id": "2"},
    {"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": "5"},
    {"jsonrpc": "2.0", "result": ["hello", 5], "id": "9"}
]
JSON;

        $this->assertJsonStringEqualsJsonString($output, $this->processRawRequest($input));
    }

    /**
     * @return void
     */
    public function testRpcCallBatchAllNotifications(): void
    {
        $this->router->on('notify_sum', function (BaseMethodParameters $parameters): int {
            return \array_sum($parameters->getParams());
        });

        $this->router->on('notify_hello', function (): bool {
            return true;
        });

        $input = <<<'JSON'
[
    {"jsonrpc": "2.0", "method": "notify_sum", "params": [1,2,4]},
    {"jsonrpc": "2.0", "method": "notify_hello", "params": [7]}
]
JSON;

        $this->assertNull($this->processRawRequest($input));
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->app->make(FactoryInterface::class);
        $this->kernel  = $this->app->make(Kernel::class);
        $this->router  = $this->app->make(RouterInterface::class);
    }
}

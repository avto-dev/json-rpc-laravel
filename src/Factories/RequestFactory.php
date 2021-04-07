<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Factories;

use Throwable;
use InvalidArgumentException;
use AvtoDev\JsonRpc\Requests\Request;
use AvtoDev\JsonRpc\Errors\ParseError;
use AvtoDev\JsonRpc\Errors\ServerError;
use AvtoDev\JsonRpc\Errors\InternalError;
use AvtoDev\JsonRpc\Errors\ErrorInterface;
use AvtoDev\JsonRpc\Requests\RequestsStack;
use AvtoDev\JsonRpc\Requests\ErroredRequest;
use AvtoDev\JsonRpc\Errors\InvalidParamsError;
use AvtoDev\JsonRpc\Errors\InvalidRequestError;
use AvtoDev\JsonRpc\Errors\MethodNotFoundError;
use AvtoDev\JsonRpc\Responses\ResponseInterface;
use AvtoDev\JsonRpc\Requests\RequestsStackInterface;
use AvtoDev\JsonRpc\Responses\ErrorResponseInterface;
use AvtoDev\JsonRpc\Responses\ResponsesStackInterface;
use AvtoDev\JsonRpc\Responses\SuccessResponseInterface;
use AvtoDev\JsonRpc\Traits\ValidateNonStrictValuesTrait;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RequestFactory implements FactoryInterface
{
    use ValidateNonStrictValuesTrait;

    /**
     * {@inheritdoc}
     */
    public function jsonStringToRequestsStack(string $json_string, int $options = 0): RequestsStackInterface
    {
        $is_batch = \mb_strpos($json_string = \trim($json_string), '[') === 0;

        /*
         * rpc call with invalid JSON:
         * --> {"jsonrpc": "2.0", "method": "foobar, "params": "bar", "baz]
         * <-- {"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error"}, "id": null}.
         *
         * rpc call Batch, invalid JSON:
         * --> [
         * {"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},
         * {"jsonrpc": "2.0", "method"
         * ]
         * <-- {"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error"}, "id": null}
         */
        try {
            $raw_requests = \json_decode($json_string, false, 512, $options | \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ParseError(null, 0, $e, $e);
        }

        // Wrap using array (for iterator below)
        $raw_requests = \is_array($raw_requests)
            ? $raw_requests
            : [$raw_requests];

        /*
         * rpc call with an empty Array:
         * --> []
         * <-- {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}.
         */
        if ($is_batch === true && \count($raw_requests) === 0) {
            throw new InvalidRequestError('Invalid Request');
        }

        $result = new RequestsStack($is_batch);

        foreach ($raw_requests as $request) {
            if ($this->isValidRequest($request)) {
                /**
                 * rpc call with positional parameters:
                 * --> {"jsonrpc": "2.0", "method": "subtract", "params": [42, 23], "id": 1}
                 * --> {"jsonrpc": "2.0", "method": "subtract", "params": [23, 42], "id": 2}
                 * --> {"jsonrpc": "2.0", "method": "subtract", "params": {"subtrahend": 23, "minuend": 42}, "id": 3}
                 * --> {"jsonrpc": "2.0", "method": "subtract", "params": {"minuend": 42, "subtrahend": 23}, "id": 4}
                 * --> {"jsonrpc": "2.0", "method": "foobar", "id": "1"}.
                 *
                 * a Notification:
                 * --> {"jsonrpc": "2.0", "method": "update", "params": [1,2,3,4,5]}
                 */
                $params = \property_exists($request, 'params')
                          && (\is_array($request->params) || \is_object($request->params))
                    ? $request->params
                    : null;

                // Push regular request without error
                $result->push(new Request(
                    \property_exists($request, 'id')
                        ? $request->id
                        : null,
                    \property_exists($request, 'method')
                        ? (string) $request->method
                        : '__UNKNOWN_METHOD__',
                    $params,
                    $request
                ));
            } else {
                /*
                 * rpc call with invalid Batch:.
                 *
                 * --> [1,2,3]
                 * <-- [
                 * {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null},
                 * {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null},
                 * {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}
                 * ]
                 *
                 * --> [1]
                 * <-- [
                 *   {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}
                 * ]
                 *
                 * rpc call with invalid Request object:
                 * --> {"jsonrpc": "2.0", "method": 1, "params": "bar"}
                 * <-- {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}
                 *
                 * rpc call Batch:
                 * --> [
                 *     {"jsonrpc": "2.0", "method": "subtract", "params": [42,23], "id": "2"},
                 *     {"foo": "boo"}, // <== SHOULD RETURNS AS AN ERROR
                 *     {"jsonrpc": "2.0", "method": "foo.get", "params": {"name": "myself"}, "id": "5"},
                 * ]
                 * <-- [
                 *     {"jsonrpc": "2.0", "result": 19, "id": "2"},
                 *     {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null},
                 *     {"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": "5"},
                 * ]
                 */

                // If iterated request is not object - push "errored" request without any ID
                $result->push(new ErroredRequest(new InvalidRequestError));
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function errorToHttpResponse(ErrorResponseInterface $error, int $options = 0): HttpResponse
    {
        return new HttpResponse(
            $this->errorResponseToJsonString($error, $options),
            HttpResponse::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function errorResponseToJsonString(ErrorResponseInterface $response, int $options = 0): string
    {
        $error = $response->getError();
        $data  = $error->getData();

        // Any throwable objects in data we should convert into "plain' object for security reasons (hide
        // stack-trace, etc)
        if (\is_object($data) === true && $data instanceof Throwable) {
            $data = [
                'type'    => 'exception',
                'class'   => (new \ReflectionClass($data))->getShortName(),
                'message' => $data->getMessage(),
                'code'    => $data->getCode(),
            ];
        }

        $result = [
            'jsonrpc' => '2.0',
            'error'   => [
                'code'    => $this->errorToCode($error, $error->getCode()),
                'message' => $error->getMessage(),
            ],
            'id'      => $response->getId(),
        ];

        if ($data !== null) {
            $result['error']['data'] = $data;
        }

        return \json_encode($result, $options | \JSON_THROW_ON_ERROR);
    }

    /**
     * {@inheritdoc}
     */
    public function responsesToHttpResponse(ResponsesStackInterface $responses, int $options = 0): HttpResponse
    {
        /*
         * --> [
         * {"jsonrpc": "2.0", "method": "notify_sum", "params": [1,2,4]},
         * {"jsonrpc": "2.0", "method": "notify_hello", "params": [7]}
         * ]
         * <-- //Nothing is returned for all notification batches.
         */
        return new HttpResponse(
            $responses->isNotEmpty()
                ? $this->responsesStackToJsonString($responses, $options)
                : '',
            HttpResponse::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function responsesStackToJsonString(ResponsesStackInterface $stack, int $options = 0): ?string
    {
        if ($stack->isNotEmpty()) {
            if ($stack->isBatch() === false) {
                return $this->responseToJsonString($stack->first(), $options);
            }

            $response_jsons = [];

            foreach ($stack->all() as $response) {
                $response_jsons[] = $this->responseToJsonString($response, $options);
            }

            return '[' . \implode(',', $response_jsons) . ']';
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function responseToJsonString(ResponseInterface $response, int $options = 0): string
    {
        if ($response instanceof SuccessResponseInterface) {
            return $this->successResponseToJsonString($response, $options);
        }

        if ($response instanceof ErrorResponseInterface) {
            return $this->errorResponseToJsonString($response, $options);
        }

        throw new InvalidArgumentException('Unsupported (' . \get_class($response) . ') response passed');
    }

    /**
     * {@inheritdoc}
     */
    public function successResponseToJsonString(SuccessResponseInterface $response, int $options = 0): string
    {
        return (string) \json_encode([
            'jsonrpc' => '2.0',
            'result'  => $response->getResult(),
            'id'      => $response->getId(),
        ], $options | JSON_THROW_ON_ERROR);
    }

    /**
     * Get Json-RPC error code, based on error type.
     *
     * The error codes from and including -32768 to -32000 are reserved for pre-defined errors.
     *
     * @see <https://www.jsonrpc.org/specification> paragraph 5.1
     *
     * @param ErrorInterface $error
     * @param int            $default
     *
     * @return int
     */
    protected function errorToCode(ErrorInterface $error, int $default = 0): int
    {
        switch (\get_class($error)) {
            case InternalError::class:
                return -32603;
            case InvalidParamsError::class:
                return -32602;
            case InvalidRequestError::class:
                return -32600;
            case MethodNotFoundError::class:
                return -32601;
            case ParseError::class:
                return -32700;
            case ServerError::class:
                return -32099;
            default:
                return $default;
        }
    }

    /**
     * Valid request from stack.
     *
     * You can override it in your RequestFactory.
     *
     * @param object $request
     *
     * @return bool
     */
    protected function isValidRequest($request): bool
    {
        return \is_object($request);
    }
}

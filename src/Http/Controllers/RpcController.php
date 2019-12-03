<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use AvtoDev\JsonRpc\KernelInterface;
use AvtoDev\JsonRpc\Errors\ServerError;
use AvtoDev\JsonRpc\Errors\ErrorInterface;
use AvtoDev\JsonRpc\Responses\ErrorResponse;
use Symfony\Component\HttpFoundation\Response;
use AvtoDev\JsonRpc\Factories\FactoryInterface;

class RpcController extends Controller
{
    /**
     * @param Request          $request
     * @param FactoryInterface $factory
     * @param KernelInterface  $rpc
     *
     * @throws \AvtoDev\JsonRpc\Errors\InvalidRequestError
     * @throws \AvtoDev\JsonRpc\Errors\ParseError
     *
     * @return Response
     */
    public function __invoke(Request $request,
                             FactoryInterface $factory,
                             KernelInterface $rpc): Response
    {
        try {
            // Convert JSON string to RequestsStack
            $requests = $factory->jsonStringToRequestsStack((string) $request->getContent());

            // Handle an incoming RPC request
            $responses = $rpc->handle($requests);

            // Convert responses stack into HTTP response with required content
            return $factory->responsesToHttpResponse($responses);
        } catch (ErrorInterface $error) {
            return $factory->errorToHttpResponse(new ErrorResponse(null, $error));
        } catch (Throwable $e) {
            return $factory->errorToHttpResponse(new ErrorResponse(null, new ServerError(
                "Server error: {$e->getMessage()}", 0, $e, $e
            )));
        }
    }
}

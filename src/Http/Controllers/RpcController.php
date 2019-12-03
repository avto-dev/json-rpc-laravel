<?php

declare(strict_types=1);

namespace AvtoDev\JsonRpc\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use AvtoDev\JsonRpc\KernelInterface;
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
    public function index(Request $request,
                          FactoryInterface $factory,
                          KernelInterface $rpc): Response
    {
        // Convert JSON string to RequestsStack
        $requests = $factory->jsonStringToRequestsStack((string) $request->getContent());

        // Optional: `foreach ($requests as $rpc_request) { ... }`

        // Handle an incoming RPC request
        $responses = $rpc->handle($requests);

        // Convert responses stack into HTTP response with required content
        return $factory->responsesToHttpResponse($responses);
    }
}

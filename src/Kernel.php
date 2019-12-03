<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc;

use AvtoDev\JsonRpc\Errors\ErrorInterface;
use AvtoDev\JsonRpc\Errors\InternalError;
use AvtoDev\JsonRpc\Errors\MethodNotFoundError;
use AvtoDev\JsonRpc\Events\ErroredRequestDetectedEvent;
use AvtoDev\JsonRpc\Events\RequestHandledEvent;
use AvtoDev\JsonRpc\Events\RequestHandledExceptionEvent;
use AvtoDev\JsonRpc\Requests\ErroredRequestInterface;
use AvtoDev\JsonRpc\Requests\RequestInterface;
use AvtoDev\JsonRpc\Requests\RequestsStackInterface;
use AvtoDev\JsonRpc\Responses\ErrorResponse;
use AvtoDev\JsonRpc\Responses\ResponsesStack;
use AvtoDev\JsonRpc\Responses\ResponsesStackInterface;
use AvtoDev\JsonRpc\Responses\SuccessResponse;
use AvtoDev\JsonRpc\Router\RouterInterface;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Throwable;

class Kernel implements KernelInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EventsDispatcher
     */
    protected $events;

    /**
     * Kernel constructor.
     *
     * @param RouterInterface  $router
     * @param EventsDispatcher $events
     */
    public function __construct(RouterInterface $router, EventsDispatcher $events)
    {
        $this->router = $router;
        $this->events = $events;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RequestsStackInterface $requests): ResponsesStackInterface
    {
        $responses = new ResponsesStack($requests->isBatch());

        foreach ($requests->all() as $request) {
            if ($request instanceof ErroredRequestInterface) {
                $this->events->dispatch(new ErroredRequestDetectedEvent($request));

                $responses->push(new ErrorResponse($request->getId(), $request->getError()));
            } elseif ($request instanceof RequestInterface) {
                if ($this->router->methodExists($request->getMethod())) {
                    try {
                        $result = $this->router->call($request);

                        if ($request->isNotification() === false) {
                            $responses->push(new SuccessResponse($request->getId(), $result));
                        }
                        $this->events->dispatch(new RequestHandledEvent($request));
                    } catch (Throwable $e) {
                        $this->events->dispatch(new RequestHandledExceptionEvent($request, $e));

                        $responses->push(
                            new ErrorResponse(
                                $request->getId(),
                                $e instanceof ErrorInterface
                                    ? $e
                                    : new InternalError(null, (int) $e->getCode(), $e, $e)
                            )
                        );
                    }
                } else {
                    if ($request->isNotification() === false) {
                        $responses->push(new ErrorResponse($request->getId(), new MethodNotFoundError));
                    }
                }
            }
        }

        return $responses;
    }
}

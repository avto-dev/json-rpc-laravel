<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Router;

use Closure;
use InvalidArgumentException;
use Illuminate\Contracts\Container\Container;
use AvtoDev\JsonRpc\Requests\RequestInterface as RPCRequest;
use AvtoDev\JsonRpc\MethodParameters\MethodParametersInterface as ParametersInterface;

class Router implements RouterInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var callable[]|string[]
     */
    protected $map = [];

    /**
     * Router constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->container->resolving(ParametersInterface::class, function (ParametersInterface $parameters): void {
            $request = $this->container->make(RPCRequest::class);

            if ($request instanceof RPCRequest) {
                $parameters->parse($request->getParams());
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function on(string $method_name, $do_action): void
    {
        if (\is_callable($do_action)) {
            $this->map[$method_name] = Closure::fromCallable($do_action);
        } elseif (\is_string($do_action)) {
            $this->map[$method_name] = $do_action;
        } else {
            throw new InvalidArgumentException(
                'Wrong action passed. It should be a class name with action (like \\My\\Class@method) or callable.'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function call(RPCRequest $request)
    {
        if (! $this->methodExists($method_name = $request->getMethod())) {
            throw new InvalidArgumentException("Method [{$method_name}] does not exists");
        }

        // Bind request instance into container
        $this->container->bind(RPCRequest::class, function () use ($request): ?RPCRequest {
            return $request;
        });

        // Make method calling
        return $this->container->call($this->map[$request->getMethod()]);
    }

    /**
     * {@inheritdoc}
     */
    public function methodExists(string $method_name): bool
    {
        return \in_array($method_name, $this->methods(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function methods(): array
    {
        return \array_keys($this->map);
    }
}

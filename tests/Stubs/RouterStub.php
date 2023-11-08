<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Stubs;

use Illuminate\Contracts\Foundation\Application;

class RouterStub
{
    /**
     * Required for actions testing.
     *
     * @param Application $app
     *
     * @return Application
     */
    public function someAction(Application $app): Application
    {
        return $app;
    }
}

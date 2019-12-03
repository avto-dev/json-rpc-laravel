<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests;

use Dotenv\Dotenv;
use AvtoDev\JsonRpc\ServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use AvtoDev\JsonRpc\Tests\Traits\WithFaker;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Config\Repository as ConfigRepository;

abstract class AbstractTestCase extends TestCase
{
    use WithFaker;

    /**
     * Creates the application.
     *
     * @param string[] $providers
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication(array $providers = [ServiceProvider::class])
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        foreach ($providers as $provider) {
            $app->register($provider);
        }

        return $app;
    }

    /**
     * Get app config repository.
     *
     * @return ConfigRepository
     */
    protected function config(): ConfigRepository
    {
        return $this->app->make(ConfigRepository::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function beforeApplicationBootstrapped(Application $app): void
    {
        Dotenv::create(__DIR__, '.env.testing')->overload();
    }

    /**
     * Get dispatched job by class name.
     *
     * @param string $job_class
     *
     * @return object|null
     */
    protected function getDispatchedJob(string $job_class): ?object
    {
        foreach ($this->dispatchedJobs as $job) {
            if ($job instanceof $job_class) {
                return $job;
            }
        }

        return null;
    }

    /**
     * Get dispatched jobs based on condition.
     *
     * @param callable $closure
     *
     * @return object[]
     */
    protected function getDispatchedJobByCondition(callable $closure): array
    {
        return \array_values((array) \array_filter($this->dispatchedJobs, $closure));
    }

    /**
     * Get fired event by class name.
     *
     * @param string $event_class
     *
     * @return object|null
     */
    protected function getFiredEvent(string $event_class): ?object
    {
        foreach ($this->firedEvents as $event) {
            if ($event instanceof $event_class) {
                return $event;
            }
        }

        return null;
    }
}

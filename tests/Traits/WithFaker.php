<?php

declare(strict_types = 1);

namespace AvtoDev\JsonRpc\Tests\Traits;

use AvtoDev\FakerProviders\ExtendedFaker;
use Faker\Generator as FakerGenerator;
use Illuminate\Contracts\Foundation\Application;

/**
 * @mixin \AvtoDev\DevTools\Tests\PHPUnit\AbstractLaravelTestCase
 */
trait WithFaker
{
    /**
     * The Faker instance.
     *
     * @var ExtendedFaker|FakerGenerator
     */
    private $faker;

    /**
     * Setup up the Faker instance.
     *
     * @return void
     */
    protected function setUpFaker(): void
    {
        $this->faker = $this->makeFaker();
    }

    /**
     * Create a Faker instance.
     *
     * @throws \LogicException
     *
     * @return ExtendedFaker|FakerGenerator
     *
     * @see \AvtoDev\FakerProviders\Frameworks\Laravel\ServiceProvider
     */
    protected function makeFaker(): FakerGenerator
    {
        if ($this->app instanceof Application) {
            return $this->app->make(FakerGenerator::class);
        }

        throw new \LogicException('Application instance not initialized');
    }

    /**
     * Get the Faker instance.
     *
     * @return ExtendedFaker|FakerGenerator
     */
    protected function faker(): FakerGenerator
    {
        if ($this->faker === null) {
            $this->faker = $this->makeFaker();
        }

        return $this->faker;
    }
}

<?php

namespace App\Providers;

use App\Services\Refersion\RefersionClient;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RefersionClient::class, function ($app): RefersionClient {
            $config = $app['config']['services.refersion'];

            return new RefersionClient(
                $app->make(HttpFactory::class),
                $app->make(CacheRepository::class),
                (string) ($config['token_url'] ?? ''),
                (string) ($config['client_id'] ?? ''),
                (string) ($config['client_secret'] ?? ''),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

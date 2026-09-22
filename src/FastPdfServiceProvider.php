<?php

declare(strict_types=1);

namespace ZentiqLabs\FastPdf\Laravel;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\ServiceProvider;

class FastPdfServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/fast-pdf.php',
            'fast-pdf',
        );

        $this->app->singleton(FastPdfManager::class, function (): FastPdfManager {
            /** @var array<string, mixed> $config */
            $config = config('fast-pdf', []);

            return new FastPdfManager(
                $this->app->make(ViewFactory::class),
                $config,
            );
        });

        $this->app->alias(FastPdfManager::class, 'fast-pdf');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/fast-pdf.php' => config_path('fast-pdf.php'),
            ], 'fast-pdf-config');
        }
    }
}

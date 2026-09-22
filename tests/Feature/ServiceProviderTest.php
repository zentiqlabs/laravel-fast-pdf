<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;
use ZentiqLabs\FastPdf\Laravel\FastPdfManager;
use ZentiqLabs\FastPdf\Laravel\Facades\FastPdf;

it('registers FastPdfManager in the container', function (): void {
    $manager = app(FastPdfManager::class);

    expect($manager)->toBeInstanceOf(FastPdfManager::class);
});

it('resolves FastPdfManager through the fast-pdf alias', function (): void {
    $manager = app('fast-pdf');

    expect($manager)->toBeInstanceOf(FastPdfManager::class);
});

it('publishes the config file', function (): void {
    $paths = ServiceProvider::pathsToPublish(
        \ZentiqLabs\FastPdf\Laravel\FastPdfServiceProvider::class,
        'fast-pdf-config',
    );

    expect($paths)->not->toBeEmpty();
});

it('resolves the same singleton on repeated calls', function (): void {
    $a = app(FastPdfManager::class);
    $b = app(FastPdfManager::class);

    expect($a)->toBe($b);
});

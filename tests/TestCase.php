<?php

declare(strict_types=1);

namespace ZentiqLabs\FastPdf\Laravel\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use ZentiqLabs\FastPdf\Laravel\FastPdfServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [FastPdfServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'FastPdf' => \ZentiqLabs\FastPdf\Laravel\Facades\FastPdf::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('fast-pdf.containerized', (bool) env('FAST_PDF_CONTAINERIZED', false));
        $app['config']->set('fast-pdf.timeout', 30);
    }
}

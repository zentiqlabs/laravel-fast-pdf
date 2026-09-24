<?php

declare(strict_types=1);

namespace ZentiqLabs\FastPdf\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use ZentiqLabs\FastPdf\Laravel\FastPdfManager;
use ZentiqLabs\FastPdf\Laravel\LaravelPdfBuilder;
use ZentiqLabs\FastPdf\Laravel\Testing\FastPdfManagerFake;

/**
 * @method static LaravelPdfBuilder fromView(string $view, array $data = [])
 * @method static LaravelPdfBuilder fromHtml(string $html)
 * @method static LaravelPdfBuilder fromFile(string $filePath, array $data = [])
 * @method static LaravelPdfBuilder builder()
 * @method static void assertRendered(string $view, callable|null $callback = null)
 * @method static void assertSaved(string $path)
 * @method static void assertNothingRendered()
 *
 * @see FastPdfManager
 */
class FastPdf extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'fast-pdf';
    }

    /**
     * Replace the bound FastPdfManager with a fake that records all renders.
     *
     * Assertion methods (assertRendered, assertSaved, assertNothingRendered)
     * are callable both on the returned fake object and as static Facade calls.
     *
     *   FastPdf::fake();
     *   FastPdf::fromView('pdf.invoice', $data)->download();
     *   FastPdf::assertRendered('pdf.invoice');
     *
     * @param  array<string, mixed> $config  Unused — kept for API symmetry with the real manager.
     */
    public static function fake(array $config = []): FastPdfManagerFake
    {
        static::swap($fake = new FastPdfManagerFake());

        return $fake;
    }
}

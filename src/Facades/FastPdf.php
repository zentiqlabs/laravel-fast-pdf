<?php

declare(strict_types=1);

namespace ZentiqLabs\FastPdf\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use ZentiqLabs\FastPdf\Laravel\FastPdfManager;
use ZentiqLabs\FastPdf\Laravel\LaravelPdfBuilder;

/**
 * @method static LaravelPdfBuilder fromView(string $view, array $data = [])
 * @method static LaravelPdfBuilder fromHtml(string $html)
 * @method static LaravelPdfBuilder fromFile(string $filePath, array $data = [])
 * @method static LaravelPdfBuilder builder()
 *
 * @see FastPdfManager
 */
class FastPdf extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'fast-pdf';
    }
}

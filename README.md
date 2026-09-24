# Laravel Fast PDF

> Official Laravel bridge for [zentiq-labs/fast-pdf](https://github.com/zentiqlabs/fast-pdf). Adds Blade view rendering, Facade access, and native Laravel HTTP response helpers. Powered by direct headless Chromium IPC. **Zero Node.js. Zero Puppeteer.**

> **Official Package:** Maintained by [Zentiq Labs](https://github.com/zentiqlabs).  
> For security disclosures and support, visit [zentiqlabs.com](https://zentiqlabs.com).

[![Tests](https://github.com/zentiqlabs/laravel-fast-pdf/actions/workflows/tests.yml/badge.svg)](https://github.com/zentiqlabs/laravel-fast-pdf/actions)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/zentiq-labs/laravel-fast-pdf.svg?style=flat-square)](https://packagist.org/packages/zentiq-labs/laravel-fast-pdf)
[![Total Downloads](https://img.shields.io/packagist/dt/zentiq-labs/laravel-fast-pdf.svg?style=flat-square)](https://packagist.org/packages/zentiq-labs/laravel-fast-pdf)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

## Requirements

| Requirement | Version |
|---|---|
| PHP | `^8.3` |
| Laravel | `^12.0` |
| zentiq-labs/fast-pdf | `^1.0` |
| Chromium / Google Chrome | Any headless-capable build |

## Installation

```bash
composer require zentiq-labs/laravel-fast-pdf
```

The service provider and `FastPdf` facade are auto-discovered via Laravel's package discovery.

### Publish the config

```bash
php artisan vendor:publish --tag=fast-pdf-config
```

This copies `config/fast-pdf.php` into your application's config directory.

## Quick Start

### Via Facade

```php
use ZentiqLabs\FastPdf\Laravel\Facades\FastPdf;

// Render a Blade view and return a download response
return FastPdf::fromView('invoices.pdf', ['invoice' => $invoice])
    ->landscape()
    ->withTailwind()
    ->download('invoice-001.pdf');

// Render inline in the browser
return FastPdf::fromHtml($html)->inline('report.pdf');
```

### Via Dependency Injection

```php
use ZentiqLabs\FastPdf\Laravel\FastPdfManager;

class InvoiceController extends Controller
{
    public function __construct(private readonly FastPdfManager $pdf) {}

    public function download(Invoice $invoice): Response
    {
        return $this->pdf
            ->fromView('pdf.invoice', compact('invoice'))
            ->paperSize('a4')
            ->download("invoice-{$invoice->number}.pdf");
    }
}
```

## Configuration

After publishing, edit `config/fast-pdf.php`. Key options:

| Key | Default | Description |
|---|---|---|
| `binary` | *(auto-discovered)* | Absolute path to the Chromium binary |
| `timeout` | `30` | Max seconds per render |
| `paper_size` | `a4` | Default paper size |
| `orientation` | `portrait` | `portrait` or `landscape` |
| `emulate_media` | `print` | CSS media type: `print` or `screen` |
| `containerized` | `false` | Set `true` inside Docker/Alpine |
| `allow_local_file_access` | `false` | Enable only for fully trusted HTML |
| `chromium_flags` | `['--disable-dev-shm-usage']` | Extra Chromium CLI flags |

### Environment variables

| Variable | Description |
|---|---|
| `FAST_PDF_BINARY` | Absolute path to the Chromium binary |
| `FAST_PDF_TIMEOUT` | Render timeout in seconds |
| `FAST_PDF_CONTAINERIZED` | `true` when running in Docker |
| `FAST_PDF_ALLOW_LOCAL_FILE_ACCESS` | `true` to enable local file reads |

## Fluent Builder API

`FastPdfManager` returns a `LaravelPdfBuilder`. All methods are chainable.

| Method | Description |
|---|---|
| `fromView(string $view, array $data = [])` | Render a Blade view as the PDF source |
| `fromHtml(string $html)` | Use a raw HTML string as the source |
| `fromFile(string $path, array $data = [])` | Render a PHP template file as the source |
| `paper(string $format = 'a4', string $orientation = 'portrait')` | Set paper format and orientation in one call |
| `paperSize(PaperSize\|string $size)` | Set paper size |
| `landscape()` | Landscape orientation |
| `portrait()` | Portrait orientation |
| `margins(float $t, float $r, float $b, float $l, string $unit = 'mm')` | Override margins |
| `withTailwind()` | Inject standalone Tailwind CDN |
| `emulateMedia(string $media = 'print')` | CSS media type |
| `output(): string` | Return raw binary PDF bytes |
| `save(string $path): void` | Write to a file path |
| `download(?string $filename = null): Response` | Browser download response |
| `inline(?string $filename = null): Response` | Inline browser response |

## Page Setup

Use `paper()` to set format and orientation together, or chain `paperSize()` + `landscape()` separately.

```php
// Shorthand
FastPdf::fromView('pdf.report', $data)
    ->paper('letter', 'landscape')
    ->margins(15, 15, 15, 15)
    ->download('report.pdf');

// Longhand
FastPdf::fromHtml($html)
    ->paperSize('a4')
    ->portrait()
    ->download();
```

Supported formats: `a0` `a1` `a2` `a3` `a4` `a5` `a6` `letter` `legal` `tabloid` `ledger`.

## Controller Response Helpers

`download()` and `inline()` return an `Illuminate\Http\Response` — no `exit()` is called, so the response goes through Laravel's middleware stack as normal.

```php
use ZentiqLabs\FastPdf\Laravel\Facades\FastPdf;

class InvoiceController extends Controller
{
    public function download(Invoice $invoice): Response
    {
        return FastPdf::fromView('pdf.invoice', compact('invoice'))
            ->paper('a4')
            ->withTailwind()
            ->download("invoice-{$invoice->number}.pdf");
    }

    public function preview(Invoice $invoice): Response
    {
        return FastPdf::fromView('pdf.invoice', compact('invoice'))
            ->inline("invoice-{$invoice->number}.pdf");
    }
}
```

Both methods default the filename to `document.pdf` when called without arguments.

## Testing with FastPdf::fake()

Call `FastPdf::fake()` in your test to prevent Chromium from spawning. The facade is swapped with an in-memory fake that records every call.

```php
use ZentiqLabs\FastPdf\Laravel\Facades\FastPdf;

it('generates and downloads an invoice PDF', function (): void {
    FastPdf::fake();

    $invoice = Invoice::factory()->create();

    $response = $this->get(route('invoices.download', $invoice));

    $response->assertOk()
             ->assertHeader('Content-Type', 'application/pdf');

    FastPdf::assertRendered('pdf.invoice');
});
```

### Available assertions

```php
// Assert a Blade view was rendered (optionally inspect the data passed to it)
FastPdf::assertRendered('pdf.invoice');
FastPdf::assertRendered('pdf.invoice', function (array $data): void {
    expect($data['invoice']->id)->toBe(42);
});

// Assert no renders occurred at all
FastPdf::assertNothingRendered();

// Assert save() was called with a specific path
FastPdf::assertSaved('/var/invoices/001.pdf');
```

## Modern CSS & Asset Support (vs. mPDF / TCPDF)

Legacy PHP PDF generators rely on outdated internal parsing engines that silently omit broken images, crash on modern image formats, or require server-level file permission workarounds.

`laravel-fast-pdf` uses native headless Chromium IPC under the hood, giving you complete feature parity with modern web browsers:

- **Zero Silent Image Failures:** Renders all standard browser assets natively — including remote URLs (`https://...`), relative paths, and Base64 data URIs (`data:image/png;base64,...`).
- **Modern Media Formats:** Built-in support for WebP, AVIF, PNG with alpha transparency, JPEGs, and dynamic inline SVGs.
- **Full CSS3 Layout Capabilities:** Native support for CSS Flexbox, CSS Grid, `object-fit`, `border-radius`, custom web fonts, and dynamic background images.
- **No Server Hacks Required:** Works out of the box without enabling risky `isRemoteEnabled` flags, compiling extra GD/Imagick extensions, or setting raw file path overrides.

## Blade Template Example

```blade
{{-- resources/views/pdf/invoice.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->number }}</title>
</head>
<body>
    <h1>Invoice #{{ $invoice->number }}</h1>
    <p>Total: ${{ number_format($invoice->total, 2) }}</p>
</body>
</html>
```

```php
return FastPdf::fromView('pdf.invoice', ['invoice' => $invoice])
    ->withTailwind()
    ->download("invoice-{$invoice->number}.pdf");
```

## Docker and Alpine Linux

```dockerfile
FROM php:8.3-fpm-alpine
RUN apk add --no-cache chromium
ENV FAST_PDF_BINARY=/usr/bin/chromium-browser
ENV FAST_PDF_CONTAINERIZED=true
```

In `config/fast-pdf.php` or `.env`:

```ini
FAST_PDF_CONTAINERIZED=true
```

## Testing

```bash
composer test
```

With coverage:

```bash
composer test:coverage
```

Static analysis (PHPStan level 9):

```bash
composer check
```

Code style (PSR-12):

```bash
composer cs
```

## Contributing

Contributions, issues, and feature requests are welcome. Please ensure any pull request:

1. Targets the `develop` branch.
2. Ships with corresponding test coverage for all new behaviour.
3. Passes the full CI pipeline (`test`, `check`, `cs`) locally before opening a PR.
4. Follows [Conventional Commits](https://www.conventionalcommits.org/).

## License

The MIT License (MIT). See [LICENSE](LICENSE) for details.

Developed and maintained by [Usman Khan](https://github.com/usman-khan) at [Zentiq Labs](https://zentiqlabs.com).

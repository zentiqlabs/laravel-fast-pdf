<?php

declare(strict_types=1);

use Illuminate\Http\Response;
use ZentiqLabs\FastPdf\Laravel\Facades\FastPdf;
use ZentiqLabs\FastPdf\Laravel\LaravelPdfBuilder;
use ZentiqLabs\FastPdf\Laravel\Testing\FastPdfManagerFake;

// ------------------------------------------------------------------
// FastPdf::fake()
// ------------------------------------------------------------------

it('fake() returns a FastPdfManagerFake instance', function (): void {
    $fake = FastPdf::fake();

    expect($fake)->toBeInstanceOf(FastPdfManagerFake::class);
});

it('fake() swaps the facade root so fromHtml() returns LaravelPdfBuilder', function (): void {
    FastPdf::fake();

    expect(FastPdf::fromHtml('<p>Hello</p>'))->toBeInstanceOf(LaravelPdfBuilder::class);
});

it('fake() swaps the facade root so fromView() returns LaravelPdfBuilder', function (): void {
    FastPdf::fake();

    expect(FastPdf::fromView('pdf.invoice', ['id' => 1]))->toBeInstanceOf(LaravelPdfBuilder::class);
});

// ------------------------------------------------------------------
// assertRendered()
// ------------------------------------------------------------------

it('assertRendered() passes when the view was rendered', function (): void {
    FastPdf::fake();

    FastPdf::fromView('pdf.invoice', ['id' => 1])->output();

    FastPdf::assertRendered('pdf.invoice');
});

it('assertRendered() throws when the view was not rendered', function (): void {
    FastPdf::fake();

    expect(fn () => FastPdf::assertRendered('pdf.invoice'))->toThrow(\RuntimeException::class);
});

it('assertRendered() passes the data array to the optional callback', function (): void {
    FastPdf::fake();

    FastPdf::fromView('pdf.invoice', ['order_id' => 99])->output();

    FastPdf::assertRendered('pdf.invoice', function (array $data): void {
        expect($data['order_id'])->toBe(99);
    });
});

it('assertRendered() throws when the callback expectation fails', function (): void {
    FastPdf::fake();

    FastPdf::fromView('pdf.invoice', ['order_id' => 1])->output();

    expect(fn () => FastPdf::assertRendered('pdf.invoice', function (array $data): void {
        expect($data['order_id'])->toBe(999);
    }))->toThrow(\Exception::class);
});

// ------------------------------------------------------------------
// assertNothingRendered()
// ------------------------------------------------------------------

it('assertNothingRendered() passes when no renders occurred', function (): void {
    FastPdf::fake();

    FastPdf::assertNothingRendered();
});

it('assertNothingRendered() throws after a render call', function (): void {
    FastPdf::fake();

    FastPdf::fromView('pdf.invoice', [])->output();

    expect(fn () => FastPdf::assertNothingRendered())->toThrow(\RuntimeException::class);
});

// ------------------------------------------------------------------
// assertSaved()
// ------------------------------------------------------------------

it('assertSaved() passes when save() was called with the given path', function (): void {
    FastPdf::fake();

    $path = sys_get_temp_dir() . '/fast_pdf_fake_' . uniqid() . '.pdf';
    FastPdf::fromHtml('<p>Hello</p>')->save($path);

    FastPdf::assertSaved($path);
});

it('assertSaved() throws when save() was not called with that path', function (): void {
    FastPdf::fake();

    expect(fn () => FastPdf::assertSaved('/no/such/path.pdf'))->toThrow(\RuntimeException::class);
});

// ------------------------------------------------------------------
// Controller response helpers
// ------------------------------------------------------------------

it('download() returns a 200 Response with attachment disposition', function (): void {
    FastPdf::fake();

    $response = FastPdf::fromView('pdf.invoice', [])->download('invoice.pdf');

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->getStatusCode())->toBe(200)
        ->and($response->headers->get('Content-Type'))->toBe('application/pdf')
        ->and($response->headers->get('Content-Disposition'))->toContain('attachment')
        ->and($response->headers->get('Content-Disposition'))->toContain('invoice.pdf');
});

it('inline() returns a 200 Response with inline disposition', function (): void {
    FastPdf::fake();

    $response = FastPdf::fromHtml('<p>Report</p>')->inline('report.pdf');

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->headers->get('Content-Disposition'))->toContain('inline')
        ->and($response->headers->get('Content-Disposition'))->toContain('report.pdf');
});

it('download() uses document.pdf as the default filename', function (): void {
    FastPdf::fake();

    $response = FastPdf::fromHtml('<p>x</p>')->download();

    expect($response->headers->get('Content-Disposition'))->toContain('document.pdf');
});

it('inline() uses document.pdf as the default filename', function (): void {
    FastPdf::fake();

    $response = FastPdf::fromHtml('<p>x</p>')->inline();

    expect($response->headers->get('Content-Disposition'))->toContain('document.pdf');
});

// ------------------------------------------------------------------
// paper() page layout API
// ------------------------------------------------------------------

it('paper() is chainable via the Facade', function (): void {
    FastPdf::fake();

    $builder = FastPdf::fromHtml('<p>x</p>')->paper('letter', 'landscape');

    expect($builder)->toBeInstanceOf(LaravelPdfBuilder::class);
});

it('full chain with paper() and download() returns a Response', function (): void {
    FastPdf::fake();

    $response = FastPdf::fromHtml('<h1>Invoice</h1>')
        ->paper('a4', 'portrait')
        ->withTailwind()
        ->download('invoice.pdf');

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->getStatusCode())->toBe(200);
});

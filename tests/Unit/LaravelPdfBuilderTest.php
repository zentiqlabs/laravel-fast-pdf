<?php

declare(strict_types=1);

namespace ZentiqLabs\FastPdf\Laravel\Tests\Unit;

use Illuminate\Http\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use ZentiqLabs\FastPdf\Enums\PaperSize;
use ZentiqLabs\FastPdf\Laravel\Contracts\PdfBuilderContract;
use ZentiqLabs\FastPdf\Laravel\LaravelPdfBuilder;

class LaravelPdfBuilderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private function makeContract(): PdfBuilderContract
    {
        return Mockery::mock(PdfBuilderContract::class);
    }

    public function test_download_returns_laravel_response(): void
    {
        $contract = $this->makeContract();
        $contract->shouldReceive('fromHtml')->once()->andReturnSelf();
        $contract->shouldReceive('output')->once()->andReturn('%PDF-1.4 fake');

        $builder  = new LaravelPdfBuilder($contract);
        $response = $builder->fromHtml('<h1>Test</h1>')->download('test.pdf');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('test.pdf', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_inline_returns_laravel_response_with_inline_disposition(): void
    {
        $contract = $this->makeContract();
        $contract->shouldReceive('fromHtml')->once()->andReturnSelf();
        $contract->shouldReceive('output')->once()->andReturn('%PDF-1.4 fake');

        $builder  = new LaravelPdfBuilder($contract);
        $response = $builder->fromHtml('<h1>Test</h1>')->inline('report.pdf');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertStringContainsString('inline', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_fluent_methods_return_static(): void
    {
        $contract = $this->makeContract();
        $contract->shouldReceive('paperSize')->once()->andReturnSelf();
        $contract->shouldReceive('landscape')->once()->andReturnSelf();
        $contract->shouldReceive('withTailwind')->once()->andReturnSelf();

        $builder = new LaravelPdfBuilder($contract);

        $result = $builder
            ->paperSize(PaperSize::A4)
            ->landscape()
            ->withTailwind();

        $this->assertSame($builder, $result);
    }

    public function test_output_delegates_to_contract(): void
    {
        $contract = $this->makeContract();
        $contract->shouldReceive('output')->once()->andReturn('binary-pdf-data');

        $builder = new LaravelPdfBuilder($contract);

        $this->assertSame('binary-pdf-data', $builder->output());
    }

    public function test_save_delegates_to_contract(): void
    {
        $contract = $this->makeContract();
        $contract->shouldReceive('save')->once()->with('/tmp/out.pdf');

        $builder = new LaravelPdfBuilder($contract);
        $builder->save('/tmp/out.pdf');

        $this->assertTrue(true);
    }

    public function test_content_length_header_matches_pdf_byte_size(): void
    {
        $pdfBytes = str_repeat('x', 1024);
        $contract = $this->makeContract();
        $contract->shouldReceive('fromHtml')->once()->andReturnSelf();
        $contract->shouldReceive('output')->once()->andReturn($pdfBytes);

        $builder  = new LaravelPdfBuilder($contract);
        $response = $builder->fromHtml('<p>hi</p>')->download();

        $this->assertSame('1024', $response->headers->get('Content-Length'));
    }
}

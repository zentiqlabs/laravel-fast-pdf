<?php

declare(strict_types=1);

namespace ZentiqLabs\FastPdf\Laravel;

use Illuminate\Http\Response;
use ZentiqLabs\FastPdf\Enums\PaperSize;
use ZentiqLabs\FastPdf\Laravel\Contracts\PdfBuilderContract;

/**
 * Wraps a PdfBuilderContract with Laravel HTTP response helpers.
 *
 * All fluent methods delegate to the underlying builder and return $this so
 * chains work identically to the framework-agnostic API. The concrete
 * implementation passed at construction is CorePdfBuilderAdapter.
 */
class LaravelPdfBuilder
{
    public function __construct(private readonly PdfBuilderContract $builder)
    {
    }

    public function fromHtml(string $html): static
    {
        $this->builder->fromHtml($html);

        return $this;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function fromFile(string $filePath, array $data = []): static
    {
        $this->builder->fromFile($filePath, $data);

        return $this;
    }

    public function paperSize(PaperSize|string $size): static
    {
        $this->builder->paperSize($size);

        return $this;
    }

    public function landscape(): static
    {
        $this->builder->landscape();

        return $this;
    }

    public function portrait(): static
    {
        $this->builder->portrait();

        return $this;
    }

    public function margins(
        float $top,
        float $right,
        float $bottom,
        float $left,
        string $unit = 'mm',
    ): static {
        $this->builder->margins($top, $right, $bottom, $left, $unit);

        return $this;
    }

    public function withTailwind(): static
    {
        $this->builder->withTailwind();

        return $this;
    }

    public function emulateMedia(string $media = 'print'): static
    {
        $this->builder->emulateMedia($media);

        return $this;
    }

    /**
     * Render and return raw binary PDF bytes.
     */
    public function output(): string
    {
        return $this->builder->output();
    }

    /**
     * Render and write the PDF to an absolute file path.
     */
    public function save(string $path): void
    {
        $this->builder->save($path);
    }

    /**
     * Return a Laravel HTTP response that triggers a browser file download.
     */
    public function download(string $filename = 'document.pdf'): Response
    {
        return $this->buildResponse('attachment', $filename);
    }

    /**
     * Return a Laravel HTTP response that renders the PDF inline in the browser.
     */
    public function inline(string $filename = 'document.pdf'): Response
    {
        return $this->buildResponse('inline', $filename);
    }

    private function buildResponse(string $disposition, string $filename): Response
    {
        $pdf  = $this->output();
        $safe = rawurlencode(basename($filename));

        return new Response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "{$disposition}; filename=\"{$safe}\"; filename*=UTF-8''{$safe}",
            'Content-Length'      => (string) strlen($pdf),
            'Cache-Control'       => 'private, max-age=0, must-revalidate',
            'Pragma'              => 'public',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace ZentiqLabs\FastPdf\Laravel;

use ZentiqLabs\FastPdf\Enums\PaperSize;
use ZentiqLabs\FastPdf\Laravel\Contracts\PdfBuilderContract;
use ZentiqLabs\FastPdf\PdfBuilder;

/**
 * Adapts the framework-agnostic (final) PdfBuilder to PdfBuilderContract
 * so LaravelPdfBuilder can be fully tested without mocking a final class.
 */
class CorePdfBuilderAdapter implements PdfBuilderContract
{
    public function __construct(private readonly PdfBuilder $builder)
    {
    }

    public function fromHtml(string $html): static
    {
        $this->builder->fromHtml($html);

        return $this;
    }

    /** @param array<string, mixed> $data */
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

    public function output(): string
    {
        return $this->builder->output();
    }

    public function save(string $path): void
    {
        $this->builder->save($path);
    }
}

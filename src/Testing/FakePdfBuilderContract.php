<?php

declare(strict_types=1);

namespace ZentiqLabs\FastPdf\Laravel\Testing;

use ZentiqLabs\FastPdf\Enums\PaperSize;
use ZentiqLabs\FastPdf\Laravel\Contracts\PdfBuilderContract;

/**
 * In-process fake that satisfies PdfBuilderContract for unit/feature tests.
 *
 * All fluent methods are no-ops that return $this. output() returns a
 * predictable fake PDF string. save() records the path via the callback
 * supplied by FastPdfManagerFake so assertions can verify it was called.
 */
final class FakePdfBuilderContract implements PdfBuilderContract
{
    public function __construct(private readonly \Closure $onSave)
    {
    }

    public function fromHtml(string $html): static
    {
        return $this;
    }

    /** @param array<string, mixed> $data */
    public function fromFile(string $filePath, array $data = []): static
    {
        return $this;
    }

    public function paper(string $format = 'a4', string $orientation = 'portrait'): static
    {
        return $this;
    }

    public function paperSize(PaperSize|string $size): static
    {
        return $this;
    }

    public function landscape(): static
    {
        return $this;
    }

    public function portrait(): static
    {
        return $this;
    }

    public function margins(
        float $top,
        float $right,
        float $bottom,
        float $left,
        string $unit = 'mm',
    ): static {
        return $this;
    }

    public function withTailwind(): static
    {
        return $this;
    }

    public function emulateMedia(string $media = 'print'): static
    {
        return $this;
    }

    public function output(): string
    {
        return '%PDF-1.4 fake';
    }

    public function save(string $path): void
    {
        ($this->onSave)($path);
    }
}

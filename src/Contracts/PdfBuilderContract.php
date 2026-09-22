<?php

declare(strict_types=1);

namespace ZentiqLabs\FastPdf\Laravel\Contracts;

use ZentiqLabs\FastPdf\Enums\PaperSize;

/**
 * Defines the fluent builder surface that LaravelPdfBuilder delegates to.
 *
 * The concrete implementation is CorePdfBuilderAdapter, which wraps the
 * framework-agnostic ZentiqLabs\FastPdf\PdfBuilder. Coding against this
 * contract instead of the final PdfBuilder class keeps the bridge fully
 * testable without requiring any mocking extensions.
 */
interface PdfBuilderContract
{
    public function fromHtml(string $html): static;

    /** @param array<string, mixed> $data */
    public function fromFile(string $filePath, array $data = []): static;

    public function paperSize(PaperSize|string $size): static;

    public function landscape(): static;

    public function portrait(): static;

    public function margins(
        float $top,
        float $right,
        float $bottom,
        float $left,
        string $unit = 'mm',
    ): static;

    public function withTailwind(): static;

    public function emulateMedia(string $media = 'print'): static;

    public function output(): string;

    public function save(string $path): void;
}

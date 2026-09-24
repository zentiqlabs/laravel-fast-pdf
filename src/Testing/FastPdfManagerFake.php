<?php

declare(strict_types=1);

namespace ZentiqLabs\FastPdf\Laravel\Testing;

use ZentiqLabs\FastPdf\Laravel\LaravelPdfBuilder;

/**
 * Drop-in replacement for FastPdfManager used during testing.
 *
 * Activated via FastPdf::fake() which swaps this into the service container.
 * Chromium is never spawned. Every fromView/fromHtml/fromFile call is
 * recorded and can be verified with the assertion helpers below, which are
 * also forwarded through the FastPdf Facade as static calls.
 *
 * Usage in a test:
 *
 *   FastPdf::fake();
 *
 *   FastPdf::fromView('pdf.invoice', ['id' => 1])->download('inv.pdf');
 *
 *   FastPdf::assertRendered('pdf.invoice');
 *   FastPdf::assertRendered('pdf.invoice', fn ($data) => $data['id'] === 1);
 *   FastPdf::assertNothingRendered();  // would fail here
 *
 *   FastPdf::fromView('pdf.invoice', [])->save('/tmp/out.pdf');
 *   FastPdf::assertSaved('/tmp/out.pdf');
 */
final class FastPdfManagerFake
{
    /**
     * @var array<int, array{type: string, view?: string, html?: string, path?: string, data: array<string, mixed>}>
     */
    private array $renders = [];

    /** @var string[] */
    private array $savedPaths = [];

    /** @param array<string, mixed> $data */
    public function fromView(string $view, array $data = []): LaravelPdfBuilder
    {
        $this->renders[] = ['type' => 'view', 'view' => $view, 'data' => $data];

        return $this->fakeBuilder();
    }

    public function fromHtml(string $html): LaravelPdfBuilder
    {
        $this->renders[] = ['type' => 'html', 'html' => $html, 'data' => []];

        return $this->fakeBuilder();
    }

    /** @param array<string, mixed> $data */
    public function fromFile(string $filePath, array $data = []): LaravelPdfBuilder
    {
        $this->renders[] = ['type' => 'file', 'path' => $filePath, 'data' => $data];

        return $this->fakeBuilder();
    }

    public function builder(): LaravelPdfBuilder
    {
        return $this->fakeBuilder();
    }

    // ------------------------------------------------------------------
    // Assertions
    // ------------------------------------------------------------------

    /**
     * Assert that the given Blade view was rendered at least once.
     *
     * An optional callback receives the data array passed to the view so
     * callers can make additional assertions on the payload.
     *
     * @param callable(array<string, mixed>): void|null $callback
     */
    public function assertRendered(string $view, ?callable $callback = null): void
    {
        $matching = array_values(array_filter(
            $this->renders,
            fn (array $r) => ($r['type'] ?? '') === 'view' && ($r['view'] ?? '') === $view,
        ));

        if ($matching === []) {
            $renderedViews = array_column(
                array_filter($this->renders, fn ($r) => $r['type'] === 'view'),
                'view',
            );

            throw new \RuntimeException(
                "The view [{$view}] was not rendered."
                . ($renderedViews !== [] ? ' Rendered: ' . implode(', ', $renderedViews) : ' No views were rendered.'),
            );
        }

        if ($callback !== null) {
            foreach ($matching as $call) {
                $callback($call['data']);
            }
        }
    }

    /**
     * Assert that save() was called with the given path at least once.
     */
    public function assertSaved(string $path): void
    {
        if (! in_array($path, $this->savedPaths, true)) {
            throw new \RuntimeException(
                "PDF was not saved to [{$path}]."
                . ($this->savedPaths !== [] ? ' Saved paths: ' . implode(', ', $this->savedPaths) : ' No saves occurred.'),
            );
        }
    }

    /**
     * Assert that no PDF renders occurred (no fromView/fromHtml/fromFile calls).
     */
    public function assertNothingRendered(): void
    {
        $count = count($this->renders);

        if ($count > 0) {
            throw new \RuntimeException(
                "Expected no PDF renders, but {$count} render(s) occurred.",
            );
        }
    }

    // ------------------------------------------------------------------
    // Inspection helpers
    // ------------------------------------------------------------------

    /**
     * @return array<int, array{type: string, view?: string, html?: string, path?: string, data: array<string, mixed>}>
     */
    public function renders(): array
    {
        return $this->renders;
    }

    /** @return string[] */
    public function savedPaths(): array
    {
        return $this->savedPaths;
    }

    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    private function fakeBuilder(): LaravelPdfBuilder
    {
        $onSave = function (string $path): void {
            $this->savedPaths[] = $path;
        };

        return new LaravelPdfBuilder(new FakePdfBuilderContract($onSave));
    }
}

<?php

declare(strict_types=1);

namespace ZentiqLabs\FastPdf\Laravel;

use Illuminate\Contracts\View\Factory as ViewFactory;
use ZentiqLabs\FastPdf\FastPdf;

class FastPdfManager
{
    private readonly FastPdf $pdf;

    /** @param array<string, mixed> $config */
    public function __construct(
        private readonly ViewFactory $viewFactory,
        array $config = [],
    ) {
        $this->pdf = new FastPdf($config);
    }

    /**
     * Render a Blade view and use the resulting HTML as the PDF source.
     *
     * @param array<string, mixed> $data
     */
    public function fromView(string $view, array $data = []): LaravelPdfBuilder
    {
        $html = $this->viewFactory->make($view, $data)->render();

        return $this->builder()->fromHtml($html);
    }

    /**
     * Start a new builder from a raw HTML string.
     */
    public function fromHtml(string $html): LaravelPdfBuilder
    {
        return $this->builder()->fromHtml($html);
    }

    /**
     * Start a new builder from a PHP template file.
     *
     * @param array<string, mixed> $data
     */
    public function fromFile(string $filePath, array $data = []): LaravelPdfBuilder
    {
        return $this->builder()->fromFile($filePath, $data);
    }

    /**
     * Return a fresh LaravelPdfBuilder for full manual control.
     */
    public function builder(): LaravelPdfBuilder
    {
        return new LaravelPdfBuilder($this->pdf->builder());
    }
}

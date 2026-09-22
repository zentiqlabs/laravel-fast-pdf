<?php

declare(strict_types=1);

namespace ZentiqLabs\FastPdf\Laravel\Tests\Unit;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Mockery;
use PHPUnit\Framework\TestCase;
use ZentiqLabs\FastPdf\Laravel\FastPdfManager;
use ZentiqLabs\FastPdf\Laravel\LaravelPdfBuilder;

class FastPdfManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_builder_returns_laravel_pdf_builder(): void
    {
        $factory = Mockery::mock(ViewFactory::class);
        $manager = new FastPdfManager($factory);

        $this->assertInstanceOf(LaravelPdfBuilder::class, $manager->builder());
    }

    public function test_from_html_returns_laravel_pdf_builder(): void
    {
        $factory = Mockery::mock(ViewFactory::class);
        $manager = new FastPdfManager($factory);

        $result = $manager->fromHtml('<p>Hello</p>');

        $this->assertInstanceOf(LaravelPdfBuilder::class, $result);
    }

    public function test_from_view_renders_blade_and_returns_builder(): void
    {
        $view = Mockery::mock(View::class);
        $view->shouldReceive('render')->once()->andReturn('<h1>Blade Output</h1>');

        $factory = Mockery::mock(ViewFactory::class);
        $factory->shouldReceive('make')->once()->with('invoices.pdf', ['number' => 1])->andReturn($view);

        $manager = new FastPdfManager($factory);
        $result  = $manager->fromView('invoices.pdf', ['number' => 1]);

        $this->assertInstanceOf(LaravelPdfBuilder::class, $result);
    }
}

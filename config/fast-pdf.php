<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Chromium Binary Path
    |--------------------------------------------------------------------------
    |
    | Absolute path to the headless Chromium binary. When null the core
    | engine probes the discovery paths listed below.
    |
    */
    'binary' => env('FAST_PDF_BINARY', null),

    /*
    |--------------------------------------------------------------------------
    | Binary Discovery Paths
    |--------------------------------------------------------------------------
    */
    'binary_discovery_paths' => [
        '/usr/bin/chromium',
        '/usr/bin/chromium-browser',
        '/usr/bin/google-chrome',
        '/usr/bin/google-chrome-stable',
        '/snap/bin/chromium',
        '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        '/Applications/Chromium.app/Contents/MacOS/Chromium',
    ],

    /*
    |--------------------------------------------------------------------------
    | Process Timeout (seconds)
    |--------------------------------------------------------------------------
    */
    'timeout' => (int) env('FAST_PDF_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Default Paper Size
    |--------------------------------------------------------------------------
    */
    'paper_size' => env('FAST_PDF_PAPER_SIZE', 'a4'),

    /*
    |--------------------------------------------------------------------------
    | Default Orientation
    |--------------------------------------------------------------------------
    |
    | 'portrait' or 'landscape'.
    |
    */
    'orientation' => env('FAST_PDF_ORIENTATION', 'portrait'),

    /*
    |--------------------------------------------------------------------------
    | Default Margins (mm)
    |--------------------------------------------------------------------------
    */
    'margins' => [
        'top'    => 10,
        'right'  => 10,
        'bottom' => 10,
        'left'   => 10,
        'unit'   => 'mm',
    ],

    /*
    |--------------------------------------------------------------------------
    | CSS Media Emulation
    |--------------------------------------------------------------------------
    */
    'emulate_media' => env('FAST_PDF_MEDIA', 'print'),

    /*
    |--------------------------------------------------------------------------
    | Containerized Environment
    |--------------------------------------------------------------------------
    |
    | Set true when running inside Docker / Alpine. Adds --no-sandbox and
    | --disable-setuid-sandbox to Chromium. Never enable on bare-metal hosts.
    |
    */
    'containerized' => (bool) env('FAST_PDF_CONTAINERIZED', false),

    /*
    |--------------------------------------------------------------------------
    | Allow Local File Access
    |--------------------------------------------------------------------------
    |
    | Keep false unless you intentionally load local disk assets from trusted
    | HTML. Never enable when rendering user-supplied content.
    |
    */
    'allow_local_file_access' => (bool) env('FAST_PDF_ALLOW_LOCAL_FILE_ACCESS', false),

    /*
    |--------------------------------------------------------------------------
    | Additional Chromium Flags
    |--------------------------------------------------------------------------
    */
    'chromium_flags' => [
        '--disable-dev-shm-usage',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tailwind CDN URL
    |--------------------------------------------------------------------------
    */
    'tailwind_cdn_url' => env('FAST_PDF_TAILWIND_CDN', 'https://cdn.tailwindcss.com'),

    /*
    |--------------------------------------------------------------------------
    | Temporary Directory
    |--------------------------------------------------------------------------
    */
    'temp_dir' => env('FAST_PDF_TEMP_DIR', null),

];

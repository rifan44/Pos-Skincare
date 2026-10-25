<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('backup:run-with-heartbeat', function () {
    $exitCode = $this->call('backup:run');

    if ($exitCode !== 0) {
        return $exitCode;
    }

    $path = storage_path('app/private/healthchecks-ping-url');

    if (!is_readable($path)) {
        $this->error('Healthchecks configuration is missing or unreadable.');
        return 1;
    }

    $url = trim((string) file_get_contents($path));

    if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
        $this->error('Healthchecks configuration is invalid.');
        return 1;
    }

    $response = Http::timeout(15)->get($url);

    if ($response->failed()) {
        $this->error('Healthchecks heartbeat failed.');
        return 1;
    }

    $this->info('Backup completed and Healthchecks heartbeat sent.');
    return 0;
})->purpose('Run the application backup and signal Healthchecks after success');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

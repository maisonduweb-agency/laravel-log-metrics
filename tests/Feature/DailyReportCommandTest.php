<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;

describe('SendDailyLogReportCommand', function () {
    it('executes with dry-run option', function () {
        $this->artisan('log-metrics:daily-report', ['--dry-run' => true])
            ->assertExitCode(0);
    });

    it('executes for a specific date with dry-run', function () {
        $this->artisan('log-metrics:daily-report', [
            '--date' => '2024-01-15',
            '--dry-run' => true,
        ])
            ->assertExitCode(0);
    });

    it('sends report to Discord when configured', function () {
        Http::fake([
            'discord.com/api/webhooks/*' => Http::response(['success' => true], 200),
        ]);

        config(['log-metrics.daily_report.enabled' => true]);
        config(['log-metrics.daily_report.webhook_url' => 'https://discord.com/api/webhooks/daily/token']);

        $this->artisan('log-metrics:daily-report')
            ->assertExitCode(0);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'discord.com/api/webhooks');
        });
    });

    it('fails gracefully when daily report is not configured', function () {
        config(['log-metrics.daily_report.enabled' => false]);

        $this->artisan('log-metrics:daily-report')
            ->assertExitCode(1);
    });
});

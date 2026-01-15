<?php

declare(strict_types=1);

use HaythemBekir\LogMetrics\Application\DailyReport\BuildDailyReportAction;
use HaythemBekir\LogMetrics\Application\DailyReport\GatherLogStatisticsAction;
use HaythemBekir\LogMetrics\Application\DailyReport\SendDailyReportAction;
use HaythemBekir\LogMetrics\Domain\Config\LogMetricsConfig;
use HaythemBekir\LogMetrics\Infrastructure\Http\DiscordWebhookClient;

describe('LogMetricsServiceProvider', function () {
    it('registers LogMetricsConfig as singleton', function () {
        $config1 = app(LogMetricsConfig::class);
        $config2 = app(LogMetricsConfig::class);

        expect($config1)->toBeInstanceOf(LogMetricsConfig::class);
        expect($config1)->toBe($config2);
    });

    it('registers DiscordWebhookClient as singleton', function () {
        $client1 = app(DiscordWebhookClient::class);
        $client2 = app(DiscordWebhookClient::class);

        expect($client1)->toBeInstanceOf(DiscordWebhookClient::class);
        expect($client1)->toBe($client2);
    });

    it('registers all actions as singletons', function () {
        $actions = [
            GatherLogStatisticsAction::class,
            BuildDailyReportAction::class,
            SendDailyReportAction::class,
        ];

        foreach ($actions as $actionClass) {
            $action1 = app($actionClass);
            $action2 = app($actionClass);

            expect($action1)->toBeInstanceOf($actionClass);
            expect($action1)->toBe($action2);
        }
    });

    it('loads config from package', function () {
        expect(config('log-metrics'))->toBeArray();
        expect(config('log-metrics.daily_report'))->toBeArray();
    });
});

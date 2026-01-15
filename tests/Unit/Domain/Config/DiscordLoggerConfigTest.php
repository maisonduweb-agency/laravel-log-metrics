<?php

declare(strict_types=1);

use HaythemBekir\LogMetrics\Domain\Config\LogMetricsConfig;

describe('LogMetricsConfig', function () {
    it('creates from array', function () {
        $config = LogMetricsConfig::fromArray(createTestConfig());

        expect($config->environmentLabel)->toBe('testing');
        expect($config->realtime->enabled)->toBeTrue();
        expect($config->dailyReport->enabled)->toBeTrue();
    });

    it('reports enabled when realtime is configured', function () {
        $config = LogMetricsConfig::fromArray(createTestConfig([
            'realtime' => [
                'enabled' => true,
                'webhook_url' => 'https://discord.com/api/webhooks/test/token',
            ],
            'daily_report' => [
                'enabled' => false,
            ],
        ]));

        expect($config->isEnabled())->toBeTrue();
    });

    it('reports enabled when daily report is configured', function () {
        $config = LogMetricsConfig::fromArray(createTestConfig([
            'realtime' => [
                'enabled' => false,
            ],
            'daily_report' => [
                'enabled' => true,
                'webhook_url' => 'https://discord.com/api/webhooks/daily/token',
            ],
        ]));

        expect($config->isEnabled())->toBeTrue();
    });

    it('reports disabled when nothing is configured', function () {
        $config = LogMetricsConfig::fromArray([
            'realtime' => ['enabled' => false],
            'daily_report' => ['enabled' => false],
        ]);

        expect($config->isEnabled())->toBeFalse();
    });

    it('uses default environment label when not specified', function () {
        $config = LogMetricsConfig::fromArray([]);

        expect($config->environmentLabel)->toBe('production');
    });
});

<?php

declare(strict_types=1);

namespace HaythemBekir\LogMetrics\Providers;

use HaythemBekir\LogMetrics\Application\DailyReport\BuildDailyReportAction;
use HaythemBekir\LogMetrics\Application\DailyReport\GatherLogStatisticsAction;
use HaythemBekir\LogMetrics\Application\DailyReport\SendDailyReportAction;
use HaythemBekir\LogMetrics\Console\Commands\SendDailyLogReportCommand;
use HaythemBekir\LogMetrics\Domain\Config\LogMetricsConfig;
use HaythemBekir\LogMetrics\Infrastructure\Http\DiscordWebhookClient;
use HaythemBekir\LogMetrics\Infrastructure\LogViewer\LogViewerDetector;
use Illuminate\Contracts\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class LogMetricsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('log-metrics')
            ->hasConfigFile()
            ->hasCommand(SendDailyLogReportCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->registerConfig();
        $this->registerLogViewerDetector();
        $this->registerWebhookClient();
        $this->registerActions();
    }

    private function registerConfig(): void
    {
        $this->app->singleton(LogMetricsConfig::class, function (): LogMetricsConfig {
            return LogMetricsConfig::fromArray(
                config('log-metrics', [])
            );
        });
    }

    private function registerLogViewerDetector(): void
    {
        $this->app->singleton(LogViewerDetector::class);
    }

    private function registerWebhookClient(): void
    {
        $this->app->singleton(DiscordWebhookClient::class, function (Application $app): DiscordWebhookClient {
            $config = $app->make(LogMetricsConfig::class);

            return new DiscordWebhookClient($config->appearance);
        });
    }

    private function registerActions(): void
    {
        $this->app->singleton(GatherLogStatisticsAction::class);
        $this->app->singleton(BuildDailyReportAction::class);
        $this->app->singleton(SendDailyReportAction::class);
    }
}

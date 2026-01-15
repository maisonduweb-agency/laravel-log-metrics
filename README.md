# Laravel Log Metrics

[![Latest Version on Packagist](https://img.shields.io/packagist/v/haythem-bekir/laravel-log-metrics.svg?style=flat-square)](https://packagist.org/packages/haythem-bekir/laravel-log-metrics)
[![Total Downloads](https://img.shields.io/packagist/dt/haythem-bekir/laravel-log-metrics.svg?style=flat-square)](https://packagist.org/packages/haythem-bekir/laravel-log-metrics)
[![License](https://img.shields.io/packagist/l/haythem-bekir/laravel-log-metrics.svg?style=flat-square)](https://packagist.org/packages/haythem-bekir/laravel-log-metrics)

A Laravel package that sends **daily log statistics reports** to Discord. Get comprehensive insights about your application's logs delivered to your Discord channel every day.

## Why Daily Reports?

Instead of alert fatigue from real-time notifications, this package provides:

- 📊 **Comprehensive daily statistics** - Total logs, breakdown by level and channel
- ⚠️ **Top recurring errors** - Identify patterns and prioritize fixes
- 🎯 **Better signal-to-noise** - Review at your chosen time, not during incidents
- 🚀 **Zero performance impact** - Runs as a scheduled task, not on every request
- 📈 **Historical insights** - See trends and patterns over time

## Features

- ✅ Daily log statistics report sent to Discord
- ✅ Breakdown by log level (emergency, alert, critical, error, warning, etc.)
- ✅ Breakdown by channel
- ✅ Top 5 recurring errors with count
- ✅ Customizable bot appearance (username, avatar)
- ✅ Environment labels (production, staging, etc.)
- ✅ Manual or scheduled execution
- ✅ Dry-run mode for previewing reports
- ✅ Clean SOLID architecture
- ✅ Fully testable

## Requirements

- PHP 8.1 or higher
- Laravel 10.x, 11.x, or 12.x

## Installation

Install via Composer:

```bash
composer require haythem-bekir/laravel-log-metrics
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=log-metrics-config
```

## Quick Start

### 1. Get Your Discord Webhook URL

1. Go to your Discord server settings
2. Navigate to **Integrations** → **Webhooks**
3. Click **New Webhook**
4. Choose the channel for reports
5. Copy the webhook URL

### 2. Configure Your Environment

Add to your `.env` file:

```env
LOG_METRICS_DAILY_REPORT_ENABLED=true
LOG_METRICS_DAILY_REPORT_WEBHOOK_URL=https://discord.com/api/webhooks/YOUR_WEBHOOK_URL
LOG_METRICS_DAILY_REPORT_TIME=08:00
LOG_METRICS_DAILY_REPORT_TIMEZONE=UTC
```

### 3. Schedule the Daily Report

**Laravel 11+ (`routes/console.php`):**

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('log-metrics:daily-report')->dailyAt('08:00');
```

**Laravel 10 (`app/Console/Kernel.php`):**

```php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('log-metrics:daily-report')->dailyAt('08:00');
}
```

Make sure your scheduler is running:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

That's it! You'll receive daily reports at 8:00 AM (UTC).

## Usage

### Manual Execution

Run reports manually anytime:

```bash
# Send yesterday's report
php artisan log-metrics:daily-report

# Send report for a specific date
php artisan log-metrics:daily-report --date=2024-01-15

# Preview without sending (dry run)
php artisan log-metrics:daily-report --dry-run
```

### Programmatic Usage

Use the actions directly in your code:

```php
use HaythemBekir\DiscordLogger\Application\DailyReport\GatherLogStatisticsAction;
use HaythemBekir\DiscordLogger\Application\DailyReport\BuildDailyReportAction;
use HaythemBekir\DiscordLogger\Application\DailyReport\SendDailyReportAction;
use Carbon\Carbon;

class CustomReportService
{
    public function __construct(
        private GatherLogStatisticsAction $gather,
        private BuildDailyReportAction $build,
        private SendDailyReportAction $send,
    ) {}

    public function sendWeeklyDigest(): void
    {
        $dates = collect(range(0, 6))->map(fn($days) => Carbon::now()->subDays($days));

        foreach ($dates as $date) {
            $stats = $this->gather->execute($date);
            $report = $this->build->execute($stats);
            $this->send->execute($report);
        }
    }
}
```

## Configuration

The package provides several customization options in `config/log-metrics.php`:

```php
return [
    'daily_report' => [
        'enabled' => env('LOG_METRICS_DAILY_REPORT_ENABLED', false),
        'webhook_url' => env('LOG_METRICS_DAILY_REPORT_WEBHOOK_URL'),
        'time' => env('LOG_METRICS_DAILY_REPORT_TIME', '08:00'),
        'timezone' => env('LOG_METRICS_DAILY_REPORT_TIMEZONE', 'UTC'),
    ],

    'appearance' => [
        'username' => env('LOG_METRICS_BOT_USERNAME', 'Laravel Logger'),
        'avatar_url' => env('LOG_METRICS_BOT_AVATAR_URL'),
    ],

    'colors' => [
        'emergency' => 0xFF0000, // Red
        'alert'     => 0xFF3300, // Orange-Red
        'critical'  => 0xFF6600, // Orange
        'error'     => 0xFF9900, // Dark Orange
        'warning'   => 0xFFCC00, // Yellow
        'notice'    => 0x00CCFF, // Cyan
        'info'      => 0x0099FF, // Blue
        'debug'     => 0x999999, // Gray
    ],

    'environment_label' => env('LOG_METRICS_ENVIRONMENT_LABEL', env('APP_ENV')),

    'log_viewer' => [
        'enabled' => env('LOG_METRICS_LOG_VIEWER_ENABLED', true),
        'app_url' => env('LOG_METRICS_APP_URL', env('APP_URL')),
    ],
];
```

## Log Viewer Integration

If you have a Laravel Log Viewer package installed, error messages in Discord reports will automatically become **clickable links** that take you directly to the log viewer!

### Supported Log Viewers

- **[opcodesio/log-viewer](https://github.com/opcodesio/log-viewer)** - Modern, feature-rich log viewer (Recommended)
- **[rap2hpoutre/laravel-log-viewer](https://github.com/rap2hpoutre/laravel-log-viewer)** - Simple, lightweight log viewer

### How It Works

1. Install any supported log viewer package:
   ```bash
   composer require opcodesio/log-viewer
   ```

2. Ensure your `APP_URL` is configured in `.env`:
   ```env
   APP_URL=https://your-app.com
   ```

3. Error messages in Discord will automatically become clickable:
   ```
   Top Recurring Errors:
     • 8x: [foreach() argument must be of type array|object, null given](https://your-app.com/log-viewer?query=foreach)
     • 6x: [syntax error, unexpected token "return"](https://your-app.com/log-viewer?query=syntax+error)
   ```

Clicking the error takes you directly to the log viewer with a search query pre-filled, making it easy to investigate issues!

### Configuration

You can customize the log viewer integration:

```env
# Enable/disable log viewer links
LOG_METRICS_LOG_VIEWER_ENABLED=true

# Override app URL if needed
LOG_METRICS_APP_URL=https://your-app.com
```

## What the Report Includes

Each daily report contains:

- **📅 Date** - Which day the report covers
- **📊 Total Logs** - Total number of log entries
- **📈 Breakdown by Level** - Count for each level with emoji indicators:
  - 🚨 Emergency
  - 🔔 Alert
  - 💥 Critical
  - ❌ Error
  - ⚠️ Warning
  - 📝 Notice
  - ℹ️ Info
  - 🔍 Debug
- **📁 Breakdown by Channel** - Count per logging channel
- **⚠️ Top Recurring Errors** - Top 5 most frequent error messages

## Example Discord Message

```
📅 Daily Log Report - 2024-01-15

Issues detected in the last 24 hours.

Total Logs: 1,234
❌ Error: 15
⚠️ Warning: 47
📝 Notice: 122
ℹ️ Info: 1,050

By Channel:
  production: 1,234

Top Recurring Errors:
  • 8x: foreach() argument must be of type array|object, null given [View in Log Viewer]
  • 6x: syntax error, unexpected token "return" [View in Log Viewer]
  • 2x: Call to undefined method [View in Log Viewer]

Generated by Laravel Log Metrics
```

> 💡 **Note:** If you have a Log Viewer package installed, the error messages become clickable links!

## Testing

```bash
# Run tests
composer test

# Run with coverage
composer test-coverage

# Static analysis
composer analyse

# Code style check
composer format-check

# Auto-fix code style
composer format
```

## Architecture

This package follows **SOLID principles** and **Domain-Driven Design**:

```
src/
├── Application/DailyReport/      # Use cases & DTOs
│   ├── GatherLogStatisticsAction.php
│   ├── BuildDailyReportAction.php
│   └── SendDailyReportAction.php
├── Domain/                        # Business logic & value objects
│   ├── Config/
│   └── ValueObjects/
├── Infrastructure/                # External integrations
│   └── Http/DiscordWebhookClient.php
└── Console/Commands/              # Artisan commands
```

This makes the package:
- ✅ Testable - Mock dependencies easily
- ✅ Extensible - Implement custom transports
- ✅ Maintainable - Clear separation of concerns

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Upgrading

Please see [UPGRADE](UPGRADE.md) for upgrade instructions.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Credits

- [Haythem Bekir](https://github.com/haythembekir20)
- Built with [Spatie Laravel Package Tools](https://github.com/spatie/laravel-package-tools)

## Support

If you find this package helpful, please consider starring it on [GitHub](https://github.com/maisonduweb-agency/laravel-log-metrics)!

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

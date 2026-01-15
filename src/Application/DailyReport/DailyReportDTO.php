<?php

declare(strict_types=1);

namespace HaythemBekir\LogMetrics\Application\DailyReport;

final class DailyReportDTO
{
    public function __construct(
        public readonly LogStatisticsDTO $statistics,
        public readonly string $summary,
    ) {}
}

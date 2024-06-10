<?php

namespace App\Filament\Advisor\Widgets;

use App\Models\Advisor;
use App\Models\ProjectQuery;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ProjectQueriesChart extends ChartWidget
{
    protected static ?string $heading = 'Project Queries';

    protected function getOptions(): array|RawJs|null
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $queriesTrend = Trend::query(ProjectQuery::query()->whereIn('project_id', Advisor::authUser()->projects->pluck('id')))
            ->between(
                start: now()->startOfWeek(),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'labels' => $queriesTrend->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('l'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Project Queries',
                    'data' => $queriesTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

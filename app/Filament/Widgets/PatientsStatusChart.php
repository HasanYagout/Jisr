<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use Filament\Widgets\ChartWidget;

class PatientsStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?int $sort=3;

    protected function getData(): array
    {
        // Fetch the count of patients with status 1 and 0
        $status1Count = Patient::where('status', 1)->count();
        $status0Count = Patient::where('status', 0)->count();

        return [
            'datasets' => [
                [
                    'label' => 'In Progress', // Label for the first dataset
                    'data' => [$status0Count], // Data for status 0
                    'backgroundColor' => '#FF6384', // Color for status 0
                ],
                [
                    'label' => 'Done', // Label for the second dataset
                    'data' => [$status1Count], // Data for status 1
                    'backgroundColor' => '#36A2EB', // Color for status 1
                ],
            ],
            'labels' => ['Status'], // Labels for the x-axis
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\BugReport;
use Filament\Widgets\ChartWidget;

class BugReportsChart extends ChartWidget
{
    protected static ?string $heading = 'Laporan Bug (7 Hari Terakhir)';
    
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = BugReport::whereDate('created_at', $date->format('Y-m-d'))->count();
            
            $data[] = $count;
            $labels[] = $date->format('d M');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Laporan Bug',
                    'data' => $data,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

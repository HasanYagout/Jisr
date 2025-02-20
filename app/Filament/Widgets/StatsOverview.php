<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Patients', User::where('type',2)->count())
                ->icon('heroicon-s-users')
            ,
            Stat::make('Total Instructors', User::where('type',3)->count())
            ->icon('heroicon-s-users'),
            Stat::make('Total Students', User::where('type',1)->count())
            ->icon('heroicon-s-users'),
        ];
    }
}

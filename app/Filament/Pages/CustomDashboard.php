<?php
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use JetBrains\PhpStorm\NoReturn;

class CustomDashboard extends BaseDashboard
{
    use HasFiltersForm;
    #[NoReturn] public static function shouldRegisterNavigation(): bool
    {
        return !auth()->user()->hasRole(['student']);
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate'),
                        DatePicker::make('endDate'),

                    ])
                    ->columns(3),
            ]);
    }
}

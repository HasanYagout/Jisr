<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPatients extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort=4;
    public static function canView(): bool
    {
       return auth()->check() && auth()->user()->hasRole(['student','instructor']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                auth()->user()->hasRole(['admin','instructor'])?   Patient::latest():Patient::where('user_id',auth()->id())->latest()

            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('age'),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('phone')
                ->searchable(),
                Tables\Columns\TextColumn::make('address'),
                Tables\Columns\TextColumn::make('pain_level')
                    ->badge(),
            ]);
    }
}

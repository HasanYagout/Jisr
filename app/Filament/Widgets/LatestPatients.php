<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPatients extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
        protected static ?int $sort=3;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Patient::query()->orderBy('created_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('age'),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('address'),
                Tables\Columns\TextColumn::make('pain_level')
                    ->badge(),
            ]);
    }
}

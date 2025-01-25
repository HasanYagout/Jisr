<?php

namespace App\Filament\Resources;

use App\Filament\Pages\ExaminationRecord;
use App\Filament\Resources\PatientListResource\Pages;
use App\Filament\Resources\PatientListResource\RelationManagers;
use App\Models\Patient;
use App\Models\Student;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;

class PatientListResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('age'),
                TextInput::make('gender'),
                TextInput::make('phone'),
                TextInput::make('address'),
                Forms\Components\Select::make('pain_level')
                ->options(['mild','moderate','severe']),
                Forms\Components\Textarea::make('complaint'),
                Forms\Components\Textarea::make('dental_history'),
                Forms\Components\FileUpload::make('dental_history_file')
                ->multiple(),
                Forms\Components\Select::make('user_id')
                    ->label('assign to')
                ->options(User::where('type',2)->pluck('name', 'id'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('age'),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('address'),
                Tables\Columns\TextColumn::make('pain_level')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('wizard')
                    ->label('Start Wizard')
                    ->url(fn (Patient $record): string => ExaminationRecord::getUrl(['patient' => $record])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatientLists::route('/'),
            'create' => Pages\CreatePatientList::route('/create'),
            'edit' => Pages\EditPatientList::route('/{record}/edit'),
        ];
    }
}

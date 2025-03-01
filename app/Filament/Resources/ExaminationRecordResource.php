<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalRecordResource\Pages;
use App\Filament\Resources\MedicalRecordResource\RelationManagers;
use App\Models\Examination;
use App\Models\MedicalRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExaminationRecordResource extends Resource
{
    protected static ?string $model = Examination::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Examination Records';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['student', 'instructor']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn()=>null)
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereHas('patient', function ($subQuery) {
                    $subQuery->where('status', 1);
                });
            })
            ->columns([
                TextColumn::make('patient.name')
                    ->label('Name'),
                TextColumn::make('patient.age')
                    ->label('Age'),
                TextColumn::make('patient.gender')
                    ->label('Gender'),
                TextColumn::make('patient.phone')
                    ->label('Phone'),
                TextColumn::make('patient.address')
                    ->label('Address'),
                TextColumn::make('patient.status')
                    ->formatStateUsing(function ($record, $state) {
                        return  $state==0?"In Progress": "Done";
                    })->color(function ($state) {

                        return $state === 0 ? 'success' : 'warning';
                    })->badge()
                    ->label('Status'),
                Tables\Columns\TextColumn::make('patient.pain_level')
                    ->label('Pain Level')
                    ->badge(),
                Tables\Columns\TextColumn::make('grade')
                    ->formatStateUsing(function ($record, $state) {
                        // Decode the JSON string into an associative array
                        $grades = json_decode($state, true);

                        // Extract individual grades
                        $basicInfoGrade = $grades['basic_information_grade'] ?? 'N/A';
                        $dentalHistoryGrade = $grades['dental_history_grade'] ?? 'N/A';
                        $extraExaminationGrade = $grades['extra_examination_grade'] ?? 'N/A';
                        $intraExaminationGrade = $grades['intra_examination_grade'] ?? 'N/A';

                        // Format the grades with line breaks
                        return "Basic Info: $basicInfoGrade<br>"
                            . "Dental History: $dentalHistoryGrade<br>"
                            . "Extra Exam: $extraExaminationGrade<br>"
                            . "Intra Exam: $intraExaminationGrade";
                    })
                    ->html() // Allow HTML rendering
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMedicalRecords::route('/'),
            'create' => Pages\CreateMedicalRecord::route('/create'),
            'edit' => Pages\EditMedicalRecord::route('/{record}/edit'),
        ];
    }
}

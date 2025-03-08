<?php

namespace App\Filament\Resources;

use App\Filament\Pages\ExaminationRecord;
use App\Filament\Resources\ApprovalResource\Pages;
use App\Filament\Resources\ApprovalResource\RelationManagers;
use App\Models\Approval;
use App\Models\Examination;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApprovalResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Approvals';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereNotNull('user_id')->with('student')->whereHas('student', function (Builder $query) {
                    $query->where('subject', auth()->user()->instructor->subject); // Filter by the authenticated user's subject

                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->description(function ($record) {
                        return $record->student->user->name??'';
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('age'),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address'),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(function ($record, $state) {
                        return  $state==0?"In Progress": "Done";
                    })->color(function ($state) {

                        return $state === 0 ? 'success' : 'warning';
                    })->badge(),
                Tables\Columns\TextColumn::make('pain_level')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('wizard')
                    ->label('Examination')
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
            'index' => Pages\ListApprovals::route('/'),
            'create' => Pages\CreateApproval::route('/create'),
            'edit' => Pages\EditApproval::route('/{record}/edit'),
        ];
    }
}

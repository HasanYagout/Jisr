<?php

namespace App\Filament\Resources;

use App\Filament\Pages\ExaminationRecord;
use App\Filament\Resources\PatientListResource\Pages;
use App\Models\Patient;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PatientListResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return auth()->check() && auth()->user()->hasRole('student') ? 'My Patients' : 'Patient List';
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->disabled(auth()->user()->hasRole(['instructor','admin'])),
                TextInput::make('age')
                    ->numeric()
                    ->minValue(0) // Ensure age is not negative
                    ->maxValue(120) // Set a reasonable maximum age
                    ->required()
                    ->disabled(auth()->user()->hasRole(['instructor','admin'])),
                TextInput::make('gender')->disabled(auth()->user()->hasRole(['instructor','admin'])),
                TextInput::make('phone')
                    ->tel()
                    ->length(9)
                    ->required()
                    ->disabled(auth()->user()->hasRole(['instructor','admin'])),
                TextInput::make('address')->disabled(auth()->user()->hasRole(['instructor','admin'])),
                Forms\Components\Select::make('pain_level')
                    ->disabled(auth()->user()->hasRole(['instructor','admin']))
                ->options(['mild','moderate','severe']),
                Forms\Components\Textarea::make('complaint')
                    ->disabled(auth()->user()->hasRole(['instructor','admin'])),
                Forms\Components\Textarea::make('dental_history')
                    ->disabled(auth()->user()->hasRole(['instructor','admin'])),
                Forms\Components\FileUpload::make('dental_history_file')
                    ->directory('uploads') // This line can be removed if you want to save directly to the root.
                    ->acceptedFileTypes(['application/pdf']),
                Forms\Components\Select::make('user_id')
                    ->required()
                    ->label('assign to')
                    ->disabled(auth()->user()->hasRole(['student','admin']))
                ->options(
                    User::where('type',1)
                    ->when(auth()->user()->hasRole(['instructor']),function (Builder $query){
                        $query->whereHas('student',function ($query){
                            $query->where('subject',auth()->user()->instructor->subject);
                        });
                    })
                        ->pluck('name','id')
                    ),
                Select::make('diagnosis')
                    ->required()
                    ->hidden(auth()->user()->hasRole(['student','admin']))
                ->options(['Complete'=>'Complete','Partial'=>'Partial'])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $student=auth()->user()->hasRole(['student']);
                if ($student) {
                return $query->with('student')->where('user_id',auth()->id())->where('status',0);
                }

                return $query->with('student')->where('status',0)->whereNull('user_id');
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
                      return  $state==0?"Not Assigned": "In Progress";
                    })->color(function ($state) {

                        return $state === 0 ? 'success' : 'warning';
                    })->badge(),
                Tables\Columns\TextColumn::make('pain_level')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                ->options(['Male'=>'Male','Female'=>'Female']),
                Tables\Filters\SelectFilter::make('pain_level')
                    ->options(['Mild'=>'Mild','Moderate'=>'Moderate','Severe'=>'Severe']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Patient $record) {
                        $record->examination->delete();
                    })

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(auth()->user()->hasPermissionTo('delete_patient::list')),
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

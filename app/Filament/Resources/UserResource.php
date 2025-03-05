<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(User::Type)
                    ->reactive()
                    ->afterStateHydrated(function ($component, $state) {
                        // Set the initial state of the type field
                        $component->state($state);
                    }),
                TextInput::make('student_id')
                    ->unique(table: 'students', column: 'student_id') // Apply unique rule to the `students` table
                    ->visible(function ($get) {
                        return $get('type') == 1; // Show only if type is '1'
                    })
                    ->required(function ($get) {
                        return $get('type') == 1; // Required only if type is '1'
                    })
                    ->rules([
                        Rule::unique('students', 'student_id')->whereNotNull('student_id'), // Custom unique rule
                    ]),
                Forms\Components\TextInput::make('subject')
                    ->visible(function ($get) {
                        return $get('type') == 3; // Show only if type is '3'
                    })
                    ->required(function ($get) {
                        return $get('type') == 3; // Required only if type is '3'
                    }),
                Forms\Components\Select::make('level')
                    ->visible(function ($get) {
                        return $get('type') == 1; // Show only if type is '1'
                    })
                    ->options([
                        '1' => 'Level 3',
                        '2' => 'Level 4',
                        '3' => 'Level 5',
                    ])
                    ->required(function ($get) {
                        return $get('type') == 1; // Required only if type is '1'
                    }),
                Forms\Components\Select::make('roles') // Changed from 'role' to 'roles'
                ->relationship('roles', 'name')
                    ->options(Role::all()->pluck('name', 'id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->sortable()
                ->searchable(),
                TextColumn::make('type')
                    ->formatStateUsing(function ($record, $state) {

                        return self::$model::Type[$state]; // Convert the type value to its label
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            1 => 'success', // Student (green)
                            2 => 'warning', // Instructor (yellow)
                            3 => 'danger',  // Admin (red)
                            default => 'gray', // Default (gray)
                        };
                    })
                    ->icon(function ($state) {
                        // Assign icons based on the type
                        return match ($state) {
                            1 => 'heroicon-o-shield-check', // Student
                            2 => 'heroicon-o-user-circle',  // Instructor
                            3 => 'heroicon-o-academic-cap',  // Admin
                            default => 'heroicon-o-question-mark-circle', // Default
                        };
                    })
                    ->badge(), // Display as a badge
                 Tables\Columns\TextColumn::make('roles.name')
                     ->label('Role')
                     ->formatStateUsing(function ($state){
                         return __($state);
                     })
                     ->translateLabel(),
                Tables\Columns\ToggleColumn::make('status')

        ])
            ->filters([
                Tables\Filters\QueryBuilder::make()
                ->constraints([
                    SelectConstraint::make('type')
                        ->options(self::$model::Type)
                        ->multiple(),
                    Tables\Filters\QueryBuilder\Constraints\BooleanConstraint::make('status')
                ])->constraintPickerColumns(2)
            ],layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

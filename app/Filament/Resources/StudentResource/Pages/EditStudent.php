<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\EditRecord;

class EditStudent extends EditRecord
{
    use HasWizard;
    protected static string $resource = StudentResource::class;

    protected function fillForm(): void
    {
        // Load the student and its related user
        $this->record->load('user');

        // Fill the form with the student and user data
        $this->form->fill([
            'name' => $this->record->user->name,
            'email' => $this->record->user->email,
            'student_id' => $this->record->student_id,
            'level' => $this->record->level,
        ]);
    }
    public function getSteps(): array
    {
        return [
            Step::make('Name')
                ->description('Give the category a clear and unique name')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')
                        ->disabled()
                        ->required()
                ]),
            Step::make('Description')
                ->description('Add some extra details')
                ->schema([
                    MarkdownEditor::make('description')
                        ->columnSpan('full'),
                ]),
            Step::make('Visibility')
                ->description('Control who can view it')
                ->schema([
                    Toggle::make('is_visible')
                        ->label('Visible to customers.')
                        ->default(true),
                ]),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

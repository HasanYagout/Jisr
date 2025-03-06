<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function fillForm(): void
    {
        // Load the record being edited
        $record = $this->getRecord();

        // Load related data (e.g., Student or Instructor)
        $studentData = $record->type == 1 ? $record->student : null;
        $instructorData = $record->type == 3 ? $record->instructor : null;

        // Fill the form with the record's data
        $this->form->fill([
            'name' => $record->name,
            'email' => $record->email,
            'type' => $record->type,
            'student_id' => $studentData->student_id ?? null, // Fill student_id if type is 1
            'level' => $studentData->level ?? null, // Fill level if type is 1
            'subject' => $instructorData->subject ?? null, // Fill subject if type is 3
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->action(function (Model $record) {
                    if ($record->type == 1) {
                        $record->student->delete();
                    } elseif ($record->type == 3) {
                        $record->instructor->delete();
                        $record->delete();
                    }
                    else{
                        $record->delete();

                    }

                    Notification::make()
                        ->title('Record deleted successfully')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Update the user record
        $record->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'type' => $data['type'],
        ]);

        // Handle related data based on the type
        if ($data['type'] == 1) {
            // Update or create student data
            $record->student()->updateOrCreate(
                ['user_id' => $record->id],
                [
                    'student_id' => $data['student_id'],
                    'level' => $data['level'],
                ]
            );

            $record->instructor()->delete();
        } elseif ($data['type'] == 2) {

        } elseif ($data['type'] == 3) {
            // Update or create instructor data
            $record->instructor()->updateOrCreate(
                ['user_id' => $record->id],
                [
                    'subject' => $data['subject'],
                ]
            );

            // Delete student data if it exists
            $record->student()->delete();
        }

        return $record;
    }
}

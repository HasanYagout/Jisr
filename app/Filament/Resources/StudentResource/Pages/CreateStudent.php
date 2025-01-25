<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // You can still modify the data here if needed
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Create the User record
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => 2,
        ]);

        // Create the Student record
        $student = Student::create([
            'student_id' => $data['student_id'],
            'user_id' => $user->id,
            'level' => $data['level'],
        ]);

        // Return the Student model
        return $student;
    }
}

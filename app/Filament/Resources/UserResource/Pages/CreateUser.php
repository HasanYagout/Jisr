<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // Import the Role model

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('password'), // Default password
            'type' => $data['type'],
        ]);
        if ($data['type']==1){
            // Create the Student associated with the User
            Student::create([
                'user_id' => $user->id,
                'student_id' => $data['student_id'],
                'level' => $data['level'],
            ]);
        }
        elseif ($data['type']==3){
            Instructor::create([
                'user_id' => $user->id,
                'subject'=>$data['subject'],
            ]);
        }


        // Assign the role to the user
        $role = Role::find($data['roles']); // Find the role by ID
        if ($role) {
            $user->assignRole($role); // Assign the role to the user
        } else {
            // Handle the case where the role is not found (optional)
            throw new \Exception("Role with ID {$data['roles']} not found.");
        }

        return $user;
    }
}

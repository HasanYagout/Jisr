<?php

namespace App\Filament\Resources\PatientListResource\Pages;

use App\Filament\Resources\PatientListResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePatientList extends CreateRecord
{
    protected static string $resource = PatientListResource::class;
}

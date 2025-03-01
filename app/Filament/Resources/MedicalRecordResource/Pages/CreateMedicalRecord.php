<?php

namespace App\Filament\Resources\MedicalRecordResource\Pages;

use App\Filament\Resources\ExaminationRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicalRecord extends CreateRecord
{
    protected static string $resource = ExaminationRecordResource::class;
}

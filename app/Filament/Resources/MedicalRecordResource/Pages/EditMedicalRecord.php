<?php

namespace App\Filament\Resources\MedicalRecordResource\Pages;

use App\Filament\Resources\ExaminationRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMedicalRecord extends EditRecord
{
    protected static string $resource = ExaminationRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

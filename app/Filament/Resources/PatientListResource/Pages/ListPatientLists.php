<?php

namespace App\Filament\Resources\PatientListResource\Pages;

use App\Filament\Resources\PatientListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPatientLists extends ListRecords
{
    protected static string $resource = PatientListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

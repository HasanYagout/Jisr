<?php

namespace App\Filament\Pages;

use App\Models\Examination; // Replace with your model
use Filament\Pages\Page;
use Livewire\WithPagination;

class MedicalRecords extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.medical-records';


    public function getViewData(): array
    {
        return [
            'records' => Examination::with('patient')
                ->whereHas('patient', fn($query) => $query->where('status', 1))
                ->paginate(10)
        ];
    }
}

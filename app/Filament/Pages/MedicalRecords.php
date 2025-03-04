<?php

namespace App\Filament\Pages;

use App\Models\Examination; // Replace with your model
use Filament\Pages\Page;
use Livewire\WithPagination;

class MedicalRecords extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.medical-records';

    public $search = ''; // Add a search property

    public function getViewData(): array
    {
        return [
            'records' => Examination::with('patient')
                ->whereHas('patient', fn($query) => $query->where('status', 1))
                ->when($this->search, function ($query) {
                    $query->whereHas('patient', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%')
                            ->orWhere('address', 'like', '%' . $this->search . '%');
                    });
                })
                ->paginate(10)
        ];
    }
}

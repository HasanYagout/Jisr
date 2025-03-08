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
        // Base query with the 'patient' relationship
        $query = Examination::with('patient')
            ->whereHas('patient', function ($query) {
                $query->where('status', 1); // Ensure patient status is 1
            });

        // Role-based filtering
        if (auth()->user()->hasRole('instructor')) {
            // Filter patients where the student's subject matches the instructor's subject
            $query->whereHas('patient.student', function ($query) {
                $query->where('subject', auth()->user()->instructor->subject);
            });
        } elseif (auth()->user()->hasRole('student')) {
            // Filter patients where the patient's user_id matches the authenticated user's ID
            $query->whereHas('patient', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }

        // Search functionality
        if ($this->search) {
            $query->whereHas('patient', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%');
            });
        }

        // Paginate the results
        $records = $query->paginate(10);

        return [
            'records' => $records,
        ];
    }
}

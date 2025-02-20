<?php
namespace App\Filament\Pages;

use App\Models\Examination;
use App\Models\Patient;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class ExaminationRecord extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.examination-record';

    public ?array $data = [];

    public Patient $patient;
    public Examination $examination;


    public function mount(): void
    {
        $patientId = request()->query('patient');
        $this->patient = Patient::with('examination')->findOrFail($patientId);

        $this->examination = $this->patient->examination;

        // Decode the medical_history JSON
        $medicalHistory = json_decode($this->patient->medical_history, true) ?? [];

        // Define default structure for medical_history
        $defaultMedicalHistory = [
            'cardiac_disease' => null,
            'hypertension' => null,
            'diabetes' => null,
            'others' => null,
        ];

        // Merge input JSON with default structure
        $medicalHistory = array_merge($defaultMedicalHistory, $medicalHistory);

        // Fill the form with the patient's data
        $this->form->fill([
            'name' => $this->patient->name,
            'age' => $this->patient->age,
            'phone' => $this->patient->phone,
            'medical_history' => array_keys(array_filter($medicalHistory, function ($value, $key) {
                return $value === 'Yes' && in_array($key, ['cardiac_disease', 'hypertension', 'diabetes']);
            }, ARRAY_FILTER_USE_BOTH)),
            'medical_history_others' => $medicalHistory['others'] ?? '',
            'occupation' => $this->patient->occupation,
            'address' => $this->patient->address,
            'gender' => $this->patient->gender,
            'pain_level' => $this->patient->pain_level,
            'complaint' => $this->patient->complaint,
            'dental_history' => $this->patient->dental_history,
            'dental_history_file' => $this->patient->dental_history_file,
        ]);
    }
    public function form(Form $form): Form
    {
        // Define tooth status options
        $toothStatusOptions = [
            'missing' => 'Missing',
            'decay' => 'Decay',
            'crown' => 'Crown',
            'mobile' => 'Mobile',
        ];


        return $form
            ->schema([
                Wizard::make([
                    // Step 1: Basic Information
                    Wizard\Step::make('Basic Information')
                        ->columns(3)
                        ->schema([
                            TextInput::make('name')
                                ->label('Patient Name')
                                ->required(),
                            TextInput::make('age')
                                ->numeric()
                                ->required(),
                            TextInput::make('phone')
                                ->tel()
                                ->required(),
                            TextInput::make('occupation')
                                ->required(),
                            TextInput::make('address')
                                ->required(),
                            TextInput::make('gender')
                                ->required(),
                            CheckboxList::make('medical_history')
                                ->label('Medical History')
                                ->options([
                                    'cardiac_disease' => 'Cardiac Disease',
                                    'hypertension' => 'Hypertension',
                                    'diabetes' => 'Diabetes',
                                ])
                                ->default(function () {
                                    // Decode the JSON data
                                    $medicalHistory = json_decode($this->patient->medical_history, true);

                                    // Map "Yes" values to selected options
                                    return array_keys(array_filter($medicalHistory, function ($value, $key) {
                                        return $value === 'Yes' && in_array($key, ['cardiac_disease', 'hypertension', 'diabetes']);
                                    }, ARRAY_FILTER_USE_BOTH));
                                }),

                            TextInput::make('medical_history_others')
                                ->label('Others')
                                ->default(function () {
                                    // Decode the JSON data
                                    $medicalHistory = json_decode($this->patient->medical_history, true);

                                    // Get the "others" field
                                    return $medicalHistory['others'] ?? '';
                                }),
                            TextArea::make('complaint')
                                ->columnSpan(2),
                            TextArea::make('dental_history')
                                ->columnSpan(2),
                            Select::make('pain_level')
                                ->options([
                                    'mild'=>'mild',
                                    'moderate'=>'moderate',
                                    'severe'=>'severe',
                                ])
                                ->columnSpan(2),
                        ]),

                    // Step 2: Dental History
                    Wizard\Step::make('dental_history')
                        ->columns(2)
                        ->label('Dental History')
                        ->schema([
                            TextInput::make('last_extraction'),
                            CheckboxList::make('problem_satisfaction_patient')
                                ->label('Problem and satisfaction for the existing prosthesis(patient):')
                                ->options([
                                    'retention' => 'retention',
                                    'stability' => 'stability',
                                    'appearance' => 'appearance',
                                    'speech' => 'speech',
                                    'mastication' => 'mastication',
                                ])->columns(3),

                            CheckboxList::make('problem_satisfaction_dentist')
                                ->label('Problem and satisfaction for the existing prosthesis(Dentist):')
                                ->options([
                                    'retention' => 'retention',
                                    'stability' => 'stability',
                                    'appearance' => 'appearance',
                                    'Vertical_dimension' => 'Vertical_dimension',
                                    'centric_relation' => 'centric_relation',
                                    'Teeth_attrition' => 'Teeth_attrition',
                                ])->columns(3),
                        ]),

                    // Step 3: Extra-Examination
                    Wizard\Step::make('Extra-Examination')
                        ->schema([
                            Placeholder::make('examination_label')
                                ->label('Extra-oral-examination:'),
                            CheckboxList::make('face_form')
                                ->options([
                                    'square' => 'square',
                                    'square_tapering' => 'square_tapering',
                                    'tapering' => 'tapering',
                                    'oviod' => 'oviod',
                                ])->columns(4),
                            CheckboxList::make('Facial_Profile')
                                ->options([
                                    'class1' => 'class1',
                                    'class2' => 'class2',
                                    'class3' => 'class3',
                                ])->columns(4),
                            CheckboxList::make('Facial_complexion')
                                ->options([
                                    'dark' => 'dark',
                                    'medium' => 'medium',
                                    'light' => 'light',
                                ])->columns(4),
                            TextInput::make('tmj')
                                ->label('TMJ'),
                        ]),

                    // Step 4: Intra-Examination
                    Wizard\Step::make('Intra-Examination')
                        ->schema([
                         Repeater::make('da')
                        ]),
                ])
                    ->skippable()
                    ->submitAction(
                        \Filament\Forms\Components\Actions\Action::make('submit')
                            ->label('Submit')
                            ->submit('submit')
                    ),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

//        dd($data);
        $currentMedicalHistory = json_decode($this->patient->medical_history, true) ?? [];

// Merge the current medical_history with the new medical_history data
        $updatedMedicalHistory = array_merge($currentMedicalHistory, $data['medical_history'] ?? []);

// Update the patient record
        $this->patient->update([
            'name' => $data['name'] ?? $this->patient->name,
            'age' => $data['age'] ?? $this->patient->age,
            'phone' => $data['phone'] ?? $this->patient->phone,
            'occupation' => $data['occupation'] ?? $this->patient->occupation,
            'address' => $data['address'] ?? $this->patient->address,
            'gender' => $data['gender'] ?? $this->patient->gender,
            'medical_history' => json_encode($updatedMedicalHistory), // Updated medical_history
            'complaint' => $data['complaint'] ?? $this->patient->complaint,
            'dental_history' => $data['dental_history'] ?? $this->patient->dental_history,
            'pain_level' => $data['pain_level'] ?? $this->patient->pain_level,
        ]);

        $this->examination->update([
            'last_extraction' => $data['last_extraction'] ?? null, // Set to null if empty
            'problem_satisfaction_patient' => isset($data['problem_satisfaction_patient']) ? json_encode($data['problem_satisfaction_patient']) : null, // Encode only if not empty
            'problem_satisfaction_dentist' => isset($data['problem_satisfaction_dentist']) ? json_encode($data['problem_satisfaction_dentist']) : null, // Encode only if not empty
            'face_form' => $data['face_form'] ?? null, // Set to null if empty
            'facial_profile' => $data['facial_profile'] ?? null, // Set to null if empty
            'facial_complexion' => $data['facial_complexion'] ?? null, // Set to null if empty
            'tmj' => $data['tmj'] ?? null, // Set to null if empty
        ]);        // Optionally, display a success message
        Notification::make()
            ->success()
            ->title('Success')
            ->body('Patient information updated successfully!')
            ->send();
    }
}

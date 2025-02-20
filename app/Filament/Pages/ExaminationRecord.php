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
        $medicalHistory = json_decode($this->patient->medical_history, true) ?? [];
        $selectedMedicalHistory = array_keys(array_filter($medicalHistory, fn($key) => $key !== 'medical_history_others', ARRAY_FILTER_USE_KEY));

        $problemSatisfactionPatient = json_decode($this->patient->examination->problem_satisfaction_patient, true) ?? [];
        $problemSatisfactionDentist = json_decode($this->patient->examination->problem_satisfaction_dentist, true) ?? [];

        $this->form->fill([
            'name' => $this->patient->name,
            'age' => $this->patient->age,
            'phone' => $this->patient->phone,
            'medical_history' => $selectedMedicalHistory,
            'medical_history_others' => $medicalHistory['medical_history_others'] ?? '',
            'occupation' => $this->patient->occupation,
            'address' => $this->patient->address,
            'gender' => $this->patient->gender,
            'pain_level' => $this->patient->pain_level,
            'complaint' => $this->patient->complaint,
            'dental_history' => $this->patient->dental_history,
            'dental_history_file' => $this->patient->dental_history_file,
            'last_extraction' => $this->patient->examination->last_extraction,
            'problem_satisfaction_patient' => $problemSatisfactionPatient,
            'problem_satisfaction_dentist' => $problemSatisfactionDentist,

        ]);
    }    public function form(Form $form): Form
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
                               ,


                            TextInput::make('medical_history_others')
                                ->label('Others'),
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
                        ->label('Intra-Examination')
                        ->schema([
                            Repeater::make('teeth')
                                ->columnSpan(2)
                                ->label('Intra-Examination Details')
                                ->grid(2)
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('tooth_number')
                                                ->label('Tooth Number')
                                                ->columnSpan(1)
                                                ->required(),
                                            Select::make('condition')
                                                ->label('Condition')
                                                ->columnSpan(1)
                                                ->options([
                                                    'caries' => 'Caries',
                                                    'missing' => 'Missing',
                                                    'filled' => 'Filled',
                                                    'fractured' => 'Fractured',
                                                    'healthy' => 'Healthy',
                                                ])
                                                ->required(),
                                            Textarea::make('notes')
                                                ->label('Notes')
                                                ->columnSpan(2)
                                                ->placeholder('Additional notes for this tooth...')
                                                ->rows(5),
                                        ]),
                                ])
                                ->columns(1)
                                ->createItemButtonLabel('Add Tooth')
                                ->default([]),
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
        $medicalHistory = $data['medical_history'];
        $medicalHistoryOthers = $data['medical_history_others'];

        $medicalData = array_merge(
            array_combine($medicalHistory, $medicalHistory),
            array('medical_history_others' => $medicalHistoryOthers)
        );

// Encode the array into JSON
        $updatedMedicalHistory = json_encode($medicalData);

        $this->patient->update([
            'name' => $data['name'] ?? $this->patient->name,
            'age' => $data['age'] ?? $this->patient->age,
            'phone' => $data['phone'] ?? $this->patient->phone,
            'occupation' => $data['occupation'] ?? $this->patient->occupation,
            'address' => $data['address'] ?? $this->patient->address,
            'gender' => $data['gender'] ?? $this->patient->gender,
            'medical_history' => $updatedMedicalHistory,
            'complaint' => $data['complaint'] ?? $this->patient->complaint,
            'dental_history' => $data['dental_history'] ?? $this->patient->dental_history,
            'pain_level' => $data['pain_level'] ?? $this->patient->pain_level,
        ]);

        $this->examination->update([
            'last_extraction' => $data['last_extraction'] ?? null, // Set to null if empty
            'problem_satisfaction_patient' => isset($data['problem_satisfaction_patient']) ? json_encode($data['problem_satisfaction_patient']) : null,
            'problem_satisfaction_dentist' => isset($data['problem_satisfaction_dentist']) ? json_encode($data['problem_satisfaction_dentist']) : null,
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

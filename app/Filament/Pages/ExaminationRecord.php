<?php

namespace App\Filament\Pages;

use App\Models\Examination;
use App\Models\Patient;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ExaminationRecord extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.examination-record';

    public ?array $data = [];
    protected static bool $shouldRegisterNavigation = false;
    public Patient $patient;
    public Examination $examination;


    public function mount(): void
    {
        $patientId = request()->query('patient');
        $this->patient = Patient::with('examination')->findOrFail($patientId);
        $this->examination = $this->patient->examination;

        // Decode the medical_history JSON
        $medicalHistory = json_decode($this->patient->medical_history, true) ?? [];
        $defaultMedicalHistory = [
            'cardiac_disease' => null,
            'hypertension' => null,
            'diabetes' => null,
            'others' => null,
        ];
        $defaultFaceForm = [
            'oviod' => null,
            'tapering' => null,
            'square_tapering' => null,
            'square' => null,
        ];
        $teethData = json_decode($this->patient->examination->teeth, true) ?? [];

        $formattedTeethData = array_map(function ($tooth) {
            return [
                'tooth_number' => $tooth['tooth_number'] ?? null,
                'condition' => $tooth['condition'] ?? null,
                'notes' => $tooth['notes'] ?? null,
            ];
        }, $teethData);
        // Face form data
        $faceForm = json_decode($this->patient->examination->face_form, true) ?? [];
        $medicalHistory = array_merge($defaultMedicalHistory, $medicalHistory);
        $grade=json_decode($this->patient->examination->grade);
//        dd(json_decode($this->patient->medical_history, true));

        $this->form->fill([
            'name' => $this->patient->name,
            'age' => $this->patient->age,
            'phone' => $this->patient->phone,
            'medical_history' => array_keys(array_filter($medicalHistory)), // Populate with checked values
            'medical_history_others' => $medicalHistory['medical_history_others'] ?? '',
            'occupation' => $this->patient->occupation,
            'address' => $this->patient->address,
            'face_form' => json_decode($this->patient->examination->face_form, true) ?? [],
            'facial_profile' => json_decode($this->patient->examination->facial_profile, true) ?? [],
            'facial_complexion' => json_decode($this->patient->examination->facial_complexion, true) ?? [],
            'gender' => $this->patient->gender,
            'pain_level' => $this->patient->pain_level,
            'complaint' => $this->patient->complaint,
            'dental_history' => $this->patient->dental_history,
            'dental_history_file' => $this->patient->dental_history_file,
            'problem_satisfaction_dentist' => json_decode($this->patient->examination->problem_satisfaction_dentist, true) ?? [],
            'problem_satisfaction_patient' => json_decode($this->patient->examination->problem_satisfaction_patient, true) ?? [],
            'last_extraction'=>$this->patient->examination->last_extraction,
            'tmj' => $this->patient->examination->tmj,
            'soft_tissue_upper' => $this->patient->examination->soft_tissue_upper,
            'soft_tissue_lower' => $this->patient->examination->soft_tissue_period,
            'soft_tissue_period' => $this->patient->examination->soft_tissue_period,
            'treatment_plan' => $this->patient->examination->treatment_plan,
            'diagnosis_classes' => $this->patient->examination->diagnosis_classes,
            'teeth' => $formattedTeethData,
            'basic_information_grade'=>$grade->basic_information_grade,
            'dental_history_grade'=>$grade->dental_history_grade,
            'extra_examination_grade'=>$grade->extra_examination_grade,
            'intra_examination_grade'=>$grade->intra_examination_grade,
            'final_evaluation_diagnose'=>json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_diagnose'] ?? [],
            'final_evaluation_primary_impression'=>json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_primary_impression'] ?? [],
            'final_evaluation_border_molding'=>json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_border_molding'] ?? [],
            'final_evaluation_secondary_impression'=>json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_secondary_impression'] ?? [],
            'final_evaluation_designing'=>json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_designing'] ?? [],
            'final_evaluation_vertical_dimension'=>json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_vertical_dimension'] ?? [],
            'final_evaluation_centric_relation'=>json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_centric_relation'] ?? [],
            'final_evaluation_try_in'=>json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_try_in'] ?? [],
            'final_evaluation_insertion'=>json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_insertion'] ?? [],
            'final_evaluation_recall'=>json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_recall'] ?? [],
        ]);
    }

    public function form(Form $form): Form
    {

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
                            Select::make('gender')
                                ->options([
                                    'Male' => 'male',
                                    'Female' => 'female',
                                ])
                                ->required(),
                            CheckboxList::make('medical_history')
                                ->label('Medical History')
                                ->options([
                                    'cardiac_disease' => 'Cardiac Disease',
                                    'hypertension' => 'Hypertension',
                                    'diabetes' => 'Diabetes',
                                ]),

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
                                    'mild' => 'mild',
                                    'moderate' => 'moderate',
                                    'severe' => 'severe',
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
                                ->label('Problem and satisfaction for the existing prosthesis (Patient):')
                                ->options([
                                    'retention' => 'Retention',
                                    'stability' => 'Stability',
                                    'appearance' => 'Appearance',
                                    'speech' => 'speech',
                                    'Mastication' => 'Mastication',
                                ])
                                ->columns(3),


                            CheckboxList::make('problem_satisfaction_dentist')
                                ->label('Problem and satisfaction for the existing prosthesis (Dentist):')
                                ->options([
                                    'retention' => 'Retention',
                                    'stability' => 'Stability',
                                    'appearance' => 'Appearance',
                                    'Vertical_dimension' => 'Vertical Dimension',
                                    'centric_relation' => 'Centric Relation',
                                    'Teeth_attrition' => 'Teeth Attrition',
                                ])
                                ->columns(3),

                        ]),

                    Wizard\Step::make('Extra-Examination')
                        ->schema([
                            Placeholder::make('examination_label')
                                ->label('Extra-oral-examination:'),
                            CheckboxList::make('face_form')
                                ->options([
                                    'square' => 'Square',
                                    'square_tapering' => 'Square Tapering',
                                    'tapering' => 'Tapering',
                                    'oviod' => 'Oviod',
                                ])
                                ->default(function () {
                                    return json_decode($this->examination->face_form, true) ?? [];

                                })
                                ->columns(4),
                            CheckboxList::make('facial_profile')
                                ->options([
                                    'class1' => 'Class 1',
                                    'class2' => 'Class 2',
                                    'class3' => 'Class 3',
                                ])
                                ->columns(4),
                            CheckboxList::make('facial_complexion')
                                ->options([
                                    'dark' => 'Dark',
                                    'medium' => 'Medium',
                                    'light' => 'Light',
                                ])
                                ->columns(4),
                            TextInput::make('tmj')
                                ->label('TMJ'),
                        ]),

                    Wizard\Step::make('Intra-Examination')
                        ->columns(2)
                        ->label('Intra-Examination')
                        ->schema([
                            TextInput::make('soft_tissue_upper'),
                            TextInput::make('soft_tissue_lower'),
                            TextInput::make('soft_tissue_period'),
                            Textarea::make('treatment_plan'),
                            Textarea::make('diagnosis_classes'),
                            Repeater::make('teeth')
                                ->columnSpan(2) // Span across 2 columns in the parent grid
                                ->label('Intra-Examination Details')
                                ->grid(2) // Ensure the repeater itself uses a 2-column grid
                                ->schema([
                                    Grid::make(2) // Use a 2-column grid for the row
                                    ->schema([
                                        // Tooth Number and Condition in the same row
                                        TextInput::make('tooth_number')
                                            ->label('Tooth Number')
                                            ->columnSpan(1) // Span 1 column in the 2-column grid
                                            ->required(),
                                        Select::make('condition')
                                            ->label('Condition')
                                            ->columnSpan(1) // Span 1 column in the 2-column grid
                                            ->options([
                                                'caries' => 'Caries',
                                                'missing' => 'Missing',
                                                'filled' => 'Filled',
                                                'fractured' => 'Fractured',
                                                'healthy' => 'Healthy',
                                            ])
                                            ->required(),
                                        // Notes field takes the full width below
                                        Textarea::make('notes')
                                            ->label('Notes')
                                            ->columnSpan(2) // Span across both columns
                                            ->placeholder('Additional notes for this tooth...')
                                            ->rows(5),
                                    ]),
                                ])
                                ->columns(1) // Each row in the repeater is treated as a single unit
                                ->createItemButtonLabel('Add Tooth')

                        ]),
                    Wizard\Step::make('final_evaluation')
                        ->columns(2)
                        ->schema([
                            Placeholder::make('evaluation'),
                            Card::make()
                                ->schema([
                                    // Diagnosis: Checkbox and Date
                                    Grid::make(2) // Two columns for checkbox and date
                                    ->schema([
                                        Checkbox::make('final_evaluation_diagnose.value')
                                            ->label('Diagnosis')
                                            ->reactive()
                                            ->columnSpan(1) // Ensure it takes 50% width
                                            ->extraAttributes(['class' => 'flex items-center justify-center']), // Center vertically and horizontally
                                        DateTimePicker::make('final_evaluation_diagnose.date')
                                            ->label('Date')
                                            ->hidden(fn($get) => !$get('final_evaluation_diagnose.value'))
                                            ->formatStateUsing(function ($record,$state) {
                                                if ($state){
                                                    return $state;
                                                }
                                                return now()->toDateTimeString();
                                            })
                                            ->columnSpan(1) // Ensure it takes 50% width
                                            ->extraAttributes(['class' => 'flex items-center justify-center']), // Center vertically and horizontally
                                    ]),

                                    // Primary Impression: Checkbox and Date
                                    Grid::make(2) // Two columns for checkbox and date
                                    ->schema([
                                        Checkbox::make('final_evaluation_primary_impression.value')
                                            ->label('Primary Impression')
                                            ->reactive()
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                        DateTimePicker::make('final_evaluation_primary_impression.date')
                                            ->label('Date')
                                            ->hidden(fn($get) => !$get('final_evaluation_primary_impression.value'))
                                            ->formatStateUsing(function ($state) {
                                                if ($state){
                                                    return $state;
                                                }
                                                return now()->toDateTimeString();
                                            })
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                    ]),

                                    // Border Molding: Checkbox and Date
                                    Grid::make(2) // Two columns for checkbox and date
                                    ->schema([
                                        Checkbox::make('final_evaluation_border_molding.value')
                                            ->label('Border Molding')
                                            ->reactive()
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                        DateTimePicker::make('final_evaluation_border_molding.date')
                                            ->label('Date')
                                            ->hidden(fn($get) => !$get('final_evaluation_border_molding.value'))
                                            ->formatStateUsing(function ($state) {
                                                if ($state){
                                                    return $state;
                                                }
                                                return now()->toDateTimeString();
                                            })
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                    ]),

                                    // Secondary Impression: Checkbox and Date
                                    Grid::make(2) // Two columns for checkbox and date
                                    ->schema([
                                        Checkbox::make('final_evaluation_secondary_impression.value')
                                            ->label('Secondary Impression')
                                            ->reactive()
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                        DateTimePicker::make('final_evaluation_secondary_impression.date')
                                            ->label('Date')
                                            ->hidden(fn($get) => !$get('final_evaluation_secondary_impression.value'))
                                            ->formatStateUsing(function ($state) {
                                                if ($state){
                                                    return $state;
                                                }
                                                return now()->toDateTimeString();
                                            })
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                    ]),
                                    Grid::make(2) // Two columns for checkbox and date
                                    ->schema([
                                        Checkbox::make('final_evaluation_designing.value')
                                            ->label('Designing')
                                            ->reactive()
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                        DateTimePicker::make('final_evaluation_designing.date')
                                            ->label('Date')
                                            ->hidden(fn($get) => !$get('final_evaluation_designing.value'))
                                            ->formatStateUsing(function ($state) {
                                                if ($state){
                                                    return $state;
                                                }
                                                return now()->toDateTimeString();
                                            })
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                    ]),
                                    Grid::make(2) // Two columns for checkbox and date
                                    ->schema([
                                        Checkbox::make('final_evaluation_vertical_dimension.value')
                                            ->label('Vertical Dimension')
                                            ->reactive()
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                        DateTimePicker::make('final_evaluation_vertical_dimension.date')
                                            ->label('Date')
                                            ->hidden(fn($get) => !$get('final_evaluation_vertical_dimension.value'))
                                            ->formatStateUsing(function ($state) {
                                                if ($state){
                                                    return $state;
                                                }
                                                return now()->toDateTimeString();
                                            })
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                    ]),
                                    Grid::make(2) // Two columns for checkbox and date
                                    ->schema([
                                        Checkbox::make('final_evaluation_centric_relation.value')
                                            ->label('Designing')
                                            ->reactive()
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                        DateTimePicker::make('final_evaluation_centric_relation.date')
                                            ->label('Date')
                                            ->hidden(fn($get) => !$get('final_evaluation_centric_relation.value'))
                                            ->formatStateUsing(function () {
                                                return now()->toDateTimeString();
                                            })
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                    ]),
                                    Grid::make(2) // Two columns for checkbox and date
                                    ->schema([
                                        Checkbox::make('final_evaluation_try_in.value')
                                            ->label('Try In')
                                            ->reactive()
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                        DateTimePicker::make('final_evaluation_try_in.date')
                                            ->label('Date')
                                            ->hidden(fn($get) => !$get('final_evaluation_try_in.value'))
                                            ->formatStateUsing(function ($state) {
                                                if ($state){
                                                    return $state;
                                                }
                                                return now()->toDateTimeString();
                                            })
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                    ]),
                                    Grid::make(2) // Two columns for checkbox and date
                                    ->schema([
                                        Checkbox::make('final_evaluation_insertion.value')
                                            ->label('Insertion')
                                            ->reactive()
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                        DateTimePicker::make('final_evaluation_insertion.date')
                                            ->label('Date')
                                            ->hidden(fn($get) => !$get('final_evaluation_insertion.value'))
                                            ->formatStateUsing(function ($state) {
                                                if ($state){
                                                    return $state;
                                                }
                                                return now()->toDateTimeString();
                                            })
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),

                                    ]),
                                    Grid::make(2) // Two columns for checkbox and date
                                    ->schema([
                                        Checkbox::make('final_evaluation_recall.value')
                                            ->label('Recall')
                                            ->reactive()
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                        DateTimePicker::make('final_evaluation_recall.date')
                                            ->label('Date')
                                            ->hidden(fn($get) => !$get('final_evaluation_recall.value'))
                                            ->formatStateUsing(function ($state) {
                                                if ($state){
                                                    return $state;
                                                }
                                                return now()->toDateTimeString();
                                            })
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center']),
                                    ]),
                                ]),

                            Placeholder::make('grades'),
                            Card::make()
                                ->schema([
                                    Grid::make(3)
                                        ->schema([
                                            TextInput::make('basic_information_grade')
                                                ->numeric()
                                                ->disabled(function ($get) {
                                                    // Check if all required fields in Basic Information are filled
                                                    $requiredFields = ['name', 'age', 'phone', 'occupation', 'address', 'gender', 'medical_history', 'complaint', 'dental_history', 'pain_level'];
                                                    foreach ($requiredFields as $field) {
                                                        if (empty($get($field))) {
                                                            return true; // Disable if any required field is empty
                                                        }
                                                    }
                                                    return false; // Enable if all required fields are filled
                                                }),
                                            TextInput::make('dental_history_grade')
                                                ->numeric()
                                                ->disabled(function ($get) {
                                                    // Check if all required fields in Dental History are filled
                                                    $requiredFields = ['last_extraction', 'problem_satisfaction_patient', 'problem_satisfaction_dentist'];
                                                    foreach ($requiredFields as $field) {
                                                        if (empty($get($field))) {
                                                            return true; // Disable if any required field is empty
                                                        }
                                                    }
                                                    return false; // Enable if all required fields are filled
                                                }),
                                            TextInput::make('extra_examination_grade')
                                                ->numeric()
                                                ->disabled(function ($get) {
                                                    // Check if all required fields in Extra-Examination are filled
                                                    $requiredFields = ['face_form', 'facial_profile', 'facial_complexion', 'tmj'];
                                                    foreach ($requiredFields as $field) {
                                                        if (empty($get($field))) {
                                                            return true; // Disable if any required field is empty
                                                        }
                                                    }
                                                    return false; // Enable if all required fields are filled
                                                }),
                                            TextInput::make('intra_examination_grade')
                                                ->numeric()
                                                ->disabled(function ($get) {
                                                    // Check if all required fields in Intra-Examination are filled
                                                    $requiredFields = ['soft_tissue_upper', 'soft_tissue_lower', 'soft_tissue_period', 'treatment_plan', 'diagnosis_classes', 'teeth'];
                                                    foreach ($requiredFields as $field) {
                                                        if (empty($get($field))) {
                                                            return true; // Disable if any required field is empty
                                                        }
                                                    }
                                                    return false; // Enable if all required fields are filled
                                                }),
                                        ]),

                                ]),
                        ])


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

//        // Define the required fields for each step
//        $requiredFields = [
//            'Basic Information' => ['name', 'age', 'phone', 'occupation', 'address', 'gender', 'medical_history', 'complaint', 'dental_history', 'pain_level'],
//            'Dental History' => ['last_extraction', 'problem_satisfaction_patient', 'problem_satisfaction_dentist'],
//            'Extra-Examination' => ['face_form', 'facial_profile', 'facial_complexion', 'tmj'],
//            'Intra-Examination' => ['soft_tissue_upper', 'soft_tissue_lower', 'soft_tissue_period', 'treatment_plan', 'diagnosis_classes', 'teeth'],
//            'Final Evaluation' => ['final_evaluation_diagnose', 'final_evaluation_primary_impression', 'final_evaluation_border_molding', 'final_evaluation_secondary_impression', 'final_evaluation_designing', 'final_evaluation_vertical_dimension', 'final_evaluation_try_in', 'final_evaluation_insertion', 'final_evaluation_recall'],
//        ];
//
//        // Check if all required fields are filled
//        $allFieldsFilled = true;
//        foreach ($requiredFields as $step => $fields) {
//            foreach ($fields as $field) {
//                if (empty($data[$field])) {
//                    $allFieldsFilled = false;
//                    break 2; // Exit both loops if any field is empty
//                }
//            }
//        }
//
//        if (!$allFieldsFilled) {
//            Notification::make()
//                ->danger()
//                ->title('Error')
//                ->body('Please fill all the required fields before submitting.')
//                ->send();
//            return;
//        }

        // Proceed with updating the grades and other data
        $grade = [
            'basic_information_grade' => $data['basic_information_grade'] ?? null,
            'dental_history_grade' => $data['dental_history_grade'] ?? null,
            'extra_examination_grade' => $data['extra_examination_grade'] ?? null,
            'intra_examination_grade' => $data['intra_examination_grade'] ?? null,
        ];

        $evaluation = [
            'final_evaluation_diagnose' => [
                'value' => $data['final_evaluation_diagnose']['value'] ?? false,
                'date' => $data['final_evaluation_diagnose']['value'] ? ($data['final_evaluation_diagnose']['date'] ?? now()->toDateTimeString()) : null,
            ],
            'final_evaluation_primary_impression' => [
                'value' => $data['final_evaluation_primary_impression']['value'] ?? false,
                'date' => $data['final_evaluation_primary_impression']['value'] ? ($data['final_evaluation_primary_impression']['date'] ?? now()->toDateTimeString()) : null,
            ],
            'final_evaluation_border_molding' => [
                'value' => $data['final_evaluation_border_molding']['value'] ?? false,
                'date' => $data['final_evaluation_border_molding']['value'] ? ($data['final_evaluation_border_molding']['date'] ?? now()->toDateTimeString()) : null,
            ],
            'final_evaluation_secondary_impression' => [
                'value' => $data['final_evaluation_secondary_impression']['value'] ?? false,
                'date' => $data['final_evaluation_secondary_impression']['value'] ? ($data['final_evaluation_secondary_impression']['date'] ?? now()->toDateTimeString()) : null,
            ],
            'final_evaluation_designing' => [
                'value' => $data['final_evaluation_designing']['value'] ?? false,
                'date' => $data['final_evaluation_designing']['value'] ? ($data['final_evaluation_designing']['date'] ?? now()->toDateTimeString()) : null,
            ],
            'final_evaluation_vertical_dimension' => [
                'value' => $data['final_evaluation_vertical_dimension']['value'] ?? false,
                'date' => $data['final_evaluation_vertical_dimension']['value'] ? ($data['final_evaluation_vertical_dimension']['date'] ?? now()->toDateTimeString()) : null,
            ],
            'final_evaluation_try_in' => [
                'value' => $data['final_evaluation_try_in']['value'] ?? false,
                'date' => $data['final_evaluation_try_in']['value'] ? ($data['final_evaluation_try_in']['date'] ?? now()->toDateTimeString()) : null,
            ],
            'final_evaluation_insertion' => [
                'value' => $data['final_evaluation_insertion']['value'] ?? false,
                'date' => $data['final_evaluation_insertion']['value'] ? ($data['final_evaluation_insertion']['date'] ?? now()->toDateTimeString()) : null,
            ],
            'final_evaluation_recall' => [
                'value' => $data['final_evaluation_recall']['value'] ?? false,
                'date' => $data['final_evaluation_recall']['value'] ? ($data['final_evaluation_recall']['date'] ?? now()->toDateTimeString()) : null,
            ],
        ];
        $MedicalHistoryArray = [];  // Initialize an empty array to hold the formatted data

        foreach ($data['medical_history'] as $key => $value) {
            $MedicalHistoryArray[$value] = $value;
        }
        $MedicalHistoryArray['medical_history_others']=$data['medical_history_others'];

        $this->patient->update([
            'name' => $data['name'] ?? $this->patient->name,
            'age' => $data['age'] ?? $this->patient->age,
            'phone' => $data['phone'] ?? $this->patient->phone,
            'occupation' => $data['occupation'] ?? $this->patient->occupation,
            'address' => $data['address'] ?? $this->patient->address,
            'gender' => $data['gender'] ?? $this->patient->gender,
            'medical_history' => json_encode($MedicalHistoryArray ?? []),
            'complaint' => $data['complaint'] ?? $this->patient->complaint,
            'dental_history' => $data['dental_history'] ?? $this->patient->dental_history,
            'pain_level' => $data['pain_level'] ?? $this->patient->pain_level,
        ]);

        // Update the examination record
        $this->examination->update([
            'last_extraction' => $data['last_extraction'],
            'problem_satisfaction_patient' => isset($data['problem_satisfaction_patient']) ? json_encode($data['problem_satisfaction_patient']) : null,
            'problem_satisfaction_dentist' => isset($data['problem_satisfaction_dentist']) ? json_encode($data['problem_satisfaction_dentist']) : null,
            'face_form' => isset($data['face_form']) ? json_encode($data['face_form']) : null,
            'facial_profile' => isset($data['facial_profile']) ? json_encode($data['facial_profile']) : null,
            'facial_complexion' => isset($data['facial_complexion']) ? json_encode($data['facial_complexion']) : null,
            'tmj' => $data['tmj'] ?? null,
            'soft_tissue_upper' => $data['soft_tissue_upper'] ?? null,
            'soft_tissue_lower' => $data['soft_tissue_lower'] ?? null,
            'soft_tissue_period' => $data['soft_tissue_period'] ?? null,
            'treatment_plan' => $data['treatment_plan'] ?? null,
            'diagnosis_classes' => $data['diagnosis_classes'] ?? null,
            'teeth' => json_encode($data['teeth']) ?? null,
            'final_evaluation' => json_encode($evaluation) ?? null,
            'grade' => json_encode($grade) ?? null,
        ]);

        // Display a success message
        Notification::make()
            ->success()
            ->title('Success')
            ->body('Patient information updated successfully!')
            ->send();
    }}


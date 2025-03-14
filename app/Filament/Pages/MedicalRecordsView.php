<?php

namespace App\Filament\Pages;

use App\Models\Examination;
use App\Models\Patient;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class MedicalRecordsView extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.medical-records-view';
    public ?array $data = [];
    protected static bool $shouldRegisterNavigation = false;
    public Patient $patient;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['student', 'instructor']);
    }
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
        $grade = json_decode($this->patient->examination->grade);
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
            'notes' => $this->patient->notes,
            'problem_satisfaction_dentist' => json_decode($this->patient->examination->problem_satisfaction_dentist, true) ?? [],
            'problem_satisfaction_patient' => json_decode($this->patient->examination->problem_satisfaction_patient, true) ?? [],
            'last_extraction' => $this->patient->examination->last_extraction,
            'tmj' => $this->patient->examination->tmj,
            'soft_tissue_upper' => $this->patient->examination->soft_tissue_upper,
            'soft_tissue_lower' => $this->patient->examination->soft_tissue_period,
            'soft_tissue_period' => $this->patient->examination->soft_tissue_period,
            'treatment_plan' => $this->patient->examination->treatment_plan,
            'diagnosis_classes' => $this->patient->examination->diagnosis_classes,
            'teeth' => $formattedTeethData,
            'basic_information_grade' => $grade->basic_information_grade ?? '',
            'dental_history_grade' => $grade->dental_history_grade ?? '',
            'extra_examination_grade' => $grade->extra_examination_grade ?? '',
            'intra_examination_grade' => $grade->intra_examination_grade ?? '',
            'final_evaluation_diagnose' => json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_diagnose'] ?? [],
            'final_evaluation_primary_impression' => json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_primary_impression'] ?? [],
            'final_evaluation_border_molding' => json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_border_molding'] ?? [],
            'final_evaluation_secondary_impression' => json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_secondary_impression'] ?? [],
            'final_evaluation_designing' => json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_designing'] ?? [],
            'final_evaluation_vertical_dimension' => json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_vertical_dimension'] ?? [],
            'final_evaluation_centric_relation' => json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_centric_relation'] ?? [],
            'final_evaluation_try_in' => json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_try_in'] ?? [],
            'final_evaluation_insertion' => json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_insertion'] ?? [],
            'final_evaluation_recall' => json_decode($this->patient->examination->final_evaluation, true)['final_evaluation_recall'] ?? [],
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
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->required(),
                            TextInput::make('age')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(120)
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->required(),
                            TextInput::make('phone')
                                ->tel()
                                ->length(9)
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->required(),
                            TextInput::make('occupation')
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->required(),
                            TextInput::make('address')
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->required(),
                            Select::make('gender')
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->options([
                                    'Male' => 'male',
                                    'Female' => 'female',
                                ])
                                ->required(),
                            CheckboxList::make('medical_history')
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->label('Medical History')
                                ->options([
                                    'cardiac_disease' => 'Cardiac Disease',
                                    'hypertension' => 'Hypertension',
                                    'diabetes' => 'Diabetes',
                                ]),
                            TextInput::make('medical_history_others')
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->label('Others'),
                            TextArea::make('complaint')
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->columnSpan(2),
                            TextArea::make('dental_history')
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->columnSpan(2),
                            Select::make('pain_level')
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->options([
                                    'mild' => 'mild',
                                    'moderate' => 'moderate',
                                    'severe' => 'severe',
                                ])
                                ->columnSpan(2),
                            ViewField::make('dental_history_file')
                                ->label('Dental History Files')
                                ->view('filament.views.uploaded-files')
                                ->columnSpan(2)
                        ]),

                    // Step 2: Dental History
                    Wizard\Step::make('dental_history')
                        ->columns(2)
                        ->label('Dental History')
                        ->schema([
                            TextInput::make('last_extraction')
                                ->disabled(true), // Disable the field

                            CheckboxList::make('problem_satisfaction_patient')
                                ->disabled(true) // Disable the field
                                ->dehydrated()
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
                                ->disabled(true) // Disable the field
                                ->dehydrated()
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
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->options([
                                    'square' => 'Square',
                                    'square_tapering' => 'Square Tapering',
                                    'tapering' => 'Tapering',
                                    'oviod' => 'Oviod',
                                ])
                                ->columns(4),
                            CheckboxList::make('facial_profile')
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->options([
                                    'class1' => 'Class 1',
                                    'class2' => 'Class 2',
                                    'class3' => 'Class 3',
                                ])
                                ->columns(4),
                            CheckboxList::make('facial_complexion')
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->options([
                                    'dark' => 'Dark',
                                    'medium' => 'Medium',
                                    'light' => 'Light',
                                ])
                                ->columns(4),
                            TextInput::make('tmj')
                                ->disabled(true) // Disable the field
                                ->dehydrated()
                                ->label('TMJ'),
                        ]),

                    Wizard\Step::make('Intra-Examination')
                        ->columns(2)
                        ->label('Intra-Examination')
                        ->schema([
                            TextInput::make('soft_tissue_upper')
                                ->disabled(true), // Disable the field
                            TextInput::make('soft_tissue_lower')
                                ->disabled(true), // Disable the field
                            TextInput::make('soft_tissue_period')
                                ->disabled(true), // Disable the field
                            Textarea::make('treatment_plan')
                                ->disabled(true), // Disable the field
                            Textarea::make('diagnosis_classes')
                                ->disabled(true), // Disable the field
                            Repeater::make('teeth')
                                ->columnSpan(2)
                                ->label('Intra-Examination Details')
                                ->grid(2)
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('tooth_number')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->label('Tooth Number')
                                                ->columnSpan(1)
                                                ->required(),
                                            Select::make('condition')
                                                ->label('Condition')
                                                ->disabled(true) // Disable the field
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
                                                ->disabled(true) // Disable the field
                                                ->columnSpan(2)
                                                ->placeholder('Additional notes for this tooth...')
                                                ->rows(5),
                                        ]),
                                ])
                                ->columns(1)
                                ->createItemButtonLabel('Add Tooth')
                        ]),

                    Wizard\Step::make('final_evaluation')
                        ->columns(2)
                        ->schema([
                            Placeholder::make('evaluation'),
                            Card::make()
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            Checkbox::make('final_evaluation_diagnose.value')
                                                ->label('Diagnosis')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                            DateTimePicker::make('final_evaluation_diagnose.date')
                                                ->label('Date')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            Checkbox::make('final_evaluation_primary_impression.value')
                                                ->label('Primary Impression')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                            DateTimePicker::make('final_evaluation_primary_impression.date')
                                                ->label('Date')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            Checkbox::make('final_evaluation_border_molding.value')
                                                ->label('Border Molding')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                            DateTimePicker::make('final_evaluation_border_molding.date')
                                                ->label('Date')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            Checkbox::make('final_evaluation_secondary_impression.value')
                                                ->label('Secondary Impression')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                            DateTimePicker::make('final_evaluation_secondary_impression.date')
                                                ->label('Date')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            Checkbox::make('final_evaluation_designing.value')
                                                ->label('Designing')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                            DateTimePicker::make('final_evaluation_designing.date')
                                                ->label('Date')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            Checkbox::make('final_evaluation_vertical_dimension.value')
                                                ->label('Vertical Dimension')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                            DateTimePicker::make('final_evaluation_vertical_dimension.date')
                                                ->label('Date')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            Checkbox::make('final_evaluation_centric_relation.value')
                                                ->label('Centric Relation')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                            DateTimePicker::make('final_evaluation_centric_relation.date')
                                                ->label('Date')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            Checkbox::make('final_evaluation_try_in.value')
                                                ->label('Try In')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                            DateTimePicker::make('final_evaluation_try_in.date')
                                                ->label('Date')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            Checkbox::make('final_evaluation_insertion.value')
                                                ->label('Insertion')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                            DateTimePicker::make('final_evaluation_insertion.date')
                                                ->label('Date')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            Checkbox::make('final_evaluation_recall.value')
                                                ->label('Recall')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                            DateTimePicker::make('final_evaluation_recall.date')
                                                ->label('Date')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated()
                                                ->columnSpan(1),
                                        ]),
                                ]),

                            Placeholder::make('grades'),
                            Card::make()
                                ->schema([
                                    Grid::make(3)
                                        ->schema([
                                            TextInput::make('basic_information_grade')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10)
                                                ->disabled(true) // Disable the field
                                                ->dehydrated(),
                                            TextInput::make('dental_history_grade')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10)
                                                ->disabled(true) // Disable the field
                                                ->dehydrated(),
                                            TextInput::make('extra_examination_grade')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10)
                                                ->disabled(true) // Disable the field
                                                ->dehydrated(),
                                            TextInput::make('intra_examination_grade')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10)
                                                ->disabled(true) // Disable the field
                                                ->dehydrated(),
                                        ]),
                                ]),

                            Card::make()
                                ->schema([
                                    Grid::make()
                                        ->schema([
                                            Textarea::make('notes')
                                                ->disabled(true) // Disable the field
                                                ->dehydrated(),
                                        ]),
                                ]),
                        ])
                        ->visible()
                ])
                    ->skippable()

            ])
            ->statePath('data');
    }


}

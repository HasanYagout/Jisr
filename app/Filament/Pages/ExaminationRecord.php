<?php
namespace App\Filament\Pages;

use App\Models\Patient;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;

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

    public function mount(): void
    {
        $patientId = request()->query('patient');
        $this->patient = Patient::findOrFail($patientId);
        $medicalHistory = json_decode($this->patient->medical_history, true);

        // Fill the form with the patient's data
        $this->form->fill([
            'name' => $this->patient->name,
            'age' => $this->patient->age,
            'phone' => $this->patient->phone,
            'medical_history' => array_keys(array_filter($medicalHistory, fn ($value) => $value === 'Yes')),
            'others' => $medicalHistory['others'],
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

        // Define teeth diagram
        $teethDiagram = [
            // Upper Teeth (16 teeth)
            'upper' => [
                '1' => ['label' => 'Upper Right 3rd Molar', 'position' => 'upper_1'],
                '2' => ['label' => 'Upper Right 2nd Molar', 'position' => 'upper_2'],
                '3' => ['label' => 'Upper Right 1st Molar', 'position' => 'upper_3'],
                // Add all 16 upper teeth...
            ],
            // Lower Teeth (16 teeth)
            'lower' => [
                '1' => ['label' => 'Lower Right 3rd Molar', 'position' => 'lower_1'],
                '2' => ['label' => 'Lower Right 2nd Molar', 'position' => 'lower_2'],
                '3' => ['label' => 'Lower Right 1st Molar', 'position' => 'lower_3'],
                // Add all 16 lower teeth...
            ],
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
                                ->options([
                                    'cardiac_disease' => 'cardiac_disease',
                                    'hypertension' => 'hypertension',
                                    'diabetes' => 'diabetes',
                                ])->default(function ($get) {
                                    // Decode the JSON data
                                    $medicalHistory = json_decode($this->patient->medical_history, true);

                                    // Map "Yes" values to selected options
                                    return array_keys(array_filter($medicalHistory, fn ($value) => $value === 'Yes'));
                                }),
                            TextInput::make('others')
                                ->label('Others'),
                            TextArea::make('complaint')
                                ->columnSpan(2),
                        ]),

                    // Step 2: Dental History
                    Wizard\Step::make('dental_history')
                        ->columns(2)
                        ->label('Dental History')
                        ->schema([
                            TextInput::make('last_extraction')
                                ->required(),

                            CheckboxList::make('Problem and satisfaction for the existing prosthesis(patient):')
                                ->options([
                                    'retention' => 'retention',
                                    'stability' => 'stability',
                                    'appearance' => 'appearance',
                                    'speech' => 'speech',
                                    'mastication' => 'mastication',
                                ])->columns(3),

                            CheckboxList::make('Problem and satisfaction for the existing prosthesis(Dentist):')
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
                            // Upper Teeth
                            Placeholder::make('upper_teeth_label')
                                ->label('Upper Teeth')
                                ->columnSpanFull(),
                            Grid::make(8) // 8 teeth per row (adjust as needed)
                            ->schema(
                                array_map(
                                    fn ($tooth) => Select::make($tooth['position'])
                                        ->label($tooth['label'])
                                        ->options([
                                            'missing' => 'Missing',
                                            'decay' => 'Decay',
                                            'crown' => 'Crown',
                                            'mobile' => 'Mobile',
                                        ]),
                                    $teethDiagram['upper']
                                )
                            ),

                            // Lower Teeth
                            Placeholder::make('lower_teeth_label')
                                ->label('Lower Teeth')
                                ->columnSpanFull(),
                            Grid::make(8) // 8 teeth per row (adjust as needed)
                            ->schema(
                                array_map(
                                    fn ($tooth) => Select::make($tooth['position'])
                                        ->label($tooth['label'])
                                        ->options([
                                            'missing' => 'Missing',
                                            'decay' => 'Decay',
                                            'crown' => 'Crown',
                                            'mobile' => 'Mobile',
                                        ]),
                                    $teethDiagram['lower']
                                )
                            ),
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

        // Update the patient record with the wizard data
        $this->patient->update($data);

        // Optionally, display a success message
        $this->notify('success', 'Patient information updated successfully!');
    }
}

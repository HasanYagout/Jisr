<x-filament::page>
    <div>
        <!-- Grid layout with square cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($records as $record)
                <!-- Square card -->
                <div class="aspect-square bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                    <div class="p-4 flex flex-col justify-between h-full">
                        <!-- Patient Name -->
                        <h2 class="text-lg font-semibold mb-2">{{ $record->patient->name }}</h2>

                        <!-- Patient Details -->
                        <div class="flex-grow space-y-2">
                            <p><strong>Age:</strong> {{ $record->patient->age }}</p>
                            <p><strong>Gender:</strong> {{ $record->patient->gender }}</p>
                            <p><strong>Phone:</strong> {{ $record->patient->phone }}</p>
                            <p><strong>Address:</strong> {{ $record->patient->address }}</p>
                        </div>

                        <!-- Status and Pain Level Badges -->
                        <div class="flex flex-wrap gap-2 mt-2">
                            <x-filament::badge color="{{ $record->patient->status == 0 ? 'warning' : 'success' }}">
                                {{ $record->patient->status == 0 ? 'In Progress' : 'Done' }}
                            </x-filament::badge>

                            <x-filament::badge color="{{ $record->patient->pain_level == 'mild' ? 'info' : ($record->patient->pain_level == 'moderate' ? 'warning' : 'danger') }}">
                                <strong>Pain Level:</strong> {{ ucfirst($record->patient->pain_level) }}
                            </x-filament::badge>
                        </div>

                        <!-- Grade Badges -->
                        <div class="mt-2">
                            <p><strong>Grade:</strong></p>
                            <div class="flex flex-wrap gap-2 pl-4">
                                @php
                                    $grades = json_decode($record->grade, true);
                                    $basicInfoGrade = $grades['basic_information_grade'] ?? 'N/A';
                                    $dentalHistoryGrade = $grades['dental_history_grade'] ?? 'N/A';
                                    $extraExaminationGrade = $grades['extra_examination_grade'] ?? 'N/A';
                                    $intraExaminationGrade = $grades['intra_examination_grade'] ?? 'N/A';
                                @endphp
                                <x-filament::badge color="gray">{{ "Basic Info: $basicInfoGrade" }}</x-filament::badge>
                                <x-filament::badge color="gray">{{ "Dental History: $dentalHistoryGrade" }}</x-filament::badge>
                                <x-filament::badge color="gray">{{ "Extra Exam: $extraExaminationGrade" }}</x-filament::badge>
                                <x-filament::badge color="gray">{{ "Intra Exam: $intraExaminationGrade" }}</x-filament::badge>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $records->links() }} <!-- Render pagination links -->
        </div>
    </div>
</x-filament::page>

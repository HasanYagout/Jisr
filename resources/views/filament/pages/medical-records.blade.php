<x-filament::page>
    <div class="p-6">
        <!-- Search Field -->
        <div class="mb-5">
            <input
                type="text"
                wire:model.debounce.300ms="search"
            placeholder="Search by name, phone, or address..."
            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100"
            onchange="this.dispatchEvent(new InputEvent('input'))"
            />
        </div>

        <!-- Grid layout with responsive cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($records as $record)


                    <!-- Card Container -->
                    <div class="aspect-square bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden flex flex-col border border-gray-200 dark:border-gray-700">
                        <div class="p-4 flex flex-col justify-between h-full">
                            <!-- Patient Name -->
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ $record->patient->name }}
                            </h2>

                            <!-- Patient Details -->
                            <div class="flex-grow space-y-2">
                                <p>
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">Age:</span>
                                    <span class="text-gray-700 dark:text-gray-400">{{ $record->patient->age }}</span>
                                </p>
                                <p>
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">Gender:</span>
                                    <span class="text-gray-700 dark:text-gray-400">{{ $record->patient->gender }}</span>
                                </p>
                                <p>
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">Phone:</span>
                                    <span class="text-gray-700 dark:text-gray-400">{{ $record->patient->phone }}</span>
                                </p>
                                <p>
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">Address:</span>
                                    <span class="text-gray-700 dark:text-gray-400">{{ $record->patient->address }}</span>
                                </p>
                            </div>

                            <!-- Status and Pain Level Badges -->
                            <div class="flex flex-wrap gap-2 mt-3">
                                <x-filament::badge
                                    :color="$record->patient->status == 0 ? 'warning' : 'success'">
                                    {{ $record->patient->status == 0 ? 'In Progress' : 'Done' }}
                                </x-filament::badge>

                                <x-filament::badge
                                    :color="$record->patient->pain_level == 'mild' ? 'info' : ($record->patient->pain_level == 'moderate' ? 'warning' : 'danger')">
                                    <strong>Pain Level:</strong> {{ ucfirst($record->patient->pain_level) }}
                                </x-filament::badge>
                            </div>

                            <!-- Grade Badges -->
                            <div class="mt-3">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-200">Grade:</p>
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
        <div class="mt-6 text-center">
            {{ $records->links() }} <!-- Render pagination links -->
        </div>
    </div>
</x-filament::page>

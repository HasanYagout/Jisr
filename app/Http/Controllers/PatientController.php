<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\ExaminationRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\NoReturn;


class PatientController extends Controller
{

    public function store(Request $request)
    {
        try {
            // Define validation rules
            $rules = [
                'name' => 'required|string|max:255',
                'age' => 'required|date|after_or_equal:' . now()->subYears(100)->format('Y-m-d') . '|before_or_equal:' . now()->format('Y-m-d'),
                'gender' => 'required|in:Male,Female',
                'occupation' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|numeric|digits:9',
                'complaint' => 'nullable|string|max:1000',
                'dental_history' => 'nullable|string|max:1000',
                'pain_level' => 'nullable|in:mild,moderate,severe',
                'dental_history_file.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            ];

            // Custom error messages
            $messages = [
                'name.required' => 'The name field is required.',
                'age.required' => 'The date of birth field is required.',
                'age.after_or_equal' => 'The date of birth cannot be more than 100 years ago.',
                'age.before_or_equal' => 'The date of birth cannot be in the future.',
                'gender.required' => 'The gender field is required.',
                'phone.digits' => 'The phone number must be exactly 9 digits.',
                'dental_history_file.*.mimes' => 'Only PDF, DOC, DOCX, JPG, and PNG files are allowed.',
                'dental_history_file.*.max' => 'File size must be less than 2MB.',
            ];

            // Validate the request
            $validatedData = $request->validate($rules, $messages);

            // Calculate Age from Date of Birth
            $dob = Carbon::parse($validatedData['age']); // Parse the date
            $calculatedAge = $dob->age; // Get age in years


            $validatedData['age'] = $calculatedAge; // Replace 'age' with calculated age

            // Default structure for medical_history
            $defaultMedicalHistory = [
                'cardiac_disease' => null,
                'hypertension' => null,
                'diabetes' => null,
                'others' => null,
            ];

            $validatedData['medical_history'] = isset($request->all()['medical_history']) ?
                array_merge($defaultMedicalHistory, $request->all()['medical_history']) :
                $defaultMedicalHistory;

            // Encode medical history to JSON
            $validatedData['medical_history'] = json_encode(array_map(function ($value) {
                return $value === '' || $value === null ? null : $value;
            }, $request->all()['medical_history']));


            // Handle file uploads
            if ($request->hasFile('dental_history_file')) {
                $file = $request->file('dental_history_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('uploads', $fileName);
                $validatedData['dental_history_file'] = 'uploads/' . $fileName;
            }

            // Save to the database
            $patient = \App\Models\Patient::create($validatedData);
            Examination::create(['patient_id' => $patient->id]);

            return redirect()->route('home')->with('success', 'Medical history saved successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'There was an error saving the medical history. Please check the form and try again.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }


}

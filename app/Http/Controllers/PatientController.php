<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\ExaminationRecord;
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
                'age' => 'required|integer|min:0|max:120',
                'gender' => 'required|in:Male,Female',
                'occupation' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|numeric|digits:9', // Ensure phone is exactly 9 digits
                'complaint' => 'nullable|string|max:1000',
                'dental_history' => 'nullable|string|max:1000',
                'pain_level' => 'nullable|in:mild,moderate,severe',
                'dental_history_file.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048', // Allow images and documents
            ];

            // Custom error messages
            $messages = [
                'name.required' => 'The name field is required.',
                'age.required' => 'The age field is required.',
                'gender.required' => 'The gender field is required.',
                'phone.digits' => 'The phone number must be exactly 9 digits.',
                'dental_history_file.*.mimes' => 'Only PDF, DOC, DOCX, JPG, and PNG files are allowed.',
                'dental_history_file.*.max' => 'File size must be less than 2MB.',
            ];

            // Validate the request
            $validatedData = $request->validate($rules, $messages);

            // Define the default structure for medical_history
            $defaultMedicalHistory = [
                'cardiac_disease' => null,
                'hypertension' => null,
                'diabetes' => null,
                'others' => null,
            ];

            // Merge the request data with the default structure
            if (isset($validatedData['medical_history'])) {
                $validatedData['medical_history'] = array_merge($defaultMedicalHistory, $validatedData['medical_history']);
            } else {
                $validatedData['medical_history'] = $defaultMedicalHistory;
            }

            // Ensure empty fields in medical_history are saved as null
            $validatedData['medical_history'] = array_map(function ($value) {
                return $value === '' || $value === null ? null : $value;
            }, $validatedData['medical_history']);

            // Encode medical_history to JSON
            $validatedData['medical_history'] = json_encode($validatedData['medical_history']);

            // Handle multiple file uploads
            if ($request->hasFile('dental_history_file')) {

               $file= $request->file('dental_history_file');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('uploads', $fileName); // Save the file to the "uploads" directory
                    $filePaths = $fileName;
                $validatedData['dental_history_file'] = 'uploads'.'/'.$filePaths; // Store file paths as JSON
            }

            // Save the data to the database
            $patient = \App\Models\Patient::create($validatedData);
            Examination::create(['patient_id' => $patient->id]);

            // Redirect with success flash message
            return redirect()->route('home')->with('success', 'Medical history saved successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'There was an error saving the medical history. Please check the form and try again.');
        } catch (\Exception $e) {

            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ExaminationRecord;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\NoReturn;

class PatientController extends Controller
{

    public function store(Request $request)
    {
        try {
            // Validate the form data
            $validatedData = $request->validate([
                'name' => 'required|string',
                'age' => 'required|numeric',
                'gender' => 'required|in:Male,Female',
                'occupation' => 'nullable|string',
                'address' => 'nullable|string',
                'phone' => 'nullable|numeric',
                'cardiac_disease' => 'nullable|boolean',
                'hypertension' => 'nullable|boolean',
                'diabetes' => 'nullable|boolean',
                'others' => 'nullable|string',
                'complaint' => 'nullable|string',
                'medical_history' => 'nullable|array',
                'medical_history.cardiac_disease' => 'nullable|string',
                'medical_history.hypertension' => 'nullable|string',
                'medical_history.diabetes' => 'nullable|string',
                'medical_history.others' => 'nullable|string',
                'dental_history' => 'nullable|string',
                'pain_level' => 'nullable|in:mild,moderate,severe',
                'dental_history_file.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048', // Allow images and documents
            ], [
                // Custom error messages
                'name.required' => 'The name field is required.',
                'age.required' => 'The age field is required.',
                'gender.required' => 'The gender field is required.',
                'dental_history_file.*.mimes' => 'Only PDF, DOC, and DOCX files are allowed.',
                'dental_history_file.*.max' => 'File size must be less than 2MB.',
            ]);
            if (isset($validatedData['medical_history'])) {
                $validatedData['medical_history'] = json_encode($validatedData['medical_history']);
            }

            // Handle multiple file uploads
            if ($request->hasFile('dental_history_file')) {
                $filePaths = [];
                foreach ($request->file('dental_history_file') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('uploads', $fileName); // Save the file to the "uploads" directory
                    $filePaths[] = $fileName;
                }
                $validatedData['dental_history_file'] = json_encode($filePaths); // Store file paths as JSON
            }

            // Save the data to the database
            \App\Models\Patient::create($validatedData);

            // Redirect with success flash message
            return redirect()->route('home')->with('success', 'Medical history saved successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Redirect back with input and flash error message
            dd($e->getMessage());
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->withInput()
                ->with('error', 'There was an error saving the medical history. Please check the form and try again.');
        }
    }}

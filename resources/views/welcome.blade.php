@extends('layouts.app')
@section('content')
    <section class="bg-pantone-purple d-flex flex-row">
        <section class="d-flex flex-column justify-content-center px-6">
            <p class="fs-5">Welcome to Jisr</p>
            <h1 class="fw-bold">your bridge to better dental care. </h1>
            <p class="fs-5">We connect patients with skilled dental students, ensuring all treatments are conducted
                under the supervision of experienced doctors. To help us provide you with the best care, please fill out
                the form accurately and honestly. Start your journey to a healthy smile today!"</p>
            <a href="#form" class="bg-dark-blue ms-auto text-decoration-none rounded text-center text-white w-15 p-2 ">Schedule an
                Appointment</a>
        </section>
        <section>
            <img class="" src="{{asset('assets/images/Doctors.png')}}" alt="">
        </section>
    </section>
    <section class="d-flex bg-bright-gray px-6">

        <section class="d-flex flex-column justify-content-center  w-50">
           <h1 class="fw-bold">Apply for Dental Care</h1>
        </section>
        <section id="form" class="py-5">
            <form action="{{ route('medical_history.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="container">
                    <div class="row gy-3">
                        <!-- Name Field -->
                        <div class="col-lg-6">
                            <label for="name">Name</label>
                            <input class="form-control p-3" placeholder="Enter Your Full Name" type="text" name="name" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Age Field -->
                        <div class="col-lg-3">
                            <label for="age">Age</label>
                            <input class="form-control p-3" placeholder="Enter Your Age" type="number" name="age" value="{{ old('age') }}" min="0" max="120" required>
                            @error('age')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gender Field -->
                        <div class="col-lg-3">
                            <label for="gender">Gender</label>
                            <select class="form-control p-3" name="gender" required>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Occupation Field -->
                        <div class="col-lg-6">
                            <label for="occupation">Occupation</label>
                            <input type="text" class="form-control p-3" name="occupation" value="{{ old('occupation') }}" required>
                            @error('occupation')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address Field -->
                        <div class="col-lg-6">
                            <label for="address">Address</label>
                            <input type="text" class="form-control p-3" name="address" value="{{ old('address') }}" required>
                            @error('address')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone Number Field -->
                        <div class="col-lg-6">
                            <label for="phone">Phone Number</label>
                            <input type="number" class="form-control p-3" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Medical History Fields -->
                        <div class="col-lg-6">
                            <label for="medical_history">Medical History</label>
                            <div class="row">
                                <div class="col-lg-4">
                                    <label for="cardiac_disease">Cardiac Disease</label>
                                    <input type="checkbox" class="form-check-input" value="Yes" name="medical_history[cardiac_disease]">
                                </div>
                                <div class="col-lg-4">
                                    <label for="hypertension">Hypertension</label>
                                    <input type="checkbox" class="form-check-input" value="Yes" name="medical_history[hypertension]">
                                </div>
                                <div class="col-lg-4">
                                    <label for="diabetes">Diabetes</label>
                                    <input type="checkbox" class="form-check-input" value="Yes" name="medical_history[diabetes]">
                                </div>
                                <div class="col-lg-12">
                                    <label for="others">Others</label>
                                    <input type="text" class="form-control p-3" name="medical_history[others]">
                                </div>
                            </div>
                        </div>

                        <!-- Chief Complaint Field -->
                        <div class="col-lg-6">
                            <label for="complaint">Chief Complaint</label>
                            <textarea class="form-control p-3" name="complaint" required>{{ old('complaint') }}</textarea>
                            @error('complaint')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dental History Field -->
                        <div class="col-lg-6">
                            <label for="dental_history">Dental History</label>
                            <textarea class="form-control p-3" name="dental_history" required>{{ old('dental_history') }}</textarea>
                            @error('dental_history')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pain Level Field -->
                        <div class="col-lg-6">
                            <label for="pain_level">How Would You Rate Your Level of Pain?</label>
                            <select class="form-control p-3" name="pain_level" required>
                                <option value="mild" {{ old('pain_level') == 'mild' ? 'selected' : '' }}>Mild</option>
                                <option value="moderate" {{ old('pain_level') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                <option value="severe" {{ old('pain_level') == 'severe' ? 'selected' : '' }}>Severe</option>
                            </select>
                            @error('pain_level')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload Field -->
                        <div class="col-lg-6">
                            <label for="dental_history_file">If You Have a Dental History, Please Upload It:</label>
                            <input type="file"   accept="application/pdf" class="form-control p-3" name="dental_history_file">
                            @error('dental_history_file.*')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="col-lg-12 text-end">
                            <button class="bg-dark-blue px-4 py-1 rounded text-white" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>

    </section>

@endsection

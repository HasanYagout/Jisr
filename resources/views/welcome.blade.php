@extends('layouts.app')
@section('content')
    <section class="bg-pantone-purple d-flex flex-row">
        <section class="d-flex flex-column justify-content-center px-6">
            <p class="fs-5">Welcome to Jisr</p>
            <h1 class="fw-bold">your bridge to better dental care. </h1>
            <p class="fs-5">We connect patients with skilled dental students, ensuring all treatments are conducted
                under the supervision of experienced doctors. To help us provide you with the best care, please fill out
                the form accurately and honestly. Start your journey to a healthy smile today!"</p>
            <a class="bg-dark-blue ms-auto text-decoration-none rounded text-center text-white w-15 p-2 ">Schedule an
                Appointment</a>
        </section>
        <section>
            <img class="" src="{{asset('assets/images/Doctors.png')}}" alt="">
        </section>
    </section>
    <section class="d-flex bg-bright-gray">
        <section class="d-flex flex-column justify-content-center ps-6 w-50">
           <h1 class="fw-bold">Apply for Dental Care</h1>
        </section>
        <section class="py-5">
            <form action="{{route('')}}" method="POST">
                <div class="container">
                    <div class="row gy-3">
                        <div class="col-lg-6">
                            <label for="name">Name</label>
                            <input class="form-control p-3" placeholder="Enter Your Full Name" type="text" name="name">
                        </div>
                        <div class="col-lg-3">
                            <label for="name">Age</label>
                            <input class="form-control p-3" placeholder="Enter Your Age" type="date" name="age">
                        </div>
                        <div class="col-lg-3">
                            <label for="name">Gender</label>
                            <select class="form-control p-3" name="gender" id="">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label for="name">Occupation</label>
                            <input type="text" class="form-control p-3" name="occupation">
                        </div>
                        <div class="col-lg-6">
                            <label for="name">Address</label>
                            <input type="text" class="form-control p-3" name="address">
                        </div>
                        <div class="col-lg-6">
                            <label for="name">Phone Number</label>
                            <input type="number" class="form-control p-3" name="phone">
                        </div>

                        <div class="col-lg-6">
                            <label for="name">Medical History</label>
                            <div class="row">
                                <div class="col-lg-4">
                                    <label for="cardiac_disease">cardiac disease</label>
                                    <input type="checkbox" class="form-check-input"  value="cardiac_disease" name="cardiac_disease">
                                </div>
                                <div class="col-lg-4">
                                    <label for="name">hypertension</label>
                                    <input type="checkbox" class="form-check-input"  value="cardiac_disease" name="hypertension">
                                </div>
                                <div class="col-lg-4">
                                    <label for="name">diabetes</label>
                                    <input type="checkbox" class="form-check-input"  value="cardiac_disease" name="diabetes">

                                </div>
                                <div class="col-lg-12">
                                    <label for="name">Others</label>
                                    <input type="text" class="form-control p-3" name="others">
                                </div>
                            </div>

                        </div>


                        <div class="col-lg-6">
                            <label for="complaint">Chief compliant</label>
                            <textarea class="form-control p-3" name="complaint"></textarea>
                        </div>
                        <div class="col-lg-6">
                            <label for="complaint">Dental History</label>
                            <textarea class="form-control p-3" name="dental_history" id=""></textarea>
                        </div>
                        <div class="col-lg-6">
                            <label for="complaint">How Would you rate your level of pain?</label>
                            <select class="form-control p-3" name="pain" id="">
                                <option value="mild">Mild</option>
                                <option value="moderate">Moderate</option>
                                <option value="severe">Severe</option>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label for="complaint">If you have a dental history, please upload it:</label>
                            <input type="file" class="form-control p-3" name="dental_history_file">
                        </div>
                    </div>
                </div>
            </form>
        </section>


    </section>

@endsection

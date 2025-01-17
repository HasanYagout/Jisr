@extends('layouts.app')
@section('content')
<section class="bg-pantone-purple d-flex flex-row">
    <section class="d-flex flex-column justify-content-center px-6">
        <p class="fs-5">Welcome to Jisr</p>
        <h1 class="fw-bold">your bridge to better dental care. </h1>
        <p class="fs-5">We connect patients with skilled dental students, ensuring all treatments are conducted under the supervision of experienced doctors. To help us provide you with the best care, please fill out the form accurately and honestly. Start your journey to a healthy smile today!"</p>
    <a class="bg-dark-blue ms-auto text-decoration-none rounded text-center text-white w-15 p-2 ">Schedule an Appointment</a>
    </section>
    <section>
        <img class="" src="{{asset('assets/images/Doctors.png')}}" alt="">
    </section>
</section>

@endsection

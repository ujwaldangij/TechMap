@extends('layout.WebsiteLayout.SuperAdmin.login_register')
@section('title')
    {{ $title }}
@endsection
@section('links')
@endsection

<div class="row">
    <div class="col-md-6 mx-auto text-center">
        <div class="logo-container">
            <img src="{{ asset('logo/Logos-01.png') }}" alt="Logo 1" style="max-width: 100%; height: auto;">
        </div>
        <div class="logo-container">
            <img src="{{ asset('logo/Logos-02.png') }}" alt="Logo 2" style="max-width: 100%; height: auto;">
        </div>
        <div class="logo-container">
            <img src="{{ asset('logo/Logos-03.png') }}" alt="Logo 3" style="max-width: 100%; height: auto;">
        </div>
        <div class="logo-container">
            <img src="{{ asset('logo/Logos-04.png') }}" alt="Logo 4" style="max-width: 100%; height: auto;">
        </div>
       
    </div>
    <div class="col-md-6 text-center">
        <div>
            <h4 class="my-3"><b>@error('issue'){{ $message }}@enderror</b></h4>
            <div id="mydata">
                <h3 class="text-center mt-4" style="font-size: 36px;">
                    <span style="color: #007bff; font-weight: bold;">Dr. {{ $doctor_data->name }}</span>
                </h3>
                <img src="{{ asset('reports/' . $doctor_data->upload_report) }}" alt="Image Preview" style="max-width: 100%; width: 500px; height: auto;">
                <hr>
                <h3 class="text-center mt-4" style="font-size: 36px;">
                    <span style="color: #007bff; font-weight: bold;">Total Enrolled Doctors: {{ $count1 }}</span>
                </h3>
                 <a href="{{ route('schedule') }}" class="btn btn-primary text-center">Go to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<style>
    .logo-container {
        margin-bottom: 20px;
    }
</style>

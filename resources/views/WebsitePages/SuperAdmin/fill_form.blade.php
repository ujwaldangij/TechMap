@extends('layout.WebsiteLayout.SuperAdmin.login_register')
@section('title')
    {{ $title }}
@endsection
@section('links')
@endsection
<div style="display: flex; justify-content: space-between;">
    <img src="{{ asset('logo/Logos-01.png') }}" alt="Logo 1" style="width: 25%;">
    <img src="{{ asset('logo/Logos-02.png') }}" alt="Logo 2" style="width: 25%;">
    <img src="{{ asset('logo/Logos-03.png') }}" alt="Logo 3" style="width: 25%;">
    <img src="{{ asset('logo/Logos-04.png') }}" alt="Logo 4" style="width: 25%;">
 </div>
@section('content')
 {{-- Responsive images in one line --}}
    <div>
        <h3>Welcome to {{ $title }} page</h3>
        <p>{{ $title }} in. To see it in action.</p>
        <h4><b>@error('issue'){{ $message }}@enderror</b></h4>
        <form class="m-t" role="form" action="{{ route('fill_form_post') }}" method="POST"  enctype="multipart/form-data" autocomplete="off">
            @csrf
            {{-- !from input field schedule_id --}}
            <div class="form-group @error('schedule_id'){{ "has-error" }} @enderror">
                <input type="hidden" class="form-control" placeholder="Enter Your schedule_id" name="schedule_id" id="schedule_id"
                    value="{{ old('schedule_id', $doctor_data->id ?? '') }}">
            </div>
            <p class="py-0 text-danger text-small" style="text-align-last : left !important">
                @error('schedule_id')
                    {{ $message }}
                @enderror
            </p>
            {{-- !from input field doctor_name --}}
            <div class="form-group">
                <label for="doctor_name">Doctor Name</label>
                <input type="text" class="form-control" placeholder="Enter Doctor's Name" name="doctor_name" id="doctor_name" value="{{ old('doctor_name', $doctor_data->name ?? '') }}">
            </div>
            <p class="text-danger">
                @error('doctor_name')
                    {{ $message }}
                @enderror
            </p>
            {{-- !from input field report --}}
            <div class="form-group @error('report'){{ "has-error" }} @enderror">
                <input type="file" class="form-control" placeholder="Enter Your report" name="report" id="report"
                    value="{{ old('report', $doctor_data->report ?? '') }}" accept=".jpg, .jpeg, .png">
            </div>
            <p class="py-0 text-danger text-small" style="text-align-last : left !important">
                @error('report')
                    {{ $message }}
                @enderror
            </p>
            {{-- !from input field specialty --}}
            <select class="form-control" name="specialty" id="specialty">
                <option value="Gynecologist">
                    Gynecologist
                </option>
                <option value="Dietician">
                    Dietician
                </option>
            </select>
            
            
            
            <button type="submit" class="btn btn-primary block full-width mt-5">Submit</button>
        </form>
        <p class="m-t"> <small>{{ $compony_details['name'] }} is developed by {{ $compony_details['developed'] }} &copy;
                {{ date('Y') }}</small> </p>
    </div>
@endsection
@section('script')
@endsection

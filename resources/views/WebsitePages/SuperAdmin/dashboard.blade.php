@extends('layout.WebsiteLayout.SuperAdmin.dashboard')
@section('title')
{{ $title }}
@endsection
@section('links')
<!-- Toastr style -->
<link href="{{ asset('storage/WebsiteAsset/SuperAdmin/css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
@endsection
@section('script')
<!-- Toastr -->
<script src="{{ asset('storage/WebsiteAsset/SuperAdmin/js/plugins/toastr/toastr.min.js') }}"></script>
<script>
    $(document).ready(function() {
        let toast = $('.toast');
        setTimeout(function() {
            toast.toast({
                delay: 10000,
                animation: true
            });
            toast.toast('show');
        }, 1000);
    });

    $(window).bind("scroll", function() {
        let toast = $('.toast');
        toast.css("top", window.pageYOffset + 20);

    });
</script>
@endsection
@section('body')
<div class="row justify-content-between">
    <div class="col-lg-3">
        <div class="ibox ">
            <div class="ibox-title">
                <div class="ibox-tools">
                </div>
                <h5>Dr</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $dr_count }}</h1>
                <small>Total Dr</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox ">
            <div class="ibox-title">
                <div class="ibox-tools">
                </div>
                <h5>Schedule</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $schedule_count }}</h1>
                <small>New Schedule</small>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@extends('layout.WebsiteLayout.SuperAdmin.dashboard')
@section('title')
{{ $title }}
@endsection
@section('links')
<!-- Toastr style -->
<link href="{{ asset('storage/WebsiteAsset/SuperAdmin/css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
<link href="{{ asset('storage/WebsiteAsset/SuperAdmin/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
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
<script src="{{ asset('storage/WebsiteAsset/SuperAdmin/js/plugins/dataTables/datatables.min.js') }}"></script>
<script>

    // Upgrade button class name
    $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-white btn-sm';

    $(document).ready(function(){
        $('.dataTables-example').DataTable({
            pageLength: 25,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                { extend: 'copy'},
                {extend: 'csv'},
                {extend: 'excel', title: 'ExampleFile'},
                {extend: 'pdf', title: 'ExampleFile'},

                {extend: 'print',
                 customize: function (win){
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');

                        $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                }
                }
            ]

        });

    });

</script>

@endsection
@section('body')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Doctor Details</h5>
                <div class="ibox-tools">
                    <a class="btn btn-success" href="{{ route('fill_form') }}">
                        add form
                    </a>
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                    <a class="close-link">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">

                <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover dataTables-example" >
            <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Specialties</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Align</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($doctor as $doc)
                <tr>
                    <td>{{ $doc->id }}</td>
                    <td>{{ $doc->name }}</td>
                    <td>{{ $doc->specialties }}</td>
                    <td class="center">{{ $doc->contact }}</td>
                    <td>{{ $doc->email }}</td>
                    <td>{{ $doc->align }}</td>
                    <td class="text-center footable-visible footable-last-column">
                        <div class="btn-group">
                            <a href="{{ route('choose_id', ['id'=>$doc->id]) }}" class="btn-info btn btn-xs">Choose</a>
                            @if (session('user')->role == 1)
                            <a href="{{ route('choose_id_edit', ['id'=>$doc->id]) }}" class="btn-white btn btn-xs">Edit</a>
                            <a href="{{ route('choose_id_delete', ['id'=>$doc->id]) }}" class="btn-danger btn btn-xs">Delete</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
                </table>
                </div>

            </div>
        </div>
    </div>
    </div>
</div>
@endsection

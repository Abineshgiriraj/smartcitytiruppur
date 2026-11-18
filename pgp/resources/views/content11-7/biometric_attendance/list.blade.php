@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
@php

$todayDate=date('Y-m-d');
$punch_category_options = get_punch_category();
@endphp

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-sm-6">

        <h4 class="">
            <span class="text-muted fw-light">Reports /</span> Bio-Metric Attendance Report
        </h4>
    </div>

</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label" for="basic-default-fullname">From Date</label>
                <input type="date" class="form-control" value="<?php echo $todayDate; ?>" id="from_date"
                    name="from_date" />
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label" for="basic-default-fullname">To Date</label>
                <input type="date" class="form-control" value="<?php echo $todayDate; ?>" id="to_date" name="to_date" />
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label" for="basic-default-fullname">Punching Category</label>
                <select class="form-select" id="punching_category" name="punching_category">
                    <option value="">Select Punch Category</option>
                    @foreach($punch_category_options as $category)
                    <option value="{{$category->verify_mode}}">
                        {{$category->verify_mode}}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mt-4">
                <button type="button" class="btn btn-primary" onclick="punch_category()">GO</button>
            </div>
        </div>

        <div class="card-datatable table-responsive">

            <table class="dt-responsive table border-top" id="example">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Enroll No</th>
                        <th>Student name</th>
                        <th>Department Name</th>
                        <th>Punching Category</th>
                        <th>Punching Date/Time</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <!-- div/div> -->
    </div>
    <!--/ Responsive Datatable -->


    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/responsive.bootstrap.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('public/assets/css/core.css') }}" />

    <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
    <script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
    <script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>


    <script src="https://cdn.datatables.net/buttons/3.1.0/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.0/js/buttons.dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.0/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script src="https://cdn.datatables.net/buttons/3.1.0/js/buttons.html5.min.js"></script>

    <script type="text/javascript" class="init">
    // new DataTable('#example', {
    //     responsive: true
    // });


    $(document).ready(function() {
        var table = $('#example').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('biometric_attendance.punch') }}",
                type: "POST",
                data: function(d) {
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.punching_category = $('#punching_category').val();
                    d._token = "{{ csrf_token() }}";
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'enroll_no',
                    title: 'Enroll No'
                },
                {
                    data: 'enroll_name',
                    title: 'Enroll Name'
                },
                {
                    data: 'department_name',
                    title: 'Department Name'
                },
                {
                    data: 'verify_mode',
                    title: 'Verify Mode'
                },
                {
                    data: 'formatted_punching_datetime',
                    title: 'Punching DateTime'
                }
            ]
        });

        // Trigger DataTable reload on filter change

    });

    //         processing: true,
    //         serverSide: true,
    //         columns: [{
    //                 data: 'id',
    //                 name: 'id'
    //             },
    //             {
    //                 data: 'enroll_no',
    //                 name: 'enroll_no'
    //             },
    //             {
    //                 data: 'enroll_name',
    //                 name: 'enroll_name'
    //             },
    //             {
    //                 data: 'department_name',
    //                 name: 'department_name'
    //             },
    //             {
    //                 data: 'verify_mode',
    //                 name: 'verify_mode'
    //             },
    //             {
    //                 data: 'formatted_punching_datetime',
    //                 name: 'formatted_punching_datetime'
    //             }
    //         ],


    //     });
    // });








    function punch_category() {
        var Formdata = {
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val(),
            punching_category: $('#punching_category').val(),
            _token: '{{ csrf_token() }}'
        }
        $.ajax({
            url: "{{route('biometric_attendance.punch')}}",
            method: "POST",
            data: Formdata,
            success: function(data) {
                // alert('success');
                if (data) {
                    var table = $('#example').DataTable();
                    table.clear().rows.add(data.data).draw();


                    // $('#example').append(data);
                } else {
                    alert('no data');
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);
            }


        });
    }
    </script>

    <!-- </div> -->

    @endsection
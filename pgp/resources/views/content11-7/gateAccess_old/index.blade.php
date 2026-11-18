@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Academic Year Creation')

@section('content')
<style>
/* btn style */
.btn-right {
    float: right;
}

.btn-medium-custom {
    padding: 6px 12px;
    font-size: 14px;
}
</style>

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4 class="">
            <span class="text-muted fw-light">Gate Entry/</span> Access Gate OUT
        </h4>
    </div>
    <!-- <div class="col-sm-6">
        @if(session('user_type') == '66eaeaf79982060867')
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/pass_entry_from')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
        @else
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/PassEntry')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
        @endif
    </div> -->


</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="pass_entry_table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Gate ID</th>
                        <th>Area Access</th>
                        <th>Vehicle/Person</th>
                        <th>Pass ID</th>
                        <th>Pass Type</th>
                        <th>Date</th>
                        <th>IN Time</th>
                        <th>Out Time</th>
                    </tr>
                </thead>
                <tbody>
                 <tr>

                    <td>1</td>
                    <td>5363000</td>
                    <td>Inside Custom Bound Area(SBW)</td>
                    <td>TN23AS1233</td>
                    <td>00001</td>
                    <td>Day</td>
                    <td>25-10-2024</td>
                    <td>09:21 AM</td>
                    <td>03:43 PM</td> 
                </tr>
                <tr>
                    <td>1</td>
                    <td>53636363</td>
                    <td>Administrative Office</td>
                    <td>Karthick</td>
                    <td>00031</td>
                    <td>Day</td>
                    <td>25-10-2024</td>
                    <td>09:30 AM</td>
                    <td>01:10 PM</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>53636900</td>
                    <td>Inside Custom Bound Area(SBW)</td>
                    <td>TN30AC0989</td>
                    <td>00031</td>
                    <td>Weekly</td>
                    <td>25-10-2024</td>
                    <td>09:41 AM</td>
                    <td>05:01 PM</td>
                </tr>

                </tbody>
            </table>
            <!-- medium modal -->
            <div class="modal fade" id="mediumModal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel"
                aria-hidden="true" style="--bs-modal-width: 66rem !important;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="mediumModalLabel">Pass Request View</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="mediumBody">
                            <div>
                                <div class="row mb-3 mt-2">

                                    <div class="col-sm-2  ">
                                        <label for="colFormLabelSm" class="col-form-label">Name</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  "><b>Tuticorin Lorry Owners
                                                Association</b></label>
                                    </div>

                                    <div class="col-sm-2">
                                        <label for="colFormLabelSm" class="col-form-label">Mobile no</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  "><b>9090909018</b></label>
                                    </div>

                                    <div class="col-sm-2">
                                        <label for="colFormLabelSm" class="col-form-label">Email Id</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-12 col-form-label  "><b>tutlorryass@gmail.com</b></label>
                                    </div>

                                    <div class="col-sm-2">
                                        <label for="colFormLabelSm" class="col-form-label">Address</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="col-sm-12 col-form-label" class="col-form-label">Tuticorin</label>
                                    </div>
                                </div>

                                <hr>
                                <h6>Pass Details</h6>
                                <div id="dataTableContainer" class="table-responsive ">
                                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>S.No</th>
                                                <th>RFID Card No.</th>
                                                <th>Registration No.</th>
                                                <th>Pass Type</th>
                                                <th>No of Days</th>
                                                <th>From Date</th>
                                                <th>To Date</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
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

<script>
// display a modal (medium modal)
$(document).on('click', '#mediumButton', function(event) {
    event.preventDefault();
    let href = $(this).attr('data-attr');
    $('#mediumModal').modal("show");


});

// Delete center function

function deleteCenter(id) {
    // alert();
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("pass_entry.delete", ":id") }}'.replace(":id",
                    id), // Correct URL

                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        sweetalert('success_delete');
                        window.location.href =
                            "{{ route('pass_entry') }}";
                        // Swal.fire('Deleted!', response.message, 'success').then(() => {
                        //     window.location.reload();
                        // });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });

        }
    });
}
</script>

@endsection
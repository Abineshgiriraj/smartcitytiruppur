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
            <span class="text-muted fw-light">Pass/</span> Pass Entry
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/pass_entry_from')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>


</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="pass_entry_table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Pass ID</th>
                        <th>Agent Name</th>
                        <th>Person / Vehicle Type</th>
                        <th>No of Pass</th>
                        <!-- <th>Approved No of Vehicle/Person</th> -->
                        <!-- <th>Approval status</th> -->
                        <th>Payment</th>
                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($pass_entry_list as $pass_entrylist)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $pass_entrylist->pass_id }}</td>
                        <td>{{ $firm_registration1[$pass_entrylist->firms_agent] ?? 'N/A' }}</td>
                        <td>{{ $personTypes[$pass_entrylist->persontype] ?? 'N/A' }} </td>
                        <td>{{ $pass_entrylist->sublist_count }}</td>
                        <!-- <td>
                            <span
                                class="badge {{ $pass_entrylist->pass_status == 'Approved' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($pass_entrylist->pass_status) }}
                            </span>
                        </td> -->
                        <td>
                            <button type="button" id="openTableButton" class="btn btn-primary"
                                onclick="window.location.href='{{ url('/payment_option') }}'">Pay</button>
                        </td>
                        <td>
                            <!-- <a class="text-secondary" data-toggle="modal" id="mediumButton" data-target="#mediumModal">
                                <i class="fas fa-eye"></i>
                            </a> -->
                            <a href="{{ route('pass_entry.edit', $pass_entrylist->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a onclick="deleteCenter({{ $pass_entrylist->id }})">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

                <!-- <td>1</td>
                    <td>10965764</td>
                    <td>Tuticorin Lorry Owners Association</td>
                    <td>1</td>
                    <td>1</td>
                    <td>2</td>
                    <td>Approved</td>
                    <td>Paid</td> -->
                <!-- <td><button type="button" id="openTableButton" class="btn btn-primary"
                            onclick="window.location.href='{{ url('/payment_option')}}'">Pay</button></td> -->
                <!-- <td>
                        <a class="text-secondary" data-toggle="modal" id="mediumButton" data-target="#mediumModal">
                            <i class="fas fa-eye"></i>
                        </a> -->
                <!-- <a href="#" class="text-primary mr-2">
                           
                            <i class="fas fa-eye"></i> Edit Icon
                        </a>
                        <span class="mx-3"></span> -->
                <!-- </td> -->

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
                                            <tr>
                                                <td>1</td>
                                                <td>9867585</td>
                                                <td>RFD1213131</td>
                                                <td>Day</td>
                                                <td>1</td>
                                                <td>02.09.2024</td>
                                                <td>02.09.2024</td>
                                                <td>120</td>


                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>9067235</td>
                                                <td>RFD1210101</td>
                                                <td>Day</td>
                                                <td>2</td>
                                                <td>05.09.2024</td>
                                                <td>06.09.2024</td>
                                                <td>240</td>


                                            </tr>
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
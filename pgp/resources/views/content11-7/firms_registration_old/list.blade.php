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
            <span class="text-muted fw-light">User Firms/</span> Firms Registration
        </h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/firms_registration_add')}}'"
            type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>


</div>

<!-- Responsive Datatable -->
<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="table table-bordered dt-responsive nowrap" id="employee_designation">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Registartion Date</th>
                        <th>Type of Firm</th>
                        <th>Comapany Reg No</th>
                        <th>Company Name</th>
                        <th>Phone</th>
                        <th>email</th>
                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($firms as $key => $firm) <!-- Changed variable from $companies to $firms -->
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $firm ->entry_date}}</td>
                        <td>{{ $firm->firms }}</td> <!-- Correct variable name -->
                        <td>{{ $firm->company_reg_no }}</td> <!-- Correct variable name -->
                        <td>{{ $firm->name }}</td>
                        <td>{{ $firm->mobile }}</td> <!-- Correct variable name -->
                        <td>{{ $firm->email }}</td> <!-- Correct variable name -->
                        <td>
                        <a href="{{ route('firms_registration.edit', $firm->unique_id) }}" class="text-primary" aria-label="Edit Firm">
    <i class="fas fa-edit"></i> <!-- Edit Icon -->
</a>
                            <form action="{{ route('firms_registration.destroy', $firm->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-danger border-0 bg-transparent" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash-alt"></i> <!-- Delete Icon -->
                                </button>
                            </form>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}'
        });
    @endif



    
</script>
@endsection

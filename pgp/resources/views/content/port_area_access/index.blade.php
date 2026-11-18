@extends('layouts/contentNavbarLayout')

@section('title', 'Port Area of Access')

@section('content')

<!-- @if(session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
@endif -->

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4><span class="text-muted fw-light">Settings /</span> Port Area of Access</h4>
    </div>
    <div class="col-sm-6">
        <button class="btn btn-primary btn-right" onclick="window.location.href='{{ url('/port_area_access_add') }}'" type="button">
            <i class="i-Add"></i> Add New
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-datatable table-responsive">
            <table class="dt-responsive table border-top data-table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Area Name</th>
                        <th>Short Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    @foreach($port_area as $key => $area)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $area->area_name }}</td>
                        <td>{{ $area->short_name }}</td>
                        <td>{{ $area->description }}</td>
                        <td>{{ $area->status }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('port_area_access.edit', $area->id) }}" class="text-primary mr-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <span class="mx-1"></span>
                                <form action="{{ route('port_area_access.destroy', $area->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-danger" style="border: none; background: none;">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
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

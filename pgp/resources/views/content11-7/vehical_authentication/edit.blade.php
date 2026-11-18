@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Edit User Type')

@section('content')

<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Authentication/</span>Edit Harbour Permit Category</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="usertypeForm" method="post" onsubmit="return submitForm(event);">
            @csrf
            @method('PUT')
            <!-- Always use PUT method for updates -->

            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="department">Department</label>
                    <input type="text" class="form-control" id="department" name="department"
                        value="{{ old('department', $user_type->user_type) }}" />
                    <div id="usertypeError" class="text-danger"></div>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="1" {{ $user_type->is_active == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $user_type->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <div id="statusError" class="form-text text-danger"></div>
                </div>
            </div>
            <div class="row">
                <div class="btn-float">
                    <button type="button" class="btn btn-danger"
                        onclick="window.location.href='{{ route('department') }}'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>



<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>


<script>

</script>

@endsection
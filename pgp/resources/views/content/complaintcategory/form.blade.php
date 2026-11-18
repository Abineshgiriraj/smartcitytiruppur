@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
<h4 class=""><span class="text-muted fw-light">Settings /</span>Complaint category</h4>
</div>
</div>
<style>
/* btn style */
.btn-right {
    float: right;
}
</style>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
            <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        <span class="text-danger fw-bold">* Mandatory Fields</span>
                    </div>

                    <form id="complaintcategory_form">
                        <div class="row mb-3">
                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="Department_name">Department<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <select class="form-select" id="zone" name="zone">
                                        <option value="">Select Department</option>
                                        <option value="1">Accounts</option>
                                        <option value="2">e-Governance</option>
                                    </select>
                            </div>
                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="Department_name">Complaint Category<span
                                        class="text-danger fw-bold">*</span></label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <input type="text" class="form-control" id="type" name="type"
                                    value="{{ isset($location) ? $location->location_type : '' }}"
                                    oninput="validateInputName(this)" />
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="description">Description</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <textarea class="form-control" id="description" name="description"
                                    oninput="validatetextarea(this)">{{ isset($location) ? $location->description : '' }}</textarea>
                            </div>

                            <div class="col-sm-2 mt-3">
                                <label class="form-label" for="status">Status</label>
                            </div>
                            <div class="col-sm-4 mt-3">
                                <select class="form-select" name="status" id="status">
                                    <option value="1" {{ isset($location) && $location->is_active == 1 ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ isset($location) && $location->is_active == 0 ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>

                        </div>

                        <div class="btn-float">
                            <button type="button" class="btn btn-danger"
                                onclick="window.location.href='{{ route('complaintcategory') }}'">Cancel</button>
                            <button type="submit"
                                class="btn btn-primary">{{ isset($location) ? 'Update' : 'Save' }}</button>

                        </div>
                    </form>
                </div>
        </div>
    </div>
        </div>
    </div>

</div>
@endsection
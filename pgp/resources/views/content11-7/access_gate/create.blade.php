@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Port Operating System/</span>Access Gate</h4>
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
        <div class="card mb-4">
            <div class="card-body">
                <form id="access_gate_form">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-sm-2 mt-3">
                            <label class="form-label me-2" for="access_gate">Access Gate</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="access_gate" name="access_gate" value="{{ isset($access_gate) ? $access_gate->access_gate : '' }}">
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label class="form-label" for="short_name">Short Name</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <input type="text" class="form-control" id="short_name" name="short_name" value="{{ isset($access_gate) ? $access_gate->short_name : '' }}">
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label class="form-label" for="description">Description</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <textarea class="form-control" id="description" name="description">{{ isset($access_gate) ? $access_gate->description : '' }}</textarea>
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label class="form-label" for="status">Status</label>
                        </div>
                        <div class="col-sm-4 mt-3">
                            <select class="form-select" name="status" id="status">
                                <option value="1" {{ isset($access_gate) && $access_gate->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="2" {{ isset($access_gate) && $access_gate->status == 2 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="btn-float">
                            <button type="button" class="btn btn-danger" onclick="window.location.href='{{ url('/access_gate') }}'">Cancel</button>
                            <button type="submit" class="btn btn-primary">{{ isset($access_gate) ? 'Update' : 'Submit' }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

<script>
    $(document).ready(function () {
        $('#access_gate_form').on('submit', function (e) {
            e.preventDefault();

            var formData = $(this).serialize();
            var url = "{{ isset($access_gate) ? route('access_gate.update', $access_gate->id) : route('access_gate.store') }}";
            var method = "{{ isset($access_gate) ? 'PUT' : 'POST' }}";

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Success', response.message, 'success').then(() => {
                            window.location.href = "{{ url('/access_gate') }}";
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        });
    });
</script>

@endsection

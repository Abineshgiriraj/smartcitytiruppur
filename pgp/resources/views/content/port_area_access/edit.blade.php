@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Port Area of Access')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4><span class="text-muted fw-light">Settings /</span> Port Area of Access</h4>
    </div>
</div>

<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="category_form" method="POST" action="{{ isset($port_area) ? route('port_area_access.update', $port_area->id) : route('port_area_access.store') }}">
                    @csrf
                    @if(isset($port_area))
                        @method('PUT')
                    @endif

                    <div class="row mb-3 com-form">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="area_name">Area Name</label>
                            <input type="text" class="form-control" id="area_name" name="area_name" value="{{ old('area_name', $port_area->area_name ?? '') }}" required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="short_name">Short Name</label>
                            <input type="text" class="form-control" id="short_name" name="short_name" value="{{ old('short_name', $port_area->short_name ?? '') }}" required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea id="description" class="form-control" name="description" required>{{ old('description', $port_area->description ?? '') }}</textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" name="status" id="status" required>
                                <option value="Active" {{ (isset($port_area) && $port_area->status == 'Active') ? 'selected' : '' }}>Active</option>
                                <option value="In Active" {{ (isset($port_area) && $port_area->status == 'In Active') ? 'selected' : '' }}>In Active</option>
                            </select>
                        </div>
                    </div>

                    <div class="btn-float">
                        <button type="button" class="btn btn-danger" onclick="window.location.href='{{ url('/port_area_access') }}'">Cancel</button>
                        <button type="submit" class="btn btn-primary">{{ isset($port_area) ? 'Update' : 'Submit' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('public/assets/js/common.js') }}"></script>
<script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

@endsection

@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">User Firms/</span> Firms Registration</h4>
    </div>
</div>

<style>
    /* btn style */
    .btn-right {
        float: right;
    }

    ,
    .font-weight-bold {
        font-weight: bold;
    }
</style>
<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form id="academicYearForm">
                    @csrf
                    @if(isset($acc_year))
                    @method('PUT')
                    @endif
                    <div class="row mb-3">

                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Firm ID:</label>
                            <label><strong>20240900001</strong></label>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Company Name</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Company Reg No</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Date Of Corporation</label>
                            <input type="date" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Bussiness Type</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Industry Type</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Company Address</label>
                            <textarea id="remarks" class="form-control"
                                name="remarks">{{ isset($acc_year) ? $acc_year->remarks : '' }}</textarea>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Mail</label>
                            <input type="email" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Phone</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">website</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Contract person Name</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Contract person Mobile No</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Position In Company</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">GSTIN no</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Document name</label>
                            <input type="text" class="form-control" id="designation" name="designation" />

                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Document Upload</label>
                            <input type="file" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">User Name</label>
                            <input type="text" class="form-control" id="designation" name="designation" />
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Password</label>
                            <input type="password" class="form-control" id="designation" name="designation" />

                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label" for="basic-default-fullname">Confrim Password</label>
                            <input type="password" class="form-control" id="designation" name="designation" />
                        </div>

                    </div>
                    <div class="btn-float">
                        <button type="button" class="btn btn-danger"
                            onclick="window.location.href='{{ url('/firms_registration_list')}}'">Cancel</button>
                        <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                        <button type="submit"
                            class="btn btn-primary">{{ isset($acc_year) ? 'Update' : 'Submit' }}</button>
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
@extends('layouts/contentNavbarLayout')
@section('title', ' Vertical Layouts - Forms')
@section('content')


<div class="row mb-3 page-hed">
    <div class="col-12">
        <h4 class=""><span class="text-muted fw-light">Common Module/</span> Taluk creation</h4>
    </div>
</div>



<div class="card">
    <div class="card-body">
        <form class="browser-default-validation">
            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="basic-default-fullname">Zone Name</label>
                    <select class="form-select" id="district" name="district">
                        <option value="">Select Zone</option>
                        <option value="Chennai">Zone 1</option>
                        <option value="Salem">Zone 2</option>
                        <option value="Erode">Zone 3</option>
                        <option value="Namakkal">Zone 4</option>
                        <option value="Coimbatore">Zone 5</option>
                        <option value="Karur">Zone 6</option>
                    </select>
                </div>
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="basic-default-fullname">District Name</label>
                    <select class="form-select" id="district" name="district">
                        <option value="">Select District</option>
                        <option value="Chennai">Chennai</option>
                        <option value="Salem">Salem</option>
                        <option value="Erode">Erode</option>
                        <option value="Namakkal">Namakkal</option>
                        <option value="Coimbatore">Coimbatore</option>
                        <option value="Karur">Karur</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label" for="basic-default-fullname">Taluk Name</label>
                    <input type="text" class="form-control" id="taluk_name" name="taluk_name" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="basic-default-message">Remarks</label>
                    <textarea id="remark" class="form-control" name="remark"></textarea>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="btn-float">
                <button type="submit" class="btn btn-danger"
                    onclick="window.location.href='{{ route('taluk_creation.index') }}'">Cancel</button>
                <button type="button" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
























@endsection
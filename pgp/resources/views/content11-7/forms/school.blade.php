@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<h4 class="py-3 mb-4"><span class="text-muted fw-light">Common Module/</span> School</h4>

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
                <form>
                    <div class="row mb-3">

                        <div class="col-sm-6">
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
                        <div class="col-sm-6">
                            <label class="form-label" for="basic-default-fullname">Zone Name</label>
                            <select class="form-select select2" id="zone" name="zone">
                                <option value="">Select Zone</option>
                                <option value="Madhavaram">Madhavaram</option>
                                <option value="Tondiarpet">Tondiarpet</option>
                                <option value="Royapuram">Royapuram</option>
                                <option value="Thiruvottiyur">Thiruvottiyur</option>
                                <option value="Manali">Manali</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="basic-default-fullname">School (or) Location Name</label>
                            <input type="text" class="form-control" id="school" name="school" />
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label" for="basic-default-message">Remarks</label>
                            <textarea id="remark" class="form-control" name="remark"></textarea>
                        </div>
                    </div>
                    <div class="btn-right">
                        <button type="submit" class="btn btn-danger">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection
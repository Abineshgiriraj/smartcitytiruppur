@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<div class="row mb-3 page-hed">
    <div class="col-12">
<h4 class=""><span class="text-muted fw-light">Common Module/</span> Zone</h4>
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
                <form>
                    <div class="row mb-3 com-form">

                        <div class="col-md-4 mb-3">

                            <label class="form-label" for="basic-default-fullname">Zone Name</label>
                            <input type="text" class="form-control" id="zone" name="zone" />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="basic-default-message">Remarks</label>
                            <textarea id="remark" class="form-control" name="remark"></textarea>
                        </div>
                    </div>
                    <div class="btn-float">
                         <button type="button" class="btn btn-danger" onclick="window.location.href='{{ url('/zone') }}'">Cancel</button>
               
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>
@endsection
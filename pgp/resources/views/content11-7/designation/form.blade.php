@extends('layouts/contentNavbarLayout')
@section('title', ' Vertical Layouts - Forms')
@section('content')


<div class="row mb-3 page-hed">
    <div class="col-12">
<h4 class=""><span class="text-muted fw-light">Common Module/</span> Designation creation</h4>
</div>
</div>

    
   
    <div class="card">
    <div class="card-body">
            <form class="browser-default-validation">
            <div class="row">
                <div class="mb-3 col-md-3">
                    <label class="form-label" for="basic-default-fullname">Designation Name</label>
                    <input type="text"  class="form-control" id="designation" name="designation"/>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="basic-default-message">Remarks</label>
                    <textarea id="remark" class="form-control" name="remark"></textarea>
                </div>
            </div>
            </form>
            <div class="row">
                <div class="btn-float">
                <button type="submit" class="btn btn-danger" onclick="window.location.href='{{ url('/designation_list') }}'">Cancel</button>
                <button type="button" class="btn btn-primary" >Submit</button>
                </div>
            </div>
    </div>
    </div>
  
        
        
        
        
  


















@endsection
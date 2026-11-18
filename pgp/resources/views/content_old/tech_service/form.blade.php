@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Forms')

@section('content')
    <div class="row mb-3 page-hed">
        <div class="col-12">
            <h4 class=""><span class="text-muted fw-light">Service Module/</span>Technical Service</h4>
        </div>
    </div>

    <style>
        .btn-right {
            float: right;
        }
    </style>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                    </div>

                    <form id="product_form">
                        <div class="row mb-4">
                            <!-- Supplier Details (Left Corner) -->
                            <div class="col-md-4">
    <h6 class="text-uppercase" style="font-size: large; color: #878a99;">Customer Details</h6>
    <h6 class="mb-2" style="font-size: large; color: #506018;"><strong>Kalai</strong></h6>
    <p class="mb-1" style="color: black;">AED345</p>
    <p class="mb-1" style="color: black;">440, Car Street, Coimbatore-26</p>
    <p class="mb-1">
        <i data-feather="phone" style="width:15px; height:15px; color:#506018"></i> 8908907898
    </p>
    <p class="mb-1">
        <i data-feather="mail" style="width:15px; height:15px; color:#506018"></i> kalai@mail.com
    </p>
    <p class="mb-1">
        <i data-feather="hash" style="width:15px; height:15px; color:#506018"></i> 33AAACH7409R1Z8
    </p>
</div>


                            <!-- Invoice Details (Right Side) -->
                            <div class="col-md-4 offset-md-4">
                                <h6 class="text-uppercase" style="font-size: large; color: #878a99;">Product Details</h6>
                                <div class="row mb-1">
                                    <div class="col-5 text-body-secondary">Product Group</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6" style="color: black;">Laptop</div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-5 text-body-secondary">Product Category</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6" style="color: black;">DELL</div>
                                </div>
                                 <div class="row mb-1">
                                    <div class="col-5 text-body-secondary">Product Name</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6" style="color: black;">Dell latitude 5420 i7</div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-5 text-body-secondary">Product Serial No</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6" style="color: black;">PRSNO-1234</div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-5 text-body-secondary">Invoice No</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6" style="color: black;">INV-78483</div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-5 text-body-secondary">Invoice Date</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6" style="color: black;">03-08-2024</div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-5 text-body-secondary">Warranty claim</div>
                                    <div class="col-1">:</div>
                                    <div class="col-6" style="color: black;">No</div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row mb-4 align-items-center">
                            <div class="col-sm-2">
                                <label  for="status" style="font-size: 15px; ">
                                    Reported Problem
                                </label>
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-sm-3">
                              <span id="call_type" style="font-size: 18px; font-weight: normal;color: black;">Laptop Issue</span> 
                            </div>
                        
                        </div>

                        <div class="row mb-4 align-items-center">
                            <div class="col-sm-2">
                                <label for="status" style="font-size: 15px;">
                                    Problem diagnostics
                                </label>
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-sm-3">
   <textarea class="form-control" name="product_group" id="product_group">
    </textarea>
                            </div>
                        </div>
                         <div class="row mb-4 align-items-center">
                            <div class="col-sm-2">
                                 <input class="form-check-input" type="checkbox" name="is_servicable" id="servicable9" value="2"
            onclick="Servicable()">
            <label class="form-check-label" for="servicable2">Spare Request</label>
                            </div>
                                                       <script>
                                 function Servicable() {
        const spareChecked = document.getElementById("servicable9").checked;

        const spareDiv = document.getElementById("spare");

        // Show or hide the corresponding sections
        spareDiv.style.display = spareChecked ? "block" : "none";
        }
        </script>
                              <div class="row" id="spare" style="display:none;">
            <div class="col-md-12">
            <div class="col-sm-12 mb-2 text-end">
              <button type="button" class="btn btn-primary" data-bs-toggle="modal"
              data-bs-target="#productModal">
              Request
              </button>
            </div>
            <table class="table table-bordered table-sm dt-responsive nowrap" id="invoice_table">
              <thead>
              <tr>
                <th>S.NO</th>
                <th>Spare</th>
                <th>Req Date</th>
                <th>Req Qty</th>
                <th>Rec Date</th>
                <th>Rec Qty</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>1</td>
                <td>SSD-64GB<br>RAM</td>
                <td>31-07-2024</td>
                <td>13</td>
                <td>01-08-2024</td>
                <td>10</td>
              </tr>
              </tbody>
            </table>
            </div>

          </div>

                        </div>
                        <hr>
                        <div class="row mb-4 align-items-center">
                            <div class="col-sm-2">
                                <label for="status" style="font-size: 15px;">
                                    Action Taken
                                </label>
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-sm-3">
   <textarea class="form-control" name="product_group" id="product_group">
    </textarea>
                            </div>
                            <div class="col-sm-2">
                            <label for="completed_date" style="font-size: 15px; ">
                                   Completed Date  </label>
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-sm-3">
                                                    <input type="date" class="form-control" id="warr_period" name="warr_period"/>

                        </div>
                        </div>
                        
 <div class="row mb-4 align-items-center">
                            <div class="col-sm-2">
                                <label for="status" style="font-size: 15px;">
                                    Remarks
                                </label>
                            </div>
                            <div class="col-1">:</div>
                            <div class="col-sm-3">
   <textarea class="form-control" name="product_group" id="product_group">
    </textarea>
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="text-end">
                            <button type="button" class="btn btn-danger" style="font-size: 18px; padding: 8px 15px;"
                                onclick="window.location.href='{{ route('tech_service') }}'">Cancel</button>
                            <button type="submit" class="btn btn-primary"
                                style="font-size: 18px; padding: 8px 15px;">Submit</button>
                            
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">

          <div class="modal-header">
          <h5 class="modal-title" id="productModalLabel">Add Spare</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
          <form id="productForm">
            <div class="mb-3">
            <label for="product_name" class="form-label">Spare Group</label>
            <select class="form-select select2" id="product_name" name="product_name">
              <option value="">Select Group</option>
              <option value="Product A">RAM</option>
              <option value="Product B">Display</option>
            </select>
            </div>
            <div class="mb-3">
            <label for="product_name" class="form-label">Spare Category</label>
            <select class="form-select select2" id="product_name" name="product_name">
              <option value="">Select Category</option>
              <option value="Product A">SSD</option>
              <option value="Product B">LED</option>
            </select>
            </div>
            <div class="mb-3">
            <label for="product_name" class="form-label">Spare Name</label>
            <select class="form-select select2" id="product_name" name="product_name">
              <option value="">Select Spare</option>
              <option value="Product A">64GB SSD 5</option>
              <option value="Product C">4K UHD (3840x2160)</option>
            </select>
            </div>
            <div class="mb-3">
            <label for="qty" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="qty" name="qty" min="1" required>
            </div>
          </form>
          </div>

          <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="addProductToTable()">Add</button>
          </div>

        </div>
        </div>
      </div>

    <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('public/assets/js/common.js') }}"></script>
    <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>

    <script>
          $(document).ready(function () {
        $('#productModal').on('shown.bs.modal', function () {
          $('#product_name').select2({
          dropdownParent: $('#productModal'), // important for modals
          width: '100%',
          placeholder: 'Select Product',
          allowClear: true
          });
        });
        });
    document.addEventListener("DOMContentLoaded", function () {
        const today = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD
        document.getElementById("warr_period").value = today;
    });

     
       document.addEventListener("DOMContentLoaded", function () {
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0'); // Months are 0-based
        const year = today.getFullYear();
        const formattedDate = `${day}-${month}-${year}`;
        document.getElementById('completed_date_value').textContent = formattedDate;
    });
        $(document).ready(function () {
            $('#product_form').submit(function (event) {
                event.preventDefault();

                let isValid = validateForm();
                if (!isValid) return;

                const formData = {
                    product_name: $('#product_name').val(),
                    product_category: $('#product_category').val(),
                    product_group: $('#product_group').val(),
                    unit: $('#unit').val(),
                    hsn_code: $('#hsn_code').val(),
                    warranty_period: $('#warr_period').val(),
                    description: $('#description').val(),
                    status: $('#status').val(),
                    _token: $('input[name="_token"]').val()
                };

                let isUpdate = "{{ isset($product) ? 'true' : 'false' }}";
                let url = isUpdate === 'true' ?
                    "{{ isset($product) ? route('product_creation.update', $product->id) : '' }}" :
                    "{{ route('product_creation.store') }}";

                if (isUpdate === 'true') {
                    formData._method = 'PUT';
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        Swal.fire({
                            title: 'Success!',
                            text: (isUpdate === 'true' ? 'Updated' : 'Created') + ' successfully!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: true,
                        }).then(() => {
                            window.location.href = "{{ route('product_creation') }}";
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let msg = Object.values(errors).flat().join('<br>');
                            Swal.fire('Validation Error', msg, 'warning');
                        } else {
                            Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                        }
                    }
                });
            });
        });


        
  
        function validateForm() {
            let isValid = true;
            const fields = ['product_name', 'product_category', 'product_group', 'unit', 'hsn_code', 'warr_period', 'status'];

            for (const id of fields) {
                if (document.getElementById(id).value.trim() === '') {
                    sweetalert('form_alert');
                    return false;
                }
            }

            return true;
        }

        $('#product_group').on('change', function () {
            let groupId = $(this).val();

            $.ajax({
                url: '{{ url("/get-categories-by-group") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    group_id: groupId
                },
                success: function (response) {
                    $('#product_category').html(response.options);
                },
                error: function () {
                    alert('Failed to fetch product categories.');
                }
            });
        });
    </script>

@endsection
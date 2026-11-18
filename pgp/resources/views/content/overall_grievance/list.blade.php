@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')

<style>
/* btn style */
.btn-right {
    float: right;
}
</style>

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="row mb-3 page-hed">
    <div class="col-sm-6">

        <h4 class="">
            <span class="text-muted fw-light">Grievance Module /</span> Overall Grievance  
        </h4>
    </div>
</div>
<!-- Tabs -->
<ul class="nav nav-tabs mb-3" id="complaintTabs">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#pendingTab">Pending</a>
    </li>
     <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#completedTab">Processing</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#completedTab">Completed</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#completedTab">Cancelling</a>
    </li>
</ul>

<div class="tab-content">
    <!-- Pending Tab -->
    <div class="tab-pane fade show active" id="pendingTab">
        <!-- Filter Section -->
        <div class="card shadow-sm mb-3">
            <div id="filterSection" class="card-body">
                <div class="row g-2 align-items-end mb-3">
    <!-- Date From -->
    <div class="col-md-2">
        <label class="form-label">From Date</label>
        <input type="date" id="from_date" class="form-control">
    </div>

    <!-- Date To -->
    <div class="col-md-2">
        <label class="form-label">To Date</label>
        <input type="date" id="to_date" class="form-control">
    </div>

    <!-- Zone -->
    <div class="col-md-2">
        <label class="form-label">Zone</label>
        <select id="zone_filter" class="form-select">
            <option value="">All Zones</option>
            <option value="ZONE - 01">ZONE - 01</option>
            <option value="ZONE - 02">ZONE - 02</option>
        </select>
    </div>

    <!-- Go Button -->
    <div class="col-md-1">
        <button id="filter_btn" class="btn btn-success w-100">
            <i class="bi bi-search"></i> Go
        </button>
    </div>

    <!-- Right-aligned buttons -->
    <div class="col-md-5 d-flex justify-content-end gap-2">
        <button id="print_btn" class="btn btn-outline-primary">
            <i class="bi bi-printer"></i> Print
        </button>
        <button id="excel_btn" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </button>
    </div>
</div>
            </div>
        </div>

        <!-- Pending Table -->
        <div class="card">
            <div class="card-body">
                <div class="card-datatable table-responsive">
                    <table class="dt-responsive table border-top">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Grievance ID</th>
                                <th>Name</th>
                                <th>Mobile No</th>
                                <th>Zone</th>
                                <th>Location</th>
                                <th>Source</th>
                                <th>Attachment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                    <!-- Complaint 1 -->
                    <tr>
                        <td style="text-align:center;">1</td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px; text-decoration: underline; color: #000000; cursor: pointer;"
                                onclick="toggleComplaintRow(this)">
                                COM-20250800001
                            </div>
                            <div style="font-size: 14px;">30-07-2025</div>
                        </td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px;">Manoj</div>
                            <div style="font-size: 14px;">S.V Colony 2nd Street</div>
                        </td>
                        <td style="text-align:left;">989897855</td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px;">I</div>
                            <div style="font-size: 14px;">Ward-2</div>
                        </td>
                        <td style="text-align:left;">Lakshmi Nagar 1st Street,Tiruppur</td>
                        <td style="text-align:left;">Whatsapp</td>
                        <td style="text-align:center;">
    <i class="fas fa-paperclip" 
       style="font-size: 18px; color: #356106; cursor: pointer;"
       data-bs-toggle="modal" 
       data-bs-target="#attachmentModal"></i>
</td>
                        <td style="text-align:center;">
    <!-- SMS Icon -->
    <i class="bi bi-chat-dots-fill" 
       style="font-size: 18px; color: #356106; cursor: pointer; margin-right: 4px;" 
       title="Send SMS"
       onclick="openSMSModal()"></i>

    <!-- Employee Lifecycle Icon -->
    <i class="bi bi-people-fill" 
       style="font-size: 18px; color: #356106; cursor: pointer;" 
       title="View Employee Lifecycle"
       onclick="openLifecycleModal()"></i>
</td>
                    </tr>
                    <tr class="complaint-details-row" style="display: none; background: #f8f9fa;">
                        <td colspan="8">
                             <div style="font-weight: bold; margin-bottom: 8px; color: #356106;">
        Complaint Info 
    </div>
    <table style="border-collapse: collapse; width: 100%;">
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; white-space: nowrap;">Pending at</td>
            <td style="padding: 4px 8px;">RAMAKRISHNAN A - SI</td>
        </tr>
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; white-space: nowrap;">Category</td>
            <td style="padding: 4px 8px;">Garbage</td>
        </tr>
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; vertical-align: top; white-space: nowrap;">Description</td>
            <td style="padding: 4px 8px;">
               Lakshmi Nag 30-வது வார்டில் குப்பைகள் எடுக்காமல் குப்பைகள் குவிந்துள்ளது .குப்பைகளை சுத்தம் செய்ய வேண்டும்.
            </td>
        </tr>
    </table>
</div>
                        </td>
                    </tr>

                    <!-- Complaint 2 -->
                    <tr>
                        <td style="text-align:center;">2</td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px; text-decoration: underline; color: #000000; cursor: pointer;"
                                onclick="toggleComplaintRow(this)">
                                COM-20250800002
                            </div>
                            <div style="font-size: 14px;">14-08-2025</div>
                        </td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px;">Hariesh</div>
                            <div style="font-size: 14px;">MNM Colony 4th street</div>
                        </td>
                        <td style="text-align:left;">989897877</td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px;">IV</div>
                            <div style="font-size: 14px;">Ward-8</div>
                        </td>
                        <td style="text-align:left;">Mani Nagar 7st Street,Tiruppur</td>
                        <td style="text-align:left;">Newspaper</td>
                        <td style="text-align:center;">
    <i class="fas fa-paperclip" 
       style="font-size: 18px; color: #356106; cursor: pointer;"
       data-bs-toggle="modal" 
       data-bs-target="#attachmentModal"></i>
</td>
                         <td style="text-align:center;">
    <!-- SMS Icon -->
    <i class="bi bi-chat-dots-fill" 
       style="font-size: 18px; color: #356106; cursor: pointer; margin-right: 4px;" 
       title="Send SMS"
       onclick="openSMSModal()"></i>

    <!-- Employee Lifecycle Icon -->
    <i class="bi bi-people-fill" 
       style="font-size: 18px; color: #356106; cursor: pointer;" 
       title="View Employee Lifecycle"
       onclick="openLifecycleModal()"></i>
</td>
                    </tr>
                    <tr class="complaint-details-row" style="display: none; background: #f8f9fa;">
                        <td colspan="8">
                             <div style="font-weight: bold; margin-bottom: 8px; color: #356106;">
        Complaint Info 
    </div>
    <table style="border-collapse: collapse; width: 100%;">
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; white-space: nowrap;">Pending at</td>
            <td style="padding: 4px 8px;">
ARUMUGAM K - AE(ELECTRICAL)</td>
        </tr>
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; white-space: nowrap;">Category</td>
            <td style="padding: 4px 8px;">Non illumination of Street Light</td>
        </tr>
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; vertical-align: top; white-space: nowrap;">Description</td>
            <td style="padding: 4px 8px;">
               E.R.P.Nagar Cross Street-(19) வது வார்டில் தெருவிளக்கு எரியவில்லை .எனவே தெருவிளக்கை விரைவில் சரி செய்து தருமாறு மாநகராட்சியை தாழ்மையுடன் கேட்டுக் கொள்கிறேன்.
            </td>
        </tr>
    </table>
</div>
                        </td>
                    </tr>

                    <!-- Complaint 3 -->
                    <tr>
                        <td style="text-align:center;">3</td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px; text-decoration: underline; color: #000000; cursor: pointer;"
                                onclick="toggleComplaintRow(this)">
                                COM-20250800003
                            </div>
                            <div style="font-size: 14px;">14-08-2025</div>
                        </td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px;">Manoj</div>
                            <div style="font-size: 14px;">MK Magal 2nd Street</div>
                        </td>
                        <td style="text-align:left;">9457597855</td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px;">III</div>
                            <div style="font-size: 14px;">Ward-9</div>
                        </td>
                        <td style="text-align:left;">Ram Nagar 1st Street,Tiruppur</td>
                        <td style="text-align:left;">CM Cell</td>
                        <td style="text-align:center;">
    <i class="fas fa-paperclip" 
       style="font-size: 18px; color: #356106; cursor: pointer;"
       data-bs-toggle="modal" 
       data-bs-target="#attachmentModal"></i>
</td>
                         <td style="text-align:center;">
    <!-- SMS Icon -->
    <i class="bi bi-chat-dots-fill" 
       style="font-size: 18px; color: #356106; cursor: pointer; margin-right: 4px;" 
       title="Send SMS"
       onclick="openSMSModal()"></i>

    <!-- Employee Lifecycle Icon -->
    <i class="bi bi-people-fill" 
       style="font-size: 18px; color: #356106; cursor: pointer;" 
       title="View Employee Lifecycle"
       onclick="openLifecycleModal()"></i>
</td>
                    </tr>
                    <tr class="complaint-details-row" style="display: none; background: #f8f9fa;">
                        <td colspan="8">
                            <div style="font-size: 14px; ">
    <div style="font-weight: bold; margin-bottom: 8px; color: #356106;">
        Complaint Info 
    </div>
    <table style="border-collapse: collapse; width: 100%;">
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; white-space: nowrap;">Pending at</td>
            <td style="padding: 4px 8px;">K SURESH - AE(UGD)</td>
        </tr>
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; white-space: nowrap;">Category</td>
            <td style="padding: 4px 8px;">UGD - Cleaning of Drainage</td>
        </tr>
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; vertical-align: top; white-space: nowrap;">Description</td>
            <td style="padding: 4px 8px;">
                M.S Nagar 1st Street-(17)வது வார்டில் பாதாள சாக்கடை அடைத்துள்ளது. 
                பாதாள சாக்கடையை சுத்தம் செய்து தருமாறு மாநகராட்சியை பணிவுடன் கேட்டுக்கொள்கிறேன். 
                (UGD SUPERVISOR NUM: 84894 00090)
            </td>
        </tr>
    </table>
</div>

                        </td>
                    </tr>

                </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Tab -->
    <div class="tab-pane fade" id="completedTab">
        <!-- Filter Section -->
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2 align-items-end mb-3">
    <!-- Date From -->
    <div class="col-md-2">
        <label class="form-label">From Date</label>
        <input type="date" id="from_date" class="form-control">
    </div>

    <!-- Date To -->
    <div class="col-md-2">
        <label class="form-label">To Date</label>
        <input type="date" id="to_date" class="form-control">
    </div>

    <!-- Zone -->
    <div class="col-md-2">
        <label class="form-label">Zone</label>
        <select id="zone_filter" class="form-select">
            <option value="">All Zones</option>
            <option value="ZONE - 01">ZONE - 01</option>
            <option value="ZONE - 02">ZONE - 02</option>
        </select>
    </div>

    <!-- Go Button -->
    <div class="col-md-1">
        <button id="filter_btn" class="btn btn-success w-100">
            <i class="bi bi-search"></i> Go
        </button>
    </div>

    <!-- Right-aligned buttons -->
    <div class="col-md-5 d-flex justify-content-end gap-2">
        <button id="print_btn" class="btn btn-outline-primary">
            <i class="bi bi-printer"></i> Print
        </button>
        <button id="excel_btn" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </button>
    </div>
</div>
            </div>
        </div>

        <!-- Completed Table -->
        <div class="card">
            <div class="card-body">
                <div class="card-datatable table-responsive">
                    <table class="dt-responsive table border-top">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Grievance ID</th>
                                <th>Name</th>
                                <th>Mobile No</th>
                                <th>Zone</th>
                                <th>Location</th>
                                <th>Source</th>
                                <th>Attachment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                    <!-- Complaint 1 -->
                    <tr>
                        <td style="text-align:center;">1</td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px; text-decoration: underline; color: #000000; cursor: pointer;"
                                onclick="toggleComplaintRow(this)">
                                COM-20250800001
                            </div>
                            <div style="font-size: 14px;">30-07-2025</div>
                        </td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px;">Manoj</div>
                            <div style="font-size: 14px;">S.V Colony 2nd Street</div>
                        </td>
                        <td style="text-align:left;">989897855</td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px;">I</div>
                            <div style="font-size: 14px;">Ward-2</div>
                        </td>
                        <td style="text-align:left;">Lakshmi Nagar 1st Street,Tiruppur</td>
                        <td style="text-align:left;">Whatsapp</td>
                        <td style="text-align:center;">
    <i class="fas fa-paperclip" 
       style="font-size: 18px; color: #356106; cursor: pointer;"
       data-bs-toggle="modal" 
       data-bs-target="#attachmentModal"></i>
</td>
                           <td style="text-align:center;">
 <!-- Employee Lifecycle Icon -->
    <i class="bi bi-people-fill" 
       style="font-size: 18px; color: #356106; cursor: pointer;" 
       title="View Employee Lifecycle"
       onclick="openLifecycleModal()"></i>
</td>
                    </tr>
                    <tr class="complaint-details-row" style="display: none; background: #f8f9fa;">
                        <td colspan="8">
                             <div style="font-weight: bold; margin-bottom: 8px; color: #356106;">
        Complaint Info 
    </div>
    <table style="border-collapse: collapse; width: 100%;">
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; white-space: nowrap;">Completed at</td>
            <td style="padding: 4px 8px;">RAMAKRISHNAN A - SI</td>
        </tr>
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; white-space: nowrap;">Category</td>
            <td style="padding: 4px 8px;">Garbage</td>
        </tr>
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; vertical-align: top; white-space: nowrap;">Description</td>
            <td style="padding: 4px 8px;">
               Lakshmi Nag 30-வது வார்டில் குப்பைகள் எடுக்காமல் குப்பைகள் குவிந்துள்ளது .குப்பைகளை சுத்தம் செய்ய வேண்டும்.
            </td>
        </tr>
    </table>
</div>
                        </td>
                    </tr>

                    <!-- Complaint 2 -->
                    <tr>
                        <td style="text-align:center;">2</td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px; text-decoration: underline; color: #000000; cursor: pointer;"
                                onclick="toggleComplaintRow(this)">
                                COM-20250800002
                            </div>
                            <div style="font-size: 14px;">14-08-2025</div>
                        </td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px;">Hariesh</div>
                            <div style="font-size: 14px;">MNM Colony 4th street</div>
                        </td>
                        <td style="text-align:left;">989897877</td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px;">IV</div>
                            <div style="font-size: 14px;">Ward-8</div>
                        </td>
                        <td style="text-align:left;">Mani Nagar 7st Street,Tiruppur</td>
                        <td style="text-align:left;">Newspaper</td>
                        <td style="text-align:center;">
    <i class="fas fa-paperclip" 
       style="font-size: 18px; color: #356106; cursor: pointer;"
       data-bs-toggle="modal" 
       data-bs-target="#attachmentModal"></i>
</td>
                        <td style="text-align:center;">
 <!-- Employee Lifecycle Icon -->
    <i class="bi bi-people-fill" 
       style="font-size: 18px; color: #356106; cursor: pointer;" 
       title="View Employee Lifecycle"
       onclick="openLifecycleModal()"></i>
</td>
                    </tr>
                    <tr class="complaint-details-row" style="display: none; background: #f8f9fa;">
                        <td colspan="8">
                             <div style="font-weight: bold; margin-bottom: 8px; color: #356106;">
        Complaint Info 
    </div>
    <table style="border-collapse: collapse; width: 100%;">
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; white-space: nowrap;">Completed at</td>
            <td style="padding: 4px 8px;">
ARUMUGAM K - AE(ELECTRICAL)</td>
        </tr>
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; white-space: nowrap;">Category</td>
            <td style="padding: 4px 8px;">Non illumination of Street Light</td>
        </tr>
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; vertical-align: top; white-space: nowrap;">Description</td>
            <td style="padding: 4px 8px;">
               E.R.P.Nagar Cross Street-(19) வது வார்டில் தெருவிளக்கு எரியவில்லை .எனவே தெருவிளக்கை விரைவில் சரி செய்து தருமாறு மாநகராட்சியை தாழ்மையுடன் கேட்டுக் கொள்கிறேன்.
            </td>
        </tr>
    </table>
</div>
                        </td>
                    </tr>

                    <!-- Complaint 3 -->
                    <tr>
                        <td style="text-align:center;">3</td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px; text-decoration: underline; color: #000000; cursor: pointer;"
                                onclick="toggleComplaintRow(this)">
                                COM-20250800003
                            </div>
                            <div style="font-size: 14px;">14-08-2025</div>
                        </td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px;">Manoj</div>
                            <div style="font-size: 14px;">MK Magal 2nd Street</div>
                        </td>
                        <td style="text-align:left;">9457597855</td>
                        <td style="text-align:left;">
                            <div style="font-weight: bold; font-size: 16px;">III</div>
                            <div style="font-size: 14px;">Ward-9</div>
                        </td>
                        <td style="text-align:left;">Ram Nagar 1st Street,Tiruppur</td>
                        <td style="text-align:left;">CM Cell</td>
                        <td style="text-align:center;">
    <i class="fas fa-paperclip" 
       style="font-size: 18px; color: #356106; cursor: pointer;"
       data-bs-toggle="modal" 
       data-bs-target="#attachmentModal"></i>
</td>
                      <td style="text-align:center;">
 <!-- Employee Lifecycle Icon -->
    <i class="bi bi-people-fill" 
       style="font-size: 18px; color: #356106; cursor: pointer;" 
       title="View Employee Lifecycle"
       onclick="openLifecycleModal()"></i>
</td>
                    </tr>
                    <tr class="complaint-details-row" style="display: none; background: #f8f9fa;">
                        <td colspan="8">
                            <div style="font-size: 14px; ">
    <div style="font-weight: bold; margin-bottom: 8px; color: #356106;">
        Complaint Info 
    </div>
    <table style="border-collapse: collapse; width: 100%;">
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; white-space: nowrap;">Completed at</td>
            <td style="padding: 4px 8px;">K SURESH - AE(UGD)</td>
        </tr>
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; white-space: nowrap;">Category</td>
            <td style="padding: 4px 8px;">UGD - Cleaning of Drainage</td>
        </tr>
        <tr>
            <td style="color: red; font-weight: bold; padding: 4px 8px; vertical-align: top; white-space: nowrap;">Description</td>
            <td style="padding: 4px 8px;">
                M.S Nagar 1st Street-(17)வது வார்டில் பாதாள சாக்கடை அடைத்துள்ளது. 
                பாதாள சாக்கடையை சுத்தம் செய்து தருமாறு மாநகராட்சியை பணிவுடன் கேட்டுக்கொள்கிறேன். 
                (UGD SUPERVISOR NUM: 84894 00090)
            </td>
        </tr>
    </table>
</div>

                        </td>
                    </tr>

                </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons & JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!--/ Responsive Datatable -->
<div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="attachmentModalLabel">Attachments</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <!-- Image 1 -->
          <div class="col-md-4">
            <img src="https://via.placeholder.com/200" class="img-fluid rounded border" alt="Attachment 1">
          </div>
          <!-- Image 2 -->
          <div class="col-md-4">
            <img src="https://via.placeholder.com/200" class="img-fluid rounded border" alt="Attachment 2">
          </div>
          <!-- Image 3 -->
          <div class="col-md-4">
            <img src="https://via.placeholder.com/200" class="img-fluid rounded border" alt="Attachment 3">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="smsModal" tabindex="-1" aria-labelledby="smsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="smsModalLabel">SMS Log</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <textarea class="form-control" rows="5">
Cell: 8220050467, Delivered On: 13-Aug-2025 10:27 AM
A new complaint with reference no. 33939 has been assigned to you regarding a request for cleaning of drainage. Contact number: 9788377137.
- Tiruppur City Municipal Corporation
        </textarea>
      </div>
      
    </div>
  </div>
</div>


<div class="modal fade" id="lifecycleModal" tabindex="-1" aria-labelledby="lifecycleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="lifecycleModalLabel">Complaint Lifecycle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-striped table-sm">
          <thead class="table-success">
            <tr>
              <th style="width: 60px;text-align:center">S.No</th>
              <th>Designation</th>
              <th style="width: 120px;">No. of Days</th>
              <th style="width: 180px;">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>SI</td>
              <td>2</td>
              <td>12-08-2025</td>
            </tr>
            <tr>
              <td>2</td>
              <td>SO</td>
              <td>1</td>
              <td>13-Aug-2025</td>
            </tr>
             <tr>
              <td>3</td>
              <td>CHO</td>
              <td>1</td>
              <td>14-Aug-2025</td>
            </tr>
             <tr>
              <td>4</td>
              <td>COMMR</td>
              <td>2</td>
              <td>16-Aug-2025</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


<script>
function toggleComplaintRow(element) {
    const currentRow = element.closest("tr");
    const detailsRow = currentRow.nextElementSibling;
    if (detailsRow && detailsRow.classList.contains("complaint-details-row")) {
        detailsRow.style.display = (detailsRow.style.display === "none" || detailsRow.style.display === "") 
            ? "table-row" 
            : "none";
    }
}


    function openSMSModal() {
    var myModal = new bootstrap.Modal(document.getElementById('smsModal'));
    myModal.show();
}

function openLifecycleModal() {
    var myModal = new bootstrap.Modal(document.getElementById('lifecycleModal'));
    myModal.show();
}
        $(document).ready(function () {
            $('#ward_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("ward") }}',

                columns: [
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                        className: "text-end"
                    },
                    {
                        data: 'zone',
                        name: 'zone'
                    },
                   
                    { 
                        data: 'description', 
                        name: 'description' 
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [[0, 'asc']]
            });
        });



        // function confirmDelete(id) {
        //     Swal.fire({
        //         title: 'Are you sure?',
        //         text: "This will delete the category.",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonText: 'Yes, delete it!'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: '{{ route("zone.delete", ":id") }}'.replace(':id', id),
        //                 type: 'POST',
        //                 data: {
        //                     _method: 'DELETE',
        //                     _token: '{{ csrf_token() }}'
        //                 },
        //                 success: function (response) {
        //                     $('#zone_table').DataTable().ajax.reload();
        //                     Swal.fire('Deleted!', 'Deleted Successfully.', 'success');
        //                 },
        //                 error: function () {
        //                     Swal.fire('Error!', 'Something went wrong.', 'error');
        //                 }
        //             });
        //         }
        //     });
        // }


    </script>

@endsection
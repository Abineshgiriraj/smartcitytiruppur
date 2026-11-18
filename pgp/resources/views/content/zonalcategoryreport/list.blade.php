@extends('layouts/contentNavbarLayout')

@section('title', 'Zone and Department Wise Report')

@section('content')
<style>
    /* Custom button styles */
    .btn-right {
        float: right;
    }
</style>

<?php $get_zone_name = get_zone(); ?>

<div class="row mb-3 page-hed">
    <div class="col-sm-6">
        <h4>
            <span class="text-muted fw-light">Report /</span> Zonal Wise Category Report 
        </h4>
    </div>
</div>

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
        <select class="form-select" id="zone" name="zone">
        <option value="">Select Zone</option>
        @foreach ($get_zone_name as $get_zone)
            <option value="{{ $get_zone->unique_id }}" 
                {{ isset($zonalwisecategory) && $zonalwisecategory->zone == $get_zone->unique_id ? 'selected' : '' }}>
                {{ $get_zone->zone }}
            </option>
        @endforeach
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
<!-- Zone and Department Wise Report Table -->
<div class="card">
    <div class="card-header">
        <h5>Zonal Wise Category Report</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th>Zone</th>
                    <th>Department</th>
                    <th>Received</th>
                    <th>Resolved</th>
                    <th>Pending</th>
                </tr>
            </thead>
            <tbody>
                <!-- Zone 01 -->
                <tr>
                    <td rowspan="5">ZONE - 01</td>
                    <td>Engineering - Sewerage</td>
                    <td>58</td>
                    <td>51</td>
                    <td>13</td>
                </tr>
                <tr><td>Engineering - Street Light</td><td>34</td><td>28</td><td>6</td></tr>
                <tr><td>Engineering - Water Supply</td><td>28</td><td>20</td><td>8</td></tr>
                <tr><td>Engineering - Works</td><td>42</td><td>38</td><td>4</td></tr>
                <tr><td>Public Health</td><td>23</td><td>15</td><td>8</td></tr>

                <!-- Zone 02 -->
                <tr>
                    <td rowspan="5">ZONE - 02</td>
                    <td>Engineering - Sewerage</td>
                    <td>104</td>
                    <td>71</td>
                    <td>33</td>
                </tr>
                <tr><td>Engineering - Street Light</td><td>82</td><td>64</td><td>18</td></tr>
                <tr><td>Engineering - Water Supply</td><td>55</td><td>41</td><td>14</td></tr>
                <tr><td>Engineering - Works</td><td>70</td><td>60</td><td>10</td></tr>
                <tr><td>Public Health</td><td>50</td><td>32</td><td>18</td></tr>

                <!-- Zone 03 -->
                <tr>
                    <td rowspan="5">ZONE - 03</td>
                    <td>Engineering - Sewerage</td>
                    <td>91</td>
                    <td>60</td>
                    <td>31</td>
                </tr>
                <tr><td>Engineering - Street Light</td><td>61</td><td>49</td><td>12</td></tr>
                <tr><td>Engineering - Water Supply</td><td>37</td><td>27</td><td>10</td></tr>
                <tr><td>Engineering - Works</td><td>56</td><td>44</td><td>12</td></tr>
                <tr><td>Public Health</td><td>42</td><td>29</td><td>13</td></tr>

                <!-- Zone 04 -->
                <tr>
                    <td rowspan="5">ZONE - 04</td>
                    <td>Engineering - Sewerage</td>
                    <td>72</td>
                    <td>54</td>
                    <td>18</td>
                </tr>
                <tr><td>Engineering - Street Light</td><td>49</td><td>39</td><td>10</td></tr>
                <tr><td>Engineering - Water Supply</td><td>34</td><td>25</td><td>9</td></tr>
                <tr><td>Engineering - Works</td><td>46</td><td>39</td><td>7</td></tr>
                <tr><td>Public Health</td><td>40</td><td>30</td><td>10</td></tr>
            </tbody>
        </table>
    </div>
</div>
<!-- Bootstrap Icons & JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection

@extends('layouts/contentNavbarLayout')

@section('title', 'Municipal Dashboard')

@section('content')
<div class="tcmc-dashboard">
  <div class="tcmc-page-heading">
    <div>
      <p class="tcmc-kicker">Tiruppur City Municipal Corporation</p>
      <h1>Grievance Management</h1>
      <p>Welcome, {{ session('staff_name') ?: 'Municipal Staff' }}. Monitor and manage citizen service requests.</p>
    </div>
    <span class="tcmc-date-label">{{ now()->format('d M Y') }}</span>
  </div>

  @if($dataError)
    <div class="alert alert-warning" role="alert">{{ $dataError }}</div>
  @endif

  <div class="tcmc-stats">
    @foreach ([
      ['label' => 'Total Complaints', 'value' => $stats['total'], 'icon' => 'fa-folder-open', 'tone' => 'blue'],
      ['label' => 'Pending', 'value' => $stats['pending'], 'icon' => 'fa-clock', 'tone' => 'amber'],
      ['label' => 'Completed', 'value' => $stats['completed'], 'icon' => 'fa-circle-check', 'tone' => 'green'],
      ['label' => 'Other Statuses', 'value' => $stats['other'], 'icon' => 'fa-list-check', 'tone' => 'slate']
    ] as $stat)
      <div class="tcmc-stat">
        <div class="tcmc-stat-top"><span class="tcmc-stat-icon {{ $stat['tone'] }}"><i class="fa-solid {{ $stat['icon'] }}"></i></span></div>
        <div class="tcmc-stat-value">{{ is_null($stat['value']) ? '—' : number_format($stat['value']) }}</div>
        <div class="tcmc-stat-label">{{ $stat['label'] }}</div>
      </div>
    @endforeach
  </div>

  <section class="tcmc-panel">
    <div class="tcmc-panel-heading">
      <h2>Grievance Operations</h2>
      <p>Open the relevant workspace to manage registrations, resolution and reporting.</p>
    </div>
    <div class="tcmc-quick-grid">
      <a href="{{ route('new_complaints') }}" class="tcmc-quick-link"><i class="fa-solid fa-clipboard-list"></i><strong>Complaint Register</strong><span>Review pending grievances</span></a>
      <a href="{{ route('new_complaints_com') }}" class="tcmc-quick-link"><i class="fa-solid fa-check-double"></i><strong>Resolved Complaints</strong><span>Review closed work</span></a>
      <a href="{{ route('departmentcomplaint') }}" class="tcmc-quick-link"><i class="fa-solid fa-building"></i><strong>Departments</strong><span>Department-level reports</span></a>
      <a href="{{ route('zonecomplaint') }}" class="tcmc-quick-link"><i class="fa-solid fa-map-location-dot"></i><strong>Zone Reports</strong><span>Understand area workloads</span></a>
      <a href="{{ route('wardwisereport') }}" class="tcmc-quick-link"><i class="fa-solid fa-map"></i><strong>Ward Reports</strong><span>Ward-wise grievance data</span></a>
      <a href="{{ route('escalation_report') }}" class="tcmc-quick-link"><i class="fa-solid fa-arrow-trend-up"></i><strong>Escalations</strong><span>Monitor overdue cases</span></a>
    </div>
  </section>

  <p class="tcmc-help-text">Figures above come from the PGP <code>new_complaints</code> table, not yet from the separate Smart City Tiruppur grievance database. Pending = status 0; completed = status 2; other statuses are grouped.</p>
</div>
@endsection

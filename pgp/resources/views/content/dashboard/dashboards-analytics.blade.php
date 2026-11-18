@extends('layouts/contentNavbarLayout')
@section('title', 'Municipal Dashboard')
@section('content')
<div class="tcmc-dashboard">
  <div class="tcmc-page-heading">
    <div>
      <p class="tcmc-kicker">Tiruppur City Municipal Corporation</p>
      <h1>Grievance Management</h1>
      <p>Welcome, {{ session('staff_name') ?: 'Municipal Staff' }}. The two datasets are shown separately until migration is verified.</p>
    </div>
    <span class="tcmc-date-label">{{ now()->format('d M Y') }}</span>
  </div>

  @foreach ($warnings as $warning)
    <div class="alert alert-warning" role="alert">{{ $warning }}</div>
  @endforeach

  @foreach ($sources as $key => $source)
    <section class="tcmc-source-summary">
      <div class="tcmc-source-heading">
        <h2>{{ $source['label'] }}</h2>
        <span>{{ $key === 'pgp' ? 'pgp.new_complaints' : 'smartcitytiruppur.complaints' }}</span>
      </div>
      <div class="tcmc-stats">
        @foreach ([
          ['label' => 'Total Complaints', 'value' => $source['total'], 'icon' => 'fa-folder-open', 'tone' => 'blue'],
          ['label' => 'Pending', 'value' => $source['pending'], 'icon' => 'fa-clock', 'tone' => 'amber'],
          ['label' => 'Resolved / Completed', 'value' => $source['completed'], 'icon' => 'fa-circle-check', 'tone' => 'green'],
          ['label' => 'Other Statuses', 'value' => $source['other'], 'icon' => 'fa-list-check', 'tone' => 'slate']
        ] as $stat)
          <div class="tcmc-stat">
            <div class="tcmc-stat-top"><span class="tcmc-stat-icon {{ $stat['tone'] }}"><i class="fa-solid {{ $stat['icon'] }}"></i></span></div>
            <div class="tcmc-stat-value">{{ is_null($stat['value']) ? '—' : number_format($stat['value']) }}</div>
            <div class="tcmc-stat-label">{{ $stat['label'] }}</div>
          </div>
        @endforeach
      </div>
    </section>
  @endforeach

  <section class="tcmc-panel">
    <div class="tcmc-panel-heading">
      <h2>PGP Grievance Operations</h2>
      <p>These modules still operate on the previous PGP database. Check your assigned menu permissions before use.</p>
    </div>
    <div class="tcmc-quick-grid">
      <a href="{{ route('new_complaints') }}" class="tcmc-quick-link"><i class="fa-solid fa-clipboard-list"></i><strong>Complaint Register</strong><span>Pending PGP grievances</span></a>
      <a href="{{ route('new_complaints_com') }}" class="tcmc-quick-link"><i class="fa-solid fa-check-double"></i><strong>Resolved Complaints</strong><span>PGP completed cases</span></a>
      <a href="{{ route('departmentcomplaint') }}" class="tcmc-quick-link"><i class="fa-solid fa-building"></i><strong>Department Reports</strong><span>PGP department figures</span></a>
      <a href="{{ route('zonecomplaint') }}" class="tcmc-quick-link"><i class="fa-solid fa-map-location-dot"></i><strong>Zone Reports</strong><span>PGP zone data</span></a>
      <a href="{{ route('wardwisereport') }}" class="tcmc-quick-link"><i class="fa-solid fa-map"></i><strong>Ward Reports</strong><span>PGP ward statistics</span></a>
      <a href="{{ route('escalation_report') }}" class="tcmc-quick-link"><i class="fa-solid fa-arrow-trend-up"></i><strong>Escalations</strong><span>PGP escalation records</span></a>
    </div>
  </section>
  <p class="tcmc-help-text">Counts exclude soft-deleted complaints in each source. PGP pending = 0, completed = 2. Smart City pending includes Registered, Assigned, Acknowledged, In Progress and Reopened; completed includes Resolved and Closed. Totals are deliberately not added together because the two databases may contain duplicate real-world complaints.</p>
</div>
@endsection

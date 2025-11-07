@extends('employer.layouts.base')
@section('title', 'Audit Logs - Job Portal Mandaluyong')

@php
  $pageTitle = 'AUDIT LOGS';
@endphp

@section('content')
  <div class="content-area">
    <div class="page-header">
      <h1 class="page-title"><i class="fas fa-clipboard-list"></i> Audit Logs</h1>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 class="card-title">Activity Logs</h2>
      </div>
      <div class="card-body">
        <p>Audit log content will be displayed here.</p>
      </div>
    </div>
  </div>
@endsection

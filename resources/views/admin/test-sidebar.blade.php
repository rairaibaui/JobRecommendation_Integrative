@extends('admin.layout')

@section('title', 'Test Sidebar - Admin Panel')

@section('content')
@php
    $pageTitle = 'TEST SIDEBAR';
@endphp

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-eye"></i>
        System Admin Sidebar Test
    </h1>
    <p class="page-subtitle">Testing the new System Admin sidebar layout and spacing</p>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-check-circle"></i> Sidebar Implementation Status</h2>
    </div>
    <div class="card-body">
        <p><strong>✅ System Admin sidebar is now implemented!</strong></p>
        <ul style="margin-left: 20px; margin-top: 10px;">
            <li>Navigation items in correct order</li>
            <li>Enhanced vertical spacing matching Employer sidebar</li>
            <li>System Admin button with shield icon</li>
            <li>Proper active states and hover effects</li>
        </ul>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-list"></i> Navigation Items</h2>
    </div>
    <div class="card-body">
        <ol style="margin-left: 20px;">
            <li><strong>System Admin</strong> (styled button)</li>
            <li><strong>Dashboard</strong></li>
            <li><strong>Analytics</strong></li>
            <li><strong>Verifications</strong></li>
            <li><strong>Users</strong></li>
            <li><strong>Audit Logs</strong></li>
            <li><strong>Logout</strong></li>
        </ol>
    </div>
</div>
@endsection
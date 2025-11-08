@extends('layouts.app')

@section('content')
<div style="max-width:700px;margin:40px auto;padding:20px;background:#fff;border-radius:8px;">
    <h2>Email Verification Required</h2>
    <p>We've sent an email with a verification link to <strong>{{ auth()->user()->email }}</strong>. Please click the link in that email to verify your account.</p>

    @if(session('resent'))
        <div style="padding:10px;background:#d1fae5;border-radius:6px;margin-bottom:12px;color:#065f46;">A fresh verification link has been sent to your email address.</div>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" style="padding:10px 14px;background:#5B9BD5;color:#fff;border:none;border-radius:6px;">Resend Verification Email</button>
    </form>

    <p style="margin-top:12px;color:#666;">If you don't see the email, check your spam folder or contact support.</p>
</div>
@endsection

@extends('jobseeker.layouts.base')

@php $pageTitle = 'Verification Link Expired'; @endphp

@section('title', 'Verification Link Expired')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Verification Link Expired</h1>
        <p class="page-subtitle">Your verification link has expired or is invalid.</p>
    </div>

    <div class="card">
        <div class="card-body" style="display:flex; justify-content:center;">
            <div style="width:100%; max-width:720px;">
                <div style="background: #fff; color: #2c3e50; padding:22px; border-radius:8px; box-shadow:0 6px 20px rgba(0,0,0,0.06);">
                    <h3 style="margin-top:0; margin-bottom:8px;">Verification Link Expired</h3>
                    <p style="margin-top:0; color:#4b5563;">The verification link you used is invalid or has expired. For your security, verification links expire after a short time. You can request a new verification email below.</p>

                    <form method="POST" action="{{ route('verification.resend.public') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <div style="margin-top:14px; display:flex; align-items:center; gap:12px;">
                            <button type="submit" class="btn btn-primary" style="padding:8px 14px;">Send a new verification email</button>
                            <a href="{{ route('login') }}" class="btn-link">Back to sign in</a>
                        </div>
                    </form>

                    <p style="color:#6b7280; font-size:13px; margin-top:14px;">Note: For security and to avoid spam, this action is rate limited. If you don't receive an email, check your spam folder or contact support.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

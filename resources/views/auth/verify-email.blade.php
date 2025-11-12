@extends('layouts.app')

@section('content')
<div class="card" style="max-width:820px;margin:28px auto;padding:22px;">
    <div style="display:flex;align-items:center;gap:18px;">
        <div style="flex:1;">
            <h2 style="margin:0 0 6px 0;">Verify your email</h2>
            <p style="margin:0;color:#555;">We've sent a verification message to <strong>{{ auth()->user()->email }}</strong>. Please verify your account to unlock employer features (posting jobs, uploading permits, etc.).</p>
        </div>

        <div style="display:flex;gap:10px;align-items:center">
            @if(! auth()->user()->hasVerifiedEmail())
                <button id="open-verify-modal" class="btn btn-primary" style="background:#2b6cb0;border-color:#2b6cb0;padding:8px 12px;">Verify Email</button>
            @else
                <span class="badge badge-success" style="background:#2f855a;color:#fff;padding:8px 12px;border-radius:6px;">Email Verified</span>
            @endif
        </div>
    </div>

    @if(session('resent'))
        <div style="margin-top:14px;padding:10px;background:#e6fffa;border-radius:6px;color:#0b7765;">A fresh verification link has been sent to your email address.</div>
    @endif

    <div style="margin-top:14px;color:#666;font-size:14px;">
        <p>If you don't receive the email within a few minutes, check your spam/junk folder. You can also resend the verification message from the modal.</p>
    </div>

    <!-- Verify modal (hidden by default) -->
    <div id="verify-modal" class="modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:1200;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:8px;max-width:640px;width:96%;padding:20px;box-shadow:0 8px 30px rgba(2,6,23,0.2);">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <h3 style="margin:0">Verify your email</h3>
                <button id="close-verify-modal" style="background:none;border:none;font-size:18px;">&times;</button>
            </div>

            <p style="color:#444;margin-top:12px;">We will send a verification link to <strong>{{ auth()->user()->email }}</strong>. Click the button below to send the email now. If you'd rather verify later, you can close this dialog.</p>

            <div id="verify-feedback" style="margin-top:8px;display:none;padding:10px;border-radius:6px"></div>

            <div style="margin-top:16px;display:flex;gap:10px;">
                <form id="resend-form" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button id="resend-button" type="submit" class="btn btn-primary" style="background:#2b6cb0;border-color:#2b6cb0;padding:10px 14px;">Send verification email</button>
                </form>

                <button id="close-verify-modal-2" class="btn btn-secondary" style="background:#edf2f7;border:1px solid #cbd5e1;padding:10px 14px;">Close</button>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
    (function(){
        const openBtn = document.getElementById('open-verify-modal');
        const modal = document.getElementById('verify-modal');
        const closeBtn = document.getElementById('close-verify-modal');
        const closeBtn2 = document.getElementById('close-verify-modal-2');
        const resendForm = document.getElementById('resend-form');
        const resendBtn = document.getElementById('resend-button');
        const feedback = document.getElementById('verify-feedback');

        function openModal(){ modal.style.display = 'flex'; }
        function closeModal(){ modal.style.display = 'none'; feedback.style.display='none'; }

        if(openBtn){ openBtn.addEventListener('click', function(e){ e.preventDefault(); openModal(); }); }
        if(closeBtn){ closeBtn.addEventListener('click', function(e){ e.preventDefault(); closeModal(); }); }
        if(closeBtn2){ closeBtn2.addEventListener('click', function(e){ e.preventDefault(); closeModal(); }); }

        // Prefer AJAX submission to avoid full page reload; gracefully fallback to form POST
        if(resendForm){
            resendForm.addEventListener('submit', function(e){
                e.preventDefault();
                if(!resendBtn) return;
                resendBtn.disabled = true;
                resendBtn.textContent = 'Sending...';

                const token = document.querySelector('input[name="_token"]')?.value;
                fetch(resendForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin'
                }).then(res => {
                    if(res.ok){
                        return res.json().catch(()=>({}));
                    }
                    throw new Error('Network error');
                }).then(data => {
                    feedback.style.display = 'block';
                    feedback.style.background = '#e6fffa';
                    feedback.style.color = '#065f46';
                    feedback.textContent = 'Verification email sent. Please check your inbox.';
                    resendBtn.textContent = 'Sent';
                }).catch(err => {
                    feedback.style.display = 'block';
                    feedback.style.background = '#fff5f5';
                    feedback.style.color = '#9b2c2c';
                    feedback.textContent = 'Failed to send verification email. Please try again or contact support.';
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Send verification email';
                });
            });
        }

        // Close modal on outside click
        window.addEventListener('click', function(e){ if(e.target === modal){ closeModal(); } });
    })();
</script>
@endsection

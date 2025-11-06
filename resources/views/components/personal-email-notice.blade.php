{{--
    Personal Email Verification Notice Component
    
    Displays an informational alert for employers using personal email addresses,
    explaining the higher verification confidence threshold (90% vs 80%).
    
    Usage:
        <x-personal-email-notice />
    
    Props:
        - dismissible (bool): Whether the alert can be closed. Default: true
        - compact (bool): Use compact styling. Default: false
--}}

@props(['dismissible' => true, 'compact' => false])

@php
    // Auto-detect personal email domains
    $userEmail = Auth::user()->email ?? '';
    $isPersonalEmail = preg_match('/@(gmail|yahoo|hotmail|outlook|live|aol|icloud)\./i', $userEmail);
@endphp

@if($isPersonalEmail)
    <div 
        id="personalEmailNotice" 
        class="personal-email-notice {{ $compact ? 'compact' : '' }}"
        role="alert"
        style="
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: {{ $compact ? '10px 14px' : '14px 18px' }};
            border-radius: 10px;
            margin-bottom: 16px;
            display: flex;
            align-items: {{ $compact ? 'center' : 'start' }};
            gap: 12px;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
            position: relative;
            animation: slideDown 0.4s ease-out;
        "
    >
        {{-- Icon --}}
        <i class="fas fa-shield-alt" style="font-size: {{ $compact ? '18px' : '22px' }}; flex-shrink: 0; margin-top: {{ $compact ? '0' : '2px' }};"></i>
        
        {{-- Content --}}
        <div style="flex: 1; line-height: 1.5;">
            <strong style="display: block; margin-bottom: {{ $compact ? '2px' : '4px' }}; font-size: {{ $compact ? '14px' : '15px' }};">
                <i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i>
                Higher Verification Standards
            </strong>
            <p style="margin: 0; font-size: {{ $compact ? '12px' : '13px' }}; opacity: 0.95;">
                Personal email addresses (e.g., <code style="background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 3px; font-size: {{ $compact ? '11px' : '12px' }};">{{ $userEmail }}</code>) 
                require a <strong>higher verification confidence (90%)</strong> compared to business emails (80%). 
                Please upload a <strong>clear, high-quality business permit</strong> to avoid manual review delays.
            </p>
        </div>

        {{-- Dismissible Close Button --}}
        @if($dismissible)
            <button 
                onclick="dismissPersonalEmailNotice()" 
                style="
                    background: rgba(255, 255, 255, 0.2);
                    border: none;
                    color: #fff;
                    width: 28px;
                    height: 28px;
                    border-radius: 50%;
                    cursor: pointer;
                    font-size: 18px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                    transition: all 0.2s ease;
                    padding: 0;
                    line-height: 1;
                "
                onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                onmouseout="this.style.background='rgba(255,255,255,0.2)'"
                aria-label="Dismiss notice"
                title="Dismiss this notice"
            >
                &times;
            </button>
        @endif
    </div>

    {{-- Inline Styles & Animation --}}
    <style>
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .personal-email-notice code {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        .personal-email-notice.compact {
            margin-bottom: 12px;
        }

        /* Dark mode support (auto-adjusts based on system preference) */
        @media (prefers-color-scheme: dark) {
            .personal-email-notice {
                box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .personal-email-notice {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .personal-email-notice button {
                position: absolute;
                top: 10px;
                right: 10px;
            }
        }
    </style>

    {{-- Dismiss functionality with localStorage persistence --}}
    <script>
        function dismissPersonalEmailNotice() {
            const notice = document.getElementById('personalEmailNotice');
            if (notice) {
                // Fade out animation
                notice.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                notice.style.opacity = '0';
                notice.style.transform = 'translateY(-10px)';
                
                // Remove from DOM after animation
                setTimeout(() => {
                    notice.remove();
                }, 300);

                // Save dismissal state to localStorage (persists for 7 days)
                const dismissKey = 'personalEmailNotice_dismissed_{{ md5($userEmail) }}';
                const expiryDate = new Date();
                expiryDate.setDate(expiryDate.getDate() + 7); // 7 days
                localStorage.setItem(dismissKey, expiryDate.toISOString());
            }
        }

        // Check if notice was previously dismissed and still within expiry
        document.addEventListener('DOMContentLoaded', function() {
            const notice = document.getElementById('personalEmailNotice');
            if (notice) {
                const dismissKey = 'personalEmailNotice_dismissed_{{ md5($userEmail) }}';
                const dismissedUntil = localStorage.getItem(dismissKey);
                
                if (dismissedUntil) {
                    const expiryDate = new Date(dismissedUntil);
                    const now = new Date();
                    
                    if (now < expiryDate) {
                        // Still dismissed - hide the notice
                        notice.style.display = 'none';
                    } else {
                        // Expired - clear localStorage
                        localStorage.removeItem(dismissKey);
                    }
                }
            }
        });
    </script>
@endif

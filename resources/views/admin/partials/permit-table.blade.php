<table class="table">
    <thead>
        <tr>
            <th>Employer</th>
            <th>Status</th>
            <th>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-brain" style="color: #648EB5; font-size: 14px;"></i>
                    <span>AI Confidence</span>
                    <div class="tooltip" style="display: inline;">
                        <i class="fas fa-info-circle" style="color: #648EB5; font-size: 12px; cursor: help;"></i>
                        <span class="tooltip-text" style="width: 320px; left: -160px;">
                            <div style="margin-bottom: 12px;">
                                <strong style="color: #648EB5; font-size: 14px;">
                                    <i class="fas fa-brain"></i> Custom AI Vision Model
                                </strong>
                                <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
                                    100% Training Accuracy • < 2s Inference Time
                                </div>
                            </div>
                            <div style="border-top: 1px solid #334155; padding-top: 12px; margin-top: 12px;">
                                <strong style="color: #10b981;">Detecting:</strong>
                                <div style="margin-top: 8px; font-size: 12px; line-height: 1.8;">
                                    <div style="color: #86efac;">✓ Mandaluyong City logo</div>
                                    <div style="color: #86efac;">✓ Business permit titles</div>
                                    <div style="color: #86efac;">✓ Business details & nature</div>
                                    <div style="color: #86efac;">✓ Business addresses</div>
                                    <div style="color: #86efac;">✓ Owner/applicant names</div>
                                    <div style="color: #86efac;">✓ Issued dates</div>
                                    <div style="color: #86efac;">✓ Government signatures</div>
                                </div>
                            </div>
                        </span>
                    </div>
                </div>
            </th>
            <th>Document Type</th>
            <th>Expiry Date</th>
            <th>Submitted</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($permits as $permit)
            <tr>
                <td>
                    <div class="user-info">
                        <div class="user-avatar">
                            {{ strtoupper(substr($permit->user->company_name ?? $permit->user->email, 0, 1)) }}
                        </div>
                        <div class="user-details">
                            <div class="user-name">{{ $permit->user->company_name ?? 'N/A' }}</div>
                            <div class="user-email">{{ $permit->user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @php
                        $status = $permit->validation_status ?? 'pending_review';
                        $statusClass = match($status) {
                            'approved' => 'status-approved',
                            'rejected' => 'status-rejected',
                            'pending_review' => 'status-needs-review',
                            default => 'status-pending'
                        };
                        $statusIcon = match($status) {
                            'approved' => 'fa-check-circle',
                            'rejected' => 'fa-times-circle',
                            'pending_review' => 'fa-clock',
                            default => 'fa-clock'
                        };
                        $aiAnalysis = is_string($permit->ai_analysis) ? json_decode($permit->ai_analysis, true) : $permit->ai_analysis;
                        $reason = $permit->reason ?? ($aiAnalysis['reason'] ?? null);
                        $validatedBy = $permit->validated_by ?? null;
                        $isAiValidated = $validatedBy === 'ai';
                        $confidence = $permit->confidence_score ?? 0;
                        $detectedElements = $aiAnalysis['detected_elements'] ?? [];
                        $missingElements = $aiAnalysis['missing_elements'] ?? [];
                        $aiModel = $aiAnalysis['ai_model_used'] ?? 'Custom AI Vision Model';
                        
                        // Build comprehensive tooltip for pending review
                        // Show tooltip if status is pending_review and there's any AI data or reason
                        $hasDetailedAiAnalysis = $status === 'pending_review' && (
                            count($detectedElements) > 0 || 
                            count($missingElements) > 0 || 
                            $confidence > 0 || 
                            !empty($reason) ||
                            !empty($aiAnalysis)
                        );
                    @endphp
                    <div class="status-container" style="position: relative;">
                        <div class="tooltip" style="position: relative; z-index: 1001;">
                            <div class="status-badge-enhanced {{ $statusClass }}">
                                <div class="badge-icon-wrapper">
                                    <i class="fas {{ $statusIcon }}"></i>
                                </div>
                                <div class="badge-content">
                                    <span class="badge-text">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                    @if($isAiValidated && $confidence > 0)
                                        <span class="badge-subtext">
                                            <i class="fas fa-brain" style="font-size:9px;"></i>
                                            AI: {{ $confidence }}%
                                        </span>
                                    @elseif($validatedBy === 'admin')
                                        <span class="badge-subtext">
                                            <i class="fas fa-user-shield" style="font-size:9px;"></i>
                                            Admin
                                        </span>
                                    @elseif($status === 'pending_review' && $confidence > 0)
                                        <span class="badge-subtext">
                                            <i class="fas fa-brain" style="font-size:9px;"></i>
                                            AI: {{ $confidence }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if($hasDetailedAiAnalysis)
                                <div class="tooltip-text-enhanced" style="width: 400px;">
                                    <div style="margin-bottom: 12px;">
                                        <strong style="color: #648EB5; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-brain"></i> {{ $aiModel }}
                                        </strong>
                                        <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
                                            100% Training Accuracy • < 2s Inference Time
                                        </div>
                                    </div>
                                    
                                    @if($reason)
                                        <div style="margin-bottom: 12px; padding: 10px; background: rgba(100, 142, 181, 0.1); border-radius: 6px; border-left: 3px solid #648EB5;">
                                            <strong style="color: #e2e8f0; font-size: 12px; display: block; margin-bottom: 6px;">
                                                <i class="fas fa-info-circle"></i> AI Analysis:
                                            </strong>
                                            <div style="color: #cbd5e1; font-size: 12px; line-height: 1.6;">
                                                {{ $reason }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div style="border-top: 1px solid #475569; padding-top: 12px; margin-top: 12px;">
                                        @if($confidence > 0)
                                            <div style="margin-bottom: 10px;">
                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                                    <strong style="color: #e2e8f0; font-size: 12px;">Overall Confidence:</strong>
                                                    <span style="color: #10b981; font-size: 13px; font-weight: 700;">{{ $confidence }}%</span>
                                                </div>
                                                <div style="width: 100%; height: 8px; background: #334155; border-radius: 4px; overflow: hidden;">
                                                    <div style="height: 100%; background: linear-gradient(90deg, #10b981 0%, #059669 100%); width: {{ $confidence }}%; border-radius: 4px;"></div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if(count($detectedElements) > 0)
                                            <div style="margin-bottom: 10px;">
                                                <strong style="color: #10b981; font-size: 12px; display: flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                                                    <i class="fas fa-check-circle"></i> Detected Elements ({{ count($detectedElements) }}):
                                                </strong>
                                                <div style="font-size: 11px; line-height: 1.8; color: #86efac;">
                                                    @foreach($detectedElements as $element)
                                                        <div style="display: flex; align-items: center; gap: 6px;">
                                                            <span style="color: #10b981;">✓</span>
                                                            <span>{{ ucwords(str_replace('_', ' ', $element)) }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if(count($missingElements) > 0)
                                            <div>
                                                <strong style="color: #ef4444; font-size: 12px; display: flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                                                    <i class="fas fa-exclamation-circle"></i> Missing Elements ({{ count($missingElements) }}):
                                                </strong>
                                                <div style="font-size: 11px; line-height: 1.8; color: #fca5a5;">
                                                    @foreach($missingElements as $element)
                                                        <div style="display: flex; align-items: center; gap: 6px;">
                                                            <span style="color: #ef4444;">✗</span>
                                                            <span>{{ ucwords(str_replace('_', ' ', $element)) }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if(count($detectedElements) === 0 && count($missingElements) === 0 && !$reason)
                                            <div style="color: #94a3b8; font-size: 11px; font-style: italic;">
                                                AI analysis in progress or unavailable
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="tooltip-text">
                                    @if($status === 'pending_review')
                                        {{ $reason ?: 'This permit is waiting for administrator review. AI analysis may be in progress.' }}
                                    @else
                                        @php
                                            $tooltipText = match($status) {
                                                'approved' => 'This business permit has been approved. The employer can post jobs and hire applicants.',
                                                'rejected' => 'This permit was rejected. The employer must upload a valid business permit to continue.',
                                                default => 'Status unknown'
                                            };
                                        @endphp
                                        {{ $tooltipText }}
                                    @endif
                                </span>
                            @endif
                        </div>

                        @php
                            // Only show AI Analysis box if there's meaningful analysis
                            // Hide generic "awaiting review" messages that are redundant with status badge
                            $genericMessages = [
                                'uploaded by employer',
                                'awaiting ai',
                                'awaiting manual review',
                                'awaiting ai/manual review',
                                'waiting for review'
                            ];
                            $reasonLower = strtolower($reason ?? '');
                            $isGenericMessage = false;
                            foreach ($genericMessages as $generic) {
                                if (str_contains($reasonLower, $generic)) {
                                    $isGenericMessage = true;
                                    break;
                                }
                            }
                            
                            // Show AI Analysis box only if there's a meaningful reason (not generic)
                            // OR if there's actual analysis data (detected/missing elements)
                            $hasMeaningfulReason = $reason && !$isGenericMessage;
                            $hasAnalysisData = count($detectedElements) > 0 || count($missingElements) > 0;
                            $shouldShowAnalysis = $hasMeaningfulReason || ($hasAnalysisData && $reason);
                        @endphp
                        @if($shouldShowAnalysis)
                            <div style="margin-top:6px; font-size:11px; max-width:100%;">
                                @php
                                    $isAiUnavailable = str_contains(strtolower($reason), 'ai validation is not available');
                                @endphp
                                @if($isAiUnavailable)
                                    <div style="background:linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-left:3px solid #f59e0b; padding:8px 10px; border-radius:6px; box-shadow:0 2px 4px rgba(245,158,11,0.1);">
                                        <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                                            <i class="fas fa-exclamation-triangle" style="font-size:11px; color:#d97706; flex-shrink:0;"></i>
                                            <strong style="font-weight:600; font-size:11px; color:#92400e;">AI Unavailable</strong>
                                        </div>
                                        <span style="font-size:10px; line-height:1.4; color:#78350f; word-wrap:break-word;">Requires manual admin review</span>
                                    </div>
                                @else
                                    <div style="background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-left:3px solid #648EB5; padding:8px 10px; border-radius:6px; color:#64748b; box-shadow:0 2px 4px rgba(100,142,181,0.1); max-width:100%;">
                                        <div style="display:flex; align-items:start; gap:6px;">
                                            <i class="fas fa-info-circle" style="font-size:11px; color:#648EB5; margin-top:2px; flex-shrink:0;"></i>
                                            <div style="min-width:0; flex:1;">
                                                <strong style="font-weight:600; color:#506B81; display:block; margin-bottom:3px; font-size:10px;">AI Analysis:</strong>
                                                <span style="font-size:10px; line-height:1.4; word-wrap:break-word; overflow-wrap:break-word; display:block;">{{ \Illuminate\Support\Str::limit($reason, 80) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </td>
                <td>
                    @php
                        $confidence = $permit->confidence_score ?? 0;
                        $confidenceClass = $confidence >= 80 ? 'high' : ($confidence >= 50 ? 'medium' : 'low');
                        $aiAnalysis = is_string($permit->ai_analysis) ? json_decode($permit->ai_analysis, true) : $permit->ai_analysis;
                        $detectedElements = $aiAnalysis['detected_elements'] ?? [];
                        $missingElements = $aiAnalysis['missing_elements'] ?? [];
                        $aiModel = $aiAnalysis['ai_model_used'] ?? 'Custom AI Vision Model';
                        $totalElements = count($detectedElements) + count($missingElements);
                        $detectionRate = $totalElements > 0 ? round((count($detectedElements) / $totalElements) * 100) : 0;
                    @endphp
                    <div class="ai-analysis-container">
                        <div class="tooltip" style="position: relative;">
                            <div class="ai-score-enhanced">
                                <div class="ai-confidence-card">
                                    <div class="confidence-header">
                                        <i class="fas fa-brain" style="color: #648EB5; font-size: 16px;"></i>
                                        <span class="confidence-label">AI Confidence</span>
                                    </div>
                                    <div class="score-bar-enhanced" style="position: relative;">
                                        @if($confidence > 0)
                                            <div class="score-fill-enhanced {{ $confidenceClass }}" style="width: {{ $confidence }}%;">
                                                @if($confidence > 8)
                                                    <span class="score-percentage">{{ $confidence }}%</span>
                                                @endif
                                            </div>
                                        @endif
                                        @if($confidence <= 8)
                                            <span class="score-percentage" style="position: absolute; left: 4px; top: 50%; transform: translateY(-50%); color: #64748b; text-shadow: none; font-size: 10px; font-weight: 600; z-index: 1;">{{ $confidence }}%</span>
                                        @endif
                                    </div>
                                    <div class="detection-summary">
                                        @if(count($detectedElements) > 0)
                                            <div class="detection-stat">
                                                <span class="stat-icon detected">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                                <span class="stat-text">{{ count($detectedElements) }} Detected</span>
                                            </div>
                                        @endif
                                        @if(count($missingElements) > 0)
                                            <div class="detection-stat">
                                                <span class="stat-icon missing">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                </span>
                                                <span class="stat-text">{{ count($missingElements) }} Missing</span>
                                            </div>
                                        @endif
                                        @if(count($detectedElements) === 0 && count($missingElements) === 0)
                                            <div class="detection-stat">
                                                <span class="stat-icon" style="background: #e2e8f0; color: #94a3b8;">
                                                    <i class="fas fa-info-circle"></i>
                                                </span>
                                                <span class="stat-text" style="color: #94a3b8;">No analysis data</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="tooltip-text">
                                <div style="margin-bottom: 12px;">
                                    <strong style="color: #648EB5; font-size: 14px;">
                                        <i class="fas fa-brain"></i> {{ $aiModel }}
                                    </strong>
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
                                        100% Training Accuracy • < 2s Inference Time
                                    </div>
                                </div>
                                <div style="border-top: 1px solid #334155; padding-top: 12px; margin-top: 12px;">
                                    <div style="margin-bottom: 8px;">
                                        <strong style="color: #10b981;">Overall Confidence: {{ $confidence }}%</strong>
                                    </div>
                                    @if($detectedElements)
                                        <div style="margin-bottom: 8px;">
                                            <strong style="color: #10b981;">
                                                <i class="fas fa-check-circle"></i> Detected Elements ({{ count($detectedElements) }}):
                                            </strong>
                                            <div style="margin-top: 4px; font-size: 12px; line-height: 1.6;">
                                                @foreach($detectedElements as $element)
                                                    <div style="color: #86efac;">
                                                        • {{ ucwords(str_replace('_', ' ', $element)) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if($missingElements)
                                        <div>
                                            <strong style="color: #ef4444;">
                                                <i class="fas fa-exclamation-circle"></i> Missing Elements ({{ count($missingElements) }}):
                                            </strong>
                                            <div style="margin-top: 4px; font-size: 12px; line-height: 1.6;">
                                                @foreach($missingElements as $element)
                                                    <div style="color: #fca5a5;">
                                                        • {{ ucwords(str_replace('_', ' ', $element)) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </span>
                        </div>
                    </div>
                </td>
                <td>
                    @php
                        $typeLabels = [
                            'MAYORS_PERMIT' => "Mayor's Permit",
                            'BARANGAY_CLEARANCE' => 'Barangay Clearance',
                            'BARANGAY_LOCATIONAL_CLEARANCE' => 'Barangay Locational Clearance',
                            'DTI' => 'DTI Certificate',
                            'BUSINESS_PERMIT' => 'Business Permit',
                            'UNKNOWN' => 'Not specified',
                        ];

                        $aiAnalysis = is_string($permit->ai_analysis) ? json_decode($permit->ai_analysis, true) : $permit->ai_analysis;
                        // Prefer the saved document_type on the record, fallback to AI analysis
                        $rawType = $permit->document_type ?? ($aiAnalysis['document_type'] ?? 'UNKNOWN');
                        $key = strtoupper((string) $rawType);
                        $docLabel = $typeLabels[$key] ?? 'Not specified';
                        $isAiDetected = isset($aiAnalysis['document_type']) && $aiAnalysis['document_type'] === $rawType;
                    @endphp
                    <div style="display: flex; align-items: center; gap: 6px; max-width: 100%;">
                        <i class="fas fa-file-certificate" style="color: #648EB5; font-size: 13px; flex-shrink: 0;"></i>
                        <div style="min-width: 0; flex: 1;">
                            <span style="font-size: 12px; font-weight: 600; color: #334155; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $docLabel }}</span>
                            @if($isAiDetected)
                                <span style="font-size: 9px; color: #64748b; display: flex; align-items: center; gap: 3px; margin-top: 2px; white-space: nowrap;">
                                    <i class="fas fa-brain" style="font-size: 8px; color: #648EB5;"></i>
                                    AI Detected
                                </span>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    @php
                        $expiryDateRaw = $permit->permit_expiry_date;
                        $inferred = false;
                        // Prepare text sources to search for 'mandaluyong'
                        $aiText = '';
                        if (is_string($permit->ai_analysis)) {
                            $decoded = json_decode($permit->ai_analysis, true);
                        } else {
                            $decoded = is_array($permit->ai_analysis) ? $permit->ai_analysis : null;
                        }
                        if (is_array($decoded)) {
                            // flatten nested arrays and collect scalar values only to avoid Array to string conversion
                            $flatten = function($arr) use (&$flatten) {
                                $out = [];
                                foreach ($arr as $v) {
                                    if (is_array($v)) {
                                        $out = array_merge($out, $flatten($v));
                                    } elseif (is_scalar($v)) {
                                        $out[] = (string) $v;
                                    }
                                }
                                return $out;
                            };
                            $aiText = implode(' ', $flatten($decoded));
                        }
                        $combinedText = strtolower(trim(($aiText ?? '') . ' ' . ($permit->ocr_text ?? '') . ' ' . ($permit->raw_text ?? '') . ' ' . ($permit->reason ?? '')));
                        if (!$expiryDateRaw && strpos($combinedText, 'mandaluyong') !== false) {
                            $expiryDateRaw = \Carbon\Carbon::create(2025,12,31);
                            $inferred = true;
                        }

                        if ($expiryDateRaw) {
                            $expiryDate = is_string($expiryDateRaw) ? \Carbon\Carbon::parse($expiryDateRaw) : $expiryDateRaw;
                            $isExpiringSoon = $expiryDate->diffInDays(now()) <= 30 && $expiryDate->isFuture();
                            $isExpired = $expiryDate->isPast();
                        }
                    @endphp

                    @if(isset($expiryDate))
                        <div style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap;">
                            <span style="color: {{ $isExpired ? '#ef4444' : ($isExpiringSoon ? '#f59e0b' : '#64748b') }}; font-size: 12px; white-space: nowrap;">
                                {{ $expiryDate->format('M d, Y') }}
                            </span>
                            @if($inferred)
                                <small style="color:#94a3b8; font-size: 10px; white-space: nowrap;">(inferred)</small>
                            @endif
                            @if($isExpired)
                                <i class="fas fa-exclamation-triangle" title="Expired" style="font-size: 11px; color: #ef4444;"></i>
                            @elseif($isExpiringSoon)
                                <i class="fas fa-clock" title="Expiring Soon" style="font-size: 11px; color: #f59e0b;"></i>
                            @endif
                        </div>
                    @else
                        <span style="color: #94a3b8; font-size: 12px;">Not specified</span>
                    @endif
                </td>
                <td>
                    <span style="font-size: 12px; color: #64748b; white-space: nowrap;">{{ $permit->created_at->diffForHumans() }}</span>
                </td>
                <td>
                    <div class="action-buttons" style="flex-wrap: wrap; gap: 6px;">
                        <a href="{{ route('admin.verifications.file', $permit->id) }}"
                           class="btn-action btn-view"
                           target="_blank"
                           title="View Permit Document"
                           style="padding: 6px 10px; font-size: 11px;">
                            <i class="fas fa-eye"></i>
                            View
                        </a>
                        @if($status !== 'approved')
                            <button class="btn-action btn-approve"
                                    title="Approve this permit"
                                    onclick="showApproveModal({{ $permit->id }}, '{{ $permit->user->company_name ?? '' }}')"
                                    style="padding: 6px 10px; font-size: 11px;">
                                <i class="fas fa-check"></i>
                                Approve
                            </button>
                        @endif
                        @if($status !== 'rejected')
                            <button class="btn-action btn-reject"
                                    title="Reject this permit"
                                    onclick="showPermitRejectModal({{ $permit->id }}, '{{ $permit->user->company_name ?? '' }}')"
                                    style="padding: 6px 10px; font-size: 11px;">
                                <i class="fas fa-times"></i>
                                Reject
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="fas fa-certificate"></i>
                        <h3>No Business Permits Found</h3>
                        <p>There are no business permits matching your current filters.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Approve Modal -->
<div id="approveModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 15px; padding: 30px; max-width: 500px; width: 90%;">
        <h2 style="margin-bottom: 20px; color: #1e293b;">Approve Business Permit</h2>
        <p style="margin-bottom: 20px; color: #64748b;">Approving permit for <strong id="approveCompanyName"></strong>:</p>
        <form id="approveForm" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Permit Expiry Date (Optional)</label>
                <input
                    type="date"
                    name="permit_expiry_date"
                    min="{{ date('Y-m-d') }}"
                    style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit;"
                >
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Admin Notes (Optional)</label>
                <textarea
                    name="admin_notes"
                    style="width: 100%; min-height: 80px; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit;"
                    placeholder="Any additional notes or comments..."
                ></textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeApproveModal()" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary" style="background: #10b981;">
                    <i class="fas fa-check"></i>
                    Approve Permit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="permitRejectModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 15px; padding: 30px; max-width: 500px; width: 90%;">
        <h2 style="margin-bottom: 20px; color: #1e293b;">Reject Business Permit</h2>
        <p style="margin-bottom: 20px; color: #64748b;">Please provide a reason for rejecting <strong id="rejectCompanyName"></strong>'s business permit:</p>
        <form id="permitRejectForm" method="POST">
            @csrf
            <textarea
                name="rejection_reason"
                required
                style="width: 100%; min-height: 120px; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit; margin-bottom: 20px;"
                placeholder="e.g., Document is unclear/blurry, permit has expired, not a valid business permit, etc."
            ></textarea>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closePermitRejectModal()" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary" style="background: #ef4444;">
                    <i class="fas fa-times"></i>
                    Reject Permit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showApproveModal(permitId, companyName) {
        document.getElementById('approveCompanyName').textContent = companyName;
        document.getElementById('approveForm').action = '/admin/verifications/' + permitId + '/approve';
        document.getElementById('approveModal').style.display = 'flex';
    }

    function closeApproveModal() {
        document.getElementById('approveModal').style.display = 'none';
    }

    function showPermitRejectModal(permitId, companyName) {
        document.getElementById('rejectCompanyName').textContent = companyName;
        document.getElementById('permitRejectForm').action = '/admin/verifications/' + permitId + '/reject';
        document.getElementById('permitRejectModal').style.display = 'flex';
    }

    function closePermitRejectModal() {
        document.getElementById('permitRejectModal').style.display = 'none';
    }

    // Close modals when clicking outside
    document.getElementById('approveModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeApproveModal();
    });

    document.getElementById('permitRejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closePermitRejectModal();
    });

    // Enhanced tooltip positioning to prevent clipping (only for status badge tooltips)
    document.addEventListener('DOMContentLoaded', function() {
        // Only target tooltips within status-container (status badge tooltips)
        const statusTooltips = document.querySelectorAll('.status-container .tooltip');
        
        statusTooltips.forEach(function(tooltip) {
            const tooltipText = tooltip.querySelector('.tooltip-text-enhanced');
            if (!tooltipText) return;
            
            function positionTooltip() {
                const rect = tooltip.getBoundingClientRect();
                
                // Position tooltip above the badge using fixed positioning
                tooltipText.style.position = 'fixed';
                tooltipText.style.left = (rect.left + rect.width / 2) + 'px';
                tooltipText.style.top = (rect.top - 10) + 'px';
                tooltipText.style.transform = 'translate(-50%, -100%)';
                tooltipText.style.zIndex = '10000';
                tooltipText.style.marginBottom = '10px';
            }
            
            tooltip.addEventListener('mouseenter', function(e) {
                // Small delay to ensure CSS visibility is applied
                setTimeout(function() {
                    if (tooltipText.style.visibility !== 'hidden') {
                        positionTooltip();
                    }
                }, 10);
            });
            
            // Reposition on scroll or resize
            window.addEventListener('scroll', function() {
                if (tooltip.matches(':hover')) {
                    positionTooltip();
                }
            });
            
            window.addEventListener('resize', function() {
                if (tooltip.matches(':hover')) {
                    positionTooltip();
                }
            });
        });

        // Fix AI Confidence tooltip positioning
        const aiConfidenceTooltips = document.querySelectorAll('.ai-analysis-container .tooltip');
        
        aiConfidenceTooltips.forEach(function(tooltip) {
            const tooltipText = tooltip.querySelector('.tooltip-text');
            if (!tooltipText) return;
            
            function positionAiTooltip() {
                const rect = tooltip.getBoundingClientRect();
                const tooltipRect = tooltipText.getBoundingClientRect();
                
                // Position tooltip above the confidence card using fixed positioning
                tooltipText.style.position = 'fixed';
                tooltipText.style.left = (rect.left + rect.width / 2) + 'px';
                tooltipText.style.top = (rect.top - 10) + 'px';
                tooltipText.style.transform = 'translate(-50%, -100%)';
                tooltipText.style.zIndex = '10001';
                tooltipText.style.marginBottom = '10px';
                
                // Adjust if tooltip would go off screen
                const tooltipWidth = tooltipRect.width || 350;
                const leftPos = rect.left + rect.width / 2;
                
                if (leftPos - tooltipWidth / 2 < 10) {
                    tooltipText.style.left = (tooltipWidth / 2 + 10) + 'px';
                    tooltipText.style.transform = 'translate(-50%, -100%)';
                } else if (leftPos + tooltipWidth / 2 > window.innerWidth - 10) {
                    tooltipText.style.left = (window.innerWidth - tooltipWidth / 2 - 10) + 'px';
                    tooltipText.style.transform = 'translate(-50%, -100%)';
                }
            }
            
            tooltip.addEventListener('mouseenter', function(e) {
                setTimeout(function() {
                    if (tooltipText.style.visibility !== 'hidden') {
                        positionAiTooltip();
                    }
                }, 10);
            });
            
            // Reposition on scroll or resize
            window.addEventListener('scroll', function() {
                if (tooltip.matches(':hover')) {
                    positionAiTooltip();
                }
            });
            
            window.addEventListener('resize', function() {
                if (tooltip.matches(':hover')) {
                    positionAiTooltip();
                }
            });
        });
    });
</script>

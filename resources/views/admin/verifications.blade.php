<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System Administrator - Business Permit Verifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            color: #1e293b;
        }
        
        /* Top Navigation */
        .top-nav {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 32px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        
        .nav-left {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        
        .admin-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 20px;
            color: #0f172a;
        }
        
        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #648EB5 0%, #334A5E 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
        }
        
        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }
        
        .profile-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #648EB5 0%, #334A5E 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 15px;
        }
        
        .profile-info {
            display: flex;
            flex-direction: column;
        }
        
        .profile-name {
            font-weight: 600;
            font-size: 14px;
            color: #0f172a;
        }
        
        .profile-role {
            font-size: 12px;
            color: #64748b;
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            background: transparent;
            border: 1.5px solid #dc3545;
            border-radius: 10px;
            color: #dc3545;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
            transform: translateY(-1px);
        }
        
        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px;
        }
        
        .page-header {
            margin-bottom: 32px;
        }
        
        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }
        
        .page-subtitle {
            color: #64748b;
            font-size: 15px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        
        .alert i {
            font-size: 18px;
        }
        
        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        
        .stat-card {
            background: white;
            padding: 26px;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }
        
        .stat-card.pending { --card-accent: #ffc107; }
        .stat-card.approved { --card-accent: #43A047; }
        .stat-card.rejected { --card-accent: #dc3545; }
        .stat-card.ai { --card-accent: #648EB5; }
    .stat-card.expiring { --card-accent: #ffc107; }
        
        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
        }
        
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        
        .stat-card.pending .stat-icon { background: linear-gradient(135deg, #ffd54f, #ffc107); }
        .stat-card.approved .stat-icon { background: linear-gradient(135deg, #66bb6a, #43A047); }
        .stat-card.rejected .stat-icon { background: linear-gradient(135deg, #e57373, #dc3545); }
        .stat-card.ai .stat-icon { background: linear-gradient(135deg, #648EB5, #334A5E); }
    .stat-card.expiring .stat-icon { background: linear-gradient(135deg, #ffd54f, #ffc107); }
        
        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 40px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 8px;
        }
        
        .stat-desc {
            font-size: 13px;
            color: #94a3b8;
        }
        
        /* AI Detection Toggle */
        .ai-toggle-card {
            background: linear-gradient(135deg, #648EB5 0%, #334A5E 100%);
            color: white;
            padding: 24px;
            border-radius: 14px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(100, 142, 181, 0.25);
        }
        
        .ai-toggle-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        
        .ai-toggle-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 16px;
        }
        
        .toggle-switch {
            position: relative;
            width: 56px;
            height: 30px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.3);
            transition: 0.3s;
            border-radius: 30px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 3px;
            bottom: 3px;
            background: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background: rgba(16, 185, 129, 0.9);
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        .ai-toggle-desc {
            font-size: 13px;
            opacity: 0.95;
            line-height: 1.6;
        }
        
        /* Verifications Table */
        .verifications-section {
            background: white;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        
        .section-header {
            padding: 24px 28px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: #f59e0b;
        }

        .notif-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            height: 22px;
            padding: 0 8px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            margin-left: 8px;
            cursor: pointer;
            user-select: none;
        }

        .notif-badge.yellow { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .notif-badge.red { background: #f8d7da; color: #842029; border: 1px solid #dc3545; }

        .notif-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #334155;
            text-decoration: none;
            font-weight: 700;
        }
        
        /* Filters Section */
        .filters-section {
            padding: 24px 28px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .filters-form {
            display: flex;
            gap: 16px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .filter-group label i {
            font-size: 12px;
            color: #64748b;
        }
        
        .filter-select,
        .filter-input {
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            background: white;
            color: #1e293b;
            transition: all 0.2s;
        }
        
        .filter-select:focus,
        .filter-input:focus {
            outline: none;
            border-color: #648EB5;
            box-shadow: 0 0 0 3px rgba(100, 142, 181, 0.15);
        }
        
        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .btn-apply,
        .btn-clear {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            border: none;
            font-family: 'Inter', sans-serif;
        }
        
        .btn-apply {
            background: linear-gradient(135deg, #648EB5 0%, #334A5E 100%);
            color: white;
        }
        
        .btn-apply:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(100, 142, 181, 0.4);
        }
        
        .btn-clear {
            background: white;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }
        
        .btn-clear:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }
        
        .active-filters {
            margin-top: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
        }
        
        .active-filter-badge {
            padding: 6px 12px;
            background: white;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13px;
            color: #475569;
            font-weight: 500;
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #f8fafc;
        }
        
        th {
            text-align: left;
            padding: 18px 20px;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        td {
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        
        tbody tr {
            transition: all 0.2s;
        }
        
        tbody tr:hover {
            background: #f8fafc;
        }
        
        tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge.ai-verified {
            background: #e7f0f7;
            color: #334A5E;
        }
        
        .badge.system-flagged {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge.duplicate {
            background: #f8d7da;
            color: #842029;
        }
        
        /* Company Info */
        .company-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .company-name {
            font-weight: 600;
            color: #0f172a;
        }
        
        .company-address {
            font-size: 13px;
            color: #64748b;
        }
        
        /* Action Buttons */
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-view {
            background: #e7f0f7;
            color: #334A5E;
        }
        
        .btn-view:hover {
            background: #d1e3f0;
        }
        
        .btn-approve {
            background: #d1e7dd;
            color: #0f5132;
        }
        
        .btn-approve:hover {
            background: #a3cfbb;
        }
        
        .btn-reject {
            background: #f8d7da;
            color: #842029;
        }
        
        .btn-reject:hover {
            background: #f1aeb5;
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }

        .btn[disabled], .btn.disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        /* Color-Coded Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }
        
        .status-approved {
            background: #d1e7dd;
            color: #0f5132;
            border: 1px solid #43A047;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #dc3545;
        }
        
        .status-duplicate {
            background: #fed7aa;
            color: #9a3412;
            border: 1px solid #f97316;
        }

        .status-expiring {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }

        .status-expired {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #dc3545;
        }
        
        /* AI Analysis Badge */
        .ai-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: linear-gradient(135deg, #648EB5, #334A5E);
            color: white;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }
        
        /* Duplicate Warning */
        .duplicate-warning {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #94a3b8;
        }
        
        .empty-state i {
            font-size: 64px;
            color: #cbd5e0;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 20px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            padding: 32px;
            border-radius: 16px;
            max-width: 520px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .modal-header i {
            font-size: 28px;
            color: #ef4444;
        }
        
        .modal-header h3 {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
            font-size: 14px;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            resize: vertical;
            font-size: 14px;
        }
        
        .form-group textarea:focus {
            outline: none;
            border-color: #648EB5;
        }
        
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        
        .btn-cancel {
            background: #f1f5f9;
            color: #475569;
        }
        
        .btn-cancel:hover {
            background: #e2e8f0;
        }
        
        .btn-submit {
            background: #dc3545;
            color: white;
        }
        
        .btn-submit:hover {
            background: #c82333;
        }
        
        /* Duplicate Warning in Modal */
        .duplicate-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .duplicate-warning i {
            color: #ffc107;
            font-size: 20px;
            margin-top: 2px;
        }
        
        .duplicate-warning p {
            color: #856404;
            font-size: 14px;
            margin: 0;
            line-height: 1.6;
        }
        
        .duplicate-warning strong {
            color: #533f03;
        }
        
        .checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
            padding: 14px;
            background: #f8fafc;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            transition: all 0.2s;
        }
        
        .checkbox-label:hover {
            border-color: #648EB5;
            background: #e7f0f7;
        }
        
        .checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            margin-top: 2px;
        }
        
        .checkbox-label span {
            flex: 1;
            font-size: 14px;
            color: #334155;
            font-weight: 500;
        }
        
        /* Detailed View Modal */
        .detail-modal {
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-close {
            margin-left: auto;
            background: #f1f5f9;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-close:hover {
            background: #e2e8f0;
            transform: rotate(90deg);
        }
        
        .modal-close i {
            color: #64748b;
            font-size: 16px;
        }
        
        .detail-content {
            max-height: 65vh;
            overflow-y: auto;
            padding-right: 4px;
        }
        
        .detail-section {
            margin-bottom: 28px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        
        .detail-section h4 {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-section h4 i {
            color: #648EB5;
            font-size: 18px;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        
        .detail-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 14px;
            color: #0f172a;
            font-weight: 500;
        }
        
        .duplicate-info,
        .admin-info {
            background: white;
            padding: 16px;
            border-radius: 8px;
        }
        
        .duplicate-info {
            border: 2px solid #f59e0b;
        }
        
        .duplicate-info p {
            color: #92400e;
            margin-bottom: 12px;
            font-weight: 500;
        }
        
        .document-preview {
            background: white;
            padding: 16px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .document-preview iframe {
            width: 100%;
            height: 400px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        
        .btn-download {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #648EB5, #334A5E);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .btn-download:hover {
            background: linear-gradient(135deg, #4E8EA2, #2B4053);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(100, 142, 181, 0.3);
        }
        
        /* Confidence Progress */
        .progress-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .progress {
            width: 220px;
            height: 12px;
            background: #e9ecef;
            border-radius: 999px;
            overflow: hidden;
        }
        .progress > .fill {
            height: 100%;
            width: 0%;
            transition: width 0.4s ease;
        }
        .fill.green { background: linear-gradient(90deg, #28a745, #20c997); }
        .fill.yellow { background: linear-gradient(90deg, #ffc107, #ff9800); }
        .fill.red { background: linear-gradient(90deg, #dc3545, #c82333); }
        .progress-label {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            min-width: 48px;
            text-align: right;
        }

        /* Muted NA */
        .muted { color: #94a3b8; }

        /* Admin card layout */
        .admin-info {
            background: #ffffff;
            padding: 16px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
        }
        .kv-item { display: grid; grid-template-columns: 160px 1fr; gap: 8px; align-items: center; }
        .helper-note { font-size: 12px; color: #64748b; margin-top: 10px; }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="nav-left">
            <div class="admin-brand">
                <div class="brand-icon">
                    <i class="fas fa-shield-check"></i>
                </div>
                <span>System Administrator</span>
            </div>
        </div>
        
        <div class="nav-right">
            <div class="admin-profile">
                <div class="profile-avatar">
                    {{ strtoupper(substr(Auth::user()->first_name ?? 'A', 0, 1)) }}
                </div>
                <div class="profile-info">
                    <div class="profile-name">{{ Auth::user()->first_name ?? 'Admin' }} {{ Auth::user()->last_name ?? 'User' }}</div>
                    <div class="profile-role">System Administrator</div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        @if(session('success'))
            <div class="alert success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="page-header">
            <h1 class="page-title">Business Permit Verifications</h1>
            <p class="page-subtitle">AI-powered document verification and duplicate detection system</p>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card pending">
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-label">Pending Review</div>
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-desc">Awaiting manual verification</div>
            </div>

            <div class="stat-card approved">
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-label">Approved</div>
                <div class="stat-value">{{ $approvedCount }}</div>
                <div class="stat-desc">Valid permits verified</div>
            </div>

            <div class="stat-card rejected">
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <div class="stat-label">Rejected</div>
                <div class="stat-value">{{ $rejectedCount }}</div>
                <div class="stat-desc">Invalid or expired permits</div>
            </div>

            <div class="stat-card ai">
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                </div>
                <div class="stat-label">AI Analyzed</div>
                <div class="stat-value">{{ $approvedCount + $rejectedCount }}</div>
                <div class="stat-desc">Processed by AI system</div>
            </div>

            <!-- Optional: Expiring Soon Summary -->
            <div class="stat-card expiring">
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-exclamation"></i>
                    </div>
                </div>
                <div class="stat-label">Expiring Soon</div>
                <div class="stat-value">{{ $expiringSoonCount ?? 0 }}</div>
                <div class="stat-desc">Within next 30 days</div>
            </div>
        </div>

        <!-- AI Detection Toggle -->
        <div class="ai-toggle-card">
            <div class="ai-toggle-header">
                <div class="ai-toggle-title">
                    <i class="fas fa-brain"></i>
                    Enhanced AI Detection
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="enhancedAI" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="ai-toggle-desc">
                Advanced content analysis to detect re-scanned, altered, or format-shifted permits across different file types. The AI extracts permit numbers and validates authenticity even when documents are photographed or converted.
            </div>
        </div>

        <!-- Verifications Table -->
        <div class="verifications-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-tasks"></i>
                    Business Permit Verifications
                    @php
                        $badgeCount = $adminUnreadCount ?? 0;
                        $badgeClass = $badgeCount > 0 ? 'yellow' : null;
                    @endphp
                    <span id="adminNotifBadge" class="notif-badge {{ $badgeClass ?? '' }}" style="{{ $badgeCount > 0 ? '' : 'display:none;' }}" onclick="openExpiryAlertsModal()" title="View admin notifications">
                        <span id="adminNotifCount">{{ $badgeCount }}</span>
                    </span>
                </h2>
                <a href="{{ route('admin.notifications.index') }}" style="color: #648EB5; text-decoration: none; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 5px;">
                    📬 View All Notifications
                </a>
            </div>

            <!-- Filters and Search -->
            <div class="filters-section">
                <form method="GET" action="{{ route('admin.verifications.index') }}" class="filters-form">
                    <div class="filter-group">
                        <label for="status">
                            <i class="fas fa-filter"></i>
                            Status
                        </label>
                        <select name="status" id="status" class="filter-select">
                            <option value="">All Statuses</option>
                            <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="expiring_soon" {{ request('status') === 'expiring_soon' ? 'selected' : '' }}>Expiring Soon (30 days)</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="search">
                            <i class="fas fa-search"></i>
                            Search
                        </label>
                        <input 
                            type="text" 
                            name="search" 
                            id="search" 
                            class="filter-input" 
                            placeholder="Company name or email..."
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-apply">
                            <i class="fas fa-check"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.verifications.index') }}" class="btn-clear">
                            <i class="fas fa-times"></i>
                            Clear
                        </a>
                    </div>
                </form>

                @if(request('status') || request('search'))
                    <div class="active-filters">
                        <span class="filter-label">Active Filters:</span>
                        @if(request('status'))
                            <span class="active-filter-badge">
                                Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                            </span>
                        @endif
                        @if(request('search'))
                            <span class="active-filter-badge">
                                Search: "{{ request('search') }}"
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            @if($pendingVerifications->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-{{ request('status') || request('search') ? 'search' : 'check-circle' }}"></i>
                    <h3>{{ request('status') || request('search') ? 'No Results Found' : 'All Caught Up!' }}</h3>
                    <p>
                        @if(request('status') || request('search'))
                            No verifications match your current filters. Try adjusting your search criteria.
                        @else
                            No pending verifications at the moment
                        @endif
                    </p>
                </div>
            @else
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Contact Person</th>
                                <th>Email</th>
                                <th>AI Analysis</th>
                                <th>Status</th>
                                <th>Expiry Date</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingVerifications as $validation)
                                @php
                                    $validationData = [
                                        'company_name' => $validation->user->company_name ?? 'N/A',
                                        'contact_person' => trim(($validation->user->first_name ?? '') . ' ' . ($validation->user->last_name ?? '')),
                                        'email' => $validation->user->email ?? 'N/A',
                                        'address' => $validation->user->address ?? 'No address provided',
                                        'file_path' => route('admin.verifications.file', $validation->id),
                                        'validation_status' => $validation->validation_status,
                                        'validated_by' => $validation->validated_by,
                                        'confidence_score' => $validation->confidence_score ?? 0,
                                        'created_at' => $validation->created_at->format('M d, Y h:i A'),
                                        'ai_analysis' => $validation->ai_analysis ?? (object)[],
                                    ];
                                @endphp
                                <tr data-validation-id="{{ $validation->id }}"
                                    data-validation-data='@json($validationData)'
                                >
                                    <td>
                                        <div class="company-info">
                                            <div class="company-name">{{ $validation->user->company_name ?? 'N/A' }}</div>
                                            <div class="company-address">{{ Str::limit($validation->user->address ?? 'No address', 40) }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $validation->user->first_name }} {{ $validation->user->last_name }}</td>
                                    <td>{{ $validation->user->email }}</td>
                                    <td>
                                        @if($validation->validated_by === 'ai')
                                            <span class="badge ai-verified">
                                                <i class="fas fa-robot"></i>
                                                AI Verified
                                            </span>
                                        @else
                                            <span class="badge system-flagged">
                                                <i class="fas fa-flag"></i>
                                                System Flagged
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $isDup = isset($validation->ai_analysis['duplicate_detection']['is_duplicate']) 
                                                && $validation->ai_analysis['duplicate_detection']['is_duplicate'];
                                            $status = $validation->validation_status;
                                            $expiry = $validation->permit_expiry_date;
                                            $isExpired = $expiry ? $expiry->lt(now()->startOfDay()) : false;
                                            $isExpiringSoon = $expiry ? ($expiry->gte(now()->startOfDay()) && $expiry->lte(now()->addDays(30)->endOfDay())) : false;
                                        @endphp
                                        
                                        @if($isDup)
                                            <span class="status-badge status-duplicate">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Duplicate Detected
                                            </span>
                                        @elseif($status === 'approved')
                                            <span class="status-badge status-approved">
                                                <i class="fas fa-check-circle"></i>
                                                Approved
                                            </span>
                                        @elseif($status === 'rejected')
                                            <span class="status-badge status-rejected">
                                                <i class="fas fa-times-circle"></i>
                                                Rejected
                                            </span>
                                        @else
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-clock"></i>
                                                Pending Review
                                            </span>
                                        @endif

                                        @if($isExpired)
                                            <div style="margin-top:6px;">
                                                <span class="status-badge status-expired">
                                                    <i class="fas fa-calendar-times"></i>
                                                    Expired
                                                </span>
                                            </div>
                                        @elseif($isExpiringSoon)
                                            <div style="margin-top:6px;">
                                                <span class="status-badge status-expiring">
                                                    <i class="fas fa-calendar-exclamation"></i>
                                                    Expiring Soon
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($validation->permit_expiry_date)
                                            {{ $validation->permit_expiry_date->format('M j, Y') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $validation->created_at->diffForHumans() }}</td>
                                    <td>
                                        <div class="actions">
                                            <button onclick="openDetailModal({{ $validation->id }})" class="btn btn-view">
                                                <i class="fas fa-eye"></i>
                                                View
                                            </button>
                                            @php
                                                $isDuplicate = isset($validation->ai_analysis['duplicate_detection']['is_duplicate']) 
                                                    && $validation->ai_analysis['duplicate_detection']['is_duplicate'];
                                                $hasPendingForUser = in_array($validation->user_id, $usersWithPending ?? []);
                                                $canApprove = !($validation->validation_status === 'rejected' && !$hasPendingForUser);
                                            @endphp
                                            @if(!$canApprove)
                                                <button class="btn btn-approve" disabled title="Awaiting new permit upload for re-verification.">
                                                    <i class="fas fa-check"></i>
                                                    Approve
                                                </button>
                                            @else
                                                @if($isDuplicate)
                                                    <button onclick="openApproveModal({{ $validation->id }}, '{{ addslashes($validation->user->company_name ?? 'N/A') }}')" class="btn btn-approve">
                                                        <i class="fas fa-check"></i>
                                                        Approve
                                                    </button>
                                                @else
                                                    <form method="POST" action="{{ route('admin.verifications.approve', $validation->id) }}" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-approve" onclick="return confirm('Approve this business permit?')">
                                                            <i class="fas fa-check"></i>
                                                            Approve
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                            <button onclick="openRejectModal({{ $validation->id }})" class="btn btn-reject">
                                                <i class="fas fa-times"></i>
                                                Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Expiring/Expired Permits Modal -->
    <div id="expiryAlertsModal" class="modal">
        <div class="modal-content detail-modal" style="max-width: 700px;">
            <div class="modal-header" style="color: #f59e0b;">
                <i class="fas fa-calendar-alt"></i>
                <h3>Expiring & Expired Permits</h3>
                <button onclick="closeExpiryAlertsModal()" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="detail-content">
                <div style="display:flex; justify-content:flex-end; margin-bottom:10px;">
                    <button class="btn btn-approve" onclick="markAllAdminNotificationsRead()" style="background:#e7f0f7; color:#334A5E;">
                        <i class="fas fa-check-double"></i> Mark All as Read
                    </button>
                </div>
                <div class="detail-section">
                    <h4><i class="fas fa-exclamation-triangle"></i> Expiring Soon (Next 30 Days)</h4>
                    @if(($expiringSoonList ?? collect())->isEmpty())
                        <p style="color:#64748b;">No permits expiring within the next 30 days.</p>
                    @else
                        <div class="document-preview" style="gap:8px;">
                            @foreach($expiringSoonList as $item)
                                <div style="display:flex; justify-content:space-between; align-items:center; border:1px solid #e2e8f0; padding:10px 12px; border-radius:8px; background:white;">
                                    <div>
                                        <div style="font-weight:700; color:#0f172a;">{{ $item->user->company_name ?? 'N/A' }}</div>
                                        <div style="font-size:13px; color:#64748b;">{{ $item->user->email ?? 'N/A' }}</div>
                                    </div>
                                    <div style="text-align:right;">
                                        <div class="status-badge status-expiring" style="display:inline-flex;">
                                            <i class="fas fa-calendar-exclamation"></i>&nbsp;Expiring: {{ optional($item->permit_expiry_date)->format('M j, Y') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-calendar-times"></i> Expired</h4>
                    @if(($expiredList ?? collect())->isEmpty())
                        <p style="color:#64748b;">No expired permits found.</p>
                    @else
                        <div class="document-preview" style="gap:8px;">
                            @foreach($expiredList as $item)
                                <div style="display:flex; justify-content:space-between; align-items:center; border:1px solid #e2e8f0; padding:10px 12px; border-radius:8px; background:white;">
                                    <div>
                                        <div style="font-weight:700; color:#0f172a;">{{ $item->user->company_name ?? 'N/A' }}</div>
                                        <div style="font-size:13px; color:#64748b;">{{ $item->user->email ?? 'N/A' }}</div>
                                    </div>
                                    <div style="text-align:right;">
                                        <div class="status-badge status-expired" style="display:inline-flex;">
                                            <i class="fas fa-calendar-times"></i>&nbsp;Expired: {{ optional($item->permit_expiry_date)->format('M j, Y') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="modal-actions">
                <button onclick="closeExpiryAlertsModal()" class="btn btn-cancel">Close</button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-times-circle"></i>
                <h3>Reject Business Permit</h3>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="form-group">
                    <label>Reason for Rejection:</label>
                    <textarea name="rejection_reason" rows="5" required placeholder="Enter detailed reason for rejection..."></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeRejectModal()" class="btn btn-cancel">Cancel</button>
                    <button type="submit" class="btn btn-submit">Reject Permit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Approve Duplicate Modal -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" style="color: #f59e0b;">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Approve Duplicate Permit</h3>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="duplicate-warning">
                    <i class="fas fa-info-circle"></i>
                    <p>This permit has been flagged as a <strong>potential duplicate</strong> for company: <span id="duplicateCompanyName"></span></p>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="override_duplicate" value="1" required>
                        <span>I confirm this is a valid permit and want to override the duplicate detection</span>
                    </label>
                </div>
                <div class="form-group">
                    <label>Admin Notes (Required for duplicate override):</label>
                    <textarea name="admin_notes" rows="4" required placeholder="Explain why you're approving this duplicate permit..."></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeApproveModal()" class="btn btn-cancel">Cancel</button>
                    <button type="submit" class="btn btn-submit">Approve with Override</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Detailed View Modal -->
    <div id="detailModal" class="modal">
        <div class="modal-content detail-modal">
            <div class="modal-header" style="color: #6366f1;">
                <i class="fas fa-file-alt"></i>
                <h3>Business Permit Details</h3>
                <button onclick="closeDetailModal()" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="detail-content">
                <!-- Company Information -->
                <div class="detail-section">
                    <h4><i class="fas fa-building"></i> Company Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Company Name:</span>
                            <span class="detail-value" id="detail-company"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Contact Person:</span>
                            <span class="detail-value" id="detail-contact"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value" id="detail-email"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Address:</span>
                            <span class="detail-value" id="detail-address"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Submitted:</span>
                            <span class="detail-value" id="detail-submitted"></span>
                        </div>
                    </div>
                </div>

                <!-- AI Analysis Results -->
                <div class="detail-section">
                    <h4><i class="fas fa-robot"></i> AI Analysis Results</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Validation Method:</span>
                            <span class="detail-value" id="detail-method"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Confidence Score:</span>
                            <span class="detail-value" id="detail-confidence"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Document Type:</span>
                            <span class="detail-value" id="detail-doctype"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Permit Number:</span>
                            <span class="detail-value" id="detail-permit"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Issuing Authority:</span>
                            <span class="detail-value" id="detail-authority"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Expiry Date:</span>
                            <span class="detail-value" id="detail-expiry"></span>
                        </div>
                    </div>
                </div>

                <!-- Duplicate Detection -->
                <div class="detail-section" id="duplicate-section" style="display: none;">
                    <h4><i class="fas fa-exclamation-triangle"></i> Duplicate Detection Alert</h4>
                    <div class="duplicate-info">
                        <p id="duplicate-message"></p>
                        <div class="detail-grid" id="duplicate-details"></div>
                    </div>
                </div>

                <!-- Admin Tracking -->
                <div class="detail-section" id="admin-section" style="display: none;">
                    <h4><i class="fas fa-user-shield"></i> Admin Action History</h4>
                    <div class="admin-info">
                        <div class="detail-grid" id="admin-details"></div>
                    </div>
                </div>

                <!-- Document Preview -->
                <div class="detail-section">
                    <h4><i class="fas fa-file-pdf"></i> Document Preview</h4>
                    <div class="document-preview">
                        <iframe id="detail-preview" frameborder="0"></iframe>
                        <a id="detail-download" target="_blank" class="btn-download">
                            <i class="fas fa-download"></i>
                            Download Original File
                        </a>
                    </div>
                </div>
            </div>

            <p class="helper-note">If information appears outdated, request a new permit upload.</p>
 
            <div class="modal-actions">
                 <button onclick="closeDetailModal()" class="btn btn-cancel">Close</button>
             </div>
         </div>
     </div>    <script>
        function openRejectModal(id) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/admin/verifications/${id}/reject`;
            modal.style.display = 'flex';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }

        function openApproveModal(id, companyName) {
            const modal = document.getElementById('approveModal');
            const form = document.getElementById('approveForm');
            const companyDisplay = document.getElementById('duplicateCompanyName');
            
            form.action = `/admin/verifications/${id}/approve`;
            companyDisplay.textContent = companyName;
            modal.style.display = 'flex';
        }

        function closeApproveModal() {
            document.getElementById('approveModal').style.display = 'none';
        }

        function openExpiryAlertsModal() {
            const modal = document.getElementById('expiryAlertsModal');
            if (modal) modal.style.display = 'flex';
        }

        function closeExpiryAlertsModal() {
            const modal = document.getElementById('expiryAlertsModal');
            if (modal) modal.style.display = 'none';
        }

        async function markAllAdminNotificationsRead() {
            try {
                const resp = await fetch("{{ route('admin.notifications.markAllRead') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({})
                });

                if (!resp.ok) {
                    throw new Error('Request failed with status ' + resp.status);
                }
                const data = await resp.json();
                if (data && data.success) {
                    // Hide badge and set count to 0
                    const badge = document.getElementById('adminNotifBadge');
                    const countEl = document.getElementById('adminNotifCount');
                    if (countEl) countEl.textContent = '0';
                    if (badge) {
                        badge.style.display = 'none';
                        badge.classList.remove('yellow', 'red');
                    }
                }
            } catch (e) {
                console.error('Failed to mark admin notifications as read:', e);
                alert('Failed to mark notifications as read. Please try again.');
            }
        }

        function openDetailModal(id) {
            console.log('Opening detail modal for validation ID:', id);
            const row = document.querySelector(`tr[data-validation-id="${id}"]`);
            
            if (!row) {
                console.error('Row not found for validation ID:', id);
                alert('Error: Unable to find validation data');
                return;
            }
            
            try {
                const dataAttr = row.getAttribute('data-validation-data');
                console.log('Data attribute:', dataAttr);
                const data = JSON.parse(dataAttr);
                console.log('Parsed data:', data);
                const ai = data.ai_analysis || {};
                
                // Populate company info
                document.getElementById('detail-company').textContent = data.company_name;
                document.getElementById('detail-contact').textContent = data.contact_person;
                document.getElementById('detail-email').textContent = data.email;
                document.getElementById('detail-address').textContent = data.address;
                document.getElementById('detail-submitted').textContent = data.created_at;
                
                // Populate AI analysis
                // Validation method with icon
                const status = data.validation_status;
                let methodIcon = 'fa-flag';
                let methodText = 'System Flagged';
                let methodClass = 'system-flagged';
                if (status === 'approved') {
                    methodIcon = 'fa-check-circle';
                    methodText = 'Verified';
                    methodClass = 'ai-verified';
                } else if (status === 'rejected') {
                    methodIcon = 'fa-times-circle';
                    methodText = 'Rejected';
                    methodClass = 'status-rejected';
                } else if (data.validated_by === 'ai') {
                    methodIcon = 'fa-robot';
                    methodText = 'AI Validated';
                    methodClass = 'ai-verified';
                }
                document.getElementById('detail-method').innerHTML = `
                    <span class="badge ${methodClass}">
                        <i class="fas ${methodIcon}"></i>
                        ${methodText}
                    </span>
                `;

                // Confidence progress bar
                let confidencePercent = parseFloat(data.confidence_score) || 0;
                if (confidencePercent <= 1) confidencePercent = confidencePercent * 100; // tolerate 0-1 inputs
                const band = confidencePercent >= 80 ? 'green' : (confidencePercent >= 50 ? 'yellow' : 'red');
                document.getElementById('detail-confidence').innerHTML = `
                    <div class="progress-wrap">
                        <div class="progress"><div class="fill ${band}" style="width:${Math.max(0, Math.min(100, confidencePercent))}%;"></div></div>
                        <div class="progress-label">${confidencePercent.toFixed(0)}%</div>
                    </div>
                `;
                
                const setField = (id, value) => {
                    const el = document.getElementById(id);
                    const val = value && String(value).trim() !== '' ? value : 'N/A';
                    el.textContent = val;
                    if (val === 'N/A') el.classList.add('muted'); else el.classList.remove('muted');
                };
                setField('detail-doctype', ai.document_type);
                setField('detail-permit', ai.permit_number || 'Not extracted');
                setField('detail-authority', ai.issuing_authority);
                setField('detail-expiry', ai.expiry_date || ai.validity_end_date);
                
                // Handle duplicate detection
                const duplicateSection = document.getElementById('duplicate-section');
                if (ai.duplicate_detection && ai.duplicate_detection.is_duplicate) {
                    duplicateSection.style.display = 'block';
                    const dup = ai.duplicate_detection;
                    document.getElementById('duplicate-message').innerHTML = `
                        <strong>⚠️ Duplicate Alert:</strong> This permit appears to be a duplicate based on ${dup.match_type || 'multiple factors'}.
                    `;
                    
                    let duplicateDetailsHTML = '';
                    if (dup.matching_fields) {
                        duplicateDetailsHTML = `
                            <div class="detail-item">
                                <span class="detail-label">Matching Fields:</span>
                                <span class="detail-value">${dup.matching_fields.join(', ')}</span>
                            </div>
                        `;
                    }
                    if (dup.existing_validation_id) {
                        duplicateDetailsHTML += `
                            <div class="detail-item">
                                <span class="detail-label">Existing Validation ID:</span>
                                <span class="detail-value">#${dup.existing_validation_id}</span>
                            </div>
                        `;
                    }
                    document.getElementById('duplicate-details').innerHTML = duplicateDetailsHTML;
                } else {
                    duplicateSection.style.display = 'none';
                }
                
                // Handle admin tracking
                const adminSection = document.getElementById('admin-section');
                if (ai.admin_approval || ai.admin_rejection) {
                    adminSection.style.display = 'block';
                    const adminAction = ai.admin_approval || ai.admin_rejection;
                    const actionType = ai.admin_approval ? 'Approved' : 'Rejected';
                    const actionColor = ai.admin_approval ? '#43A047' : '#dc3545';
                    
                    let adminHTML = `
                        <div class="kv-item">
                            <span class="detail-label">Action</span>
                            <span class="detail-value" style="color: ${actionColor}; font-weight: 700;">${actionType}</span>
                        </div>
                        <div class="kv-item">
                            <span class="detail-label">Admin ID</span>
                            <span class="detail-value">#${adminAction.admin_id || 'N/A'}</span>
                        </div>
                        <div class="kv-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value">${adminAction.admin_email || 'N/A'}</span>
                        </div>
                        <div class="kv-item">
                            <span class="detail-label">Timestamp</span>
                            <span class="detail-value">${adminAction.approved_at || adminAction.rejected_at || 'N/A'}</span>
                        </div>
                    `;
                    
                    if (adminAction.notes) {
                        adminHTML += `
                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <span class="detail-label">Admin Notes</span>
                                <span class="detail-value">${adminAction.notes}</span>
                            </div>
                        `;
                    }
                    
                    if (adminAction.reason) {
                        adminHTML += `
                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <span class="detail-label">Rejection Reason</span>
                                <span class="detail-value">${adminAction.reason}</span>
                            </div>
                        `;
                    }
                    
                    document.getElementById('admin-details').innerHTML = adminHTML;
                } else {
                    adminSection.style.display = 'none';
                }
                
                // Set document preview
                document.getElementById('detail-preview').src = data.file_path;
                document.getElementById('detail-download').href = data.file_path;
                
                // Show modal
                const modal = document.getElementById('detailModal');
                modal.style.display = 'flex';
                console.log('Modal should now be visible');
                
            } catch (error) {
                console.error('Error parsing validation data:', error);
                alert('Error loading permit details: ' + error.message);
            }
        }

        function closeDetailModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const rejectModal = document.getElementById('rejectModal');
            const approveModal = document.getElementById('approveModal');
            const detailModal = document.getElementById('detailModal');
            
            if (event.target === rejectModal) {
                rejectModal.style.display = 'none';
            }
            if (event.target === approveModal) {
                approveModal.style.display = 'none';
            }
            if (event.target === detailModal) {
                detailModal.style.display = 'none';
            }
            const expiryModal = document.getElementById('expiryAlertsModal');
            if (event.target === expiryModal) {
                expiryModal.style.display = 'none';
            }
        }

        // Auto-hide success messages
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.3s';
                setTimeout(() => alert.remove(), 300);
            }
        }, 4000);

        // Enhanced AI toggle
        document.getElementById('enhancedAI').addEventListener('change', function() {
            console.log('Enhanced AI Detection:', this.checked ? 'Enabled' : 'Disabled');
            // You can add AJAX call here to update preference
        });
    </script>
</body>
</html>

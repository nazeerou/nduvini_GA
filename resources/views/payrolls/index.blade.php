@extends('layouts.app_header')
@section('content')

<style>
    /* ===================== Payroll – extra styles ===================== */
    .pr-badge{display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 6px;margin-left:6px;font-size:11px;font-weight:700;border-radius:999px;background:#ef4444;color:#fff;}
    .pr-badge.pr-badge-warning{background:#f59e0b;}
    .pr-badge.pr-badge-muted{background:#94a3b8;}
    .pr-status{display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:999px;font-size:11.5px;font-weight:600;line-height:1.35;white-space:nowrap;border:1px solid transparent;}
    .pr-status::before{content:"";display:inline-block;width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0;}
    .pr-status-pending {background:#fff7ed;color:#c2410c;border-color:#fed7aa;}
    .pr-status-approved{background:#ecfdf5;color:#047857;border-color:#a7f3d0;}
    .pr-status-rejected{background:#fef2f2;color:#b91c1c;border-color:#fecaca;}
    .pr-status-cancelled{background:#f8fafc;color:#475569;border-color:#e2e8f0;}
    .pr-status-paid    {background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;}
    .pr-type{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:6px;font-size:11.5px;font-weight:600;}
    .pr-type-salary {background:#dbeafe;color:#1e40af;}
    .pr-type-advance{background:#e0e7ff;color:#4338ca;}
    #requestSummaryTable td{vertical-align:middle;}
    #inboxTable .inbox-check{transform:scale(1.15);cursor:pointer;}
    .pr-approval-banner{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:13px;}
    .pr-approval-banner.pr-approved{background:#dcfce7;color:#166534;border:1px solid #a7f3d0;}
    .pr-approval-banner.pr-pending {background:#fef3c7;color:#92400e;border:1px solid #fde68a;}
    .pr-approval-banner.pr-missing {background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;}

    .salary-shell{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.04),0 1px 2px rgba(15,23,42,.03);}
    .salary-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:#e5e7eb;border-bottom:1px solid #e5e7eb;}
    @media (max-width:991px){ .salary-stats{grid-template-columns:repeat(2,1fr);} }
    @media (max-width:575px){ .salary-stats{grid-template-columns:1fr;} }
    .salary-stat{background:#fff;padding:18px 22px;position:relative;}
    .salary-stat-label{font-size:10.5px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:#94a3b8;margin:0 0 6px;display:flex;align-items:center;gap:6px;}
    .salary-stat-label i{font-size:11px;color:#cbd5e1;}
    .salary-stat-value{font-family:'SFMono-Regular',Consolas,monospace;font-size:20px;font-weight:700;color:#0f172a;letter-spacing:-.02em;line-height:1.15;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .salary-stat-sub{font-size:11.5px;color:#94a3b8;margin-top:4px;}
    .salary-stat.is-paid   .salary-stat-value{color:#047857;}
    .salary-stat.is-partial .salary-stat-value{color:#c2410c;}
    .salary-stat.is-unpaid  .salary-stat-value{color:#b91c1c;}
    .salary-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;padding:16px 22px;border-bottom:1px solid #f1f5f9;background:#fafbfc;}
    .salary-filter{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
    .salary-filter label{font-size:11.5px;font-weight:600;color:#64748b;margin:0;letter-spacing:.02em;}
    .salary-filter select{height:34px;padding:0 30px 0 10px;font-size:12.5px;font-weight:500;color:#0f172a;background:#fff;border:1px solid #e2e8f0;border-radius:7px;outline:none;cursor:pointer;transition:all .15s ease;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='%2394a3b8' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;}
    .salary-filter select:hover{border-color:#cbd5e1;}
    .salary-filter select:focus{border-color:#1e3a5f;box-shadow:0 0 0 3px rgba(30,58,95,.10);}
    .salary-filter-reset{height:34px;padding:0 12px;font-size:12px;font-weight:600;color:#64748b;background:#fff;border:1px solid #e2e8f0;border-radius:7px;cursor:pointer;transition:all .15s ease;display:inline-flex;align-items:center;gap:6px;}
    .salary-filter-reset:hover{background:#f1f5f9;color:#0f172a;border-color:#cbd5e1;}
    .salary-filter-count{font-size:11.5px;color:#64748b;padding:4px 10px;background:#eff6ff;color:#1d4ed8;border-radius:999px;font-weight:600;}
    .salary-btn-generate{display:inline-flex;align-items:center;gap:7px;height:34px;padding:0 16px;font-size:12.5px;font-weight:600;letter-spacing:.01em;color:#fff;background:linear-gradient(180deg,#2d5282 0%,#1e3a5f 100%);border:1px solid #1e3a5f;border-radius:7px;cursor:pointer;transition:all .18s ease;box-shadow:0 1px 2px rgba(30,58,95,.20),inset 0 1px 0 rgba(255,255,255,.10);}
    .salary-btn-generate:hover{background:linear-gradient(180deg,#1e3a5f 0%,#152a45 100%);transform:translateY(-1px);color:#fff;text-decoration:none;box-shadow:0 3px 8px rgba(30,58,95,.25),inset 0 1px 0 rgba(255,255,255,.10);}
    .salary-btn-generate i{font-size:11px;}
    .salary-table-wrap{overflow-x:auto;}
    .salary-table{width:100%!important;border-collapse:separate;border-spacing:0;margin:0;font-size:13px;}
    .salary-table thead th{background:#fff;color:#64748b;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:12px 18px;text-align:left;border-bottom:1px solid #e5e7eb;white-space:nowrap;}
    .salary-table thead th.text-right{text-align:right;}
    .salary-table thead th.text-center{text-align:center;}
    .salary-table tbody td{padding:14px 18px;border-bottom:1px solid #f1f5f9;vertical-align:middle;color:#334155;background:#fff;transition:background .12s ease;}
    .salary-table tbody tr:hover td{background:#f8fafc;}
    .salary-table tbody tr:last-child td{border-bottom:none;}
    .salary-table .col-month{font-weight:600;color:#0f172a;white-space:nowrap;}
    .salary-table .col-month small{display:block;font-weight:500;color:#94a3b8;font-size:11px;margin-top:2px;}
    .salary-table .col-num{font-family:'SFMono-Regular',Consolas,monospace;font-size:12.5px;font-weight:600;color:#0f172a;text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums;}
    .salary-table .col-num.is-muted{color:#94a3b8;font-weight:500;}
    .salary-table .col-employees{font-size:12.5px;color:#475569;text-align:center;}
    .salary-table .col-employees strong{color:#0f172a;font-weight:600;}
    .salary-actions{display:flex;align-items:center;gap:6px;flex-wrap:nowrap;}
    .sal-act{display:inline-flex;align-items:center;gap:5px;height:30px;padding:0 10px;font-size:11.5px;font-weight:600;letter-spacing:.01em;border-radius:6px;cursor:pointer;text-decoration:none;border:1px solid transparent;white-space:nowrap;transition:all .15s ease;}
    .sal-act i{font-size:11px;opacity:.9;}
    .sal-act:hover{text-decoration:none;transform:translateY(-1px);}
    .sal-act-pay{background:#00a1ff;color:#fff;border-color:#00a1dd;}
    .sal-act-pay:hover{background:#00a1fd;color:#fff;}
    .sal-act-view{background:#f8fafc;color:#334155;border-color:#e2e8f0;}
    .sal-act-view:hover{background:#f1f5f9;color:#0f172a;border-color:#cbd5e1;}
    .sal-act-bank{background:#fff;color:#475569;border-color:#e2e8f0;}
    .sal-act-bank:hover{background:#f1f5f9;color:#0f172a;border-color:#cbd5e1;}
    .sal-act-danger{background:#fff;color:#b91c1c;border-color:#fecaca;}
    .sal-act-danger:hover{background:#fef2f2;color:#991b1b;border-color:#fca5a5;}
    .sal-act-placeholder{font-size:11.5px;color:#94a3b8;font-style:italic;}
    .sal-act-request{background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;}
    .sal-act-request:hover{background:#dbeafe;color:#1e40af;}
    .sal-act-success{background:#10b981;color:#fff;border-color:#059669;}
    .sal-act-success:hover{background:#059669;color:#fff;border-color:#047857;}
    .sal-act-pending{background:#fef3c7;color:#92400e;border-color:#fde68a;cursor:not-allowed;opacity:.75;}
    .sal-act-pending:hover{transform:none;}
    .sal-dropdown{position:relative;display:inline-block;}
    .sal-dropdown-menu{display:none;position:absolute;top:calc(100% + 4px);left:0;min-width:170px;padding:4px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 10px 30px rgba(15,23,42,.12);z-index:1050;list-style:none;margin:0;}
    .sal-dropdown.open .sal-dropdown-menu{display:block;}
    .sal-dropdown-menu a{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#475569;text-decoration:none;cursor:pointer;transition:background .12s ease;}
    .sal-dropdown-menu a:hover{background:#f1f5f9;color:#0f172a;text-decoration:none;}
    .sal-dropdown-menu a i{width:14px;font-size:11px;text-align:center;color:#94a3b8;}
    .sal-dropdown-menu a:hover i{color:#1e3a5f;}

    #payPayrollModal .modal-content,#payAdvanceModal .modal-content,#approvalRequestModal .modal-content,#approvePayrollModal .modal-content,#recordRepaymentModal .modal-content{border:none;border-radius:14px;overflow:hidden;box-shadow:0 24px 60px rgba(15,23,42,.20);}
    #payPayrollModal .modal-header,#payAdvanceModal .modal-header,#approvalRequestModal .modal-header,#approvePayrollModal .modal-header,#recordRepaymentModal .modal-header{background:#fff;border-bottom:1px solid #e5e7eb;padding:18px 22px;}
    #payPayrollModal .modal-header h4,#payAdvanceModal .modal-header h4,#approvalRequestModal .modal-header h4,#approvePayrollModal .modal-header h4,#recordRepaymentModal .modal-header h4{margin:0;font-size:16px;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:8px;}
    #payPayrollModal .modal-header h4 i,#payAdvanceModal .modal-header h4 i,#approvalRequestModal .modal-header h4 i{color:#1e3a5f;font-size:15px;}
    #approvePayrollModal .modal-header h4 i{color:#10b981;font-size:15px;}
    #recordRepaymentModal .modal-header h4 i{color:#475569;font-size:15px;}
    #payPayrollModal .modal-header .close,#payAdvanceModal .modal-header .close,#approvalRequestModal .modal-header .close,#approvePayrollModal .modal-header .close,#recordRepaymentModal .modal-header .close{color:#94a3b8;opacity:.8;font-size:22px;margin-top:-2px;}
    #payPayrollModal .modal-header .close:hover,#payAdvanceModal .modal-header .close:hover,#approvalRequestModal .modal-header .close:hover,#approvePayrollModal .modal-header .close:hover,#recordRepaymentModal .modal-header .close:hover{color:#0f172a;opacity:1;}
    #payPayrollModal .modal-body,#payAdvanceModal .modal-body,#approvalRequestModal .modal-body,#approvePayrollModal .modal-body,#recordRepaymentModal .modal-body{padding:22px;background:#fff;}
    .pay-modal-note{display:flex;align-items:flex-start;gap:10px;padding:11px 14px;border-radius:8px;background:#eff6ff;border:1px solid #bfdbfe;font-size:12.5px;color:#1e40af;line-height:1.5;margin-bottom:16px;}
    .pay-modal-note i{color:#1e40af;font-size:13px;margin-top:1px;}
    .pay-modal-period{display:flex;align-items:center;justify-content:space-between;padding:11px 14px;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;margin-bottom:14px;font-size:13px;}
    .pay-modal-period strong{color:#0f172a;font-weight:700;}
    #payEmployeeTable,#payAdvanceTable{width:100%!important;border-collapse:separate;border-spacing:0;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;margin:0;}
    #payEmployeeTable thead th,#payAdvanceTable thead th{background:#f8fafc;color:#64748b;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:10px 14px;text-align:left;border-bottom:1px solid #e5e7eb;}
    #payEmployeeTable thead th.text-right,#payAdvanceTable thead th.text-right{text-align:right;}
    #payEmployeeTable tbody td,#payAdvanceTable tbody td{padding:11px 14px;border-bottom:1px solid #f1f5f9;font-size:12.5px;color:#334155;vertical-align:middle;}
    #payEmployeeTable tbody tr:last-child td,#payAdvanceTable tbody tr:last-child td{border-bottom:none;}
    #payEmployeeTable tbody tr.row-paid,#payAdvanceTable tbody tr.row-paid{background:#ecfdf5;}
    #payEmployeeTable tbody tr.row-paid td,#payAdvanceTable tbody tr.row-paid td{color:#475569;}
    #payEmployeeTable tbody tr.row-unpaid:hover,#payAdvanceTable tbody tr.row-unpaid:hover{background:#f8fafc;}
    #payEmployeeTable .pay-emp-check,#payAdvanceTable .pay-adv-check{width:16px;height:16px;cursor:pointer;accent-color:#1e3a5f;margin:0;}
    #payEmployeeTable .pay-emp-check:disabled,#payAdvanceTable .pay-adv-check:disabled{cursor:not-allowed;}
    .pay-total-bar{display:flex;align-items:center;justify-content:flex-end;gap:8px;padding:14px 18px;margin-top:14px;background:linear-gradient(180deg,#f8fafc,#f1f5f9);border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#475569;}
    .pay-total-bar .total-label{font-weight:600;}
    .pay-total-bar .total-value{font-family:'SFMono-Regular',Consolas,monospace;font-size:16px;font-weight:700;color:#0f172a;font-variant-numeric:tabular-nums;}
    #payPayrollModal .modal-footer,#payAdvanceModal .modal-footer,#approvalRequestModal .modal-footer,#approvePayrollModal .modal-footer,#recordRepaymentModal .modal-footer{background:#fafbfc;border-top:1px solid #e5e7eb;padding:14px 22px;}
    #payPayrollModal .btn-default,#payAdvanceModal .btn-default,#approvalRequestModal .btn-default,#approvePayrollModal .btn-default,#recordRepaymentModal .btn-default{background:#fff;border:1px solid #e2e8f0;color:#475569;border-radius:7px;padding:8px 18px;font-weight:600;font-size:12.5px;}
    #payPayrollModal .btn-default:hover,#payAdvanceModal .btn-default:hover,#approvalRequestModal .btn-default:hover,#approvePayrollModal .btn-default:hover,#recordRepaymentModal .btn-default:hover{background:#f1f5f9;color:#0f172a;border-color:#cbd5e1;}
    #payPayrollModal .btn-primary,#payAdvanceModal .btn-primary,#approvalRequestModal .btn-primary{background:linear-gradient(180deg,#2d5282,#1e3a5f);border-color:#1e3a5f;color:#fff;border-radius:7px;padding:9px 20px;font-weight:600;font-size:12.5px;box-shadow:0 1px 2px rgba(30,58,95,.20);}
    #payPayrollModal .btn-primary:hover,#payAdvanceModal .btn-primary:hover,#approvalRequestModal .btn-primary:hover{background:linear-gradient(180deg,#1e3a5f,#152a45);border-color:#152a45;}
    #approvePayrollModal .btn-primary{background:linear-gradient(180deg,#10b981,#059669);border-color:#059669;color:#fff;border-radius:7px;padding:9px 20px;font-weight:600;font-size:12.5px;box-shadow:0 1px 2px rgba(5,150,105,.25);}
    #approvePayrollModal .btn-primary:hover{background:linear-gradient(180deg,#059669,#047857);border-color:#047857;}
    #recordRepaymentModal .btn-primary{background:linear-gradient(180deg,#475569,#334155);border-color:#334155;color:#fff;border-radius:7px;padding:9px 20px;font-weight:600;font-size:12.5px;box-shadow:0 1px 2px rgba(51,65,85,.25);}
    #recordRepaymentModal .btn-primary:hover{background:linear-gradient(180deg,#334155,#1e293b);border-color:#1e293b;}
    #payPayrollModal .btn-primary[disabled],#payAdvanceModal .btn-primary[disabled],#approvalRequestModal .btn-primary[disabled],#approvePayrollModal .btn-primary[disabled],#recordRepaymentModal .btn-primary[disabled]{opacity:.65;cursor:not-allowed;}
    #approvalRequestModal .form-group,#approvePayrollModal .form-group,#recordRepaymentModal .form-group{margin-bottom:16px;}
    #approvalRequestModal .control-label,#approvePayrollModal .control-label,#recordRepaymentModal .control-label{display:block;margin-bottom:7px;font-size:12px;font-weight:600;color:#475569;letter-spacing:.02em;}
    #approvalRequestModal .control-label .req,#approvePayrollModal .control-label .req,#recordRepaymentModal .control-label .req{color:#dc2626;font-weight:700;}
    #approvalRequestModal .form-control,#approvePayrollModal .form-control,#recordRepaymentModal .form-control{width:100%;height:42px;padding:0 12px;font-size:13px;color:#0f172a;background:#fff;border:1px solid #e2e8f0;border-radius:8px;outline:none;box-shadow:none;transition:all .15s ease;}
    #approvalRequestModal select.form-control{padding:0 30px 0 12px;appearance:none;cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='%2394a3b8' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;}
    #approvalRequestModal textarea.form-control,#approvePayrollModal textarea.form-control,#recordRepaymentModal textarea.form-control{height:auto;padding:10px 12px;resize:vertical;min-height:80px;line-height:1.5;}
    #approvalRequestModal .form-control:focus{border-color:#1e3a5f;box-shadow:0 0 0 3px rgba(30,58,95,.10);}
    #approvePayrollModal .form-control:focus{border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,.12);}
    #recordRepaymentModal .form-control:focus{border-color:#475569;box-shadow:0 0 0 3px rgba(71,85,105,.12);}
    #approvalRequestModal .form-control[readonly],#approvePayrollModal .form-control[readonly],#recordRepaymentModal .form-control[readonly]{background:#f1f5f9;color:#64748b;cursor:not-allowed;}
    .approve-channels{display:flex;gap:8px;flex-wrap:wrap;}
    .approve-channel{flex:1;min-width:110px;display:flex;align-items:center;gap:9px;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;cursor:pointer;font-weight:600;color:#475569;background:#fff;transition:all .15s ease;margin:0;font-size:13px;user-select:none;}
    .approve-channel:hover{border-color:#cbd5e1;background:#f8fafc;}
    .approve-channel input[type="checkbox"]{margin:0;cursor:pointer;}
    .approve-channel:has(input:checked){color:#000;box-shadow:0 0 0 3px rgba(16,185,129,.10);}
    .approve-channel i{font-size:14px;}
    .approve-channel:has(input:checked) i{color:#398edd;}
    #approvePayrollModal .approve-channel.decision-accepted:has(input:checked){border-color:#10b981;background:#ecfdf5;color:#047857;box-shadow:0 0 0 3px rgba(16,185,129,.10);}
    #approvePayrollModal .approve-channel.decision-accepted:has(input:checked) i{color:#047857;}
    #approvePayrollModal .approve-channel.decision-rejected:has(input:checked){border-color:#ef4444;background:#fef2f2;color:#b91c1c;box-shadow:0 0 0 3px rgba(239,68,68,.10);}
    #approvePayrollModal .approve-channel.decision-rejected:has(input:checked) i{color:#b91c1c;}
    .text-right{text-align:right;}
    .text-center{text-align:center;}
    .m-t-30{margin-top:30px;}
    .m-t-2{margin-top:20px;}
    .m-b-2{margin-bottom:20px;}

    /* ===================================================================
       EMPLOYEE LOANS — SLATE / GRAPHITE THEME
       =================================================================== */
    .loan-shell{background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.04),0 1px 2px rgba(15,23,42,.03);}
    .loan-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:#e5e7eb;border-bottom:1px solid #e5e7eb;}
    @media (max-width:991px){ .loan-stats{grid-template-columns:repeat(2,1fr);} }
    @media (max-width:575px){ .loan-stats{grid-template-columns:1fr;} }
    .loan-stat{background:#fff;padding:18px 22px;}
    .loan-stat-label{font-size:10.5px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:#94a3b8;margin:0 0 6px;display:flex;align-items:center;gap:6px;}
    .loan-stat-label i{font-size:11px;color:#cbd5e1;}
    .loan-stat-value{font-family:'SFMono-Regular',Consolas,monospace;font-size:20px;font-weight:700;color:#0f172a;letter-spacing:-.02em;line-height:1.15;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .loan-stat-sub{font-size:11.5px;color:#94a3b8;margin-top:4px;}
    .loan-stat.is-active .loan-stat-value{color:#475569;}
    .loan-stat.is-repaid .loan-stat-value{color:#334155;}
    .loan-stat.is-outstanding .loan-stat-value{color:#b45309;}

    .loan-search-wrap{position:relative;}
    .loan-search-wrap .loan-search-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;}
    .loan-search-wrap input{height:34px;width:240px;padding:0 14px 0 36px;font-size:12.5px;color:#0f172a;background:#fff;border:1px solid #e2e8f0;border-radius:7px;outline:none;transition:all .15s ease;}
    .loan-search-wrap input:focus{border-color:#475569;box-shadow:0 0 0 3px rgba(71,85,105,.12);}

    .loan-progress{height:6px;background:#f1f5f9;border-radius:999px;overflow:hidden;}
    .loan-progress-fill{height:100%;border-radius:999px;background:linear-gradient(90deg, #47e727, #1bd124);transition:width .4s ease;}

    #loanRepaymentModal .modal-content{border:none;border-radius:16px;overflow:hidden;box-shadow:0 24px 60px rgba(15,23,42,.24);}
    #loanRepaymentModal .modal-header{background:linear-gradient(135deg, #6b88ca 0%, #7295cc 55%, #6a9fe9 100%);padding:20px 24px;border:none;position:relative;overflow:hidden;}
    #loanRepaymentModal .modal-header::before{content:"";position:absolute;right:-40px;top:-40px;width:160px;height:160px;border-radius:50%;background:rgba(251,191,36,.08);}
    #loanRepaymentModal .modal-header::after{content:"";position:absolute;left:20%;bottom:-60px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.04);}
    #loanRepaymentModal .modal-header h4{margin:0;font-size:17px;font-weight:700;color:#fff;display:flex;align-items:center;gap:10px;letter-spacing:.01em;position:relative;z-index:1;}
    #loanRepaymentModal .modal-header h4 i{color:#fbbf24;font-size:17px;text-shadow:0 0 12px rgba(251,191,36,.4);}
    #loanRepaymentModal .modal-header .close{color:#fff;opacity:.85;font-size:26px;margin-top:-4px;text-shadow:none;position:relative;z-index:1;}
    #loanRepaymentModal .modal-header .close:hover{opacity:1;}
    #loanRepaymentModal .modal-body{padding:0;background:#f8fafc;}

    .lr-search-bar{padding:18px 24px;background:#fff;border-bottom:1px solid #e5e7eb;}
    .lr-search-wrap{position:relative;}
    .lr-search-wrap .lr-search-icon{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:14px;}
    .lr-search-wrap input{width:100%;height:46px;padding:0 16px 0 44px;font-size:14px;color:#0f172a;background:#fff;border:1px solid #e2e8f0;border-radius:10px;outline:none;transition:all .18s ease;}
    .lr-search-wrap input:focus{border-color:#475569;box-shadow:0 0 0 4px rgba(71,85,105,.12);}
    .lr-search-wrap input::placeholder{color:#94a3b8;}

    .lr-body{display:grid;grid-template-columns:320px 1fr;min-height:480px;max-height:640px;}
    @media (max-width:768px){ .lr-body{grid-template-columns:1fr;} }

    .lr-emp-list{background:#fff;border-right:1px solid #e5e7eb;overflow-y:auto;padding:10px;}
    .lr-emp-item{padding:14px 16px;border-radius:10px;cursor:pointer;border:1px solid transparent;transition:all .18s ease;margin-bottom:6px;position:relative;}
    .lr-emp-item:hover{background:#f8fafc;border-color:#e2e8f0;}
    .lr-emp-item.is-active{background:linear-gradient(135deg,#f1f5f9,#e2e8f0);border-color:#cbd5e1;box-shadow:0 1px 3px rgba(71,85,105,.10);}
    .lr-emp-item-name{font-size:13px;font-weight:700;color:#0f172a;margin:0 0 6px;display:flex;align-items:center;gap:6px;}
    .lr-emp-item-name i{color:#64748b;font-size:11px;}
    .lr-emp-item.is-active .lr-emp-item-name{color:#1e293b;}
    .lr-emp-item.is-active .lr-emp-item-name i{color:#475569;}
    .lr-emp-item-meta{font-size:11px;color:#64748b;display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
    .lr-emp-mini-bar{height:4px;background:#f1f5f9;border-radius:999px;overflow:hidden;margin-top:8px;}
    .lr-emp-mini-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,#64748b,#334155);}
    .lr-emp-badge{padding:2px 7px;border-radius:5px;font-weight:700;font-size:10.5px;}
    .lr-emp-badge.rp-badge-active{background:#fff7ed;color:#c2410c;}
    .lr-emp-badge.rp-badge-repaid{background:#f1f5f9;color:#334155;}
    .lr-emp-badge.rp-badge-mixed{background:#eff6ff;color:#1d4ed8;}

    .lr-empty{padding:30px 20px;text-align:center;color:#94a3b8;font-size:12.5px;}

    .lr-detail{padding:24px;overflow-y:auto;max-height:640px;}

    .lr-hero{
        background:linear-gradient(135deg,#f1f5f9,#e2e8f0);
        border-radius:14px;padding:22px;color:#fff;position:relative;overflow:hidden;margin-bottom:20px;}
    .lr-hero::before{content:"";position:absolute;right:-40px;top:-40px;width:180px;height:180px;border-radius:50%;background:rgba(251,191,36,.10);}
    .lr-hero::after{content:"";position:absolute;right:40px;bottom:-60px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.04);}
    .lr-avatar{width:54px;height:54px;border-radius:14px;background:rgba(177, 168, 168, 0.15);display:inline-flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color: #2fbb1c; margin-bottom:12px;letter-spacing:-.02em;border:1px solid rgba(148,163,184,.5);box-shadow:0 4px 12px rgba(0,0,0,.15);}
    .lr-name{font-size:17px;font-weight:700;margin:0 0 4px;letter-spacing:-.01em;}
    .lr-role{font-size:11.5px;color:#000000;font-weight:600;text-transform:uppercase;letter-spacing:.08em;}
    .lr-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:18px;position:relative;z-index:1;}
    .lr-stat{background:#dddddd;border:1px solid rgba(255,255,255,.15);border-radius:10px;padding:12px 14px;backdrop-filter:blur(10px);}
    .lr-stat-label{font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color: #0c2649;margin:0 0 5px;}
    .lr-stat-value{font-family:'SFMono-Regular',Consolas,monospace;font-size:14.5px;font-weight:700;color:#000;font-variant-numeric:tabular-nums;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .lr-stat.lr-stat-outstanding .lr-stat-value{color: #974d10;}

    .lr-progress-section{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;margin-bottom:16px;}
    .lr-progress-label{display:flex;justify-content:space-between;align-items:center;font-size:11.5px;font-weight:700;margin-bottom:10px;text-transform:uppercase;letter-spacing:.06em;color:#64748b;}
    .lr-progress-label strong{color:#334155;font-size:14px;letter-spacing:0;font-family:'SFMono-Regular',Consolas,monospace;}
    .lr-progress-track{height:10px;background:#f1f5f9;border-radius:999px;overflow:hidden;position:relative;}
    .lr-progress-fill{height:100%;border-radius:999px;background:linear-gradient(90deg, #53d617, #6eb96b 70%, #fbbf24);transition:width .5s ease;box-shadow:inset 0 1px 0 rgba(255,255,255,.25);}
    .lr-progress-meta{display:flex;justify-content:space-between;font-size:11px;color:#94a3b8;margin-top:6px;font-family:'SFMono-Regular',Consolas,monospace;}

    .lr-loans-title{font-size:13px;font-weight:700;color:#0f172a;margin:0 0 12px;display:flex;align-items:center;gap:8px;}
    .lr-loans-title i{color:#475569;font-size:12px;}
    .lr-loans-title span{font-size:11px;color:#94a3b8;font-weight:500;margin-left:auto;}

    .lr-loan-card{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px;margin-bottom:10px;transition:all .15s ease;position:relative;overflow:hidden;}
    .lr-loan-card:hover{border-color:#cbd5e1;box-shadow:0 2px 8px rgba(71,85,105,.08);}
    .lr-loan-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(180deg,#334155,#64748b);}
    .lr-loan-card.is-repaid::before{background:linear-gradient(180deg,#0f172a,#1e293b);}
    .lr-loan-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;gap:8px;}
    .lr-loan-type{font-size:12.5px;font-weight:700;color:#0f172a;text-transform:capitalize;display:flex;align-items:center;gap:6px;}
    .lr-loan-type i{color:#475569;font-size:11px;}
    .lr-loan-head-right{display:flex;align-items:center;gap:8px;flex-shrink:0;}
    .lr-pdf-link{
        display:inline-flex;align-items:center;justify-content:center;
        padding:2px 10px 2px 10px;
        height:26px;
        border-radius:6px;background:#f8fafc;color:#475569;text-decoration:none;font-size:11px;
        transition:all .15s ease;border:1px solid #e2e8f0;
    }
    .lr-pdf-link:hover{
        background: #b0bdc0;
        color: #fff;
        border-color: #e6e1e1;text-decoration:none;}
    .lr-loan-status{padding:3px 9px;border-radius:5px;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;}
    .lr-loan-status.is-active{background:#fef3c7;color:#92400e;}
    .lr-loan-status.is-repaid{background:#f1f5f9;color:#334155;}
    .lr-loan-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:10px;}
    .lr-loan-field{font-size:11px;}
    .lr-loan-field-label{color:#94a3b8;font-weight:600;letter-spacing:.03em;text-transform:uppercase;margin:0 0 3px;font-size:9.5px;}
    .lr-loan-field-value{color:#0f172a;font-weight:700;font-family:'SFMono-Regular',Consolas,monospace;font-size:12px;font-variant-numeric:tabular-nums;}
    .lr-loan-progress{height:5px;background:#f1f5f9;border-radius:999px;overflow:hidden;}
    .lr-loan-progress-fill{height:100%;background:linear-gradient(90deg, #0dbe25, #5bc912);border-radius:999px;}
    .lr-loan-card.is-repaid .lr-loan-progress-fill{background:linear-gradient(90deg,#0f172a,#334155);}
    .lr-loan-progress-meta{display:flex;justify-content:space-between;font-size:10.5px;color:#64748b;margin-top:6px;font-family:'SFMono-Regular',Consolas,monospace;}

    /* Actions in the loans table */
    .loan-act-detail{background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;}
    .loan-act-detail:hover{background:#dbeafe;color:#1e40af;}
    .loan-act-history{background:linear-gradient(180deg, #628dca, #628dca); color:#fff;border-color: #7daff5;}
    .loan-act-history:hover{background:linear-gradient(180deg,#334155,#1e293b);color:#fff;border-color:#1e293b;}
    .loan-act-edit{background:#fef3c7;color:#92400e;border-color:#fde68a;}
    .loan-act-edit:hover{background:#fde68a;color:#78350f;border-color:#fbbf24;}
    .loan-act-repay{background:#f1f5f9;color:#334155;border-color:#cbd5e1;}
    .loan-act-repay:hover{background:#e2e8f0;color:#0f172a;border-color:#94a3b8;}
    .loan-act-pdf{background:#f8fafc;color:#475569;border-color:#e2e8f0;}
    .loan-act-pdf:hover{background:#fef2f2;color:#b91c1c;border-color:#fecaca;}

    /* ===================== EMPLOYEE LOANS — expanded sub-table ===================== */
    .loan-emp-row{cursor:pointer;transition:background .12s ease;}
    .loan-emp-row:hover td{background:#f8fafc!important;}
    .expand-loans-btn{background:transparent;border:none;color:#64748b;cursor:pointer;padding:0;
        width:22px;height:22px;display:inline-flex;align-items:center;justify-content:center;
        border-radius:4px;transition:all .15s ease;}
    .expand-loans-btn:hover{background:#e2e8f0;color:#0f172a;}
    .expand-loans-btn i{transition:transform .2s ease;font-size:11px;}
    .expand-loans-btn.is-open i{transform:rotate(90deg);}
    .loan-detail-row td{background:#f8fafc!important;border-bottom:1px solid #e5e7eb!important;}
    .loan-detail-row table{margin:0;width:100%;border-collapse:collapse;}
    .loan-detail-row table thead th{background:#f1f5f9;font-size:10px;text-transform:uppercase;
        letter-spacing:.06em;color:#64748b;padding:8px 12px;border:none;font-weight:700;text-align:left;}
    .loan-detail-row table tbody td{padding:10px 12px;border-bottom:1px solid #e5e7eb;
        font-size:12px;background:#fff;color:#334155;}
    .loan-detail-row table tbody tr:last-child td{border-bottom:none;}
    .loan-detail-row .loan-act{height:26px;font-size:11px;padding:0 8px;}

    /* ===================== LOAN STATEMENT (modal content) ===================== */
    .loan-statement-wrap{padding:4px 2px 16px;}
    .loan-statement-header{text-align:center;padding-bottom:16px;border-bottom:2px solid #1e293b;
        margin-bottom:20px;}
    .loan-statement-header h3{margin:0 0 6px;font-size:20px;font-weight:700;color:#0f172a;letter-spacing:-.01em;}
    .loan-statement-header h4{margin:0 0 4px;font-size:13px;font-weight:600;color:#475569;
        text-transform:uppercase;letter-spacing:.10em;}
    .loan-statement-header p{margin:0;font-size:11.5px;color:#94a3b8;}
    .loan-statement-table{width:100%;border-collapse:separate;border-spacing:0;
        border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;}
    .loan-statement-table th{background:#f8fafc;color:#64748b;font-size:11px;font-weight:700;
        text-transform:uppercase;letter-spacing:.06em;padding:10px 14px;text-align:left;
        border-bottom:1px solid #e5e7eb;width:42%;}
    .loan-statement-table td{padding:11px 14px;font-size:13px;color:#0f172a;border-bottom:1px solid #f1f5f9;}
    .loan-statement-table tr:last-child th,.loan-statement-table tr:last-child td{border-bottom:none;}
    .loan-statement-table td.is-num{font-family:'SFMono-Regular',Consolas,monospace;
        font-weight:700;text-align:right;font-variant-numeric:tabular-nums;}
    .loan-statement-table td.is-amount{color:#334155;font-weight:700;}
    .loan-statement-table td.is-outstanding{color:#b45309;font-weight:700;}
    .loan-statement-progress{height:8px;background:#f1f5f9;border-radius:999px;
        overflow:hidden;margin-top:8px;}
    .loan-statement-progress-fill{height:100%;
        background:linear-gradient(90deg,#334155,#64748b);border-radius:999px;transition:width .4s ease;}

    /* ============ Loan history — date filter ============ */
    .lr-date-filter{display:flex;align-items:center;gap:10px;padding:12px 16px;background:#fff;
        border:1px solid #e5e7eb;border-radius:10px;margin-bottom:16px;flex-wrap:wrap;}
    .lr-date-filter label{font-size:11.5px;font-weight:700;color:#475569;
        text-transform:uppercase;letter-spacing:.05em;margin:0;}
    .lr-date-filter input[type="date"]{height:34px;padding:0 10px;font-size:12.5px;color:#0f172a;
        background:#fff;border:1px solid #e2e8f0;border-radius:7px;outline:none;transition:all .15s ease;}
    .lr-date-filter input[type="date"]:focus{border-color:#475569;box-shadow:0 0 0 3px rgba(71,85,105,.12);}
    .lr-date-clear{height:34px;padding:0 12px;font-size:12px;font-weight:600;color:#64748b;
        background:#fff;border:1px solid #e2e8f0;border-radius:7px;cursor:pointer;transition:all .15s ease;}
    .lr-date-clear:hover{background:#f1f5f9;color:#0f172a;border-color:#cbd5e1;}
    .lr-date-summary{font-size:11.5px;color:#334155;font-weight:700;padding:4px 10px;
        background:#f1f5f9;border-radius:999px;margin-left:auto;}

    /* ============ Repayment ledger inside each loan card ============ */
    .lr-repayments{margin-top:12px;border-top:1px dashed #e5e7eb;padding-top:12px;}
    .lr-repayments-title{font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;
        letter-spacing:.06em;margin-bottom:8px;display:flex;justify-content:space-between;align-items:center;}
    .lr-repayments-title span:last-child{color:#94a3b8;font-weight:600;text-transform:none;
        letter-spacing:0;font-size:10.5px;}
    .lr-repayments-table{width:100%;border-collapse:separate;border-spacing:0;
        border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;font-size:11.5px;}
    .lr-repayments-table thead th{background:#f8fafc;color:#64748b;font-size:9.5px;font-weight:700;
        text-transform:uppercase;letter-spacing:.05em;padding:8px 12px;text-align:left;
        border-bottom:1px solid #e5e7eb;}
    .lr-repayments-table tbody td{padding:9px 12px;border-bottom:1px solid #f1f5f9;color:#334155;}
    .lr-repayments-table tbody tr:last-child td{border-bottom:none;}
    .lr-repayments-table .is-amount{font-family:'SFMono-Regular',Consolas,monospace;
        font-weight:700;color:#334155;text-align:right;font-variant-numeric:tabular-nums;}
    .lr-repayments-empty{padding:14px;text-align:center;color:#94a3b8;font-size:11.5px;
        font-style:italic;background:#f8fafc;border-radius:8px;}
</style>

@php
    $pendingRequestCount = $pendingRequestCount ?? 0;
    $recentRequests      = $recentRequests      ?? collect();
    $pendingInbox        = $pendingInbox        ?? collect();

    $currentMonthApproval = \App\Models\PayrollRequest::forBranch(auth()->user()->branch_id)
        ->where('type', 'monthly_salary')
        ->where('period_year', now()->year)
        ->where('period_month', now()->month)
        ->latest()->first();

    $salaryStats = [
        'total'   => $months->where('exists', true)->count(),
        'paid'    => $months->filter(fn($m) => $m['exists'] && $m['total_count'] > 0 && $m['paid_count'] >= $m['total_count'])->count(),
        'partial' => $months->filter(fn($m) => $m['exists'] && $m['paid_count'] > 0 && $m['paid_count'] < $m['total_count'])->count(),
        'unpaid'  => $months->filter(fn($m) => $m['exists'] && $m['paid_count'] == 0 && $m['total_count'] > 0)->count(),
    ];

    $salaryApprovalMap = \App\Models\PayrollRequest::forBranch(auth()->user()->branch_id)
        ->where('type', 'monthly_salary')
        ->whereIn('status', ['pending', 'approved', 'rejected', 'cancelled'])
        ->orderByDesc('id')->get()
        ->keyBy(fn($r) => $r->period_year . '-' . str_pad($r->period_month, 2, '0', STR_PAD_LEFT));

    $advanceGroups = collect($salary_advances ?? [])
        ->filter(fn($a) => !empty($a->month ?? $a->created_at ?? null))
        ->groupBy(fn($a) => \Carbon\Carbon::parse($a->month ?? $a->created_at)->format('Y-m'))
        ->map(function ($items, $month) {
            $totalCount = $items->count();
            $paidCount  = $items->filter(fn($a) => ($a->is_paid ?? false) || in_array($a->status ?? '', ['paid']))->count();
            $empCount   = $items->pluck('employee_id')->filter()->unique()->count();
            return [
                'month'          => $month,
                'total_count'    => $totalCount,
                'paid_count'     => $paidCount,
                'employee_count' => $empCount ?: $totalCount,
                'total_amount'   => (float) $items->sum('amount'),
                'reference'      => 'ADV-' . str_replace('-', '', $month),
                'advances'       => $items->values(),
            ];
        })
        ->sortKeysDesc();

    $advanceApprovalMap = \App\Models\PayrollRequest::forBranch(auth()->user()->branch_id)
        ->where('type', 'salary_advance')
        ->whereIn('status', ['pending', 'approved', 'rejected', 'cancelled'])
        ->orderByDesc('id')->get()
        ->keyBy(fn($r) => $r->period_year . '-' . str_pad($r->period_month, 2, '0', STR_PAD_LEFT));

    $advanceStats = [
        'total'   => $advanceGroups->count(),
        'paid'    => $advanceGroups->filter(fn($g) => $g['total_count'] > 0 && $g['paid_count'] >= $g['total_count'])->count(),
        'partial' => $advanceGroups->filter(fn($g) => $g['paid_count'] > 0 && $g['paid_count'] < $g['total_count'])->count(),
        'unpaid'  => $advanceGroups->filter(fn($g) => $g['paid_count'] == 0 && $g['total_count'] > 0)->count(),
    ];

    /* ---------- Loans: balance derived from loan_repayments ---------- */
    $loansWithBalance = collect($loans ?? [])->map(function ($l) {
        $repayments = method_exists($l, 'loanRepayments') ? $l->loanRepayments : collect();

        $principal = (float) ($l->principal ?? 0);
        $repaid    = (float) $repayments->sum('amount');
        $balance   = max(0, $principal - $repaid);
        $progress  = $principal > 0 ? min(100, ($repaid / $principal) * 100) : 0;

        return [
            'loan'       => $l,
            'principal'  => $principal,
            'repaid'     => $repaid,
            'balance'    => $balance,
            'progress'   => $progress,
            'repayments' => $repayments,
        ];
    });

    $loanStats = [
        'total'       => $loansWithBalance->count(),
        'active'      => $loansWithBalance->filter(fn($x) => $x['balance'] > 0)->count(),
        'repaid'      => $loansWithBalance->filter(fn($x) => $x['balance'] <= 0)->count(),
        'outstanding' => $loansWithBalance->sum('balance'),
    ];

    $loanByEmployee = $loansWithBalance
        ->filter(fn($x) => !empty(optional($x['loan']->employee)->id))
        ->groupBy(fn($x) => optional($x['loan']->employee)->id)
        ->map(function ($items, $employeeId) {
            $employee = optional($items->first()['loan']->employee);

            $loansArr = $items->map(function ($item) {
                $l = $item['loan'];

                $repayments = collect($item['repayments'])->map(function ($r) {
                    $rawDate = $r->repayment_date ?? $r->payment_date ?? $r->paid_at ?? $r->created_at;
                    return [
                        'id'       => $r->id,
                        'amount'   => (float) $r->amount,
                        'date'     => $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d M Y') : '—',
                        'date_raw' => $rawDate ? \Carbon\Carbon::parse($rawDate)->format('Y-m-d') : null,
                        'notes'    => $r->notes ?? '',
                    ];
                })->sortByDesc('date_raw')->values();

                return [
                    'id'                => $l->id,
                    'type'              => $l->type ?? 'Loan',
                    'principal'         => $item['principal'],
                    'repaid'            => $item['repaid'],
                    'balance'           => $item['balance'],
                    'progress'          => $item['progress'],
                    'monthly_deduction' => (float) ($l->monthly_deduction ?? 0),
                    'start_date'        => $l->start_date
                        ? \Carbon\Carbon::parse($l->start_date)->format('M Y')
                        : '—',
                    'repayments'        => $repayments,
                ];
            })->values();

            return [
                'employee_id'     => (int) $employeeId,
                'employee_name'   => trim(
                    ($employee->firstname ?? '') . ' ' . ($employee->surname ?? '')
                ) ?: ('Employee #' . $employeeId),
                'total_principal' => $loansArr->sum('principal'),
                'total_repaid'    => $loansArr->sum('repaid'),
                'total_balance'   => $loansArr->sum('balance'),
                'monthly_deduct'  => $loansArr->sum('monthly_deduction'),
                'loan_count'      => $loansArr->count(),
                'loans'           => $loansArr,
            ];
        })
        ->sortBy('employee_name')
        ->values();

    $loanStatements = $loansWithBalance->mapWithKeys(function ($item) {
        $l       = $item['loan'];
        $empName = trim(
            (optional($l->employee)->firstname ?? '') . ' ' .
            (optional($l->employee)->surname ?? '')
        );

        return [$l->id => [
            'id'                => $l->id,
            'employee_name'     => $empName ?: ('Employee #' . optional($l->employee)->id),
            'type'              => $l->type ?? 'Loan',
            'principal'         => $item['principal'],
            'repaid'            => $item['repaid'],
            'balance'           => $item['balance'],
            'progress'          => $item['progress'],
            'monthly_deduction' => (float) ($l->monthly_deduction ?? 0),
            'start_date'        => $l->start_date
                ? \Carbon\Carbon::parse($l->start_date)->format('d M Y')
                : '—',
            'repayment_count'   => collect($item['repayments'])->count(),
        ]];
    });

    $managingDirector = \App\Models\User::where('role_id', 2)->first();
    $rolesList        = \App\Models\Role::orderBy('id')->get();
@endphp

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="step-indicator" style="float: right">
                <a class="step completed" href="{{ url('home') }}">Home</a>
                <a class="step" href="#">Payroll</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-custom panel-border">
                <div class="panel-heading"><h4 class="pull-left">Payrolls</h4></div>
                <br/><br/>

                <div class="panel-body">
                    <ul class="nav nav-tabs" role="tablist" style="margin-bottom:15px;">
                        <li role="presentation" class="active"><a href="#overview" aria-controls="overview" role="tab" data-toggle="tab">Overviews</a></li>
                        <li role="presentation"><a href="#salary" aria-controls="salary" role="tab" data-toggle="tab">Salary</a></li>
                        <li role="presentation"><a href="#salarySlip" aria-controls="salary-slip" role="tab" data-toggle="tab">Salary Slip</a></li>
                        <li role="presentation"><a href="#salary-advances" aria-controls="salary-advances" role="tab" data-toggle="tab">Salary Advance</a></li>
                        <li role="presentation"><a href="#loan-details" aria-controls="loan-details" role="tab" data-toggle="tab">Employee Loans</a></li>
                    </ul>

                    <div class="tab-content">

                        {{-- ============ OVERVIEW ============ --}}
                        <div role="tabpanel" class="tab-pane fade in active" id="overview">
                            <div class="row mb-4 m-t-2" style="background:#ddd;padding:10px;">
                                <div class="col-md-2">
                                    <label for="filterMonth">Month</label>
                                    <select id="filterMonth" class="form-control">
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $m == now()->month ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filterYear">Year</label>
                                    <select id="filterYear" class="form-control">
                                        @for ($y = now()->year; $y <= now()->year + 5; $y++)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <br/>
                            <div class="row mb-4">
                                <div class="col-md-3"><div class="card text-center shadow-sm"><div class="card-body"><h5 class="card-title">Total Employees</h5><p class="card-text display-4">{{ $totalEmployees }}</p></div></div></div>
                                <div class="col-md-3"><div class="card text-center shadow-sm"><div class="card-body"><h5 class="card-title">This Month's Payroll</h5><p class="card-text display-4">TZS {{ number_format($currentMonthPayroll, 2) }}</p></div></div></div>
                                <div class="col-md-3"><div class="card text-center shadow-sm"><div class="card-body"><h5 class="card-title">Pending Requests</h5><p class="card-text display-4">{{ $pendingRequestCount }}</p></div></div></div>
                                <div class="col-md-3"><div class="card text-center shadow-sm"><div class="card-body"><h5 class="card-title">Approved This Month</h5><p class="card-text display-4">{{ \App\Models\PayrollRequest::forBranch(auth()->user()->branch_id)->where('status','approved')->whereMonth('approved_at', now()->month)->whereYear('approved_at', now()->year)->count() }}</p></div></div></div>
                            </div>
                        </div>

                        {{-- ============ SALARY ============ --}}
                        <div role="tabpanel" class="tab-pane fade" id="salary">
                            @php
                                $currentMonthRow = $months->firstWhere('month', now()->format('Y-m'));
                                $currentMonthFullyPaid = $currentMonthRow && ($currentMonthRow['exists'] ?? false) && ($currentMonthRow['total_count'] ?? 0) > 0 && ($currentMonthRow['paid_count'] ?? 0) >= ($currentMonthRow['total_count'] ?? 0);
                            @endphp
                            @if($currentMonthFullyPaid)
                            @elseif(!$currentMonthApproval)
                            @elseif($currentMonthApproval->status === 'pending')
                                <div class="pr-approval-banner pr-pending"><i class="fa fa-clock-o"></i><span>{{ now()->format('F Y') }} Payroll request is <strong>Pending approval</strong>.</span></div>
                            @elseif($currentMonthApproval->status === 'approved')
                                <div class="pr-approval-banner pr-approved"><i class="fa fa-check-circle"></i><span>{{ now()->format('F Y') }} Payroll request is <strong>approved</strong> — ready for Payment.</span></div>
                            @elseif($currentMonthApproval->status === 'rejected')
                                <div class="pr-approval-banner pr-missing"><i class="fa fa-times-circle"></i><span>{{ now()->format('F Y') }} payroll request was <strong>rejected</strong>: {{ $currentMonthApproval->rejection_reason }}</span></div>
                            @endif

                            <div class="salary-shell">
                                <div class="salary-stats">
                                    <div class="salary-stat"><p class="salary-stat-label"><i class="fa fa-calendar"></i> Months Generated</p><div class="salary-stat-value">{{ $salaryStats['total'] }}</div><div class="salary-stat-sub">Payroll runs on record</div></div>
                                    <div class="salary-stat is-paid"><p class="salary-stat-label"><i class="fa fa-check-circle"></i> Fully Paid</p><div class="salary-stat-value">{{ $salaryStats['paid'] }}</div><div class="salary-stat-sub">All employees settled</div></div>
                                    <div class="salary-stat is-partial"><p class="salary-stat-label"><i class="fa fa-adjust"></i> Partial</p><div class="salary-stat-value">{{ $salaryStats['partial'] }}</div><div class="salary-stat-sub">Some employees unpaid</div></div>
                                    <div class="salary-stat is-unpaid"><p class="salary-stat-label"><i class="fa fa-hourglass-half"></i> Unpaid</p><div class="salary-stat-value">{{ $salaryStats['unpaid'] }}</div><div class="salary-stat-sub">Awaiting payment</div></div>
                                </div>

                                <div class="salary-toolbar">
                                    <div class="salary-filter">
                                        <label><i class="fa fa-filter" style="margin-right:4px;color:#cbd5e1;"></i> Filter</label>
                                        <select id="salaryFilterMonth">
                                            <option value="">All Months</option>
                                            @for ($m = 1; $m <= 12; $m++)
                                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                                            @endfor
                                        </select>
                                        <select id="salaryFilterYear">
                                            <option value="">All Years</option>
                                            @for ($y = now()->year - 5; $y <= now()->year + 1; $y++)
                                                <option value="{{ $y }}" {{ $y === now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                        <button type="button" class="salary-filter-reset" id="salaryFilterReset"><i class="fa fa-refresh"></i> Reset</button>
                                        <span id="salaryFilterCount" class="salary-filter-count" style="display:none;"></span>
                                    </div>
                                    <button type="button" class="salary-btn-generate" data-toggle="modal" data-target="#generatePayrollModal"><i class="fa fa-cogs"></i> Generate Payroll</button>
                                </div>

                                <div class="modal fade" id="generatePayrollModal" tabindex="-1" role="dialog">
                                    <div class="modal-dialog modal-lg" style="width:1000px;"><div class="modal-content">
                                        <form method="POST" action="{{ route('payrolls.store') }}">
                                            @csrf
                                            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Generate Payroll</h4></div>
                                            <div class="modal-body">
                                                <div class="form-group"><label for="payroll-month">Payroll Month</label><input type="month" name="month" id="payroll-month" class="form-control" required style="width:200px;"></div>
                                                <h4>Select Employees and Contributions</h4>
                                                <table class="table table-bordered table-striped">
                                                    <thead><tr><th><input type="checkbox" id="select-all"></th><th>Name</th><th>Basic Salary</th><th>Allowance</th><th>Contributions</th></tr></thead>
                                                    <tbody>
                                                        @foreach($employees as $employee)
                                                            <tr>
                                                                <td><input type="checkbox" name="employees[{{ $employee->id }}][selected]" class="employee-checkbox" data-employee-id="{{ $employee->id }}"></td>
                                                                <td>{{ $employee->firstname }} {{ $employee->middlename }} {{ $employee->surname }}</td>
                                                                <td>{{ number_format($employee->basic_salary, 2) }}<input type="hidden" name="employees[{{ $employee->id }}][basic_salary]" value="{{ $employee->basic_salary }}"></td>
                                                                <td><input type="text" name="employees[{{ $employee->id }}][allowance]" value="{{ $employee->allowance }}" class="form-control allowance-field" style="width:100px;" data-employee-id="{{ $employee->id }}" placeholder="e.g. 2000"></td>
                                                                <td width="400px;"><div class="contributions-container" data-employee-id="{{ $employee->id }}"><em>Select employee to load contributions</em></div></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer"><button type="submit" class="btn btn-success">Generate Payroll</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button></div>
                                        </form>
                                    </div></div>
                                </div>

                                <div class="salary-table-wrap">
                                    <table class="salary-table" id="salaryTable">
                                        <thead><tr><th>Month</th><th class="text-center">Employees</th><th class="text-right">Gross Salary</th><th class="text-right">NSSF</th><th class="text-right">NHIF</th><th>Payment Status</th><th>Actions</th></tr></thead>
                                        <tbody>
                                            @foreach($months as $m)
                                                @php
                                                    $paidCount  = $m['paid_count']  ?? 0;
                                                    $totalCount = $m['total_count'] ?? ($m['paid_employees'] ?? 0);
                                                    $isFullyPaid = $m['exists'] && $totalCount > 0 && $paidCount >= $totalCount;
                                                    $carbon = \Carbon\Carbon::parse($m['month'].'-01');
                                                    $rowApproval        = $salaryApprovalMap[$m['month']] ?? null;
                                                    $rowApprovalStatus  = $rowApproval->status ?? null;
                                                    $canRequestApproval = !$rowApproval || in_array($rowApprovalStatus, ['rejected', 'cancelled']);
                                                    $isPendingApproval  = $rowApprovalStatus === 'pending';
                                                    $isApprovedRow      = $rowApprovalStatus === 'approved';
                                                    $canApprovePayroll  = in_array((int) auth()->user()->role_id, [1, 2]);
                                                    $showApproveBtn     = $canApprovePayroll && $rowApprovalStatus !== 'approved';
                                                    $requesterName      = $rowApproval && $rowApproval->requester ? trim(($rowApproval->requester->fname ?? '') . ' ' . ($rowApproval->requester->lname ?? '')) : '';
                                                @endphp
                                                <tr data-month="{{ $m['month'] }}">
                                                    <td class="col-month">{{ $carbon->format('M Y') }}<small>{{ $carbon->format('F') }}</small></td>
                                                    <td class="col-employees">@if($m['exists'])<strong>{{ $m['paid_employees'] }}</strong>@else<span style="color:#cbd5e1;">—</span>@endif</td>
                                                    <td class="col-num {{ $m['exists'] ? '' : 'is-muted' }}">{{ $m['exists'] ? number_format($m['total_amount'], 2) : '—' }}</td>
                                                    <td class="col-num {{ $m['exists'] ? '' : 'is-muted' }}">{{ $m['exists'] ? number_format($m['nssf'], 2) : '—' }}</td>
                                                    <td class="col-num {{ $m['exists'] ? '' : 'is-muted' }}">{{ $m['exists'] ? number_format($m['nhif'], 2) : '—' }}</td>
                                                    <td>
                                                        @if(!$m['exists'])<span class="text-muted">—</span>
                                                        @elseif($isFullyPaid)<span class="pr-status pr-status-approved">Fully Paid</span>
                                                        @elseif($paidCount > 0)<span class="pr-status pr-status-pending">Partial ({{ $paidCount }}/{{ $totalCount }})</span>
                                                        @else<span class="pr-status pr-status-rejected">Not Paid</span>@endif
                                                    </td>
                                                    <td>
                                                        @if($m['exists'])
                                                    <div class="salary-actions">
                                                        <button class="sal-act sal-act-pay view-salary-btn" 
                                                                data-reference="{{ $m['reference'] }}" 
                                                                data-month="{{ $m['month'] }}">
                                                            <i class="fa fa-eye"></i> View
                                                        </button>

                                                        {{-- Approve Request (approver only) --}}
                                                        @if($showApproveBtn && Auth::user()->role_id == '2' || Auth::user()->role_id == '1')
                                                            <button type="button" 
                                                                    class="sal-act sal-act-success approve-payroll-btn"
                                                                    data-month="{{ $m['month'] }}"
                                                                    data-amount="{{ $m['total_amount'] }}"
                                                                    data-employees="{{ $m['paid_employees'] }}"
                                                                    data-reference="{{ $m['reference'] }}"
                                                                    data-requester-name="{{ $requesterName }}"
                                                                    data-requester-id="{{ $rowApproval->requested_by ?? '' }}"
                                                                    data-request-id="{{ $rowApproval->id ?? '' }}">
                                                                <i class="fa fa-check-circle"></i> Approve Request
                                                            </button>
                                                        @endif

                                                        {{-- Send for Approval (non-approver) --}}
                                                        @if($canRequestApproval && Auth::user()->role_id != '2')
                                                            <button class="sal-act sal-act-request request-approval-btn"
                                                                    data-month="{{ $m['month'] }}"
                                                                    data-amount="{{ $m['total_amount'] }}"
                                                                    data-employees="{{ $m['paid_employees'] }}">
                                                                <i class="fa fa-paper-plane"></i> Send for Approval
                                                            </button>
                                                        @endif

                                                        {{-- Pending approval badge --}}
                                                        @if(Auth::user()->role_id != '2' && $isPendingApproval)
                                                            <button class="sal-act sal-act-pending" disabled>
                                                                <i class="fa fa-clock-o"></i> Pending Approval
                                                            </button>
                                                        @endif

                                                        {{-- Pay + Vouchers: approved rows only --}}
                                                        @if($isApprovedRow)
                                                            @if(Auth::user()->role_id != '2' && $totalCount > 0 && $paidCount < $totalCount)
                                                                <button class="sal-act sal-act-pay pay-payroll-btn"
                                                                        data-reference="{{ $m['reference'] }}"
                                                                        data-month="{{ $m['month'] }}">
                                                                    <i class="fa fa-money"></i> Pay
                                                                </button>
                                                            @endif
                                                         @endif
                                                            <div class="sal-dropdown">
                                                                <button type="button" class="sal-act sal-act-bank sal-dropdown-toggle">
                                                                    <i class="fa fa-file-text-o"></i> Vouchers 
                                                                    <i class="fa fa-angle-down" style="font-size:9px;margin-left:2px;opacity:.7;"></i>
                                                                </button>
                                                                <ul class="sal-dropdown-menu">
                                                                    <li><a class="view-bank-btn" data-reference="{{ $m['reference'] }}"><i class="fa fa-bank"></i> Bank Voucher</a></li>
                                                                    <li><a class="view-nssf-btn" data-month="{{ $m['month'] }}"><i class="fa fa-file-excel-o"></i> NSSF Voucher</a></li>
                                                                    <li><a class="view-tuico-btn" data-month="{{ $m['month'] }}"><i class="fa fa-file-excel-o"></i> TUICO Voucher</a></li>
                                                                    <li style="height:1px;background:#f1f5f9;margin:4px 0;padding:0;"></li>
                                                                    <li><a class="rollback-btn" data-id="{{ $m['id'] }}" style="color:#b91c1c;"><i class="fa fa-trash" style="color:#b91c1c;"></i> Rollback</a></li>
                                                                </ul>
                                                            </div>
                                                    </div>
                                                @else
                                                    <span class="sal-act-placeholder">Not Generated</span>
                                                @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="modal fade" id="salaryDetailModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document" style="width:1200px;"><div class="modal-content">
                                    <div class="modal-header"><h4 class="modal-title">Salary Details</h4><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                    <div class="modal-body" id="salary-detail-content"><div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div></div>
                                </div></div>
                            </div>
                        </div>

                        {{-- ============ SALARY SLIP ============ --}}
                        <div role="tabpanel" class="tab-pane fade" id="salarySlip">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Employee</label>
                                        <select id="employee_id" class="form-control select2" required>
                                            <option value="">Select Name</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ $employee->firstname }} {{ $employee->middlename }} {{ $employee->surname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3"><label>Month</label><input type="month" id="month" class="form-control" required></div>
                                    <div class="col-md-3 m-t-30"><button id="generate-slip" class="btn btn-success">Generate Salary Slip</button></div>
                                </div>
                                <div id="loading-spinner" class="text-center" style="margin-top:20px;display:none;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
                                <div id="salary-slip-result" style="margin-top:20px;"></div>
                            </div>
                        </div>

                        {{-- ====================================================================
                             EMPLOYEE LOANS — slate theme, balance from loan_repayments, PDF statement
                             ==================================================================== --}}
                        <div role="tabpanel" class="tab-pane fade" id="loan-details">
                            <div class="loan-shell">

                                {{-- Stats --}}
                                <div class="loan-stats">
                                    <div class="loan-stat">
                                        <p class="loan-stat-label"><i class="fa fa-list-ul"></i> Total Loans</p>
                                        <div class="loan-stat-value">{{ $loanStats['total'] }}</div>
                                        <div class="loan-stat-sub">Loan records on file</div>
                                    </div>
                                    <div class="loan-stat is-active">
                                        <p class="loan-stat-label"><i class="fa fa-refresh"></i> Active</p>
                                        <div class="loan-stat-value">{{ $loanStats['active'] }}</div>
                                        <div class="loan-stat-sub">Balance &gt; 0</div>
                                    </div>
                                    <div class="loan-stat is-repaid">
                                        <p class="loan-stat-label"><i class="fa fa-check-circle"></i> Fully Repaid</p>
                                        <div class="loan-stat-value">{{ $loanStats['repaid'] }}</div>
                                        <div class="loan-stat-sub">Balance cleared</div>
                                    </div>
                                    <div class="loan-stat is-outstanding">
                                        <p class="loan-stat-label"><i class="fa fa-hourglass-half"></i> Outstanding</p>
                                        <div class="loan-stat-value">{{ number_format($loanStats['outstanding'], 2) }}</div>
                                        <div class="loan-stat-sub">Total balance</div>
                                    </div>
                                </div>

                                {{-- Toolbar --}}
                                <div class="salary-toolbar">
                                    <div class="salary-filter">
                                        <label><i class="fa fa-search" style="margin-right:4px;color:#cbd5e1;"></i> Search</label>
                                        <div class="loan-search-wrap">
                                            <i class="fa fa-search loan-search-icon"></i>
                                            <input type="text" id="loanSearchInput" placeholder="Employee name…">
                                        </div>
                                        <select id="loanStatusFilter">
                                            <option value="">All Status</option>
                                            <option value="active">Active</option>
                                            <option value="repaid">Fully Repaid</option>
                                        </select>
                                        <button type="button" class="salary-filter-reset" id="loanSearchReset">
                                            <i class="fa fa-refresh"></i> Reset
                                        </button>
                                        <span id="loanFilterCount" class="salary-filter-count" style="display:none;"></span>
                                    </div>

                                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                        <button type="button" class="salary-btn-generate" id="openLoanRepaymentHistoryBtn"
                                                style="background:linear-gradient(180deg, #6091d6, #6091d6);border-color:#6091d6;">
                                            <i class="fa fa-history"></i> Repayment History
                                        </button>
                                        <a href="{{ route('loans.pdf') }}" class="salary-btn-generate"
                                           style="background:#fff;color:#475569;border-color:#e2e8f0;text-decoration:none;">
                                            <i class="fa fa-file-pdf-o"></i> Loan PDF
                                        </a>
                                        <button type="button" class="salary-btn-generate" data-toggle="modal" data-target="#addLoanModal">
                                            <i class="fa fa-plus"></i> Add Loan
                                        </button>
                                    </div>
                                </div>

                                {{-- Table --}}
                                <div class="salary-table-wrap">
                                    <table class="salary-table" id="loansTable">
                                        <thead>
                                            <tr>
                                                <th style="width:34px;"></th>
                                                <th>Employee</th>
                                                <th>Loan Types</th>
                                                <th class="text-right">Total Principal</th>
                                                <th class="text-right">Total Repaid</th>
                                                <th class="text-right">Balance</th>
                                                <th>Progress</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($loanByEmployee as $emp)
                                                @php
                                                    $empProgress  = $emp['total_principal'] > 0
                                                        ? min(100, ($emp['total_repaid'] / $emp['total_principal']) * 100)
                                                        : 0;
                                                    $empTypes     = $emp['loans']->pluck('type')->unique()->implode(', ');
                                                    $empStatusKey = $emp['total_balance'] <= 0 ? 'repaid' : 'active';
                                                @endphp
                                                <tr class="loan-emp-row"
                                                    data-employee-id="{{ $emp['employee_id'] }}"
                                                    data-employee-name="{{ strtolower($emp['employee_name']) }}"
                                                    data-status="{{ $empStatusKey }}">
                                                    <td>
                                                        <button type="button" class="expand-loans-btn"
                                                                data-employee-id="{{ $emp['employee_id'] }}"
                                                                aria-label="Expand loans">
                                                            <i class="fa fa-chevron-right"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <strong style="color:#0f172a;">{{ $emp['employee_name'] }}</strong>
                                                        <br><small style="color:#94a3b8;">{{ $emp['loan_count'] }} loan{{ $emp['loan_count'] != 1 ? 's' : '' }}</small>
                                                    </td>
                                                    <td><span style="color:#475569;text-transform:capitalize;">{{ $empTypes ?: '—' }}</span></td>
                                                    <td class="col-num">{{ number_format($emp['total_principal'], 2) }}</td>
                                                    <td class="col-num" style="color:#334155;">{{ number_format($emp['total_repaid'], 2) }}</td>
                                                    <td class="col-num {{ $emp['total_balance'] > 0 ? '' : 'is-muted' }}">
                                                        {{ number_format($emp['total_balance'], 2) }}
                                                    </td>
                                                    <td style="min-width:130px;">
                                                        <div class="loan-progress">
                                                            <div class="loan-progress-fill" style="width:{{ $empProgress }}%;"></div>
                                                        </div>
                                                        <div style="font-size:10.5px;color:#64748b;margin-top:4px;font-family:'SFMono-Regular',Consolas,monospace;">
                                                            {{ number_format($empProgress, 0) }}%
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="salary-actions">
                                                            <button class="sal-act loan-act-history open-loan-history-btn"
                                                                    data-employee-id="{{ $emp['employee_id'] }}"
                                                                    title="Repayment History">
                                                                <i class="fa fa-history"></i> History
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                {{-- Expandable per-loan rows --}}
                                                <tr class="loan-detail-row" data-parent="{{ $emp['employee_id'] }}" style="display:none;">
                                                    <td colspan="8">
                                                        <table>
                                                            <thead>
                                                                <tr>
                                                                    <th>Loan Type</th>
                                                                    <th style="text-align:right;">Principal</th>
                                                                    <th style="text-align:right;">Repaid</th>
                                                                    <th style="text-align:right;">Balance</th>
                                                                    <th>Start</th>
                                                                    <th style="width:440px;">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($emp['loans'] as $loanRow)
                                                                    <tr>
                                                                        <td style="text-transform:capitalize;">{{ $loanRow['type'] }}</td>
                                                                        <td style="text-align:right;font-family:'SFMono-Regular',Consolas,monospace;">
                                                                            {{ number_format($loanRow['principal'], 2) }}
                                                                        </td>
                                                                        <td style="text-align:right;font-family:'SFMono-Regular',Consolas,monospace;color:#334155;">
                                                                            {{ number_format($loanRow['repaid'], 2) }}
                                                                        </td>
                                                                        <td style="text-align:right;font-family:'SFMono-Regular',Consolas,monospace;{{ $loanRow['balance'] <= 0 ? 'color:#94a3b8;' : '' }}">
                                                                            {{ number_format($loanRow['balance'], 2) }}
                                                                        </td>
                                                                        <td style="color:#64748b;">{{ $loanRow['start_date'] }}</td>
                                                                        <td>
                                                                            <div class="salary-actions">
                                                                                @if($loanRow['balance'] > 0)
                                                                                    <button class="sal-act loan-act-repay record-repayment-btn"
                                                                                            data-loan-id="{{ $loanRow['id'] }}"
                                                                                            data-employee-id="{{ $emp['employee_id'] }}"
                                                                                            data-loan-label="{{ $emp['employee_name'] }} — {{ $loanRow['type'] }}"
                                                                                            data-balance="{{ $loanRow['balance'] }}"
                                                                                            title="Record Repayment">
                                                                                        <i class="fa fa-plus-circle"></i> Repay
                                                                                    </button>
                                                                                @endif
                                                                                <button class="sal-act loan-act-detail"
                                                                                        onclick="loadLoanDetails({{ $loanRow['id'] }})"
                                                                                        data-toggle="modal"
                                                                                        data-target="#detailsLoanModal"
                                                                                        title="Statement">
                                                                                    <i class="fa fa-eye"></i> Print Loan Balance
                                                                                </button>
                                                                                <button class="sal-act loan-act-edit"
                                                                                        data-toggle="modal"
                                                                                        data-target="#editLoanModal{{ $loanRow['id'] }}"
                                                                                        title="Edit">
                                                                                    <i class="fa fa-pencil"></i> Edit
                                                                                </button>
                                                                                <form method="POST" action="{{ route('loans.destroy', $loanRow['id']) }}" class="delete-loan-form" style="display:inline;">
                                                                                    @csrf @method('DELETE')
                                                                                    <button type="button"
                                                                                            class="sal-act sal-act-danger delete-loan-btn"
                                                                                            data-loan-type="{{ $loanRow['type'] }}"
                                                                                            data-employee="{{ $emp['employee_name'] }}"
                                                                                            data-balance="{{ number_format($loanRow['balance'], 2) }}"
                                                                                            title="Delete">
                                                                                        <i class="fa fa-trash"></i>
                                                                                    </button>
                                                                                </form>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted" style="padding:30px;">
                                                        No employee loans recorded yet.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Edit Loan Modals --}}
                            @foreach($loans ?? [] as $loan)
                                <div class="modal fade" id="editLoanModal{{ $loan->id }}">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('loans.update', $loan->id) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-header"><h4>Edit Loan</h4></div>
                                                <div class="modal-body">
                                                    @include('payrolls.loan_form_fields', ['loan' => $loan])
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-success">Update</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Record Repayment Modal --}}
                            <div class="modal fade" id="recordRepaymentModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <form id="recordRepaymentForm" method="POST" action="">
                                        @csrf
                                        <input type="hidden" name="employee_id" id="rrEmployeeId" value="">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4><i class="fa fa-plus-circle"></i> Record Loan Repayment</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="pay-modal-period">
                                                    <span><strong>Loan:</strong> <span id="rrLoanLabel">—</span></span>
                                                    <span id="rrBalanceLabel" style="color:#64748b;font-size:12px;"></span>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Amount (TZS) <span class="req">*</span></label>
                                                    <input type="number" name="amount" id="rrAmount" step="0.01" min="0.01" class="form-control" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Repayment Date <span class="req">*</span></label>
                                                    <input type="date" name="repayment_date" id="rrDate" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                                                </div>
                                                <div class="form-group" style="margin-bottom:0;">
                                                    <label class="control-label">Notes</label>
                                                    <textarea name="notes" class="form-control" rows="2" placeholder="Optional reference…"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary" id="rrSubmitBtn"><i class="fa fa-check"></i> Record Repayment</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Repayment History Modal --}}
                            <div class="modal fade" id="loanRepaymentModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document" style="width:1140px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4><i class="fa fa-history"></i> Employee Loan Repayment History</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="lr-search-bar">
                                                <div class="lr-search-wrap">
                                                    <i class="fa fa-search lr-search-icon"></i>
                                                    <input type="text" id="lrEmployeeSearch" placeholder="Search employee by name…">
                                                </div>
                                            </div>
                                            <div class="lr-body">
                                                <div class="lr-emp-list" id="lrEmployeeList"></div>
                                                <div class="lr-detail" id="lrDetailPane">
                                                    <div class="text-center text-muted" style="padding:60px 20px;">
                                                        <i class="fa fa-user-circle-o" style="font-size:48px;color:#cbd5e1;"></i>
                                                        <p style="margin-top:16px;">Select an employee to view their loan repayment history</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============ SALARY ADVANCE ============ --}}
                        <div role="tabpanel" class="tab-pane fade" id="salary-advances">
                            <div class="salary-shell">
                                <div class="salary-stats">
                                    <div class="salary-stat"><p class="salary-stat-label"><i class="fa fa-calendar"></i> Months with Advances</p><div class="salary-stat-value">{{ $advanceStats['total'] }}</div><div class="salary-stat-sub">Advance runs on record</div></div>
                                    <div class="salary-stat is-paid"><p class="salary-stat-label"><i class="fa fa-check-circle"></i> Fully Paid</p><div class="salary-stat-value">{{ $advanceStats['paid'] }}</div><div class="salary-stat-sub">All advances settled</div></div>
                                    <div class="salary-stat is-partial"><p class="salary-stat-label"><i class="fa fa-adjust"></i> Partial</p><div class="salary-stat-value">{{ $advanceStats['partial'] }}</div><div class="salary-stat-sub">Some advances unpaid</div></div>
                                    <div class="salary-stat is-unpaid"><p class="salary-stat-label"><i class="fa fa-hourglass-half"></i> Unpaid</p><div class="salary-stat-value">{{ $advanceStats['unpaid'] }}</div><div class="salary-stat-sub">Awaiting payment</div></div>
                                </div>

                                <div class="salary-toolbar">
                                    <div class="salary-filter">
                                        <label><i class="fa fa-filter" style="margin-right:4px;color:#cbd5e1;"></i> Filter</label>
                                        <select id="advFilterMonth">
                                            <option value="">All Months</option>
                                            @for ($m = 1; $m <= 12; $m++)
                                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ \DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                                            @endfor
                                        </select>
                                        <select id="advFilterYear">
                                            <option value="">All Years</option>
                                            @for ($y = now()->year - 5; $y <= now()->year + 1; $y++)
                                                <option value="{{ $y }}" {{ $y === now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                        <button type="button" class="salary-filter-reset" id="advFilterReset"><i class="fa fa-refresh"></i> Reset</button>
                                        <span id="advFilterCount" class="salary-filter-count" style="display:none;"></span>
                                    </div>
                                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                        <a href="#" id="advancePdfBtn" target="_blank" class="salary-btn-generate"
                                        style="background:#fff;color:#475569;border-color:#e2e8f0;text-decoration:none;">
                                            <i class="fa fa-file-pdf-o"></i> Advance PDF
                                        </a>
                                        <button type="button" class="salary-btn-generate" data-toggle="modal" data-target="#addAdvanceModal">
                                            <i class="fa fa-plus"></i> Add Salary Advance
                                        </button>
                                    </div>
                                </div>

                                <div class="salary-table-wrap">
                                    <table class="salary-table" id="advanceTable">
                                        <thead><tr><th>Month</th><th class="text-center">Employees</th><th class="text-right">Total Amount</th><th>Payment Status</th><th>Approval</th><th>Actions</th></tr></thead>
                                        <tbody>
                                            @forelse($advanceGroups as $g)
                                                @php
                                                    $carbon      = \Carbon\Carbon::parse($g['month'] . '-01');
                                                    $isFullyPaid = $g['total_count'] > 0 && $g['paid_count'] >= $g['total_count'];
                                                    $rowApproval        = $advanceApprovalMap[$g['month']] ?? null;
                                                    $rowApprovalStatus  = $rowApproval->status ?? null;
                                                    $canRequestApproval = !$rowApproval || in_array($rowApprovalStatus, ['rejected', 'cancelled']);
                                                    $isPendingApproval  = $rowApprovalStatus === 'pending';
                                                    $isApprovedRow      = $rowApprovalStatus === 'approved';
                                                    $canApprove         = in_array((int) auth()->user()->role_id, [1, 2]);
                                                    $showApproveBtn     = $canApprove && $rowApprovalStatus !== 'approved';
                                                    $requesterName      = $rowApproval && $rowApproval->requester ? trim(($rowApproval->requester->fname ?? '') . ' ' . ($rowApproval->requester->lname ?? '')) : '';
                                                    $advancesData = $g['advances']->map(fn($a) => [
                                                        'id'            => $a->id,
                                                        'employee_id'   => $a->employee_id ?? null,
                                                        'employee_name' => trim((optional($a->employee)->firstname ?? '') . ' ' . (optional($a->employee)->surname ?? '')) ?: ('Employee #' . ($a->employee_id ?? '—')),
                                                        'amount'        => (float) $a->amount,
                                                        'is_paid'       => ($a->is_paid ?? false) || in_array($a->status ?? '', ['paid']),
                                                    ])->values();
                                                @endphp
                                                <tr data-month="{{ $g['month'] }}">
                                                    <td class="col-month">{{ $carbon->format('M Y') }}<small>{{ $carbon->format('F') }}</small></td>
                                                    <td class="col-employees"><strong>{{ $g['employee_count'] }}</strong></td>
                                                    <td class="col-num">{{ number_format($g['total_amount'], 2) }}</td>
                                                    <td>
                                                        @if($isFullyPaid)<span class="pr-status pr-status-approved">Fully Paid</span>
                                                        @elseif($g['paid_count'] > 0)<span class="pr-status pr-status-pending">Partial ({{ $g['paid_count'] }}/{{ $g['total_count'] }})</span>
                                                        @else<span class="pr-status pr-status-rejected">Not Paid</span>@endif
                                                    </td>
                                                    <td>
                                                        @if($rowApprovalStatus === 'pending')<span class="pr-status pr-status-pending">Pending</span>
                                                        @elseif($rowApprovalStatus === 'approved')<span class="pr-status pr-status-approved">Approved</span>
                                                        @elseif($rowApprovalStatus === 'rejected')<span class="pr-status pr-status-rejected">Rejected</span>
                                                        @elseif($rowApprovalStatus === 'cancelled')<span class="pr-status pr-status-cancelled">Cancelled</span>
                                                        @else<span class="pr-status pr-status-cancelled">Not Sent</span>@endif
                                                    </td>
                                                    <td>
                                                        <div class="salary-actions">
                                                            <button type="button" class="sal-act sal-act-view view-advance-btn" data-month="{{ $g['month'] }}" data-advances="{{ json_encode($advancesData) }}"><i class="fa fa-eye"></i> View</button>
                                                            @if($showApproveBtn)
                                                             @if(Auth::user()->role_id == "2" || Auth::user()->role_id == "1")
                                                                <button type="button" class="sal-act sal-act-success approve-advance-btn" data-month="{{ $g['month'] }}" data-amount="{{ $g['total_amount'] }}" data-employees="{{ $g['total_count'] }}" data-reference="{{ $g['reference'] }}" data-requester-name="{{ $requesterName }}" data-requester-id="{{ $rowApproval->requested_by ?? '' }}" data-request-id="{{ $rowApproval->id ?? '' }}"><i class="fa fa-check-circle"></i> Approve Request</button>
                                                            @endif
                                                            @endif
                                                            @if($canRequestApproval)
                                                            @if(Auth::user()->role_id != "2")
                                                                <button type="button" class="sal-act sal-act-request request-advance-approval-btn" data-month="{{ $g['month'] }}" data-amount="{{ $g['total_amount'] }}" data-employees="{{ $g['total_count'] }}"><i class="fa fa-paper-plane"></i> Send for Approval</button>
                                                            @endif
                                                                @elseif($isPendingApproval)
                                                                <button class="sal-act sal-act-pending" disabled><i class="fa fa-clock-o"></i> Pending Approval</button>
                                                            @endif
                                                            @if($isApprovedRow)
                                                            @if(Auth::user()->role_id != "2")
                                                                <button type="button" class="sal-act sal-act-pay pay-advance-btn" data-month="{{ $g['month'] }}" data-advances="{{ json_encode($advancesData) }}"><i class="fa fa-money"></i> Make Pay</button>
                                                                @endif
                                                                @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center text-muted" style="padding:30px;">No salary advances created yet.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="modal fade" id="advanceDetailModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document" style="width:900px;"><div class="modal-content">
                                    <div class="modal-header"><h4 class="modal-title">Salary Advance Details</h4><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                    <div class="modal-body" id="advance-detail-content"></div>
                                </div></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- New Monthly Salary Request Modal --}}
    <div class="modal fade" id="newSalaryRequestModal"><div class="modal-dialog">
        <form id="newSalaryRequestForm" method="POST" action="{{ route('payroll-requests.store') }}">
            @csrf
            <input type="hidden" name="type" value="monthly_salary">
            <div class="modal-content">
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4>New Monthly Salary Request</h4></div>
                <div class="modal-body">
                    <div class="form-group"><label>Period</label><input type="month" name="period" class="form-control" required value="{{ now()->format('Y-m') }}"></div>
                    <div class="form-group"><label>Total Gross Amount (TZS)</label><input type="number" name="amount" step="0.01" min="0.01" class="form-control" required></div>
                    <div class="form-group"><label>Employee Count</label><input type="number" name="employee_count" min="1" class="form-control" required value="{{ $totalEmployees ?? 1 }}"></div>
                    <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Submit for Approval</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button></div>
            </div>
        </form>
    </div></div>

    {{-- New Salary Advance Request Modal --}}
    <div class="modal fade" id="newAdvanceRequestModal"><div class="modal-dialog">
        <form id="newAdvanceRequestForm" method="POST" action="{{ route('payroll-requests.store') }}">
            @csrf
            <input type="hidden" name="type" value="salary_advance">
            <input type="hidden" name="employee_count" value="1">
            <div class="modal-content">
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4>New Salary Advance Request</h4></div>
                <div class="modal-body">
                    <div class="form-group"><label>Employee</label>
                        <select name="employee_id" class="form-control select2" required>
                            <option value="">Select employee</option>
                            @foreach($employees ?? [] as $e)<option value="{{ $e->id }}">{{ $e->firstname }} {{ $e->surname }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Period</label><input type="month" name="period" class="form-control" required value="{{ now()->format('Y-m') }}"></div>
                    <div class="form-group"><label>Advance Amount (TZS)</label><input type="number" name="amount" step="0.01" min="0.01" class="form-control" required></div>
                    <div class="form-group"><label>Reason</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Submit for Approval</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button></div>
            </div>
        </form>
    </div></div>

    {{-- Send for Approval Modal --}}
    <div class="modal fade" id="approvalRequestModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="approvalRequestForm" method="POST" action="{{ route('payroll-requests.store') }}">
                @csrf
                <input type="hidden" name="type" value="monthly_salary">
                <div class="modal-content">
                    <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4><i class="fa fa-paper-plane"></i> Send Request for Approval</h4></div>
                    <div class="modal-body">
                        <div class="form-group"><label class="control-label">Period <span class="req">*</span></label><input type="text" id="approvalPeriodDisplay" class="form-control" readonly></div>
                        <div class="form-group">
                            <label class="control-label">Send To <span class="req">*</span></label>
                            @if($managingDirector)
                                <label class="approve-channel" style="width:100%;"> 
                                    <input type="checkbox" name="send_to" value="{{ $managingDirector->id }}" class="md-checkbox-sa" checked>
                                    <span>{{ $managingDirector->fname }} {{ $managingDirector->lname }}</span>
                                    <small style="color:#94a3b8;font-weight:500;margin-left:4px;">(Managing Director)</small>
                                </label>
                            @else
                                <span class="text-muted" style="font-size:12.5px;">No Managing Director (role_id = 2) found.</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="control-label">Notify Via <span class="req">*</span></label>
                            <div class="approve-channels">
                                <label class="approve-channel"><input type="checkbox" name="notify_sms" value="1" checked><i class="fa fa-comment-o"></i><span>SMS</span></label>
                                <label class="approve-channel"><input type="checkbox" name="notify_whatsapp" value="1"><i class="fa fa-whatsapp"></i><span>WhatsApp</span></label>
                                <label class="approve-channel"><input type="checkbox" name="notify_email" value="1" id="saNotifyEmailCheckbox"><i class="fa fa-envelope-o"></i><span>Email</span></label>
                            </div>
                            <div id="saEmailFieldContainer" style="display:none;margin-top:10px;"><input type="email" name="email" class="form-control" placeholder="Enter email address"></div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;"><label class="control-label">Message <span class="req">*</span></label><textarea id="approvalMessage" name="message" class="form-control" rows="5" required></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary" id="sendApprovalBtn"><i class="fa fa-paper-plane"></i> Send Request</button></div>
                </div>
            </form>
        </div>
    </div>

    {{-- Approve Request Modal --}}
    <div class="modal fade" id="approvePayrollModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="approvePayrollForm" method="POST" action="{{ route('payroll-requests.store') }}">
                @csrf
                <input type="hidden" name="type" value="monthly_salary">
                <input type="hidden" name="reply_to" id="approveReplyToId" value="">
                <div class="modal-content">
                    <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4><i class="fa fa-check-circle"></i> Approve Request</h4></div>
                    <div class="modal-body">
                        <div class="form-group"><label class="control-label">Request Name <span class="req">*</span></label><input type="text" id="approveRequestName" class="form-control" readonly></div>
                        <div class="form-group"><label class="control-label">Reply To <span class="req">*</span></label><input type="text" id="approveReplyTo" class="form-control" readonly></div>
                        <div class="form-group">
                            <label class="control-label">Decision <span class="req">*</span></label>
                            <div class="approve-channels">
                                <label class="approve-channel decision-accepted"><input type="checkbox" class="decision-checkbox" data-decision="accepted" checked><i class="fa fa-check"></i><span>Accepted</span></label>
                                <label class="approve-channel decision-rejected"><input type="checkbox" class="decision-checkbox" data-decision="rejected"><i class="fa fa-times"></i><span>Rejected</span></label>
                            </div>
                            <input type="hidden" name="decision" id="approveDecisionStatus" value="accepted">
                        </div>
                        <div class="form-group"><label class="control-label">Message <span class="req">*</span></label><textarea id="approveMessage" name="message" class="form-control" rows="5" required></textarea></div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="control-label">Notify Via</label>
                            <div class="approve-channels">
                                <label class="approve-channel"><input type="checkbox" name="notify_sms" value="1" checked><i class="fa fa-comment-o"></i><span>SMS</span></label>
                                <label class="approve-channel"><input type="checkbox" name="notify_whatsapp" value="1"><i class="fa fa-whatsapp"></i><span>WhatsApp</span></label>
                                <label class="approve-channel"><input type="checkbox" name="notify_email" value="1" id="apNotifyEmailCheckbox"><i class="fa fa-envelope-o"></i><span>Email</span></label>
                            </div>
                            <div id="apEmailFieldContainer" style="display:none;margin-top:10px;"><input type="email" name="email" class="form-control" placeholder="Enter email address"></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary" id="approvePayrollBtn"><i class="fa fa-paper-plane"></i> Send Reply</button></div>
                </div>
            </form>
        </div>
    </div>

    {{-- Pay Payroll Modal --}}
    <div class="modal fade" id="payPayrollModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
            <form id="payPayrollForm" method="POST" action="">
                @csrf
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-money"></i> Mark Payroll as Paid</h4></div>
                <div class="modal-body">
                    <div class="pay-modal-note"><i class="fa fa-info-circle"></i><div>Tick the employees whose salaries have been paid. Unchecked employees remain unpaid and can be paid later.</div></div>
                    <div class="pay-modal-period"><span><strong>Period:</strong> <span id="payPeriodLabel">—</span></span><span id="payEmployeesLabel" style="color:#64748b;font-size:12px;"></span></div>
                    <table id="payEmployeeTable">
                        <thead><tr><th width="40"><input type="checkbox" id="paySelectAll" style="width:16px;height:16px;cursor:pointer;accent-color:#1e3a5f;margin:0;"></th><th>Employee</th><th class="text-right">Net Salary (TZS)</th><th width="110">Status</th></tr></thead>
                        <tbody id="payEmployeeBody"><tr><td colspan="4" class="text-center text-muted" style="padding:24px;">Loading employees…</td></tr></tbody>
                    </table>
                    <div class="pay-total-bar"><span class="total-label">Amount to mark as paid:</span><span class="total-value">TZS <span id="paySelectedTotal">0.00</span></span></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary" id="paySubmitBtn"><i class="fa fa-check"></i> Mark Selected as Paid</button></div>
            </form>
        </div></div>
    </div>

    {{-- Pay Advance Modal --}}
    <div class="modal fade" id="payAdvanceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
            <form id="payAdvanceForm" method="POST" action="{{ route('salary-advances.pay') }}">
                @csrf
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-money"></i> Mark Advances as Paid</h4></div>
                <div class="modal-body">
                    <div class="pay-modal-note"><i class="fa fa-info-circle"></i><div>Tick the employees whose advances have been paid. Unchecked employees remain unpaid and can be paid later.</div></div>
                    <div class="pay-modal-period"><span><strong>Period:</strong> <span id="payAdvancePeriodLabel">—</span></span><span id="payAdvanceEmployeesLabel" style="color:#64748b;font-size:12px;"></span></div>
                    <table id="payAdvanceTable">
                        <thead><tr><th width="40"><input type="checkbox" id="payAdvanceSelectAll" style="width:16px;height:16px;cursor:pointer;accent-color:#1e3a5f;margin:0;"></th><th>Employee</th><th class="text-right">Amount (TZS)</th><th width="110">Status</th></tr></thead>
                        <tbody id="payAdvanceBody"></tbody>
                    </table>
                    <div class="pay-total-bar"><span class="total-label">Amount to mark as paid:</span><span class="total-value">TZS <span id="payAdvanceSelectedTotal">0.00</span></span></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary" id="payAdvanceSubmitBtn"><i class="fa fa-check"></i> Mark Selected as Paid</button></div>
            </form>
        </div></div>
    </div>

    {{-- Bank Modal --}}
    <div class="modal fade" id="bankModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
            <div class="modal-header"><h4 class="modal-title">Bank Details</h4><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body"></div>
        </div></div>
    </div>

    {{-- Add Loan Modal --}}
    <div class="modal fade" id="addLoanModal"><div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('loans.store') }}">
            @csrf
            <div class="modal-header"><h4 class="modal-title">Add New Loan</h4></div>
            <div class="modal-body">@include('payrolls.loan_form_fields', ['loan' => null])</div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Save Loan</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button></div>
        </form>
    </div></div></div>

    {{-- Add Salary Advance Modal --}}
    <div class="modal fade" id="addAdvanceModal"><div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('salary-advances.store') }}">
            @csrf
            <div class="modal-header"><h4>Add Salary Advance</h4></div>
            <div class="modal-body">@include('payrolls.advance_form_fields', ['advance' => null])</div>
            <div class="modal-footer"><button class="btn btn-primary">Save</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button></div>
        </form>
    </div></div></div>
</div>

{{-- Loan Details Modal (statement) --}}
<div class="modal fade" id="detailsLoanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog"><div class="modal-content" id="loanDetailsModalContent">
        <div class="modal-body" id="loanDetailsBody"><div class="text-center"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i><p>Loading loan Statement...</p></div></div>
        <div class="modal-footer no-print"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-success" onclick="printLoanDetails('loanDetailsModalContent')">Print</button></div>
    </div></div>
</div>



<div class="modal fade" id="editAdvanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content">

            <form id="editAdvanceForm" method="POST">

                @csrf
                @method('PUT')

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>

                    <h4 class="modal-title">
                        <i class="fa fa-pencil"></i> Edit Salary Advance
                    </h4>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="edit_advance_id">

                    <div class="form-group">
                        <label>Employee</label>
                        <input type="text"
                            id="edit_advance_employee"
                            class="form-control"
                            readonly>
                    </div>

                    <div class="form-group">
                        <label>Amount (TZS)</label>
                        <input type="number"
                            name="amount"
                            id="edit_advance_amount"
                            class="form-control"
                            min="0.01"
                            step="0.01"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Payment Status</label>

                        <select name="is_paid"
                            id="edit_advance_paid"
                            class="form-control"
                            required>
                            <option value="0">Unpaid</option>
                            <option value="1">Paid</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Update Advance
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- =====================================================================
     Employee selection / contributions loading
     ===================================================================== --}}
<script>
$(document).ready(function() {
    $('#select-all').change(function() {
        let checked = $(this).is(':checked');
        $('.employee-checkbox').prop('checked', checked).trigger('change');
    });
    $('.employee-checkbox').change(function() {
        let employeeId = $(this).data('employee-id');
        let contributionsContainer = $('.contributions-container[data-employee-id="' + employeeId + '"]');
        let allowanceField = $('.allowance-field[data-employee-id="' + employeeId + '"]');
        if ($(this).is(':checked')) {
            allowanceField.prop('disabled', false);
            $.ajax({
                url: '/employee/' + employeeId + '/contributions', method: 'GET',
                success: function(response) {
                    let html = '';
                    $.each(response, function(i, contribution) {
                        html += `<label class="checkbox-inline" style="margin-right:10px;">
                            <input type="checkbox" name="employees[${employeeId}][contributions][]" value="${contribution.id}" checked>
                            ${contribution.name}
                        </label>`;
                    });
                    contributionsContainer.html(html);
                },
                error: function() { contributionsContainer.html('<em>Failed to load contributions.</em>'); }
            });
        } else {
            allowanceField.prop('disabled', true).val('');
            contributionsContainer.html('<em>Select employee to load contributions</em>');
        }
    });
});
</script>

{{-- Salary table filter + advance table filter --}}
<script>
$(function () {
    const PDF_BASE   = '{{ route("salary-advances.pdf") }}';
    const DEF_MONTH  = '{{ str_pad(now()->month, 2, "0", STR_PAD_LEFT) }}';
    const DEF_YEAR   = '{{ now()->year }}';

    function updateAdvancePdfBtn() {
        const m = $('#advFilterMonth').val() || DEF_MONTH;
        const y = $('#advFilterYear').val()  || DEF_YEAR;
        $('#advancePdfBtn').attr('href', PDF_BASE + '?month=' + encodeURIComponent(m) + '&year=' + encodeURIComponent(y));
    }

    function filterAdvanceTable() {
        const m = $('#advFilterMonth').val();
        const y = $('#advFilterYear').val();
        let visible = 0;
        const $rows = $('#advanceTable tbody tr[data-month]');
        $rows.each(function () {
            const [rowY, rowM] = String($(this).data('month')).split('-');
            const show = (!m || m === rowM) && (!y || y === rowY);
            $(this).toggle(show);
            if (show) visible++;
        });
        const $cnt = $('#advFilterCount');
        if (m || y) $cnt.text(visible + ' / ' + $rows.length).show();
        else $cnt.hide().text('');
        updateAdvancePdfBtn();
    }

    $('#advFilterMonth, #advFilterYear').on('change', filterAdvanceTable);
    $('#advFilterReset').on('click', function () {
        $('#advFilterMonth').val('');
        $('#advFilterYear').val('');
        filterAdvanceTable();
    });

    filterAdvanceTable();
});

$(function () {
    function filterSalaryTable() {
        const m = $('#salaryFilterMonth').val();
        const y = $('#salaryFilterYear').val();
        let visible = 0;
        const $rows = $('#salaryTable tbody tr');
        $rows.each(function () {
            const rowMonth = $(this).data('month');
            if (!rowMonth) { $(this).show(); visible++; return; }
            const parts = String(rowMonth).split('-');
            const show = (!m || m === parts[1]) && (!y || y === parts[0]);
            $(this).toggle(show);
            if (show) visible++;
        });
        const $count = $('#salaryFilterCount');
        if (m || y) $count.text(visible + ' / ' + $rows.length).show();
        else $count.hide().text('');
    }
    $('#salaryFilterMonth, #salaryFilterYear').on('change', filterSalaryTable);
    $('#salaryFilterReset').on('click', function () {
        $('#salaryFilterMonth').val('');
        $('#salaryFilterYear').val('');
        filterSalaryTable();
    });
    filterSalaryTable();
});
</script>

{{-- Vouchers dropdown --}}
<script>
$(document).on('click', '.sal-dropdown-toggle', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const $dd = $(this).closest('.sal-dropdown');
    const wasOpen = $dd.hasClass('open');
    $('.sal-dropdown').removeClass('open');
    if (!wasOpen) $dd.addClass('open');
});
$(document).on('click', function (e) {
    if (!$(e.target).closest('.sal-dropdown').length) {
        $('.sal-dropdown').removeClass('open');
    }
});
</script>

{{-- Send for Approval --}}
<script>
$(function () {
    const csrf = $('meta[name="csrf-token"]').attr('content');
    const MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    function formatPeriod(monthStr) {
        const [y, m] = String(monthStr).split('-');
        const idx = parseInt(m, 10) - 1;
        return (MONTH_NAMES[idx] || m) + ' ' + y;
    }
    function buildApprovalMessage(periodLabel, employees, amount, kind) {
        return 'Dear Managing Director,\n\nPlease review and approve the ' + kind + ' for ' + periodLabel + '.\n\nEmployees: ' + employees + '\nTotal Amount: TZS ' + amount.toLocaleString(undefined, { minimumFractionDigits: 2 }) + '\n\nKindly respond at your earliest convenience.\n\nThank you.';
    }
    $('#saNotifyEmailCheckbox').on('change', function () { $('#saEmailFieldContainer').toggle(this.checked); });
    function openApprovalModal($btn, type, labelKind) {
        const month     = $btn.data('month');
        const amount    = parseFloat($btn.data('amount')) || 0;
        const employees = parseInt($btn.data('employees'), 10) || 0;
        const [year, mon] = String(month).split('-');
        const periodLabel = formatPeriod(month);
        $('#approvalRequestForm input[name="type"]').val(type);
        $('#approvalRequestForm').data('year', year).data('month', parseInt(mon, 10)).data('amount', amount).data('employees', employees);
        $('#approvalPeriodDisplay').val(periodLabel);
        $('#approvalMessage').val(buildApprovalMessage(periodLabel, employees, amount, labelKind));
        $('#approvalRequestForm input[name="notify_sms"]').prop('checked', true);
        $('#approvalRequestForm input[name="notify_whatsapp"]').prop('checked', false);
        $('#approvalRequestForm input[name="notify_email"]').prop('checked', false);
        $('#saEmailFieldContainer').hide();
        $('#approvalRequestForm input[name="email"]').val('');
        $('#approvalRequestForm .md-checkbox-sa').prop('checked', true);
        $('#sendApprovalBtn').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Request');
        $('#approvalRequestModal').modal('show');
    }
    $(document).on('click', '.request-approval-btn', function () { openApprovalModal($(this), 'monthly_salary', 'payroll'); });
    $(document).on('click', '.request-advance-approval-btn', function () { openApprovalModal($(this), 'salary_advance', 'salary advances'); });

    $('#approvalRequestForm').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this), $btn = $('#sendApprovalBtn');
        const year = $form.data('year'), month = $form.data('month');
        const amount = $form.data('amount'), empCnt = $form.data('employees');
        const message = ($('#approvalMessage').val() || '').trim();
        const type = $form.find('input[name="type"]').val() || 'monthly_salary';
        if (!message) { alert('Please enter a message.'); return; }
        const sendTo = $form.find('input[name="send_to"]:checked').val() || null;
        if (!sendTo.length) { alert('Please select a recipient in "Send To".'); return; }
        const channels = [];
        if ($form.find('input[name="notify_sms"]').is(':checked'))      channels.push('sms');
        if ($form.find('input[name="notify_whatsapp"]').is(':checked')) channels.push('whatsapp');
        if ($form.find('input[name="notify_email"]').is(':checked'))    channels.push('email');
        if (!channels.length) { alert('Please select at least one notification channel.'); return; }
        const email = $form.find('input[name="email"]').val() || '';
        if (channels.includes('email') && !email) { alert('Please enter an email address.'); return; }
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending…');
        $.ajax({
            url: $form.attr('action'), type: 'POST',
            data: { _token: csrf, type: type, period_year: year, period_month: month, amount: amount, employee_count: empCnt, send_to: sendTo, channels: channels, email: email, notes: message },
            success: function (res) {
                $('#approvalRequestModal').modal('hide');
                Swal.fire({ icon: 'success', title: 'Request Sent', text: (res && res.message) || 'Your request has been submitted for approval.', confirmButtonText: 'OK', confirmButtonColor: '#10b981', timer: 2000, timerProgressBar: true }).then(function () { window.location.reload(); });
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Request');
                const errs = xhr.responseJSON && xhr.responseJSON.errors;
                if (errs) alert(Object.values(errs).flat().join('\n'));
                else if (xhr.responseJSON && xhr.responseJSON.message) alert(xhr.responseJSON.message);
                else alert('Failed to send request. Please try again.');
            }
        });
    });
});
</script>

{{-- Approve Request --}}
<script>
$(function () {
    const csrf = $('meta[name="csrf-token"]').attr('content');
    const MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    function formatPeriod(monthStr) {
        const [y, m] = String(monthStr).split('-');
        const idx = parseInt(m, 10) - 1;
        return (MONTH_NAMES[idx] || m) + ' ' + y;
    }
    $('#apNotifyEmailCheckbox').on('change', function () { $('#apEmailFieldContainer').toggle(this.checked); });
    $(document).on('change', '.decision-checkbox', function () {
        if (this.checked) { $('.decision-checkbox').not(this).prop('checked', false); $('#approveDecisionStatus').val($(this).data('decision')); }
        else { this.checked = true; }
    });
    function openApproveModal($btn, type, labelPrefix) {
        const month     = $btn.data('month');
        const amount    = parseFloat($btn.data('amount')) || 0;
        const employees = parseInt($btn.data('employees'), 10) || 0;
        const reqName   = $btn.data('requester-name') || '';
        const reqId     = $btn.data('requester-id') || '';
        const [year, mon] = String(month).split('-');
        const periodLabel = formatPeriod(month);
        $('#approvePayrollForm input[name="type"]').val(type);
        $('#approvePayrollForm').data('year', year).data('month', parseInt(mon, 10)).data('amount', amount).data('employees', employees);
        $('#approveRequestName').val(labelPrefix + ' — ' + periodLabel);
        $('#approveReplyTo').val(reqName || 'N/A');
        $('#approveReplyToId').val(reqId);
        $('#approveMessage').val('Dear ' + (reqName || 'Requester') + ',\n\nYour ' + labelPrefix.toLowerCase() + ' request for ' + periodLabel + ' has been reviewed.\n\nEmployees: ' + employees + '\nTotal Amount: TZS ' + amount.toLocaleString(undefined, { minimumFractionDigits: 2 }) + '\n\nPlease see the decision above.\n\nThank you.');
        $('.decision-checkbox[data-decision="accepted"]').prop('checked', true);
        $('.decision-checkbox[data-decision="rejected"]').prop('checked', false);
        $('#approveDecisionStatus').val('accepted');
        $('#approvePayrollForm input[name="notify_sms"]').prop('checked', true);
        $('#approvePayrollForm input[name="notify_whatsapp"]').prop('checked', false);
        $('#approvePayrollForm input[name="notify_email"]').prop('checked', false);
        $('#apEmailFieldContainer').hide();
        $('#approvePayrollForm input[name="email"]').val('');
        $('#approvePayrollBtn').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Reply');
        $('#approvePayrollModal').modal('show');
    }
    $(document).on('click', '.approve-payroll-btn', function () { openApproveModal($(this), 'monthly_salary', 'Payroll Request'); });
    $(document).on('click', '.approve-advance-btn', function () { openApproveModal($(this), 'salary_advance', 'Salary Advance Request'); });

    $('#approvePayrollForm').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this), $btn = $('#approvePayrollBtn');
        const year = $form.data('year'), month = $form.data('month');
        const amount = $form.data('amount'), empCnt = $form.data('employees');
        const message = ($('#approveMessage').val() || '').trim();
        const decision = $('#approveDecisionStatus').val() || 'accepted';
        const type = $form.find('input[name="type"]').val() || 'monthly_salary';
        if (!message) { alert('Please enter a message.'); return; }
        const channels = [];
        if ($form.find('input[name="notify_sms"]').is(':checked'))      channels.push('sms');
        if ($form.find('input[name="notify_whatsapp"]').is(':checked')) channels.push('whatsapp');
        if ($form.find('input[name="notify_email"]').is(':checked'))    channels.push('email');
        const email = $form.find('input[name="email"]').val() || '';
        if (channels.includes('email') && !email) { alert('Please enter an email address.'); return; }
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending…');
        $.ajax({
            url: $form.attr('action'), type: 'POST',
            data: { _token: csrf, type: type, period_year: year, period_month: month, amount: amount, employee_count: empCnt, decision: decision, reply_to: $('#approveReplyToId').val(), channels: channels, email: email, notes: message },
            success: function (res) {
                $('#approvePayrollModal').modal('hide');
                const isAccepted = decision === 'accepted';
                Swal.fire({ icon: 'success', title: isAccepted ? 'Request Approved' : 'Request Rejected', text: (res && res.message) || (isAccepted ? 'The request has been approved.' : 'The request has been rejected.'), confirmButtonText: 'OK', confirmButtonColor: isAccepted ? '#10b981' : '#dc2626', timer: 2000, timerProgressBar: true }).then(function () { window.location.reload(); });
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Reply');
                const errs = xhr.responseJSON && xhr.responseJSON.errors;
                if (errs) alert(Object.values(errs).flat().join('\n'));
                else if (xhr.responseJSON && xhr.responseJSON.message) alert(xhr.responseJSON.message);
                else alert('Failed to send reply. Please try again.');
            }
        });
    });
});
</script>

{{-- Pay Payroll --}}
<script>
$(function () {
    function updateSelectedTotal() {
        let total = 0, count = 0;
        $('.pay-emp-check:checked:not(:disabled)').each(function () { total += parseFloat($(this).data('amount')) || 0; count++; });
        $('#paySelectedTotal').text(total.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $('#payEmployeesLabel').text(count ? count + ' employee(s) selected' : '');
    }
    $(document).on('click', '.pay-payroll-btn', function () {
        const reference = $(this).data('reference');
        const month = $(this).data('month');
        $('#payPeriodLabel').text(month || '—');
        $('#payEmployeesLabel').text('');
        $('#payEmployeeBody').html('<tr><td colspan="4" class="text-center text-muted" style="padding:24px;">Loading employees…</td></tr>');
        $('#paySelectAll').prop('checked', false).prop('disabled', true);
        $('#paySelectedTotal').text('0.00');
        $('#paySubmitBtn').prop('disabled', true).html('<i class="fa fa-check"></i> Mark Selected as Paid');
        $('#payPayrollForm').attr('action', `/payrolls/${encodeURIComponent(reference)}/pay-employees`);
        $('#payPayrollModal').modal('show');
        $.ajax({
            url: `/payrolls/${encodeURIComponent(reference)}/employees`, type: 'GET', dataType: 'json',
            success: function (res) {
                const employees = (res && res.employees) ? res.employees : [];
                if (!employees.length) { $('#payEmployeeBody').html('<tr><td colspan="4" class="text-center text-muted" style="padding:24px;">No employees found.</td></tr>'); return; }
                let html = '', anyUnpaid = false;
                employees.forEach(function (e) {
                    const isPaid = !!e.is_paid;
                    const disabled = isPaid ? 'disabled' : '';
                    const checked  = isPaid ? 'checked' : '';
                    if (!isPaid) anyUnpaid = true;
                    html += `<tr class="${isPaid ? 'row-paid' : 'row-unpaid'}"><td><input type="checkbox" class="pay-emp-check" name="payroll_ids[]" value="${e.id}" data-amount="${e.net_salary}" ${checked} ${disabled}></td><td>${e.name}</td><td class="text-right" style="font-family:'SFMono-Regular',Consolas,monospace;font-size:12.5px;font-weight:600;">${Number(e.net_salary).toLocaleString(undefined, {minimumFractionDigits: 2})}</td><td>${isPaid ? '<span class="pr-status pr-status-approved">Paid</span>' : '<span class="pr-status pr-status-pending">Unpaid</span>'}</td></tr>`;
                });
                $('#payEmployeeBody').html(html);
                $('#paySelectAll').prop('disabled', !anyUnpaid);
                $('#paySubmitBtn').prop('disabled', !anyUnpaid);
                updateSelectedTotal();
            },
            error: function () { $('#payEmployeeBody').html('<tr><td colspan="4" class="text-center text-danger" style="padding:24px;">Failed to load employees.</td></tr>'); }
        });
    });
    $(document).on('change', '.pay-emp-check', function () {
        const anyChecked = $('.pay-emp-check:checked:not(:disabled)').length > 0;
        $('#paySubmitBtn').prop('disabled', !anyChecked);
        updateSelectedTotal();
    });
    $('#paySelectAll').on('change', function () {
        const checked = this.checked;
        $('.pay-emp-check:not(:disabled)').prop('checked', checked);
        $('#paySubmitBtn').prop('disabled', !checked);
        updateSelectedTotal();
    });
    $('#payPayrollForm').on('submit', function (e) {
        e.preventDefault();
        const ids = $('.pay-emp-check:checked:not(:disabled)').map(function () { return this.value; }).get();
        if (!ids.length) { alert('Please select at least one employee.'); return; }
        const $btn = $('#paySubmitBtn');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving…');
        $.ajax({
            url: $(this).attr('action'), type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), payroll_ids: ids },
            success: function () { $('#payPayrollModal').modal('hide'); window.location.reload(); },
            error: function (xhr) { $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Mark Selected as Paid'); alert((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to mark payroll as paid.'); }
        });
    });
});
</script>

{{-- Salary Advance tab — View / Make Pay --}}

<script>
$(function () {

    const advanceUrl = "{{ url('/salary-advances') }}";

    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
    }

    // VIEW ADVANCES
    $(document).on('click', '.view-advance-btn', function () {

        const month = $(this).data('month');
        const list = JSON.parse($(this).attr('data-advances') || '[]');

        let rows = '';
        let total = 0;

        list.forEach(function (a, i) {

            const amount = parseFloat(a.amount) || 0;
            total += amount;

            rows += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${escapeHtml(a.employee_name)}</td>

                    <td class="text-right">
                        ${amount.toLocaleString(undefined, {
                            minimumFractionDigits: 2
                        })}
                    </td>

                    <td>
                        ${a.is_paid
                            ? '<span class="pr-status pr-status-approved">Paid</span>'
                            : '<span class="pr-status pr-status-pending">Unpaid</span>'}
                    </td>

                    <td class="text-center">
                        <button type="button"
                            class="btn btn-xs btn-primary edit-advance-btn"
                            data-id="${a.id}"
                            data-employee="${escapeHtml(a.employee_name)}"
                            data-amount="${amount}"
                            data-paid="${a.is_paid ? 1 : 0}">
                            <i class="fa fa-pencil"></i> Edit
                        </button>

                        <button type="button"
                            class="btn btn-xs btn-danger delete-advance-btn"
                            data-id="${a.id}"
                            data-employee="${escapeHtml(a.employee_name)}">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `;
        });

        const html = `
            <div class="pay-modal-period" style="margin-bottom:14px;">
                <span><strong>Period:</strong> ${escapeHtml(month)}</span>
                <span style="color:#64748b;font-size:12px;">
                    ${list.length} employee(s)
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped"
                    style="margin-bottom:0;">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th class="text-right">Amount (TZS)</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>${rows}</tbody>

                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total</th>
                            <th class="text-right">
                                ${total.toLocaleString(undefined, {
                                    minimumFractionDigits: 2
                                })}
                            </th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>

                </table>
            </div>
        `;

        $('#advance-detail-content').html(html);
        $('#advanceDetailModal').modal('show');
    });


    // EDIT ADVANCE - OPEN MODAL
    $(document).on('click', '.edit-advance-btn', function () {

        const id = $(this).data('id');

        $('#edit_advance_id').val(id);
        $('#edit_advance_employee').val($(this).data('employee'));
        $('#edit_advance_amount').val($(this).data('amount'));
        $('#edit_advance_paid').val($(this).data('paid'));

        $('#editAdvanceForm').attr('action', advanceUrl + '/' + id);

        $('#advanceDetailModal').modal('hide');

        $('#editAdvanceModal').modal('show');
    });


    // UPDATE ADVANCE
    $('#editAdvanceForm').on('submit', function (e) {

        e.preventDefault();

        const form = $(this);
        const button = form.find('button[type="submit"]');

        button.prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (response) {

                $('#editAdvanceModal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: response.message || 'Salary advance updated successfully.',
                    timer: 1800,
                    showConfirmButton: false
                }).then(function () {
                    location.reload();
                });
            },
            error: function (xhr) {

                let message = 'Unable to update salary advance.';

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors)
                            .flat()
                            .join('\n');
                    } else {
                        message = xhr.responseJSON.message || message;
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: message
                });
            },
            complete: function () {
                button.prop('disabled', false)
                    .html('Update Advance');
            }
        });
    });


    // DELETE ADVANCE - SWEET ALERT
    $(document).on('click', '.delete-advance-btn', function () {

        const id = $(this).data('id');
        const employee = $(this).data('employee');

        Swal.fire({
            title: 'Delete Salary Advance?',
            text: 'Are you sure you want to delete the advance for ' + employee + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then(function (result) {

            if (!result.isConfirmed) return;

            $.ajax({
                url: advanceUrl + '/' + id,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                },
                success: function (response) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message || 'Salary advance deleted successfully.',
                        timer: 1800,
                        showConfirmButton: false
                    }).then(function () {
                        location.reload();
                    });

                },
                error: function (xhr) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Delete Failed',
                        text: xhr.responseJSON?.message || 'Unable to delete salary advance.'
                    });

                }
            });

        });

    });

});
</script>
<script>
    function updateAdvanceTotal() {
        let total = 0, count = 0;
        $('.pay-adv-check:checked:not(:disabled)').each(function () {
            total += parseFloat($(this).data('amount')) || 0;
            count++;
        });
        $('#payAdvanceSelectedTotal').text(total.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $('#payAdvanceEmployeesLabel').text(count ? count + ' employee(s) selected' : '');
    }

    $(document).on('click', '.pay-advance-btn', function () {
        const month = $(this).data('month');
        const list  = JSON.parse($(this).attr('data-advances') || '[]');
        $('#payAdvancePeriodLabel').text(month);
        $('#payAdvanceEmployeesLabel').text('');
        $('#payAdvanceSelectedTotal').text('0.00');

        let rows = '', anyUnpaid = false;
        list.forEach(function (a) {
            const isPaid = !!a.is_paid;
            if (!isPaid) anyUnpaid = true;
            rows += `<tr class="${isPaid ? 'row-paid' : 'row-unpaid'}">
                <td><input type="checkbox" class="pay-adv-check" name="advance_ids[]" value="${a.id}" data-amount="${a.amount}" ${isPaid ? 'checked disabled' : ''}></td>
                <td>${a.employee_name}</td>
                <td class="text-right" style="font-family:'SFMono-Regular',Consolas,monospace;font-size:12.5px;font-weight:600;">${Number(a.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                <td>${isPaid ? '<span class="pr-status pr-status-approved">Paid</span>' : '<span class="pr-status pr-status-pending">Unpaid</span>'}</td>
            </tr>`;
        });
        $('#payAdvanceBody').html(rows);
        $('#payAdvanceSelectAll').prop('checked', false).prop('disabled', !anyUnpaid);
        $('#payAdvanceSubmitBtn').prop('disabled', !anyUnpaid);
        updateAdvanceTotal();
        $('#payAdvanceModal').modal('show');
    });

    $(document).on('change', '.pay-adv-check', function () {
        const anyChecked = $('.pay-adv-check:checked:not(:disabled)').length > 0;
        $('#payAdvanceSubmitBtn').prop('disabled', !anyChecked);
        updateAdvanceTotal();
    });

    $('#payAdvanceSelectAll').on('change', function () {
        const checked = this.checked;
        $('.pay-adv-check:not(:disabled)').prop('checked', checked);
        $('#payAdvanceSubmitBtn').prop('disabled', !checked);
        updateAdvanceTotal();
    });

    $('#payAdvanceForm').on('submit', function (e) {
        e.preventDefault();
        const ids = $('.pay-adv-check:checked:not(:disabled)').map(function () { return this.value; }).get();
        if (!ids.length) { alert('Please select at least one employee.'); return; }
        const $btn = $('#payAdvanceSubmitBtn');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving…');
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), advance_ids: ids },
            success: function () { $('#payAdvanceModal').modal('hide'); window.location.reload(); },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Mark Selected as Paid');
                alert((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to mark advances as paid.');
            }
        });
    });
</script>

{{-- Salary detail / bank / NSSF / TUICO vouchers / rollback / salary slip --}}

<style>
/* ─────────────────────────────────────────────
   Salary details — modal view
   ───────────────────────────────────────────── */
.sd-wrap { font-size: 13px; color: #1f2937; }

.sd-header {
    display: flex; flex-wrap: wrap; gap: 12px;
    align-items: flex-start; justify-content: space-between;
    padding-bottom: 14px; margin-bottom: 16px;
    border-bottom: 1px solid #e9edf3;
}
.sd-title { margin: 0 0 5px; font-size: 18px; font-weight: 700; color: #111827; }
.sd-period {
    display: inline-block; margin-left: 6px; padding: 4px 10px;
    border-radius: 2px; background: #eee; color: #0c0608;
    font-size: 12px; font-weight: 600; vertical-align: middle;
}
.sd-meta { font-size: 12px; color: #6b7280; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.sd-actions { display: flex; gap: 8px; flex-wrap: wrap; }

.sd-stats {
    display: grid; gap: 12px; margin-bottom: 16px;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
}
.sd-card { border: 1px solid #e9edf3; border-radius: 10px; padding: 12px 14px; background: #fff; }
.sd-card .sd-label {
    font-size: 11px; text-transform: uppercase; letter-spacing: .04em;
    color: #6b7280; font-weight: 600;
}
.sd-card .sd-value {
    font-size: 18px; font-weight: 700; margin-top: 4px;
    color: #111827; font-variant-numeric: tabular-nums;
}
.sd-card.sd-card-red   .sd-value { color: #b91c1c; }
.sd-card.sd-card-green .sd-value { color: #047857; }

.sd-toolbar {
    display: flex; justify-content: space-between; align-items: center;
    gap: 12px; margin-bottom: 10px; flex-wrap: wrap;
}
.sd-search { position: relative; }
.sd-search i {
    position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
    color: #9ca3af; font-size: 12px;
}
.sd-search input {
    padding: 7px 10px 7px 28px; border: 1px solid #d9dee7; border-radius: 8px;
    font-size: 13px; width: 240px; outline: none; transition: .15s;
}
.sd-search input:focus { border-color: #ec4899; box-shadow: 0 0 0 3px rgba(236,72,153,.12); }
.sd-hint { font-size: 11px; color: #9ca3af; }

.sd-table-wrap {
    border: 1px solid #e9edf3; border-radius: 10px;
    max-height: 60vh; overflow: auto;
}
.sd-table { margin: 0; font-size: 12px; white-space: nowrap; width: 100%; }
.sd-table thead th {
    position: sticky; top: 0; z-index: 2;
    background: #f8fafc; color: #475569;
    font-size: 11px; text-transform: uppercase; letter-spacing: .03em;
    font-weight: 700; padding: 10px 12px;
    border-bottom: 1px solid #e5e9f0;
}
.sd-table tbody td { padding: 9px 12px; border-top: 1px solid #f1f4f9; vertical-align: middle; }
.sd-table tbody tr:hover { background: #fdf7fb; }
.sd-table tfoot td {
    background: #f8fafc; font-weight: 700; padding: 10px 12px;
    border-top: 2px solid #e5e9f0;
}

.sd-name { display: flex; align-items: center; gap: 9px; }
.sd-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    background: #409cf3; color: #f2faef;
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center; flex: none;
}
.sd-num { text-align: right; font-variant-numeric: tabular-nums; }
.sd-net { font-weight: 700; color: #047857; }
.sd-neg { font-weight: 700; color: #b91c1c; }
.sd-idx { color: #9ca3af; }

.sd-note { margin-top: 10px; font-size: 12px; color: #6b7280; }
</style>

<script>
/* ─────────────────────────────────────────────
   Salary details — click handler
   ───────────────────────────────────────────── */
(function () {

    'use strict';

    var FIELDS = ['basic_salary', 'allowance', 'salary_advance', 'nssf', 'nhif',
                  'tuico', 'loan', 'paye', 'net_salary'];

    /* ---------- helpers ---------- */

    if (typeof window.escapeHtml !== 'function') {
        window.escapeHtml = function (value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        };
    }

    function escapeHtml(value) {
        return window.escapeHtml(value);
    }

    /**
     * Converts "3,000,000.00" / 3000000 / null → Number.
     * (parseFloat("3,000,000.00") returns 3 — this is why totals were wrong.)
     */
    function toNumber(value) {
        if (typeof value === 'number') return isFinite(value) ? value : 0;
        if (value === null || value === undefined || value === '') return 0;
        var n = parseFloat(String(value).replace(/[^0-9.\-]/g, ''));
        return isFinite(n) ? n : 0;
    }

    function money(value) {
        return toNumber(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function initials(name) {
        var parts = String(name || '').trim().split(/\s+/).filter(Boolean);
        if (!parts.length) return '?';
        var first = parts[0].charAt(0);
        var last  = parts.length > 1 ? parts[parts.length - 1].charAt(0) : '';
        return (first + last).toUpperCase();
    }

    /* ---------- styles (injected once) ---------- */

    function ensureStyles() {
        if (document.getElementById('sd-salary-styles')) return;

        var css = `
            .sd-wrap { font-size: 13px; color: #1f2937; }
            .sd-header {
                display: flex; flex-wrap: wrap; gap: 12px;
                align-items: flex-start; justify-content: space-between;
                padding-bottom: 14px; margin-bottom: 16px;
                border-bottom: 1px solid #e9edf3;
            }
            .sd-title { margin: 0 0 5px; font-size: 18px; font-weight: 700; color: #111827; }
            .sd-period {
                display: inline-block; margin-left: 6px; padding: 2px 10px;
                border-radius: 20px; background: #fdf2f8; color: #be185d;
                font-size: 12px; font-weight: 600; vertical-align: middle;
            }
            .sd-meta { font-size: 12px; color: #6b7280; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
            .sd-actions { display: flex; gap: 8px; flex-wrap: wrap; }

            .sd-stats {
                display: grid; gap: 12px; margin-bottom: 16px;
                grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            }
            .sd-card { border: 1px solid #e9edf3; border-radius: 10px; padding: 12px 14px; background: #fff; }
            .sd-card .sd-label {
                font-size: 11px; text-transform: uppercase; letter-spacing: .04em;
                color: #6b7280; font-weight: 600;
            }
            .sd-card .sd-value {
                font-size: 18px; font-weight: 700; margin-top: 4px;
                color: #111827; font-variant-numeric: tabular-nums;
            }
            .sd-card.sd-card-red   .sd-value { color: #b91c1c; }
            .sd-card.sd-card-green .sd-value { color: #047857; }

            .sd-toolbar {
                display: flex; justify-content: space-between; align-items: center;
                gap: 12px; margin-bottom: 10px; flex-wrap: wrap;
            }
            .sd-search { position: relative; }
            .sd-search i {
                position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
                color: #9ca3af; font-size: 12px;
            }
            .sd-search input {
                padding: 7px 10px 7px 28px; border: 1px solid #d9dee7; border-radius: 8px;
                font-size: 13px; width: 240px; outline: none; transition: .15s;
            }
            .sd-search input:focus { border-color: #ec4899; box-shadow: 0 0 0 3px rgba(236,72,153,.12); }
            .sd-hint { font-size: 11px; color: #9ca3af; }

            .sd-table-wrap {
                border: 1px solid #e9edf3; border-radius: 10px;
                max-height: 60vh; overflow: auto;
            }
            .sd-table { margin: 0; font-size: 12px; white-space: nowrap; width: 100%; }
            .sd-table thead th {
                position: sticky; top: 0; z-index: 2;
                background: #f8fafc; color: #475569;
                font-size: 11px; text-transform: uppercase; letter-spacing: .03em;
                font-weight: 700; padding: 10px 12px;
                border-bottom: 1px solid #e5e9f0;
            }
            .sd-table tbody td { padding: 9px 12px; border-top: 1px solid #f1f4f9; vertical-align: middle; }
            .sd-table tbody tr:hover { background: #fdf7fb; }
            .sd-table tfoot td {
                background: #f8fafc; font-weight: 700; padding: 10px 12px;
                border-top: 2px solid #e5e9f0;
            }

            .sd-name { display: flex; align-items: center; gap: 9px; }
            .sd-avatar {
                width: 28px; height: 28px; border-radius: 50%;
                background: #fce7f3; color: #be185d;
                font-size: 11px; font-weight: 700;
                display: flex; align-items: center; justify-content: center; flex: none;
            }
            .sd-num { text-align: right; font-variant-numeric: tabular-nums; }
            .sd-net { font-weight: 700; color: #047857; }
            .sd-neg { font-weight: 700; color: #b91c1c; }
            .sd-idx { color: #9ca3af; }

            .sd-note { margin-top: 10px; font-size: 12px; color: #6b7280; }
        `;

        var style = document.createElement('style');
        style.id = 'sd-salary-styles';
        style.type = 'text/css';
        style.appendChild(document.createTextNode(css));
        document.head.appendChild(style);
    }

    /* ---------- click handler ---------- */

    $(document).on('click', '.view-salary-btn', function () {

        var $btn      = $(this);
        var reference = $btn.data('reference');
        var month     = $btn.data('month');
        var $content  = $('#salary-detail-content');

        ensureStyles();
        $content.off('.salary');

        $content.html(
            '<div class="text-center" style="padding:40px;">' +
                '<i class="fa fa-spinner fa-spin fa-2x" style="color:#ec4899;"></i>' +
                '<p style="margin-top:12px;color:#6b7280;">Loading salary details…</p>' +
            '</div>'
        );

        $('#salaryDetailModal').modal('show');

        $.ajax({
            url: '/payrolls/' + encodeURIComponent(reference) + '/details',
            type: 'GET',
            dataType: 'json',

            success: function (response) {

                if (!response || !response.success ||
                    !Array.isArray(response.slips) || !response.slips.length) {

                    $content.html(
                        '<div class="alert alert-warning">' +
                            '<i class="fa fa-exclamation-triangle"></i> ' +
                            'No salary records found for this payroll.' +
                        '</div>'
                    );
                    return;
                }

                var period      = response.month || month || '—';
                var downloadUrl = '/payrolls/' + encodeURIComponent(reference) + '/download';

                /* ---- normalise the JSON (values arrive as "3,000,000.00") ---- */
                var slips = response.slips.map(function (s) {

                    var item = { name: s.employee_name || 'Unknown employee' };

                    FIELDS.forEach(function (field) {
                        item[field] = toNumber(s[field]);
                    });

                    item.gross      = item.basic_salary + item.allowance;
                    item.deductions = item.salary_advance + item.nssf + item.nhif +
                                      item.tuico + item.loan + item.paye;

                    return item;
                });

                /* ---- totals ---- */
                function totalsOf(list) {
                    var t = {
                        basic_salary: 0, allowance: 0, salary_advance: 0, nssf: 0,
                        nhif: 0, tuico: 0, loan: 0, paye: 0, net_salary: 0,
                        gross: 0, deductions: 0
                    };
                    list.forEach(function (s) {
                        FIELDS.forEach(function (f) { t[f] += s[f]; });
                        t.gross      += s.gross;
                        t.deductions += s.deductions;
                    });
                    return t;
                }

                /* ---- rows ---- */
                function rowsOf(list) {

                    if (!list.length) {
                        return '<tr><td colspan="11" class="text-center" ' +
                               'style="padding:26px;color:#9ca3af;">' +
                               'No employees match your search.</td></tr>';
                    }

                    return list.map(function (s, index) {

                        var negative = s.net_salary < 0;

                        return '<tr>' +
                            '<td class="sd-idx">' + (index + 1) + '</td>' +
                            '<td>' +
                                '<div class="sd-name">' +
                                    '<span class="sd-avatar">' + escapeHtml(initials(s.name)) + '</span>' +
                                    '<span>' + escapeHtml(s.name) + '</span>' +
                                '</div>' +
                            '</td>' +
                            '<td class="sd-num">' + money(s.basic_salary)  + '</td>' +
                            '<td class="sd-num">' + money(s.allowance)     + '</td>' +
                            '<td class="sd-num">' + money(s.salary_advance)+ '</td>' +
                            '<td class="sd-num">' + money(s.nssf)          + '</td>' +
                            '<td class="sd-num">' + money(s.nhif)          + '</td>' +
                            '<td class="sd-num">' + money(s.tuico)         + '</td>' +
                            '<td class="sd-num">' + money(s.loan)          + '</td>' +
                            '<td class="sd-num">' + money(s.paye)          + '</td>' +
                            '<td class="sd-num ' + (negative ? 'sd-neg' : 'sd-net') + '">' +
                                money(s.net_salary) +
                            '</td>' +
                        '</tr>';
                    }).join('');
                }

                /* ---- footer ---- */
                function footOf(t) {
                    return '<tr>' +
                        '<td colspan="2" class="sd-num">TOTAL</td>' +
                        '<td class="sd-num">' + money(t.basic_salary)   + '</td>' +
                        '<td class="sd-num">' + money(t.allowance)      + '</td>' +
                        '<td class="sd-num">' + money(t.salary_advance) + '</td>' +
                        '<td class="sd-num">' + money(t.nssf)           + '</td>' +
                        '<td class="sd-num">' + money(t.nhif)           + '</td>' +
                        '<td class="sd-num">' + money(t.tuico)          + '</td>' +
                        '<td class="sd-num">' + money(t.loan)           + '</td>' +
                        '<td class="sd-num">' + money(t.paye)           + '</td>' +
                        '<td class="sd-num sd-net">' + money(t.net_salary) + '</td>' +
                    '</tr>';
                }

                /* ---- summary cards ---- */
                function statsOf(t, count) {
                    return '' +
                        '<div class="sd-card">' +
                            '<div class="sd-label">Employees</div>' +
                            '<div class="sd-value">' + count + '</div>' +
                        '</div>' +
                        '<div class="sd-card">' +
                            '<div class="sd-label">Gross Pay</div>' +
                            '<div class="sd-value">' + money(t.gross) + '</div>' +
                        '</div>' +
                        '<div class="sd-card sd-card-red">' +
                            '<div class="sd-label">Total Deductions</div>' +
                            '<div class="sd-value">' + money(t.deductions) + '</div>' +
                        '</div>' +
                        '<div class="sd-card sd-card-green">' +
                            '<div class="sd-label">Net Pay</div>' +
                            '<div class="sd-value">' + money(t.net_salary) + '</div>' +
                        '</div>';
                }

                /* ---- shell ---- */
                var html =
                    '<div class="sd-wrap">' +

                        '<div class="sd-header">' +
                            '<div>' +
                                '<h4 class="sd-title">Salary Details' +
                                    '<span class="sd-period">' + escapeHtml(period) + '</span>' +
                                '</h4>' +
                                '<div class="sd-meta">' +
                                    '<span><i class="fa fa-hashtag"></i> ' + escapeHtml(reference) + '</span>' +
                                    '<span>•</span>' +
                                    '<span id="sd-count">' + slips.length + ' employees</span>' +
                                '</div>' +
                            '</div>' +
                            '<div class="sd-actions">' +
                                '<a href="' + downloadUrl + '" target="_blank" class="btn btn-pink btn-sm">' +
                                    '<i class="fa fa-download"></i> Download Salary' +
                                '</a>' +
                            '</div>' +
                        '</div>' +

                        '<div class="sd-stats" id="sd-stats"></div>' +

                        '<div class="sd-toolbar">' +
                            '<div class="sd-search">' +
                                '<i class="fa fa-search"></i>' +
                                '<input type="text" id="sd-search" placeholder="Search employee…" autocomplete="off">' +
                            '</div>' +
                            '<div class="sd-hint">All amounts in TZS</div>' +
                        '</div>' +

                        '<div class="sd-table-wrap">' +
                            '<table class="table sd-table" id="sd-table">' +
                                '<thead>' +
                                    '<tr>' +
                                        '<th>#</th>' +
                                        '<th>Employee Name</th>' +
                                        '<th class="sd-num">Basic Salary</th>' +
                                        '<th class="sd-num">Allowance</th>' +
                                        '<th class="sd-num">Advance</th>' +
                                        '<th class="sd-num">NSSF</th>' +
                                        '<th class="sd-num">NHIF</th>' +
                                        '<th class="sd-num">TUICO</th>' +
                                        '<th class="sd-num">Loan</th>' +
                                        '<th class="sd-num">PAYE</th>' +
                                        '<th class="sd-num">Net Salary</th>' +
                                    '</tr>' +
                                '</thead>' +
                                '<tbody id="sd-tbody"></tbody>' +
                                '<tfoot id="sd-tfoot"></tfoot>' +
                            '</table>' +
                        '</div>' +

                        '<div class="sd-note" id="sd-note"></div>' +

                    '</div>';

                $content.html(html);

                /* ---- render ---- */
                function render(list) {

                    var totals = totalsOf(list);

                    $('#sd-tbody').html(rowsOf(list));
                    $('#sd-tfoot').html(footOf(totals));
                    $('#sd-stats').html(statsOf(totals, list.length));

                    $('#sd-count').text(list.length + ' of ' + slips.length + ' employees');

                    $('#sd-note').text(
                        list.length === slips.length
                            ? 'Showing all ' + slips.length + ' salary record(s).'
                            : 'Filtered: ' + list.length + ' of ' + slips.length + ' record(s).'
                    );
                }

                render(slips);

                /* ---- search ---- */
                $content.on('input.salary', '#sd-search', function () {

                    var query = $.trim($(this).val()).toLowerCase();

                    if (!query) {
                        render(slips);
                        return;
                    }

                    render(slips.filter(function (s) {
                        return s.name.toLowerCase().indexOf(query) !== -1;
                    }));
                });

                /* ---- print ---- */
                $content.on('click.salary', '#sd-print', function () {

                    var printWindow = window.open('', '_blank', 'width=1150,height=750');
                    if (!printWindow) {
                        alert('Please allow pop-ups to print this salary sheet.');
                        return;
                    }

                    var tableHtml = $('#sd-table')[0].outerHTML;

                    printWindow.document.open();
                    printWindow.document.write(
                        '<!doctype html><html><head><title>' +
                            escapeHtml('Salary Details - ' + period) +
                        '</title><style>' +
                            'body{font-family:Arial,Helvetica,sans-serif;padding:18px;color:#111;}' +
                            'h2{font-size:16px;margin:0 0 2px;}' +
                            'p.sub{font-size:11px;color:#555;margin:0 0 14px;}' +
                            'table{width:100%;border-collapse:collapse;font-size:10px;}' +
                            'th,td{border:1px solid #bbb;padding:5px 6px;}' +
                            'th{background:#f2f2f2;text-transform:uppercase;font-size:9px;text-align:left;}' +
                            'tfoot td{background:#f8f8f8;font-weight:bold;}' +
                            '.sd-num{text-align:right;}' +
                            '.sd-avatar,.sd-idx{display:none;}' +
                            '.sd-name{display:block;}' +
                        '</style></head><body>' +
                        '<h2>Salary Details — ' + escapeHtml(period) + '</h2>' +
                        '<p class="sub">Reference: ' + escapeHtml(reference) +
                            ' &nbsp;|&nbsp; Employees: ' + slips.length + '</p>' +
                        tableHtml +
                        '</body></html>'
                    );
                    printWindow.document.close();
                    printWindow.focus();
                    printWindow.print();
                });
            },

            error: function (xhr) {

                var message = (xhr.responseJSON && xhr.responseJSON.message) ||
                              'Endpoint not available.';

                $content.html(
                    '<div class="alert alert-warning">' +
                        '<i class="fa fa-exclamation-triangle"></i> ' +
                        'Unable to load salary details for ' +
                        escapeHtml(month || reference) + '.' +
                        '<br><small>' + escapeHtml(message) + '</small>' +
                    '</div>'
                );
            }
        });
    });

})();


    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
    }

    $(document).on('click', '.view-bank-btn', function () {
        const reference = $(this).data('reference');
        $('.sal-dropdown').removeClass('open');
        $('#bankModal .modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
        $('#bankModal').modal('show');
        $.ajax({
            url: `/payrolls/${encodeURIComponent(reference)}/bank-voucher`,
            type: 'GET', dataType: 'html',
            success: function (html) { $('#bankModal .modal-body').html(html); },
            error: function (xhr) {
                $('#bankModal .modal-body').html(
                    '<div class="alert alert-warning">Bank voucher is not available for this period.<br><small>' +
                    ((xhr.responseJSON && xhr.responseJSON.message) || '') +
                    '</small></div>'
                );
            }
        });
    });

    $(document).on('click', '.view-nssf-btn', function () {
        const month = $(this).data('month');
        $('.sal-dropdown').removeClass('open');
        $('#bankModal .modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
        $('#bankModal').modal('show');
        $.ajax({
            url: `/payrolls/nssf-voucher?month=${encodeURIComponent(month)}`,
            type: 'GET', dataType: 'html',
            success: function (html) { $('#bankModal .modal-body').html(html); },
            error: function (xhr) {
                $('#bankModal .modal-body').html(
                    '<div class="alert alert-warning">NSSF voucher is not available for ' + month + '.<br><small>' +
                    ((xhr.responseJSON && xhr.responseJSON.message) || '') +
                    '</small></div>'
                );
            }
        });
    });

    $(document).on('click', '.view-tuico-btn', function () {
        const month = $(this).data('month');
        $('.sal-dropdown').removeClass('open');
        $('#bankModal .modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
        $('#bankModal').modal('show');
        $.ajax({
            url: `/payrolls/tuico-voucher?month=${encodeURIComponent(month)}`,
            type: 'GET', dataType: 'html',
            success: function (html) { $('#bankModal .modal-body').html(html); },
            error: function (xhr) {
                $('#bankModal .modal-body').html(
                    '<div class="alert alert-warning">TUICO voucher is not available for ' + month + '.<br><small>' +
                    ((xhr.responseJSON && xhr.responseJSON.message) || '') +
                    '</small></div>'
                );
            }
        });
    });

   $(document).on('click', '.rollback-btn', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    $('.sal-dropdown').removeClass('open');

    const hasSwal = (typeof Swal !== 'undefined');

    const confirmHtml =
        '<div style="text-align:left;font-size:13px;line-height:1.7;">' +
            'This will permanently delete:' +
            '<ul style="margin:10px 0 0 18px;padding:0;">' +
                '<li>All payroll records for this month</li>' +
                '<li>All loan repayments created by this payroll</li>' +
            '</ul>' +
            '<div style="color:#dc2626;margin-top:12px;font-weight:700;">' +
                '<i class="fa fa-exclamation-triangle"></i> This action cannot be undone.' +
            '</div>' +
        '</div>';

    if (!hasSwal) {
        if (confirm('Rollback this payroll? This will delete all payrolls, loan repayments, and requests for this month.')) {
            runRollback(id);
        }
        return;
    }

    Swal.fire({
        icon: 'warning',
        title: 'Rollback Payroll?',
        html: confirmHtml,
        showCancelButton: true,
        confirmButtonText: '<i class="fa fa-undo"></i> Yes, rollback',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        reverseButtons: true,
        focusCancel: true
    }).then(function (result) {
        if (result.isConfirmed) {
            runRollback(id);
        }
    });

    function runRollback(payrollId) {
        if (hasSwal) {
            Swal.fire({
                title: 'Rolling back…',
                html: 'Please wait while we delete the payroll and restore loan balances.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: function () { Swal.showLoading(); }
            });
        }

        $.ajax({
            url: '/payrolls/' + encodeURIComponent(payrollId) + '/rollback',
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') }
        })
        .done(function (res) {
            const msg = (res && res.message) || 'Payroll rolled back successfully.';
            if (hasSwal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Rolled Back',
                    text: msg,
                    confirmButtonColor: '#475569'
                }).then(function () { window.location.reload(); });
            } else {
                alert(msg);
                window.location.reload();
            }
        })
        .fail(function (xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Unable to rollback payroll.';
            if (hasSwal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Rollback Failed',
                    text: msg,
                    confirmButtonColor: '#dc2626'
                });
            } else {
                alert(msg);
            }
        });
    }
});


(function () {

    'use strict';

    /* ──────────────────────────────────────────────
       SweetAlert wrapper (with graceful fallback)
       ────────────────────────────────────────────── */
    function notify(type, title, text) {
        // SweetAlert2
        if (window.Swal && typeof Swal.fire === 'function') {
            return Swal.fire({
                icon: type,                 // 'warning' | 'error' | 'success' | 'info'
                title: title,
                text: text || '',
                confirmButtonColor: '#ec4899',
                confirmButtonText: 'OK'
            });
        }
        // SweetAlert v1
        if (window.swal && typeof swal === 'function') {
            return swal(title, text || '', type);
        }
        // Native fallback
        alert(title + (text ? '\n\n' + text : ''));
    }

    /* ──────────────────────────────────────────────
       helpers (unchanged from previous version)
       ────────────────────────────────────────────── */
    function escapeHtml(v) {
        return String(v == null ? '' : v)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
    function toNumber(v) {
        if (typeof v === 'number') return isFinite(v) ? v : 0;
        if (v == null || v === '') return 0;
        var n = parseFloat(String(v).replace(/[^0-9.\-]/g, ''));
        return isFinite(n) ? n : 0;
    }
    function money(v) {
        return toNumber(v).toLocaleString('en-US', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        });
    }

    function ensureStyles() {
        if (document.getElementById('ps-styles')) return;
        var style = document.createElement('style');
        style.id = 'ps-styles';
        style.textContent = `
            .ps-wrap { font-size:13px; color:#1f2937; font-family:inherit; }
            .ps-header { display:flex; flex-wrap:wrap; gap:12px; justify-content:space-between;
                         align-items:flex-start; padding-bottom:14px; margin-bottom:16px;
                         border-bottom:1px solid #e9edf3; }
            .ps-title { margin:0 0 5px; font-size:18px; font-weight:700; color:#111827; }
            .ps-period { display:inline-block; margin-left:6px; padding:2px 10px;
                         border-radius:20px; background:#fdf2f8; color:#be185d;
                         font-size:12px; font-weight:600; vertical-align:middle; }
            .ps-meta { font-size:12px; color:#6b7280; display:flex; gap:8px; flex-wrap:wrap; }
            .ps-actions { display:flex; gap:8px; flex-wrap:wrap; }
            .ps-cols { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
            @media (max-width:640px) { .ps-cols { grid-template-columns:1fr; } }
            .ps-col { border:1px solid #e9edf3; border-radius:10px; overflow:hidden; background:#fff; }
            .ps-col-title { font-size:11px; text-transform:uppercase; letter-spacing:.04em;
                            font-weight:700; color: #333132; background:#fdf2f8; padding:8px 12px; }
            .ps-table { width:100%; margin:0; font-size:12px; border-collapse:collapse; }
            .ps-table td { padding:8px 12px; border-top:1px solid #f1f4f9; }
            .ps-table tr:first-child td { border-top:none; }
            .ps-table tfoot td { background:#f8fafc; font-weight:700; border-top:2px solid #e5e9f0; }
            .ps-num { text-align:right; font-variant-numeric:tabular-nums; }
            .ps-net { margin-top:16px; padding:14px 18px;
                      background:linear-gradient(90deg,#ecfdf5,#f0fdf4);
                      border:1px solid #bbf7d0; border-radius:10px;
                      display:flex; justify-content:space-between; align-items:center;
                      font-size:13px; color:#065f46; font-weight:600; }
            .ps-net strong { font-size:20px; color:#047857; font-variant-numeric:tabular-nums; }
        `;
        document.head.appendChild(style);
    }

    function renderSalarySlip(slip, period, employeeId, month) {
        var basic     = toNumber(slip.basic_salary);
        var allowance = toNumber(slip.allowance);
        var gross     = basic + allowance;

        var deductions = [
            ['Salary Advance', toNumber(slip.salary_advance)],
            ['NSSF',           toNumber(slip.nssf)],
            ['WCF',            toNumber(slip.wcf)],
            ['NHIF',           toNumber(slip.nhif)],
            ['TUICO',          toNumber(slip.tuico)],
            ['Loan',           toNumber(slip.loan)],
            ['PAYE',           toNumber(slip.paye)]
        ].filter(function (d) { return d[1] > 0; });

        var totalDed = deductions.reduce(function (s, d) { return s + d[1]; }, 0);
        var net      = toNumber(slip.net_salary);

        var dedRows = deductions.map(function (d) {
            return '<tr><td>' + escapeHtml(d[0]) + '</td>' +
                   '<td class="ps-num">' + money(d[1]) + '</td></tr>';
        }).join('') ||
            '<tr><td colspan="2" class="ps-num" style="color:#9ca3af;">No deductions</td></tr>';

        var downloadUrl = '/payrolls/salary-slip-download/view/pdf' +
                          '?employee_id=' + encodeURIComponent(employeeId) +
                          '&month=' + encodeURIComponent(month);

        return '' +
        '<div class="ps-wrap" id="ps-slip">' +
            '<div class="ps-header">' +
                '<div>' +
                    '<h4 class="ps-title">Payslip ' +
                        '<span class="ps-period">' + escapeHtml(period) + '</span></h4>' +
                    '<div class="ps-meta">' +
                        '<span><i class="fa fa-user"></i> ' + escapeHtml(slip.employee_name) + '</span>' +
                        (slip.reference
                            ? '<span>•</span><span><i class="fa fa-hashtag"></i> ' +
                              escapeHtml(slip.reference) + '</span>'
                            : '') +
                    '</div>' +
                '</div>' +
                '<div class="ps-actions">' +
                    '<button type="button" class="btn btn-default btn-sm" id="ps-print">' +
                        '<i class="fa fa-print"></i> Print</button>' +
                    '<a class="btn btn-pink btn-sm" target="_blank" href="' + downloadUrl + '">' +
                        '<i class="fa fa-download"></i> Download PDF</a>' +
                '</div>' +
            '</div>' +
            '<div class="ps-cols">' +
                '<div class="ps-col">' +
                    '<div class="ps-col-title">Earnings</div>' +
                    '<table class="ps-table">' +
                        '<tr><td>Basic Salary</td><td class="ps-num">' + money(basic) + '</td></tr>' +
                        '<tr><td>Allowance</td><td class="ps-num">' + money(allowance) + '</td></tr>' +
                        '<tfoot><tr><td>Gross Salary</td><td class="ps-num">' + money(gross) + '</td></tr></tfoot>' +
                    '</table>' +
                '</div>' +
                '<div class="ps-col">' +
                    '<div class="ps-col-title">Deductions</div>' +
                    '<table class="ps-table">' +
                        dedRows +
                        '<tfoot><tr><td>Total Deductions</td><td class="ps-num">' + money(totalDed) + '</td></tr></tfoot>' +
                    '</table>' +
                '</div>' +
            '</div>' +
            '<div class="ps-net">' +
                '<span>Net Salary</span><strong>' + money(net) + '</strong>' +
            '</div>' +
        '</div>';
    }

    /* ──────────────────────────────────────────────
       click handler — SweetAlert replaces alert()
       ────────────────────────────────────────────── */
    $(document).on('click', '#generate-slip', function () {

        var employeeId = $('#employee_id').val();
        var month      = $('#month').val();

        if (!employeeId) {
            notify('warning', 'Missing employee', 'Please select an employee first.');
            return;
        }
        if (!month) {
            notify('warning', 'Missing month', 'Please select a month first.');
            return;
        }

        ensureStyles();
        $('#loading-spinner').show();
        $('#salary-slip-result').html('');

        $.ajax({
            url: '/salary-slip?employee_id=' + encodeURIComponent(employeeId) +
                 '&month=' + encodeURIComponent(month),
            type: 'GET',
            dataType: 'json',

            success: function (data) {
                if (!data || !data.success ||
                    !Array.isArray(data.slips) || !data.slips.length) {
                    $('#salary-slip-result').html('');
                    notify('info', 'No slip found',
                        'There is no salary slip for this employee in the selected month.');
                    return;
                }
                var slip   = data.slips[0];
                var period = data.month || month;
                $('#salary-slip-result').html(
                    renderSalarySlip(slip, period, employeeId, month)
                );
            },

            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                          'Endpoint not available.';
                $('#salary-slip-result').html('');
                notify('error', 'Unable to generate slip', msg);
            },

            complete: function () {
                $('#loading-spinner').hide();
            }
        });
    });

    /* ──────────────────────────────────────────────
       print — SweetAlert for popup block
       ────────────────────────────────────────────── */
    $(document).on('click', '#ps-print', function () {
        var slipEl = document.getElementById('ps-slip');
        if (!slipEl) return;

        var win = window.open('', '_blank', 'width=900,height=720');
        if (!win) {
            notify('error', 'Pop-up blocked',
                'Please allow pop-ups for this site so the payslip can be printed.');
            return;
        }

        win.document.open();
        win.document.write(
            '<!doctype html><html><head><title>Payslip</title><style>' +
                'body{font-family:Arial,Helvetica,sans-serif;padding:24px;' +
                'color:#111;max-width:720px;margin:0 auto;}' +
                '.ps-header{display:flex;justify-content:space-between;align-items:flex-start;' +
                'border-bottom:2px solid #be185d;padding-bottom:10px;margin-bottom:16px;}' +
                'h4{font-size:16px;margin:0;}' +
                '.ps-period{background:#fdf2f8;color:#be185d;padding:2px 8px;' +
                'border-radius:12px;font-size:11px;margin-left:6px;}' +
                '.ps-meta{font-size:11px;color:#555;margin-top:4px;}' +
                '.ps-cols{display:flex;gap:18px;}' +
                '.ps-col{flex:1;}' +
                '.ps-col-title{font-size:11px;text-transform:uppercase;color:#be185d;' +
                'font-weight:bold;margin-bottom:6px;letter-spacing:.04em;}' +
                '.ps-table{width:100%;border-collapse:collapse;font-size:11px;}' +
                '.ps-table td{padding:6px 8px;border-bottom:1px solid #eee;}' +
                '.ps-table tfoot td{border-top:2px solid #ccc;border-bottom:none;' +
                'font-weight:bold;padding-top:8px;}' +
                '.ps-num{text-align:right;font-variant-numeric:tabular-nums;}' +
                '.ps-net{margin-top:16px;padding:12px 16px;background:#ecfdf5;' +
                'border-radius:8px;display:flex;justify-content:space-between;' +
                'align-items:center;font-size:13px;}' +
                '.ps-net strong{font-size:18px;color:#047857;}' +
                '.ps-actions{display:none;}' +
            '</style></head><body>' + slipEl.outerHTML + '</body></html>'
        );
        win.document.close();
        win.focus();
        win.print();
    });

})();
</script>

{{-- SweetAlert confirmations for destructive loan actions --}}
<script>
$(function () {
    const hasSwal = (typeof Swal !== 'undefined');

    $(document).on('click', '.delete-loan-btn', function (e) {
        e.preventDefault();
        const $btn    = $(this);
        const $form   = $btn.closest('form.delete-loan-form');
        const type    = $btn.data('loan-type') || 'this loan';
        const emp     = $btn.data('employee')  || '';
        const balance = $btn.data('balance')   || '0.00';

        const confirmHtml =
            '<div style="text-align:left;font-size:13px;line-height:1.6;">' +
                '<div style="margin-bottom:8px;"><strong>Employee:</strong> ' + emp + '</div>' +
                '<div style="margin-bottom:8px;"><strong>Loan type:</strong> ' + type + '</div>' +
                '<div style="margin-bottom:8px;"><strong>Outstanding balance:</strong> TZS ' + balance + '</div>' +
                '<div style="color:#dc2626;margin-top:12px;">' +
                    '<i class="fa fa-exclamation-triangle"></i> All repayments for this loan will also be deleted.' +
                '</div>' +
            '</div>';

        if (!hasSwal) {
            if (confirm('Delete ' + type + ' loan for ' + emp + '? All repayments will also be deleted.')) {
                $form.trigger('submit');
            }
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Delete this loan?',
            html: confirmHtml,
            showCancelButton: true,
            confirmButtonText: '<i class="fa fa-trash"></i> Yes, delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
            focusCancel: true
        }).then(function (result) {
            if (result.isConfirmed) { $form.trigger('submit'); }
        });
    });
});
</script>

{{-- ==========================================================
     EMPLOYEE LOANS — balance from repayments + date-filtered history
     ========================================================== --}}
<script>
window.LOAN_EMPLOYEES  = @json($loanByEmployee);
window.LOAN_STATEMENTS = @json($loanStatements);

function numberFormat(n) {
    return Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
window.numberFormat = numberFormat;

/* ---------- Statement (modal) ---------- */
function loadLoanDetails(loanId) {
    const loan = (window.LOAN_STATEMENTS || {})[loanId];
    if (!loan) {
        $('#loanDetailsBody').html('<div class="alert alert-danger">Loan not found.</div>');
        return;
    }
    const progress = Math.min(100, loan.progress || 0);

    $('#loanDetailsBody').html(`
        <div class="loan-statement-wrap">
            <div class="loan-statement-header">
                <h3>NDUVINI AUTOWORKS (NAW)</h3>
                <h4>Loan Statement</h4>
                <p>Generated on ${new Date().toLocaleDateString()}</p>
            </div>
            <table class="loan-statement-table">
                <tr><th>Employee</th><td>${loan.employee_name}</td></tr>
                <tr><th>Loan Type</th><td style="text-transform:capitalize;">${loan.type}</td></tr>
                <tr><th>Start Date</th><td>${loan.start_date}</td></tr>
                <tr><th>Principal</th><td class="is-num">TZS ${numberFormat(loan.principal)}</td></tr>
                <tr><th>Monthly Deduction</th><td class="is-num">TZS ${numberFormat(loan.monthly_deduction)}</td></tr>
                <tr><th>Total Repaid (${loan.repayment_count} payments)</th>
                    <td class="is-num is-amount">TZS ${numberFormat(loan.repaid)}</td></tr>
                <tr><th>Outstanding Balance</th>
                    <td class="is-num is-outstanding">TZS ${numberFormat(loan.balance)}</td></tr>
                <tr>
                    <th>Repayment Progress</th>
                    <td>
                        <strong style="font-family:'SFMono-Regular',Consolas,monospace;">${progress.toFixed(1)}%</strong>
                        <div class="loan-statement-progress">
                            <div class="loan-statement-progress-fill" style="width:${progress}%;"></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>`);
}
window.loadLoanDetails = loadLoanDetails;

function printLoanDetails(containerId) {
    const el = document.getElementById(containerId);
    if (!el) return;
    const w = window.open('', '_blank');
    w.document.write('<html><head><title>Loan Statement</title><style>' +
        'body{font-family:Arial,Helvetica,sans-serif;padding:24px;color:#0f172a;}' +
        '.no-print{display:none!important;}h2,h3,h4{text-align:center;margin:0 0 6px;}' +
        'table{width:100%;border-collapse:collapse;margin-top:16px;}' +
        'th,td{padding:8px 10px;border:1px solid #d1d5db;text-align:left;font-size:13px;}' +
        'th{background:#f3f4f6;font-weight:700;}' +
        '.modal-header .close,.modal-footer{display:none!important;}' +
        '</style></head><body>' + el.innerHTML + '</body></html>');
    w.document.close(); w.focus(); w.print(); w.close();
}
window.printLoanDetails = printLoanDetails;

/* ---------- Expand / collapse ---------- */
$(document).on('click', '.expand-loans-btn', function (e) {
    e.stopPropagation();
    const empId = $(this).data('employee-id');
    const $row  = $('.loan-detail-row[data-parent="' + empId + '"]');
    const open  = $row.is(':visible');
    $row.toggle(!open);
    $(this).toggleClass('is-open', !open);
});

/* ---------- Filters ---------- */
function filterLoanTable() {
    const search = ($('#loanSearchInput').val() || '').toLowerCase().trim();
    const status = $('#loanStatusFilter').val();
    let visible  = 0;
    const $rows  = $('#loansTable tbody tr.loan-emp-row');
    $rows.each(function () {
        const name      = ($(this).data('employee-name') || '').toLowerCase();
        const rowStatus = $(this).data('status');
        const show      = (!search || name.indexOf(search) !== -1) && (!status || rowStatus === status);
        $(this).toggle(show);
        const empId = $(this).data('employee-id');
        if (!show) {
            $('.loan-detail-row[data-parent="' + empId + '"]').hide();
            $('.expand-loans-btn[data-employee-id="' + empId + '"]').removeClass('is-open');
        }
        if (show) visible++;
    });
    const $cnt = $('#loanFilterCount');
    if (search || status) $cnt.text(visible + ' / ' + $rows.length).show();
    else $cnt.hide().text('');
}
$(document).on('input change', '#loanSearchInput, #loanStatusFilter', filterLoanTable);
$(document).on('click', '#loanSearchReset', function () {
    $('#loanSearchInput').val('');
    $('#loanStatusFilter').val('');
    filterLoanTable();
});

/* ---------- Record Repayment modal ---------- */
$(document).on('click', '.record-repayment-btn', function () {
    const loanId    = $(this).data('loan-id');
    const empId     = $(this).data('employee-id');
    const loanLabel = $(this).data('loan-label');
    const balance   = parseFloat($(this).data('balance')) || 0;

    $('#rrLoanLabel').text(loanLabel);
    $('#rrBalanceLabel').text('Balance: TZS ' + numberFormat(balance));
    $('#rrAmount').val('').attr('max', balance).attr('placeholder', 'Max ' + numberFormat(balance));
    $('#rrEmployeeId').val(empId);
    $('#recordRepaymentForm').attr('action', '/loans/' + loanId + '/repayments');
    $('#recordRepaymentModal').modal('show');
});

/* ---------- Repayment History modal ---------- */
let lrCurrentEmployee = null;

function renderLoanEmployeeList(filter) {
    const list = $('#lrEmployeeList');
    const data = window.LOAN_EMPLOYEES || [];
    const q    = (filter || '').toLowerCase().trim();
    const filtered = data.filter(e => !q || String(e.employee_name || '').toLowerCase().indexOf(q) !== -1);

    if (!filtered.length) {
        list.html('<div class="lr-empty">No employees found.</div>');
        return;
    }

    let html = '';
    filtered.forEach(function (emp) {
        const progress = emp.total_principal > 0
            ? Math.min(100, (emp.total_repaid / emp.total_principal) * 100)
            : 0;
        let badgeClass = 'rp-badge-active', badgeText = 'Active';
        if (emp.total_balance <= 0) { badgeClass = 'rp-badge-repaid'; badgeText = 'Repaid'; }
        else if (emp.total_repaid > 0) { badgeClass = 'rp-badge-mixed'; }

        html += '<div class="lr-emp-item" data-employee-id="' + emp.employee_id + '">' +
                    '<div class="lr-emp-item-name"><i class="fa fa-user"></i> ' + emp.employee_name + '</div>' +
                    '<div class="lr-emp-item-meta">' +
                        '<span class="lr-emp-badge ' + badgeClass + '">' + badgeText + '</span>' +
                        '<span>' + emp.loan_count + ' loan' + (emp.loan_count !== 1 ? 's' : '') + '</span>' +
                    '</div>' +
                    '<div class="lr-emp-mini-bar"><div class="lr-emp-mini-fill" style="width:' + progress + '%;"></div></div>' +
                '</div>';
    });
    list.html(html);
}

function renderLoanEmployeeDetail(employeeId) {
    const emp = (window.LOAN_EMPLOYEES || []).find(e => String(e.employee_id) === String(employeeId));
    const pane = $('#lrDetailPane');

    if (!emp) {
        pane.html('<div class="text-center text-muted" style="padding:60px 20px;">Employee not found.</div>');
        return;
    }

    lrCurrentEmployee = emp;

    const progress = emp.total_principal > 0
        ? Math.min(100, (emp.total_repaid / emp.total_principal) * 100)
        : 0;
    const initials = (emp.employee_name || '').split(/\s+/).filter(Boolean)
        .map(n => n.charAt(0)).slice(0, 2).join('').toUpperCase() || '—';

    pane.html(
        '<div class="lr-hero">' +
            '<div class="lr-avatar">' + initials + '</div>' +
            '<h3 class="lr-name">' + emp.employee_name + '</h3>' +
            '<div class="lr-role">Employee #' + emp.employee_id + '</div>' +
            '<div class="lr-stats">' +
                '<div class="lr-stat"><div class="lr-stat-label">Principal</div>' +
                    '<div class="lr-stat-value">' + numberFormat(emp.total_principal) + '</div></div>' +
                '<div class="lr-stat"><div class="lr-stat-label">Repaid</div>' +
                    '<div class="lr-stat-value">' + numberFormat(emp.total_repaid) + '</div></div>' +
                '<div class="lr-stat lr-stat-outstanding"><div class="lr-stat-label">Outstanding</div>' +
                    '<div class="lr-stat-value">' + numberFormat(emp.total_balance) + '</div></div>' +
            '</div>' +
        '</div>' +

        '<div class="lr-date-filter">' +
            '<label><i class="fa fa-calendar"></i> Repayment Period</label>' +
            '<input type="date" id="lrDateFrom">' +
            '<span style="color:#94a3b8;font-size:11px;">to</span>' +
            '<input type="date" id="lrDateTo">' +
            '<button type="button" class="lr-date-clear" id="lrDateClear">Clear</button>' +
            '<span class="lr-date-summary" id="lrDateSummary"></span>' +
        '</div>' +

        '<div class="lr-progress-section">' +
            '<div class="lr-progress-label"><span>Overall Repayment Progress</span><strong>' +
                progress.toFixed(1) + '%</strong></div>' +
            '<div class="lr-progress-track"><div class="lr-progress-fill" style="width:' + progress + '%;"></div></div>' +
            '<div class="lr-progress-meta"><span>' + numberFormat(emp.total_repaid) + ' repaid</span>' +
                '<span>' + numberFormat(emp.total_balance) + ' remaining</span></div>' +
        '</div>' +

        '<h4 class="lr-loans-title"><i class="fa fa-list-ul"></i> Loans ' +
            '<span>' + emp.loan_count + ' total</span></h4>' +
        '<div id="lrLoansList"></div>'
    );

    renderFilteredLoans();
}

function renderFilteredLoans() {
    if (!lrCurrentEmployee) return;

    const from = $('#lrDateFrom').val();
    const to   = $('#lrDateTo').val();

    let totalPaid  = 0;
    let totalCount = 0;
    let html       = '';

    (lrCurrentEmployee.loans || []).forEach(function (loan) {
        const repayments = (loan.repayments || []).filter(function (r) {
            if (!r.date_raw) return true;
            if (from && r.date_raw < from) return false;
            if (to && r.date_raw > to) return false;
            return true;
        });

        const paidInRange = repayments.reduce((s, r) => s + Number(r.amount || 0), 0);
        totalPaid  += paidInRange;
        totalCount += repayments.length;

        html += renderLoanCard(loan, repayments, from || to);
    });

    $('#lrLoansList').html(html || '<div class="lr-empty">No loans.</div>');

    const suffix = (from || to) ? ' in range' : ' (all time)';
    $('#lrDateSummary').text(
        totalCount + ' payment' + (totalCount !== 1 ? 's' : '') +
        ' • TZS ' + numberFormat(totalPaid) + suffix
    );
}

function renderLoanCard(loan, repayments, filtered) {
    const isRepaid = loan.balance <= 0;
    const progress = loan.progress || 0;

    let repaymentsHtml = '';
    if (repayments.length) {
        repaymentsHtml =
            '<div class="lr-repayments">' +
                '<div class="lr-repayments-title">' +
                    '<span><i class="fa fa-list-ol"></i> Repayments</span>' +
                    '<span>' + repayments.length + ' entr' + (repayments.length !== 1 ? 'ies' : 'y') + '</span>' +
                '</div>' +
                '<table class="lr-repayments-table">' +
                    '<thead><tr><th>Date</th><th style="text-align:right;">Amount</th><th>Notes</th></tr></thead>' +
                    '<tbody>' +
                        repayments.map(r =>
                            '<tr>' +
                                '<td>' + r.date + '</td>' +
                                '<td class="is-amount">' + numberFormat(r.amount) + '</td>' +
                                '<td style="color:#64748b;">' + (r.notes || '—') + '</td>' +
                            '</tr>'
                        ).join('') +
                    '</tbody>' +
                '</table>' +
            '</div>';
    } else {
        repaymentsHtml =
            '<div class="lr-repayments">' +
                '<div class="lr-repayments-title">' +
                    '<span><i class="fa fa-list-ol"></i> Repayments</span>' +
                '</div>' +
                '<div class="lr-repayments-empty">' +
                    (filtered ? 'No repayments in selected period.' : 'No repayments recorded yet.') +
                '</div>' +
            '</div>';
    }

    return '<div class="lr-loan-card ' + (isRepaid ? 'is-repaid' : '') + '">' +
        '<div class="lr-loan-header">' +
            '<div class="lr-loan-type"><i class="fa fa-money"></i> ' + loan.type + '</div>' +
            '<div class="lr-loan-head-right">' +
                '<a class="lr-pdf-link" href="/loans/' + loan.id + '/statement-pdf" target="_blank" title="Download PDF Statement">' +
                    '<i class="fa fa-file-pdf-o"></i>' + 'Download Stement' +
                '</a>' +
                '<span class="lr-loan-status ' + (isRepaid ? 'is-repaid' : 'is-active') + '">' +
                    (isRepaid ? 'Fully Repaid' : 'Active') +
                '</span>' +
            '</div>' +
        '</div>' +
        '<div class="lr-loan-grid">' +
            '<div class="lr-loan-field"><div class="lr-loan-field-label">Principal</div>' +
                '<div class="lr-loan-field-value">' + numberFormat(loan.principal) + '</div></div>' +
            '<div class="lr-loan-field"><div class="lr-loan-field-label">Repaid</div>' +
                '<div class="lr-loan-field-value" style="color:#334155;">' + numberFormat(loan.repaid) + '</div></div>' +
            '<div class="lr-loan-field"><div class="lr-loan-field-label">Balance</div>' +
                '<div class="lr-loan-field-value">' + numberFormat(loan.balance) + '</div></div>' +
            '<div class="lr-loan-field"><div class="lr-loan-field-label">Start</div>' +
                '<div class="lr-loan-field-value">' + loan.start_date + '</div></div>' +
        '</div>' +
        '<div class="lr-loan-progress"><div class="lr-loan-progress-fill" style="width:' + progress + '%;"></div></div>' +
        '<div class="lr-loan-progress-meta">' +
            '<span>' + progress.toFixed(0) + '% repaid</span>' +
            '<span>' + numberFormat(loan.repaid) + ' / ' + numberFormat(loan.principal) + '</span>' +
        '</div>' +
        repaymentsHtml +
    '</div>';
}

function selectLoanEmployee(employeeId) {
    $('.lr-emp-item').removeClass('is-active');
    const $item = $('.lr-emp-item[data-employee-id="' + employeeId + '"]');
    if ($item.length) $item.addClass('is-active');
    renderLoanEmployeeDetail(employeeId);
}

$(document).on('click', '.lr-emp-item', function () {
    selectLoanEmployee($(this).data('employee-id'));
});

$(document).on('input', '#lrEmployeeSearch', function () {
    renderLoanEmployeeList($(this).val());
});

$(document).on('change', '#lrDateFrom, #lrDateTo', function () {
    renderFilteredLoans();
});

$(document).on('click', '#lrDateClear', function () {
    $('#lrDateFrom').val('');
    $('#lrDateTo').val('');
    renderFilteredLoans();
});

$(document).on('click', '#openLoanRepaymentHistoryBtn', function () {
    $('#lrEmployeeSearch').val('');
    renderLoanEmployeeList('');
    $('#loanRepaymentModal').modal('show');
});

$(document).on('click', '.open-loan-history-btn', function () {
    const empId = $(this).data('employee-id');
    $('#lrEmployeeSearch').val('');
    renderLoanEmployeeList('');
    $('#loanRepaymentModal').modal('show');
    $('#loanRepaymentModal').one('shown.bs.modal', function () {
        selectLoanEmployee(empId);
    });
});

$(function () { filterLoanTable(); });
</script>

@endsection
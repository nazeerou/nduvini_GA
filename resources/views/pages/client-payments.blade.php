@extends('layouts.app_header')
@section('content')

<style>
    /* ===================== Client Payments – Professional UI ===================== */
    :root{
        /* Core palette */
        --cp-primary:#1e3a5f;
        --cp-primary-dark:#152a45;
        --cp-primary-light:#2d5282;
        --cp-ink:#0f172a;
        --cp-ink-soft:#334155;
        --cp-muted:#64748b;
        --cp-line:#e2e8f0;
        --cp-line-soft:#f1f5f9;
        --cp-soft:#f8fafc;
        --cp-white:#ffffff;

        /* Semantic colors */
        --cp-paid:#059669;
        --cp-paid-dark:#047857;
        --cp-paid-bg:#ecfdf5;
        --cp-paid-border:#a7f3d0;

        --cp-unpaid:#dc2626;
        --cp-unpaid-dark:#b91c1c;
        --cp-unpaid-bg:#fef2f2;
        --cp-unpaid-border:#fecaca;

        --cp-partial:#d97706;
        --cp-partial-dark:#b45309;
        --cp-partial-bg:#fffbeb;
        --cp-partial-border:#fde68a;

        --cp-overpaid:#065f46;
        --cp-overpaid-bg:#d1fae5;

        /* Shadows */
        --cp-shadow-sm:0 1px 2px rgba(15,23,42,.04);
        --cp-shadow:0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
        --cp-shadow-md:0 4px 12px rgba(15,23,42,.08), 0 2px 4px rgba(15,23,42,.04);
    }

    .cp-wrap *{box-sizing:border-box;}
    .cp-wrap .panel-custom{
        background:var(--cp-white);
        border:1px solid var(--cp-line);
        border-radius:14px;overflow:hidden;
        box-shadow:var(--cp-shadow);
        border-top:none;
    }

    /* ---------- Step indicator ---------- */
    .cp-steps{display:flex;justify-content:flex-end;align-items:center;gap:8px;margin-bottom:20px;}
    .cp-steps a{
        font-size:12px;font-weight:500;letter-spacing:.02em;padding:7px 14px;border-radius:8px;
        background:var(--cp-white);color:var(--cp-muted);text-decoration:none;
        border:1px solid var(--cp-line);transition:all .2s ease;
    }
    .cp-steps a:hover{background:var(--cp-line-soft);color:var(--cp-ink);text-decoration:none;}
    .cp-steps a.completed{background:var(--cp-white);color:var(--cp-ink-soft);}
    .cp-steps a.active{
        background:var(--cp-primary);color:var(--cp-white);border-color:var(--cp-primary);
        box-shadow:0 2px 6px rgba(30,58,95,.22);
    }
    .cp-steps .cp-sep{color:#cbd5e1;font-size:11px;}

    /* ---------- Card header ---------- */
    .cp-head{
        display:flex;justify-content:space-between;align-items:flex-start;gap:20px;
        padding:24px 28px;border-bottom:1px solid var(--cp-line);
        background:linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
    }
    .cp-head-text{min-width:0;}
    .cp-head-text h3{
        margin:0 0 6px;font-size:19px;font-weight:700;color:var(--cp-ink);letter-spacing:-.2px;
    }
    .cp-head-text h3 i{color:var(--cp-primary);margin-right:8px;}
    .cp-head-text p{margin:0;font-size:13px;color:var(--cp-muted);line-height:1.5;}

    /* ---------- Buttons ---------- */
    .cp-btn-primary{
        display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border:none;
        border-radius:8px;font-size:13px;font-weight:600;letter-spacing:.01em;cursor:pointer;
        color:var(--cp-white);
        background:linear-gradient(180deg, var(--cp-primary-light) 0%, var(--cp-primary) 100%);
        box-shadow:0 1px 2px rgba(30,58,95,.20), inset 0 1px 0 rgba(255,255,255,.10);
        transition:all .18s ease;
    }
    .cp-btn-primary:hover{
        background:linear-gradient(180deg, var(--cp-primary) 0%, var(--cp-primary-dark) 100%);
        transform:translateY(-1px);color:#fff;
        box-shadow:0 3px 8px rgba(30,58,95,.25), inset 0 1px 0 rgba(255,255,255,.10);
    }
    .cp-btn-primary i{font-size:12px;}

    /* ---------- Summary KPI cards ---------- */
    .cp-summary{
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:16px;
        padding:24px 28px 8px;
    }
    @media (max-width:1199px){ .cp-summary{grid-template-columns:repeat(2,1fr);} }
    @media (max-width:575px){ .cp-summary{grid-template-columns:1fr;} }

    .cp-kpi{
        position:relative;
        background:var(--cp-white);
        border:1px solid var(--cp-line);
        border-radius:12px;
        padding:18px 20px;
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:14px;
        overflow:hidden;
        box-shadow:var(--cp-shadow-sm);
        transition:all .22s ease;
    }
    .cp-kpi::before{
        content:"";position:absolute;left:0;top:0;right:0;height:3px;
        background:linear-gradient(90deg, var(--cp-primary-light), var(--cp-primary));
    }
    .cp-kpi.kpi-paid::before{
        background:linear-gradient(90deg, #34d399, var(--cp-paid));
    }
    .cp-kpi.kpi-unpaid::before{
        background:linear-gradient(90deg, #fbbf24, var(--cp-partial));
    }
    .cp-kpi.kpi-count::before{
        background:linear-gradient(90deg, #94a3b8, #475569);
    }

    .cp-kpi:hover{
        border-color:#cbd5e1;
        box-shadow:var(--cp-shadow-md);
        transform:translateY(-2px);
    }

    .cp-kpi-body{min-width:0;flex:1;}
    .cp-kpi-label{
        font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
        color:var(--cp-muted);margin:0 0 8px;
    }
    .cp-kpi-value{
        font-family:'SFMono-Regular',Consolas,monospace;
        font-size:20px;font-weight:700;color:var(--cp-ink);line-height:1.1;
        white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
        letter-spacing:-.02em;
    }
    .cp-kpi-sub{
        font-size:11.5px;color:var(--cp-muted);margin-top:8px;line-height:1.35;
    }
    .cp-kpi-icon{
        width:42px;height:42px;flex-shrink:0;
        display:flex;align-items:center;justify-content:center;
        border-radius:10px;
        background:linear-gradient(135deg, var(--cp-line-soft), var(--cp-line));
        color:var(--cp-primary);
        font-size:16px;
        box-shadow:inset 0 1px 0 rgba(255,255,255,.6);
    }
    .cp-kpi.kpi-paid .cp-kpi-icon{
        background:linear-gradient(135deg, #d1fae5, var(--cp-paid-border));
        color:var(--cp-paid-dark);
    }
    .cp-kpi.kpi-unpaid .cp-kpi-icon{
        background:linear-gradient(135deg, #fef3c7, var(--cp-partial-border));
        color:var(--cp-partial-dark);
    }
    .cp-kpi.kpi-count .cp-kpi-icon{
        background:linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color:var(--cp-ink-soft);
    }

    .cp-kpi.kpi-paid .cp-kpi-value{color:var(--cp-paid-dark);}
    .cp-kpi.kpi-unpaid .cp-kpi-value{color:var(--cp-partial-dark);}

    .cp-kpi-progress{
        position:relative;height:5px;background:var(--cp-line);
        border-radius:999px;margin-top:10px;overflow:hidden;
    }
    .cp-kpi-progress > span{
        display:block;height:100%;
        background:linear-gradient(90deg, #34d399, var(--cp-paid));
        border-radius:999px;
        transition:width .4s ease;
    }

    /* ---------- Filter bar ---------- */
    .cp-filters{
        display:flex;align-items:center;gap:8px;flex-wrap:wrap;
        padding:20px 28px 16px;
    }
    .cp-filters-label{
        font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
        color:var(--cp-muted);margin-right:8px;
    }
    .cp-filter{
        display:inline-flex;align-items:center;gap:8px;
        padding:8px 14px;font-size:12.5px;font-weight:600;
        color:var(--cp-ink-soft);background:var(--cp-white);
        border:1px solid var(--cp-line);border-radius:8px;
        cursor:pointer;transition:all .18s ease;
        box-shadow:var(--cp-shadow-sm);
    }
    .cp-filter:hover{
        background:var(--cp-soft);
        border-color:#cbd5e1;
        color:var(--cp-ink);
        transform:translateY(-1px);
        box-shadow:var(--cp-shadow);
    }
    .cp-filter:focus{outline:none;box-shadow:0 0 0 3px rgba(30,58,95,.10);}
    .cp-filter.active,
    .cp-filter.active:hover{
        transform:none;
    }

    /* Per-filter active colors */
    .cp-filter[data-filter="all"].active{
        background:linear-gradient(180deg, var(--cp-primary-light), var(--cp-primary));
        color:#fff;border-color:var(--cp-primary);
        box-shadow:0 2px 6px rgba(30,58,95,.25);
    }
    .cp-filter[data-filter="paid"].active{
        background:linear-gradient(180deg, #10b981, var(--cp-paid));
        color:#fff;border-color:var(--cp-paid);
        box-shadow:0 2px 6px rgba(5,150,105,.28);
    }
    .cp-filter[data-filter="partial"].active{
        background:linear-gradient(180deg, #f59e0b, var(--cp-partial));
        color:#fff;border-color:var(--cp-partial);
        box-shadow:0 2px 6px rgba(217,119,6,.28);
    }
    .cp-filter[data-filter="unpaid"].active{
        background:linear-gradient(180deg, #ef4444, var(--cp-unpaid));
        color:#fff;border-color:var(--cp-unpaid);
        box-shadow:0 2px 6px rgba(220,38,38,.28);
    }

    .cp-filter .cp-count{
        display:inline-flex;align-items:center;justify-content:center;
        min-width:22px;height:20px;padding:0 7px;
        font-size:11px;font-weight:700;border-radius:999px;
        background:var(--cp-line-soft);color:var(--cp-ink-soft);
        font-variant-numeric:tabular-nums;
        transition:all .18s ease;
    }
    .cp-filter:hover .cp-count{
        background:var(--cp-line);
    }
    .cp-filter.active .cp-count{
        background:rgba(255,255,255,.28);color:#fff;
    }

    .cp-dot{width:8px;height:8px;border-radius:50%;display:inline-block;flex-shrink:0;}
    .cp-dot-paid{background:var(--cp-paid);box-shadow:0 0 0 2px var(--cp-paid-bg);}
    .cp-dot-partial{background:var(--cp-partial);box-shadow:0 0 0 2px var(--cp-partial-bg);}
    .cp-dot-unpaid{background:var(--cp-unpaid);box-shadow:0 0 0 2px var(--cp-unpaid-bg);}

    /* ---------- Table ---------- */
    .cp-body{padding:0 0 12px;}
    .cp-table-outer{position:relative;}
    .cp-table-wrap{width:100%;overflow-x:auto;padding:8px 28px 0;}
    .cp-wrap #datatable{
        width:100%!important;border-collapse:collapse;margin:0;font-size:13px;
        border:1px solid var(--cp-line);border-radius:0;
        background:var(--cp-white);
    }
    .cp-wrap #datatable thead th{
        background:linear-gradient(180deg, var(--cp-primary-light) 0%, var(--cp-primary) 100%);
        color:#fff;
        font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;
        padding:15px 14px;text-align:left;border:none;white-space:nowrap;
        vertical-align:middle;
        position:relative;
    }
    .cp-wrap #datatable thead th + th::before{
        content:"";position:absolute;left:0;top:28%;bottom:28%;
        width:1px;background:rgba(255,255,255,.14);
    }
    .cp-wrap #datatable thead th:last-child{text-align:center;}

    .cp-wrap #datatable tbody td{
        padding:13px 14px;
        border-bottom:1px solid var(--cp-line-soft);
        vertical-align:middle;color:var(--cp-ink-soft);
        background:var(--cp-white);
        transition:background .15s ease;
    }
    .cp-wrap #datatable tbody tr:nth-child(even) td{
        background:#fbfcfd;
    }
    .cp-wrap #datatable tbody tr:hover td{
        background:#f1f5f9;
    }
    .cp-wrap #datatable tbody tr:last-child td{border-bottom:none;}

    /* Row status accent (left border) */
    .cp-wrap #datatable tbody tr[data-status="paid"] td:first-child{
        box-shadow:inset 3px 0 0 var(--cp-paid);
    }
    .cp-wrap #datatable tbody tr[data-status="partial"] td:first-child{
        box-shadow:inset 3px 0 0 var(--cp-partial);
    }
    .cp-wrap #datatable tbody tr[data-status="unpaid"] td:first-child{
        box-shadow:inset 3px 0 0 var(--cp-unpaid);
    }

    .cp-wrap #datatable tbody td.num{
        font-family:'SFMono-Regular', Consolas, monospace;
        font-size:12.5px;white-space:nowrap;text-align:right;
        font-variant-numeric:tabular-nums;
    }
    .cp-wrap #datatable tbody td.center{text-align:center;}

    /* ---------- Loading overlay ---------- */
    .cp-table-loading{
        position:absolute;inset:0;
        display:none;align-items:center;justify-content:center;
        background:rgba(255,255,255,.78);
        backdrop-filter:blur(2px);
        z-index:5;min-height:200px;
        border-radius:0;
    }
    .cp-table-loading.show{display:flex;}
    .cp-spinner{
        width:38px;height:38px;
        border:3px solid var(--cp-line);
        border-top-color:var(--cp-primary);
        border-radius:50%;
        animation:cp-spin .8s linear infinite;
    }
    @keyframes cp-spin{to{transform:rotate(360deg);}}

    /* ---------- Download modal ---------- */
    #download_modal .modal-content{
        border:none;border-radius:14px;overflow:hidden;
        box-shadow:0 24px 60px rgba(15,23,42,.20);
        background:var(--cp-white);
    }
    #download_modal .modal-body{padding:34px 24px;}
    #download_modal .cp-spinner{
        width:44px;height:44px;
        border:3px solid var(--cp-line);
        border-top-color:var(--cp-primary);
        border-radius:50%;
        animation:cp-spin .8s linear infinite;
        margin:0 auto 18px;
    }

    /* ---------- Inline validation ---------- */
    .cp-error-text{
        display:block;margin-top:6px;font-size:11.5px;font-weight:600;color:var(--cp-unpaid);
    }
    .cp-modal .form-control.cp-invalid{
        border-color:var(--cp-unpaid);
        box-shadow:0 0 0 3px rgba(220,38,38,.10);
    }

    /* Amount emphasis */
    .cp-amount{
        font-weight:600;color:var(--cp-ink);
        font-family:'SFMono-Regular', Consolas, monospace;
        font-variant-numeric:tabular-nums;
    }
    .cp-unpaid{color:var(--cp-unpaid-dark);font-weight:700;}
    .cp-zero{color:#94a3b8;font-weight:500;}
    .cp-balance{color:var(--cp-primary);font-weight:700;}
    .cp-overpaid-txt{color:var(--cp-overpaid);font-weight:700;}

    /* Status pills */
    .cp-pill{
        display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:999px;
        font-size:11.5px;font-weight:600;line-height:1.4;
    }
    .cp-pill-paid{background:var(--cp-paid-bg);color:var(--cp-paid-dark);}
    .cp-pill-unpaid{background:var(--cp-unpaid-bg);color:var(--cp-unpaid-dark);}
    .cp-pill i{font-size:10px;}

    /* ---------- Action buttons ---------- */
    .cp-actions{display:flex;gap:6px;justify-content:center;flex-wrap:nowrap;}
    .cp-act{
        display:inline-flex;align-items:center;gap:5px;padding:6px 11px;border-radius:6px;
        font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;
        border:1px solid transparent;
        transition:all .15s ease;white-space:nowrap;
        box-shadow:var(--cp-shadow-sm);
    }
    .cp-act i{font-size:11px;}
    .cp-act:hover{text-decoration:none;transform:translateY(-1px);box-shadow:var(--cp-shadow);}

    .cp-act-pay{
        background:linear-gradient(180deg, var(--cp-primary-light), var(--cp-primary));
        color:#fff;border-color:var(--cp-primary);
    }
    .cp-act-pay:hover{
        background:linear-gradient(180deg, var(--cp-primary), var(--cp-primary-dark));
        color:#fff;
    }

    .cp-act-view{
        background:var(--cp-white);color:var(--cp-ink-soft);border-color:var(--cp-line);
    }
    .cp-act-view:hover{
        background:var(--cp-soft);color:var(--cp-ink);border-color:#cbd5e1;
    }

    .cp-act-paid{
        background:var(--cp-paid-bg);color:var(--cp-paid-dark);
        border-color:var(--cp-paid-border);cursor:default;
    }
    .cp-act-paid:hover{transform:none;box-shadow:var(--cp-shadow-sm);}

    /* ---------- Modals ---------- */
    .cp-wrap .modal-content, .cp-modal .modal-content{
        border:none;border-radius:14px;overflow:hidden;
        box-shadow:0 24px 60px rgba(15,23,42,.20);
    }
    .cp-wrap .modal-header, .cp-modal .modal-header{
        background:linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
        border-bottom:1px solid var(--cp-line);
        padding:20px 24px;
    }
    .cp-wrap .modal-header h4, .cp-modal .modal-header h4{
        margin:0;font-size:16px;font-weight:700;color:var(--cp-ink);
    }
    .cp-wrap .modal-header .close, .cp-modal .modal-header .close{
        font-size:24px;color:var(--cp-muted);opacity:.7;margin-top:-2px;
    }
    .cp-wrap .modal-header .close:hover, .cp-modal .modal-header .close:hover{opacity:1;color:var(--cp-ink);}

    .cp-wrap .modal-body, .cp-modal .modal-body{padding:24px;background:var(--cp-white);}

    .cp-wrap .modal-footer, .cp-modal .modal-footer{
        background:var(--cp-soft);border-top:1px solid var(--cp-line);
        padding:14px 24px;
    }

    .cp-wrap .form-group, .cp-modal .form-group{margin-bottom:18px;}
    .cp-wrap .control-label, .cp-modal .control-label{
        display:block;margin-bottom:7px;font-size:12px;font-weight:600;
        color:var(--cp-ink-soft);letter-spacing:.02em;
    }
    .cp-wrap .control-label .req, .cp-modal .control-label .req{color:var(--cp-unpaid);font-weight:700;}

    .cp-wrap .form-control, .cp-modal .form-control{
        width:100%;height:42px;padding:0 12px;font-size:14px;color:var(--cp-ink);
        background:var(--cp-white);border:1px solid var(--cp-line);border-radius:8px;
        outline:none;box-shadow:none;transition:all .18s ease;
    }
    .cp-wrap select.form-control, .cp-modal select.form-control{
        height:42px;padding:0 34px 0 12px;
    }
    .cp-wrap .form-control:hover, .cp-modal .form-control:hover{border-color:#cbd5e1;}
    .cp-wrap .form-control:focus, .cp-modal .form-control:focus{
        border-color:var(--cp-primary);box-shadow:0 0 0 3px rgba(30,58,95,.10);
    }
    .cp-wrap .form-control[readonly], .cp-modal .form-control[readonly]{
        background:var(--cp-line-soft);color:var(--cp-muted);cursor:not-allowed;
    }

    .cp-wrap .input-group-addon, .cp-modal .input-group-addon{
        background:linear-gradient(180deg, var(--cp-primary-light), var(--cp-primary))!important;
        color:#fff!important;border:none;
        border-radius:0 8px 8px 0;
    }

    /* Footer buttons */
    .cp-wrap .btn-default, .cp-modal .btn-default{
        background:var(--cp-white);border:1px solid var(--cp-line);color:var(--cp-ink-soft);
        border-radius:8px;padding:9px 18px;font-weight:600;font-size:13px;
    }
    .cp-wrap .btn-default:hover, .cp-modal .btn-default:hover{
        background:var(--cp-line-soft);color:var(--cp-ink);border-color:#cbd5e1;
    }
    .cp-wrap .btn-primary, .cp-modal .btn-primary{
        background:linear-gradient(180deg, var(--cp-primary-light), var(--cp-primary));
        border-color:var(--cp-primary);color:#fff;
        border-radius:8px;padding:9px 20px;font-weight:600;font-size:13px;
        box-shadow:0 1px 2px rgba(30,58,95,.20), inset 0 1px 0 rgba(255,255,255,.10);
    }
    .cp-wrap .btn-primary:hover, .cp-modal .btn-primary:hover{
        background:linear-gradient(180deg, var(--cp-primary), var(--cp-primary-dark));
        border-color:var(--cp-primary-dark);
    }
    .cp-wrap .btn-rounded, .cp-modal .btn-rounded{border-radius:8px;}

    /* Section divider inside modals */
    .cp-section-title{
        font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
        color:var(--cp-muted);margin:0 0 14px;padding-bottom:8px;
        border-bottom:1px solid var(--cp-line);
    }

    /* ---------- Actions group (right of filter bar) ---------- */
    .cp-head-actions{
        display:flex;gap:10px;align-items:center;
        margin-left:auto;
    }

    .cp-btn-secondary{
        display:inline-flex;align-items:center;gap:8px;padding:9px 16px;
        border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;
        color:var(--cp-ink-soft);background:var(--cp-white);
        border:1px solid var(--cp-line);box-shadow:var(--cp-shadow-sm);
        transition:all .18s ease;
    }
    .cp-btn-secondary:hover{
        background:var(--cp-soft);border-color:#cbd5e1;color:var(--cp-ink);
        transform:translateY(-1px);box-shadow:var(--cp-shadow);
    }

    .cp-dropdown{position:relative;display:inline-block;}
    .cp-dropdown-menu{
        position:absolute;right:0;top:calc(100% + 6px);
        min-width:220px;background:#fff;border:1px solid var(--cp-line);
        border-radius:10px;box-shadow:var(--cp-shadow-md);
        padding:6px;display:none;z-index:1050;list-style:none;margin:0;
    }
    .cp-dropdown-menu.open{display:block;}
    .cp-dropdown-menu li a{
        display:flex;align-items:center;gap:10px;
        padding:9px 12px;font-size:13px;font-weight:500;
        color:var(--cp-ink-soft);text-decoration:none;border-radius:6px;
        transition:all .15s ease;
    }
    .cp-dropdown-menu li a:hover{
        background:var(--cp-soft);color:var(--cp-ink);text-decoration:none;
    }
    .cp-dropdown-menu li a i{width:14px;text-align:center;font-size:12px;}
    .cp-dropdown-menu .cp-divider{height:1px;background:var(--cp-line);margin:4px 0;}

    .cp-dot-menu{width:8px;height:8px;border-radius:50%;display:inline-block;flex-shrink:0;}

    /* ---------- Payment Type radios ---------- */
    .cp-radio-group{display:flex;gap:10px;flex-wrap:wrap;}
    .cp-radio{
        display:inline-flex;align-items:center;gap:9px;
        padding:10px 16px;border:1px solid var(--cp-line);border-radius:9px;
        cursor:pointer;font-size:13px;font-weight:600;color:var(--cp-ink-soft);
        background:var(--cp-white);transition:all .18s ease;user-select:none;
        box-shadow:var(--cp-shadow-sm);margin:0;
    }
    .cp-radio:hover{border-color:#cbd5e1;background:var(--cp-soft);color:var(--cp-ink);}
    .cp-radio input[type="radio"]{
        appearance:none;-webkit-appearance:none;
        width:16px;height:16px;flex-shrink:0;margin:0;
        border:2px solid #cbd5e1;border-radius:50%;
        position:relative;cursor:pointer;transition:all .18s ease;
    }
    .cp-radio input[type="radio"]:checked{
        border-color:var(--cp-primary);background:var(--cp-primary);
    }
    .cp-radio input[type="radio"]:checked::after{
        content:"";position:absolute;top:50%;left:50%;
        transform:translate(-50%,-50%);width:5px;height:5px;
        border-radius:50%;background:#fff;
    }
    .cp-radio.active{
        border-color:var(--cp-primary);
        background:#f0f4f9;
        color:var(--cp-ink);
        box-shadow:0 0 0 3px rgba(30,58,95,.08);
    }
    .cp-radio.active input[type="radio"]{
        border-color:var(--cp-primary);background:var(--cp-primary);
    }

    @media (max-width:767px){
        .cp-head{flex-direction:column;align-items:stretch;padding:20px;}
        .cp-head-text h3{font-size:17px;}
        .cp-actions{flex-wrap:wrap;}
        .cp-summary{padding:18px 20px 4px;}
        .cp-filters{padding:16px 20px 12px;}
        .cp-table-wrap{padding:8px 20px 0;}
        .cp-head-actions{margin-left:0;width:100%;justify-content:flex-end;}
    }
</style>

<div class="cp-wrap">

    {{-- ===== Step indicator ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="cp-steps">
                <a href="{{ url('home') }}" class="completed"><i class="fa fa-home"></i> Home</a>
                <span class="cp-sep"><i class="fa fa-angle-right"></i></span>
                <a href="#" class="active">Client Payments</a>
            </div>
        </div>
    </div>

    {{-- ===== Main panel ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-custom panel-border">

                <div class="cp-body">

                    {{-- ===== Summary KPI cards ===== --}}
                    <div class="cp-summary">

                        <div class="cp-kpi">
                            <div class="cp-kpi-body">
                                <p class="cp-kpi-label">Total Invoiced</p>
                                <div class="cp-kpi-value">{{ number_format($totalInvoiced, 2) }}</div>
                                <div class="cp-kpi-sub">{{ $invoiceCount }} {{ $invLabel }}</div>
                            </div>
                            <div class="cp-kpi-icon"><i class="fa fa-file-text-o"></i></div>
                        </div>

                        <div class="cp-kpi kpi-paid">
                            <div class="cp-kpi-body">
                                <p class="cp-kpi-label">Total Paid</p>
                                <div class="cp-kpi-value">{{ number_format($totalPaid, 2) }}</div>
                                <div class="cp-kpi-progress" title="{{ $paidPct }}% collected">
                                    <span style="width: {{ $paidPct }}%;"></span>
                                </div>
                                <div class="cp-kpi-sub">{{ $paidPct }}% collected · {{ $fullyPaid }} settled</div>
                            </div>
                            <div class="cp-kpi-icon"><i class="fa fa-check"></i></div>
                        </div>

                        <div class="cp-kpi kpi-unpaid">
                            <div class="cp-kpi-body">
                                <p class="cp-kpi-label">Total Unpaid</p>
                                <div class="cp-kpi-value">{{ number_format($totalUnpaid, 2) }}</div>
                                <div class="cp-kpi-sub">{{ $withBalance }} {{ $balLabel }} with balance</div>
                            </div>
                            <div class="cp-kpi-icon"><i class="fa fa-exclamation-triangle"></i></div>
                        </div>

                        <div class="cp-kpi kpi-count">
                            <div class="cp-kpi-body">
                                <p class="cp-kpi-label">Invoices</p>
                                <div class="cp-kpi-value">{{ $invoiceCount }}</div>
                                <div class="cp-kpi-sub">{{ $fullyPaid }} paid · {{ $withBalance }} pending</div>
                            </div>
                            <div class="cp-kpi-icon"><i class="fa fa-list-alt"></i></div>
                        </div>

                    </div>

                    {{-- ===== Filter bar + Action buttons (right) ===== --}}
                    <div class="cp-filters">
                        <span class="cp-filters-label">Filter:</span>

                        <button type="button" class="cp-filter active" data-filter="all">
                            All <span class="cp-count">{{ $invoiceCount }}</span>
                        </button>

                        <button type="button" class="cp-filter" data-filter="paid">
                            <span class="cp-dot cp-dot-paid"></span>
                            Paid <span class="cp-count">{{ $fullyPaid }}</span>
                        </button>

                        <button type="button" class="cp-filter" data-filter="partial">
                            <span class="cp-dot cp-dot-partial"></span>
                            Partial <span class="cp-count">{{ $partialCount }}</span>
                        </button>

                        <button type="button" class="cp-filter" data-filter="unpaid">
                            <span class="cp-dot cp-dot-unpaid"></span>
                            Unpaid <span class="cp-count">{{ $unpaidCount }}</span>
                        </button>

                        {{-- Actions moved to the right --}}
                        <div class="cp-head-actions">

                            {{-- Export PDF dropdown --}}
                            <div class="cp-dropdown">
                                <button type="button" class="cp-btn-secondary cp-dropdown-toggle">
                                    <i class="fa fa-file-pdf-o"></i> Export PDF
                                    <i class="fa fa-angle-down" style="font-size:10px;margin-left:4px;"></i>
                                </button>
                                <ul class="cp-dropdown-menu">
                                    <li>
                                        <a href="{{ route('client-payments.pdf', ['status' => 'all']) }}"
                                           data-export="all">
                                            <i class="fa fa-file-pdf-o" style="color:var(--cp-primary);"></i>
                                            All Invoices
                                        </a>
                                    </li>
                                    <li class="cp-divider"></li>
                                    <li>
                                        <a href="{{ route('client-payments.pdf', ['status' => 'paid']) }}"
                                           data-export="paid">
                                            <i class="fa fa-file-pdf-o" style="color:var(--cp-paid);"></i>
                                            Paid Only
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('client-payments.pdf', ['status' => 'partial']) }}"
                                           data-export="partial">
                                            <i class="fa fa-file-pdf-o" style="color:var(--cp-partial);"></i>
                                            Partial Only
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('client-payments.pdf', ['status' => 'unpaid']) }}"
                                           data-export="unpaid">
                                            <i class="fa fa-file-pdf-o" style="color:var(--cp-unpaid);"></i>
                                            Unpaid Only
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            {{-- Add Debit button --}}
                            <button type="button"
                                    class="cp-btn-primary"
                                    data-toggle="modal"
                                    data-target=".bs-example-modal-lg">
                                <i class="fa fa-plus"></i> Add Debit
                            </button>

                        </div>
                    </div>

                    {{-- ===== Table ===== --}}
                    <div class="cp-table-outer">
                        <div class="cp-table-loading" id="cpTableLoading">
                            <div class="cp-spinner"></div>
                        </div>

                        <div class="cp-table-wrap">
                            <table id="datatable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:48px;">#</th>
                                        <th>Client Name</th>
                                        <th>Vehicle No</th>
                                        <th>Invoice No</th>
                                        <th class="text-right">Invoice Amount</th>
                                        <th>Created Date</th>
                                        <th class="text-right">Paid Amount</th>
                                        <th class="text-right">Unpaid Amount</th>
                                        <th class="text-right">Balance</th>
                                        <th style="width:180px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sales as $key => $product)
                                        @php
                                            $check = (float) $product->bill_amount - (float) $product->paid_amount;
                                            if ((float) $product->paid_amount >= (float) $product->bill_amount) {
                                                $rowStatus = 'paid';
                                            } elseif ((float) $product->paid_amount > 0) {
                                                $rowStatus = 'partial';
                                            } else {
                                                $rowStatus = 'unpaid';
                                            }
                                        @endphp
                                        <tr data-status="{{ $rowStatus }}">
                                            <td class="center">{{ $key + 1 }}</td>
                                            <td><strong style="color:var(--cp-ink);">{{ strtoupper($product->client_name) }}</strong></td>
                                            <td>{{ strtoupper($product->vehicle_reg) }} | {{ strtoupper($product->model) }} </td>
                                            <td>
                                                <span style="font-family:'SFMono-Regular',Consolas,monospace;font-size:12.5px;color:#475569;">
                                                    {{ $product->invoice_number }}
                                                </span>
                                            </td>
                                            <td class="num">
                                                <span class="cp-amount">{{ number_format($product->bill_amount, 2) }}</span>
                                            </td>
                                            <td>{{ strtoupper($product->created_date) }}</td>
                                            <td class="num">
                                                <span class="cp-amount" style="color:var(--cp-paid-dark);">{{ number_format($product->paid_amount, 2) }}</span>
                                            </td>
                                            <td class="num">
                                                @if($product->bill_amount > $product->paid_amount)
                                                    <span class="cp-unpaid">{{ number_format($product->bill_amount - $product->paid_amount, 2) }}</span>
                                                @else
                                                    <span class="cp-zero">0.00</span>
                                                @endif
                                            </td>
                                            <td class="num">
                                                @if($product->bill_amount - $product->paid_amount > 0)
                                                    <span class="cp-balance">{{ number_format($product->bill_amount - $product->paid_amount, 2) }}</span>
                                                @elseif($product->bill_amount - $product->paid_amount < 0)
                                                    <span class="cp-overpaid-txt">{{ number_format($product->bill_amount - $product->paid_amount, 2) }}</span>
                                                @else
                                                    <span class="cp-zero">0.00</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="cp-actions">
                                                    @if ($check == 0)
                                                        <button type="button"
                                                                value="{{ $product->invoice_number }}"
                                                                class="cp-act cp-act-paid">
                                                            <i class="fa fa-check"></i> Paid
                                                        </button>
                                                    @else
                                                        <button type="button"
                                                                value="{{ $product->invoice_number }}"
                                                                class="cp-act cp-act-pay addPayment">
                                                            <i class="fa fa-plus"></i> Pay
                                                        </button>
                                                    @endif

                                                    @if(!$product->invoice_number)
                                                        <a class="cp-act cp-act-view viewModal">
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                    @else
                                                        <a href="{{ url('clients/payments/view-payments/'.$product->invoice_number) }}"
                                                           class="cp-act cp-act-view">
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== Payment Modal ===================== --}}
<div class="modal fade bs-example-modal-md cp-modal" tabindex="-1" role="dialog" aria-hidden="true" id="payment_modal">
    <div class="modal-dialog modal-md">
        <form id="formUpdate" method="post" action="{{ url('clients/add-payments') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Client Payment</h4>
                </div>

                <div class="modal-body">

                    <div class="cp-section-title">Invoice details</div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Client Name</label>
                                <input type="text" name="client_name" readonly id="client_name" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Invoice No #</label>
                                <input type="hidden" name="client_id" readonly id="client_id">
                                <input type="text" name="invoice_number" readonly id="invoice_no" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Invoiced Amount</label>
                                <input type="text" name="bill_amount" readonly id="bill_amount" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Balance Due</label>
                                <input type="text" name="balance_due" readonly id="balance_due" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="cp-section-title" style="margin-top:8px;">Payment details</div>

                    {{-- Payment type selector --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Payment Type <span class="req">*</span></label>
                                <div class="cp-radio-group">
                                    <label class="cp-radio active">
                                        <input type="radio" name="payment_type" value="full" checked>
                                        <span>Full Payment</span>
                                    </label>
                                    <label class="cp-radio">
                                        <input type="radio" name="payment_type" value="partial">
                                        <span>Partial Payment</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" id="amount_wrap" style="display:none;">
                            <div class="form-group">
                                <label class="control-label">Amount <span class="req">*</span></label>
                                <input type="text"
                                       name="paid_amount"
                                       id="paid_amount"
                                       class="form-control"
                                       placeholder="Amount to pay"
                                       autocomplete="off"
                                       inputmode="decimal"
                                       data-max="">
                                <span class="cp-error-text" id="paid_amount_error" style="display:none;"></span>
                            </div>
                        </div>
                        <div class="col-md-12" id="date_wrap">
                            <div class="form-group">
                                <label class="control-label">Date <span class="req">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control created_date" required autocomplete="off"
                                           name="created_date" placeholder="Payment date"
                                           id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                                    <span class="input-group-addon"><i class="ti-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnPay">Pay</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===================== Add Debit Modal ===================== --}}
<div class="modal fade bs-example-modal-lg cp-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ url('clients/add-clients-debit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Add Client Debit</h4>
                </div>

                <div class="modal-body">

                    <div class="cp-section-title">Client &amp; Vehicle</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Client Name <span class="req">*</span></label>
                                <select name="client_id" class="form-control select2" id="client_id" required>
                                    <option>Select Client</option>
                                    @foreach($clients as $s)
                                        <option value="{{ $s->id }}">{{ $s->client_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Vehicle Reg <span class="req">*</span></label>
                                <input type="text" name="vehicle_reg" id="vehicle_reg" class="form-control" placeholder="Vehicle Reg Number" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Make &amp; Model <span class="req">*</span></label>
                                <input type="text" name="make" id="make" class="form-control" placeholder="e.g. TOYOTA PRADO" required>
                            </div>
                        </div>
                    </div>

                    <div class="cp-section-title" style="margin-top:8px;">Invoice details</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Invoice No. <span class="req">*</span></label>
                                <input type="text" name="invoice_no" id="invoice" class="form-control" placeholder="Invoice No." required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Invoice Amount <span class="req">*</span></label>
                                <input type="text" name="invoice_amount" id="invoice_amount" class="form-control" placeholder="Invoice amount" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Invoice Date <span class="req">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control created_date" required autocomplete="off"
                                           name="created_date" placeholder="Invoice date"
                                           id="datepicker1" data-date-format="yyyy-mm-dd">
                                    <span class="input-group-addon"><i class="ti-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===================== Download Progress Modal ===================== --}}
<div class="modal fade cp-modal" id="download_modal" tabindex="-1" role="dialog" aria-hidden="true"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="max-width:360px;margin:120px auto 0;">
        <div class="modal-content">
            <div class="modal-body" style="text-align:center;">
                <div class="cp-spinner"></div>
                <h4 style="margin:0 0 8px;font-size:15px;font-weight:700;color:var(--cp-ink);">
                    Downloading…
                </h4>
                <p style="margin:0;font-size:12.5px;color:var(--cp-muted);line-height:1.5;">
                    Please wait while your PDF is being prepared.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery('#datepicker1').datepicker({
        autoclose: true,
        todayHighlight: true
    });

    /* ============================================================
     * FILTER + LOADER + PAGINATION-AWARE RENUMBERING
     * ============================================================ */
    var currentFilter = 'all';
    var filterTimer   = null;

    function hasDataTable() {
        return !!(window.jQuery && $.fn.DataTable && $.fn.DataTable.isDataTable('#datatable'));
    }

    if ($.fn.dataTable && $.fn.dataTable.ext && $.fn.dataTable.ext.search) {
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (!settings || !settings.nTable || settings.nTable.id !== 'datatable') {
                return true;
            }
            if (currentFilter === 'all') {
                return true;
            }
            var row = settings.aoData[dataIndex];
            if (!row || !row.nTr) {
                return true;
            }
            return $(row.nTr).data('status') === currentFilter;
        });
    }

    function showTableSpinner() { $('#cpTableLoading').addClass('show'); }
    function hideTableSpinner() { $('#cpTableLoading').removeClass('show'); }

    function renumberVisibleRows() {
        if (hasDataTable()) {
            var dt      = $('#datatable').DataTable();
            var start   = dt.page.info().start;
            var counter = start + 1;

            dt.rows({ page: 'current' }).nodes().each(function (row) {
                $(row).find('td').first().text(counter++);
            });
        } else {
            var i = 1;
            $('#datatable tbody tr').each(function () {
                if ($(this).is(':visible')) {
                    $(this).find('td').first().text(i++);
                }
            });
        }
    }

    if (hasDataTable()) {
        $('#datatable').on('draw.dt', function () {
            renumberVisibleRows();
        });
        renumberVisibleRows();
    }

    function applyTableFilter() {
        if (hasDataTable()) {
            $('#datatable').DataTable().draw();
        } else {
            $('#datatable tbody tr').each(function () {
                var status = $(this).data('status');
                var show   = (currentFilter === 'all') || (status === currentFilter);
                $(this).toggle(show);
            });
            renumberVisibleRows();
        }
    }

    $(document).on('click', '.cp-filter', function () {
        var $btn   = $(this);
        var filter = $btn.data('filter');

        if (filter === currentFilter) { return; }

        $('.cp-filter').removeClass('active');
        $btn.addClass('active');

        if (filterTimer) { clearTimeout(filterTimer); }
        showTableSpinner();

        filterTimer = setTimeout(function () {
            currentFilter = filter;
            applyTableFilter();
            hideTableSpinner();
            filterTimer = null;
        }, 250);
    });

    /* ============================================================
     * EXPORT DROPDOWN open/close
     * ============================================================ */
    $(document).on('click', '.cp-dropdown-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $menu = $(this).siblings('.cp-dropdown-menu');
        $('.cp-dropdown-menu').not($menu).removeClass('open');
        $menu.toggleClass('open');
    });
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.cp-dropdown').length) {
            $('.cp-dropdown-menu').removeClass('open');
        }
    });

    /* ============================================================
     * EXPORT PDF — fetch blob, hide modal ONLY on full download
     * ============================================================ */
    function extractFilename(disposition, fallback) {
        if (!disposition) { return fallback; }
        var match = disposition.match(/filename\*?=(?:UTF-8'')?["']?([^"';\r\n]+)/i);
        if (match && match[1]) {
            try { return decodeURIComponent(match[1]); }
            catch (e) { return match[1]; }
        }
        return fallback;
    }

    $(document).on('click', '.cp-dropdown-menu a[data-export]', function (e) {
        e.preventDefault();

        var url = $(this).attr('href');
        if (!url) { return; }

        $('.cp-dropdown-menu').removeClass('open');
        $('#download_modal').modal('show');

        fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/pdf'
            }
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Server returned ' + response.status);
            }
            var filename = extractFilename(
                response.headers.get('Content-Disposition'),
                'client-payments.pdf'
            );
            return response.blob().then(function (blob) {
                return { blob: blob, filename: filename };
            });
        })
        .then(function (data) {
            var blobUrl = window.URL.createObjectURL(data.blob);
            var $a = $('<a></a>')
                .attr('href', blobUrl)
                .attr('download', data.filename)
                .css('display', 'none')
                .appendTo(document.body);

            $a[0].click();

            setTimeout(function () {
                $a.remove();
                window.URL.revokeObjectURL(blobUrl);
            }, 1500);

            $('#download_modal').modal('hide');
        })
        .catch(function (err) {
            console.error('PDF download failed:', err);
            $('#download_modal').modal('hide');
            alert('Failed to download the PDF. Please try again.');
        });
    });

    /* ============================================================
     * MONEY HELPER — float comparison with epsilon tolerance
     * (0.005 = half a cent; tolerates IEEE-754 residue such as
     *  1234.5599999999999 vs 1234.56 without masking real overpayments)
     * ============================================================ */
    var MONEY_EPSILON = 0.005;

    /* ============================================================
     * PAYMENT MODE (Full / Partial) — UI helper
     * ============================================================ */
    function setPaymentMode(mode) {
        var balanceDue  = parseFloat($('#balance_due').val()) || 0;
        var $amountWrap = $('#amount_wrap');
        var $dateWrap   = $('#date_wrap');
        var $input      = $('#paid_amount');

        // Sync radio visual state
        $('.cp-radio').removeClass('active');
        $('input[name="payment_type"][value="' + mode + '"]')
            .prop('checked', true)
            .closest('.cp-radio')
            .addClass('active');

        if (mode === 'full') {
            // Hide amount input; auto-fill with balance due (still submitted)
            $amountWrap.hide();
            $dateWrap.removeClass('col-md-6').addClass('col-md-12');

            // Keep data-max and value exactly in sync with displayed balance
            $input.attr('data-max', balanceDue.toFixed(2))
                  .val(balanceDue.toFixed(2))
                  .removeClass('cp-invalid');

            $('#paid_amount_error').hide().text('');
        } else {
            // Show amount input for manual entry
            $amountWrap.show();
            $dateWrap.removeClass('col-md-12').addClass('col-md-6');
            $input.val('')
                  .removeClass('cp-invalid');
            $('#paid_amount_error').hide().text('');
            setTimeout(function () { $input.trigger('focus'); }, 120);
        }
    }

    // Radio change
    $(document).on('change', 'input[name="payment_type"]', function () {
        setPaymentMode($(this).val());
    });

    /* ============================================================
     * PAYMENT MODAL — load invoice, balance and prepare validation
     * ============================================================ */
    $('.addPayment').click(function() {
        var invoice_no = $(this).val();
        $.ajax({
            url: `../clients/payments-details/${invoice_no}`,
            type: "GET",
            success: function (response) {
                var row        = response[0];
                var billAmount = parseFloat(row.bill_amount) || 0;
                var paidAmount = parseFloat(row.paid_amount) || 0;
                var balanceDue = Math.max(0, billAmount - paidAmount);

                // Guard against tiny float residue — round to 2dp for display/storage
                balanceDue = Math.round(balanceDue * 100) / 100;

                $('#payment_modal').modal('show');
                $("#client_name").val(row.client_name);
                $("#invoice_no").val(row.invoice_number);
                $("#bill_amount").val(billAmount.toFixed(2));
                $("#balance_due").val(balanceDue.toFixed(2));
                $("#client_id").val(row.client_id);

                $("#paid_amount")
                    .val('')
                    .removeClass('cp-invalid')
                    .attr('data-max', balanceDue.toFixed(2));

                $("#paid_amount_error").hide().text('');
                $("#btnPay").prop('disabled', false).text('Pay');

                // Default: Full payment
                setPaymentMode('full');
            }
        });
    });

    $(document).on('input', '#paid_amount', function () {
        $(this).removeClass('cp-invalid');
        $('#paid_amount_error').hide().text('');
    });

    /* ============================================================
     * PAYMENT FORM — client-side validation + AJAX submit
     * (Float comparison with epsilon tolerance so decimals like
     *  1234.56 do NOT falsely trigger "greater than balance due")
     * ============================================================ */
    $(document).on('submit', '#formUpdate', function(e) {
        e.preventDefault();

        var $input = $('#paid_amount');
        var $error = $('#paid_amount_error');
        var $btn   = $('#btnPay');
        var raw    = ($input.val() || '').trim();

        // ---- Read max FRESH from data-max, correctly handle 0 ----
        var maxRaw = parseFloat($input.attr('data-max'));
        var max    = isNaN(maxRaw) ? null : maxRaw;   // null = no limit

        $input.removeClass('cp-invalid');
        $error.hide().text('');

        if (raw === '') {
            $input.addClass('cp-invalid');
            $error.text('Please enter a payment amount.').show();
            return false;
        }

        if (!/^\d+(\.\d{1,2})?$/.test(raw)) {
            $input.addClass('cp-invalid');
            $error.text('Please enter a valid number (e.g. 1500 or 1500.50).').show();
            return false;
        }

        var amount = parseFloat(raw);
        if (amount <= 0) {
            $input.addClass('cp-invalid');
            $error.text('Amount must be greater than zero.').show();
            return false;
        }

        // ---- Float comparison with epsilon tolerance (fixes decimal false positives) ----
        if (max !== null && (amount - max) > MONEY_EPSILON) {
            $input.addClass('cp-invalid');
            $error.text('Amount cannot exceed the balance due of ' + max.toFixed(2) + '.').show();
            return false;
        }

        $btn.prop('disabled', true).text('Processing...');

        $.ajax({
            url:  $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function() {
                $('#payment_modal').modal('hide');
                window.location.reload();
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Pay');
                var errors = xhr.responseJSON && xhr.responseJSON.errors;
                if (errors && errors.paid_amount) {
                    $input.addClass('cp-invalid');
                    $error.text(errors.paid_amount[0]).show();
                } else {
                    $error.text('An error occurred. Please try again.').show();
                }
            }
        });

        return false;
    });

    $('.viewModal').click(function() {
        alert('Bill number not Created');
    });
  });
</script>

@endsection
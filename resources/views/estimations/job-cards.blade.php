@extends('layouts.app_header')

@section('content')

<style>
    /* ============================================================
       JOB CARDS — matches Estimations History design language
       ============================================================ */
    :root {
        --d1: 0 1px 2px rgba(15,23,42,.04);
        --d2: 0 4px 14px rgba(15,23,42,.06), 0 1px 3px rgba(15,23,42,.04);
        --d3: 0 18px 40px -12px rgba(15,23,42,.14), 0 6px 14px -6px rgba(15,23,42,.06);
        --d-glow: 0 0 0 4px rgba(30,58,95,.08);
        --d-inset: inset 0 1px 0 rgba(255,255,255,.65);

        --n-950: #0b1220; --n-900: #0f172a; --n-800: #1e293b;
        --n-700: #334155; --n-600: #475569; --n-500: #64748b;
        --n-400: #94a3b8; --n-300: #cbd5e1; --n-200: #e2e8f0;
        --n-100: #f1f5f9; --n-50:  #f8fafc; --white: #ffffff;

        --brand-900: #142a45; --brand-800: #1e3a5f;
        --brand-700: #2d5282; --brand-600: #3b6ba5;

        --emerald: #059669; --emerald-dark: #047857;
        --emerald-soft: #ecfdf5; --emerald-line: #a7f3d0;

        --amber: #d97706; --amber-dark: #b45309;
        --amber-soft: #fffbeb; --amber-line: #fde68a;

        --rose: #e11d48; --rose-dark: #be123c;
        --rose-soft: #fff1f2; --rose-line: #fecdd3;

        --cyan: #0891b2; --cyan-dark: #0e7490;
        --cyan-soft: #ecfeff; --cyan-line: #a5f3fc;

        --violet: #7c3aed; --violet-dark: #6d28d9;
        --violet-soft: #f5f3ff; --violet-line: #ddd6fe;

        --ease-out: cubic-bezier(.22,.61,.36,1);
    }

    .est * { box-sizing: border-box; }

    .est-panel {
        background:
            radial-gradient(1200px 420px at 105% -20%, rgba(45,82,130,.08), transparent 55%),
            radial-gradient(900px 340px at -10% 115%, rgba(5,150,105,.06), transparent 55%),
            linear-gradient(180deg, #ffffff 0%, #fbfcfd 100%);
        border: 1px solid var(--n-200);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: var(--d3);
        position: relative;
    }

    .est-steps {
        display: flex; justify-content: flex-end; align-items: center;
        gap: 8px; margin-bottom: 20px; flex-wrap: wrap;
    }
    .est-steps a {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 12px; font-weight: 600; letter-spacing: .02em;
        padding: 8px 15px; border-radius: 10px;
        background: var(--white); color: var(--n-500);
        text-decoration: none; border: 1px solid var(--n-200);
        box-shadow: var(--d1); transition: all .22s var(--ease-out);
    }
    .est-steps a:hover {
        background: var(--n-50); color: var(--n-900);
        text-decoration: none; transform: translateY(-1px);
        box-shadow: var(--d2);
    }
    .est-steps a.active {
        background: linear-gradient(180deg, var(--brand-700), var(--brand-800));
        color: #fff; border-color: var(--brand-800);
        box-shadow: 0 4px 14px -2px rgba(30,58,95,.35), var(--d-inset);
    }
    .est-steps .est-sep { color: var(--n-300); font-size: 10px; }

    .est-head {
        display: flex; justify-content: space-between; align-items: flex-start;
        gap: 24px; padding: 28px 32px 24px;
        border-bottom: 1px solid var(--n-200); position: relative;
    }
    .est-head::after {
        content: ""; position: absolute; left: 32px; right: 32px;
        bottom: -1px; height: 1px;
        background: linear-gradient(90deg, transparent, var(--n-200), transparent);
    }
    .est-head-text h3 {
        margin: 0 0 8px; font-size: 22px; font-weight: 700;
        color: var(--n-900); letter-spacing: -.4px;
        display: flex; align-items: center; gap: 12px;
    }
    .est-head-text h3 .est-head-icon {
        width: 40px; height: 40px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 11px;
        background: linear-gradient(135deg, var(--brand-700), var(--brand-800));
        color: #fff; font-size: 15px;
        box-shadow: 0 8px 20px -6px rgba(30,58,95,.5), var(--d-inset);
    }
    .est-head-text p {
        margin: 0; font-size: 13px; color: var(--n-500);
        line-height: 1.55; max-width: 560px;
    }

    /* ---- KPI strip ---- */
    .est-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px; padding: 26px 32px 10px;
    }
    @media (max-width: 900px) { .est-summary { grid-template-columns: 1fr; } }

    .est-kpi {
        position: relative; border-radius: 16px;
        padding: 20px 22px 18px;
        background:
            radial-gradient(300px 120px at 100% 0%, rgba(45,82,130,.06), transparent 60%),
            var(--white);
        border: 1px solid var(--n-200); box-shadow: var(--d2);
        display: flex; justify-content: space-between; align-items: flex-start;
        gap: 16px; overflow: hidden;
        transition: transform .25s var(--ease-out), box-shadow .25s var(--ease-out);
    }
    .est-kpi::before {
        content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--brand-600), var(--brand-800), var(--brand-600));
        background-size: 200% 100%;
        animation: est-kpi-shimmer 8s ease-in-out infinite;
    }
    .est-kpi.kpi-completed::before {
        background: linear-gradient(90deg, #34d399, var(--emerald), #34d399);
        background-size: 200% 100%;
    }
    .est-kpi.kpi-ongoing::before {
        background: linear-gradient(90deg, #fbbf24, var(--amber), #fbbf24);
        background-size: 200% 100%;
    }
    @keyframes est-kpi-shimmer {
        0%, 100% { background-position: 0% 50%; }
        50%      { background-position: 100% 50%; }
    }
    .est-kpi:hover { transform: translateY(-3px); box-shadow: var(--d3); }

    .est-kpi-label {
        font-size: 11px; font-weight: 700; letter-spacing: .1em;
        text-transform: uppercase; color: var(--n-500); margin: 0 0 10px;
    }
    .est-kpi-value {
        font-family: 'SFMono-Regular', Consolas, monospace;
        font-size: 24px; font-weight: 700; color: var(--n-900);
        line-height: 1.05; letter-spacing: -.03em;
        font-variant-numeric: tabular-nums;
    }
    .est-kpi.kpi-completed .est-kpi-value { color: var(--emerald-dark); }
    .est-kpi.kpi-ongoing   .est-kpi-value { color: var(--amber-dark); }

    .est-kpi-sub {
        margin-top: 8px; font-size: 11.5px; color: var(--n-500);
        line-height: 1.4; display: flex; align-items: center; gap: 6px;
    }
    .est-kpi-icon {
        width: 46px; height: 46px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        border-radius: 12px;
        background:
            radial-gradient(circle at 30% 25%, rgba(255,255,255,.9), transparent 55%),
            linear-gradient(135deg, var(--n-100), var(--n-200));
        color: var(--brand-800); font-size: 17px;
        box-shadow: var(--d-inset), 0 3px 8px rgba(15,23,42,.05);
    }
    .est-kpi.kpi-completed .est-kpi-icon {
        background:
            radial-gradient(circle at 30% 25%, rgba(255,255,255,.9), transparent 55%),
            linear-gradient(135deg, #d1fae5, var(--emerald-line));
        color: var(--emerald-dark);
    }
    .est-kpi.kpi-ongoing .est-kpi-icon {
        background:
            radial-gradient(circle at 30% 25%, rgba(255,255,255,.9), transparent 55%),
            linear-gradient(135deg, #fef3c7, var(--amber-line));
        color: var(--amber-dark);
    }

    /* ---- Filter bar ---- */
    .est-filters {
        display: flex; align-items: center; gap: 12px;
        flex-wrap: wrap; padding: 22px 32px 20px;
    }
    .est-filters-label {
        font-size: 11px; font-weight: 700; letter-spacing: .1em;
        text-transform: uppercase; color: var(--n-500); margin-right: 4px;
    }

    .est-mode-toggle {
        display: inline-flex; padding: 3px;
        background: var(--n-100); border: 1px solid var(--n-200);
        border-radius: 10px; gap: 2px;
        box-shadow: inset 0 1px 2px rgba(15,23,42,.05);
    }
    .est-mode-btn {
        padding: 8px 16px; font-size: 12.5px; font-weight: 600;
        color: var(--n-600); background: transparent; border: none;
        border-radius: 7px; cursor: pointer;
        transition: all .18s var(--ease-out);
        display: inline-flex; align-items: center; gap: 6px;
        white-space: nowrap;
    }
    .est-mode-btn i { font-size: 11px; }
    .est-mode-btn:hover:not(.active) { color: var(--n-900); }
    .est-mode-btn.active {
        background: var(--white); color: var(--brand-800);
        box-shadow: 0 1px 3px rgba(15,23,42,.1), var(--d-inset);
    }
    .est-mode-btn.active i { color: var(--brand-700); }

    .est-filter-field {
        position: relative; flex: 1 1 320px;
        min-width: 260px; max-width: 420px;
    }
    .est-filter-field > .est-filter-icon {
        position: absolute; left: 15px; top: 50%;
        transform: translateY(-50%); color: var(--n-400);
        font-size: 13px; pointer-events: none; z-index: 5;
        transition: color .2s var(--ease-out);
    }
    .est-filter-field.is-focused > .est-filter-icon { color: var(--brand-700); }
    .est-filter-field::after {
        content: "\f107"; font-family: FontAwesome;
        position: absolute; right: 15px; top: 50%;
        transform: translateY(-50%); color: var(--n-400);
        font-size: 13px; pointer-events: none; z-index: 2;
    }
    .est-filter-field.select2-active::after { display: none; }

    .est-filter-select {
        width: 100%;
        height: 44px;
        text-align: center !important;
        font-size: 13.5px; font-weight: 500; color: var(--n-900);
        background: var(--white); border: 1px solid var(--n-200);
        border-radius: 10px; outline: none;
        transition: all .22s var(--ease-out);
        appearance: none; -webkit-appearance: none;
        cursor: pointer; box-shadow: var(--d1);
    }
    .est-filter-select:hover { border-color: var(--n-300); }
    .est-filter-select:focus {
        border-color: var(--brand-700);
        box-shadow: var(--d-glow), var(--d1);
    }

    /* Select2 theming */
    .est-filter-field .select2-container { width: 100% !important; }
    .est-filter-field .select2-container--default .select2-selection--single {
        height: 44px; background: var(--white);
        border: 1px solid var(--n-200); border-radius: 10px;
        box-shadow: var(--d1); display: flex; align-items: center;
        padding-left: 40px; padding-right: 38px;
        position: relative; transition: all .22s var(--ease-out);
    }
    .est-filter-field .select2-container--default .select2-selection--single:hover {
        border-color: var(--n-300);
    }
    .est-filter-field .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding: 0; color: var(--n-900); font-size: 13.5px;
        font-weight: 500; line-height: 44px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .est-filter-field .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: var(--n-500); font-weight: 500;
    }
    .est-filter-field .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px; width: 38px; right: 0; top: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .est-filter-field .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: var(--n-400) transparent transparent transparent;
        border-width: 5px 4px 0 4px; margin-left: 0;
        transition: all .22s var(--ease-out);
    }
    .est-filter-field .select2-container--default .select2-selection--single .select2-selection__clear {
        position: absolute; right: 38px; top: 50%;
        transform: translateY(-50%); margin: 0; padding: 0;
        width: 18px; height: 18px;
        display: flex; align-items: center; justify-content: center;
        color: var(--n-400); font-size: 14px;
        transition: color .15s var(--ease-out); z-index: 3;
    }
    .est-filter-field .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        color: var(--rose);
    }
    .est-filter-field .select2-container--default.select2-container--focus .select2-selection--single,
    .est-filter-field .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--brand-700);
        box-shadow: var(--d-glow), var(--d1);
    }
    .est-filter-field .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent var(--brand-700) transparent;
        border-width: 0 4px 5px 4px;
    }
    body > .select2-container--default .select2-dropdown {
        border: 1px solid var(--n-200); border-radius: 10px;
        box-shadow: var(--d3); overflow: hidden;
        margin-top: 6px; background: var(--white);
    }
    body > .select2-container--default .select2-search--dropdown {
        padding: 8px; border-bottom: 1px solid var(--n-100);
        background: var(--n-50);
    }
    body > .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid var(--n-200); border-radius: 8px;
        padding: 8px 12px; font-size: 13px; outline: none;
        background: var(--white);
    }
    body > .select2-container--default .select2-results > .select2-results__options {
        max-height: 280px; padding: 4px 0;
    }
    body > .select2-container--default .select2-results__option {
        padding: 10px 16px; font-size: 13px; color: var(--n-700);
    }
    body > .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: var(--brand-800); color: #fff;
    }

    .est-btn-secondary {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 18px; border-radius: 10px;
        font-size: 13px; font-weight: 600; cursor: pointer;
        color: var(--n-700); background: var(--white);
        border: 1px solid var(--n-200); box-shadow: var(--d1);
        transition: all .2s var(--ease-out);
    }
    .est-btn-secondary:hover {
        background: var(--n-50); border-color: var(--n-300);
        color: var(--n-900); transform: translateY(-1px);
        box-shadow: var(--d2);
    }

    /* Download PDF button (matches Estimations) */
    .est-btn-download {
        display: none;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        color: #fff;
        border: 1px solid var(--emerald-dark);
        background: linear-gradient(180deg, #10b981, var(--emerald));
        box-shadow: 0 6px 18px -6px rgba(5,150,105,.5), var(--d-inset);
        transition: all .2s var(--ease-out);
        white-space: nowrap;
    }
    .est-btn-download.is-visible { display: inline-flex; }
    .est-btn-download:hover {
        transform: translateY(-1px);
        background: linear-gradient(180deg, var(--emerald), var(--emerald-dark));
    }

    /* Period filter */
    .est-monthyear-group {
        margin-left: auto; display: flex; gap: 10px;
        align-items: center; flex-wrap: wrap;
    }
    .est-filter-compact {
        height: 40px; padding: 0 32px 0 14px;
        font-size: 12.5px; font-weight: 500; color: var(--n-900);
        background: var(--white); border: 1px solid var(--n-200);
        border-radius: 9px; outline: none;
        appearance: none; -webkit-appearance: none;
        cursor: pointer; box-shadow: var(--d1);
        transition: all .2s var(--ease-out);
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 14px;
        min-width: 120px;
    }
    .est-filter-compact:hover  { border-color: var(--n-300); }
    .est-filter-compact:focus  { border-color: var(--brand-700); box-shadow: var(--d-glow), var(--d1); }

    /* ---- Detail panel ---- */
    .est-detail-panel {
        margin: 0 32px 20px; background: var(--white);
        border: 1px solid var(--n-200); border-radius: 14px;
        box-shadow: var(--d2); overflow: hidden; display: none;
    }
    .est-detail-panel.is-visible { display: block; }
    .est-detail-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 16px 22px;
        background: linear-gradient(180deg, #fbfcfd, #f8fafc);
        border-bottom: 1px solid var(--n-200);
    }
    .est-detail-title {
        display: flex; align-items: center; gap: 14px; min-width: 0;
    }
    .est-detail-icon {
        width: 40px; height: 40px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--brand-700), var(--brand-800));
        color: white; font-size: 15px;
        box-shadow: 0 6px 16px -6px rgba(30,58,95,.5), var(--d-inset);
    }
    .est-detail-title h4 {
        margin: 0 0 3px; font-size: 15px; font-weight: 700; color: var(--n-900);
    }
    .est-detail-title p { margin: 0; font-size: 12px; color: var(--n-500); }
    .est-detail-close {
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        border: 1px solid var(--n-200); background: var(--white);
        border-radius: 8px; color: var(--n-500);
        cursor: pointer; transition: all .15s var(--ease-out);
    }
    .est-detail-close:hover {
        background: var(--rose-soft); color: var(--rose);
        border-color: var(--rose-line);
    }
    .est-detail-body { padding: 6px 0; max-height: 360px; overflow-y: auto; }

    .est-item {
        display: flex; align-items: center; gap: 16px;
        padding: 13px 22px; border-bottom: 1px solid var(--n-100);
        flex-wrap: wrap;
    }
    .est-item:last-child { border-bottom: none; }
    .est-item:hover { background: var(--n-50); }
    .est-item-main { flex: 1; min-width: 220px; }
    .est-item-title {
        font-family: 'SFMono-Regular', Consolas, monospace;
        font-size: 12.5px; font-weight: 700; color: var(--brand-800);
        display: inline-block; padding: 3px 9px;
        background: linear-gradient(180deg, #eff6ff, #dbeafe);
        border: 1px solid #bfdbfe; border-radius: 6px;
        margin-bottom: 6px;
    }
    .est-item-meta {
        display: flex; gap: 14px; flex-wrap: wrap;
        font-size: 11.5px; color: var(--n-500); margin-top: 4px;
    }
    .est-item-meta span {
        display: inline-flex; align-items: center; gap: 5px;
    }
    .est-item-meta i { color: var(--n-400); font-size: 10px; }

    .est-item-badges {
        display: flex; gap: 6px; align-items: center; flex-wrap: wrap;
    }
    .est-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 9px; border-radius: 6px;
        font-size: 10.5px; font-weight: 600; white-space: nowrap;
    }
    .est-badge-done {
        background: var(--emerald-soft); color: var(--emerald-dark);
        border: 1px solid var(--emerald-line);
    }
    .est-badge-pending {
        background: var(--amber-soft); color: var(--amber-dark);
        border: 1px solid var(--amber-line);
    }
    .est-badge-cancelled {
        background: var(--rose-soft); color: var(--rose-dark);
        border: 1px solid var(--rose-line);
    }
    .est-badge-info {
        background: var(--cyan-soft); color: var(--cyan-dark);
        border: 1px solid var(--cyan-line);
    }

    .est-item-actions { display: flex; gap: 6px; flex-wrap: wrap; }
    .est-act {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 11px; border-radius: 6px;
        font-size: 11.5px; font-weight: 600;
        text-decoration: none; border: 1px solid transparent;
        cursor: pointer; transition: all .16s var(--ease-out);
        white-space: nowrap; line-height: 1;
        font-family: inherit; background: transparent;
    }
    .est-act:hover { text-decoration: none; transform: translateY(-1px); }
    .est-act-view {
        background: linear-gradient(180deg, #eff6ff, #dbeafe);
        color: var(--brand-800); border-color: #bfdbfe;
    }
    .est-act-view:hover {
        background: linear-gradient(180deg, #dbeafe, #bfdbfe);
        color: var(--brand-900);
    }
    .est-act-create {
        background: linear-gradient(180deg, var(--brand-700), var(--brand-800));
        color: white; border-color: var(--brand-800);
    }
    .est-act-create:hover {
        color: white;
        background: linear-gradient(180deg, var(--brand-800), var(--brand-900));
    }
    .est-act-pdf {
        background: linear-gradient(180deg, #ecfeff, #cffafe);
        color: var(--cyan-dark); border-color: var(--cyan-line);
    }
    .est-act-pdf:hover {
        background: linear-gradient(180deg, #cffafe, #a5f3fc);
        color: var(--cyan-dark); border-color: var(--cyan);
    }

    .est-empty { text-align: center; padding: 40px 20px; color: var(--n-500); }
    .est-empty i { font-size: 32px; color: var(--n-300); margin-bottom: 12px; display: block; }
    .est-empty p { margin: 0; font-size: 13px; }

    /* ---- Table ---- */
    .est-table-outer { position: relative; padding: 0 32px 24px; }
    .est-table-loading {
        position: absolute; top: 0; left: 32px; right: 32px; bottom: 24px;
        display: none; align-items: center; justify-content: center;
        background: rgba(255,255,255,.78);
        backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
        z-index: 20; border-radius: 12px;
    }
    .est-table-loading.show { display: flex; }
    .est-spinner {
        width: 42px; height: 42px;
        border: 3px solid var(--n-200); border-top-color: var(--brand-700);
        border-radius: 50%; animation: est-spin .85s linear infinite;
        box-shadow: 0 6px 20px -6px rgba(30,58,95,.3);
    }
    @keyframes est-spin { to { transform: rotate(360deg); } }

    .est-wrap-table { overflow-x: auto; border-radius: 14px; }
    .est-wrap #datatable {
        width: 100% !important; border-collapse: separate;
        border-spacing: 0; margin: 0; font-size: 13.5px;
        background: var(--white); border: 1px solid var(--n-200);
        border-radius: 14px; overflow: hidden; box-shadow: var(--d1);
    }
    .est-wrap #datatable thead th {
        background: linear-gradient(180deg, var(--brand-700) 0%, var(--brand-800) 100%);
        color: #fff; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .09em;
        padding: 16px 16px; text-align: left;
        border: none; white-space: nowrap; position: relative;
    }
    .est-wrap #datatable thead th + th::before {
        content: ""; position: absolute; left: 0;
        top: 26%; bottom: 26%; width: 1px;
        background: rgba(255,255,255,.18);
    }
    .est-wrap #datatable tbody td {
        padding: 14px 16px; border-bottom: 1px solid var(--n-100);
        vertical-align: middle; color: var(--n-700);
        background: var(--white); white-space: nowrap;
    }
    .est-wrap #datatable tbody tr:nth-child(even) td { background: #fcfdfe; }
    .est-wrap #datatable tbody tr:hover td { background: #f1f5f9; }
    .est-wrap #datatable tbody tr:last-child td { border-bottom: none; }

    /* Client column — wide enough to fit long names, allowed to wrap */
    .est-wrap #datatable thead th:nth-child(2),
    .est-wrap #datatable tbody td:nth-child(2) {
        min-width: 240px;
    }
    .est-wrap #datatable tbody td:nth-child(2) {
        white-space: normal;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .est-cell-client {
        font-weight: 600; color: var(--n-900); display: block; line-height: 1.35;
    }
    .est-cell-place {
        color: var(--n-500); font-size: 11.5px; display: block; margin-top: 2px;
    }
    .est-cell-vehicle { color: var(--n-800); font-weight: 600; }
    .est-cell-jobcard {
        font-family: 'SFMono-Regular', Consolas, monospace;
        font-size: 12.5px; font-weight: 700; color: var(--brand-800);
        padding: 3px 9px;
        background: linear-gradient(180deg, #eff6ff, #dbeafe);
        border: 1px solid #bfdbfe; border-radius: 6px;
        display: inline-block; text-decoration: none;
    }
    .est-cell-jobcard:hover { text-decoration: none; color: var(--brand-900); }
    .est-cell-date {
        font-size: 12.5px; color: var(--n-600); font-variant-numeric: tabular-nums;
    }

    /* Row actions */
    .est-actions {
        display: inline-flex; align-items: center; gap: 5px;
        flex-wrap: nowrap; white-space: nowrap;
    }
    .est-row-act {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 11px; border-radius: 6px;
        font-size: 11.5px; font-weight: 600;
        text-decoration: none; border: 1px solid transparent;
        cursor: pointer; transition: all .16s var(--ease-out);
        white-space: nowrap; line-height: 1;
        box-shadow: 0 1px 2px rgba(15,23,42,.04);
        font-family: inherit; background: transparent;
    }
    .est-row-act i { font-size: 11px; }
    .est-row-act:hover { text-decoration: none; transform: translateY(-1px); }
    .est-row-act-job {
        background: linear-gradient(180deg, #ecfeff, #cffafe);
        color: var(--cyan-dark); border-color: var(--cyan-line);
    }
    .est-row-act-job:hover {
        background: linear-gradient(180deg, #cffafe, #a5f3fc);
        color: var(--cyan-dark); border-color: var(--cyan);
    }
    .est-row-act-create {
        background: linear-gradient(180deg, var(--brand-700), var(--brand-800));
        color: #fff; border-color: var(--brand-800);
        box-shadow: 0 2px 8px -2px rgba(30,58,95,.35), inset 0 1px 0 rgba(255,255,255,.15);
    }
    .est-row-act-create:hover {
        color: #fff;
        background: linear-gradient(180deg, var(--brand-800), var(--brand-900));
    }
    .est-row-act-edit {
        background: linear-gradient(180deg, var(--emerald-soft), #d1fae5);
        color: var(--emerald-dark); border-color: var(--emerald-line);
    }
    .est-row-act-edit:hover {
        background: linear-gradient(180deg, #d1fae5, #a7f3d0);
        color: var(--emerald-dark); border-color: var(--emerald);
    }
    .est-row-act-delete {
        background: linear-gradient(180deg, #fff1f2, #ffe4e6);
        color: var(--rose); border-color: var(--rose-line);
    }
    .est-row-act-delete:hover {
        background: linear-gradient(180deg, #ffe4e6, #fecdd3);
        color: var(--rose-dark); border-color: var(--rose);
    }

    /* Create Delivery — violet, distinct from other action chips */
    .est-row-act-delivery {
        background: linear-gradient(180deg, var(--violet-soft), var(--violet-line));
        color: var(--violet-dark); border-color: var(--violet-line);
        box-shadow: 0 2px 8px -2px rgba(124,58,237,.25), inset 0 1px 0 rgba(255,255,255,.4);
    }
    .est-row-act-delivery:hover {
        background: linear-gradient(180deg, var(--violet-line), #c4b5fd);
        color: var(--violet-dark); border-color: var(--violet);
    }

    /* Delivered badge */
    .est-badge-delivered {
        background: var(--emerald-soft); color: var(--emerald-dark);
        border: 1px solid var(--emerald-line);
    }

    /* ---- Modals (kept visually consistent) ---- */
    #deliveryModal .modal-content,
    #edit_sale_modal .modal-content,
    #createInvoiceModal .modal-content,
    #jobDownloadModal .modal-content {
        border: none; border-radius: 14px; overflow: hidden;
        box-shadow: 0 24px 60px rgba(15,23,42,.24);
    }
    #deliveryModal .modal-header,
    #edit_sale_modal .modal-header,
    #createInvoiceModal .modal-header {
        background: linear-gradient(135deg, var(--brand-700), var(--brand-800));
        color: #fff; border-bottom: none; padding: 20px 24px;
    }
    #deliveryModal .modal-title,
    #edit_sale_modal .modal-title,
    #createInvoiceModal .modal-title {
        color: #fff; font-weight: 700; font-size: 16px;
    }
  
    #deliveryModal .close:hover,
    #edit_sale_modal .close:hover,
    #createInvoiceModal .close:hover { opacity: 1; color: #fff; }
    #deliveryModal .modal-body,
    #edit_sale_modal .modal-body,
    #createInvoiceModal .modal-body { padding: 22px 26px; }
    #deliveryModal .modal-footer,
    #edit_sale_modal .modal-footer,
    #createInvoiceModal .modal-footer {
        padding: 14px 24px; background: var(--n-50);
        border-top: 1px solid var(--n-200);
    }
    #deliveryModal .form-control,
    #edit_sale_modal .form-control,
    #createInvoiceModal .form-control {
        height: 42px; border-radius: 9px; border: 1px solid var(--n-200);
        font-size: 13.5px;
    }
    #deliveryModal .form-control:focus,
    #edit_sale_modal .form-control:focus,
    #createInvoiceModal .form-control:focus {
        border-color: var(--brand-700);
        box-shadow: var(--d-glow), var(--d1);
    }
    #createInvoiceModal .form-control[readonly] {
        background: var(--n-50); color: var(--n-700); cursor: default;
    }
    #createInvoiceModal hr {
        border-top: 1px solid var(--n-200); margin: 18px 0 14px;
    }
    #createInvoiceModal h4.m-b-15 {
        font-size: 13px; font-weight: 700; color: var(--n-800);
        letter-spacing: .02em; margin-bottom: 15px;
    }
    #deliveryModal #details {
        padding: 16px 4px; border: 1px solid var(--n-200);
        border-radius: 12px; background: var(--n-50);
    }
    #deliveryModal .btn-rounded,
    #edit_sale_modal .btn-rounded,
    #deliveryModal .btn-success,
    #edit_sale_modal .btn-success,
    #createInvoiceModal .btn-success {
        background: linear-gradient(180deg, #10b981, var(--emerald));
        border-color: var(--emerald-dark); font-weight: 600;
        box-shadow: 0 6px 18px -6px rgba(5,150,105,.5), var(--d-inset);
    }
    #createInvoiceModal .btn-success:disabled {
        opacity: .6; box-shadow: none; cursor: not-allowed;
    }

    /* Download spinner modal */
    #jobDownloadModal .modal-body { text-align: center; padding: 34px 24px; }
    #jobDownloadModal .est-spinner { margin: 0 auto 18px; width: 44px; height: 44px; }
    #jobDownloadModal h4 { margin: 0 0 8px; font-size: 15px; font-weight: 700; color: var(--n-900); }
    #jobDownloadModal p { margin: 0; font-size: 12.5px; color: var(--n-500); line-height: 1.5; }

    @media (max-width: 991px) {
        .est-head { padding: 24px 24px 20px; }
        .est-head-text h3 { font-size: 20px; }
        .est-summary { padding: 22px 24px 8px; gap: 14px; }
        .est-filters { padding: 18px 24px 16px; }
        .est-detail-panel { margin: 0 24px 16px; }
        .est-table-outer { padding: 0 24px 20px; }
        .est-table-loading { left: 24px; right: 24px; }
    }
    @media (max-width: 767px) {
        .est-head { padding: 20px; }
        .est-head-text h3 { font-size: 18px; }
        .est-summary { grid-template-columns: 1fr; padding: 18px 20px 4px; gap: 12px; }
        .est-filters { padding: 16px 20px 12px; gap: 10px; }
        .est-mode-toggle { width: 100%; }
        .est-mode-btn { flex: 1; justify-content: center; }
        .est-filter-field { flex: 1 1 100%; max-width: 100%; min-width: 0; }
        .est-btn-secondary { width: 100%; justify-content: center; }
        .est-btn-download { width: 100%; justify-content: center; }
        .est-monthyear-group { margin-left: 0; width: 100%; }
        .est-filter-compact { flex: 1; min-width: 0; }
        .est-detail-panel { margin: 0 20px 12px; }
        .est-table-outer { padding: 0 20px 20px; }
        .est-actions { flex-wrap: wrap; }
        .est-row-act { padding: 5px 9px; font-size: 11px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .est-kpi::before { animation: none; }
        * { transition-duration: .01ms !important; }
    }

    {{-- ===================== Invoice Modal Styling ===================== --}}

    #createInvoiceModal .ci-modal-dialog {
        margin-top: 45px;
    }

    #createInvoiceModal .ci-modal-content {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 12px 45px rgba(0,0,0,0.12);
    }

    /* Header */
    #createInvoiceModal .ci-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 23px 27px;
        background: #f8f9fb;
        border-bottom: 1px solid #e9ecef;
    }

    #createInvoiceModal .ci-header-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    #createInvoiceModal .ci-header-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #e9eef3;
        color: #34495e;
        font-size: 21px;
    }

    #createInvoiceModal .ci-header h4 {
        margin: 0 0 5px;
        font-size: 19px;
        font-weight: 700;
        color: #293746;
    }

    #createInvoiceModal .ci-header p {
        margin: 0;
        color: #788492;
        font-size: 12px;
    }

    #createInvoiceModal .ci-close {
        border: none;
        background: transparent;
        font-size: 27px;
        color: #7d8791;
        cursor: pointer;
        line-height: 1;
    }

    #createInvoiceModal .ci-close:hover {
        color: #222;
    }

    /* Body */
    #createInvoiceModal .ci-body {
        padding: 25px 27px;
        background: #fff;
    }

    /* Sections */
    #createInvoiceModal .ci-section {
        margin-bottom: 24px;
    }

    #createInvoiceModal .ci-section-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 17px;
        margin-bottom: 19px;
        border-bottom: 1px solid #edf0f2;
    }

    #createInvoiceModal .ci-section-icon {
        width: 36px;
        height: 36px;
        border-radius: 7px;
        background: #f0f2f4;
        color: #465563;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 15px;
    }

    #createInvoiceModal .ci-section-heading h5 {
        font-size: 14px;
        font-weight: 700;
        color: #344252;
        margin: 0 0 4px;
    }

    #createInvoiceModal .ci-section-heading small {
        color: #89939e;
        font-size: 11px;
    }

    /* Form fields */
    #createInvoiceModal .ci-form-group {
        margin-bottom: 20px;
    }

    #createInvoiceModal .ci-form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #495563;
        margin-bottom: 8px;
    }

    #createInvoiceModal .ci-input {
        height: 41px;
        border: 1px solid #dfe4e8;
        border-radius: 6px;
        box-shadow: none;
        font-size: 13px;
        color: #344252;
        padding: 9px 12px;
        transition: border-color .2s, box-shadow .2s;
    }

    #createInvoiceModal .ci-input:focus {
        border-color: #8c9ba9;
        box-shadow: 0 0 0 2px rgba(100,120,140,0.08);
    }

    #createInvoiceModal .ci-input::placeholder {
        color: #adb5bd;
        font-size: 12px;
    }

    #createInvoiceModal .ci-readonly,
    #createInvoiceModal .ci-input[readonly] {
        background: #f5f6f7;
        cursor: not-allowed;
        color: #66717d;
    }

    #createInvoiceModal .ci-optional {
        font-size: 10px;
        font-weight: 400;
        color: #929ca6;
        margin-left: 5px;
    }

    #createInvoiceModal .ci-status-wrapper {
        min-height: 41px;
        display: flex;
        align-items: center;
    }

    /* Footer */
    #createInvoiceModal .ci-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 17px 27px;
        background: #f8f9fb;
        border-top: 1px solid #e9ecef;
    }

    #createInvoiceModal .ci-footer-note {
        font-size: 11px;
        color: #89939e;
    }

    #createInvoiceModal .ci-footer-note i {
        margin-right: 5px;
    }

    #createInvoiceModal .ci-footer-actions {
        display: flex;
        gap: 9px;
    }

    #createInvoiceModal .ci-footer .btn {
        height: 38px;
        border-radius: 6px;
        padding: 0 19px;
        font-size: 12px;
        font-weight: 600;
        transition: all .2s;
    }

    #createInvoiceModal .ci-cancel-btn {
        background: #fff;
        border: 1px solid #dce1e5;
        color: #596573;
    }

    #createInvoiceModal .ci-cancel-btn:hover {
        background: #f0f2f4;
    }

    #createInvoiceModal .ci-submit-btn {
        background: #34495e;
        border: 1px solid #34495e;
        color: #fff;
    }

    #createInvoiceModal .ci-submit-btn:hover {
        background: #293b4d;
        border-color: #293b4d;
    }

    #createInvoiceModal .ci-submit-btn:disabled {
        opacity: .65;
        cursor: not-allowed;
    }

    /* Loading */
    #createInvoiceModal .ci-loader {
        text-align: center;
        padding: 55px 20px;
    }

    #createInvoiceModal .ci-loader h4 {
        color: #344252;
        font-size: 15px;
        font-weight: 600;
        margin: 12px 0 6px;
    }

    #createInvoiceModal .ci-loader p {
        color: #89939e;
        font-size: 12px;
    }

    #createInvoiceModal .ci-spinner {
        width: 37px;
        height: 37px;
        border: 3px solid #e8ebee;
        border-top-color: #526475;
        border-radius: 50%;
        animation: ci-spin .8s linear infinite;
        margin: auto;
    }

    @keyframes ci-spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive */
    @media(max-width: 767px) {
        #createInvoiceModal .ci-modal-dialog {
            margin: 10px;
        }

        #createInvoiceModal .ci-header {
            padding: 18px;
        }

        #createInvoiceModal .ci-body {
            padding: 20px 17px;
        }

        #createInvoiceModal .ci-footer {
            padding: 15px 17px;
            align-items: stretch;
            flex-direction: column;
        }

        #createInvoiceModal .ci-footer-note {
            text-align: center;
        }

        #createInvoiceModal .ci-footer-actions {
            width: 100%;
        }

        #createInvoiceModal .ci-footer-actions .btn {
            flex: 1;
        }

        #createInvoiceModal .ci-header h4 {
            font-size: 16px;
        }

        #createInvoiceModal .ci-header p {
            font-size: 11px;
        }
    }


       #deliveryModal .modal-dialog {
        width: 94%;
        max-width: 760px;
        margin: 65px auto 20px;
    }

    #deliveryModal .modal-content {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0, 0, 0, .12);
    }

    /* Header */
    #deliveryModal .modal-header {
        padding: 21px 25px;
        background: #f7f8fa;
        border-bottom: 1px solid #e8ebef;
    }

    #deliveryModal .modal-title {
        margin: 0;
        color: #344054;
        font-size: 18px;
        font-weight: 700;
    }

    #deliveryModal .modal-title:before {
        content: "\f0d1";
        font-family: FontAwesome;
        margin-right: 10px;
        color: #64748b;
    }

    #deliveryModal .modal-header .close {
        margin-top: -3px;
        font-size: 25px;
        font-weight: 400;
        opacity: .55;
    }

    #deliveryModal .modal-header .close:hover {
        opacity: 1;
    }

    /* Body */
    #deliveryModal .modal-body {
        padding: 25px;
        background: #fff;
    }

    #deliveryModal #details {
        padding: 22px 20px !important;
        border: 1px solid #e7ebef !important;
        border-radius: 7px;
        background: #fff;
    }

    #deliveryModal .delivery-section-title {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0 0 22px;
        padding-bottom: 12px;
        border-bottom: 1px solid #edf0f3;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
    }

    #deliveryModal .delivery-section-title i {
        color: #8491a2;
        font-size: 15px;
    }

    #deliveryModal .form-group {
        margin-bottom: 18px;
    }

    #deliveryModal .control-label {
        display: block;
        margin-bottom: 8px;
        color: #46505e;
        font-size: 12px;
        font-weight: 600;
    }

    #deliveryModal .form-control {
        height: 42px;
        padding: 10px 12px;
        border: 1px solid #dce1e7;
        border-radius: 5px;
        box-shadow: none;
        color: #354052;
        font-size: 13px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    #deliveryModal .form-control:focus {
        border-color: #8b99a9;
        box-shadow: 0 0 0 3px rgba(100, 116, 139, .08);
    }

    #deliveryModal .form-control[readonly] {
        background: #f5f6f8;
        color: #657184;
        cursor: not-allowed;
    }

    /* Current status */
    #deliveryModal #current_status_label {
        display: inline-block;
        min-height: 35px;
        margin: 0;
        padding: 8px 13px;
        border: 1px solid #e4e8ed;
        border-radius: 5px;
        background: #f7f8fa;
        color: #526071;
        font-size: 13px;
        font-weight: 600;
    }

    /* Select2 */
    #deliveryModal .select2-container {
        width: 100% !important;
    }

    #deliveryModal .select2-container .select2-selection--single {
        height: 42px;
        border: 1px solid #dce1e7;
        border-radius: 5px;
    }

    #deliveryModal .select2-container .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
        color: #354052;
        font-size: 13px;
    }

    #deliveryModal .select2-container .select2-selection__arrow {
        height: 40px;
    }

    /* Footer */
    #deliveryModal .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 17px 25px;
        background: #f8f9fa;
        border-top: 1px solid #e8ebef;
    }

    #deliveryModal .delivery-submit-btn {
        min-width: 140px;
        padding: 10px 20px;
        border: 1px solid #526b60;
        border-radius: 5px;
        background: #526b60;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .3px;
        transition: all .2s ease;
    }

    #deliveryModal .delivery-submit-btn:hover {
        background: #40564c;
        border-color: #40564c;
    }

    #deliveryModal .delivery-submit-btn i {
        margin-right: 6px;
    }

    @media (max-width: 767px) {
        #deliveryModal .modal-dialog {
            width: auto;
            margin: 15px 10px;
        }

        #deliveryModal .modal-header,
        #deliveryModal .modal-body,
        #deliveryModal .modal-footer {
            padding: 16px;
        }

        #deliveryModal #details {
            padding: 15px !important;
        }

        #deliveryModal .modal-footer .delivery-submit-btn {
            width: 100%;
        }
    }

    #edit_sale_modal .modal-dialog {
        width: 94%;
        max-width: 620px;
        margin: 65px auto 20px;
    }

    #edit_sale_modal .modal-content {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 12px 40px rgba(0, 0, 0, .12);
    }

    /* Header */
    #edit_sale_modal .modal-header {
        padding: 21px 25px;
        background: #f7f8fa;
        border-bottom: 1px solid #e8ebef;
    }

    #edit_sale_modal .modal-title {
        margin: 0;
        color: #344054;
        font-size: 18px;
        font-weight: 700;
    }

    #edit_sale_modal .modal-title i {
        margin-right: 9px;
        color: #64748b;
    }

    #edit_sale_modal .modal-header .close {
        margin-top: -3px;
        font-size: 25px;
        font-weight: 400;
        opacity: .55;
    }

    #edit_sale_modal .modal-header .close:hover {
        opacity: 1;
    }

    /* Body */
    #edit_sale_modal .modal-body {
        padding: 25px;
        background: #fff;
    }

    #edit_sale_modal .delivery-edit-section {
        margin-bottom: 22px;
        padding: 20px;
        border: 1px solid #e7ebef;
        border-radius: 7px;
        background: #fff;
    }

    #edit_sale_modal .delivery-edit-section:last-child {
        margin-bottom: 0;
    }

    #edit_sale_modal .delivery-section-heading {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 19px;
        padding-bottom: 12px;
        border-bottom: 1px solid #edf0f3;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
    }

    #edit_sale_modal .delivery-section-heading i {
        color: #8491a2;
        font-size: 15px;
    }

    #edit_sale_modal .form-group {
        margin-bottom: 5px;
    }

    #edit_sale_modal .control-label {
        display: block;
        margin-bottom: 8px;
        color: #46505e;
        font-size: 12px;
        font-weight: 600;
    }

    #edit_sale_modal .form-control {
        height: 43px;
        padding: 10px 12px;
        border: 1px solid #dce1e7;
        border-radius: 5px;
        box-shadow: none;
        color: #354052;
        font-size: 13px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    #edit_sale_modal .form-control:focus {
        border-color: #8b99a9;
        box-shadow: 0 0 0 3px rgba(100, 116, 139, .08);
    }

    #edit_sale_modal .form-control[readonly] {
        background: #f5f6f8;
        color: #657184;
        cursor: not-allowed;
    }

    #edit_sale_modal .form-control::placeholder {
        color: #a0a8b4;
        font-size: 12px;
    }

    /* Date input */
    #edit_sale_modal .input-group {
        width: 100%;
    }

    #edit_sale_modal .input-group .form-control {
        border-radius: 5px 0 0 5px;
    }

    #edit_sale_modal .input-group-addon {
        min-width: 43px;
        background: #f5f6f8;
        border: 1px solid #dce1e7;
        border-left: 0;
        border-radius: 0 5px 5px 0;
        color: #64748b;
    }

    /* Footer */
    #edit_sale_modal .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 17px 25px;
        background: #f8f9fa;
        border-top: 1px solid #e8ebef;
    }

    #edit_sale_modal .delivery-update-btn {
        min-width: 130px;
        padding: 10px 20px;
        border: 1px solid #526b60;
        border-radius: 5px;
        background: #526b60;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        transition: all .2s ease;
    }

    #edit_sale_modal .delivery-update-btn:hover {
        background: #40564c;
        border-color: #40564c;
    }

    #edit_sale_modal .delivery-update-btn i {
        margin-right: 6px;
    }

    @media (max-width: 600px) {
        #edit_sale_modal .modal-dialog {
            width: auto;
            margin: 15px 10px;
        }

        #edit_sale_modal .modal-header,
        #edit_sale_modal .modal-body,
        #edit_sale_modal .modal-footer {
            padding: 16px;
        }

        #edit_sale_modal .delivery-edit-section {
            padding: 15px;
        }

        #edit_sale_modal .delivery-update-btn {
            width: 100%;
        }
    }

.inv-created-by {
  font-size: 10px;
}
</style>

<div class="est">

    {{-- ===== Step indicator ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="est-steps">
                <a href="{{ url('home') }}"><i class="fa fa-home"></i> Home</a>
                <span class="est-sep"><i class="fa fa-angle-right"></i></span>
                <a href="#" class="active">Job Cards</a>
            </div>
        </div>
    </div>

    {{-- ===== Main panel ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="est-panel">

                {{-- Header --}}
                <div class="est-head">
                    <div class="est-head-text">
                        <h3>
                            <span class="est-head-icon"><i class="fa fa-clipboard"></i></span>
                            Job Cards
                        </h3>
                        <p>Filter job cards by client or vehicle. Selecting a filter reveals the full service history for that client or vehicle.</p>
                    </div>
                </div>

                @php
                    /* ---- KPI counters ---- */
                    $totalJobs     = $sales->count();
                    $completedJobs = $sales->filter(function ($s) { return (int) $s->status === 1; })->count();
                    $ongoingJobs   = $sales->filter(function ($s) { return (int) $s->status === 0; })->count();

                    /* ---- Filter options ---- */
                    $clientNames = $sales->map(function ($s) {
                        return $s->client_name ?: $s->customer_name;
                    })->filter()->unique()->values();

                    $vehicleRegs = $sales->pluck('vehicle_reg')->filter()->unique()->values();

                    /* ---- Period filter: no default — shows ALL months / years ---- */
                    $availableYears = $sales->pluck('created_date')->filter()
                        ->map(function ($d) {
                            if (preg_match('/^(\d{4})/', (string) $d, $m)) return (int) $m[1];
                            return null;
                        })->filter()->unique()->values()
                        ->sortDesc()->values();
                @endphp

                {{-- KPI strip --}}
                <div class="est-summary">
                    <div class="est-kpi">
                        <div>
                            <p class="est-kpi-label">Total Job Cards</p>
                            <div class="est-kpi-value">{{ $totalJobs }}</div>
                            <div class="est-kpi-sub">
                                <i class="fa fa-layer-group" style="color:var(--n-400);font-size:10px;"></i>
                                across the branch
                            </div>
                        </div>
                        <div class="est-kpi-icon"><i class="fa fa-clipboard"></i></div>
                    </div>

                    <div class="est-kpi kpi-completed">
                        <div>
                            <p class="est-kpi-label">Work Completed</p>
                            <div class="est-kpi-value">{{ $completedJobs }}</div>
                            <div class="est-kpi-sub">
                                <i class="fa fa-check-circle" style="color:var(--emerald);font-size:10px;"></i>
                                delivered / finished
                            </div>
                        </div>
                        <div class="est-kpi-icon"><i class="fa fa-check-circle"></i></div>
                    </div>

                    <div class="est-kpi kpi-ongoing">
                        <div>
                            <p class="est-kpi-label">Ongoing Work</p>
                            <div class="est-kpi-value">{{ $ongoingJobs }}</div>
                            <div class="est-kpi-sub">
                                <i class="fa fa-spinner" style="color:var(--amber);font-size:10px;"></i>
                                in progress
                            </div>
                        </div>
                        <div class="est-kpi-icon"><i class="fa fa-spinner"></i></div>
                    </div>
                </div>

                {{-- Filter bar --}}
                <div class="est-filters">
                    <span class="est-filters-label">Filter By</span>

                    <div class="est-mode-toggle" role="tablist" aria-label="Filter mode">
                        <button type="button" class="est-mode-btn active" data-mode="client" role="tab" aria-selected="true">
                            <i class="fa fa-user-o"></i> Client Name
                        </button>
                        <button type="button" class="est-mode-btn" data-mode="vehicle" role="tab" aria-selected="false">
                            <i class="fa fa-car"></i> Vehicle Reg
                        </button>
                    </div>

                    <div class="est-filter-field">
                        <i class="fa fa-user-o est-filter-icon" id="filter_icon"></i>
                        <select id="filter_value" class="est-filter-select">
                            <option value="">All Clients</option>
                        </select>
                    </div>

                    <button type="button" class="est-btn-secondary" id="clear_filter">
                        <i class="fa fa-times"></i> Clear
                    </button>

                    {{-- Download PDF — appears only when a client / vehicle is selected --}}
                    <button type="button" class="est-btn-download" id="download_pdf_btn">
                        <i class="fa fa-file-pdf-o"></i> Download PDF
                    </button>

                    {{-- Period filter — no default selected, shows all months / years --}}
                    <div class="est-monthyear-group">
                        <span class="est-filters-label">Period</span>
                        <select id="filter_month" class="est-filter-compact" aria-label="Month">
                            <option value="">All Months</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                        <select id="filter_year" class="est-filter-compact" aria-label="Year">
                            <option value="">All Years</option>
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Detail panel --}}
                <div class="est-detail-panel" id="detail_panel">
                    <div class="est-detail-header">
                        <div class="est-detail-title">
                            <span class="est-detail-icon"><i class="fa fa-list" id="detail_icon"></i></span>
                            <div>
                                <h4 id="detail_title">Details</h4>
                                <p id="detail_subtitle">&nbsp;</p>
                            </div>
                        </div>
                        <button type="button" class="est-detail-close" id="close_detail" title="Close details">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div class="est-detail-body" id="detail_body"></div>
                </div>

                {{-- Table --}}
                <div class="est-table-outer">
                    <div class="est-table-loading" id="estTableLoading">
                        <div class="est-spinner"></div>
                    </div>

                    <div class="est-wrap-table">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="15px">#</th>
                                    <th width="450px">Client Name</th>
                                    <th width="90px">Vehicle Reg</th>
                                    <th width="100px">Job Card No</th>
                                    <th width="110px">Date</th>
                                    <th width="160px">Invoice </th>
                                    <th width="130px">Work Status</th>
                                    <th width="160px">Delivery Note</th>
                                    <th width="130px">Created By </th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($sales as $key => $product)
                                    @php
                                        $rowClient   = $product->client_name ?: $product->customer_name;
                                        $rowDate     = !empty($product->delivery_date) ? $product->delivery_date : $product->created_date;
                                        $displayDate = !empty($product->delivery_date)
                                            ? \Carbon\Carbon::parse($product->delivery_date)->format('Y M, d')
                                            : \Carbon\Carbon::parse($product->created_date)->format('Y M, d');
                                        $workStatus  = (int) $product->status;
                                        $isInvoiced  = !empty($product->invoice_number);
                                    @endphp
                                    <tr data-client="{{ $rowClient }}"
                                        data-vehicle="{{ $product->vehicle_reg }}"
                                        data-date="{{ $rowDate }}"
                                        data-status="{{ $workStatus }}"
                                        data-invoiced="{{ $isInvoiced ? 1 : 0 }}">
                                        <td width="30">{{ $key + 1 }}</td>

                                        <td>
                                            @if($product->client_name)
                                                <span class="est-cell-client">{{ strtoupper($product->client_name) }}</span>
                                                <span class="est-cell-place">{{ strtoupper($product->place) }}</span>
                                            @else
                                                <span class="est-cell-client">{{ $product->customer_name }}</span>
                                            @endif
                                        </td>

                                        <td><span class="est-cell-vehicle">{{ strtoupper($product->vehicle_reg) }}</span></td>

                                        <td>
                                            <a href="#" class="est-cell-jobcard">{{ $product->job_card_no }}</a>
                                        </td>

                                        <td><span class="est-cell-date">{{ $displayDate }}</span></td>

                                        {{-- Invoice status: Invoiced → #, Completed+uninvoiced → Create Invoice (modal), In Progress → Ongoing Work, Cancelled → Cancelled --}}
                                        <td>
                                            @if ($isInvoiced)
                                                @php
                                                    $inv = (int) $product->invoice_number;
                                                    $invPadded = $inv < 10 ? '00000'.$inv
                                                              : ($inv < 100 ? '0000'.$inv
                                                              : ($inv < 1000 ? '000'.$inv
                                                              : ($inv < 10000 ? '00'.$inv
                                                              : ($inv < 100000 ? '0'.$inv : $inv))));
                                                @endphp
                                                <span class="est-badge est-badge-done">
                                                    <i class="fa fa-check"></i> #{{ $invPadded }}
                                                </span>
                                            @elseif ($workStatus === 1)
                                                <button type="button"
                                                        class="est-row-act est-row-act-create create-invoice-btn"
                                                        data-job-card="{{ $product->job_card_no }}">
                                                    <i class="fa fa-plus"></i> Create Invoice
                                                </button>
                                            @elseif ($workStatus === 0)
                                                <span class="est-badge est-badge-info">
                                                    <i class="fa fa-spinner"></i> Ongoing Work
                                                </span>
                                            @else
                                                <span class="est-badge est-badge-cancelled">
                                                    <i class="fa fa-ban"></i> Cancelled
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Work status --}}
                                        <td>
                                            @if ($workStatus === 0)
                                                <span class="est-badge est-badge-pending">
                                                    <i class="fa fa-spinner"></i> In Progress
                                                </span>
                                            @elseif ($workStatus === 1)
                                                <span class="est-badge est-badge-done">
                                                    <i class="fa fa-check"></i> Completed
                                                </span>
                                            @else
                                                <span class="est-badge est-badge-cancelled">
                                                    <i class="fa fa-times"></i> Cancelled
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Delivery note --}}
                                        <td>
                                            @if ($workStatus === 0)
                                                <button type="button"
                                                        class="est-row-act est-row-act-delivery"
                                                        onclick="openDeliveryModal('{{ $product->job_card_no }}', '{{ $workStatus }}')">
                                                    <i class="fa fa-truck"></i> Create Delivery
                                                </button>
                                            @elseif ($workStatus === 1)
                                                <span class="est-badge est-badge-delivered" style="margin-right:6px;">
                                                    <i class="fa fa-check-double"></i> Delivered
                                                </span>
                                                <a href="{{ url('estimations/delivery-note/pdf/'.$product->reference) }}"
                                                   target="_blank"
                                                   class="est-row-act est-row-act-job">
                                                    <i class="fa fa-file-pdf-o"></i> Delivery Note
                                                </a>
                                            @else
                                                <span class="est-badge est-badge-cancelled">
                                                    <i class="fa fa-ban"></i> Cancelled
                                                </span>
                                            @endif
                                        </td>
                                               <td>
                                                <span class="inv-created-by">
                                                    {{ strtoupper($product->fname) }} {{ strtoupper($product->lname) }}
                                                </span>
                                         </td>
                                        {{-- Actions --}}
                                        <td>
                                            <div class="est-actions">
                                                <a href="{{ url('job-cards/details/pdf/'.$product->reference) }}"
                                                   target="_blank"
                                                   class="est-row-act est-row-act-job"
                                                   title="Job Sheet PDF">
                                                    <i class="fa fa-file-pdf-o"></i> Job Sheet
                                                </a>
                                                <button type="button"
                                                        class="est-row-act est-row-act-edit edit_delivery"
                                                        data-id="{{ $product->reference }}"
                                                        data-toggle="modal"
                                                        data-target=".bs-example-modal-sm"
                                                        title="Edit Delivery">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <a class="est-row-act est-row-act-delete delete-job-card"
                                                  href="{{ url('job-cards/delete/'.$product->reference) }}"
                                                  data-reference="{{ $product->reference }}"
                                                  data-job-card="{{ $product->job_card_no }}"
                                                  data-client="{{ $product->client_name ?: $product->customer_name }}"
                                                  data-vehicle="{{ $product->vehicle_reg }}"
                                                  data-has-invoice="{{ !empty($product->invoice_number) ? '1' : '0' }}"
                                                  title="Delete Job Card">
                                                    <i class="fa fa-trash"></i>
                                                </a>
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


{{-- ===================== Professional Create Invoice Modal ===================== --}}
<div class="modal fade" id="createInvoiceModal" tabindex="-1" role="dialog"
     data-backdrop="static" data-keyboard="false">

    <div class="modal-dialog modal-lg ci-modal-dialog" role="document">
        <div class="modal-content ci-modal-content">

            <form id="createInvoiceForm" method="POST"
                  action="{{ url('invoices/create-invoice/post') }}">

                @csrf

                {{-- Modal Header --}}
                <div class="ci-header">
                    <div class="ci-header-content">
                        <div class="ci-header-icon">
                            <i class="fa fa-file-text-o"></i>
                        </div>

                        <div>
                            <h4>Create Tax Invoice</h4>
                            <p>Complete the information below to generate an invoice.</p>
                        </div>
                    </div>

                    <button type="button" class="ci-close"
                            data-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body ci-body">

                    {{-- Loading State --}}
                    <div id="ciLoader" class="ci-loader">
                        <div class="ci-spinner"></div>
                        <h4>Preparing Invoice</h4>
                        <p>Please wait while invoice information is loading...</p>
                    </div>

                    {{-- Form Content --}}
                    <div id="ciForm" style="display:none;">

                        {{-- Hidden Fields --}}
                        <input type="hidden" name="reference" id="ci_reference">
                        <input type="hidden" name="client_id" id="ci_client_id">
                        <input type="hidden" name="estimate_ref" id="ci_estimate_ref">
                        <input type="hidden" name="vehicle_reg" id="ci_vehicle_reg">
                        <input type="hidden" name="account" id="ci_account">
                        <input type="hidden" name="make" id="ci_make">

                        {{-- Invoice Information --}}
                        <div class="ci-section">

                            <div class="ci-section-heading">
                                <div class="ci-section-icon">
                                    <i class="fa fa-file-text-o"></i>
                                </div>

                                <div>
                                    <h5>Invoice Information</h5>
                                    <small>Basic details of the invoice</small>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group ci-form-group">
                                        <label for="ci_job_card_no">
                                            Job Card
                                        </label>

                                        <input type="text"
                                               name="job_card_no"
                                               id="ci_job_card_no"
                                               readonly
                                               class="form-control ci-input">
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group ci-form-group">
                                        <label for="ci_bill_amount">
                                            Bill Amount
                                        </label>

                                        <input type="text"
                                               name="bill_amount"
                                               id="ci_bill_amount"
                                               readonly
                                               class="form-control ci-input ci-readonly">
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group ci-form-group">
                                        <label for="ci_invoice_no">
                                            Invoice Number
                                        </label>

                                        <input type="text"
                                               name="invoice_no"
                                               id="ci_invoice_no"
                                               class="form-control ci-input"
                                               placeholder="Invoice number">
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group ci-form-group">
                                        <label>Invoice Status</label>

                                        <div class="ci-status-wrapper" id="ci_status"></div>
                                    </div>
                                </div>

                            </div>

                            {{-- LPO Information --}}
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group ci-form-group">
                                        <label for="ci_lpo_number">
                                            LPO Number
                                            <span class="ci-optional">Optional</span>
                                        </label>

                                        <input type="text"
                                               name="lpo_number"
                                               id="ci_lpo_number"
                                               class="form-control ci-input"
                                               placeholder="Enter Local Purchase Order number">
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Bank Details --}}
                        <div class="ci-section ci-bank-section">

                            <div class="ci-section-heading">
                                <div class="ci-section-icon">
                                    <i class="fa fa-university"></i>
                                </div>

                                <div>
                                    <h5>Bank Details</h5>
                                    <small>Payment information displayed on the invoice</small>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group ci-form-group">
                                        <label for="ci_bank_account_no">
                                            Account Number
                                        </label>

                                        <input type="text"
                                               name="account_no"
                                               id="ci_bank_account_no"
                                               class="form-control ci-input"
                                               placeholder="Enter account number">
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group ci-form-group">
                                        <label for="ci_bank_account_name">
                                            Account Name
                                        </label>

                                        <input type="text"
                                               name="account_name"
                                               id="ci_bank_account_name"
                                               class="form-control ci-input"
                                               placeholder="Enter account name">
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group ci-form-group">
                                        <label for="ci_bank_name">
                                            Bank Name
                                        </label>

                                        <input type="text"
                                               name="bank_name"
                                               id="ci_bank_name"
                                               class="form-control ci-input"
                                               placeholder="Enter bank name">
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group ci-form-group">
                                        <label for="ci_branch_name">
                                            Bank Branch
                                        </label>

                                        <input type="text"
                                               name="branch_name"
                                               id="ci_branch_name"
                                               class="form-control ci-input"
                                               placeholder="Enter branch name">
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group ci-form-group">
                                        <label for="ci_swift_code">
                                            SWIFT Code
                                        </label>

                                        <input type="text"
                                               name="swift_code"
                                               id="ci_swift_code"
                                               class="form-control ci-input"
                                               placeholder="Enter SWIFT code">
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="ci-footer">
                    <div class="ci-footer-note">
                        <i class="fa fa-info-circle"></i>
                        Review invoice details before creating.
                    </div>

                    <div class="ci-footer-actions">
                        <button type="button"
                                class="btn ci-cancel-btn"
                                data-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit"
                                class="btn ci-submit-btn"
                                id="ci_submit_btn">
                            <i class="fa fa-check"></i>
                            Create Invoice
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>



<!-- ============ CREATE DELIVERY MODAL ============ -->
<div class="modal fade" id="deliveryModal" tabindex="-1"
     role="dialog" aria-labelledby="deliveryModalLabel">

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">

            <form method="POST"
                  action="{{ url('job-cards/update-status') }}"
                  enctype="multipart/form-data">

                @csrf

                <!-- HEADER -->
                <div class="modal-header">
                    <button type="button" class="close"
                            data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>

                    <h4 class="modal-title" id="deliveryModalLabel">
                        Create Delivery
                    </h4>

                    <small class="text-muted">
                        Confirm job completion and update delivery status.
                    </small>
                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <div id="details">

                        <div class="delivery-section-title">
                            <i class="fa fa-clipboard"></i>
                            Job Card Information
                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">
                                        Job Card #
                                    </label>

                                    <input type="text"
                                           name="job_card_no"
                                           id="job_card_no"
                                           readonly
                                           class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">
                                        Current Job Status
                                    </label>

                                    <div>
                                        <h4 id="current_status_label"></h4>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="delivery-section-title"
                             style="margin-top:10px;">
                            <i class="fa fa-refresh"></i>
                            Delivery Status Update
                        </div>

                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label class="control-label">
                                        Change Status
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select name="status"
                                            class="form-control select2"
                                            required>
                                        <option value="">
                                            --- Select Status ---
                                        </option>

                                        <option value="1">
                                            Completed
                                        </option>
                                    </select>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">

                    <button type="submit"
                            class="delivery-submit-btn">
                        <i class="fa fa-check-circle"></i>
                        CREATE DELIVERY
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>


{{-- ===================== EDIT DELIVERY NOTE MODAL ===================== --}}
<div class="modal fade bs-example-modal-md"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myLargeModalLabel"
     aria-hidden="true"
     id="edit_sale_modal">

    <form id="formUpdate"
          method="post"
          action="{{ url('job-cards/delivery-note/update') }}">

        @csrf

        <div class="modal-dialog modal-md">

            <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header">

                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-hidden="true">
                        &times;
                    </button>

                    <h4 class="modal-title" id="myLargeModalLabel">
                        <i class="fa fa-pencil-square-o"></i>
                        Edit Delivery Note
                    </h4>

                    <small class="text-muted">
                        Update delivery note information and delivery date.
                    </small>

                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <!-- JOB CARD INFORMATION -->
                    <div class="delivery-edit-section">

                        <div class="delivery-section-heading">
                            <i class="fa fa-clipboard"></i>
                            Job Card Information
                        </div>

                        <div class="form-group">

                            <label for="job_card_no" class="control-label">
                                Job Card No.
                            </label>

                            <input type="text"
                                   name="job_card_no"
                                   id="job_card_no"
                                   class="form-control job_card_no"
                                   placeholder="Job Card Number"
                                   readonly>

                            <input type="hidden"
                                   name="reference_no"
                                   id="reference_no"
                                   class="form-control">

                        </div>

                    </div>

                    <!-- DELIVERY DATE -->
                    <div class="delivery-edit-section">

                        <div class="delivery-section-heading">
                            <i class="fa fa-calendar"></i>
                            Delivery Information
                        </div>

                        <div class="form-group">

                            <label for="datepicker-autoclose"
                                   class="control-label">
                                Delivery Date
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <input type="text"
                                       class="form-control delivery_date"
                                       required
                                       autocomplete="off"
                                       name="delivery_date"
                                       placeholder="YYYY-MM-DD"
                                       id="datepicker-autoclose"
                                       data-date-format="yyyy-mm-dd">

                                <span class="input-group-addon">
                                    <i class="ti-calendar"></i>
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">

                    <button type="submit"
                            class="delivery-update-btn">
                        <i class="fa fa-save"></i>
                        Update Delivery
                    </button>

                </div>

            </div>

        </div>

    </form>
</div>


{{-- ===================== Download spinner modal ===================== --}}
<div class="modal fade" id="jobDownloadModal" tabindex="-1" role="dialog"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="max-width:360px;margin:120px auto 0;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="est-spinner"></div>
                <h4>Preparing report…</h4>
                <p>Please wait while your PDF is being generated.</p>
            </div>
        </div>
    </div>
</div>

<script>
    /* ============================================================
     * Delivery modal (unchanged behaviour)
     * ============================================================ */
    function openDeliveryModal(jobCardNo, status) {
        $('#job_card_no').val(jobCardNo);
        let statusLabel = '';
        switch (parseInt(status)) {
            case 0: statusLabel = '<span class="label label-info">In Progress</span>'; break;
            case 1: statusLabel = '<span class="label label-success">Completed</span>'; break;
            default: statusLabel = '<span class="label label-warning">Cancelled</span>';
        }
        $('#current_status_label').html(statusLabel);
        $('#deliveryModal').modal('show');
    }

    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        /* ============================================================
         * Edit Delivery modal (unchanged behaviour)
         * ============================================================ */
        $('.edit_delivery').click(function () {
            var reference_no = $(this).data('id');
            $.ajax({
                url: `job-cards/delivery-note/edit/${reference_no}`,
                type: "GET",
                success: function (response) {
                    $('#edit_sale_modal').modal('show');
                    $(".job_card_no").val(response[0].job_card_no);
                    $(".delivery_date").val(response[0].delivery_date);
                    $("#reference_no").val(response[0].estimate_reference);
                }
            });
        });

        $('#edit_sale_modal').on('shown.bs.modal', function () {
            $('#datepicker-autoclose').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                todayBtn: 'linked',
                clearBtn: true
            });
        });

        /* ============================================================
         * CLIENT / VEHICLE FILTER + PERIOD + HISTORY DETAIL
         * ============================================================ */
        var ALL_JOBS  = @json($sales);
        var filterMode = 'client';
        var currentFilter = '';
        var totalCount = ALL_JOBS.length;
        var baseUrl = '{{ url('/') }}/';

        function escapeHtml(str) {
            if (str == null) return '';
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function showSpinner() { $('#estTableLoading').addClass('show'); }
        function hideSpinner() { $('#estTableLoading').removeClass('show'); }

        function destroySelect2() {
            var $sel = $('#filter_value');
            if ($sel.hasClass('select2-hidden-accessible')) {
                $sel.select2('destroy');
            }
            $('.est-filter-field').removeClass('select2-active is-focused');
        }

        function initSelect2() {
            var $sel = $('#filter_value');
            if (!$.fn.select2) {
                $('.est-filter-field').removeClass('select2-active');
                return;
            }
            if ($sel.hasClass('select2-hidden-accessible')) {
                $sel.select2('destroy');
            }
            $sel.select2({
                width: '100%',
                placeholder: filterMode === 'client' ? 'All Clients' : 'All Vehicles',
                allowClear: true,
                minimumResultsForSearch: 6,
                dropdownParent: $('body')
            });
            $('.est-filter-field').addClass('select2-active');
            $sel.on('select2:open',  function () { $('.est-filter-field').addClass('is-focused'); });
            $sel.on('select2:close', function () { $('.est-filter-field').removeClass('is-focused'); });
        }

        function rebuildFilterSelect() {
            destroySelect2();
            var $sel = $('#filter_value');
            $sel.empty();

            if (filterMode === 'client') {
                $sel.append('<option value="">All Clients</option>');
                var seen = {}, names = [];
                ALL_JOBS.forEach(function (s) {
                    var name = s.client_name || s.customer_name;
                    if (name && !seen[name]) { seen[name] = true; names.push(name); }
                });
                names.sort();
                names.forEach(function (n) {
                    $sel.append('<option value="' + escapeHtml(n) + '">' + escapeHtml(n) + '</option>');
                });
                $('#filter_icon').removeClass().addClass('fa fa-user-o est-filter-icon');
            } else {
                $sel.append('<option value="">All Vehicles</option>');
                var seenV = {}, vehicles = [];
                ALL_JOBS.forEach(function (s) {
                    if (s.vehicle_reg && !seenV[s.vehicle_reg]) {
                        seenV[s.vehicle_reg] = true;
                        vehicles.push(s.vehicle_reg);
                    }
                });
                vehicles.sort();
                vehicles.forEach(function (v) {
                    $sel.append('<option value="' + escapeHtml(v) + '">' + escapeHtml(v) + '</option>');
                });
                $('#filter_icon').removeClass().addClass('fa fa-car est-filter-icon');
            }
            currentFilter = '';
            $sel.val('');
            initSelect2();
        }

        /* ---------- Month / Year helpers ---------- */
        function parseDateParts(dateStr) {
            if (!dateStr) return null;
            var m = String(dateStr).match(/^(\d{4})[-\/](\d{1,2})/);
            return m ? { year: +m[1], month: +m[2] } : null;
        }
        function getMonthYearFilters() {
            return {
                month: parseInt($('#filter_month').val()) || 0,
                year:  parseInt($('#filter_year').val())  || 0
            };
        }
        function rowMatchesMonthYear($row, mf) {
            if (!mf.month && !mf.year) return true;
            var p = parseDateParts($row.attr('data-date'));
            if (!p) return false;
            if (mf.month && p.month !== mf.month) return false;
            if (mf.year  && p.year  !== mf.year ) return false;
            return true;
        }
        function jobMatchesMonthYear(j, mf) {
            if (!mf.month && !mf.year) return true;
            var d = j.delivery_date || j.created_date;
            var p = parseDateParts(d);
            if (!p) return false;
            if (mf.month && p.month !== mf.month) return false;
            if (mf.year  && p.year  !== mf.year ) return false;
            return true;
        }

        /* ---------- Status badge helpers ---------- */
        function workStatusBadge(status) {
            var s = parseInt(status);
            if (s === 1) return '<span class="est-badge est-badge-done"><i class="fa fa-check"></i> Completed</span>';
            if (s === 2) return '<span class="est-badge est-badge-cancelled"><i class="fa fa-times"></i> Cancelled</span>';
            return '<span class="est-badge est-badge-pending"><i class="fa fa-spinner"></i> In Progress</span>';
        }

        function invoiceStatusBadge(inv, workStatus) {
            var n = parseInt(inv);
            if (!isNaN(n) && n > 0) {
                var padded = n < 10 ? '00000'+n
                           : (n < 100 ? '0000'+n
                           : (n < 1000 ? '000'+n
                           : (n < 10000 ? '00'+n
                           : (n < 100000 ? '0'+n : n))));
                return '<span class="est-badge est-badge-done"><i class="fa fa-check"></i> #' + padded + '</span>';
            }
            var s = parseInt(workStatus);
            if (s === 0) return '<span class="est-badge est-badge-info"><i class="fa fa-spinner"></i> Ongoing Work</span>';
            if (s === 2) return '<span class="est-badge est-badge-cancelled"><i class="fa fa-ban"></i> Cancelled</span>';
            return '';
        }

        /* ---------- Detail item builder ---------- */
        function buildDetailItem(job, mode) {
            var jobCardNo = job.job_card_no || 'N/A';
            var date      = job.delivery_date || job.created_date || '';
            var vehicle   = job.vehicle_reg || '';
            var client    = job.client_name || job.customer_name || '';
            var status    = parseInt(job.status);
            var invoiced  = job.invoice_number;
            var invNum    = parseInt(invoiced);

            var html = '<div class="est-item">';
            html += '<div class="est-item-main">';
            html += '<span class="est-item-title">' + escapeHtml(jobCardNo) + '</span>';
            html += '<div class="est-item-meta">';
            if (mode === 'client') {
                html += '<span><i class="fa fa-car"></i> ' + escapeHtml(vehicle) + '</span>';
            } else {
                html += '<span><i class="fa fa-user-o"></i> ' + escapeHtml(client) + '</span>';
            }
            html += '<span><i class="fa fa-calendar"></i> ' + escapeHtml(date) + '</span>';
            html += '</div></div>';

            html += '<div class="est-item-badges">';
            html += workStatusBadge(status);
            html += invoiceStatusBadge(invoiced, status);
            if (status === 1) {
                html += '<span class="est-badge est-badge-delivered"><i class="fa fa-check-double"></i> Delivered</span>';
            }
            html += '</div>';

            html += '<div class="est-item-actions">';
            if (job.reference) {
                html += '<a href="' + baseUrl + 'job-cards/details/pdf/' + encodeURIComponent(job.reference) + '" target="_blank" class="est-act est-act-pdf"><i class="fa fa-file-pdf-o"></i> Job Sheet</a>';
                if (status === 1) {
                    html += '<a href="' + baseUrl + 'estimations/delivery-note/pdf/' + encodeURIComponent(job.reference) + '" target="_blank" class="est-act est-act-view"><i class="fa fa-truck"></i> Delivery</a>';
                    if (!(!isNaN(invNum) && invNum > 0) && job.job_card_no) {
                        html += '<button type="button" class="est-act est-act-create create-invoice-btn" data-job-card="' + escapeHtml(job.job_card_no) + '"><i class="fa fa-plus"></i> Create Invoice</button>';
                    }
                }
            }
            html += '</div></div>';
            return html;
        }

        function showClientHistory(clientName) {
            var mf = getMonthYearFilters();
            var matches = ALL_JOBS.filter(function (j) {
                return (j.client_name || j.customer_name) === clientName
                    && jobMatchesMonthYear(j, mf);
            });
            $('#detail_icon').removeClass().addClass('fa fa-list');
            $('#detail_title').text('Client History');
            $('#detail_subtitle').text(clientName + ' — ' + matches.length + ' job card(s)');
            var html = '';
            if (matches.length === 0) {
                html = '<div class="est-empty"><i class="fa fa-folder-open-o"></i><p>No job cards found for this client.</p></div>';
            } else {
                matches.forEach(function (j) { html += buildDetailItem(j, 'client'); });
            }
            $('#detail_body').html(html);
            $('#detail_panel').addClass('is-visible');
        }

        function showVehicleHistory(vehicleReg) {
            var mf = getMonthYearFilters();
            var matches = ALL_JOBS.filter(function (j) {
                return j.vehicle_reg === vehicleReg && jobMatchesMonthYear(j, mf);
            });
            $('#detail_icon').removeClass().addClass('fa fa-wrench');
            $('#detail_title').text('Vehicle History');
            $('#detail_subtitle').text(vehicleReg + ' — ' + matches.length + ' job card(s)');
            var html = '';
            if (matches.length === 0) {
                html = '<div class="est-empty"><i class="fa fa-wrench"></i><p>No job cards recorded for this vehicle.</p></div>';
            } else {
                matches.forEach(function (j) { html += buildDetailItem(j, 'vehicle'); });
            }
            $('#detail_body').html(html);
            $('#detail_panel').addClass('is-visible');
        }

        function applyFilter() {
            currentFilter = ($('#filter_value').val() || '').toString();
            var mf = getMonthYearFilters();

            $('#datatable tbody tr').each(function () {
                var $row = $(this);
                var rowValue = filterMode === 'client'
                    ? ($row.attr('data-client') || '')
                    : ($row.attr('data-vehicle') || '');
                var matches = (!currentFilter || rowValue === currentFilter)
                            && rowMatchesMonthYear($row, mf);
                $row.toggle(matches);
            });

            // Show Download PDF only when a client / vehicle filter is active
            if (currentFilter) $('#download_pdf_btn').addClass('is-visible');
            else               $('#download_pdf_btn').removeClass('is-visible');

            if (currentFilter) {
                if (filterMode === 'client') showClientHistory(currentFilter);
                else                         showVehicleHistory(currentFilter);
            } else {
                $('#detail_panel').removeClass('is-visible');
            }
        }

        function resetToAll() { applyFilter(); }

        /* ---- init (no period default → all months/years visible) ---- */
        rebuildFilterSelect();
        resetToAll();

        /* ---- mode toggle ---- */
        $(document).on('click', '.est-mode-btn', function () {
            var mode = $(this).data('mode');
            if (mode === filterMode) return;
            filterMode = mode;
            $('.est-mode-btn').removeClass('active').attr('aria-selected', 'false');
            $(this).addClass('active').attr('aria-selected', 'true');
            showSpinner();
            setTimeout(function () {
                rebuildFilterSelect();
                resetToAll();
                hideSpinner();
            }, 220);
        });

        /* ---- filter value change ---- */
        $('#filter_value').on('change', function () {
            showSpinner();
            setTimeout(function () {
                applyFilter();
                hideSpinner();
            }, 250);
        });

        $('#filter_value')
            .on('focus', function () { $('.est-filter-field').addClass('is-focused'); })
            .on('blur',  function () { $('.est-filter-field').removeClass('is-focused'); });

        $('#clear_filter').on('click', function () {
            if (!$('#filter_value').val()) return;
            $('#filter_value').val(null).trigger('change');
        });

        /* ---- month / year change ---- */
        $('#filter_month, #filter_year').on('change', function () {
            applyFilter();
        });

        $('#close_detail').on('click', function () {
            $('#detail_panel').removeClass('is-visible');
        });

        /* ============================================================
         * DOWNLOAD PDF — same UX as Estimations
         * ============================================================ */
        function extractFilename(disposition, fallback) {
            if (!disposition) return fallback;
            var match = disposition.match(/filename\*?=(?:UTF-8'')?["']?([^"';\r\n]+)/i);
            if (match && match[1]) {
                try { return decodeURIComponent(match[1]); }
                catch (e) { return match[1]; }
            }
            return fallback;
        }

        $(document).on('click', '#download_pdf_btn', function () {
            var value = ($('#filter_value').val() || '').toString();
            if (!value) return;

            var mf = getMonthYearFilters();
            var url = '{{ route("job-cards.export-pdf") }}'
                    + '?mode='   + encodeURIComponent(filterMode)
                    + '&value='  + encodeURIComponent(value)
                    + '&month='  + encodeURIComponent(mf.month || '')
                    + '&year='   + encodeURIComponent(mf.year  || '');

            $('#jobDownloadModal').modal('show');

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
                    return response.text().then(function (body) {
                        var msg = 'HTTP ' + response.status;
                        try {
                            var j = JSON.parse(body);
                            if (j.error)  msg += '\n' + j.error;
                            if (j.message && j.message !== j.error) msg += '\n' + j.message;
                            if (j.file)   msg += '\n@ ' + j.file + ':' + j.line;
                        } catch (e) {
                            msg += '\n' + (body || 'Empty response').substring(0, 400);
                        }
                        throw new Error(msg);
                    });
                }
                var filename = extractFilename(
                    response.headers.get('Content-Disposition'),
                    'job-cards-report.pdf'
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
                $('#jobDownloadModal').modal('hide');
            })
            .catch(function (err) {
                console.error('PDF download failed:', err);
                $('#jobDownloadModal').modal('hide');
                alert('PDF export failed:\n\n' + (err.message || err));
            });
        });
    });

    /* ============================================================
     * DELETE JOB CARD — SweetAlert confirmation
     * Critical action — controller writes a critical audit log.
     * ============================================================ */
    $(document).on('click', '.delete-job-card', function (e) {
        e.preventDefault();

        var $btn        = $(this);
        var url         = $btn.attr('href');
        var reference   = $btn.data('reference')   || '—';
        var jobCardNo   = $btn.data('job-card')    || '—';
        var client      = $btn.data('client')      || '—';
        var vehicle     = $btn.data('vehicle')     || '—';
        var hasInvoice  = $btn.data('has-invoice') === 1 || $btn.data('has-invoice') === '1';

        var invoiceWarning = hasInvoice
            ? '\n\n⚠ This Job Card has an Invoice attached. Deleting it will leave the invoice orphaned.'
            : '';

        swal({
            title: "Delete Job Card?",
            text: "You are about to permanently delete Job Card:\n\n" +
                  "• Job Card: " + jobCardNo + "\n" +
                  "• Reference: " + reference + "\n" +
                  "• Client: " + client + "\n" +
                  "• Vehicle: " + vehicle + "\n\n" +
                  "The related estimation will be reset (Job Card No → empty). " +
                  "This action is CRITICAL and will be recorded in the audit log." +
                  invoiceWarning,
            type: hasInvoice ? "error" : "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Delete",
            confirmButtonColor: "#d9534f",
            cancelButtonText: "Cancel",
            closeOnConfirm: false
        }, function (isConfirm) {
            if (!isConfirm) return;

            // Second confirmation for destructive critical action
            swal({
                title: "Are you absolutely sure?",
                text: "Once deleted, this Job Card and its snapshot data cannot be " +
                      "recovered. The deletion will be logged against your user ID.",
                type: "error",
                showCancelButton: true,
                confirmButtonText: "Yes, Permanently Delete",
                confirmButtonColor: "#d9534f",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function (finalConfirm) {
                if (finalConfirm) {
                    swal({
                        title: "Deleting…",
                        text: "Please wait",
                        type: "info",
                        showConfirmButton: false
                    });
                    window.location.href = url;
                } else {
                    swal("Cancelled", "The Job Card was not deleted.", "error");
                }
            });
        });
    });

    /* ============================================================
     * CREATE INVOICE — open modal + load data
     * Fires from both the table row button and the detail panel chip.
     * ============================================================ */
    $(document).on('click', '.create-invoice-btn', function (e) {
        e.preventDefault();

                var baseUrl = '{{ url('/') }}/';

        var jobCardNo = $(this).data('job-card');
        if (!jobCardNo) return;

        var $modal = $('#createInvoiceModal');
        var $form  = $('#createInvoiceForm');

        // Reset modal to loading state
        $form[0].reset();
        $('#ci_status').empty();
        $('#ci_submit_btn').prop('disabled', true);
        $('#ciForm').hide();
        $('#ciLoader').show();
        $modal.modal('show');

        $.ajax({
            url: baseUrl + 'invoices/create-invoice/data/job-card/' + encodeURIComponent(jobCardNo),
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (data) {
                $('#ci_reference').val(data.reference_id);
                $('#ci_client_id').val(data.client_id);
                $('#ci_estimate_ref').val(data.estimate_ref);
                $('#ci_vehicle_reg').val(data.vehicle_reg);
                $('#ci_account').val(data.account);
                $('#ci_make').val(data.make);
                $('#ci_job_card_no').val(data.job_card_no);
                $('#ci_bill_amount').val(data.amount);

                if ($('#ci_invoice_no').length) {
                    $('#ci_invoice_no').val(data.invoice_no);
                }

                $('#ci_status').html(data.status_badge || '');
                $('#ci_lpo_number').val(data.lpo_number || '');

                // Optional bank defaults (only if endpoint returns them)
                if (data.bank_account_no)   $('#ci_bank_account_no').val(data.bank_account_no);
                if (data.bank_account_name) $('#ci_bank_account_name').val(data.bank_account_name);
                if (data.bank_name)         $('#ci_bank_name').val(data.bank_name);
                if (data.branch_name)       $('#ci_branch_name').val(data.branch_name);
                if (data.swift_code)        $('#ci_swift_code').val(data.swift_code);

                $('#ciLoader').hide();
                $('#ciForm').show();
                $('#ci_submit_btn').prop('disabled', false);
            },
            error: function (xhr) {
                var msg = 'Failed to load invoice data.';
                try {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg += '\n' + xhr.responseJSON.message;
                    }
                } catch (e) {}
                $('#ciLoader').hide();
                $modal.modal('hide');
                alert(msg);
            }
        });
    });
</script>

@endsection
@extends('layouts.app_header')
@section('content')

<style>
    /* ============================================================
       INVOICES SUMMARY — Advanced Professional UI
       ============================================================ */
    :root {
        /* Depth system */
        --d1: 0 1px 2px rgba(15,23,42,.04);
        --d2: 0 4px 14px rgba(15,23,42,.06), 0 1px 3px rgba(15,23,42,.04);
        --d3: 0 18px 40px -12px rgba(15,23,42,.14), 0 6px 14px -6px rgba(15,23,42,.06);
        --d-glow: 0 0 0 4px rgba(30,58,95,.08);
        --d-inset: inset 0 1px 0 rgba(255,255,255,.65);

        /* Neutral scale */
        --n-950: #0b1220;
        --n-900: #0f172a;
        --n-800: #1e293b;
        --n-700: #334155;
        --n-600: #475569;
        --n-500: #64748b;
        --n-400: #94a3b8;
        --n-300: #cbd5e1;
        --n-200: #e2e8f0;
        --n-100: #f1f5f9;
        --n-50:  #f8fafc;
        --white: #ffffff;

        /* Brand + accents */
        --brand-900: #142a45;
        --brand-800: #1e3a5f;
        --brand-700: #2d5282;
        --brand-600: #3b6ba5;
        --brand-glow: rgba(45,82,130,.15);

        --emerald: #059669;
        --emerald-dark: #047857;
        --emerald-soft: #ecfdf5;
        --emerald-line: #a7f3d0;

        --amber: #d97706;
        --amber-dark: #b45309;
        --amber-soft: #fffbeb;
        --amber-line: #fde68a;

        --rose: #e11d48;
        --rose-dark: #be123c;
        --rose-soft: #fff1f2;
        --rose-line: #fecdd3;

        --cyan: #0891b2;
        --cyan-dark: #0e7490;
        --cyan-soft: #ecfeff;
        --cyan-line: #a5f3fc;

        /* Motion */
        --ease-out: cubic-bezier(.22,.61,.36,1);
        --ease-spring: cubic-bezier(.34,1.56,.64,1);
    }

    .inv * { box-sizing: border-box; }

    /* ---------- Root panel with mesh gradient ---------- */
    .inv-panel {
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
    .inv-panel::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 18px;
        padding: 1px;
        background: linear-gradient(135deg, rgba(255,255,255,.9), rgba(226,232,240,.6), rgba(255,255,255,.9));
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
        -webkit-mask-composite: xor;
                mask-composite: exclude;
        pointer-events: none;
        opacity: .7;
    }

    /* ---------- Step indicator ---------- */
    .inv-steps {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .inv-steps a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .02em;
        padding: 8px 15px;
        border-radius: 10px;
        background: var(--white);
        color: var(--n-500);
        text-decoration: none;
        border: 1px solid var(--n-200);
        box-shadow: var(--d1);
        transition: all .22s var(--ease-out);
    }
    .inv-steps a:hover {
        background: var(--n-50);
        color: var(--n-900);
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: var(--d2);
    }
    .inv-steps a.active {
        background: linear-gradient(180deg, var(--brand-700), var(--brand-800));
        color: #fff;
        border-color: var(--brand-800);
        box-shadow: 0 4px 14px -2px rgba(30,58,95,.35), var(--d-inset);
    }
    .inv-steps .inv-sep { color: var(--n-300); font-size: 10px; }

    /* ---------- Header ---------- */
    .inv-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 24px;
        padding: 28px 32px 24px;
        border-bottom: 1px solid var(--n-200);
        position: relative;
    }
    .inv-head::after {
        content: "";
        position: absolute;
        left: 32px;
        right: 32px;
        bottom: -1px;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--n-200), transparent);
    }
    .inv-head-text { min-width: 0; }
    .inv-head-text h3 {
        margin: 0 0 8px;
        font-size: 22px;
        font-weight: 700;
        color: var(--n-900);
        letter-spacing: -.4px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .inv-head-text h3 .inv-head-icon {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: linear-gradient(135deg, var(--brand-700), var(--brand-800));
        color: #fff;
        font-size: 15px;
        box-shadow: 0 8px 20px -6px rgba(30,58,95,.5), var(--d-inset);
    }
    .inv-head-text p {
        margin: 0;
        font-size: 13px;
        color: var(--n-500);
        line-height: 1.55;
        max-width: 560px;
    }

    /* ---------- Primary button ---------- */
    .inv-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 11px 20px;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .01em;
        cursor: pointer;
        color: #fff;
        background: linear-gradient(180deg, var(--brand-700) 0%, var(--brand-800) 100%);
        box-shadow: 0 8px 20px -6px rgba(30,58,95,.5), var(--d-inset);
        transition: all .22s var(--ease-out);
        position: relative;
        overflow: hidden;
    }
    .inv-btn-primary::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(255,255,255,.10), transparent 60%);
        opacity: 0;
        transition: opacity .22s var(--ease-out);
        pointer-events: none;
    }
    .inv-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px -8px rgba(30,58,95,.55), var(--d-inset);
    }
    .inv-btn-primary:hover::after { opacity: 1; }
    .inv-btn-primary:active { transform: translateY(0); }
    .inv-btn-primary i { font-size: 12px; }

    /* ---------- KPI strip ---------- */
    .inv-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        padding: 26px 32px 10px;
    }
    @media (max-width: 900px) { .inv-summary { grid-template-columns: 1fr; } }

    .inv-kpi {
        position: relative;
        border-radius: 16px;
        padding: 20px 22px 18px;
        background:
            radial-gradient(300px 120px at 100% 0%, rgba(45,82,130,.06), transparent 60%),
            var(--white);
        border: 1px solid var(--n-200);
        box-shadow: var(--d2);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        overflow: hidden;
        transition: transform .25s var(--ease-out), box-shadow .25s var(--ease-out), border-color .25s var(--ease-out);
    }
    .inv-kpi::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--brand-600), var(--brand-800), var(--brand-600));
        background-size: 200% 100%;
        animation: kpi-shimmer 8s ease-in-out infinite;
    }
    .inv-kpi.kpi-amount::before {
        background: linear-gradient(90deg, #34d399, var(--emerald), #34d399);
        background-size: 200% 100%;
    }
    .inv-kpi.kpi-showing::before {
        background: linear-gradient(90deg, #67e8f9, var(--cyan), #67e8f9);
        background-size: 200% 100%;
    }
    @keyframes kpi-shimmer {
        0%, 100% { background-position: 0% 50%; }
        50%      { background-position: 100% 50%; }
    }
    .inv-kpi:hover {
        border-color: var(--n-300);
        transform: translateY(-3px);
        box-shadow: var(--d3);
    }

    .inv-kpi-body { min-width: 0; flex: 1; }
    .inv-kpi-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--n-500);
        margin: 0 0 10px;
    }
    .inv-kpi-value {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', monospace;
        font-size: 24px;
        font-weight: 700;
        color: var(--n-900);
        line-height: 1.05;
        letter-spacing: -.03em;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .inv-kpi.kpi-amount .inv-kpi-value { color: var(--emerald-dark); }
    .inv-kpi.kpi-showing .inv-kpi-value { color: var(--cyan-dark); }

    .inv-kpi-sub {
        margin-top: 8px;
        font-size: 11.5px;
        color: var(--n-500);
        line-height: 1.4;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .inv-kpi-sub .inv-pulse-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--cyan);
        box-shadow: 0 0 0 0 rgba(8,145,178,.6);
        animation: inv-pulse 2s infinite;
    }
    @keyframes inv-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(8,145,178,.55); }
        70%  { box-shadow: 0 0 0 8px rgba(8,145,178,0); }
        100% { box-shadow: 0 0 0 0 rgba(8,145,178,0); }
    }

    .inv-kpi-icon {
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background:
            radial-gradient(circle at 30% 25%, rgba(255,255,255,.9), transparent 55%),
            linear-gradient(135deg, var(--n-100), var(--n-200));
        color: var(--brand-800);
        font-size: 17px;
        box-shadow: var(--d-inset), 0 3px 8px rgba(15,23,42,.05);
    }
    .inv-kpi.kpi-amount .inv-kpi-icon {
        background:
            radial-gradient(circle at 30% 25%, rgba(255,255,255,.9), transparent 55%),
            linear-gradient(135deg, #d1fae5, var(--emerald-line));
        color: var(--emerald-dark);
    }
    .inv-kpi.kpi-showing .inv-kpi-icon {
        background:
            radial-gradient(circle at 30% 25%, rgba(255,255,255,.9), transparent 55%),
            linear-gradient(135deg, #cffafe, var(--cyan-line));
        color: var(--cyan-dark);
    }

    /* ---------- Filter bar ---------- */
    .inv-filters {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding: 22px 32px 20px;
    }
    .inv-filters-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--n-500);
        margin-right: 4px;
    }

    .inv-filter-field {
        position: relative;
        flex: 1 1 320px;
        min-width: 260px;
        max-width: 420px;
    }
    .inv-filter-field > .inv-filter-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--n-400);
        font-size: 13px;
        pointer-events: none;
        z-index: 5;
        transition: color .2s var(--ease-out);
    }
    .inv-filter-field.is-focused > .inv-filter-icon {
        color: var(--brand-700);
    }

    .inv-filter-field select.inv-select2-native {
        position: absolute;
        opacity: 0;
        pointer-events: none;
        width: 100%;
    }

    /* Select2 — single select */
    .inv-filter-field .select2-container { width: 100% !important; }
    .inv-filter-field .select2-container--default .select2-selection--single {
        height: 44px;
        background: var(--white);
        border: 1px solid var(--n-200);
        border-radius: 10px;
        box-shadow: var(--d1);
        transition: all .22s var(--ease-out);
        display: flex;
        align-items: center;
        padding-left: 40px;
        padding-right: 38px;
        position: relative;
    }
    .inv-filter-field .select2-container--default .select2-selection--single:hover {
        border-color: var(--n-300);
    }
    .inv-filter-field .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding: 0;
        color: var(--n-900);
        font-size: 13.5px;
        font-weight: 500;
        line-height: 44px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .inv-filter-field .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: var(--n-500);
        font-weight: 500;
    }
    .inv-filter-field .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
        width: 38px;
        right: 0;
        top: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .inv-filter-field .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: var(--n-400) transparent transparent transparent;
        border-width: 5px 4px 0 4px;
        margin-left: 0;
        transition: all .22s var(--ease-out);
    }
    .inv-filter-field .select2-container--default .select2-selection--single .select2-selection__clear {
        position: absolute;
        right: 38px;
        top: 50%;
        transform: translateY(-50%);
        margin: 0;
        padding: 0;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--n-400);
        font-size: 14px;
        font-weight: 400;
        transition: color .15s var(--ease-out);
    }
    .inv-filter-field .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        color: var(--rose);
    }

    .inv-filter-field .select2-container--default.select2-container--focus .select2-selection--single,
    .inv-filter-field .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--brand-700);
        box-shadow: var(--d-glow), var(--d1);
    }
    .inv-filter-field .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent var(--brand-700) transparent;
        border-width: 0 4px 5px 4px;
    }

    .inv-filter-field .select2-container--default .select2-dropdown {
        border: 1px solid var(--n-200);
        border-radius: 10px;
        box-shadow: var(--d3);
        overflow: hidden;
        margin-top: 6px;
        background: var(--white);
    }
    .inv-filter-field .select2-container--default .select2-search--dropdown {
        padding: 8px;
        border-bottom: 1px solid var(--n-100);
        background: var(--n-50);
    }
    .inv-filter-field .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid var(--n-200);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        outline: none;
        transition: all .2s var(--ease-out);
        background: var(--white);
    }
    .inv-filter-field .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: var(--brand-700);
        box-shadow: var(--d-glow);
    }
    .inv-filter-field .select2-container--default .select2-results > .select2-results__options {
        max-height: 280px;
        padding: 4px 0;
    }
    .inv-filter-field .select2-container--default .select2-results__option {
        padding: 10px 16px;
        font-size: 13px;
        color: var(--n-700);
        transition: background .15s var(--ease-out);
    }
    .inv-filter-field .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: var(--brand-800);
        color: #fff;
    }
    .inv-filter-field .select2-container--default .select2-results__option[aria-selected="true"] {
        background: var(--n-100);
        color: var(--n-900);
        font-weight: 600;
    }
    .inv-filter-field .select2-container--default .select2-results__option[aria-selected="true"].select2-results__option--highlighted {
        background: var(--brand-800);
        color: #fff;
    }

    .inv-btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        color: var(--n-700);
        background: var(--white);
        border: 1px solid var(--n-200);
        box-shadow: var(--d1);
        transition: all .2s var(--ease-out);
    }
    .inv-btn-secondary:hover {
        background: var(--n-50);
        border-color: var(--n-300);
        color: var(--n-900);
        transform: translateY(-1px);
        box-shadow: var(--d2);
    }
    .inv-btn-secondary:active { transform: translateY(0); }

    .inv-results-count {
        margin-left: auto;
        font-size: 12.5px;
        color: var(--n-500);
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: var(--n-50);
        border: 1px solid var(--n-200);
        border-radius: 10px;
        white-space: nowrap;
    }
    .inv-results-count strong {
        color: var(--n-900);
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }
    .inv-results-count .inv-count-divider {
        color: var(--n-300);
    }

    /* ---------- Table wrapper ---------- */
    .inv-table-outer {
        position: relative;
        padding: 0 32px 24px;
    }

    .inv-table-loading {
        position: absolute;
        top: 0; left: 32px; right: 32px; bottom: 24px;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,.78);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 20;
        border-radius: 12px;
        opacity: 0;
        transition: opacity .2s var(--ease-out);
    }
    .inv-table-loading.show { display: flex; opacity: 1; }
    .inv-spinner {
        width: 42px;
        height: 42px;
        border: 3px solid var(--n-200);
        border-top-color: var(--brand-700);
        border-radius: 50%;
        animation: inv-spin .85s linear infinite;
        box-shadow: 0 6px 20px -6px rgba(30,58,95,.3);
    }
    @keyframes inv-spin { to { transform: rotate(360deg); } }

    /* ---------- Table ---------- */
    .inv-wrap-table { overflow-x: auto; border-radius: 14px; }
    .inv-wrap #datatable {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
        font-size: 13.5px;
        background: var(--white);
        border: 1px solid var(--n-200);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: var(--d1);
    }
    .inv-wrap #datatable thead th {
        background: linear-gradient(180deg, var(--brand-700) 0%, var(--brand-800) 100%);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .09em;
        padding: 16px 16px;
        text-align: left;
        border: none;
        white-space: nowrap;
        vertical-align: middle;
        position: relative;
    }
    .inv-wrap #datatable thead th + th::before {
        content: "";
        position: absolute;
        left: 0;
        top: 26%; bottom: 26%;
        width: 1px;
        background: rgba(255,255,255,.18);
    }

    /* Prevent any word from wrapping inside table cells */
    .inv-wrap #datatable tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--n-100);
        vertical-align: middle;
        color: var(--n-700);
        background: var(--white);
        transition: background .18s var(--ease-out);
        position: relative;
        white-space: nowrap;
    }
    .inv-wrap #datatable tbody tr:nth-child(even) td { background: #fcfdfe; }
    .inv-wrap #datatable tbody tr:hover td { background: #f1f5f9; }
    .inv-wrap #datatable tbody tr:hover td:first-child::before { opacity: 1; }

    .inv-wrap #datatable tbody td:first-child { position: relative; }
    .inv-wrap #datatable tbody td:first-child::before {
        content: "";
        position: absolute;
        left: 0;
        top: 12%;
        bottom: 12%;
        width: 3px;
        border-radius: 0 3px 3px 0;
        background: linear-gradient(180deg, var(--brand-600), var(--brand-800));
        opacity: 0;
        transition: opacity .2s var(--ease-out);
    }
    .inv-wrap #datatable tbody tr:last-child td { border-bottom: none; }

    .inv-wrap #datatable tbody td.num {
        font-family: 'SFMono-Regular', Consolas, monospace;
        font-size: 12.5px;
        text-align: right;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }
    .inv-wrap #datatable tbody td.center { text-align: center; }

    /* Row cells */
    .inv-amount {
        font-weight: 700;
        color: var(--n-900);
        font-family: 'SFMono-Regular', Consolas, monospace;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .inv-client-name {
        font-weight: 600;
        color: var(--n-900);
        display: block;
        line-height: 1.35;
        white-space: nowrap;
    }
    .inv-client-place {
        color: var(--n-500);
        font-size: 11.5px;
        display: block;
        margin-top: 2px;
        letter-spacing: .02em;
        white-space: nowrap;
    }
    .inv-vehicle {
        color: var(--n-800);
        font-weight: 600;
        letter-spacing: .02em;
        white-space: nowrap;
    }
    .inv-vehicle-model {
        display: inline-block;
        margin-left: 6px;
        padding: 2px 8px;
        font-size: 10.5px;
        font-weight: 600;
        color: var(--n-600);
        background: var(--n-100);
        border-radius: 5px;
        letter-spacing: .03em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .inv-invoice-no {
        font-family: 'SFMono-Regular', Consolas, monospace;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--brand-800);
        padding: 3px 9px;
        background: linear-gradient(180deg, #eff6ff, #dbeafe);
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        display: inline-block;
        letter-spacing: .02em;
        white-space: nowrap;
    }
    .inv-created-by {
        font-size: 12px;
        color: var(--n-700);
        font-weight: 500;
        letter-spacing: .01em;
        white-space: nowrap;
    }
    .inv-date {
        font-size: 12.5px;
        color: var(--n-600);
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    /* ============================================================
       ACTION BUTTONS — individual colored pills (per-role colors)
       ============================================================ */
    .inv-wrap #datatable tbody td:last-child {
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .inv-actions {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        flex-wrap: nowrap;
        white-space: nowrap;
        vertical-align: middle;
    }

    .inv-act {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 11px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .01em;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid transparent;
        transition: transform .16s var(--ease-out),
                    box-shadow .16s var(--ease-out),
                    background .16s var(--ease-out),
                    border-color .16s var(--ease-out);
        white-space: nowrap;
        line-height: 1;
        position: relative;
        box-shadow: 0 1px 2px rgba(15,23,42,.04);
    }
    .inv-act i { font-size: 11px; }

    .inv-act:hover {
        text-decoration: none;
        transform: translateY(-1px);
    }
    .inv-act:active { transform: translateY(0); }

    /* View PDF — Rose */
    .inv-act-view {
        background: linear-gradient(180deg, #fff1f2, #ffe4e6);
        color: var(--rose-dark);
        border-color: var(--rose-line);
    }
    .inv-act-view:hover {
        background: linear-gradient(180deg, #ffe4e6, #fecdd3);
        color: var(--rose-dark);
        border-color: var(--rose);
        box-shadow: 0 4px 12px -2px rgba(225,29,72,.28);
    }

    /* Edit — Cyan */
    .inv-act-edit {
        background: linear-gradient(180deg, #ecfeff, #cffafe);
        color: var(--cyan-dark);
        border-color: var(--cyan-line);
    }
    .inv-act-edit:hover {
        background: linear-gradient(180deg, #cffafe, #a5f3fc);
        color: var(--cyan-dark);
        border-color: var(--cyan);
        box-shadow: 0 4px 12px -2px rgba(8,145,178,.28);
    }

    /* Send — Emerald */
    .inv-act-send {
        background: linear-gradient(180deg, #ecfdf5, #d1fae5);
        color: var(--emerald-dark);
        border-color: var(--emerald-line);
    }
    .inv-act-send:hover {
        background: linear-gradient(180deg, #d1fae5, #a7f3d0);
        color: var(--emerald-dark);
        border-color: var(--emerald);
        box-shadow: 0 4px 12px -2px rgba(5,150,105,.28);
    }

    /* Delete — Rose (danger) */
    .inv-act-delete {
        background: linear-gradient(180deg, #fff1f2, #ffe4e6);
        color: var(--rose);
        border-color: var(--rose-line);
    }
    .inv-act-delete:hover {
        background: linear-gradient(180deg, #ffe4e6, #fecdd3);
        color: var(--rose-dark);
        border-color: var(--rose);
        box-shadow: 0 4px 12px -2px rgba(225,29,72,.28);
    }

    /* Create — Brand navy (primary action) */
    .inv-act-create {
        background: linear-gradient(180deg, var(--brand-700), var(--brand-800));
        color: #fff;
        border-color: var(--brand-800);
        padding: 7px 13px;
        box-shadow: 0 2px 8px -1px rgba(30,58,95,.35), inset 0 1px 0 rgba(255,255,255,.15);
    }
    .inv-act-create:hover {
        color: #fff;
        background: linear-gradient(180deg, var(--brand-800), var(--brand-900));
        transform: translateY(-1px);
        box-shadow: 0 5px 14px -3px rgba(30,58,95,.5), inset 0 1px 0 rgba(255,255,255,.15);
    }

    /* ============================================================
       EDIT INVOICE MODAL — lock / unlock styles
       ============================================================ */
    #edit_modal .lock-badge {
        font-size: 11px;
        padding: 3px 9px;
        margin-left: 8px;
        border-radius: 10px;
        vertical-align: middle;
    }
    #edit_modal .proforma-warning {
        border: 2px solid #f0ad4e !important;
        background-color: #fff8e8 !important;
    }
    #edit_modal .proforma-locked-hint {
        font-size: 11px;
        color: #a94442;
        margin-top: 4px;
        display: block;
    }
    #edit_modal .proforma-unlock-box {
        background: #fff3cd;
        border-left: 4px solid #f0ad4e;
        padding: 10px 15px;
        border-radius: 4px;
        margin-top: 10px;
        display: none;
    }
    #edit_modal .proforma-unlock-box textarea {
        border: 1px solid #f0ad4e;
    }
    #edit_modal .form-control[readonly] {
        background-color: #f7f7f7;
        cursor: not-allowed;
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 991px) {
        .inv-head { padding: 24px 24px 20px; }
        .inv-head-text h3 { font-size: 20px; }
        .inv-summary { padding: 22px 24px 8px; gap: 14px; }
        .inv-kpi { padding: 18px 18px 16px; }
        .inv-kpi-value { font-size: 21px; }
        .inv-filters { padding: 18px 24px 16px; }
        .inv-table-outer { padding: 0 24px 20px; }
        .inv-table-loading { left: 24px; right: 24px; }
    }

    @media (max-width: 767px) {
        .inv-head {
            flex-direction: column;
            align-items: stretch;
            padding: 20px;
        }
        .inv-head-text h3 { font-size: 18px; }
        .inv-head-text h3 .inv-head-icon { width: 36px; height: 36px; font-size: 14px; }
        .inv-btn-primary { width: 100%; justify-content: center; }

        .inv-summary {
            grid-template-columns: 1fr;
            padding: 18px 20px 4px;
            gap: 12px;
        }
        .inv-kpi { padding: 16px 18px; }
        .inv-kpi-icon { width: 42px; height: 42px; }
        .inv-kpi-value { font-size: 20px; }

        .inv-filters {
            padding: 16px 20px 12px;
            gap: 10px;
        }
        .inv-filter-field { flex: 1 1 100%; max-width: 100%; min-width: 0; }
        .inv-btn-secondary { width: 100%; justify-content: center; }
        .inv-results-count { margin-left: 0; width: 100%; justify-content: center; }

        .inv-table-outer { padding: 0 20px 20px; }
        .inv-table-loading { left: 20px; right: 20px; bottom: 20px; }

        .inv-wrap #datatable thead th { padding: 13px 12px; font-size: 10px; }
        .inv-wrap #datatable tbody td { padding: 12px 12px; font-size: 12.5px; }

        /* Actions wrap on small screens */
        .inv-actions { flex-wrap: wrap; gap: 4px; }
        .inv-act { padding: 6px 9px; font-size: 11px; }
    }

    /* ---------- Reduced motion ---------- */
    @media (prefers-reduced-motion: reduce) {
        .inv-kpi::before,
        .inv-kpi-sub .inv-pulse-dot { animation: none; }
        * { transition-duration: .01ms !important; }
    }
    

    /* MODAL */
    #edit_modal .ei-modal-dialog {
        margin-top: 35px;
    }

    #edit_modal .ei-modal-content {
        border: none;
        border-radius: 10px;
        overflow: visible;
        background: #fff;
        box-shadow: 0 12px 45px rgba(0,0,0,.12);
    }

    /* HEADER */
    #edit_modal .ei-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 22px 27px;
        background: #f8f9fb;
        border-bottom: 1px solid #e9ecef;
        border-radius: 10px 10px 0 0;
    }

    #edit_modal .ei-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    #edit_modal .ei-header-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e9eef3;
        border-radius: 9px;
        color: #34495e;
        font-size: 21px;
    }

    #edit_modal .ei-header h4 {
        margin: 0 0 5px;
        font-size: 19px;
        font-weight: 700;
        color: #293746;
    }

    #edit_modal .ei-header p {
        margin: 0;
        font-size: 12px;
        color: #89939e;
    }

    #edit_modal .ei-close {
        background: transparent;
        border: none;
        font-size: 28px;
        color: #89939e;
        cursor: pointer;
        line-height: 1;
    }

    #edit_modal .ei-close:hover {
        color: #344252;
    }

    /* BODY */
    #edit_modal .ei-body {
        padding: 25px 27px;
        background: #fff;
    }

    /* SECTION */
    #edit_modal .ei-section {
        margin-bottom: 25px;
    }

    #edit_modal .ei-section-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 15px;
        margin-bottom: 19px;
        border-bottom: 1px solid #edf0f2;
    }

    #edit_modal .ei-section-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f2f4;
        border-radius: 7px;
        color: #465563;
        font-size: 15px;
    }

    #edit_modal .ei-section-heading h5 {
        margin: 0 0 4px;
        font-size: 14px;
        font-weight: 700;
        color: #344252;
    }

    #edit_modal .ei-section-heading small {
        color: #89939e;
        font-size: 11px;
    }

    /* INPUTS */
    #edit_modal .ei-form-group {
        margin-bottom: 20px;
    }

    #edit_modal .ei-form-group label {
        display: block;
        margin-bottom: 8px;
        color: #495563;
        font-size: 12px;
        font-weight: 600;
    }

    #edit_modal .ei-input {
        height: 41px;
        padding: 9px 12px;
        border: 1px solid #dfe4e8;
        border-radius: 6px;
        box-shadow: none;
        font-size: 13px;
        color: #344252;
        transition: all .2s ease;
    }

    #edit_modal textarea.ei-input {
        height: auto;
        min-height: 80px;
        resize: vertical;
    }

    #edit_modal .ei-input:focus {
        border-color: #8c9ba9;
        box-shadow: 0 0 0 2px rgba(100,120,140,.08);
    }

    #edit_modal .ei-input[readonly] {
        background: #f5f6f7;
        color: #66717d;
        cursor: not-allowed;
    }

    #edit_modal .ei-input::placeholder {
        color: #adb5bd;
        font-size: 12px;
    }

    #edit_modal .ei-required {
        color: #b94a48;
    }

    #edit_modal .ei-optional {
        color: #929ca6;
        font-size: 10px;
        font-weight: 400;
        margin-left: 5px;
    }

    /* SELECT2 */
    #edit_modal .select2-container {
        width: 100% !important;
    }

    #edit_modal .select2-container .select2-selection--single {
        height: 41px !important;
        border: 1px solid #dfe4e8 !important;
        border-radius: 6px !important;
        display: flex;
        align-items: center;
    }

    #edit_modal .select2-container--default
    .select2-selection--single .select2-selection__rendered {
        line-height: 39px !important;
        padding-left: 12px;
        font-size: 13px;
        color: #344252;
    }

    #edit_modal .select2-container--default
    .select2-selection--single .select2-selection__arrow {
        height: 39px !important;
    }

    /* INPUT GROUP */
    #edit_modal .input-group .ei-input {
        border-radius: 6px 0 0 6px;
    }

    #edit_modal .ei-unlock-btn {
        height: 41px;
        min-width: 43px;
        background: #f1f2f4;
        border: 1px solid #dfe4e8;
        border-left: none;
        border-radius: 0 6px 6px 0;
        color: #566575;
    }

    #edit_modal .ei-unlock-btn:hover {
        background: #e6e9ec;
    }

    #edit_modal .ei-addon {
        background: #f4f6f8;
        border: 1px solid #dfe4e8;
        border-left: none;
        border-radius: 0 6px 6px 0;
        color: #647383;
    }

    #edit_modal .ei-hint {
        display: block;
        margin-top: 7px;
        font-size: 10px;
        line-height: 1.5;
        color: #89939e;
    }

    #edit_modal .lock-badge {
        margin-left: 5px;
        padding: 3px 6px;
        background: #edf0f2;
        color: #586575;
        border: 1px solid #dce1e5;
        border-radius: 3px;
        font-size: 9px;
        vertical-align: middle;
    }

    /* REASON BOX */
    #edit_modal .ei-reason-box {
        padding: 18px;
        background: #fcf9f9;
        border: 1px solid #eadede;
        border-radius: 8px;
        margin-top: 5px;
    }

    #edit_modal .ei-reason-heading {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 17px;
    }

    #edit_modal .ei-warning-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f4eaea;
        border-radius: 7px;
        color: #a65c5c;
        font-size: 15px;
    }

    #edit_modal .ei-reason-heading h5 {
        margin: 0 0 4px;
        font-size: 13px;
        font-weight: 700;
        color: #744848;
    }

    #edit_modal .ei-reason-heading small {
        color: #987979;
        font-size: 11px;
    }

    #edit_modal .ei-audit-note {
        padding-top: 10px;
        border-top: 1px solid #eadede;
        font-size: 11px;
        color: #8b6d6d;
    }

    /* FOOTER */
    #edit_modal .ei-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 17px 27px;
        background: #f8f9fb;
        border-top: 1px solid #e9ecef;
        border-radius: 0 0 10px 10px;
    }

    #edit_modal .ei-footer-note {
        font-size: 11px;
        color: #89939e;
    }

    #edit_modal .ei-footer-note i {
        margin-right: 5px;
    }

    #edit_modal .ei-footer-actions {
        display: flex;
        gap: 9px;
    }

    #edit_modal .ei-footer .btn {
        height: 39px;
        padding: 0 19px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        transition: all .2s ease;
    }

    #edit_modal .ei-cancel-btn {
        background: #fff;
        border: 1px solid #dce1e5;
        color: #596573;
    }

    #edit_modal .ei-cancel-btn:hover {
        background: #f0f2f4;
    }

    #edit_modal .ei-update-btn {
        background: #34495e;
        border: 1px solid #34495e;
        color: #fff;
    }

    #edit_modal .ei-update-btn:hover {
        background: #293b4d;
        border-color: #293b4d;
    }

    /* RESPONSIVE */
    @media(max-width: 767px) {

        #edit_modal .ei-modal-dialog {
            margin: 10px;
        }

        #edit_modal .ei-header {
            padding: 17px;
        }

        #edit_modal .ei-header h4 {
            font-size: 16px;
        }

        #edit_modal .ei-header p {
            font-size: 11px;
        }

        #edit_modal .ei-body {
            padding: 20px 16px;
        }

        #edit_modal .ei-footer {
            padding: 15px 16px;
            flex-direction: column;
            align-items: stretch;
        }

        #edit_modal .ei-footer-note {
            text-align: center;
        }

        #edit_modal .ei-footer-actions {
            width: 100%;
        }

        #edit_modal .ei-footer-actions .btn {
            flex: 1;
        }
    }

</style>

<div class="inv">

    {{-- ===== Step indicator ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="inv-steps">
                <a href="{{ url('home') }}"><i class="fa fa-home"></i> Home</a>
                <span class="inv-sep"><i class="fa fa-angle-right"></i></span>
                <a href="#" class="active">Invoices Summary</a>
            </div>
        </div>
    </div>

    {{-- ===== Main panel ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="inv-panel">

                {{-- Header --}}
                <div class="inv-head">
                    <div class="inv-head-text">
                        <h3>
                            <span class="inv-head-icon"><i class="fa fa-file-text-o"></i></span>
                            All Invoices
                        </h3>
                        <p>Browse, filter and manage every client invoice across this branch.</p>
                    </div>
                    <div>
                        <button type="button" class="inv-btn-primary" id="btn-create-invoice">
                            <i class="fa fa-plus"></i> Create Invoice
                        </button>
                    </div>
                </div>

                @php
                    $totalInvoices = $sales->count();
                    $totalAmount   = (float) $sales->sum('grand_total_amount');
                @endphp

                {{-- KPI strip --}}
                <div class="inv-summary">
                    <div class="inv-kpi">
                        <div class="inv-kpi-body">
                            <p class="inv-kpi-label">Total Invoices</p>
                            <div class="inv-kpi-value" id="kpi-total-invoices">{{ $totalInvoices }}</div>
                            <div class="inv-kpi-sub">
                                <i class="fa fa-layer-group" style="color:var(--n-400);font-size:10px;"></i>
                                across all clients
                            </div>
                        </div>
                        <div class="inv-kpi-icon"><i class="fa fa-file-text-o"></i></div>
                    </div>

                    <div class="inv-kpi kpi-amount">
                        <div class="inv-kpi-body">
                            <p class="inv-kpi-label">Total Amount</p>
                            <div class="inv-kpi-value" id="kpi-total-amount">{{ number_format($totalAmount, 2) }}</div>
                            <div class="inv-kpi-sub">
                                <i class="fa fa-arrow-up" style="color:var(--emerald);font-size:10px;"></i>
                                gross invoiced value
                            </div>
                        </div>
                        <div class="inv-kpi-icon"><i class="fa fa-money"></i></div>
                    </div>

                    <div class="inv-kpi kpi-showing">
                        <div class="inv-kpi-body">
                            <p class="inv-kpi-label">Showing</p>
                            <div class="inv-kpi-value" id="kpi-showing">{{ $totalInvoices }}</div>
                            <div class="inv-kpi-sub">
                                <span class="inv-pulse-dot"></span>
                                live filtered results
                            </div>
                        </div>
                        <div class="inv-kpi-icon"><i class="fa fa-filter"></i></div>
                    </div>
                </div>

                {{-- Filter bar --}}
                <div class="inv-filters">
                    <span class="inv-filters-label">Filter</span>

                    <div class="inv-filter-field">
                        <i class="fa fa-user-o inv-filter-icon"></i>
                        <select id="client_filter" class="inv-select2-native">
                            <option value="">All Clients</option>
                            @foreach($clients as $client)
                                <option value="{{ strtoupper($client->client_name) }}">
                                    {{ $client->client_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" class="inv-btn-secondary" id="clear_filter">
                        <i class="fa fa-times"></i> Clear
                    </button>

                    <span class="inv-results-count" id="results_count">
                        Showing <strong>{{ $totalInvoices }}</strong>
                        <span class="inv-count-divider">/</span>
                        <strong>{{ $totalInvoices }}</strong>
                    </span>
                </div>

                {{-- Table --}}
                <div class="inv-table-outer">
                    <div class="inv-table-loading" id="invTableLoading">
                        <div class="inv-spinner"></div>
                    </div>

                    <div class="inv-wrap-table">
                        <table id="datatable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="15px">#</th>
                                    <th>Client Name</th>
                                    <th>Vehicle Reg#</th>
                                    <th>Invoice No#</th>
                                    <th>Invoice Amount</th>
                                    <th>Date</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $key => $product)
                                    @php
                                        $clientFilterValue = strtoupper($product->client_name ?: $product->customer_name);
                                    @endphp
                                    <tr data-client="{{ $clientFilterValue }}">
                                        <td class="center" width="30">{{ $key + 1 }}</td>
                                        <td>
                                            @if($product->client_name != '')
                                                <span class="inv-client-name">{{ strtoupper($product->client_name) }}</span>
                                                <span class="inv-client-place">{{ strtoupper($product->place) }}</span>
                                            @else
                                                <span class="inv-client-name">{{ $product->customer_name }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="inv-vehicle">{{ strtoupper($product->vehicle_reg) }}</span>
                                            @if(!empty($product->model))
                                                <span class="inv-client-place">{{ strtoupper($product->model) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="inv-invoice-no">
                                                @if ($product->invoice_number < 10)
                                                    {{ '00000'.$product->invoice_number }}
                                                @elseif ($product->invoice_number < 100)
                                                    {{ '0000'.$product->invoice_number }}
                                                @elseif ($product->invoice_number < 1000)
                                                    {{ '000'.$product->invoice_number }}
                                                @elseif ($product->invoice_number < 10000)
                                                    {{ '00'.$product->invoice_number }}
                                                @elseif ($product->invoice_number < 100000)
                                                    {{ '0'.$product->invoice_number }}
                                                @else
                                                    {{ $product->invoice_number }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="num">
                                            <span class="inv-amount">{{ number_format($product->grand_total_amount, 2) }}</span>
                                        </td>
                                        <td><span class="inv-date">{{ $product->created_date }}</span></td>
                                            <td>
                                                <span class="inv-created-by">
                                                    {{ strtoupper($product->fname) }} {{ strtoupper($product->lname) }}
                                                </span>
                                            </td>
                                        <td>
                                            <div class="inv-actions">
                                                @if ($product->invoice_number == NULL)
                                                    <a href="invoices/create-invoice/{{ $product->reference }}"
                                                       class="inv-act inv-act-create"
                                                       title="Create Invoice">
                                                        <i class="fa fa-plus"></i> Create
                                                    </a>
                                                @else
                                                  {{-- DELETE PERMISSION --}}
                                               @if(isset($permission_lookup['view-job-cards-invoices']))
                                                    <a href="{{ url('invoices/tax-invoices/pdf/'.$product->reference) }}"
                                                       target="_blank"
                                                       class="inv-act inv-act-view"
                                                       title="View PDF">
                                                        <i class="fa fa-file-pdf-o"></i> View
                                                    </a>
                                                    @endif
                                                      {{-- DELETE PERMISSION --}}
                                               @if(isset($permission_lookup['edit-job-cards-invoices']))
                                                    <button class="inv-act inv-act-edit edit_invoice"
                                                            data-id="{{ $product->id }}"
                                                            data-toggle="modal"
                                                            data-target=".bs-example-modal-sm"
                                                            title="Edit Invoice">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </button>
                                             @endif
                                                    <a href="{{ url('mails/tax-invoice/'.$product->client_name.'/'.$product->reference) }}"
                                                       class="inv-act inv-act-send"
                                                       title="Send Mail">
                                                        <i class="fa fa-envelope"></i> Send
                                                    </a>
                                                @endif
                                                 {{-- DELETE PERMISSION --}}
                                               @if(isset($permission_lookup['delete-job-cards-invoices']))
                                                    <a class="inv-act inv-act-delete"
                                                       href="{{ url('invoices/delete-invoice/delete/'.$product->reference) }}"
                                                       onclick="return confirm('Are you sure you want to delete this item?');"
                                                       title="Delete Invoice">
                                                        <i class="fa fa-trash"></i>
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


{{-- ===================== EDIT TAX INVOICE MODAL ===================== --}}
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true"
     id="edit_modal">

    <form id="formUpdate" method="post" action="{{ url('invoices/update') }}">
        @csrf

        <input type="hidden" name="updated_by" value="{{ Auth::id() }}">
        <input type="hidden" name="id" id="id">

        <div class="modal-dialog modal-lg ei-modal-dialog">
            <div class="modal-content ei-modal-content">

                {{-- HEADER --}}
                <div class="ei-header">
                    <div class="ei-header-left">
                        <div class="ei-header-icon">
                            <i class="fa fa-file-text-o"></i>
                        </div>

                        <div>
                            <h4>Edit Tax Invoice</h4>
                            <p>Manage invoice information and payment details</p>
                        </div>
                    </div>

                    <button type="button" class="ei-close"
                            data-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>

                {{-- BODY --}}
                <div class="modal-body ei-body">

                    {{-- CLIENT SECTION --}}
                    <div class="ei-section">

                        <div class="ei-section-heading">
                            <div class="ei-section-icon">
                                <i class="fa fa-user"></i>
                            </div>

                            <div>
                                <h5>Client Information</h5>
                                <small>Invoice customer details</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ei-form-group">
                                    <label for="client_id">
                                        Client Name <span class="ei-required">*</span>
                                    </label>

                                    <select name="client_name"
                                            class="form-control select2"
                                            id="client_id">
                                        <option value="">Select Client</option>

                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}">
                                                {{ $client->client_name }} - ({{ $client->place }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ei-form-group">
                                    <label>
                                        LPO Number
                                        <span class="ei-optional"></span>
                                    </label>

                                    <input type="text"
                                           name="lpo_number"
                                           id="lpo_number"
                                           class="form-control ei-input"
                                           placeholder="Enter LPO number">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- INVOICE SECTION --}}
                    <div class="ei-section">
                        <div class="row">

                            {{-- Invoice Number --}}
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ei-form-group">
                                    <label>
                                        Invoice Number
                                        <span class="label label-default lock-badge"
                                              id="lock_badge_invoice">
                                            Locked
                                        </span>
                                    </label>

                                    <div class="input-group">
                                        <input type="text"
                                               name="invoice_number"
                                               id="invoice_number"
                                               class="form-control ei-input"
                                               readonly>

                                        <span class="input-group-btn">
                                            <button type="button"
                                                    class="btn ei-unlock-btn"
                                                    id="unlock_invoice"
                                                    title="Unlock to edit">
                                                <i class="fa fa-lock"></i>
                                            </button>
                                        </span>
                                    </div>

                                    <small class="ei-hint">
                                        <i class="fa fa-info-circle"></i>
                                        Locked for invoice security. Unlock only for critical corrections.
                                    </small>
                                </div>
                            </div>

                            {{-- Reference --}}
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ei-form-group">
                                    <label>Reference Number</label>

                                    <input type="text"
                                           name="reference_no"
                                           id="reference_no"
                                           readonly
                                           class="form-control ei-input">
                                </div>
                            </div>

                            {{-- Date --}}
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ei-form-group">
                                    <label>Invoice Date</label>

                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control ei-input"
                                               id="created_date"
                                               autocomplete="off"
                                               name="created_date"
                                               placeholder="YYYY-MM-DD"
                                               data-date-format="yyyy-mm-dd">

                                        <span class="input-group-addon ei-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    {{-- BANK SECTION --}}
                    <div class="ei-section">

                        <div class="ei-section-heading">
                            <div class="ei-section-icon">
                                <i class="fa fa-university"></i>
                            </div>

                            <div>
                                <h5>Banking Information</h5>
                                <small>Bank account details for invoice payments</small>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ei-form-group">
                                    <label>Account Number</label>

                                    <input type="text"
                                           name="account_number"
                                           id="account_number"
                                           class="form-control ei-input"
                                           placeholder="Account number">
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ei-form-group">
                                    <label>Account Name</label>

                                    <input type="text"
                                           name="account_name"
                                           id="account_name"
                                           class="form-control ei-input"
                                           placeholder="Account holder name">
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ei-form-group">
                                    <label>Bank Name</label>

                                    <input type="text"
                                           name="bank_name"
                                           id="bank_name"
                                           class="form-control ei-input"
                                           placeholder="Bank name">
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ei-form-group">
                                    <label>Branch Name</label>

                                    <input type="text"
                                           name="branch_name"
                                           id="branch_name"
                                           class="form-control ei-input"
                                           placeholder="Branch name">
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="form-group ei-form-group">
                                    <label>SWIFT Code</label>

                                    <input type="text"
                                           name="swift_code"
                                           id="swift_code"
                                           class="form-control ei-input"
                                           placeholder="SWIFT code">
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- INVOICE UNLOCK REASON --}}
                    <div class="ei-reason-box" id="invoice_reason_box">

                        <div class="ei-reason-heading">
                            <div class="ei-warning-icon">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>

                            <div>
                                <h5>Invoice Number Change Authorization</h5>
                                <small>Provide a reason when changing the invoice number</small>
                            </div>
                        </div>

                        <div class="form-group ei-form-group">
                            <label>
                                Reason for Changing Invoice Number
                                <span class="ei-required">*</span>
                            </label>

                            <textarea name="invoice_change_reason"
                                      id="invoice_change_reason"
                                      rows="3"
                                      class="form-control ei-input"
                                      placeholder="Explain why the invoice number must be changed..."></textarea>
                        </div>

                        <div class="ei-audit-note">
                            <i class="fa fa-shield"></i>
                            This change will be logged with your user ID:
                            <strong>{{ Auth::user()->fname ?? '' }}
                                {{ Auth::user()->lname ?? '' }}</strong>
                        </div>

                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="ei-footer">

                    <div class="ei-footer-note">
                        <i class="fa fa-info-circle"></i>
                        Please verify all information before updating.
                    </div>

                    <div class="ei-footer-actions">

                        <button type="button"
                                class="btn ei-cancel-btn"
                                data-dismiss="modal">
                            <i class="fa fa-times"></i>
                            Cancel
                        </button>

                        <button type="submit"
                                class="btn ei-update-btn">
                            <i class="fa fa-check"></i>
                            Update Invoice
                        </button>

                    </div>
                </div>

            </div>
        </div>
    </form>
</div>


{{-- ===================== Create Invoice Modal ===================== --}}
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="invoice_modal">
    <form id="formUpdate" method="post" action="{{ url('invoices/new-invoice') }}">
        @csrf
        <div class="modal-dialog modal-lg" style="width: 1300px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h2 class="modal-title" id="myLargeModalLabel">Create Invoice</h2>
                </div>

                <div class="panel-body" style="background: #fff; border: 1px solid #ddd;">
                    <div class="display_message"></div>
                    <div class="row m-t-10">
                        <div class="col-md-1"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Client *</label>
                                <select name="client[]" class="form-control select2" id="client">
                                    <option value=""> --- General --- </option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}"> {{ $client->client_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-7"></div>
                    </div>

                    <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                        <div class="row m-t-10">
                            <div class="col-md-1"></div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Add Parts *</label>
                                    <select name="product_id[]" class="form-control select2" id="product_id" required>
                                        <option value=""> --- Select Part --- </option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"> {{ $product->item_name }} - {{ $product->model }} | {{ $product->part_number }} : ({{ $product->quantity }}) </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-7">
                                <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}" class="form-control">
                                <input type="hidden" name="quantity[]" id="quantity" class="form-control">

                                <section id="Table" class="container-fluid">
                                    <table class="table table-striped table-bordered" style="background: #fff;" id="myTable1">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th width="300px">Part Name</th>
                                                <th width="100px">D (%) </th>
                                                <th width="100px">Unit </th>
                                                <th width="10">Qty</th>
                                                <th>SubTotal</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dataRows"></tbody>
                                        <tfooter style="display: none;">
                                            <tr>
                                                <td style="text-align: right" colspan="5"></td>
                                                <td><input disabled id="subtotal_" name="total_charge" class="form-control subtotal_" value="0" type="text" /></td>
                                                <td></td>
                                            </tr>
                                        </tfooter>
                                    </table>
                                </section>
                            </div>
                        </div>
                    </div>

                    <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                        <div class="row m-t-10">
                            <div class="col-md-1"></div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Add Labour *</label>
                                    <select name="labour[]" class="form-control select2" id="labour">
                                        <option value=""> --- Select Labour --- </option>
                                        @foreach($labours as $l)
                                            <option value="{{ $l->id }}"> {{ strtoupper($l->labour) }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-7">
                                <section id="Table" class="container">
                                    <table class="table table-bordered" style="background: #fff;" id="myTable2">
                                        <thead>
                                            <tr class="target_3">
                                                <th>#</th>
                                                <th width="300px">Labour</th>
                                                <th width="100px">Unit</th>
                                                <th width="20">Rate/Hr</th>
                                                <th width="30">Sub Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dataRows2"></tbody>
                                    </table>
                                </section>
                            </div>
                        </div>

                        <div class="row mt-20">
                            <table class="table" id="myTableOther">
                                <tr class="target_2">
                                    <td><div class="col-md-3"></div></td>
                                    <td>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">General (Charges)</label>
                                                <input type="text" name="labour_name[]" class="form-control" placeholder="Specify general labour">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Charges</label>
                                                <input type="text" name="unit_charge[]" class="form-control charge" placeholder="Enter Charges">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Percent (%)</label>
                                                <input type="text" name="labour_qty[]" class="form-control qty" placeholder="Enter Eg. 20">
                                                <input type="hidden" name="total_amount[]" class="form-control subtotal">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div id="client_details" style="padding: 10px; border: 0px solid #ddd;">
                        <div class="row m-t-30">
                            <div class="col-md-1"></div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Vehicle Reg. *</label>
                                    <input type="text" name="vehicle_reg" class="form-control" id="vehicle_reg" placeholder="Vehicle Reg.. Eg. T390 EEE" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Client Name *</label>
                                    <select name="client_name" class="form-control select2" id="client_name" required>
                                        <option value=""> --- Select Client Name --- </option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}"> {{ $client->client_name }} - ({{ $client->place }}) </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <table id="datatable1" class="table table-bordered">
                                    <thead id="table_title" style="display: none">
                                        <tr style="text-align: center; background: #eee;">
                                            <th>Customer Name</th>
                                            <th>Make</th>
                                            <th>Model</th>
                                            <th>Chassis No.</th>
                                            <th>Milleage </th>
                                            <th>Year of Registraion </th>
                                        </tr>
                                    </thead>
                                    <tbody id="customer_data" class="tbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="client_detail_one" style="padding: 10px; border: 1px solid #ddd;">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Make *</label>
                                    <input type="text" name="make" class="form-control" placeholder="Enter Make" id="make" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Model *</label>
                                    <input type="text" name="model" class="form-control" placeholder="Enter Model " id="model" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Chassis No. *</label>
                                    <input type="text" name="chassis" class="form-control" placeholder="Enter Chassis No." id="chassis" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Milleage</label>
                                    <input type="text" name="milleage" class="form-control" placeholder=" Eg. 799 km " id="milleage">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Year of Registration *</label>
                                    <input type="text" name="registration_year" class="form-control" placeholder="Enter Year of Registration" id="registration_year" required>
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-30">
                            <div class="col-md-1"></div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">VAT</label>
                                </div>
                                <div class="radio radio-info radio-inline">
                                    <input type="radio" name="vat_amount" id="with_vat" value="1" checked required>
                                    <label for="inlineRadio1"> With VAT </label>
                                </div>
                                <div class="radio radio-inline">
                                    <input type="radio" name="vat_amount" id="without_vat" value="0" required>
                                    <label for="inlineRadio2"> Without VAT </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Date *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" required autocomplete="off" name="created_date" placeholder="YYYY-MM-DD" id="datepicker-autoclose">
                                        <span class="input-group-addon bg-primary b-0 text-white"><i class="ti-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="control-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="save_btn1">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {

        /* ========== DATE PICKER INITIALIZATION ========== */
        $('#datepicker-autoclose').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            todayBtn: 'linked',
            clearBtn: true,
            orientation: 'bottom'
        });

        var today = new Date();
        var todayFormatted = today.getFullYear() + '-' +
            String(today.getMonth() + 1).padStart(2, '0') + '-' +
            String(today.getDate()).padStart(2, '0');

        if ($('#datepicker-autoclose').val() === '') {
            $('#datepicker-autoclose').val(todayFormatted);
        }

        $('#invoice_modal').on('shown.bs.modal', function() {
            $('#datepicker-autoclose').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                todayBtn: 'linked',
                clearBtn: true
            });
            if ($('#datepicker-autoclose').val() === '') {
                $('#datepicker-autoclose').val(todayFormatted);
            }
        });

        $('#created_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            todayBtn: 'linked',
            clearBtn: true
        });

        $('#edit_modal').on('shown.bs.modal', function() {
            $('#created_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                todayBtn: 'linked',
                clearBtn: true
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        /* ============================================================
         * SELECT2 — Client filter (proper init + design integration)
         * ============================================================ */
        if ($.fn.select2) {
            $('#client_filter').select2({
                width: '100%',
                placeholder: 'All Clients',
                allowClear: true,
                minimumResultsForSearch: 6,
                dropdownAutoWidth: false,
                escapeMarkup: function(m) { return m; }
            });

            $('#client_filter').on('select2:open', function() {
                $('.inv-filter-field').addClass('is-focused');
            });
            $('#client_filter').on('select2:close', function() {
                $('.inv-filter-field').removeClass('is-focused');
            });
        }

        /* ============================================================
         * Dynamic line item totals
         * ============================================================ */
        $(function() {
            $("#myTableOther").keyup(function() {
                var sub = 0;
                $("#myTableOther .target_2").each(function() {
                    var qty = parseFloat($(this).find(".qty").val());
                    var price = parseFloat($(this).find(".charge").val());
                    var subtotal = parseFloat((qty / 100) * price);
                    $(this).find(".subtotal").val(subtotal);
                    if (!isNaN(subtotal)) sub += subtotal;
                });
                $(".subtotal").val(sub);
            });
        });

        $(function() {
            $("#myTable1").keyup(function() {
                var sub = 0;
                $("#myTable1 .target").each(function() {
                    var qty = parseFloat($(this).find(".qty").val());
                    var discount = parseFloat($(this).find(".discount").val());
                    var price = parseFloat($(this).find(".selling_price").val());
                    var discount_value = (discount / 100) * price;
                    var total_price = price - discount_value;
                    var subtotal = qty * total_price;
                    $(this).find(".total_").val(subtotal);
                    if (!isNaN(subtotal)) sub += subtotal;
                });
                $(".subtotal_").val(sub);
            });
        });

        $("#myTable2").keyup(function() {
            var sub = 0;
            $("#myTable2 .target_3").each(function() {
                var qty = parseFloat($(this).find(".qty").val());
                var price = parseFloat($(this).find(".charge").val());
                var subtotal = (qty * price);
                $(this).find(".subtotal_other").val(subtotal);
                if (!isNaN(subtotal)) sub += subtotal;
            });
        });

        /* ============================================================
         * Form + modal interactions
         * ============================================================ */
        $("#myTable1").on('click', '.btnDelete', function() {
            $(this).closest('tr').remove();
        });
        $("#myTable2").on('click', '.btnDelete', function() {
            $(this).closest('tr').remove();
        });

        $("#labour").on('change', function() {
            var labour_id = $("#labour").val();
            $.ajax({
                type: "GET",
                url: "../labours/details/" + labour_id,
                data: { labour_id: labour_id },
                success: function(response) {
                    $("#dataRows2").show();
                    var html = '<tr class="target_3">';
                    html += '<td>' + '<input type="hidden" name="labour_name[]" value="' + response[0].labour + '">' + '</td>';
                    html += '<td width="100px">' + response[0].labour + '</td>';
                    html += '<td>' + '<input type="text" name="unit_charge[]" class="charge" value="' + response[0].charge + '">' + '</td>';
                    html += '<td>' + '<input type="text" name="labour_qty[]"  class="qty" size="5"  required>' + '</td>';
                    html += '<td>' + '<input type="text" class="subtotal_other" name="total_amount[]" readonly value="0">' + '</td>';
                    html += '<td>' + '<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>' + '</td></tr>';
                    $('#dataRows2').append(html);
                }
            });
        });

        $('#client').on('change', function() {
            var client = $('#client').val();
            $.ajax({
                type: "GET",
                url: "../products/fetch-client-prices/" + client,
                data: { client: client },
                success: function(response) {
                    $('#product_id').html(response);
                }
            });
        });

        $("#product_id").on('change', function() {
            var product_id = $("#product_id").val();
            $.ajax({
                type: "GET",
                url: "../products/price-lists/details/" + product_id,
                data: { product_id: product_id },
                success: function(response) {
                    $("#dataRows").show();
                    var html = '<tr class="target">';
                    html += '<td width="10px">' + '<input type="hidden" name="id[]" id="product_id" value="' + response[0].pid + '">' + '</td>';
                    html += '<td width="100px">' + response[0].item_name + '</td>';
                    html += '<td>' + '<input type="text" name="discount[]"  class="discount" size="5" value="0">' + '</td>';
                    html += '<td>' + '<input type="text" name="selling_price[]" class="selling_price" value="' + response[0].sale_price + '">' + '</td>';
                    html += '<td>' + '<input type="text" name="qty[]" size="5" class="qty" required>' + '</td>';
                    html += '<td>' + '<input type="text" class="total_" name="total_charge[]" readonly value="0">' + '</td>';
                    html += '<td>' + '<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>' + '</td></tr>';
                    $('#dataRows').append(html);
                }
            });
        });

        $("#client_id").on('change', function() {
            var client_id = $("#client_id").val();
            $.ajax({
                type: "GET",
                url: "../../clients/client-details/" + client_id,
                data: { client_id: client_id },
                success: function(response) {
                    if (response != '') {
                        $('#client_modal').modal('show');
                        var res = '';
                        $.each(response, function(key, value) {
                            res += '<tr>' +
                                '<td>' + '<input type="checkbox" name="check" id="reg" value="' + response[0].vehicle_reg + '">' + '</td>' +
                                '<td>' + value.vehicle_reg + '</td>' + '<td>' + value.make + ' ' + '|' + value.model + '</td>' + '<td>' + value.created_date + '</td>' +
                                '<td>' + '<input type="submit" class="btn-sm btn-primary btn-rounded waves-effect waves-light" id="save_" value="submit">' + '</td>' + '<td>' +
                                '</tr>';
                        });
                        $('#tabody').html(res);
                    } else {
                        $('#alert_modal').modal('show');
                    }
                }
            });
        });

        $('#save_').click(function() {
            alert("hi");
            $.ajax({
                url: '/get-users',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    var users = response.users;
                    var userList = $('#userList');
                    userList.empty();
                    users.forEach(function(user) {
                        userList.append('<p>' + user.name + '</p>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });

        $("#vehicle_reg").keyup(function() {
            $.ajax({
                type: "GET",
                url: "../clients/vehicle_details/" + vehicle,
                data: { vehicle: vehicle },
                success: function(response) {
                    if (response) {
                        $.each(response, function(key, value) {
                            $("#client_name").empty();
                            $("#client_name").append('<option value="' + value["id"] + '">' + value["client_name"] + '</option>');
                        });
                    } else {
                        $("#client_name").empty();
                    }
                    $("#client_name").val(response[0].client_name);
                    $("#client_id").val(response[0].id);
                    $("#make").val(response[0].make);
                    $("#model").val(response[0].model);
                    $("#chassis").val(response[0].chassis);
                    $("#milleage").val(response[0].milleage);
                    $("#registration_year").val(response[0].registration_year);
                }
            });
        });

        $('#btn-create-invoice').click(function() {
            $('#invoice_modal').modal('show');
        });

        /* ============================================================
         * EDIT INVOICE — open modal, populate data, lock state
         * ============================================================ */
        var originalInvoiceNumber = '';
        var invoiceUnlocked = false;

        function resetInvoiceLock() {
            invoiceUnlocked = false;
            $('#invoice_number')
                .prop('readonly', true)
                .removeClass('proforma-warning');
            $('#lock_badge_invoice')
                .removeClass('label-danger')
                .addClass('label-default')
                .text('Locked');
            $('#unlock_invoice')
                .prop('disabled', false)
                .removeClass('btn-danger')
                .addClass('btn-warning')
                .html('<i class="fa fa-lock"></i>');
            $('#invoice_reason_box').hide();
            $('#invoice_change_reason').val('');
        }

        $('.edit_invoice').click(function() {
            var reference_no = $(this).data('id');

            // Reset lock state each time the modal opens
            resetInvoiceLock();

            $.ajax({
                url: `../invoices/edit/${reference_no}`,
                type: "GET",
                success: function(response) {
                    $("#client_id").val(response[0].client_id).trigger('change');
                    $('#edit_modal').modal('show');
                    $("#swift_code").val(response[0].swift_code);
                    $("#account_name").val(response[0].account_name);
                    $("#account_number").val(response[0].account_number);
                    $("#bank_name").val(response[0].bank_name);
                    $("#branch_name").val(response[0].branch_name);
                    $("#invoice_number").val(response[0].invoice_number);
                    $("#reference_no").val(response[0].reference);
                    $("#client_id").val(response[0].client_id);
                    $("#id").val(response[0].id);
                    $("#client_name").val(response[0].client_name);
                    $("#created_date").val(response[0].created_date);
                    $("#client_name_value").html(response[0].client_name);

                    // NEW: LPO number
                    $("#lpo_number").val(response[0].lpo_no || '');

                    // NEW: save the original invoice number for change detection
                    originalInvoiceNumber = (response[0].invoice_number || '').toString().trim();
                }
            });
        });

        /* ============================================================
         * Unlock invoice number — requires confirmation
         * ============================================================ */
        $('#unlock_invoice').on('click', function() {
            if (invoiceUnlocked) return;

            swal({
                title: "Unlock Invoice Number?",
                text: "Editing the Invoice number should only be done for critical corrections. " +
                      "This change will be LOGGED with your user ID.",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Unlock",
                confirmButtonColor: "#f0ad4e",
                cancelButtonText: "Cancel",
                closeOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    invoiceUnlocked = true;
                    $('#invoice_number')
                        .prop('readonly', false)
                        .addClass('proforma-warning')
                        .focus();
                    $('#lock_badge_invoice')
                        .removeClass('label-default')
                        .addClass('label-danger')
                        .text('Unlocked');
                    $('#unlock_invoice')
                        .prop('disabled', true)
                        .removeClass('btn-warning')
                        .addClass('btn-danger')
                        .html('<i class="fa fa-unlock"></i>');
                    $('#invoice_reason_box').slideDown();
                }
            });
        });

        /* ============================================================
         * Submit confirmation — critical change detection
         * Scoped to the Edit modal form only (create modal has same id!)
         * ============================================================ */
        $('#edit_modal form').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var currentInvoice = ($('#invoice_number').val() || '').trim();
            var invoiceChanged = (currentInvoice !== originalInvoiceNumber);

            if (invoiceChanged) {
                // Reason is mandatory when invoice number is changed
                if (!$('#invoice_change_reason').val().trim()) {
                    swal({
                        title: "Reason Required!",
                        text: "You must provide a reason before changing the Invoice number.",
                        type: "warning",
                        confirmButtonColor: "#f0ad4e"
                    });
                    return false;
                }
                swal({
                    title: "Critical Change!",
                    text: "You are about to change the Invoice Number from '" +
                          originalInvoiceNumber + "' to '" + currentInvoice +
                          "'. This action will be LOGGED with your user ID. Are you sure?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, Update & Log",
                    confirmButtonColor: "#d9534f",
                    cancelButtonText: "Cancel",
                    closeOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) form.submit();
                });
            } else {
                swal({
                    title: "Update Invoice?",
                    text: "Are you sure you want to update this Invoice?",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonText: "Yes, Update",
                    confirmButtonColor: "#5cb85c",
                    cancelButtonText: "Cancel",
                    closeOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) form.submit();
                });
            }
        });

        /* ============================================================
         * CLIENT NAME FILTER + LIVE COUNTERS + SPINNER
         * ============================================================ */
        var currentClientFilter = '';
        var filterTimer = null;
        var totalInvoicesAll = {{ $totalInvoices }};
        var totalAmountAll   = {{ number_format($totalAmount, 2, '.', '') }};

        function hasDataTable() {
            return !!(window.jQuery && $.fn.DataTable && $.fn.DataTable.isDataTable('#datatable'));
        }

        if ($.fn.dataTable && $.fn.dataTable.ext && $.fn.dataTable.ext.search) {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (!settings || !settings.nTable || settings.nTable.id !== 'datatable') {
                    return true;
                }
                if (!currentClientFilter) { return true; }
                var row = settings.aoData[dataIndex];
                if (!row || !row.nTr) { return true; }
                var clientData = ($(row.nTr).data('client') || '').toString().toUpperCase();
                return clientData.indexOf(currentClientFilter.toUpperCase()) !== -1;
            });
        }

        function showSpinner() { $('#invTableLoading').addClass('show'); }
        function hideSpinner() { $('#invTableLoading').removeClass('show'); }

        function formatMoney(n) {
            return Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function updateCounters() {
            var visibleRows = [];

            if (hasDataTable()) {
                var table = $('#datatable').DataTable();
                table.rows({ search: 'applied' }).nodes().each(function(node) {
                    visibleRows.push(node);
                });
            } else {
                $('#datatable tbody tr').each(function() {
                    if ($(this).is(':visible')) {
                        visibleRows.push(this);
                    }
                });
            }

            var shownCount = visibleRows.length;
            $('#kpi-showing').text(shownCount);
            $('#kpi-total-invoices').text(totalInvoicesAll);
            $('#kpi-total-amount').text(formatMoney(totalAmountAll));

            var $rc = $('#results_count');
            $rc.html('Showing <strong>' + shownCount + '</strong>' +
                '<span class="inv-count-divider">/</span>' +
                '<strong>' + totalInvoicesAll + '</strong>');
        }

        function applyClientFilter() {
            currentClientFilter = ($('#client_filter').val() || '').toString();

            if (hasDataTable()) {
                $('#datatable').DataTable().draw();
            } else {
                $('#datatable tbody tr').each(function() {
                    var clientData = ($(this).data('client') || '').toString().toUpperCase();
                    var matches = !currentClientFilter ||
                        clientData.indexOf(currentClientFilter.toUpperCase()) !== -1;
                    $(this).toggle(matches);
                });
            }

            updateCounters();
        }

        $('#client_filter').on('change', function() {
            if (filterTimer) { clearTimeout(filterTimer); }
            showSpinner();

            filterTimer = setTimeout(function() {
                applyClientFilter();
                hideSpinner();
                filterTimer = null;
            }, 350);
        });

        $('#clear_filter').on('click', function() {
            if (!$('#client_filter').val()) { return; }
            $('#client_filter').val(null).trigger('change');
        });

        if (hasDataTable()) {
            $('#datatable').on('draw.dt', function() {
                updateCounters();
            });
        }
        updateCounters();
    });
</script>

@endsection
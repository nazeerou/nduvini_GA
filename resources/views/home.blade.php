@extends('layouts.app_header')

@section('content')

@php
    $inventoryPurchaseValue = collect($total_inventory_purchases ?? [])
        ->sum('total_purchase_amount');

    $inventorySalesValue = $total_inventory_sales_value ?? 0;
    $lowStockCount = $less_stocks ?? 0;
    $outStockCount = is_countable($out_stocks ?? null)
        ? count($out_stocks)
        : 0;

    $monthlyPurchaseValue = collect($month_purchases ?? [])
        ->sum('total_month_purchases');

    $monthlySalesValue = collect($month_sales ?? [])
        ->sum('total_month_sales');

    $netMargin = $monthlySalesValue - $monthlyPurchaseValue;
    $netClass  = $netMargin > 0 ? 'positive' : ($netMargin < 0 ? 'negative' : 'neutral');

    $pendingApprovals  = $pending_approvals ?? 0;
    $approvedRequests  = $approved_requests ?? 0;
    $rejectedRequests  = $rejected_requests ?? 0;

    $unreadMessages    = $unread_messages ?? 0;
    $notificationsCount = $notifications_count ?? 0;

    $totalEmployees   = $total_employees ?? 0;
    $presentEmployees = $present_employees ?? 0;
    $pendingPayroll   = $pending_payroll ?? 0;

    $pendingJobs       = $pending_jobs ?? 0;
    $vehiclesInService = $vehicles_in_service ?? 0;
    $completedJobs     = $completed_jobs ?? 0;
    $unbilledJobs      = $unbilled_jobs ?? 0;

    $pendingPurchases = $pending_purchases ?? 0;
    $pendingPayments  = $pending_payments ?? 0;
    $pendingInvoices  = $pending_invoices ?? 0;

    $receivables = $receivables ?? 0;
    $payables    = $payables ?? 0;

    $recentActivities = $recent_activities ?? collect();
    $recentMessages   = $recent_messages ?? collect();

    $currency = $currency ?? 'TZS';

    $formatMoney = function ($amount) {
        return number_format((float) $amount, 2);
    };
@endphp

<style>
    /* ============================================================
       GARAGE DASHBOARD · Compact & Attractive Cards
       ============================================================ */
    .garage-dashboard {
        --gd-ink:         #0b1220;
        --gd-ink-soft:    #1e293b;
        --gd-muted:       #64748b;
        --gd-muted-light: #94a3b8;
        --gd-line:        #e8ecf3;
        --gd-line-soft:   #f1f4f9;
        --gd-white:       #ffffff;
        --gd-bg-hover:    #f1f5fb;
        --gd-primary:     #2563eb;
        --gd-primary-dk:  #1d4ed8;
        --gd-primary-lt:  #3b82f6;
        --gd-radius:      14px;

        position: relative;
        padding: 8px 0 44px;
        color: var(--gd-ink-soft);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        isolation: isolate;
    }

    .garage-dashboard::before {
        content: "";
        position: absolute;
        inset: -8px -20px 0;
        z-index: -1;
        pointer-events: none;
        background:
            radial-gradient(ellipse 55% 40% at 10% 0%,  rgba(59,130,246,.055) 0%, transparent 60%),
            radial-gradient(ellipse 50% 35% at 90% 100%, rgba(139,92,246,.055) 0%, transparent 60%);
    }

    .garage-dashboard * { box-sizing: border-box; }

    /* ============================================================
       HEADER
       ============================================================ */
    .garage-dashboard .dashboard-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--gd-line);
        position: relative;
    }

    .garage-dashboard .dashboard-header::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -1px;
        width: 64px;
        height: 2px;
        background: linear-gradient(90deg, var(--gd-primary-lt), var(--gd-primary));
        border-radius: 2px;
    }

    .garage-dashboard .dashboard-heading h3 {
        font-size: 23px;
        font-weight: 800;
        color: var(--gd-ink);
        margin: 0 0 6px;
        letter-spacing: -0.5px;
        line-height: 1.15;
        display: flex;
        align-items: center;
    }

    .garage-dashboard .dashboard-heading h3 .gd-dot {
        display: inline-block;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: linear-gradient(135deg, #34d399, #059669);
        margin-right: 11px;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, .15);
        animation: gd-pulse 2.2s ease-in-out infinite;
        flex-shrink: 0;
    }

    @keyframes gd-pulse {
        0%, 100% { box-shadow: 0 0 0 4px rgba(16, 185, 129, .15); }
        50%      { box-shadow: 0 0 0 7px rgba(16, 185, 129, .06); }
    }

    .garage-dashboard .dashboard-heading p {
        font-size: 13px;
        color: var(--gd-muted);
        margin: 0;
        line-height: 1.5;
    }

    .garage-dashboard .dashboard-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        color: var(--gd-ink-soft);
        border: 1px solid rgba(226,232,240,.9);
        border-radius: 10px;
        padding: 9px 15px;
        font-size: 12px;
        font-weight: 600;
        box-shadow:
            0 1px 2px rgba(15,23,42,.04),
            inset 0 1px 0 rgba(255,255,255,.9);
    }

    .garage-dashboard .dashboard-date i {
        color: var(--gd-primary);
        font-size: 12px;
        opacity: .85;
    }

    /* ============================================================
       SECTION HEADINGS
       ============================================================ */
    .garage-dashboard .section-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin: 30px 0 14px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .garage-dashboard .section-heading:first-of-type { margin-top: 4px; }

    .garage-dashboard .section-heading h4 {
        position: relative;
        font-size: 14px;
        font-weight: 700;
        color: var(--gd-ink);
        margin: 0;
        padding-left: 14px;
        letter-spacing: -.1px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .garage-dashboard .section-heading h4::before {
        content: "";
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 16px;
        border-radius: 2px;
        background: linear-gradient(180deg, var(--gd-primary-lt), var(--gd-primary));
        box-shadow: 0 2px 6px rgba(37,99,235,.28);
    }

    .garage-dashboard .section-heading h4 i {
        font-size: 12px;
        color: var(--gd-primary);
        opacity: .9;
    }

    .garage-dashboard .section-heading small {
        color: var(--gd-muted-light);
        font-size: 11px;
        font-weight: 500;
    }

    /* ============================================================
       COMPACT METRIC CARDS
       ============================================================ */
    .garage-dashboard .metric-card {
        position: relative;
        display: block;
        min-height: 104px;
        padding: 14px 16px 14px;
        margin-bottom: 16px;
        border-radius: var(--gd-radius);
        background: linear-gradient(160deg, #ffffff 0%, #fafbfd 100%);
        border: 1px solid var(--gd-line);
        overflow: hidden;
        text-decoration: none !important;
        color: var(--gd-ink-soft);
        box-shadow:
            0 1px 2px rgba(15,23,42,.04),
            0 4px 12px rgba(15,23,42,.02);
        transition:
            transform .26s cubic-bezier(.4,0,.2,1),
            box-shadow .26s cubic-bezier(.4,0,.2,1),
            border-color .2s ease;
        animation: gd-fadeUp .5s cubic-bezier(.4,0,.2,1) backwards;
    }

    @keyframes gd-fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .garage-dashboard .row > [class*="col-"]:nth-child(1) .metric-card { animation-delay: .03s; }
    .garage-dashboard .row > [class*="col-"]:nth-child(2) .metric-card { animation-delay: .07s; }
    .garage-dashboard .row > [class*="col-"]:nth-child(3) .metric-card { animation-delay: .11s; }
    .garage-dashboard .row > [class*="col-"]:nth-child(4) .metric-card { animation-delay: .15s; }

    .garage-dashboard .metric-card > * {
        position: relative;
        z-index: 1;
    }

    /* Decorative ambient blob */
    .garage-dashboard .metric-card::after {
        content: "";
        position: absolute;
        right: -40px;
        bottom: -40px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        pointer-events: none;
        z-index: 0;
        opacity: .85;
        transition: transform .5s cubic-bezier(.4,0,.2,1);
    }

    /* Colored left accent bar */
    .garage-dashboard .metric-card::before {
        content: "";
        position: absolute;
        top: 14px;
        bottom: 14px;
        left: 0;
        width: 3px;
        border-radius: 0 3px 3px 0;
        background: linear-gradient(180deg, var(--gd-primary-lt), var(--gd-primary));
        z-index: 2;
    }

    .garage-dashboard a.metric-card { cursor: pointer; }

    .garage-dashboard a.metric-card:hover {
        transform: translateY(-3px);
        border-color: #d6dde7;
        text-decoration: none;
    }

    .garage-dashboard a.metric-card:hover::after {
        transform: scale(1.3) translate(-8px, -8px);
    }

    .garage-dashboard a.metric-card:active {
        transform: translateY(-1px);
        transition-duration: .1s;
    }

    /* ---- Card internals (compact) ---- */
    .garage-dashboard .metric-card .metric-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .garage-dashboard .metric-card .metric-label {
        font-size: 10.5px;
        font-weight: 700;
        margin: 0;
        color: var(--gd-muted);
        text-transform: uppercase;
        letter-spacing: .07em;
        line-height: 1.35;
        max-width: 72%;
    }

    .garage-dashboard .metric-card .metric-icon {
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 9px;
        font-size: 14px;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: var(--gd-ink-soft);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.7),
            0 1px 4px rgba(15,23,42,.04);
        transition:
            transform .3s cubic-bezier(.4,0,.2,1),
            box-shadow .25s ease;
    }

    .garage-dashboard a.metric-card:hover .metric-icon {
        transform: scale(1.08) rotate(-4deg);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.7),
            0 4px 12px rgba(15,23,42,.08);
    }

    .garage-dashboard .metric-card .metric-value {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', 'Menlo', monospace;
        font-size: 19px;
        font-weight: 700;
        margin: 10px 0 0;
        letter-spacing: -.4px;
        line-height: 1.15;
        color: var(--gd-ink);
        font-variant-numeric: tabular-nums;
    }

    .garage-dashboard .metric-card .metric-footer {
        font-size: 10.5px;
        margin-top: 6px;
        color: var(--gd-muted-light);
        line-height: 1.4;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* ============================================================
       COLOR THEMES
       ============================================================ */
    .garage-dashboard .blue-card::before { background: linear-gradient(180deg, #60a5fa, #2563eb); }
    .garage-dashboard .blue-card::after  { background: radial-gradient(circle, rgba(59,130,246,.10) 0%, transparent 70%); }
    .garage-dashboard .blue-card .metric-icon {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
    }
    .garage-dashboard a.blue-card:hover {
        box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 14px 24px -12px rgba(37,99,235,.22);
    }

    .garage-dashboard .purple-card::before { background: linear-gradient(180deg, #a78bfa, #7c3aed); }
    .garage-dashboard .purple-card::after  { background: radial-gradient(circle, rgba(139,92,246,.10) 0%, transparent 70%); }
    .garage-dashboard .purple-card .metric-icon {
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        color: #6d28d9;
    }
    .garage-dashboard a.purple-card:hover {
        box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 14px 24px -12px rgba(124,58,237,.22);
    }

    .garage-dashboard .green-card::before { background: linear-gradient(180deg, #34d399, #059669); }
    .garage-dashboard .green-card::after  { background: radial-gradient(circle, rgba(16,185,129,.10) 0%, transparent 70%); }
    .garage-dashboard .green-card .metric-icon {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #047857;
    }
    .garage-dashboard a.green-card:hover {
        box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 14px 24px -12px rgba(16,185,129,.22);
    }

    .garage-dashboard .orange-card::before { background: linear-gradient(180deg, #fbbf24, #d97706); }
    .garage-dashboard .orange-card::after  { background: radial-gradient(circle, rgba(245,158,11,.10) 0%, transparent 70%); }
    .garage-dashboard .orange-card .metric-icon {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #b45309;
    }
    .garage-dashboard a.orange-card:hover {
        box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 14px 24px -12px rgba(245,158,11,.22);
    }

    .garage-dashboard .red-card::before { background: linear-gradient(180deg, #f87171, #dc2626); }
    .garage-dashboard .red-card::after  { background: radial-gradient(circle, rgba(239,68,68,.10) 0%, transparent 70%); }
    .garage-dashboard .red-card .metric-icon {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #b91c1c;
    }
    .garage-dashboard a.red-card:hover {
        box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 14px 24px -12px rgba(220,38,38,.22);
    }

    .garage-dashboard .cyan-card::before { background: linear-gradient(180deg, #22d3ee, #0891b2); }
    .garage-dashboard .cyan-card::after  { background: radial-gradient(circle, rgba(6,182,212,.10) 0%, transparent 70%); }
    .garage-dashboard .cyan-card .metric-icon {
        background: linear-gradient(135deg, #cffafe, #a5f3fc);
        color: #0e7490;
    }
    .garage-dashboard a.cyan-card:hover {
        box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 14px 24px -12px rgba(8,145,178,.22);
    }

    .garage-dashboard .pink-card::before { background: linear-gradient(180deg, #f472b6, #db2777); }
    .garage-dashboard .pink-card::after  { background: radial-gradient(circle, rgba(236,72,153,.10) 0%, transparent 70%); }
    .garage-dashboard .pink-card .metric-icon {
        background: linear-gradient(135deg, #fce7f3, #fbcfe8);
        color: #be185d;
    }
    .garage-dashboard a.pink-card:hover {
        box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 14px 24px -12px rgba(219,39,119,.22);
    }

    .garage-dashboard .slate-card::before { background: linear-gradient(180deg, #94a3b8, #475569); }
    .garage-dashboard .slate-card::after  { background: radial-gradient(circle, rgba(100,116,139,.10) 0%, transparent 70%); }
    .garage-dashboard .slate-card .metric-icon {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: #334155;
    }
    .garage-dashboard a.slate-card:hover {
        box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 14px 24px -12px rgba(71,85,105,.22);
    }

    /* ============================================================
       SPLIT METRIC CARD (Premium two-metric layout)
       ============================================================ */
    .garage-dashboard .metric-split {
        display: grid;
        grid-template-columns: 1fr minmax(120px, 140px) 1fr;
        align-items: stretch;
        margin-bottom: 16px;
        border-radius: var(--gd-radius);
        border: 1px solid var(--gd-line);
        background: linear-gradient(160deg, #ffffff 0%, #fafbfd 100%);
        overflow: hidden;
        box-shadow:
            0 1px 2px rgba(15,23,42,.04),
            0 4px 12px rgba(15,23,42,.02);
        transition: box-shadow .26s ease, border-color .2s ease;
        animation: gd-fadeUp .5s cubic-bezier(.4,0,.2,1) backwards;
        animation-delay: .05s;
    }

    .garage-dashboard .metric-split:hover {
        box-shadow:
            0 1px 2px rgba(15,23,42,.04),
            0 12px 28px -16px rgba(15,23,42,.14);
        border-color: #d6dde7;
    }

    /* ---- Sides ---- */
    .garage-dashboard .split-side {
        position: relative;
        padding: 22px 26px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 160px;
        overflow: hidden;
    }

    .garage-dashboard .split-side::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 0;
    }

    .garage-dashboard .split-purchases::before {
        background: radial-gradient(circle at 15% 100%, rgba(16,185,129,.07) 0%, transparent 65%);
    }

    .garage-dashboard .split-sales::before {
        background: radial-gradient(circle at 85% 100%, rgba(6,182,212,.07) 0%, transparent 65%);
    }

    .garage-dashboard .split-side > * {
        position: relative;
        z-index: 1;
    }

    /* ---- Header ---- */
    .garage-dashboard .split-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }

    .garage-dashboard .split-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.7),
            0 2px 6px rgba(15,23,42,.06);
    }

    .garage-dashboard .split-purchases .split-icon {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #047857;
    }

    .garage-dashboard .split-sales .split-icon {
        background: linear-gradient(135deg, #cffafe, #a5f3fc);
        color: #0e7490;
    }

    .garage-dashboard .split-label {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--gd-muted);
    }

    /* ---- Value ---- */
    .garage-dashboard .split-value {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', 'Menlo', monospace;
        font-size: clamp(20px, 2vw, 26px);
        font-weight: 700;
        letter-spacing: -.6px;
        line-height: 1.1;
        color: var(--gd-ink);
        font-variant-numeric: tabular-nums;
        display: flex;
        align-items: baseline;
        gap: 8px;
        flex-wrap: wrap;
    }

    .garage-dashboard .split-currency {
        font-family: inherit;
        font-size: 10.5px;
        font-weight: 600;
        color: var(--gd-muted-light);
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .garage-dashboard .split-footer {
        font-size: 10.5px;
        color: var(--gd-muted-light);
        margin-top: 8px;
        line-height: 1.4;
        font-weight: 500;
    }

    /* ---- Center divider + net margin badge ---- */
    .garage-dashboard .split-center {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 0;
    }

    .garage-dashboard .split-center::before {
        content: "";
        position: absolute;
        top: 20px;
        bottom: 20px;
        left: 50%;
        width: 1px;
        transform: translateX(-50%);
        background: linear-gradient(
            to bottom,
            transparent,
            var(--gd-line) 20%,
            var(--gd-line) 80%,
            transparent
        );
        z-index: 0;
    }

    .garage-dashboard .split-net {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        padding: 14px 16px;
        background: linear-gradient(160deg, #ffffff 0%, #fafbfd 100%);
        border: 1px solid var(--gd-line);
        border-radius: 12px;
        text-align: center;
        min-width: 112px;
        box-shadow:
            0 2px 8px rgba(15,23,42,.06),
            0 1px 2px rgba(15,23,42,.04),
            inset 0 1px 0 rgba(255,255,255,.9);
    }

    .garage-dashboard .split-net-label {
        font-size: 8.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: var(--gd-muted-light);
        line-height: 1;
    }

    .garage-dashboard .split-net-value {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', 'Menlo', monospace;
        font-size: 13.5px;
        font-weight: 700;
        letter-spacing: -.2px;
        line-height: 1.2;
        font-variant-numeric: tabular-nums;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .garage-dashboard .split-net-value::before {
        font-size: 8px;
        display: inline-block;
        content: "▲";
    }

    .garage-dashboard .split-net-value.positive { color: #059669; }
    .garage-dashboard .split-net-value.negative { color: #dc2626; }
    .garage-dashboard .split-net-value.neutral  { color: var(--gd-muted); }

    .garage-dashboard .split-net-value.negative::before { content: "▼"; }
    .garage-dashboard .split-net-value.neutral::before  { content: "●"; font-size: 6px; }

    .garage-dashboard .split-net-currency {
        font-size: 8.5px;
        font-weight: 600;
        color: var(--gd-muted-light);
        text-transform: uppercase;
        letter-spacing: .08em;
        line-height: 1;
    }

    /* ============================================================
       PANELS (Activities + Messages)
       ============================================================ */
    .garage-dashboard .dashboard-panel {
        background: linear-gradient(180deg, #ffffff 0%, #fdfdfe 100%);
        border: 1px solid var(--gd-line);
        border-radius: var(--gd-radius);
        margin-bottom: 18px;
        overflow: hidden;
        box-shadow:
            0 1px 2px rgba(15,23,42,.04),
            0 4px 12px rgba(15,23,42,.02);
        transition: box-shadow .26s ease, border-color .2s ease;
        animation: gd-fadeUp .5s cubic-bezier(.4,0,.2,1) backwards;
        animation-delay: .05s;
    }

    .garage-dashboard .dashboard-panel:hover {
        box-shadow:
            0 1px 2px rgba(15,23,42,.04),
            0 8px 22px rgba(15,23,42,.06);
        border-color: #dbe2ec;
    }

    .garage-dashboard .panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid var(--gd-line-soft);
        background: linear-gradient(180deg, #fbfcfe 0%, #ffffff 100%);
    }

    .garage-dashboard .panel-heading h4 {
        font-size: 13px;
        font-weight: 700;
        color: var(--gd-ink);
        margin: 0;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        letter-spacing: -.1px;
    }

    .garage-dashboard .panel-heading h4 i {
        color: var(--gd-primary);
        font-size: 12px;
    }

    .garage-dashboard .panel-heading a {
        font-size: 11px;
        color: var(--gd-primary);
        text-decoration: none;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
        transition: all .18s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .garage-dashboard .panel-heading a:hover {
        background: #dbeafe;
        text-decoration: none;
        color: var(--gd-primary-dk);
    }

    .garage-dashboard .panel-heading a .badge {
        background: linear-gradient(135deg, var(--gd-primary-lt), var(--gd-primary));
        color: #fff;
        font-size: 9.5px;
        padding: 3px 7px;
        border-radius: 999px;
        margin-left: 3px;
        box-shadow: 0 1px 3px rgba(37,99,235,.25);
    }

    .garage-dashboard .panel-content {
        padding: 4px 20px 10px;
    }

    /* ---- Activity items ---- */
    .garage-dashboard .activity-item {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--gd-line-soft);
        position: relative;
        transition: background .18s ease;
    }

    .garage-dashboard .activity-item:last-child { border-bottom: none; }

    .garage-dashboard .activity-item:hover {
        background: linear-gradient(90deg, transparent, rgba(37,99,235,.025));
    }

    .garage-dashboard .activity-dot {
        position: relative;
        width: 9px;
        height: 9px;
        min-width: 9px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--gd-primary-lt), var(--gd-primary));
        margin-top: 5px;
        box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    }

    .garage-dashboard .activity-item strong {
        font-size: 12px;
        color: var(--gd-ink);
        display: block;
        line-height: 1.45;
        font-weight: 600;
    }

    .garage-dashboard .activity-item small {
        font-size: 10.5px;
        color: var(--gd-muted);
        display: block;
        margin-top: 3px;
    }

    /* ---- Message items ---- */
    .garage-dashboard .message-item {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--gd-line-soft);
        align-items: flex-start;
        transition: background .18s ease;
    }

    .garage-dashboard .message-item:last-child { border-bottom: none; }

    .garage-dashboard .message-avatar {
        width: 36px;
        height: 36px;
        min-width: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.7),
            0 1px 4px rgba(30,64,175,.10);
        transition: transform .28s ease;
    }

    .garage-dashboard .message-item:hover .message-avatar {
        transform: scale(1.06);
    }

    .garage-dashboard .message-item strong {
        font-size: 12px;
        color: var(--gd-ink);
        font-weight: 600;
    }

    .garage-dashboard .message-item p {
        font-size: 11px;
        color: var(--gd-muted);
        margin: 3px 0 0;
        line-height: 1.5;
    }

    .garage-dashboard .message-item small {
        color: var(--gd-muted-light);
        font-size: 10px;
    }

    .garage-dashboard .action-link {
        text-decoration: none !important;
        display: block;
        border-radius: 9px;
        margin: 0 -9px;
        padding: 0 9px;
        transition: background .18s ease;
    }

    .garage-dashboard .action-link:hover {
        background: var(--gd-bg-hover);
    }

    .garage-dashboard .action-link:hover .message-item {
        border-bottom-color: transparent;
    }

    .garage-dashboard .action-link:hover strong {
        color: var(--gd-primary);
    }

    /* ============================================================
       STATUS PILL
       ============================================================ */
    .garage-dashboard .status-pill {
        display: inline-block;
        font-size: 9px;
        font-weight: 700;
        border-radius: 999px;
        padding: 3px 9px;
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #b45309;
        letter-spacing: .04em;
        text-transform: uppercase;
        box-shadow: 0 1px 2px rgba(180,83,9,.12);
        flex-shrink: 0;
        margin-top: 6px;
    }

    /* ============================================================
       EMPTY STATE
       ============================================================ */
    .garage-dashboard .empty-state {
        text-align: center;
        color: var(--gd-muted-light);
        font-size: 12px;
        padding: 32px 10px;
        line-height: 1.55;
    }

    .garage-dashboard .empty-state i {
        font-size: 28px;
        display: block;
        margin-bottom: 10px;
        color: #cbd5e1;
    }

    /* ============================================================
       RESPONSIVE
       ============================================================ */
    @media (max-width: 991px) {
        .garage-dashboard .metric-card .metric-value {
            font-size: 18px;
        }
    }

    @media (max-width: 767px) {
        .garage-dashboard { padding: 4px 0 34px; }

        .garage-dashboard .dashboard-header {
            align-items: flex-start;
            flex-direction: column;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .garage-dashboard .dashboard-heading h3 { font-size: 19px; }
        .garage-dashboard .dashboard-heading p  { font-size: 12px; }

        .garage-dashboard .dashboard-date {
            font-size: 11px;
            padding: 8px 13px;
            width: 100%;
            justify-content: center;
        }

        .garage-dashboard .section-heading { margin: 24px 0 12px; }
        .garage-dashboard .section-heading h4 { font-size: 13px; }
        .garage-dashboard .section-heading small { font-size: 10px; }

        .garage-dashboard .metric-card {
            min-height: 98px;
            padding: 12px 14px;
        }
        .garage-dashboard .metric-card .metric-value { font-size: 17px; margin-top: 9px; }
        .garage-dashboard .metric-card .metric-icon {
            width: 32px;
            height: 32px;
            font-size: 13px;
        }

        .garage-dashboard .panel-heading { padding: 12px 16px; }
        .garage-dashboard .panel-content { padding: 2px 16px 8px; }

        /* Split card stacks on mobile */
        .garage-dashboard .metric-split {
            grid-template-columns: 1fr;
        }
        .garage-dashboard .split-side {
            min-height: auto;
            padding: 20px 22px;
        }
        .garage-dashboard .split-center {
            padding: 14px 20px;
            background: linear-gradient(180deg, #fafbfd 0%, #ffffff 100%);
            border-top: 1px solid var(--gd-line-soft);
            border-bottom: 1px solid var(--gd-line-soft);
        }
        .garage-dashboard .split-center::before { display: none; }
        .garage-dashboard .split-net {
            flex-direction: row;
            min-width: auto;
            padding: 9px 14px;
            gap: 8px;
        }
        .garage-dashboard .split-value { font-size: 22px; }
    }

    @media (max-width: 480px) {
        .garage-dashboard .metric-card .metric-label {
            font-size: 10px;
            max-width: 64%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .garage-dashboard .metric-card,
        .garage-dashboard .dashboard-panel,
        .garage-dashboard .metric-split,
        .garage-dashboard .dashboard-heading h3 .gd-dot {
            animation: none !important;
        }
        .garage-dashboard .metric-card,
        .garage-dashboard .metric-card::after,
        .garage-dashboard .metric-icon {
            transition: none !important;
        }
    }
</style>

<div class="garage-dashboard">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div class="dashboard-heading">
            <h3><span class="gd-dot"></span>Dashboard</h3>
            <p>Overview of your garage, accounting and business operations.</p>
        </div>

        <div class="dashboard-date">
            <i class="fa fa-calendar"></i>
            {{ now()->format('l, d F Y') }}
        </div>
    </div>

    {{-- =========================================================
         BUSINESS OVERVIEW
    ========================================================== --}}
    <div class="section-heading">
        <h4><i class="fa fa-line-chart"></i> Business Overview</h4>
        <small>Financial performance and inventory valuation</small>
    </div>

    <div class="row">

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('current-stocks') }}" class="metric-card blue-card">
                <div class="metric-top">
                    <p class="metric-label">Inventory Purchase Value</p>
                    <div class="metric-icon"><i class="fa fa-cubes"></i></div>
                </div>
                <h3 class="metric-value">{{ $formatMoney($inventoryPurchaseValue) }}</h3>
                <div class="metric-footer">All inventory purchases</div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('all-invoices') }}" class="metric-card purple-card">
                <div class="metric-top">
                    <p class="metric-label">Inventory Sales Value</p>
                    <div class="metric-icon"><i class="fa fa-shopping-cart"></i></div>
                </div>
                <h3 class="metric-value">{{ $formatMoney($inventorySalesValue) }}</h3>
                <div class="metric-footer">Total sales value</div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('minimum-stock') }}" class="metric-card orange-card">
                <div class="metric-top">
                    <p class="metric-label">Low Stock Items</p>
                    <div class="metric-icon"><i class="fa fa-exclamation-triangle"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($lowStockCount) }}</h3>
                <div class="metric-footer">Below minimum requirement</div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('stock-out') }}" class="metric-card red-card">
                <div class="metric-top">
                    <p class="metric-label">Out of Stock</p>
                    <div class="metric-icon"><i class="fa fa-ban"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($outStockCount) }}</h3>
                <div class="metric-footer">Requires replenishment</div>
            </a>
        </div>

    </div>

    {{-- =========================================================
         MONTHLY PERFORMANCE (Premium split card)
    ========================================================== --}}
    <div class="section-heading">
        <h4><i class="fa fa-bar-chart"></i> Monthly Performance</h4>
        <small>{{ now()->format('F Y') }}</small>
    </div>

    <div class="metric-split">

        {{-- Purchases (left) --}}
        <div class="split-side split-purchases">
            <div class="split-header">
                <div class="split-icon"><i class="fa fa-truck"></i></div>
                <span class="split-label">Monthly Purchases</span>
            </div>
            <div class="split-value">
                {{ $formatMoney($monthlyPurchaseValue) }}
                <span class="split-currency">{{ $currency }}</span>
            </div>
            <div class="split-footer">Total purchase value this month</div>
        </div>

        {{-- Net margin (center) --}}
        <div class="split-center">
            <div class="split-net">
                <span class="split-net-label">Net Margin</span>
                <span class="split-net-value {{ $netClass }}">
                    {{ $formatMoney(abs($netMargin)) }}
                </span>
                <span class="split-net-currency">{{ $currency }}</span>
            </div>
        </div>

        {{-- Sales (right) --}}
        <div class="split-side split-sales">
            <div class="split-header">
                <div class="split-icon"><i class="fa fa-money"></i></div>
                <span class="split-label">Monthly Sales</span>
            </div>
            <div class="split-value">
                {{ $formatMoney($monthlySalesValue) }}
                <span class="split-currency">{{ $currency }}</span>
            </div>
            <div class="split-footer">Total sales value this month</div>
        </div>

    </div>

    {{-- =========================================================
         APPROVAL INBOX
    ========================================================== --}}
    <div class="section-heading">
        <h4><i class="fa fa-inbox"></i> Approval Inbox</h4>
        <small>Requests requiring attention</small>
    </div>

    <div class="row">

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('approval-inbox') }}" class="metric-card orange-card">
                <div class="metric-top">
                    <p class="metric-label">Pending Approvals</p>
                    <div class="metric-icon"><i class="fa fa-hourglass-half"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($pendingApprovals) }}</h3>
                <div class="metric-footer">Awaiting your approval</div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('approval-inbox?status=approved') }}" class="metric-card green-card">
                <div class="metric-top">
                    <p class="metric-label">Approved Requests</p>
                    <div class="metric-icon"><i class="fa fa-check-circle"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($approvedRequests) }}</h3>
                <div class="metric-footer">Successfully approved</div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('approval-inbox?status=rejected') }}" class="metric-card red-card">
                <div class="metric-top">
                    <p class="metric-label">Rejected Requests</p>
                    <div class="metric-icon"><i class="fa fa-times-circle"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($rejectedRequests) }}</h3>
                <div class="metric-footer">Rejected requests</div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('messages') }}" class="metric-card blue-card">
                <div class="metric-top">
                    <p class="metric-label">Unread Messages</p>
                    <div class="metric-icon"><i class="fa fa-envelope"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($unreadMessages) }}</h3>
                <div class="metric-footer">Awaiting your attention</div>
            </a>
        </div>

    </div>

    {{-- =========================================================
         OPERATIONS
    ========================================================== --}}
    <div class="section-heading">
        <h4><i class="fa fa-cogs"></i> Garage &amp; Inventory Operations</h4>
        <small>Operational activities and outstanding work</small>
    </div>

    <div class="row">

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('all-job-cards') }}" class="metric-card blue-card">
                <div class="metric-top">
                    <p class="metric-label">Pending Job Cards</p>
                    <div class="metric-icon"><i class="fa fa-wrench"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($pendingJobs) }}</h3>
                <div class="metric-footer">Jobs awaiting action</div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('all-job-cards') }}" class="metric-card orange-card">
                <div class="metric-top">
                    <p class="metric-label">Vehicles In Service</p>
                    <div class="metric-icon"><i class="fa fa-car"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($vehiclesInService) }}</h3>
                <div class="metric-footer">Currently being serviced</div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('all-job-cards') }}" class="metric-card green-card">
                <div class="metric-top">
                    <p class="metric-label">Completed Jobs</p>
                    <div class="metric-icon"><i class="fa fa-check"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($completedJobs) }}</h3>
                <div class="metric-footer">Completed service jobs</div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('all-invoices') }}" class="metric-card purple-card">
                <div class="metric-top">
                    <p class="metric-label">Unbilled Jobs</p>
                    <div class="metric-icon"><i class="fa fa-file-text-o"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($unbilledJobs) }}</h3>
                <div class="metric-footer">Awaiting invoicing</div>
            </a>
        </div>

    </div>

    {{-- =========================================================
         ACCOUNTING
    ========================================================== --}}
    <div class="section-heading">
        <h4><i class="fa fa-calculator"></i> Accounting &amp; Finance</h4>
        <small>Outstanding financial activities</small>
    </div>

    <div class="row">

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('sales-history') }}" class="metric-card cyan-card">
                <div class="metric-top">
                    <p class="metric-label">Customer Receivables</p>
                    <div class="metric-icon"><i class="fa fa-users"></i></div>
                </div>
                <h3 class="metric-value">{{ $formatMoney($receivables) }}</h3>
                <div class="metric-footer">Outstanding customer balances</div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('purchases-history') }}" class="metric-card pink-card">
                <div class="metric-top">
                    <p class="metric-label">Supplier Payables</p>
                    <div class="metric-icon"><i class="fa fa-truck"></i></div>
                </div>
                <h3 class="metric-value">{{ $formatMoney($payables) }}</h3>
                <div class="metric-footer">Outstanding supplier balances</div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('all-invoices') }}" class="metric-card orange-card">
                <div class="metric-top">
                    <p class="metric-label">Pending Invoices</p>
                    <div class="metric-icon"><i class="fa fa-file-text"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($pendingInvoices) }}</h3>
                <div class="metric-footer">Awaiting processing</div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="{{ url('accounts') }}" class="metric-card red-card">
                <div class="metric-top">
                    <p class="metric-label">Pending Payments</p>
                    <div class="metric-icon"><i class="fa fa-credit-card"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($pendingPayments) }}</h3>
                <div class="metric-footer">Awaiting processing</div>
            </a>
        </div>

    </div>

    {{-- =========================================================
         STAFF & PAYROLL (3 cards)
    ========================================================== --}}
    <div class="section-heading">
        <h4><i class="fa fa-users"></i> Staff &amp; Payroll Management</h4>
        <small>Workforce and payroll overview</small>
    </div>

    <div class="row">

        <div class="col-lg-4 col-md-4 col-sm-6">
            <a href="{{ url('employees') }}" class="metric-card blue-card">
                <div class="metric-top">
                    <p class="metric-label">Total Employees</p>
                    <div class="metric-icon"><i class="fa fa-users"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($totalEmployees) }}</h3>
                <div class="metric-footer">Registered employees</div>
            </a>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6">
            <a href="{{ url('attendance') }}" class="metric-card green-card">
                <div class="metric-top">
                    <p class="metric-label">Present Today</p>
                    <div class="metric-icon"><i class="fa fa-user-check"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($presentEmployees) }}</h3>
                <div class="metric-footer">Recorded as present</div>
            </a>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6">
            <a href="{{ url('payroll') }}" class="metric-card purple-card">
                <div class="metric-top">
                    <p class="metric-label">Pending Payroll</p>
                    <div class="metric-icon"><i class="fa fa-money"></i></div>
                </div>
                <h3 class="metric-value">{{ number_format($pendingPayroll) }}</h3>
                <div class="metric-footer">Awaiting processing</div>
            </a>
        </div>

    </div>

    {{-- =========================================================
         RECENT ACTIVITY AND MESSAGES
    ========================================================== --}}
    <div class="section-heading">
        <h4><i class="fa fa-history"></i> Business Activities &amp; Communications</h4>
        <small>Recent updates from your system</small>
    </div>

    <div class="row">

        <div class="col-lg-7 col-md-7">
            <div class="dashboard-panel">

                <div class="panel-heading">
                    <h4><i class="fa fa-clock-o"></i> Recent Activities</h4>
                    <a href="{{ url('activity-logs') }}">View all</a>
                </div>

                <div class="panel-content">

                    @forelse($recentActivities as $activity)

                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div>
                                <strong>
                                    {{ $activity->description ?? $activity->message ?? 'Business activity' }}
                                </strong>
                                <small>
                                    {{ $activity->user_name ?? $activity->causer_name ?? 'System' }}
                                    @if(!empty($activity->created_at))
                                        · {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                    @endif
                                </small>
                            </div>
                        </div>

                    @empty

                        <div class="empty-state">
                            <i class="fa fa-history"></i>
                            No recent activities available.
                        </div>

                    @endforelse

                </div>
            </div>
        </div>

        <div class="col-lg-5 col-md-5">
            <div class="dashboard-panel">

                <div class="panel-heading">
                    <h4><i class="fa fa-envelope-o"></i> Recent Messages</h4>
                    <a href="{{ url('messages') }}">
                        View all
                        @if($unreadMessages > 0)
                            <span class="badge">{{ $unreadMessages }}</span>
                        @endif
                    </a>
                </div>

                <div class="panel-content">

                    @forelse($recentMessages as $message)

                        <a href="{{ url('messages') }}" class="action-link">
                            <div class="message-item">

                                <div class="message-avatar">
                                    <i class="fa fa-user"></i>
                                </div>

                                <div style="min-width:0; flex:1;">
                                    <strong>
                                        {{ $message->sender_name ?? $message->name ?? 'System User' }}
                                    </strong>
                                    <p>
                                        {{ \Illuminate\Support\Str::limit($message->message ?? $message->body ?? '', 65) }}
                                    </p>
                                    <small>
                                        @if(!empty($message->created_at))
                                            {{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}
                                        @endif
                                    </small>
                                </div>

                                @if(isset($message->is_read) && !$message->is_read)
                                    <span class="status-pill">New</span>
                                @endif

                            </div>
                        </a>

                    @empty

                        <div class="empty-state">
                            <i class="fa fa-envelope-open-o"></i>
                            No recent messages available.
                        </div>

                    @endforelse

                </div>
            </div>
        </div>

    </div>

</div>

@endsection
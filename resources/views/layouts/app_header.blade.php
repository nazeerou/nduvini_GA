<?php 
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Module;

    $alert_qty = DB::table('products')
                    ->select('products.quantity', 'products.alert_qty')
                    ->whereColumn('products.quantity', '<=', 'products.alert_qty')
                    ->where('products.branch_id', session('current_branch_id', Auth::user()->branch_id))
                    ->count();
    $less_stocks = DB::table('products')
                    ->select('products.pid as id', 'products.quantity', 'products.model', 'items.item_name', 'brands.title', 'products.alert_qty')
                    ->join('items', 'items.id', 'products.product_id')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->whereColumn('products.quantity', '<=', 'products.alert_qty')
                    ->where('products.branch_id', session('current_branch_id', Auth::user()->branch_id))
                    ->get();

    $settings = DB::table('general_settings')
                    ->select('business_name', 'type')
                   ->get();

    $branches = DB::table('branches')
            ->select('branches.branch_name')
            ->where('branches.id', session('current_branch_id', Auth::user()->branch_id))
            ->get();

    $role = Role::findOrFail(Auth::user()->role_id);        
    $modules = Module::with(['permissions' => function ($query) {
        $query->whereNotNull('route')
              ->where('route', '!=', '');
    }])
    ->whereHas('permissions', function ($query) {
        $query->whereNotNull('route')
              ->where('route', '!=', '');
    })
    ->get();

    $assigned_permissions = $role->permissions->pluck('id')->toArray();

    $currentUser = Auth::user();
    $userInitials = strtoupper(
        mb_substr($currentUser->fname ?? '', 0, 1) .
        mb_substr($currentUser->lname ?? '', 0, 1)
    );
    if ($userInitials === '') { $userInitials = 'U'; }

    // Helper: is the given route path currently active?
    $currentPath = trim(request()->path(), '/');
    $isRouteActive = function ($route) use ($currentPath) {
        if (!is_string($route) || $route === '') return false;
        $routePath = trim($route, '/');
        return $currentPath === $routePath
            || str_starts_with($currentPath, $routePath . '/');
    };
    ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <link rel="shortcut icon" href="{{ asset('assets/images/logo_nduvini.jpeg') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Garage Assistant System (GAS)</title>
        <link href="{{ url('assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/datatables/buttons.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/datatables/fixedHeader.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/datatables/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/datatables/scroller.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/core.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/components.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/pages.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/menu.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/responsive.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
        <link href="{{ url('assets/plugins/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
        <link href="{{ url('assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/multiselect/css/multi-select.css') }}"  rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/select2/dist/css/select2.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ url('assets/plugins/select2/dist/css/select2-bootstrap.css') }}" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="{{ url('assets/plugins/morris/morris.css') }}">
        <script src="{{ url('assets/js/jquery.min.js') }}"></script>

        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

       <style>
    /* ============================================================
       GAS LAYOUT · Professional Theme
       ============================================================ */
    :root {
        --gas-primary: #2563eb;
        --gas-primary-dark: #1d4ed8;
        --gas-primary-light: #3b82f6;
        --gas-primary-soft: #eff6ff;
        --gas-primary-tint: #dbeafe;
        --gas-text: #0f172a;
        --gas-text-soft: #334155;
        --gas-muted: #64748b;
        --gas-muted-light: #94a3b8;
        --gas-border: #e2e8f0;
        --gas-border-soft: #f1f5f9;
        --gas-bg: #f5f7fb;
        --gas-white: #ffffff;
        --gas-success: #059669;
        --gas-warning: #d97706;
        --gas-danger: #dc2626;
        --gas-sidebar-width: 264px;
        --gas-topbar-height: 64px;
        --gas-shadow-sm: 0 1px 2px rgba(15,23,42,.04);
        --gas-shadow: 0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
        --gas-shadow-md: 0 8px 24px rgba(15,23,42,.10);
    }

    html, body { min-height: 100%; }
    body.fixed-left { overflow-x: hidden; background: var(--gas-bg); }
    #wrapper { min-height: 100vh; }

    /* ============================================================
       TOPBAR
       ============================================================ */
    .topbar {
        position: fixed !important;
        top: 0; left: 0; right: 0;
        height: var(--gas-topbar-height);
        z-index: 1001;
        background: var(--gas-white);
        border-bottom: 1px solid var(--gas-border);
        box-shadow: 0 1px 3px rgba(15,23,42,.04);
    }

    .topbar .topbar-left {
        width: var(--gas-sidebar-width);
        height: var(--gas-topbar-height);
        background: var(--gas-white);
        border-right: 1px solid var(--gas-border);
        display: flex; align-items: center;
        padding: 0 20px;
    }

    .topbar-left .logo {
        display: flex; align-items: center; gap: 10px;
        width: 100%;
        color: var(--gas-text) !important;
        font-size: 14px !important;
        font-weight: 700;
        line-height: 1.2;
        text-decoration: none;
    }

    .topbar-left .logo:hover { text-decoration: none; }

    .topbar-left .logo .logo-mark {
        width: 34px; height: 34px;
        border-radius: 9px;
        background: linear-gradient(135deg, var(--gas-primary-light), var(--gas-primary));
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(37,99,235,.25);
    }

    .topbar-left .logo .logo-text {
        display: flex; flex-direction: column;
        min-width: 0;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .topbar-left .logo .logo-text small {
        font-size: 10px;
        color: var(--gas-muted-light);
        font-weight: 500;
        letter-spacing: .06em;
        text-transform: uppercase;
        margin-top: 2px;
    }

    .topbar .navbar {
        margin-left: var(--gas-sidebar-width);
        min-height: var(--gas-topbar-height);
        margin-bottom: 0;
        border: 0;
        border-radius: 0;
        background: var(--gas-white) !important;
        padding: 0 22px;
    }

    .topbar .navbar .container { width: 100%; padding: 0; }
    .topbar .navbar-nav { margin: 0; }
    .topbar .navbar-nav > li {
        display: flex; align-items: center;
        min-height: var(--gas-topbar-height);
    }
    .topbar .navbar-left { float: left; }
    .topbar .navbar-right { float: right; }

    /* Menu toggle */
    .gas-menu-toggle {
        width: 40px; height: 40px;
        border: 1px solid var(--gas-border);
        border-radius: 10px;
        background: var(--gas-white);
        color: var(--gas-text-soft);
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        outline: none;
        transition: all .18s ease;
    }
    .gas-menu-toggle:hover, .gas-menu-toggle:focus {
        background: var(--gas-primary-soft);
        border-color: var(--gas-primary-tint);
        color: var(--gas-primary);
    }

    /* Page caption */
    .gas-page-caption {
        margin: 0 0 0 14px;
        color: var(--gas-text);
        font-size: 14px;
        font-weight: 600;
        line-height: 1.3;
    }
    .gas-page-caption small {
        display: block;
        font-size: 11px;
        color: var(--gas-muted);
        font-weight: 500;
        margin-top: 2px;
    }

    /* ============================================================
       TOPBAR · User + Notification
       ============================================================ */
    .gas-top-user {
        display: flex; align-items: center;
        gap: 10px;
        padding: 0 10px;
    }
    .gas-top-user-info {
        line-height: 1.35;
        text-align: right;
    }
    .gas-top-user-name {
        display: block;
        color: var(--gas-text);
        font-size: 13px;
        font-weight: 600;
    }
    .gas-top-user-branch {
        display: block;
        color: var(--gas-muted);
        font-size: 11px;
        margin-top: 2px;
    }
    .gas-top-user-branch i { font-size: 10px; margin-right: 3px; }
    .gas-top-avatar {
        width: 40px; height: 40px;
        flex: 0 0 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--gas-primary-light), var(--gas-primary));
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        box-shadow: 0 3px 8px rgba(37,99,235,.25);
        cursor: pointer;
        transition: transform .18s ease;
    }
    .gas-top-avatar:hover { transform: scale(1.05); }

    /* Notification bell */
    .gas-notification-link {
        position: relative;
        width: 42px; height: 42px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        color: var(--gas-muted) !important;
        font-size: 20px !important;
        line-height: 1 !important;
        background: #f8fafc;
        transition: all .18s ease;
    }
    .gas-notification-link:hover {
        color: var(--gas-primary) !important;
        background: var(--gas-primary-soft);
    }
    .gas-notification-link .badge {
        position: absolute;
        top: -2px; right: -2px;
        margin: 0 !important;
        min-width: 18px; height: 18px;
        padding: 3px 5px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        line-height: 12px;
        background: var(--gas-danger);
        box-shadow: 0 0 0 2px #fff;
    }

    /* ============================================================
       SIDEBAR
       ============================================================ */
    .left.side-menu {
        position: fixed !important;
        top: var(--gas-topbar-height) !important;
        bottom: 0; left: 0;
        width: var(--gas-sidebar-width) !important;
        z-index: 1000;
        background: var(--gas-white);
        border-right: 1px solid var(--gas-border);
        box-shadow: 3px 0 14px rgba(15,23,42,.025);
        transform: translateX(0);
        transition: transform .26s cubic-bezier(.4,0,.2,1);
    }

    .sidebar-inner {
        height: 100%;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    .sidebar-inner::-webkit-scrollbar { width: 6px; }
    .sidebar-inner::-webkit-scrollbar-thumb {
        background: #cbd5e1; border-radius: 3px;
    }

    /* User profile card */
    .gas-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px 20px 18px;
        border-bottom: 1px solid var(--gas-border-soft);
        background: linear-gradient(180deg, #fbfdff, #fff);
    }
    .gas-profile-avatar {
        width: 46px; height: 46px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        background: #e2e8f0;
        box-shadow: 0 0 0 2px #fff, 0 0 0 3px var(--gas-border);
    }
    .gas-profile-avatar img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
    }
    .gas-profile-info {
        flex: 1; min-width: 0;
    }
    .gas-profile-name {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--gas-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .gas-profile-role {
        display: block;
        font-size: 11px;
        color: var(--gas-muted);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .gas-profile-actions {
        display: flex; gap: 2px;
        margin-left: auto;
    }
    .gas-profile-actions a {
        width: 30px; height: 30px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--gas-muted);
        transition: all .18s ease;
        text-decoration: none;
    }
    .gas-profile-actions a:hover {
        background: var(--gas-primary-soft);
        color: var(--gas-primary);
        text-decoration: none;
    }

    /* Nav container */
    #sidebar-menu.gas-nav {
        padding: 4px 12px 22px 0;
    }

    /* .gas-nav-section { margin-bottom: 18px; } */

    .gas-nav-section-title {
        display: block;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--gas-muted-light);
        padding: 0 12px;
        margin: 4px 0 8px;
    }

    .gas-nav-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .gas-nav-item { margin: 1px 0; }

    .gas-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 4px 10px;
        border-radius: 9px;
        color: var(--gas-text-soft);
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: all .18s ease;
        position: relative;
        cursor: pointer;
    }
    .gas-nav-link:hover {
        background: var(--gas-border-soft);
        color: var(--gas-primary);
        text-decoration: none;
    }
    .gas-nav-link.active {
        background: var(--gas-primary-soft);
        color: var(--gas-primary);
        font-weight: 600;
    }
    .gas-nav-link.active::before {
        content: "";
        position: absolute;
        left: -12px;
        top: 50%;
        transform: translateY(-50%);
        width: 3px; height: 22px;
        background: var(--gas-primary);
        border-radius: 0 3px 3px 0;
    }

    .gas-nav-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--gas-border-soft);
        color: var(--gas-muted);
        font-size: 14px;
        flex-shrink: 0;
        transition: all .18s ease;
    }
    .gas-nav-icon i,
    .gas-nav-icon svg { font-size: 14px; color: inherit; }
    .gas-nav-link:hover .gas-nav-icon {
        background: var(--gas-primary-tint);
        color: var(--gas-primary);
    }
    .gas-nav-link.active .gas-nav-icon {
        background: var(--gas-primary);
        color: #fff;
        box-shadow: 0 3px 8px rgba(37,99,235,.25);
    }

    .gas-nav-text {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .gas-nav-arrow {
        font-size: 10px;
        color: var(--gas-muted-light);
        transition: transform .22s ease;
    }
    .gas-nav-group.open > .gas-nav-link .gas-nav-arrow {
        transform: rotate(90deg);
        color: var(--gas-primary);
    }

    /* Submenu */
    .gas-nav-sub {
        list-style: none;
        padding: 4px 0 2px 0;
        margin: 0;
        display: none;
        position: relative;
    }
    .gas-nav-sub::before {
        content: "";
        position: absolute;
        left: 27px;
        top: 8px;
        bottom: 8px;
        width: 1px;
        background: var(--gas-border);
        opacity: .8;
    }
    .gas-nav-sublink {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px 8px 44px;
        border-radius: 7px;
        color: var(--gas-muted);
        font-size: 12.5px;
        font-weight: 500;
        text-decoration: none;
        transition: all .15s ease;
        position: relative;
    }
    .gas-nav-sublink:hover {
        color: var(--gas-primary);
        background: #f8fafc;
        text-decoration: none;
    }
    .gas-nav-sublink.active {
        color: var(--gas-primary);
        background: var(--gas-primary-soft);
        font-weight: 600;
    }
    .gas-nav-bullet {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--gas-border);
        flex-shrink: 0;
        position: relative;
        z-index: 1;
        transition: background .15s ease;
        margin-left: -14px;
        margin-right: 8px;
    }
    .gas-nav-sublink.active .gas-nav-bullet,
    .gas-nav-sublink:hover .gas-nav-bullet {
        background: var(--gas-primary);
    }

    /* Empty state */
    .gas-nav-empty {
        padding: 20px 12px;
        font-size: 12px;
        color: var(--gas-muted-light);
        text-align: center;
    }

    /* ============================================================
       NOTIFICATION RIGHT BAR
       ============================================================ */
    .side-bar.right-bar {
        z-index: 1100;
        background: #fff;
        border-left: 1px solid var(--gas-border);
        box-shadow: -8px 0 24px rgba(15,23,42,.06);
    }
    .right-bar .gas-rb-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 22px;
        border-bottom: 1px solid var(--gas-border-soft);
    }
    .right-bar .gas-rb-head h4 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: var(--gas-text);
    }
    .right-bar .gas-rb-head .gas-rb-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px; height: 22px;
        padding: 0 7px;
        border-radius: 999px;
        background: var(--gas-danger);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
    }
    .right-bar .right-bar-toggle {
        color: var(--gas-muted);
        font-size: 22px;
        text-decoration: none;
        transition: color .15s ease;
    }
    .right-bar .right-bar-toggle:hover { color: var(--gas-text); }

    .gas-rb-list {
        list-style: none;
        padding: 8px 12px 20px;
        margin: 0;
    }
    .gas-rb-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        border-radius: 10px;
        transition: background .15s ease;
    }
    .gas-rb-item + .gas-rb-item {
        border-top: 1px solid var(--gas-border-soft);
    }
    .gas-rb-item:hover { background: var(--gas-border-soft); }
    .gas-rb-icon {
        width: 34px; height: 34px;
        border-radius: 9px;
        background: #fef3c7;
        color: #b45309;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .gas-rb-body { min-width: 0; flex: 1; }
    .gas-rb-title {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--gas-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .gas-rb-desc {
        display: block;
        font-size: 11.5px;
        color: var(--gas-muted);
        margin-top: 3px;
        line-height: 1.4;
    }
    .gas-rb-empty {
        text-align: center;
        padding: 40px 20px;
        color: var(--gas-muted-light);
    }
    .gas-rb-empty i {
        font-size: 30px;
        display: block;
        margin-bottom: 10px;
        opacity: .5;
    }
    .gas-rb-empty span { font-size: 12.5px; }

    /* ============================================================
       MAIN CONTENT
       ============================================================ */
    .content-page {
        margin-left: var(--gas-sidebar-width) !important;
        /* padding-top: var(--gas-topbar-height) !important; */
        /* min-height: 100vh; */
        background: var(--gas-bg);
    }
    .content-page > .content { padding-top: 18px; }

    /* ============================================================
       SIDEBAR OVERLAY (mobile)
       ============================================================ */
    .gas-sidebar-overlay {
        display: none;
        position: fixed;
        inset: var(--gas-topbar-height) 0 0;
        z-index: 999;
        background: rgba(15,23,42,.45);
        backdrop-filter: blur(2px);
    }

    /* ============================================================
       RESPONSIVE
       ============================================================ */
    @media (min-width: 768px) {
        .left.side-menu { transform: translateX(0) !important; }
        .gas-sidebar-overlay { display: none !important; }
    }
    @media (max-width: 767px) {
        :root { --gas-topbar-height: 60px; }
        .topbar { height: var(--gas-topbar-height); }
        .topbar .topbar-left { display: none; }
        .topbar .navbar {
            margin-left: 0;
            min-height: var(--gas-topbar-height);
            padding: 0 12px;
        }
        .topbar .navbar .container { padding: 0; }
        .topbar .navbar-nav > li { min-height: var(--gas-topbar-height); }
        .gas-page-caption { display: none; }
        .gas-top-user { gap: 8px; padding: 0 6px; }
        .gas-top-user-name {
            font-size: 12px;
            max-width: 125px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .gas-top-user-branch { font-size: 10px; }
        .gas-top-avatar {
            width: 36px; height: 36px;
            flex-basis: 36px;
            font-size: 12px;
        }
        .gas-notification-link {
            width: 36px; height: 36px;
            font-size: 18px !important;
        }
        .left.side-menu {
            top: var(--gas-topbar-height) !important;
            width: min(290px, 84vw) !important;
            transform: translateX(-105%);
            z-index: 1000;
        }
        body.gas-sidebar-open .left.side-menu { transform: translateX(0); }
        body.gas-sidebar-open .gas-sidebar-overlay { display: block; }
        .content-page {
            margin-left: 0 !important;
            padding-top: var(--gas-topbar-height) !important;
        }
        .content-page > .content { padding: 14px 0; }
    }
    @media (max-width: 420px) {
        .gas-top-user-info { max-width: 112px; }
        .gas-top-user-name { max-width: 112px; }
        .gas-top-user-branch {
            max-width: 112px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }

    /* ============================================================
       APPROVAL WORKFLOW UI (used by content pages)
       ============================================================ */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 11px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .status-badge i { font-size: 11px; }
    .status-pending  { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .status-approved { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .status-rejected { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .status-paid     { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

    .approval-actions {
        display: inline-flex;
        gap: 6px;
        flex-wrap: wrap;
        align-items: center;
    }
    .approval-actions .btn {
        font-size: 11px;
        padding: 5px 10px;
        font-weight: 600;
        border-radius: 5px;
    }
    .approval-hint {
        display: block;
        font-size: 11px;
        color: #6b7280;
        margin-top: 4px;
        line-height: 1.4;
    }
    .approval-hint strong { color: #374151; }

    .approval-summary {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 18px 22px;
        margin-bottom: 18px;
    }
    .approval-summary .ap-label {
        font-size: 11px; text-transform: uppercase;
        letter-spacing: .06em; color: #64748b;
        font-weight: 700; margin-bottom: 4px;
    }
    .approval-summary .ap-value { font-size: 15px; font-weight: 700; color: #0f172a; }
    .approval-summary .ap-amount {
        font-size: 22px; font-weight: 800;
        color: #1e40af;
        font-family: 'SFMono-Regular', Consolas, monospace;
    }

    .approval-timeline { position: relative; padding-left: 26px; margin-top: 12px; }
    .approval-timeline::before {
        content: ''; position: absolute;
        left: 8px; top: 6px; bottom: 6px;
        width: 2px; background: #e2e8f0;
    }
    .tl-item { position: relative; padding-bottom: 16px; }
    .tl-item:last-child { padding-bottom: 0; }
    .tl-item::before {
        content: ''; position: absolute;
        left: -22px; top: 4px;
        width: 12px; height: 12px;
        border-radius: 50%;
        background: #3b82f6;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #3b82f6;
    }
    .tl-item.tl-approved::before { background: #059669; box-shadow: 0 0 0 2px #059669; }
    .tl-item.tl-rejected::before { background: #dc2626; box-shadow: 0 0 0 2px #dc2626; }
    .tl-item.tl-paid::before     { background: #2563eb; box-shadow: 0 0 0 2px #2563eb; }
    .tl-action { font-weight: 700; color: #0f172a; font-size: 13px; }
    .tl-meta { font-size: 11.5px; color: #6b7280; margin-top: 2px; }
    .tl-note { font-size: 12.5px; color: #374151; margin-top: 4px; font-style: italic; }

/* ============================================================
   GAS PREMIUM NAVIGATION REFINEMENTS
   Profile actions, clearer brand identity and richer motion
   ============================================================ */
/* ============================================================
   GAS PREMIUM NAVIGATION REFINEMENTS
   Profile actions, clearer brand identity and richer motion
   ============================================================ */
.topbar .topbar-left {
    width: var(--gas-sidebar-width);
    min-width: var(--gas-sidebar-width);
    padding: 0 18px;
    background: #fff;
}
.topbar-left .logo {
    gap: 12px;
    min-width: 0;
}
.topbar-left .logo .logo-mark {
    width: 38px;
    height: 38px;
    border-radius: 11px;
    font-size: 16px;
    box-shadow: 0 5px 13px rgba(37,99,235,.22);
    transition: transform .28s ease, box-shadow .28s ease;
}
.topbar-left .logo:hover .logo-mark {
    transform: translateY(-2px) rotate(-5deg);
    box-shadow: 0 8px 18px rgba(37,99,235,.28);
}
.topbar-left .logo .logo-text {
    color: #172554;
    font-size: 13px !important;
    font-weight: 800;
    letter-spacing: -.015em;
    line-height: 1.25;
    overflow: visible;
    text-overflow: clip;
    white-space: normal;
}
.topbar-left .logo .logo-text small {
    color: #64748b;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .105em;
    line-height: 1.5;
    margin-top: 3px;
}

/* Clearer product/business title beside the menu button */
.gas-page-caption {
    max-width: min(38vw, 440px);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 14px;
    font-weight: 750;
    letter-spacing: -.015em;
    color: #172554;
}
.gas-page-caption small {
    color: #7b8798;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .035em;
}

/* Sidebar profile panel */
.gas-profile {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px 12px;
    padding: 18px 16px 15px;
    background: linear-gradient(145deg, #f8fbff 0%, #ffffff 72%);
    border-bottom: 1px solid #e8eef7;
}
.gas-profile::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #60a5fa, #2563eb);
    opacity: .9;
}
.gas-profile-avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #eaf2ff;
    box-shadow: 0 0 0 3px #fff, 0 0 0 4px #dbeafe, 0 7px 17px rgba(37,99,235,.12);
    transition: transform .3s cubic-bezier(.2,.8,.2,1), box-shadow .3s ease;
}
.gas-profile:hover .gas-profile-avatar {
    transform: translateY(-2px) scale(1.025);
    box-shadow: 0 0 0 3px #fff, 0 0 0 4px #bfdbfe, 0 10px 20px rgba(37,99,235,.16);
}
.gas-profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.gas-profile-info {
    flex: 1 1 calc(100% - 68px);
    min-width: 0;
}
.gas-profile-name {
    color: #172554;
    font-size: 13px;
    font-weight: 750;
    letter-spacing: -.01em;
}
.gas-profile-role {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 5px;
    padding: 3px 8px;
    border: 1px solid #dbeafe;
    border-radius: 20px;
    background: #eff6ff;
    color: #3765a5;
    font-size: 10px;
    font-weight: 650;
}
.gas-profile-role::before {
    content: "";
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #3b82f6;
}

/* Settings and logout now have both icon and readable label */
.gas-profile-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    flex: 1 1 100%;
    width: 100%;
    margin: 3px 0 0;
}
.gas-profile-actions .gas-profile-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    min-height: 36px;
    padding: 7px 10px;
    border: 1px solid #e4eaf2;
    border-radius: 9px;
    background: #fff;
    color: #526176;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .01em;
    text-decoration: none;
    box-shadow: 0 1px 2px rgba(15,23,42,.025);
    transition: transform .2s ease, background .2s ease, border-color .2s ease,
                color .2s ease, box-shadow .2s ease;
}
.gas-profile-actions .gas-profile-action i {
    font-size: 12px;
    transition: transform .22s ease;
}
.gas-profile-actions .gas-profile-settings:hover {
    color: #1d4ed8;
    border-color: #bfdbfe;
    background: #eff6ff;
    box-shadow: 0 5px 12px rgba(37,99,235,.09);
    transform: translateY(-1px);
}
.gas-profile-actions .gas-profile-logout:hover {
    color: #b42318;
    border-color: #fecaca;
    background: #fff5f5;
    box-shadow: 0 5px 12px rgba(220,38,38,.07);
    transform: translateY(-1px);
}
.gas-profile-actions .gas-profile-action:hover i {
    transform: scale(1.12);
}
.gas-profile-actions .gas-profile-action:focus {
    outline: 2px solid #93c5fd;
    outline-offset: 2px;
}


/* ============================================================
   SIDEBAR NAVIGATION · Professional refinements
   Left gutter, aligned accents, refined rhythm
   ============================================================ */

/* Container — balanced left/right padding gives the nav breathing room */
#sidebar-menu.gas-nav {
    padding: 8px 16px 28px 16px;
}

/* Section spacing — clean rhythm between module groups */
.gas-nav-section {
    margin-bottom: 6px;
}

.gas-nav-section-title {
    display: block;
    padding: 16px 12px 8px;
    margin: 0;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .13em;
    text-transform: uppercase;
    color: #94a3b8;
}

.gas-nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gas-nav-item {
    margin: 2px 0;
    animation: gasNavEnter .38s both;
}
.gas-nav-item:nth-child(2) { animation-delay: .025s; }
.gas-nav-item:nth-child(3) { animation-delay: .05s; }
.gas-nav-item:nth-child(4) { animation-delay: .075s; }
.gas-nav-item:nth-child(5) { animation-delay: .1s; }

/* ---- Main nav link ---- */
.gas-nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    min-height: 48px;
    border-radius: 10px;
    color: #334155;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    position: relative;
    overflow: hidden;
    transition: background .18s ease, color .18s ease;
}

.gas-nav-link:hover {
    background: #f1f5f9;
    color: #2563eb;
    text-decoration: none;
}

.gas-nav-link.active {
    background: #eff6ff;
    color: #1d4ed8;
    font-weight: 600;
}

/* Active state — accent bar sits cleanly inside the link (no negative positioning) */
.gas-nav-link.active::before {
    content: "";
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 22px;
    border-radius: 3px;
    background: linear-gradient(180deg, #3b82f6, #2563eb);
    box-shadow: 0 2px 6px rgba(37, 99, 235, .35);
}

/* Subtle shimmer on hover */
.gas-nav-link::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(110deg, transparent 20%, rgba(255,255,255,.5) 50%, transparent 80%);
    transform: translateX(-130%);
    transition: transform .55s ease;
    pointer-events: none;
}
.gas-nav-link:hover::after {
    transform: translateX(130%);
}

/* ---- Icon ---- */
.gas-nav-icon {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    color: #64748b;
    font-size: 14px;
    flex-shrink: 0;
    border: 1px solid rgba(148, 163, 184, .14);
    transition: background .18s ease, color .18s ease,
                transform .22s ease, box-shadow .22s ease;
}

.gas-nav-icon i,
.gas-nav-icon svg {
    font-size: 14px;
    color: inherit;
}

.gas-nav-link:hover .gas-nav-icon {
    background: #dbeafe;
    color: #2563eb;
    transform: translateY(-1px) scale(1.03);
}

.gas-nav-link.active .gas-nav-icon {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 3px 8px rgba(37, 99, 235, .28);
}

/* ---- Text & arrow ---- */
.gas-nav-text {
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.gas-nav-arrow {
    font-size: 10px;
    color: #cbd5e1;
    transition: transform .22s ease, color .18s ease;
}
.gas-nav-group.open > .gas-nav-link .gas-nav-arrow {
    transform: rotate(90deg);
    color: #2563eb;
}

/* ---- Submenu ---- */
.gas-nav-sub {
    list-style: none;
    padding: 4px 0;
    margin: 0;
    display: none;
    position: relative;
    overflow: hidden;
}

/* Vertical guide line — aligned with parent icon center (29px) */
.gas-nav-sub::before {
    content: "";
    position: absolute;
    left: 29px;
    top: 8px;
    bottom: 8px;
    width: 1px;
    background: #e2e8f0;
    opacity: .7;
}

.gas-nav-sublink {
    display: flex;
    align-items: center;
    padding: 9px 12px 9px 46px;
    margin: 1px 0;
    border-radius: 8px;
    color: #64748b;
    font-size: 12.5px;
    font-weight: 500;
    text-decoration: none;
    position: relative;
    transition: background .15s ease, color .15s ease, padding .2s ease;
}

.gas-nav-sublink:hover {
    background: #f8fafc;
    color: #2563eb;
    padding-left: 50px;
    text-decoration: none;
}

.gas-nav-sublink.active {
    background: #eff6ff;
    color: #1d4ed8;
    font-weight: 600;
}

/* Bullet — sits on the guide line, aligned with parent icon center */
.gas-nav-bullet {
    position: absolute;
    left: 26px;
    top: 50%;
    margin: 0;
    transform: translateY(-50%);
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #cbd5e1;
    transition: background .15s ease, transform .15s ease, box-shadow .15s ease;
}

.gas-nav-sublink:hover .gas-nav-bullet {
    background: #3b82f6;
    transform: translateY(-50%) scale(1.2);
}

.gas-nav-sublink.active .gas-nav-bullet {
    background: #2563eb;
    transform: translateY(-50%) scale(1.25);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
}

/* Subtle active accent underline */
.gas-nav-sublink.active::after {
    content: "";
    position: absolute;
    left: 46px;
    right: 12px;
    bottom: 0;
    height: 2px;
    border-radius: 2px;
    background: linear-gradient(90deg, #3b82f6, transparent);
    opacity: .35;
}

/* ---- Empty state ---- */
.gas-nav-empty {
    padding: 24px 12px;
    font-size: 12px;
    color: #94a3b8;
    text-align: center;
}
.gas-nav-empty i {
    display: block;
    font-size: 24px;
    margin-bottom: 8px;
    opacity: .5;
}

/* ---- Menu toggle micro-interaction ---- */
.gas-menu-toggle {
    box-shadow: 0 2px 5px rgba(15,23,42,.035);
}
.gas-menu-toggle:active {
    transform: scale(.94);
}


/* ============================================================
   RESPONSIVE TITLE & PROFILE CONTROLS
   ============================================================ */
@media (max-width: 767px) {
    .topbar .navbar {
        display: flex;
        align-items: center;
    }
    .topbar .navbar .container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .topbar .navbar .navbar-left,
    .topbar .navbar .navbar-right {
        display: flex;
        align-items: center;
        float: none;
    }
    .gas-page-caption {
        display: block !important;
        max-width: 38vw;
        margin-left: 9px;
        font-size: 11px;
        line-height: 1.2;
    }
    .gas-page-caption small {
        font-size: 8px;
        letter-spacing: 0;
    }
    .gas-top-user-info { max-width: 118px; }
    .gas-profile { padding: 18px 15px 14px; }
    .gas-profile-avatar {
        width: 50px;
        height: 50px;
    }

    /* Sidebar nav gets slightly tighter gutters on mobile so text isn't squeezed */
    #sidebar-menu.gas-nav {
        padding: 6px 12px 22px 12px;
    }
}

@media (max-width: 420px) {
    .gas-page-caption { max-width: 31vw; }
    .gas-page-caption small { display: none; }
    .gas-top-user-info { max-width: 95px; }
    .gas-top-user-name { max-width: 95px; }
    .gas-profile-actions .gas-profile-action {
        min-height: 35px;
        font-size: 10.5px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .gas-nav-item,
    .gas-profile-avatar,
    .gas-profile-actions .gas-profile-action,
    .gas-nav-link,
    .gas-nav-icon,
    .gas-nav-sublink,
    .gas-nav-bullet,
    .gas-menu-toggle {
        animation: none !important;
        transition: none !important;
    }
    .gas-nav-link::after { display: none; }
}

@keyframes gasNavEnter {
    from { opacity: 0; transform: translateX(-7px); }
    to   { opacity: 1; transform: translateX(0); }
}

#sidebar-menu ul li a i {
    display: inline-block;
    font-size: 16px;
    line-height: 17px;
    margin-left: 3px;
    margin-right: 0 !important;
    text-align: center;
    vertical-align: middle;
    width: 20px;
}
</style>
    </head>

    <body class="fixed-left">

        <div id="wrapper">
            <!-- ============================================================
                 TOP BAR
                 ============================================================ -->
            <div class="topbar">
                <div class="topbar-left">
                        <span class="logo-text">
                            <small>Garage Assistant System (GAS) </small>
                        </span>
                    </a>
                </div>

                <div class="navbar navbar-default" role="navigation">
                    <div class="container">
                        <ul class="nav navbar-nav navbar-left">
                            <li>
                                <button type="button" class="gas-menu-toggle" id="gasMenuToggle"
                                        aria-label="Toggle navigation" aria-expanded="false">
                                    <i class="fa fa-bars"></i>
                                </button>
                            </li>
                            <li>
                                <h4 class="gas-page-caption">
                                    {{ $settings[0]->business_name ?? 'Garage Assistant System' }}
                                    <small>Garage Assistant System  & Management </small>
                                </h4>
                            </li>
                        </ul>

                        <ul class="nav navbar-nav navbar-right">
                            <li class="gas-top-user">
                                <div class="gas-top-user-info">
                                    <span class="gas-top-user-name">
                                        {{ $currentUser->fname }} {{ $currentUser->lname }}
                                    </span>
                                    <span class="gas-top-user-branch">
                                        <i class="fa fa-map-marker-alt"></i>
                                        {{ $branches[0]->branch_name ?? 'Main Branch' }}
                                    </span>
                                </div>
                                <span class="gas-top-avatar" title="{{ $currentUser->fname }} {{ $currentUser->lname }}">
                                    {{ $userInitials }}
                                </span>
                            </li>
                            <li>
                                <div class="notification-box">
                                    <a href="javascript:void(0);" class="right-bar-toggle gas-notification-link"
                                       aria-label="View notifications" title="Low stock notifications">
                                        <i class="fa fa-bell"></i>
                                        <span class="badge badge-danger">{{ $alert_qty }}</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- ============================================================
                 LEFT SIDEBAR
                 ============================================================ -->
            <div class="left side-menu">
                <div class="sidebar-inner">

                    <!-- User profile card -->
                    <div class="gas-profile">
                        <div class="gas-profile-avatar">
                            @if(!Auth::user()->profile_img)
                                <img src="{{ url('assets/images/users/profile.jpg') }}" alt="User">
                            @else
                                <img src="{{ url('/attachments/'.Auth::user()->profile_img) }}" alt="User">
                            @endif
                        </div>
                        <div class="gas-profile-info">
                            <span class="gas-profile-name">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</span>
                            <span class="gas-profile-role">{{ $role->name ?? 'Staff' }}</span>
                        </div>
                        <div class="gas-profile-actions">
                            <a href="{{ url('/user-profile') }}" class="gas-profile-action gas-profile-settings">
                                <i class="fa fa-sliders"></i>
                                <span>My Profile</span>
                            </a>

                            <a href="{{ route('logout') }}" class="gas-profile-action gas-profile-logout"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-right-from-bracket"></i>
                                <span>Logout</span>
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav id="sidebar-menu" class="gas-nav">

                        {{-- ===== MAIN ===== --}}
                        <div class="gas-nav-section">
                            <span class="gas-nav-section-title">Main</span>
                            <ul class="gas-nav-list">
                                <li class="gas-nav-item">
                                    <a href="{{ url('/home') }}"
                                       class="gas-nav-link {{ $currentPath === 'home' || $currentPath === '' ? 'active' : '' }}">
                                        <span class="gas-nav-icon">
                                            <i class="fa fa-tachometer-alt"></i>
                                        </span>
                                        <span class="gas-nav-text">Dashboard</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        {{-- ===== MODULES ===== --}}
                        @php $hasVisibleModule = false; @endphp
                        @foreach ($modules as $module)
                            @php
                                $moduleChecked = collect($module->permissions)->pluck('id')->intersect($assigned_permissions)->isNotEmpty();
                                $moduleActive = false;
                                if ($moduleChecked) {
                                    foreach ($module->permissions as $perm) {
                                        if (in_array($perm->id, $assigned_permissions) && $isRouteActive($perm->route)) {
                                            $moduleActive = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            @if ($moduleChecked)
                                @php $hasVisibleModule = true; @endphp
                                <div class="gas-nav-section">
                                    @if ($loop->first)
                                        <span class="gas-nav-section-title">Modules</span>
                                    @endif
                                    <ul class="gas-nav-list">
                                        <li class="gas-nav-item gas-nav-group {{ $moduleActive ? 'open' : '' }}">
                                            <a href="javascript:void(0);"
                                               class="gas-nav-link gas-nav-toggle {{ $moduleActive ? 'active' : '' }}"
                                               aria-expanded="{{ $moduleActive ? 'true' : 'false' }}">
                                                <span class="gas-nav-icon">
                                                    {!! $module->icon_link !!}
                                                </span>
                                                <span class="gas-nav-text">{{ ucfirst($module->name) }}</span>
                                                <span class="gas-nav-arrow"><i class="fa fa-chevron-right"></i></span>
                                            </a>
                                            <ul class="gas-nav-sub" @if($moduleActive) style="display:block;" @endif>
                                                @foreach ($module->permissions as $permission)
                                                    @if (in_array($permission->id, $assigned_permissions))
                                                        <li>
                                                            <a href="{{ is_string($permission->route) ? url($permission->route) : '#' }}"
                                                               class="gas-nav-sublink {{ $isRouteActive($permission->route) ? 'active' : '' }}">
                                                                <span class="gas-nav-bullet"></span>
                                                                <span>{{ ucfirst(str_replace('-', ' ', $permission->name)) }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        @endforeach

                        @if (!$hasVisibleModule)
                            <div class="gas-nav-empty">
                                <i class="fa fa-lock"></i>
                                <div>No modules assigned</div>
                            </div>
                        @endif

                    </nav>
                    <!-- /#sidebar-menu -->

                </div>
            </div>
            <!-- /left side-menu -->

            <!-- Sidebar overlay (mobile) -->
            <div class="gas-sidebar-overlay" id="gasSidebarOverlay"></div>

            <!-- ============================================================
                 RIGHT NOTIFICATIONS PANEL
                 ============================================================ -->
            <div class="side-bar right-bar">
                <div class="gas-rb-head">
                    <h4>Notifications</h4>
                    <a href="javascript:void(0);" class="right-bar-toggle" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
                <div class="notification-list nicescroll">
                    @if($less_stocks->count() > 0)
                        <ul class="gas-rb-list">
                            @foreach($less_stocks as $s)
                                <li class="gas-rb-item">
                                    <span class="gas-rb-icon"><i class="fa fa-exclamation-triangle"></i></span>
                                    <div class="gas-rb-body">
                                        <span class="gas-rb-title">{{ $s->item_name }}</span>
                                        <span class="gas-rb-desc">
                                            {{ $s->title ?? 'Low stock' }} · Balance qty {{ $s->quantity }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="gas-rb-empty">
                            <i class="fa fa-bell-slash"></i>
                            <span>No low stock alerts</span>
                        </div>
                    @endif
                </div>
            </div>
            <!-- /right-bar -->

            <!-- ============================================================
                 CONTENT
                 ============================================================ -->
            <div class="content-page">
                <div class="content">
                    <div class="container">
                        @yield('content')
                    </div>
                </div>
            </div>

        </div>
        <!-- END wrapper -->

        <script>
            var resizefunc = [];
        </script>

        <script src="{{ url('assets/js/modernizr.min.js') }}"></script>
        <script src="{{ url('assets/js/jquery.min.js') }}"></script>
        <script src="{{ url('assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ url('assets/js/detect.js') }}"></script>
        <script src="{{ url('assets/js/fastclick.js') }}"></script>
        <script src="{{ url('assets/js/jquery.slimscroll.js') }}"></script>
        <script src="{{ url('assets/js/jquery.blockUI.js') }}"></script>
        <script src="{{ url('assets/js/waves.js') }}"></script>
        <script src="{{ url('assets/js/wow.min.js') }}"></script>
        <script src="{{ url('assets/js/jquery.nicescroll.js') }}"></script>
        <script src="{{ url('assets/js/jquery.scrollTo.min.js') }}"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script src="{{ url('assets/plugins/toastr/toastr.min.js') }}"></script>

        <!-- Datatables -->
        <script src="{{ url('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/dataTables.bootstrap.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/buttons.bootstrap.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/jszip.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/dataTables.fixedHeader.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/responsive.bootstrap.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/dataTables.scroller.min.js') }}"></script>

        <!-- Plugins -->
        <script src="{{ url('assets/plugins/switchery/switchery.min.js') }}"></script>
        <script src="{{ url('assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
        <script type="text/javascript" src="{{ url('assets/plugins/multiselect/js/jquery.multi-select.js') }}"></script>
        <script type="text/javascript" src="{{ url('assets/plugins/jquery-quicksearch/jquery.quicksearch.js') }}"></script>
        <script src="{{ url('assets/plugins/select2/dist/js/select2.min.js') }}" type="text/javascript"></script>
        <script src="{{ url('assets/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js') }}" type="text/javascript"></script>
        <script src="{{ url('assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js') }}" type="text/javascript"></script>
        <script src="{{ url('assets/plugins/moment/moment.js') }}"></script>
        <script src="{{ url('assets/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
        <script src="{{ url('assets/plugins/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
        <script src="{{ url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
        <script src="{{ url('assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ url('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}" type="text/javascript"></script>

        <script src="{{ url('assets/pages/datatables.init.js') }}"></script>
        <script src="{{ url('assets/plugins/jquery-knob/jquery.knob.js') }}"></script>

        <script src="{{ url('assets/js/jquery.core.js') }}"></script>
        <script src="{{ url('assets/js/jquery.app.js') }}"></script>

        <script src="{{ url('assets/plugins/bootstrap-sweetalert/sweet-alert.min.js') }}"></script>
        <script src="{{ url('assets/pages/jquery.sweet-alert.init.js') }}"></script>

        <script>
            jQuery(document).ready(function() {

                // advance multiselect start
                $('#my_multi_select3').multiSelect({
                    selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                    selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                    afterInit: function (ms) {
                        var that = this,
                            $selectableSearch = that.$selectableUl.prev(),
                            $selectionSearch = that.$selectionUl.prev(),
                            selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                            selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                        that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                            .on('keydown', function (e) {
                                if (e.which === 40) { that.$selectableUl.focus(); return false; }
                            });
                        that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                            .on('keydown', function (e) {
                                if (e.which == 40) { that.$selectionUl.focus(); return false; }
                            });
                    },
                    afterSelect: function () { this.qs1.cache(); this.qs2.cache(); },
                    afterDeselect: function () { this.qs1.cache(); this.qs2.cache(); }
                });

                $(".select2").select2();
                $(".select2-limiting").select2({ maximumSelectionLength: 2 });

                // Date Picker
                jQuery('#datepicker-autoclose').datepicker({
                    autoclose: true,
                    todayHighlight: true
                });

                // Time Picker
                jQuery('#timepicker').timepicker({ defaultTIme : false });
                jQuery('#timepicker2').timepicker({ showMeridian : false });
                jQuery('#timepicker3').timepicker({ minuteStep : 15 });

                // Color Picker
                $('.colorpicker-default').colorpicker({ format: 'hex' });
                $('.colorpicker-rgba').colorpicker();

                // Date Picker extras
                jQuery('#datepicker').datepicker();
                jQuery('#datepicker-inline').datepicker();

                // Maxlength
                $('input#defaultconfig').maxlength();
                $('textarea#textarea').maxlength({ alwaysShow: true });
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                $('#datatable').dataTable();
                $('#datatable-keytable').DataTable( { keys: true } );
                $('#datatable-responsive').DataTable();
                $('#datatable-scroller').DataTable( { ajax: "assets/plugins/datatables/json/scroller-demo.json", deferRender: true, scrollY: 380, scrollCollapse: true, scroller: true } );
                var table = $('#datatable-fixed-header').DataTable( { fixedHeader: true } );
            } );
        </script>

        <script>
            @if(Session::has('message'))
            toastr.options = { "closeButton" : true, "progressBar" : true }
            toastr.success("{{ session('message', 'title') }}");
            @endif
            @if(Session::has('error'))
            toastr.options = { "closeButton" : true, "progressBar" : true }
            toastr.error("{{ session('error') }}");
            @endif
            @if(Session::has('info'))
            toastr.options = { "closeButton" : true, "progressBar" : true }
            toastr.info("{{ session('info') }}");
            @endif
            @if(Session::has('warning'))
            toastr.options = { "closeButton" : true, "progressBar" : true }
            toastr.warning("{{ session('warning') }}");
            @endif
        </script>

        <!-- ============================================================
             SIDEBAR INTERACTION · accordion + mobile drawer
             ============================================================ -->
        <script>
            (function ($) {
                'use strict';

                function isMobile() {
                    return window.matchMedia('(max-width: 767px)').matches;
                }

                function closeGasSidebar() {
                    $('body').removeClass('gas-sidebar-open');
                    $('#gasMenuToggle').attr('aria-expanded', 'false');
                }

                // Mobile drawer toggle / desktop collapse
                $('#gasMenuToggle').on('click', function () {
                    if (isMobile()) {
                        var isOpen = $('body').toggleClass('gas-sidebar-open').hasClass('gas-sidebar-open');
                        $(this).attr('aria-expanded', isOpen ? 'true' : 'false');
                    } else {
                        $('body').toggleClass('gas-sidebar-collapsed');
                    }
                });

                $('#gasSidebarOverlay').on('click', closeGasSidebar);

                // Auto-close mobile drawer when a real link is clicked
                $('#sidebar-menu a').on('click', function () {
                    if (!isMobile()) return;
                    var href = $(this).attr('href');
                    if (href && href !== '#' && href !== 'javascript:void(0);') {
                        closeGasSidebar();
                    }
                });

                // ===== Accordion submenu =====
                $(document).on('click', '.gas-nav-toggle', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var $group   = $(this).closest('.gas-nav-group');
                    var $submenu = $group.find('.gas-nav-sub').first();
                    var isOpen   = $group.hasClass('open');

                    // Close siblings at the same level
                    $group.siblings('.gas-nav-group.open')
                          .removeClass('open')
                          .find('.gas-nav-sub').first().slideUp(200)
                          .end().find('.gas-nav-toggle').attr('aria-expanded', 'false');

                    if (isOpen) {
                        $group.removeClass('open');
                        $submenu.slideUp(200);
                        $(this).attr('aria-expanded', 'false');
                    } else {
                        $group.addClass('open');
                        $submenu.slideDown(200);
                        $(this).attr('aria-expanded', 'true');
                    }
                });

                // Open any pre-active groups on first render
                $('.gas-nav-group.open').each(function () {
                    $(this).find('.gas-nav-sub').first().show();
                });

                // Desktop collapse state styles
                $('<style>'
                  + '.gas-sidebar-collapsed .gas-sidebar,'
                  + '.gas-sidebar-collapsed .left.side-menu{transform:translateX(-100%)!important}'
                  + '.gas-sidebar-collapsed .content-page{margin-left:0!important}'
                  + '.gas-sidebar-collapsed .topbar .topbar-left{width:0;padding:0;overflow:hidden;border:0}'
                  + '.gas-sidebar-collapsed .topbar .navbar{margin-left:0}'
                  + '</style>').appendTo('head');

                $(window).on('resize', function () {
                    if (!isMobile()) {
                        closeGasSidebar();
                        $('body').removeClass('gas-sidebar-collapsed');
                    }
                });
            })(jQuery);
        </script>
        </body>
        </html>
@extends('layouts.app_header')

@section('content')
<style>
 input, select, textarea {
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
    /* width: 40px; */
}
.btn-sm {
    font-size: 10px;
    line-height: 1.5;
    border-radius: 3px;
}
table {
    border: 1px solid #ddd;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 4px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}

#labour_field {
    width: 10em;
    border-radius: 3px;
    border-color: #ddd;
}

#qty {
    width: 3em;
    border-color: #ddd;
}


/* Professional visual enhancement - styling only; form fields and behaviour remain unchanged. */
#formPost {
    color: #344054;
}

#formPost .panel-body {
    background: #f7f9fc !important;
    border: 1px solid #e5eaf1 !important;
    border-radius: 0 0 12px 12px;
    padding: 22px;
}

.panel-custom.panel-border {
    border: 1px solid #e4e9f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(28, 45, 72, .07);
}

.panel-custom .panel-heading {
    padding: 18px 24px;
    background: linear-gradient(110deg, #173b63 0%, #245b87 100%);
    border: 0;
}

.panel-custom .panel-heading .panel-title1 {
    margin: 0;
    color: #fff;
    font-size: 19px;
    font-weight: 700;
    letter-spacing: .2px;
}

#formPost #details,
#formPost #client_details,
#formPost #client_detail_one {
    background: #ddd !important;
    border: 1px solid #e4eaf1 !important;
    border-radius: 10px;
    /* padding: 18px !important; */
    /* margin: 0 0 18px; */
    box-shadow: 0 3px 12px rgba(31, 48, 70, .035);
}

#formPost .form-group {
    margin-bottom: 17px;
}

#formPost .control-label {
    display: inline-block;
    margin-bottom: 7px;
    color: #344054;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .25px;
}

#formPost .form-control {
    min-height: 46px;
    border: 1px solid #bad6f8;
    border-radius: 6px;
    background-color: #fff;
    color: #263548;
    box-shadow: none;
    transition: border-color .18s ease, box-shadow .18s ease;
}

#formPost textarea.form-control {
    min-height: 86px;
    resize: vertical;
}

#formPost .form-control:focus {
    border-color: #4384bd;
    box-shadow: 0 0 0 3px rgba(67, 132, 189, .12);
    outline: 0;
}

#formPost .select2-container .select2-selection--single {
    height: 38px;
    border: 1px solid #d8e0ea;
    border-radius: 6px;
}

#formPost .select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 11px;
}

#formPost .select2-container .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

#formPost table.table {
    width: 100%;
    margin-bottom: 12px;
    /* border: 1px solid #e1e7ef; */
    border-radius: 7px;
    /* border-collapse: separate; */
    border-spacing: 0;
    background: #fff;
    /* overflow: hidden; */
}

#formPost table.table thead th {
    padding: 11px 9px !important;
    background: #edf3f8;
    color: #29445f;
    border-top: 0 !important;
    border-bottom: 1px solid #dce5ee;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .35px;
    vertical-align: middle;
    white-space: nowrap;
}

#formPost table.table tbody td {
    padding: 8px !important;
    border-top: 1px solid #edf0f4;
    color: #475467;
    vertical-align: middle;
}

#formPost table.table tbody tr:hover {
    background: #f8fbfe;
}

#formPost table.table .form-control {
    min-width: 65px;
    min-height: 34px;
    padding: 6px 8px;
}

#formPost .radio {
    margin-top: 5px;
    margin-bottom: 8px;
}

#formPost .radio label {
    color: #475467;
    font-weight: 500;
}

#formPost .btn {
    border-radius: 6px;
    font-weight: 600;
    letter-spacing: .2px;
    transition: transform .15s ease, box-shadow .15s ease;
}

#formPost .btn-primary {
    padding: 10px 25px;
    background: #245b87;
    border-color: #245b87;
    box-shadow: 0 3px 8px rgba(36, 91, 135, .16);
}

/* ============================================================
   BORDERS — fields, selects, Select2 dropdowns
   Professional slate-blue accent, works with the B&W theme.
   ============================================================ */

/* ---------- Base input / textarea ---------- */
.form-control {
    height: auto;
    min-height: 42px;
    padding: 9px 14px;
    font-size: 14px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 9px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
}
.form-control:hover {
    border-color: #94a3b8;
    background-color: #fcfdff;
}
.form-control:focus {
    border-color: #2d5282;                                    /* brand slate-blue */
    background-color: #ffffff;
    box-shadow: 0 0 0 4px rgba(45, 82, 130, .14), 0 1px 2px rgba(15, 23, 42, .04);
    outline: none;
}
.form-control::placeholder {
    color: #94a3b8;
    font-size: 13px;
}
.form-control[disabled],
.form-control[readonly] {
    background: #f1f5f9;
    color: #475569;
    border-color: #e2e8f0;
    cursor: not-allowed;
}

/* Textarea — same border, taller padding */
textarea.form-control {
    min-height: 90px;
    padding: 10px 14px;
    line-height: 1.5;
}

/* ---------- Native select ---------- */
select.form-control {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    padding-right: 38px;
    border: 1px solid red;
    cursor: pointer;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 14px;
    transition: border-color .18s ease, box-shadow .18s ease,
                background-color .18s ease, background-image .18s ease;
}
select.form-control:hover {
    border-color: #94a3b8;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
}
select.form-control:focus {
    border-color: #2d5282;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232d5282' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    box-shadow: 0 0 0 4px rgba(45, 82, 130, .14), 0 1px 2px rgba(15, 23, 42, .04);
    outline: none;
}

/* ---------- Input-group (datepicker wrapper) ---------- */
.input-group .form-control {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}
.input-group .input-group-addon {
    background: linear-gradient(180deg, #2d5282, #1e3a5f);
    border: 1.5px solid #1e3a5f;
    border-left: none;
    border-top-right-radius: 9px;
    border-bottom-right-radius: 9px;
    color: #fff;
    transition: background .18s ease;
}
.input-group:focus-within .input-group-addon {
    background: linear-gradient(180deg, #1e3a5f, #142a45);
    border-color: #142a45;
}

/* ============================================================
   SELECT2 — dropdown boxes (single-select)
   ============================================================ */

/* Container */
.select2-container--default .select2-selection--single {
    height: 42px;
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 9px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    padding: 0 38px 0 14px;
    position: relative;
}
.select2-container--default .select2-selection--single:hover {
    border-color: #94a3b8;
    background: #fcfdff;
}
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open  .select2-selection--single {
    border-color: #2d5282;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(45, 82, 130, .14), 0 1px 2px rgba(15, 23, 42, .04);
}

/* Selected text */
.select2-container--default .select2-selection--single .select2-selection__rendered {
    padding: 0;
    line-height: 42px;
    font-size: 13.5px;
    font-weight: 500;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #94a3b8;
    font-weight: 500;
}

/* Arrow */
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px;
    width: 38px;
    right: 0;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #64748b transparent transparent transparent;
    border-width: 5px 4px 0 4px;
    margin-left: 0;
    transition: border-color .18s ease, transform .18s ease;
}
.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
    border-color: transparent transparent #2d5282 transparent;
    border-width: 0 4px 5px 4px;
}

/* Clear "×" */
.select2-container--default .select2-selection--single .select2-selection__clear {
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
    color: #94a3b8;
    font-size: 14px;
    z-index: 3;
    transition: color .15s ease;
}
.select2-container--default .select2-selection--single .select2-selection__clear:hover {
    color: #be123c;
}

/* ============================================================
   SELECT2 — Dropdown panel
   ============================================================ */
.select2-container--default .select2-dropdown {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    box-shadow:
        0 12px 28px -10px rgba(15, 23, 42, .18),
        0 4px 10px -6px rgba(15, 23, 42, .08);
    margin-top: 6px;
    overflow: hidden;
}
.select2-container--default.select2-container--open.select2-container--below .select2-selection--single {
    border-bottom-left-radius: 9px;
    border-bottom-right-radius: 9px;
}

/* Search box inside dropdown */
.select2-container--default .select2-search--dropdown {
    padding: 8px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.select2-container--default .select2-search--dropdown .select2-search__field {
    height: 36px;
    padding: 8px 12px;
    font-size: 13px;
    color: #0f172a;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 7px;
    outline: none;
    transition: border-color .15s ease, box-shadow .15s ease;
}
.select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #2d5282;
    box-shadow: 0 0 0 3px rgba(45, 82, 130, .12);
}

/* Options list */
.select2-container--default .select2-results > .select2-results__options {
    max-height: 280px;
    padding: 4px 0;
}
.select2-container--default .select2-results__option {
    padding: 10px 14px;
    font-size: 13px;
    color: #334155;
    transition: background .12s ease, color .12s ease;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: #2d5282;
    color: #ffffff;
}
.select2-container--default .select2-results__option[aria-selected="true"] {
    background: #eff6ff;
    color: #1e3a5f;
    font-weight: 600;
}
.select2-container--default .select2-results__option[aria-selected="true"]:hover {
    background: #dbeafe;
}

/* "No results found" */
.select2-container--default .select2-results__option--disabled {
    color: #94a3b8;
    background: transparent !important;
    font-style: italic;
    cursor: default;
}

/* ============================================================
   BORDERS — always visible, no hover needed
   Slate-blue accent, professional look, matches B&W theme.
   ============================================================ */

/* ---------- Base input / textarea ---------- */
.form-control {
    height: auto;
    min-height: 42px;
    padding: 9px 14px;
    font-size: 14px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1.5px solid #94a3b8;            /* ← VISIBLE slate-400 border, default state */
    border-radius: 9px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
    transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
}
.form-control:hover {
    border-color: #64748b;                  /* darker slate-500 on hover */
    background-color: #fcfdff;
}
.form-control:focus {
    border-color: #2d5282;                  /* brand slate-blue on focus */
    background-color: #ffffff;
    box-shadow: 0 0 0 4px rgba(45, 82, 130, .18), 0 1px 2px rgba(15, 23, 42, .05);
    outline: none;
}
.form-control::placeholder {
    color: #94a3b8;
    font-size: 13px;
}
.form-control[disabled],
.form-control[readonly] {
    background: #f1f5f9;
    color: #475569;
    border-color: #cbd5e1;
    cursor: not-allowed;
}

/* Textarea — same border, taller padding */
textarea.form-control {
    min-height: 90px;
    padding: 10px 14px;
    line-height: 1.5;
}

/* ---------- Native select ---------- */
select.form-control {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    padding-right: 38px;
    cursor: pointer;
    border: 1.5px solid #94a3b8;            /* ← VISIBLE border, default state */
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 14px;
    transition: border-color .18s ease, box-shadow .18s ease,
                background-color .18s ease, background-image .18s ease;
}
select.form-control:hover {
    border-color: #64748b;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23334155' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
}
select.form-control:focus {
    border-color: #2d5282;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232d5282' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    box-shadow: 0 0 0 4px rgba(45, 82, 130, .18), 0 1px 2px rgba(15, 23, 42, .05);
    outline: none;
}

/* ---------- Input-group (datepicker wrapper) ---------- */
.input-group .form-control {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}
.input-group .input-group-addon {
    background: linear-gradient(180deg, #2d5282, #1e3a5f);
    border: 1.5px solid #1e3a5f;
    border-left: none;
    border-top-right-radius: 9px;
    border-bottom-right-radius: 9px;
    color: #fff;
    transition: background .18s ease;
}
.input-group:focus-within .input-group-addon {
    background: linear-gradient(180deg, #1e3a5f, #142a45);
    border-color: #142a45;
}

/* ============================================================
   SELECT2 — dropdown boxes (single-select)
   ============================================================ */

/* Container — VISIBLE border always */
.select2-container--default .select2-selection--single {
    height: 42px;
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 1.5px solid #94a3b8;            /* ← VISIBLE border, default state */
    border-radius: 9px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
    transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    padding: 0 38px 0 14px;
    position: relative;
}
.select2-container--default .select2-selection--single:hover {
    border-color: #64748b;
    background: #fcfdff;
}
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open  .select2-selection--single {
    border-color: #2d5282;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(45, 82, 130, .18), 0 1px 2px rgba(15, 23, 42, .05);
}

/* Selected text */
.select2-container--default .select2-selection--single .select2-selection__rendered {
    padding: 0;
    line-height: 42px;
    font-size: 13.5px;
    font-weight: 500;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #94a3b8;
    font-weight: 500;
}

/* Arrow */
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px;
    width: 38px;
    right: 0;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #475569 transparent transparent transparent;
    border-width: 5px 4px 0 4px;
    margin-left: 0;
    transition: border-color .18s ease;
}
.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
    border-color: transparent transparent #2d5282 transparent;
    border-width: 0 4px 5px 4px;
}

/* Clear "×" */
.select2-container--default .select2-selection--single .select2-selection__clear {
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
    color: #94a3b8;
    font-size: 14px;
    z-index: 3;
    transition: color .15s ease;
}
.select2-container--default .select2-selection--single .select2-selection__clear:hover {
    color: #be123c;
}

/* ============================================================
   SELECT2 — Dropdown panel (also always visible)
   ============================================================ */
.select2-container--default .select2-dropdown {
    background: #ffffff;
    border: 1.5px solid #94a3b8;            /* ← VISIBLE border on dropdown panel */
    border-radius: 10px;
    box-shadow:
        0 12px 28px -10px rgba(15, 23, 42, .20),
        0 4px 10px -6px rgba(15, 23, 42, .10);
    margin-top: 6px;
    overflow: hidden;
}
.select2-container--default.select2-container--open.select2-container--below .select2-selection--single {
    border-bottom-left-radius: 9px;
    border-bottom-right-radius: 9px;
}

/* Search box inside dropdown */
.select2-container--default .select2-search--dropdown {
    padding: 8px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.select2-container--default .select2-search--dropdown .select2-search__field {
    height: 36px;
    padding: 8px 12px;
    font-size: 13px;
    color: #0f172a;
    background: #ffffff;
    border: 1.5px solid #94a3b8;            /* ← VISIBLE border on search input */
    border-radius: 7px;
    outline: none;
    transition: border-color .15s ease, box-shadow .15s ease;
}
.select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #2d5282;
    box-shadow: 0 0 0 3px rgba(45, 82, 130, .16);
}

/* Options list */
.select2-container--default .select2-results > .select2-results__options {
    max-height: 280px;
    padding: 4px 0;
}
.select2-container--default .select2-results__option {
    padding: 10px 14px;
    font-size: 13px;
    color: #334155;
    transition: background .12s ease, color .12s ease;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: #2d5282;
    color: #ffffff;
}
.select2-container--default .select2-results__option[aria-selected="true"] {
    background: #eff6ff;
    color: #1e3a5f;
    font-weight: 600;
}
.select2-container--default .select2-results__option[aria-selected="true"]:hover {
    background: #dbeafe;
}

/* "No results found" */
.select2-container--default .select2-results__option--disabled {
    color: #94a3b8;
    background: transparent !important;
    font-style: italic;
    cursor: default;
}

/* ============================================================
   Compact — form controls INSIDE tables
   ============================================================ */
.table .form-control {
    min-height: 34px;
    padding: 4px 10px;
    font-size: 13px;
    border-radius: 7px;
    border-width: 1.5px;
    border-color: #94a3b8;                  /* ← visible border in tables too */
}
.table .form-control.qty,
.table .form-control.discount {
    text-align: center;
}
.table .form-control:hover { border-color: #64748b; }
.table .form-control:focus {
    border-color: #2d5282;
    box-shadow: 0 0 0 3px rgba(45, 82, 130, .16);
}

/* ============================================================
   Reduced motion
   ============================================================ */
@media (prefers-reduced-motion: reduce) {
    .form-control,
    select.form-control,
    .input-group .input-group-addon,
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--single .select2-selection__arrow b,
    .select2-container--default .select2-results__option {
        transition-duration: .01ms !important;
    }
}

/* ============================================================
   Compact — form controls INSIDE tables
   ============================================================ */
.table .form-control {
    min-height: 34px;
    padding: 4px 10px;
    font-size: 13px;
    border-radius: 7px;
    border-width: 1.5px;
}
.table .form-control.qty,
.table .form-control.discount {
    text-align: center;
}

/* ============================================================
   Reduced motion
   ============================================================ */
@media (prefers-reduced-motion: reduce) {
    .form-control,
    select.form-control,
    .input-group .input-group-addon,
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--single .select2-selection__arrow b,
    .select2-container--default .select2-results__option {
        transition-duration: .01ms !important;
    }
}

#formPost .btn-primary:hover,
#formPost .btn-primary:focus {
    background: #19476f;
    border-color: #19476f;
    box-shadow: 0 5px 12px rgba(36, 91, 135, .22);
    transform: translateY(-1px);
}

#formPost .btn-sm {
    min-width: 30px;
    min-height: 28px;
    font-size: 11px;
    border-radius: 5px;
}

#formPost .input-group-addon {
    border-radius: 0 6px 6px 0;
}

#formPost .step-indicator {
    margin: 4px 0 12px;
}

#formPost #Table,
#formPost section.container,
#formPost section.container-fluid {
    width: 100%;
    padding-left: 0;
    padding-right: 0;
}

@media (max-width: 767px) {
    #formPost .panel-body {
        padding: 12px !important;
    }

    #formPost #details,
    #formPost #client_details,
    #formPost #client_detail_one {
        padding: 12px !important;
        margin-bottom: 13px;
    }

    #formPost .row > [class*="col-"] {
        margin-bottom: 5px;
    }

    #formPost section {
        overflow-x: auto;
    }

    #formPost table.table {
        min-width: 620px;
    }

    .panel-custom .panel-heading {
        padding: 15px 17px;
    }
}


</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="{{ url('/home') }}">Home</a> 
            <a class="step" href="#"> Estimates   </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title1"> Create Estimates </h3>
              
            </div>
            <form id="formPost" method="POST" action="{{ url('/estimate/create') }}" enctype="multipart/form-data"> 
                  @csrf
            <div class="panel-body" style="background: #fff; border: 1px solid #ddd;">
            <div class="display_message"></div>
            <div id="details" style="padding: 0 10px 0 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Price List (Client) </label>
                                <select name="client[]" class="form-control select2" id="client" >
                                <option value=""> --- General Price --- </option>
                                @foreach($clients as $client)
                                   <option value="{{ $client->id }}">  {{ $client->client_name }}  </option>
                                 @endforeach
                                </select>
                             </div> 
                    </div>
         <div class="col-md-1"> </div>
        <div class="col-md-7"></div>
     </div>
             <div id="details" style="padding: 0 10px 0 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Add Parts   * </label>
                                <select name="product_id[]" class="form-control select2" id="product_id" required>
                                <option value=""> --- Select Spare Part --- </option>
                                 @foreach($products as $product)
                                   <option value="{{ $product->id }}"> {{ $product->item_name }}  - {{ $product->model }} | {{ $product->part_number }}  :  ({{ $product->quantity }}) </option>
                                 @endforeach
                                </select>
                             </div> 
                    </div>
         <!-- <div class="col-md-1"> </div> -->
        <div class="col-md-8">
      
        <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}" class="form-control">
        <input type="hidden" name="quantity[]" id="quantity" class="form-control">

        <section id='Table' class='container-fluid'>
        <table class="table table-striped table-bordered" style="background: #fff;" id="myTable1">
            <thead>
                <tr>
                    <th>#</th>
                    <th width="300px">Part Name</th>
                    <th width="100px">D &nbsp; (%) </th>
                    <th width="100px">Unit </th>
                    <th width="10">Qty</th>
                    <th> SubTotal </th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='dataRows'></tbody> 
            <tfooter style="display: none;" >
             <tr>
                 <td style="text-align: right" colspan="5"> </td>
                 <td><input disabled id="subtotal_" name="total_charge" class="form-control subtotal_" value="0" type="text" /></td> <td></td><td></td>
               </tr>
              </tfooter>
               </table>
            </section>
            </div>
            </div>  
           </div>

           <div id="details" style="padding: 0px 10px 0px 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Add Labour   </label>
                                <select name="labour[]" class="form-control select2" id="labour">
                                <option value=""> --- Select Labour --- </option>
                                 @foreach($labours as $l)
                                   <option value="{{ $l->id }}"> {{ strtoupper($l->labour) }} </option>
                                 @endforeach
                                </select>
                             </div> 
                    </div>
         <!-- <div class="col-md-1"></div> -->
        <div class="col-md-8">
        <section id='Table' class='container'>
        <table class="table table table-bordered" style="background: #fff;" id="myTable2">
            <thead>
                <tr class="target_3">
                    <th>#</th>
                    <th width="300px">Labour </th>
                    <th width="100px">Unit Price </th>
                    <th width="20">Rate/Hr</th>
                    <th width="30">Sub Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='dataRows2'></tbody>
              </table>
            </section>
            </div>
          </div>  
          <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-11">
          <table class="table" id="myTableOther">
              <tr class="target_2">
                 <td><div class="col-md-2"></div></td>
                    <td>
                        <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Labour Charge  </label>
                            <input type="text" name="labour_name[]"  class="form-control" value="LABOUR CHARGE" readonly>
                            </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Charges (Total Materials) </label>
                            <input type="number" name="unit_charge[]" class="form-control charge" placeholder="Auto from parts total" readonly>
                            </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Percent (%) </label>
                            <input type="text" name="labour_qty[]"  class="form-control qty" placeholder="Enter  Eg. 20"> 
                            <input type="hidden" name="rate[]" value="%" size="2">
                            <input type="hidden" name="total_amount[]"  class="form-control subtotal"> 
                            </div> 
                    </div> 
                  </td>
            </tr>
       </div>
       </table>
     </div>
</div>

       <div id="client_details" style="padding: 10px; border: 0px solid #ddd;">
                <div class="row">
                <div class="col-md-1"></div> 
                <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Vehicle Reg.  *</label>
                            <input type="text" name="vehicle_reg"  class="form-control" id="vehicle_reg" placeholder="Vehicle Reg..     Eg. T390 EEE" required>
                       </div>  
                    </div> 
                <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Client Name  </label>
                        <select name="client_name" class="form-control select2" id="client_name">
                                <option value=""> --- Select Client Name --- </option>
                                 @foreach($clients as $client)
                                   <option value="{{ $client->id }}">  {{ $client->client_name }}  - ({{ $client->place }}) </option>
                                 @endforeach
                                </select>
                        </div>
                    </div>                      
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Customer (Option) </label>
                       <textarea class="form-control" id="customer" name="customer_name" rows="4" placeholder="Enter Customer Name"></textarea>
                         </div>  
                    </div>       
              </div>
        <div class="row">
                <div class="col-md-1"></div>         
                <div class="col-md-10">
                <table id="datatable1" class="table table table-bordered">
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
                         <tbody id="customer_data" class="tbody">
                        </tbody>
                    </table>
                </div>
        </div>
</div></div>
        <div id="client_detail_one" style="padding: 10px; border: 1px solid #ddd; background: #552a2a;">
                <div class="row">
                <div class="col-md-1"></div>         
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Make *</label>
                                <input type="text" name="make"  class="form-control" placeholder="Enter Make" id="make" required>
                       </div>  
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Model *</label>
                                <input type="text" name="model"  class="form-control" placeholder="Enter Model " id="model" required>
                       </div>  
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Chassis No.   *</label>
                                <input type="text" name="chassis"  class="form-control" placeholder="Enter Chassis No." id="chassis" required>
                       </div>  
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label"> Milleage   </label>
                                <input type="text" name="milleage"  class="form-control" placeholder=" Eg. 799 km " id="milleage">
                       </div>  
                    </div>
                    <div class="col-md-2">
                       <div class="form-group">
                        <label for="inputEmail3" class="control-label">Year of Registration   *</label>
                                <input type="text" name="registration_year"  class="form-control" placeholder="Enter Year of Registration" id="registration_year" required>
                          </div>  
                       </div>
                     </div>
                   <div class="row">
                <div class="col-md-1"></div>         

               <div class="col-md-3">
                <div class="form-group">
                <label for="inputEmail3" class="control-label">VAT </label>
                 </div>
                        <div class="radio radio-info radio-inline">
                                        <input type="radio" name="vat_amount" id="with_vat" value="0.18" name="radioInline" checked required>
                                        <label for="inlineRadio1"> With VAT </label>
                                    </div>
                                    <div class="radio radio-inline">
                                        <input type="radio" name="vat_amount" id="without_vat" value="0" name="radioInline" required>
                                        <label for="inlineRadio2"> Without VAT </label>
                                  </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                            <label for="inputEmail3" class="control-label">TEMESA Fee ? </label>
                         </div>
                        <div class="radio radio-info radio-inline">
                                <input type="radio" name="temesa_fee" value="0.08" name="radioInline"  required>
                                <label for="inlineRadio1">Yes</label>
                            </div>
                            <div class="radio radio-inline">
                                <input type="radio" name="temesa_fee" value="0" name="radioInline" checked required>
                                <label for="inlineRadio2">No</label>
                        </div>
                       </div>               
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date  *</label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Date " id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-primary b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
                       </div>
                    </div>
                   </div>
              </div>
              <div class="row">
                <div class="col-md-1"></div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> </label>
                                <button type="submit" class="btn btn-primary btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="save_btn1">Save Estimate </button>
                        </div>
                    </div>
                </div>
              </div>
              </form> <!-- End form -->
        </div>
    </div>
</div>
<!-- END  -->

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="client_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Client Information </h4>
                    </div>
                    <div class="modal-body">
                    <div class="row m-t-0">
                   <div class="col-md-12">
                        <table class="table table-striped m-10">
                            <thead>
                            <th>Tick </th>
                            <th>Description </th>
                            <th></th>
                            <th>Date</th>
                            <th>Action </th>
                           </thead>
                           <tbody id="tabody"></tbody>
                          </table>
                        </div>
                    </div>
                </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


        <!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="alert_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Client Information </h4>
                    </div>
                    <div class="modal-body">
                    <div class="row m-t-0">
                   <div class="col-md-12">
                                 <h4> No Record Found! <strong>Please enter Information</strong></h4>
                        </div>
                    </div>
                </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

<script>

 // Keep the parts subtotal and Labour Charge base amount synchronized.
 function recalculatePartsTotal() {
     var partsTotal = 0;

     $("#myTable1 .target").each(function() {
         var qty = parseFloat($(this).find(".qty").val()) || 0;
         var discount = parseFloat($(this).find(".discount").val()) || 0;
         var price = parseFloat($(this).find(".selling_price").val()) || 0;

         var discountValue = (discount / 100) * price;
         var lineTotal = qty * (price - discountValue);

         $(this).find(".total_").val(lineTotal.toFixed(2));
         partsTotal += lineTotal;
     });

     $("#subtotal_").val(partsTotal.toFixed(2));
     $("#myTableOther .charge").val(partsTotal.toFixed(2));
     recalculateGeneralLabourCharge();
 }

 function recalculateGeneralLabourCharge() {
     var partsTotal = parseFloat($("#myTableOther .charge").val()) || 0;
     var percentage = parseFloat($("#myTableOther .qty").val()) || 0;
     var labourCharge = (percentage / 100) * partsTotal;

     $("#myTableOther .subtotal").val(labourCharge.toFixed(2));
 }

 $(function() {
     $("#myTable1").on("input change", ".qty, .discount, .selling_price", recalculatePartsTotal);
     $("#myTableOther").on("input change", ".qty", recalculateGeneralLabourCharge);
     recalculatePartsTotal();
 });
$("#myTable2").keyup(function(event) {
        var sub = 0;
        $("#myTable2 .target_3").each(function() {

            var qty = parseFloat($(this).find(".qty").val());
            var price = parseFloat($(this).find(".charge").val());

            var subtotal = (qty * price);
            console.log(subtotal);
            
            $(this).find(".subtotal_other").val(subtotal);
            console.log(subtotal);
            if(!isNaN(subtotal))
            sub+=subtotal;
        });
        // $(".subtotal_other").val(sub);
     });


    $(document).ready(function () {
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
       });

       $("#myTable1").on('click','.btnDelete',function(){
         $(this).closest('tr').remove();
         recalculatePartsTotal();
      });
      $("#myTable2").on('click','.btnDelete',function(){
         $(this).closest('tr').remove();
      });

      $("#labour").on('change', function () {
            var labour_id = $("#labour").val();
            $.ajax({
                type: "GET",
                url: "labours/details/"+labour_id,
                data: { labour_id: labour_id },
                success: function(response){
                    $("#dataRows2").show();
                    var html = '<tr class="target_3">';
                    html += '<td width="10px;">'+'<input type="hidden" name="labour_name[]" value="'+response[0].labour+'">'+'</td>';
                    html += '<td width="100px">'+response[0].labour+'</td>';
                    html += '<td>'+'<input type="text" name="unit_charge[]" class="form-control charge" value="'+response[0].charge+'">'+'</td>';
                    html += '<td>'+'<input type="text" name="labour_qty[]"  class="form-control qty" size="5"  required><input type="hidden" name="rate[]" value="hr" size="2">'+'</td>';
                    html += '<td width="70px;">'+'<input type="text" class="form-control subtotal_other" name="total_amount[]" readonly value="0">'+'</td>';
                    html += '<td width="20px;">'+'<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>'+'</td></tr>';
                    $('#dataRows2').append(html);
                }
            });
        });

        $('#client').on('change', function() {
        var client = $('#client').val();
            $.ajax({
                type: "GET",
                url: "products/fetch-client-prices/"+client,
                data: { client: client },
                success: function(response){
                    $('#product_id').html(response);
                }
            });
        });

        $("#product_id").on('change', function () {
            var product_id = $("#product_id").val();
            $.ajax({
                type: "GET",
                url: "products/price-lists/details/"+product_id,
                data: { product_id: product_id },
                success: function(response){
                    $("#dataRows").show();
                    var html = '<tr class="target">';
                    html += '<td width="10px">'+'<input type="hidden" name="id[]" id="product_id" value="'+response[0].pid+'">'+'</td>';
                    html += '<td width="80px">'+response[0].item_name+'</td>';
                    html += '<td>'+'<input type="text" name="discount[]"  class="form-control discount" size="5" value="0">'+'</td>';
                    html += '<td>'+'<input type="text" name="selling_price[]" class="form-control selling_price" value="'+response[0].sale_price+'">'+'</td>';
                    html += '<td width="50px;">'+'<input type="text" name="qty[]" size="15" class="form-control qty" required>'+'</td>';
                    html += '<td>'+'<input type="text" class="form-control total_" name="total_charge[]" readonly value="0">'+'</td>';
                    html += '<td>'+'<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>'+'</td></tr>';
                    $('#dataRows').append(html);
                }
            });
        });


        $("#client_id").on('change', function () {
            var client_id = $("#client_id").val();
            console.log(client_id);
            $.ajax({
                type: "GET",
                url: "../clients/client-details/"+client_id,
                data: { client_id: client_id },
                success: function(response){      
                if(response != '') {
                $('#client_modal').modal('show');
                 console.log(response[0].client_name);
                var res = '';
                $.each(response, function (key, value) {
                        res +='<tr>'+
                        '<td>'+'<input type="checkbox" name="check" id="reg" value="'+response[0].vehicle_reg+'">'+'</td>'+
                        '<td>'+value.vehicle_reg+'</td>'+'<td>'+value.make+' '+'|'+value.model+'</td>'+'<td>'+value.created_date+'</td>'+
                        '<td>'+'<input type="submit" class="btn-sm btn-primary btn-rounded waves-effect waves-light" id="save_" value="submit">'+'</td>'+'<td>'+
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


    $("#vehicle_reg").keyup(function(event) {
       var vehicle = $('#vehicle_reg').val();
        $.ajax({
                type: "GET",
                url: "../clients/vehicle_details/"+vehicle,
                data: { vehicle: vehicle },
                success: function(response){ 
                    if (response) {

                        // $("#client_name").append('<option>Select Client</option>');
                    $.each(response, function (key, value) {
                     $("#client_name").empty();
                     $("#client_name").append('<option value="'+value["id"]+'">'
                     + value["client_name"] +
                      '</option>');
                    });
                } else {
                    $("#client_name").empty();
                }
                  console.log(response[0].id);
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

});

// ===== ADDED: Numeric input validation =====
// Prices and quantities must be valid numbers only (no letters, no commas).
// An error is shown inside the existing .display_message container.
$(function () {
    var numericSelector = '#myTable1 .qty, #myTable1 .discount, #myTable1 .selling_price, ' +
                          '#myTable2 .qty, #myTable2 .charge, ' +
                          '#myTableOther .qty';

    function showNumericError(msg) {
        $('.display_message').html(
            '<div class="alert alert-danger" style="padding:10px 14px;border:1px solid #f1aeb5;' +
            'background:#f8d7da;color:#842029;border-radius:6px;margin-bottom:10px;">' +
            '<i class="fa fa-exclamation-triangle"></i> ' + msg + '</div>'
        );
    }

    function clearNumericError() {
        $('.display_message').empty();
    }

    function sanitizeNumber(val) {
        // Remove commas, then strip anything that is not a digit or a dot.
        val = val.replace(/,/g, '');
        val = val.replace(/[^\d.]/g, '');
        // Keep only the first decimal point.
        var parts = val.split('.');
        if (parts.length > 2) {
            val = parts[0] + '.' + parts.slice(1).join('');
        }
        return val;
    }

    function isValidNumericString(val) {
        // Must be digits with at most one decimal point, and parseable.
        return /^\d*\.?\d*$/.test(val) && !isNaN(parseFloat(val));
    }

    // Live sanitize + error feedback while typing/pasting.
    $(document).on('input', numericSelector, function () {
        var $el = $(this);
        var raw = $el.val();

        if (raw === '') {
            clearNumericError();
            $el.css('border-color', '');
            return;
        }

        var hasInvalidChars = /[^\d.]/.test(raw);
        var hasMultipleDots = raw.split('.').length > 2;

        if (hasInvalidChars || hasMultipleDots) {
            showNumericError('Invalid input. Please enter numbers only (no letters, commas or special characters).');
            $el.css('border-color', '#dc3545');
        } else {
            clearNumericError();
            $el.css('border-color', '');
        }

        var cleaned = sanitizeNumber(raw);
        if (cleaned !== raw) {
            $el.val(cleaned);
            // Keep totals in sync after auto-cleaning.
            $el.trigger('change');
            $el.trigger('keyup');
        }
    });

    // Catch stray values on blur (e.g. someone ends on just ".").
    $(document).on('blur', numericSelector, function () {
        var $el = $(this);
        var val = $el.val();
        if (val === '' || val === '.') {
            return;
        }
        if (!isValidNumericString(val)) {
            showNumericError('Invalid input. Please enter numbers only.');
            $el.css('border-color', '#dc3545');
        }
    });

    // Final guard on submit.
    $('#formPost').on('submit', function (e) {
        clearNumericError();
        var invalid = false;
        $(numericSelector).each(function () {
            var val = $(this).val();
            if (val === '') return;
            if (!isValidNumericString(val)) {
                invalid = true;
                $(this).css('border-color', '#dc3545');
            }
        });
        if (invalid) {
            e.preventDefault();
            showNumericError('Some fields contain invalid numbers. Please correct them before saving.');
            return false;
        }
    });
});
// ===== END ADDED =====

</script>

@endsection
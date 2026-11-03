@extends('layouts.app_header')

@section('content')

<style>
    .filter-card {
        background: #ffffff;
        border: 1px solid #e3e7eb;
        border-radius: 10px;
        padding: 22px 22px 8px 22px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }
    .filter-card .filter-title {
        font-size: 13px;
        font-weight: 600;
        color: #1f3a5f;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eef1f4;
    }
    .filter-card .filter-title i { color: #E74C3C; margin-right: 6px; }

    .filter-card label {
        font-size: 12px;
        font-weight: 600;
        color: #4a5a6a;
        margin-bottom: 6px;
        display: block;
    }
    .filter-card .form-control,
    .filter-card .select2-container .select2-selection--single {
        height: 38px;
        border-radius: 6px;
        border: 1px solid #d5dbe0;
        font-size: 13px;
        box-shadow: none;
    }
    .filter-card .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }
    .filter-card .select2-container .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    .filter-card .input-daterange .input-group-addon {
        background: #1f3a5f !important;
        color: #fff !important;
        border: none;
        border-radius: 0;
        font-size: 12px;
        padding: 0 14px;
        display: flex;
        align-items: center;
    }

    .btn-search {
        background: linear-gradient(135deg, #E74C3C 0%, #c0392b 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 9px 22px;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: 0.3px;
        box-shadow: 0 3px 8px rgba(231, 76, 60, 0.35);
        transition: all 0.2s ease;
        width: 100%;
    }
    .btn-search:hover,
    .btn-search:focus {
        background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.5);
        transform: translateY(-1px);
    }
    .btn-search i { margin-left: 6px; }

    .panel-custom .panel-heading { border-bottom: 1px solid #eef1f4; }
    .panel-custom .panel-title1 {
        color: #1f3a5f;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    /* Hint text under supplier dropdown */
    .field-hint {
        font-size: 11px;
        color: #8a9aa8;
        margin-top: 4px;
        font-style: italic;
    }

    @media (max-width: 767px) {
        .filter-card { padding: 16px 16px 4px 16px; }
        .btn-search { margin-top: 10px; }
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a>
            <a class="step" href="#"> Suppliers Report </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h4 class="panel-title1">
                    <i class="fa fa-file-text-o" style="color:#E74C3C; margin-right:6px;"></i>
                    Suppliers Statement
                </h4>
            </div>
            <div class="panel-body">

                <form id="search_form" method="GET"
                      action="{{ url('accounts/suppliers-statements/pdf/') }}" target="_blank">
                    @csrf

                    <div class="filter-card">
                        <div class="filter-title">
                            <i class="fa fa-filter"></i> Filter Options
                        </div>

                        <div class="row">

                            {{-- Supplier --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="supplier_id">Supplier Name</label>
                                    <select name="supplier_id" id="supplier_id"
                                            class="form-control select2">
                                        <option value="">— All Suppliers —</option>
                                        @foreach($clients as $c)
                                            <option value="{{ $c->id }}">{{ $c->supplier_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="field-hint">
                                        Leave blank to generate for all suppliers.
                                    </div>
                                </div>
                            </div>

                            {{-- Payment type --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment">Transaction Type</label>
                                    <select name="payment" id="payment" class="form-control select2">
                                        <option value="">— All (Debit &amp; Credit) —</option>
                                        <option value="1">Credit (Payments)</option>
                                        <option value="2">Debit (Invoices)</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Date range --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date Range</label>
                                    <div class="input-daterange input-group" id="date-range"
                                         data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control"
                                               name="startdate" id="startdate"
                                               placeholder="Start Date" autocomplete="off" />
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="form-control"
                                               name="enddate" id="enddate"
                                               placeholder="End Date" autocomplete="off"/>
                                    </div>
                                    <div class="field-hint">
                                        Leave both empty to include all dates.
                                    </div>
                                </div>
                            </div>

                            {{-- Search button --}}
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-search" id="search">
                                        Search <i class="fa fa-arrow-circle-right"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection
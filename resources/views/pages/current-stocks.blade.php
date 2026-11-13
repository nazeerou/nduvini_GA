@extends('layouts.app_header')

@section('content')

<style>
/* ================= CURRENT STOCKS DESIGN ================= */

.stocks-page {
    color: #475467;
}

.stocks-page .step-indicator {
    float: right;
    margin-bottom: 15px;
}

.stocks-page .step-indicator a {
    color: #84909d;
    font-size: 12px;
    text-decoration: none;
    margin-left: 8px;
}

.stocks-page .stock-panel {
    border: 1px solid #e8ebef;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,.035);
    background: #fff;
}

.stocks-page .panel-heading {
    padding: 20px 22px;
    background: #fff;
    border-bottom: 1px solid #edf0f2;
    min-height: 75px;
}

.stocks-page .panel-title1 {
    font-size: 18px;
    font-weight: 600;
    color: #344054;
    margin: 12px 0 0;
}

.stocks-page .panel-title1 i {
    margin-right: 8px;
    color: #758397;
}

.stocks-page .panel-body {
    padding: 22px;
}

.stocks-page .pdf-btn {
    border-radius: 5px;
    padding: 9px 16px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 0;
}

.stocks-page .pdf-btn[disabled] {
    opacity: .75;
    cursor: not-allowed;
}

.stocks-page .table {
    margin-bottom: 0;
}

.stocks-page .table thead th {
    background: #f7f8fa;
    color: #526174;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    border-bottom: 1px solid #e5e7eb;
    padding: 14px 12px;
    white-space: nowrap;
}

.stocks-page .table tbody td {
    padding: 13px 12px;
    vertical-align: middle;
    font-size: 13px;
    color: #475467;
    border-color: #edf0f2;
}

.stocks-page .table tbody tr:hover {
    background: #fafbfc;
}

.stocks-page .item-name {
    font-weight: 600;
    color: #344054;
}

.stocks-page .stock-quantity {
    display: inline-block;
    min-width: 40px;
    padding: 5px 9px;
    border: 1px solid #e5e9ef;
    border-radius: 5px;
    background: #f8f9fa;
    text-align: center;
    font-weight: 600;
    color: #475467;
}

.stocks-page .action-btn {
    border-radius: 5px;
    padding: 7px 11px;
    font-size: 11px;
    font-weight: 600;
    margin: 2px;
}

.stocks-page .action-btn i {
    margin-right: 4px;
}

.stocks-page .btn-details {
    background: #f5f6f8;
    border: 1px solid #e1e5ea;
    color: #475467;
}

.stocks-page .btn-adjustment {
    background: #314946;
    border: 1px solid #d8dee7;
    color: #f4f7fc;
}

.stocks-page .action-btn:hover {
    opacity: .85;
}

/* ================= MODAL DESIGN ================= */

.stock-modal {
    border: 0;
    border-radius: 9px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,.12);
}

.stock-modal .modal-header {
    padding: 20px 25px;
    background: #f8f9fa;
    border-bottom: 1px solid #e8ebef;
}

.stock-modal .modal-title {
    font-size: 17px;
    font-weight: 600;
    color: #344054;
}

.stock-modal .modal-title i {
    margin-right: 8px;
    color: #758397;
}

.stock-modal .modal-header p {
    margin: 6px 0 0;
    font-size: 12px;
    color: #84909d;
}

.stock-modal .modal-body {
    padding: 25px;
}

.stock-modal .modal-footer {
    padding: 15px 25px;
    background: #fafafa;
    border-top: 1px solid #ededed;
}

.stock-modal .form-group {
    margin-bottom: 18px;
}

.stock-modal .form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #475467;
    margin-bottom: 7px;
}

.stock-modal .form-control {
    height: 40px;
    border: 1px solid #dfe3e8;
    border-radius: 5px;
    box-shadow: none;
    font-size: 13px;
    background: #fff;
}

.stock-modal .form-control:focus {
    border-color: #8796a8;
    box-shadow: 0 0 0 2px rgba(82,103,127,.08);
}

.stock-modal .form-control[readonly],
.stock-modal .form-control[disabled] {
    background: #f7f8fa;
    color: #667085;
}

.stock-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #526174;
    padding-bottom: 10px;
    margin-bottom: 18px;
    border-bottom: 1px solid #ededed;
}

.stock-section-title i {
    margin-right: 7px;
}

.stock-modal .btn {
    min-width: 100px;
    padding: 9px 15px;
    border-radius: 5px;
    font-weight: 600;
}

/* Product details */
.stock-details-table {
    width: 100%;
}

.stock-details-table tr td {
    padding: 13px 12px;
    border-bottom: 1px solid #edf0f2;
    font-size: 13px;
}

.stock-details-table tr td:first-child {
    width: 35%;
    font-weight: 600;
    color: #526174;
}

.stock-details-table tr td:nth-child(2) {
    width: 15px;
    color: #98a2b3;
}

.stock-details-table tr td:last-child {
    color: #344054;
    font-weight: 500;
    word-break: break-word;
}

/* ================= DOWNLOAD SPINNER MODAL ================= */

#stockDownloadModal .modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 24px 60px rgba(15,23,42,.20);
    overflow: hidden;
}

#stockDownloadModal .modal-body {
    text-align: center;
    padding: 34px 24px;
    background: #fff;
}

#stockDownloadModal h4 {
    margin: 0 0 8px;
    font-size: 15px;
    font-weight: 700;
    color: #303946;
}

#stockDownloadModal p {
    margin: 0;
    font-size: 12.5px;
    color: #667085;
    line-height: 1.5;
}

.stock-download-spinner {
    width: 44px;
    height: 44px;
    margin: 0 auto 18px;
    border: 3px solid #e2e8f0;
    border-top-color: #526b60;
    border-radius: 50%;
    animation: stock-spin .85s linear infinite;
    box-shadow: 0 6px 20px -6px rgba(82,107,96,.35);
}

@keyframes stock-spin {
    to { transform: rotate(360deg); }
}

@media(max-width:767px) {
    .stocks-page .panel-body {
        padding: 12px;
    }

    .stocks-page .panel-heading {
        padding: 15px;
    }

    .stocks-page .panel-title1 {
        font-size: 15px;
    }

    .stocks-page .pdf-btn {
        padding: 7px 10px;
        font-size: 11px;
    }

    .stock-modal .modal-body {
        padding: 18px;
    }

    .stock-details-table tr td:first-child {
        width: 40%;
    }
}
</style>


<div class="stocks-page">

    {{-- ================= BREADCRUMB ================= --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="step-indicator">
                <a class="step completed" href="#">Home</a>
                <a class="step" href="#">Stocks</a>
            </div>
        </div>
    </div>


    {{-- ================= STOCK LIST ================= --}}
    <div class="row">
        <div class="col-lg-12">

            <div class="panel panel-custom panel-border stock-panel">

                <div class="panel-heading">

                    <div style="float:right;">
                        {{-- CHANGED: <a> → <button>, no href, no target --}}
                        <button type="button"
                                id="export_pdf_btn"
                                class="btn btn-default pdf-btn"
                                data-url="{{ url('current-stocks/pdf') }}">
                            <i class="fa fa-file-pdf-o"></i>
                            Export PDF
                        </button>
                    </div>

                    <h4 class="panel-title1">
                        <i class="fa fa-cubes"></i>
                        Current Stocks
                    </h4>

                    <div style="clear:both;"></div>

                </div>


                <div class="panel-body">

                    <div class="table-responsive">

                        <table id="datatable-fixed-header"
                               class="table table-striped table-bordered">

                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Item Name</th>
                                    <th>Make</th>
                                    <th>Model</th>
                                    <th>Part Number</th>
                                    <th>Quantity</th>
                                    <th>Purchase Unit</th>
                                    <th width="190">Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($stocks as $key => $product)

                                    <tr>

                                        <td>{{ $key + 1 }}</td>

                                        <td class="item-name">
                                            {{ $product->item_name }}
                                        </td>

                                        <td>{{ $product->title }}</td>

                                        <td>{{ $product->model }}</td>

                                        <td>{{ $product->part_number }}</td>

                                        <td>
                                            <span class="stock-quantity">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>

                                        <td>{{ $product->purchase_unit }}</td>

                                        <td>

                                            <button type="button"
                                                    class="btn btn-details action-btn show-details"
                                                    data-id="{{ $product->id }}">
                                                <i class="fa fa-eye"></i>
                                                Details
                                            </button>

                                            <button type="button"
                                                    class="btn btn-adjustment action-btn item_adjustment"
                                                    data-id="{{ $product->id }}">
                                                <i class="fa fa-plus"></i>
                                                Adjustment
                                            </button>

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


{{-- ================= ITEM ADJUSTMENT MODAL ================= --}}
<div class="modal fade" id="item_adjustment_modal"
     tabindex="-1" role="dialog"
     aria-labelledby="adjustmentModalLabel">

    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content stock-modal">

            <form id="formUpdate"
                  method="POST"
                  action="{{ url('product/update') }}">

                @csrf

                <div class="modal-header">

                    <button type="button" class="close"
                            data-dismiss="modal">&times;</button>

                    <h4 class="modal-title" id="adjustmentModalLabel">
                        <i class="fa fa-sliders"></i>
                        Item Stock Adjustment
                    </h4>

                    <p>Review the current stock and enter the adjustment quantity.</p>

                </div>


                <div class="modal-body">

                    <input type="hidden" name="id" id="id">

                    <input type="hidden"
                           name="selling_price"
                           id="sellingPrice">

                    <input type="hidden"
                           name="purchasing_price"
                           id="purchasingPrice">


                    <div class="stock-section-title">
                        <i class="fa fa-cube"></i>
                        Product Information
                    </div>


                    <div class="form-group">
                        <label>Item Name</label>

                        <input type="text"
                               name="product_name"
                               disabled
                               id="product"
                               class="form-control"
                               placeholder="Product Name">
                    </div>


                    <div class="form-group">
                        <label>Make / Model</label>

                        <input type="text"
                               name="model"
                               disabled
                               id="model"
                               class="form-control">
                    </div>


                    <div class="stock-section-title">
                        <i class="fa fa-calculator"></i>
                        Stock Adjustment
                    </div>


                    <div class="row">

                        <div class="col-sm-6">

                            <div class="form-group">
                                <label>Current Stock Quantity</label>

                                <input type="text"
                                       name="qty_in_stock"
                                       id="qty_in_stock"
                                       class="form-control"
                                       readonly>
                            </div>

                        </div>


                        <div class="col-sm-6">

                            <div class="form-group">
                                <label>New Quantity to Add</label>

                                <input type="number"
                                       name="new_qty"
                                       id="new_qty"
                                       class="form-control"
                                       step="any"
                                       placeholder="Enter quantity"
                                       required>
                            </div>

                        </div>

                    </div>


                    <div class="form-group">

                        <label>Adjustment Reason <span class="text-danger">*</span></label>

                        <select name="reason_id"
                                class="form-control select2"
                                id="reason_id"
                                required>

                            <option value="">Select Reason</option>

                            @foreach($adjustments as $p)

                                <option value="{{ $p->id }}">
                                    {{ strtoupper($p->reason) }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-success"
                            id="update_btn">
                        <i class="fa fa-save"></i> Update Stock
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>


{{-- ================= PRODUCT DETAILS MODAL ================= --}}
<div class="modal fade" id="details_modal"
     tabindex="-1" role="dialog"
     aria-labelledby="detailsModalLabel">

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content stock-modal">

            <div class="modal-header">

                <button type="button" class="close"
                        data-dismiss="modal">&times;</button>

                <h4 class="modal-title" id="detailsModalLabel">
                    <i class="fa fa-info-circle"></i>
                    Product Details
                </h4>

                <p>Complete product and stock information.</p>

            </div>


            <div class="modal-body">

                <div class="stock-section-title">
                    <i class="fa fa-cube"></i>
                    Product Information
                </div>

                <div class="table-responsive">

                    <table class="stock-details-table">

                        <tr>
                            <td>Item Name</td>
                            <td>:</td>
                            <td id="item_name"></td>
                        </tr>

                        <tr>
                            <td>Part Number</td>
                            <td>:</td>
                            <td id="part_number"></td>
                        </tr>

                        <tr>
                            <td>Make</td>
                            <td>:</td>
                            <td id="title"></td>
                        </tr>

                        <tr>
                            <td>Model</td>
                            <td>:</td>
                            <td id="model_id"></td>
                        </tr>

                        <tr>
                            <td>Purchasing Price</td>
                            <td>:</td>
                            <td id="purchasing_price"></td>
                        </tr>

                        <tr>
                            <td>Selling Price</td>
                            <td>:</td>
                            <td id="selling_price"></td>
                        </tr>

                        <tr>
                            <td>Quantity</td>
                            <td>:</td>
                            <td id="qty"></td>
                        </tr>

                        <tr>
                            <td>Purchase Unit</td>
                            <td>:</td>
                            <td id="purchase_unit"></td>
                        </tr>

                        <tr>
                            <td>Total Purchases</td>
                            <td>:</td>
                            <td id="total_purchase"></td>
                        </tr>

                        <tr>
                            <td>Description</td>
                            <td>:</td>
                            <td id="description"></td>
                        </tr>

                    </table>

                </div>

            </div>


            <div class="modal-footer">

                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>

            </div>

        </div>

    </div>
</div>


{{-- ================= DOWNLOAD SPINNER MODAL ================= --}}
<div class="modal fade" id="stockDownloadModal" tabindex="-1" role="dialog"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="max-width:360px;margin:120px auto 0;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="stock-download-spinner"></div>
                <h4>Preparing report…</h4>
                <p>Please wait while your PDF is being generated.</p>
            </div>
        </div>
    </div>
</div>


{{-- ================= JAVASCRIPT ================= --}}
<script>
$(document).ready(function () {

    // CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    // ================= PRODUCT DETAILS =================

    $(document).on('click', '.show-details', function () {

        var product_id = $(this).data('id');

        $.ajax({
            url: `current-stocks/details/${product_id}`,
            type: "GET",

            success: function (response) {

                if (!response || !response.length) {
                    swal("Not Found", "Product details could not be found.", "warning");
                    return;
                }

                var product = response[0];

                $('#item_name').text(product.item_name || '');
                $('#part_number').text(product.part_number || '');
                $('#title').text(product.title || '');
                $('#model_id').text(product.model || '');
                $('#qty').text(product.quantity || '');
                $('#purchasing_price').text(product.purchasing_price || '');
                $('#selling_price').text(product.selling_price || '');
                $('#purchase_unit').text(product.purchase_unit || '');
                $('#total_purchase').text(product.total_purchase || '');
                $('#description').text(product.description || '');

                $('#details_modal').modal('show');

            },

            error: function () {
                swal("Error", "Unable to load product details.", "error");
            }

        });

    });


    // ================= ITEM ADJUSTMENT DETAILS =================

    $(document).on('click', '.item_adjustment', function () {

        var product_id = $(this).data('id');

        $('#formUpdate')[0].reset();

        $('#id').val('');
        $('#reason_id').val('').trigger('change');

        $.ajax({

            url: `products/adjustments/details/${product_id}`,
            type: "GET",

            success: function (response) {

                if (!response || !response.length) {
                    swal("Not Found", "Product information could not be found.", "warning");
                    return;
                }

                var product = response[0];

                $('#product').val(product.item_name || '');
                $('#qty_in_stock').val(product.quantity ?? 0);
                $('#new_qty').val('');
                $('#sellingPrice').val(product.selling_price || '');
                $('#purchasingPrice').val(product.purchasing_price || '');
                $('#model').val(product.model || '');
                $('#id').val(product.pid);

                $('#item_adjustment_modal').modal('show');

            },

            error: function () {
                swal("Error", "Unable to load stock adjustment details.", "error");
            }

        });

    });


    // ================= CONFIRM STOCK ADJUSTMENT =================

    $('#formUpdate').on('submit', function (e) {

        e.preventDefault();

        var form = this;
        var quantity = $('#new_qty').val();
        var reason = $('#reason_id').val();
        var currentStock = $('#qty_in_stock').val();

        if (!$('#id').val()) {
            swal("Error", "Please select a valid product.", "error");
            return;
        }

        if (quantity === '' || !isFinite(Number(quantity))) {
            swal("Invalid Quantity", "Please enter a valid quantity.", "warning");
            return;
        }

        if (!reason) {
            swal("Required", "Please select an adjustment reason.", "warning");
            return;
        }

        swal({

            title: "Confirm Stock Adjustment?",
            text: "Current stock: " + currentStock +
                  ". Quantity to add: " + quantity +
                  ". Do you want to continue?",

            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#52677f",
            confirmButtonText: "Yes, Update Stock",
            cancelButtonText: "Cancel",
            closeOnConfirm: false

        }, function (isConfirm) {

            if (isConfirm) {

                $('#update_btn')
                    .prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Updating...');

                form.submit();

            }

        });

    });


    // ================= RESET ADJUSTMENT MODAL =================

    $('#item_adjustment_modal').on('hidden.bs.modal', function () {

        $('#formUpdate')[0].reset();

        $('#id').val('');

        $('#reason_id').val('').trigger('change');

        $('#update_btn')
            .prop('disabled', false)
            .html('<i class="fa fa-save"></i> Update Stock');

    });


    /* ============================================================
     * EXPORT PDF — spinner + fetch + blob download
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

    $(document).on('click', '#export_pdf_btn', function () {

        var $btn = $(this);
        var url  = $btn.data('url');
        if (!url) return;

        // Show the branded "Preparing report…" spinner
        $('#stockDownloadModal').modal('show');

        // Disable button while working (so users can't double-click)
        $btn.prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Preparing…');

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
                        if (j.error)   msg += '\n' + j.error;
                        if (j.message && j.message !== j.error) msg += '\n' + j.message;
                        if (j.file)    msg += '\n@ ' + j.file + ':' + j.line;
                    } catch (e) {
                        msg += '\n' + (body || 'Empty response').substring(0, 400);
                    }
                    throw new Error(msg);
                });
            }

            var filename = extractFilename(
                response.headers.get('Content-Disposition'),
                'current-stocks-report.pdf'
            );

            return response.blob().then(function (blob) {
                return { blob: blob, filename: filename };
            });
        })
        .then(function (data) {

            // Trigger browser download from the received blob
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

            // Hide the spinner only after the download has been handed to the browser
            $('#stockDownloadModal').modal('hide');
        })
        .catch(function (err) {

            console.error('PDF download failed:', err);
            $('#stockDownloadModal').modal('hide');

            if (typeof swal !== 'undefined') {
                swal("Export failed", err.message || String(err), "error");
            } else {
                alert('PDF export failed:\n\n' + (err.message || err));
            }
        })
        .finally(function () {
            // Restore the button label
            $btn.prop('disabled', false)
                .html('<i class="fa fa-file-pdf-o"></i> Export PDF');
        });
    });

});
</script>

@endsection
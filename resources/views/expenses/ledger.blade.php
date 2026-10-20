@extends('layouts.app_header')

@section('content')

<style>
    /* ================= Ledger Report – Professional Monochrome UI ================= */
    :root{
        --lr-ink:#111827;
        --lr-ink-soft:#374151;
        --lr-muted:#6b7280;
        --lr-line:#e5e7eb;
        --lr-line-soft:#f9fafb;
        --lr-white:#ffffff;
        --lr-black:#111827;
    }

    .lr-wrap *{box-sizing:border-box;}

    /* ---------- Step indicator ---------- */
    .lr-steps{display:flex;justify-content:flex-end;align-items:center;gap:8px;margin-bottom:20px;}
    .lr-steps a{
        font-size:12px;font-weight:500;letter-spacing:.2px;padding:7px 14px;border-radius:8px;
        background:var(--lr-white);color:var(--lr-muted);text-decoration:none;
        border:1px solid var(--lr-line);transition:all .2s ease;
    }
    .lr-steps a:hover{background:var(--lr-line-soft);color:var(--lr-ink);text-decoration:none;}
    .lr-steps a.lr-step-done{background:var(--lr-white);color:var(--lr-ink-soft);border-color:var(--lr-line);}
    .lr-steps a.lr-step-active{
        background:var(--lr-black);color:var(--lr-white);border-color:var(--lr-black);
        box-shadow:0 2px 4px rgba(17,24,39,.10);
    }
    .lr-steps .lr-sep{color:#d1d5db;font-size:12px;}

    /* ---------- Card ---------- */
    .lr-card{
        background:var(--lr-white);border:1px solid var(--lr-line);border-radius:12px;overflow:hidden;
        box-shadow:0 1px 3px rgba(17,24,39,.05), 0 1px 2px rgba(17,24,39,.03);
    }
    .lr-card-head{
        display:flex;justify-content:space-between;align-items:center;gap:16px;
        padding:22px 28px;background:var(--lr-white);
        border-bottom:1px solid var(--lr-line);
    }
    .lr-card-head .lr-head-left{min-width:0;}
    .lr-card-head h3{
        margin:0 0 4px;font-size:17px;font-weight:700;color:var(--lr-black);letter-spacing:-.2px;
    }
    .lr-card-head h3 .fa{color:var(--lr-ink-soft);margin-right:7px;}
    .lr-card-head p{margin:0;font-size:13px;color:var(--lr-muted);line-height:1.5;}
    .lr-card-body{padding:28px;}

    /* ---------- Ghost button (Refresh) ---------- */
    .lr-btn-ghost{
        display:inline-flex;align-items:center;gap:8px;
        padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;
        color:var(--lr-ink-soft);background:var(--lr-white);border:1px solid var(--lr-line);
        transition:all .2s ease;
    }
    .lr-btn-ghost:hover{background:var(--lr-line-soft);color:var(--lr-ink);border-color:#d1d5db;}
    .lr-btn-ghost.is-spinning .fa{animation:lr-spin .8s linear infinite;}

    /* ---------- Grid & fields ---------- */
    .lr-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:20px;}
    .lr-field{grid-column:span 3;min-width:0;}
    @media (max-width:1199px){ .lr-field{grid-column:span 6;} }
    @media (max-width:575px){ .lr-field{grid-column:span 12;} }

    .lr-label{
        display:block;margin-bottom:8px;font-size:12px;font-weight:600;
        letter-spacing:.02em;color:var(--lr-ink-soft);
    }
    .lr-label .lr-req{color:#dc2626;font-weight:700;}

    .lr-control{
        width:100%;height:44px;padding:0 14px;font-size:14px;color:var(--lr-ink);
        background:var(--lr-white);border:1px solid var(--lr-line);border-radius:8px;
        outline:none;box-shadow:none;transition:all .18s ease;
    }
    select.lr-control{
        appearance:none;-webkit-appearance:none;-moz-appearance:none;
        padding-right:38px;cursor:pointer;
        background-image:url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%236b7280' d='M6 8 0 0h12z'/%3E%3C/svg%3E");
        background-repeat:no-repeat;background-position:right 14px center;background-size:11px;
    }
    .lr-control:hover{border-color:#d1d5db;}
    .lr-control:focus{
        border-color:var(--lr-ink);background:var(--lr-white);
        box-shadow:0 0 0 3px rgba(17,24,39,.06);
    }
    .lr-control.lr-invalid{border-color:#dc2626;background:#fef2f2;}

    /* ---------- Category format panel ---------- */
    .lr-format{
        margin-top:22px;padding:20px;border:1px solid var(--lr-line);border-radius:10px;
        background:var(--lr-line-soft);animation:lr-fade .3s ease;
    }
    @keyframes lr-fade{from{opacity:0;transform:translateY(-4px);}to{opacity:1;transform:none;}}

    .lr-format-badge{
        display:inline-flex;align-items:center;gap:8px;padding:6px 14px;border-radius:6px;
        background:var(--lr-white);border:1px solid var(--lr-line);
        font-size:12.5px;font-weight:600;color:var(--lr-ink);
    }
    .lr-format-badge .fa{color:var(--lr-ink-soft);}
    .lr-format-type{
        display:inline-block;margin-left:8px;padding:5px 11px;border-radius:6px;
        font-size:11px;font-weight:600;letter-spacing:.03em;text-transform:uppercase;
        background:var(--lr-black);color:var(--lr-white);
    }
    .lr-format-desc{margin:12px 0 0;font-size:13px;line-height:1.6;color:var(--lr-muted);}
    .lr-format-title{
        margin:16px 0 10px;font-size:11px;font-weight:600;letter-spacing:.08em;
        text-transform:uppercase;color:var(--lr-muted);
    }
    .lr-cols{display:flex;flex-wrap:wrap;gap:8px;}
    .lr-col-chip{
        padding:5px 11px;border-radius:6px;font-size:11.5px;font-weight:500;
        color:var(--lr-ink-soft);background:var(--lr-white);border:1px solid var(--lr-line);
    }

    /* ---------- Skeleton loader ---------- */
    .lr-skeleton{display:flex;flex-direction:column;gap:10px;}
    .lr-skeleton span{
        display:block;height:14px;border-radius:6px;
        background:linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 37%,#f3f4f6 63%);
        background-size:400% 100%;animation:lr-shimmer 1.3s ease infinite;
    }
    .lr-skeleton span:nth-child(1){width:45%;}
    .lr-skeleton span:nth-child(2){width:80%;}
    .lr-skeleton span:nth-child(3){width:60%;}
    @keyframes lr-shimmer{0%{background-position:100% 50%;}100%{background-position:0 50%;}}

    /* ---------- Actions ---------- */
    .lr-actions{
        display:flex;flex-wrap:wrap;align-items:center;gap:14px;
        margin-top:28px;padding-top:22px;border-top:1px solid var(--lr-line);
    }
    .lr-btn-primary{
        display:inline-flex;align-items:center;gap:10px;padding:12px 24px;border:none;
        border-radius:8px;font-size:14px;font-weight:600;letter-spacing:.01em;
        color:var(--lr-white);cursor:pointer;background:var(--lr-black);
        box-shadow:0 1px 2px rgba(17,24,39,.10);transition:all .2s ease;
    }
    .lr-btn-primary:hover{background:#000;transform:translateY(-1px);box-shadow:0 4px 12px rgba(17,24,39,.15);}
    .lr-btn-primary:disabled{opacity:.7;cursor:not-allowed;transform:none;box-shadow:none;}

    .lr-spin{
        display:none;width:15px;height:15px;border-radius:50%;
        border:2px solid rgba(255,255,255,.45);border-top-color:#fff;
        animation:lr-spin .7s linear infinite;
    }
    .is-loading .lr-spin{display:inline-block;}
    .is-loading .lr-btn-icon{display:none;}
    @keyframes lr-spin{to{transform:rotate(360deg);}}

    .lr-hint{font-size:12px;color:var(--lr-muted);}
    .lr-hint .fa{color:var(--lr-muted);}

    /* ---------- Download modal ---------- */
    #downloadModal .modal-content{
        border:none;border-radius:14px;overflow:hidden;
        box-shadow:0 24px 60px rgba(17,24,39,.20);
    }
    .lr-dl{padding:44px 32px 36px;text-align:center;background:var(--lr-white);}
    .lr-dl-ring{
        width:64px;height:64px;margin:0 auto 22px;border-radius:50%;
        border:3px solid var(--lr-line);border-top-color:var(--lr-black);
        animation:lr-spin .9s linear infinite;
    }
    .lr-dl-ring.is-done{animation:none;border-color:#16a34a;}
    .lr-dl-ring.is-error{animation:none;border-color:#dc2626;}
    .lr-dl h4{margin:0 0 8px;font-size:16px;font-weight:700;color:var(--lr-black);}
    .lr-dl p{margin:0;font-size:13px;line-height:1.6;color:var(--lr-muted);}
    .lr-dl-close{margin-top:24px;display:none;}
</style>

<div class="lr-wrap">

    {{-- ===== Step indicator ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="lr-steps">
                <a href="{{ url('home') }}" class="lr-step-done"><i class="fa fa-home"></i> Home</a>
                <span class="lr-sep"><i class="fa fa-angle-right"></i></span>
                <a href="#" class="lr-step-active">Ledger Report</a>
            </div>
        </div>
    </div>

    {{-- ===== Report card ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="lr-card">

                <div class="lr-card-head">
                    <div class="lr-head-left">
                        <h3><i class="fa fa-book"></i> Ledger Report</h3>
                        <p>Filter by account, category and period to generate your ledger statement.</p>
                    </div>
                    <button type="button" id="btnRefresh" class="lr-btn-ghost" title="Reset all filters">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                </div>

                <div class="lr-card-body">

                    <form id="reportForm"
                          method="GET"
                          action="{{ route('reports.petty-cash-report') }}"
                          data-category-format-url="">

                        <div class="lr-grid">

                            {{-- Account : REQUIRED --}}
                            <div class="lr-field">
                                <label class="lr-label" for="account_id">
                                    Account <span class="lr-req">*</span>
                                </label>
                                <select name="account_id" id="account_id" class="lr-control" required>
                                    <option value="">-- Select Account --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Category : OPTIONAL --}}
                            <div class="lr-field">
                                <label class="lr-label" for="category_id">
                                    Category <small style="text-transform:none;letter-spacing:0;color:var(--lr-muted);font-weight:400;">(optional)</small>
                                </label>
                                <select name="category_id" id="category_id" class="lr-control">
                                    <option value="">-- All Categories --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- From date : REQUIRED --}}
                            <div class="lr-field">
                                <label class="lr-label" for="startdate">
                                    From <span class="lr-req">*</span>
                                </label>
                                <input type="date" name="startdate" id="startdate" class="lr-control"
                                       value="{{ request('startdate') }}" required>
                            </div>

                            {{-- To date : REQUIRED --}}
                            <div class="lr-field">
                                <label class="lr-label" for="enddate">
                                    To <span class="lr-req">*</span>
                                </label>
                                <input type="date" name="enddate" id="enddate" class="lr-control"
                                       value="{{ request('enddate') }}" required>
                            </div>

                        </div>

                        {{-- Extra dynamic fields injected per category --}}
                        <div id="extraFieldsWrap" class="lr-grid" style="margin-top:20px;"></div>

                        {{-- Category-specific report format preview --}}
                        <div id="categoryFormatPanel" class="lr-format" style="display:none;">
                            <div id="categoryFormatBody"></div>
                        </div>

                        <div class="lr-actions">
                            <button type="submit" id="btnGenerate" class="lr-btn-primary">
                                <span class="lr-spin"></span>
                                <i class="fa fa-download lr-btn-icon"></i>
                                <span>Generate Report</span>
                            </button>

                            <span class="lr-hint">
                                <i class="fa fa-info-circle"></i>
                                Fields marked <strong>*</strong> are required.
                            </span>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Download / Progress Modal ===== --}}
<div class="modal fade" id="downloadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="lr-dl">
                <div class="lr-dl-ring" id="dlRing"></div>
                <h4 id="dlTitle">Preparing your report…</h4>
                <p id="dlText">Please wait while we build your ledger report.</p>

                <div class="lr-dl-close" id="dlCloseWrap">
                    <button type="button" class="lr-btn-primary" id="dlCloseBtn">
                        <i class="fa fa-times"></i> <span>Close</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    var form        = document.getElementById('reportForm');
    if (!form) return;

    var accountSel  = document.getElementById('account_id');
    var categorySel = document.getElementById('category_id');
    var startDate   = document.getElementById('startdate');
    var endDate     = document.getElementById('enddate');

    var formatPanel = document.getElementById('categoryFormatPanel');
    var formatBody  = document.getElementById('categoryFormatBody');
    var extraWrap   = document.getElementById('extraFieldsWrap');

    var btnGenerate = document.getElementById('btnGenerate');
    var btnRefresh  = document.getElementById('btnRefresh');

    var modalEl     = document.getElementById('downloadModal');
    var dlRing      = document.getElementById('dlRing');
    var dlTitle     = document.getElementById('dlTitle');
    var dlText      = document.getElementById('dlText');
    var dlCloseWrap = document.getElementById('dlCloseWrap');
    var dlCloseBtn  = document.getElementById('dlCloseBtn');

    var formatUrl   = form.getAttribute('data-category-format-url');

    /* =========================================================
       Small helpers
       ========================================================= */
    function esc(v) {
        return String(v == null ? '' : v).replace(/[&<>"']/g, function (m) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
        });
    }

    function openModal() {
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
            window.jQuery(modalEl).modal({ backdrop: 'static', keyboard: false });
        } else if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static', keyboard: false }).show();
        } else {
            modalEl.style.display = 'block';
            modalEl.style.background = 'rgba(0,0,0,.5)';
            modalEl.classList.add('show');
        }
    }

    function closeModal() {
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
            window.jQuery(modalEl).modal('hide');
        } else if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        } else {
            modalEl.style.display = 'none';
            modalEl.classList.remove('show');
        }
    }

    function modalState(state, title, text) {
        dlRing.classList.remove('is-done', 'is-error');
        if (state === 'done')  dlRing.classList.add('is-done');
        if (state === 'error') dlRing.classList.add('is-error');
        dlTitle.textContent = title;
        dlText.textContent  = text;
        dlCloseWrap.style.display = (state === 'error') ? 'block' : 'none';
    }

    function setButtonLoading(state) {
        btnGenerate.disabled = state;
        btnGenerate.classList.toggle('is-loading', state);
    }

    function getFilename(response) {
        var cd = response.headers.get('Content-Disposition') || '';
        var m  = /filename\*?=(?:UTF-8'')?"?([^";]+)"?/i.exec(cd);
        return m ? decodeURIComponent(m[1]) : null;
    }

    /* =========================================================
       Date validation
       ========================================================= */
    startDate.addEventListener('change', function () {
        endDate.min = this.value || '';
        if (endDate.value && startDate.value && endDate.value < startDate.value) {
            endDate.value = startDate.value;
        }
    });

    function datesValid() {
        endDate.setCustomValidity('');
        if (startDate.value && endDate.value && endDate.value < startDate.value) {
            endDate.setCustomValidity('The end date must be on or after the start date.');
            endDate.reportValidity();
            return false;
        }
        return true;
    }

    /* =========================================================
       Category -> fetch its OWN report format
       ========================================================= */
    categorySel.addEventListener('change', function () {
        var id = this.value;

        extraWrap.innerHTML = '';
        formatBody.innerHTML = '';
        formatPanel.style.display = 'none';

        if (!id || !formatUrl) return;

        formatPanel.style.display = 'block';
        formatBody.innerHTML =
            '<div class="lr-skeleton"><span></span><span></span><span></span></div>';

        fetch(formatUrl + '?category_id=' + encodeURIComponent(id), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(function (res) {
            if (!res.ok) throw new Error('Unable to load the report format for this category.');
            return res.json();
        })
        .then(function (data) { renderFormat(data || {}); })
        .catch(function (err) {
            formatBody.innerHTML =
                '<div style="color:#dc2626;font-size:13px;">' +
                '<i class="fa fa-exclamation-triangle"></i> ' + esc(err.message) +
                '</div>';
        });
    });

    function renderFormat(data) {
        var html = '<div>';

        html += '<span class="lr-format-badge"><i class="fa fa-tag"></i> '
              + esc(data.name || 'Selected Category') + '</span>';

        if (data.format_label) {
            html += '<span class="lr-format-type">' + esc(data.format_label) + '</span>';
        }
        html += '</div>';

        if (data.description) {
            html += '<p class="lr-format-desc">' + esc(data.description) + '</p>';
        }

        if (Array.isArray(data.columns) && data.columns.length) {
            html += '<div class="lr-format-title">This report will contain</div><div class="lr-cols">';
            data.columns.forEach(function (c) {
                html += '<span class="lr-col-chip">' + esc(c) + '</span>';
            });
            html += '</div>';
        }

        formatBody.innerHTML = html;

        /* ---- Extra fields this category requires ---- */
        (data.extra_fields || []).forEach(function (f) {
            var col = document.createElement('div');
            col.className = 'lr-field';

            var label = esc(f.label || f.name) +
                        (f.required ? ' <span class="lr-req">*</span>' : '');
            var req   = f.required ? ' required' : '';
            var inner = '<label class="lr-label">' + label + '</label>';

            if (f.type === 'select') {
                inner += '<select class="lr-control" name="' + esc(f.name) + '"' + req + '>';
                inner += '<option value="">-- Select --</option>';
                (f.options || []).forEach(function (o) {
                    var val = (o && typeof o === 'object') ? o.value : o;
                    var lbl = (o && typeof o === 'object') ? (o.label || o.value) : o;
                    inner += '<option value="' + esc(val) + '">' + esc(lbl) + '</option>';
                });
                inner += '</select>';
            } else if (f.type === 'date') {
                inner += '<input type="date" class="lr-control" name="' + esc(f.name) + '"' + req + '>';
            } else {
                inner += '<input type="text" class="lr-control" name="' + esc(f.name) + '" '
                       + 'placeholder="' + esc(f.placeholder || '') + '"' + req + '>';
            }

            col.innerHTML = inner;
            extraWrap.appendChild(col);
        });
    }

    /* =========================================================
       Refresh button – reset everything
       ========================================================= */
    btnRefresh.addEventListener('click', function () {
        btnRefresh.classList.add('is-spinning');
        setTimeout(function () { btnRefresh.classList.remove('is-spinning'); }, 700);

        form.reset();
        extraWrap.innerHTML = '';
        formatBody.innerHTML = '';
        formatPanel.style.display = 'none';
        endDate.min = '';
        endDate.setCustomValidity('');

        [accountSel, categorySel, startDate, endDate].forEach(function (el) {
            el.classList.remove('lr-invalid');
        });
    });

    /* =========================================================
       Submit -> show spinner modal -> download the report
       ========================================================= */
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // clear previous invalid marks
        [accountSel, startDate, endDate].forEach(function (el) {
            el.classList.remove('lr-invalid');
        });

        // Required-field validation (category is optional)
        var missing = [];
        if (!accountSel.value) { accountSel.classList.add('lr-invalid'); missing.push('Account'); }
        if (!startDate.value)  { startDate.classList.add('lr-invalid');  missing.push('From date'); }
        if (!endDate.value)    { endDate.classList.add('lr-invalid');    missing.push('To date'); }

        if (missing.length) {
            alert('Please fill in the required field(s): ' + missing.join(', '));
            return;
        }

        if (!datesValid()) return;

        generateReport();
    });

    async function generateReport() {
        openModal();
        modalState('loading', 'Preparing your report…', 'Please wait while we build your ledger report.');
        setButtonLoading(true);

        var params = new URLSearchParams();
        new FormData(form).forEach(function (value, key) {
            if (value !== '' && value !== null) params.append(key, value);
        });

        var url = form.getAttribute('action') + '?' + params.toString();

        try {
            var response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': '*/*'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Report generation failed (HTTP ' + response.status + ').');
            }

            var contentType = (response.headers.get('Content-Type') || '').toLowerCase();
            var blob = await response.blob();

            if (contentType.indexOf('html') !== -1) {
                // The report is returned as a rendered page -> show in a new tab
                var tabUrl = URL.createObjectURL(blob);
                window.open(tabUrl, '_blank');
                setTimeout(function () { URL.revokeObjectURL(tabUrl); }, 60000);
            } else {
                // File download (xlsx / csv / pdf ...)
                var filename = getFilename(response) || 'ledger-report';
                var dlUrl = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = dlUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                setTimeout(function () { URL.revokeObjectURL(dlUrl); }, 1500);
            }

            setButtonLoading(false);
            modalState('done', 'Download ready', 'Your report has been downloaded successfully.');
            setTimeout(closeModal, 1400);

        } catch (err) {
            setButtonLoading(false);
            modalState('error', 'Something went wrong', err.message || 'Unable to generate the report. Please try again.');
        }
    }

    dlCloseBtn.addEventListener('click', closeModal);

    /* Keep modal state clean for the next run */
    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
        window.jQuery(modalEl).on('hidden.bs.modal', function () {
            setButtonLoading(false);
        });
    }
});
</script>
@endsection
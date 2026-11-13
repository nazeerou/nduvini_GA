@extends('layouts.app_header')
@section('content')

<style>
  .audit-filter-card { background:#f8f9fa; border:1px solid #e3e6ea; border-radius:8px; padding:15px; margin-bottom:20px; }
  .audit-stat { border-radius:8px; padding:14px 18px; color:#fff; margin-bottom:15px; }
  .audit-stat .stat-num { font-size:26px; font-weight:700; line-height:1; }
  .audit-stat .stat-label { font-size:12px; opacity:.85; margin-top:4px; }
  .audit-stat.total    { background:#5bc0de; }
  .audit-stat.critical { background:#d9534f; }
  .audit-stat.today    { background:#5cb85c; }
  .audit-stat.deletes  { background:#f0ad4e; }

  .badge-action-create { background:#5cb85c; }
  .badge-action-update { background:#5bc0de; }
  .badge-action-delete { background:#d9534f; }
  .badge-action-other  { background:#777; }

  .badge-sev-info     { background:#5bc0de; }
  .badge-sev-warning  { background:#f0ad4e; }
  .badge-sev-critical { background:#d9534f; }

  .audit-row.critical { background:#fdecea !important; }
  .audit-row:hover    { background:#f5f8fa !important; cursor:pointer; }
  .audit-subject     { font-weight:600; color:#2c3e50; }
  .audit-desc        { font-size:12px; color:#666; }
  .audit-meta        { font-size:11px; color:#888; }
</style>

<div class="row m-t-10">
  <div class="col-md-12">
    <h3><i class="fa fa-history"></i> Audit Logs
      <a href="{{ route('audit-logs.critical') }}" class="btn btn-danger btn-sm pull-right" style="margin-left:6px;">
        <i class="fa fa-exclamation-triangle"></i> Critical Only
      </a>
      <button type="button" class="btn btn-warning btn-sm pull-right" data-toggle="modal" data-target="#purgeModal">
        <i class="fa fa-trash"></i> Purge Old
      </button>
    </h3>
    <hr>
  </div>
</div>

{{-- Stats --}}
<div class="row">
  <div class="col-md-3"><div class="audit-stat total">
    <div class="stat-num">{{ number_format($stats['total']) }}</div>
    <div class="stat-label">Total Logs</div>
  </div></div>
  <div class="col-md-3"><div class="audit-stat critical">
    <div class="stat-num">{{ number_format($stats['critical']) }}</div>
    <div class="stat-label">Critical</div>
  </div></div>
  <div class="col-md-3"><div class="audit-stat today">
    <div class="stat-num">{{ number_format($stats['today']) }}</div>
    <div class="stat-label">Today</div>
  </div></div>
  <div class="col-md-3"><div class="audit-stat deletes">
    <div class="stat-num">{{ number_format($stats['deletes']) }}</div>
    <div class="stat-label">Deletes</div>
  </div></div>
</div>

{{-- Flash messages --}}
@if (session('message'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('message') }}
  </div>
@endif
@if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('error') }}
  </div>
@endif

{{-- Filters --}}
<div class="audit-filter-card">
  <form method="GET" action="{{ route('audit-logs.index') }}" class="form-inline">
    <div class="form-group m-r-10 m-b-10">
      <input type="text" name="q" value="{{ request('q') }}" class="form-control input-sm"
             placeholder="Search subject / description">
    </div>
    <div class="form-group m-r-10 m-b-10">
      <input type="text" name="reference" value="{{ request('reference') }}" class="form-control input-sm"
             placeholder="Reference #">
    </div>
    <div class="form-group m-r-10 m-b-10">
      <select name="action" class="form-control input-sm">
        <option value="">-- Action --</option>
        @foreach($actions as $a)
          <option value="{{ $a }}" @selected(request('action')==$a)>{{ ucfirst($a) }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group m-r-10 m-b-10">
      <select name="severity" class="form-control input-sm">
        <option value="">-- Severity --</option>
        @foreach($severities as $s)
          <option value="{{ $s }}" @selected(request('severity')==$s)>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group m-r-10 m-b-10">
      <select name="user_id" class="form-control input-sm">
        <option value="">-- User --</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>
            {{ $u->fname }} {{ $u->lname }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="form-group m-r-10 m-b-10">
      <input type="date" name="from" value="{{ request('from') }}" class="form-control input-sm" title="From">
    </div>
    <div class="form-group m-r-10 m-b-10">
      <input type="date" name="to" value="{{ request('to') }}" class="form-control input-sm" title="To">
    </div>
    <label class="checkbox-inline m-r-10 m-b-10">
      <input type="checkbox" name="critical" value="1" @checked(request('critical'))> Critical only
    </label>
    <button class="btn btn-primary btn-sm m-b-10"><i class="fa fa-filter"></i> Filter</button>
    <a href="{{ route('audit-logs.index') }}" class="btn btn-default btn-sm m-b-10">Reset</a>
  </form>
</div>

{{-- Table --}}
<div class="table-responsive">
  <table class="table table-bordered table-striped table-hover">
    <thead class="thead-light">
      <tr>
        <th style="width:150px;">When</th>
        <th style="width:150px;">User</th>
        <th style="width:90px;">Action</th>
        <th>Subject</th>
        <th style="width:130px;">Reference</th>
        <th style="width:100px;">Severity</th>
        <th style="width:100px;">IP</th>
        <th style="width:60px;"></th>
      </tr>
    </thead>
    <tbody>
      @forelse($logs as $log)
        <tr class="audit-row {{ $log->is_critical ? 'critical' : '' }}"
            onclick="window.location='{{ route('audit-logs.show', $log->id) }}'">
          <td class="audit-meta">
            {{ $log->created_at->format('Y-m-d H:i:s') }}<br>
            <span class="text-muted">{{ $log->created_at->diffForHumans() }}</span>
          </td>
          <td>
            @if($log->user)
              <i class="fa fa-user"></i> {{ $log->user->fname }} {{ $log->user->lname }}
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>
            <span class="label badge-action-{{ $log->action === 'create' ? 'create' : ($log->action === 'update' ? 'update' : ($log->action === 'delete' ? 'delete' : 'other')) }}">
              {{ strtoupper($log->action) }}
            </span>
          </td>
          <td>
            <div class="audit-subject">
              @if($log->is_critical)
                <i class="fa fa-exclamation-triangle text-danger"></i>
              @endif
              {{ $log->subject }}
            </div>
            @if($log->description)
              <div class="audit-desc">{{ \Illuminate\Support\Str::limit($log->description, 140) }}</div>
            @endif
          </td>
          <td>
            @if($log->reference)
              <a href="{{ route('audit-logs.timeline', $log->reference) }}"
                 onclick="event.stopPropagation();"
                 title="View timeline">
                {{ $log->reference }}
              </a>
            @else
              —
            @endif
          </td>
          <td>
            <span class="label badge-sev-{{ $log->severity }}">{{ strtoupper($log->severity) }}</span>
          </td>
          <td class="audit-meta">{{ $log->ip }}</td>
          <td class="text-center">
            <a href="{{ route('audit-logs.show', $log->id) }}" class="btn btn-xs btn-info"
               onclick="event.stopPropagation();">
              <i class="fa fa-eye"></i>
            </a>
          </td>
        </tr>
      @empty
        <tr><td colspan="8" class="text-center text-muted">No audit logs found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{ $logs->links() }}

{{-- Purge Modal --}}
<div class="modal fade" id="purgeModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('audit-logs.purge') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fa fa-trash"></i> Purge Old Audit Logs</h4>
        </div>
        <div class="modal-body">
          <p class="text-warning">
            <i class="fa fa-exclamation-triangle"></i>
            <strong>Critical logs will NEVER be purged</strong>, no matter how old.
          </p>
          <div class="form-group">
            <label>Delete logs older than (days)</label>
            <input type="number" name="older_than_days" class="form-control"
                   value="90" min="30" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Purge</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection
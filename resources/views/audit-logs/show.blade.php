@extends('layouts.app_header')
@section('content')

<style>
  .log-detail-table td { vertical-align: top; }
  .log-detail-table td:first-child { width:180px; font-weight:600; color:#555; }
  .diff-old { background:#fdecea; }
  .diff-new { background:#e8f5e9; }
  .json-pre { background:#f7f7f7; padding:10px; border-radius:4px; font-size:12px;
              white-space:pre-wrap; word-break:break-all; max-height:400px; overflow:auto; }
</style>

<div class="row m-t-10">
  <div class="col-md-12">
    <h3>
      <i class="fa fa-search"></i> Audit Log #{{ $log->id }}
      <a href="{{ route('audit-logs.index') }}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-arrow-left"></i> Back
      </a>
    </h3>
    <hr>
  </div>
</div>

@if($log->is_critical)
  <div class="alert alert-danger">
    <i class="fa fa-exclamation-triangle"></i>
    <strong>CRITICAL EVENT</strong> — this action was flagged as critical and should be reviewed.
  </div>
@endif

<div class="row">
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading"><strong>Summary</strong></div>
      <div class="panel-body">
        <table class="table table-bordered log-detail-table">
          <tr><td>When</td><td>{{ $log->created_at->format('Y-m-d H:i:s') }} ({{ $log->created_at->diffForHumans() }})</td></tr>
          <tr><td>User</td>
              <td>
                @if($log->user)
                  <i class="fa fa-user"></i>
                  {{ $log->user->fname }} {{ $log->user->lname }}
                  <small class="text-muted">(#{{ $log->user->id }})</small>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td></tr>
          <tr><td>Subject</td><td><strong>{{ $log->subject }}</strong></td></tr>
          <tr><td>Description</td><td>{{ $log->description ?? '—' }}</td></tr>
          <tr><td>Action</td><td><span class="label label-info">{{ strtoupper($log->action) }}</span></td></tr>
          <tr><td>Severity</td>
              <td>
                <span class="label label-{{ $log->severity === 'critical' ? 'danger' : ($log->severity === 'warning' ? 'warning' : 'info') }}">
                  {{ strtoupper($log->severity) }}
                </span>
              </td></tr>
          <tr><td>Model</td><td>{{ $log->model_type ?? '—' }} @if($log->model_id) #{{ $log->model_id }} @endif</td></tr>
          <tr><td>Reference</td>
              <td>
                @if($log->reference)
                  <a href="{{ route('audit-logs.timeline', $log->reference) }}">{{ $log->reference }}</a>
                @else — @endif
              </td></tr>
        </table>
      </div>
    </div>

    @if($log->changes && count($log->changes))
      <div class="panel panel-default">
        <div class="panel-heading"><strong>Field Changes</strong></div>
        <div class="panel-body">
          <table class="table table-bordered">
            <thead><tr><th>Field</th><th>Old Value</th><th>New Value</th></tr></thead>
            <tbody>
              @foreach($log->changes as $field => $pair)
                <tr>
                  <td><strong>{{ $field }}</strong></td>
                  <td class="diff-old">{{ is_scalar($pair['old']) ? $pair['old'] : json_encode($pair['old']) }}</td>
                  <td class="diff-new">{{ is_scalar($pair['new']) ? $pair['new'] : json_encode($pair['new']) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif

    <div class="row">
      @if($log->old_values)
        <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-heading"><strong>Old Snapshot</strong></div>
            <div class="panel-body">
              <pre class="json-pre">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
          </div>
        </div>
      @endif
      @if($log->new_values)
        <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-heading"><strong>New Snapshot</strong></div>
            <div class="panel-body">
              <pre class="json-pre">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>

  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading"><strong>Request Info</strong></div>
      <div class="panel-body">
        <table class="table table-bordered log-detail-table">
          <tr><td>Method</td><td>{{ $log->method ?? '—' }}</td></tr>
          <tr><td>URL</td><td style="word-break:break-all;">{{ $log->url ?? '—' }}</td></tr>
          <tr><td>IP</td><td>{{ $log->ip ?? '—' }}</td></tr>
          <tr><td>Agent</td><td style="font-size:11px;">{{ $log->agent ?? '—' }}</td></tr>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection
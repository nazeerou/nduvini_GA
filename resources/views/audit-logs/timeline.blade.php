@extends('layouts.app_header')
@section('content')

<style>
  .timeline { position:relative; padding-left:30px; }
  .timeline::before {
    content:''; position:absolute; left:10px; top:0; bottom:0;
    width:2px; background:#ddd;
  }
  .timeline-item { position:relative; margin-bottom:22px; }
  .timeline-item::before {
    content:''; position:absolute; left:-24px; top:4px;
    width:14px; height:14px; border-radius:50%; background:#5bc0de;
    border:2px solid #fff; box-shadow:0 0 0 2px #5bc0de;
  }
  .timeline-item.critical::before { background:#d9534f; box-shadow:0 0 0 2px #d9534f; }
  .timeline-card { background:#fff; border:1px solid #e3e6ea; border-radius:6px; padding:12px 15px; }
  .timeline-item.critical .timeline-card { border-color:#d9534f; background:#fdf6f6; }
  .timeline-time { font-size:11px; color:#888; }
</style>

<div class="row m-t-10">
  <div class="col-md-12">
    <h3>
      <i class="fa fa-list-alt"></i> Timeline — Reference <code>{{ $reference }}</code>
      <a href="{{ route('audit-logs.index') }}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-arrow-left"></i> Back
      </a>
    </h3>
    <hr>
  </div>
</div>

@if($logs->isEmpty())
  <div class="alert alert-info">No activity recorded for this reference.</div>
@else
  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="timeline">
        @foreach($logs as $log)
          <div class="timeline-item {{ $log->is_critical ? 'critical' : '' }}">
            <div class="timeline-card">
              <div class="timeline-time">
                {{ $log->created_at->format('Y-m-d H:i:s') }} — {{ $log->created_at->diffForHumans() }}
              </div>
              <h5 style="margin:6px 0;">
                @if($log->is_critical)<i class="fa fa-exclamation-triangle text-danger"></i>@endif
                {{ $log->subject }}
                <span class="label label-info pull-right">{{ strtoupper($log->action) }}</span>
              </h5>
              @if($log->description)
                <div style="font-size:13px;">{{ $log->description }}</div>
              @endif
              <div class="m-t-5">
                <i class="fa fa-user"></i>
                @if($log->user)
                  {{ $log->user->fname }} {{ $log->user->lname }}
                @else — @endif
                &nbsp;·&nbsp;
                <a href="{{ route('audit-logs.show', $log->id) }}" class="btn btn-xs btn-info">
                  <i class="fa fa-eye"></i> Details
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endif

@endsection
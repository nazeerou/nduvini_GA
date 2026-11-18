@extends('layouts.app_header')

@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">

            <h4 class="page-title">
                <i class="fa fa-comments"></i> SMS Management
            </h4>

            <!-- Tabs -->
            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#sendSms">
                        <i class="fa fa-paper-plane"></i> Send SMS
                    </a>
                </li>
                 <li>
                    <a data-toggle="tab" href="#manageSms">
                        <i class="fa fa-cogs"></i> Manage SMS
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#smsReport">
                        <i class="fa fa-bar-chart"></i> Report
                    </a>
                </li>
            </ul>

            <div class="tab-content" style="margin-top:20px;">

               

                <!-- ================= SEND SMS ================= -->
                <div id="sendSms" class="tab-pane fade in active">

                    <form method="POST" action="{{ route('sms.send') }}">
                        @csrf

                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <strong>Send Message</strong>
                            </div>

                            <div class="panel-body">

                                <!-- Sender ID -->
                                <div class="form-group">
                                    <label>Sender ID</label>
                                    <select class="form-control" name="sender_id" required>
                                        <option value="">-- Select Sender ID --</option>
                                        <option value="1">MANANI TECH</option>
                                    </select>
                                </div>

                                <!-- Mobile -->
                                <div class="form-group">
                                    <label>Mobile Number(s)</label>
                                    <input type="text"
                                           class="form-control"
                                           name="mobile"
                                           placeholder="2557XXXXXXXX,2556XXXXXXXX"
                                           required>
                                    <small class="text-muted">
                                        Separate multiple numbers with comma
                                    </small>
                                </div>

                                <!-- Channel -->
                                <div class="form-group">
                                    <label>Send Via</label><br>
                                    <label class="radio-inline">
                                        <input type="radio" name="channel" value="sms" checked> SMS
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="channel" value="whatsapp"> WhatsApp
                                    </label>
                                </div>

                                <!-- Message -->
                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea class="form-control"
                                              name="message"
                                              rows="4"
                                              maxlength="160"
                                              required></textarea>
                                    <small class="pull-right text-muted">
                                        <span id="charCount">0</span>/160
                                    </small>
                                </div>

                            </div>

                            <div class="panel-footer text-right">
                                <button class="btn btn-success">
                                    <i class="fa fa-paper-plane"></i> Send
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                 <!-- ================= MANAGE SMS ================= -->
                <div id="manageSms" class="tab-pane fade in">

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong>Sender IDs</strong>
                            <button class="btn btn-xs btn-success pull-right">
                                <i class="fa fa-plus"></i> Add Sender ID
                            </button>
                        </div>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Sender ID</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th width="150">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>MANANI TECH</td>
                                <td>
                                    <span class="label label-success">Approved</span>
                                </td>
                                <td>2026-01-04</td>
                                <td>
                                    <button class="btn btn-xs btn-primary">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-xs btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- ================= REPORT ================= -->
                <div id="smsReport" class="tab-pane fade">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <strong>SMS Delivery Report</strong>
                        </div>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Sender ID</th>
                                <th>Recipient</th>
                                <th>Message</th>
                                <th>Channel</th>
                                <th>Status</th>
                                <th>Sent At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $key => $r)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>MANANI TECH</td>
                                <td>{{ $r->recipient }}</td>
                                <td>{{ $r->message }}</td>
                                <td>{{ $r->channel }}</td>
                                <td>
                                    <span class="label label-success">{{ $r->status }}</span>
                                </td>
                                <td>{{ $r->created_at }}</td>
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
<script>
    $('textarea[name="message"]').on('keyup', function () {
        $('#charCount').text($(this).val().length);
    });
</script>

@endsection

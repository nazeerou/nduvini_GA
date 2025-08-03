@extends('layouts.app_header')

@section('content')

<style>
    .filter {
        border: 2px solid #DDDFE1;
        padding: 15px 10px 0 20px;
        margin-bottom: 0px;
    }
    th {
  /* font-weight: 400; */
  background: #ddd;
  /* color: #FFF; */
  text-transform: uppercase;
  font-size: 0.8em;
  font-family: 'Raleway', sans-serif;
    }
</style>
<!-- whole sale setting -->
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="{{ url('home') }}">HOME</a> 
            <a class="step" href="#"> ACCOUNT STATEMENT </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title1"> ACCOUNT STATEMENT  </h3>
                <br/>
                <div id="display_message" style="display: none"></div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                    </div>
                </div>
                <div class="row">
                <table id="datatable" class="table table-striped1 table-bordered">
                        <thead>
                            <tr>
                            <th width="120px" align="center"> Date</th>
                                <th>Description </th>
                                <th>Credit  </th>
                                <th>Debit </th>
                                <th>Balance </th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($statements as $key => $s)
                            <tr>
                                <td>{{ ($s->created_at) }} </td>   
                                <td> {{ strtoupper($s->name) }} </td>
                                <td>
                                @if($s->credit == '') 
                                     @else 
                                     {{ number_format($s->credit, 2) }}
                                     @endif
                                </td>
                                <td>
                                @if($s->debit == '') 
                                     @else 
                                     {{ number_format($s->debit, 2) }} 
                                     @endif
                                     </td>
                                <td> {{ number_format($s->balance, 2) }}</td>
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
<!-- END  -->


@endsection
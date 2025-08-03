@extends('layouts.app_header')

@section('content')
<style>
    th {
  /* font-weight: 400; */
  background: #7093cc;
  color: #FFF;
  text-transform: uppercase;
  font-size: 0.8em;
  font-family: 'Raleway', sans-serif;
 }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#"> Activity Logs </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right"> 
                 </div>
                 <h4 class="panel-title1"> ACTIVITY LOGS  </h4>
            </div>
        
            <div class="panel-body">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="20px"> # </th>
                                <th> Subject </th>
                                <th> URL </th>
                                <th> IP Address </th>
                                <th> User Agent </th>
                                <th> User ID </th>
                                <th>Created Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td></td>
                                <td>  </td>
                                <td>  </td>
                                <td>  </td>
                                <td></td>   
                                <td>  </td>
                                <td>  </td>    
                            </tr>
                            </tbody>
                        </table>
            
                      </div>
                  </div>
            </div>
      </div>
</div>

@endsection
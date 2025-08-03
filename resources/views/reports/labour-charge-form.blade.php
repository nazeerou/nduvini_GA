@extends('layouts.app_header')

@section('content')

<style>
    .filter {
        border: 2px solid #DDDFE1;
        padding: 15px 10px 0 20px;
        margin-bottom: 0px;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a>
            <a class="step" href="#"> Labour Charge Report </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title"> Labour Charge Report </h3>
            </div>
            <div class="panel-body">
                <label for="product" class="col-sm-12 control-label">Filter </label><br/><br/>
              <div class="filter">
              <form id="search_form" method="GET" action="{{ url('reports/labour-charge-report-details') }}" target="_blank">
                  @csrf
                <div class="row">
                <div class="col-md-4">
                        <input name="labour_charge_id" value="{{ $labour_charge_id['pid'] }}" type="hidden">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Month  * </label>
                            <select name="month" class="form-control select2" id="month">
                                   <option value=""> --- All Month --- </option>
                                   <option value="01"> January </option>
                                   <option value="02"> February </option>
                                   <option value="03"> March </option>
                                   <option value="04"> April </option>
                                   <option value="05"> May </option>
                                   <option value="06"> June </option>
                                   <option value="07"> July </option>
                                   <option value="08"> AugUst </option>
                                   <option value="09"> September </option>
                                   <option value="10"> October </option>
                                   <option value="11"> November </option>
                                   <option value="12"> December </option>
                                </select>
                         </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Year   * </label>
                                <select name="year" class="form-control select2" id="year" required="required">
                                <option value=""> ---  Select Year --- </option>
                                 @foreach($years as $year)
                                   <option value="{{ $year->id }}">{{ $year->previous_year  }} / {{ $year->current_year  }}</option>
                                 @endforeach
                                </select>
                             </div>
                    </div>
                    <div class="col-md-2 m-t-10">
                        <div class="form-group">
                                <button type="submit" style="background: #E74C3C; color: white;" class="btn  btn-rounded pull-right m-b-0 m-t-10" id="search"><span class="pull-left" style="padding: 5px;"> Generate </span>
                                <span class="pull-right"> <i class="fa fa-arrow-circle-o-right fa-2x"></i></span></button>
                        </div>
                    </div>
                    </form>
                </div>
               </div><br/><br/>
     
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END  -->

@endsection
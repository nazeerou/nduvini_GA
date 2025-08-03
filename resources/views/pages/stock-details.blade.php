@extends('layouts.app_header')

@section('content')
<style>
input { text-transform: uppercase; }
</style>
<div class="row m-t-10">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-primary btn-sm" href="{{ url('current-stocks') }}"> <i class="ti-arrow-left"></i>  Go Back</a> 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title"> Product Details </h3>
                <!-- <br/>
                @if(session('message'))
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                {{ session('message') }}
                            </div>
                        @endif
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif -->
                 </div>
            <div class="panel-body">
             <div id="details" style="padding: 10px; border: 1px solid #ddd; background: #fff; border-bottom: red;">
                <div class="row m-t-0">
                   <div class="col-md-12">
                        <table class="table table-striped m-10">
                            <tr>
                             <td>Item Name</td> <td>: {{ $products_details[0]->item_name }}</td>
                            </tr>
                            <tr>
                             <td>Part Number</td> <td>: {{ $products_details[0]->part_number }}</td>
                            </tr>
                            <tr>
                            <td>Make </td> <td>: {{ $products_details[0]->title }}</td>
                            </tr>
                            <tr>
                            <td>Model</td> <td>: {{ $products_details[0]->model }}</td>
                            </tr>
                            <tr>
                            <td>Purchasing Price </td> <td>: {{ number_format($products_details[0]->purchasing_price, 2) }}</td>
                            </tr>
                            <tr>
                            <td>Selling Price </td> <td>: {{ number_format($products_details[0]->selling_price, 2) }}</td>
                            </tr>
                            <tr>
                            <td>Quantity </td> <td>: {{ $products_details[0]->quantity }}</td>
                            </tr>
                            <tr>
                            <td>Purchase Unit </td> <td>: {{ $products_details[0]->purchase_unit }}</td>
                            </tr>
                            <tr>
                            <td>Total Purchases </td> <td>: {{ number_format($products_details[0]->total_purchase, 2) }}</td>
                            </tr>
                            <tr>
                            <td>Alert Quantity </td> <td>: {{ $products_details[0]->alert_qty }}</td>
                            </tr>
                            <tr>
                            <td> Description </td> <td>: {{ $products_details[0]->description }}</td>
                            </tr>
                          </table>
                        </div>
                    </div>
                </div>
              </div>
            </div>
<!-- END  -->


@endsection
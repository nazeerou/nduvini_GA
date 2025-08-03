@extends('layouts.app_header')

@section('content')
<style>
    .highcharts-credits {
        display: none;
    }
    .highcharts-figure, .highcharts-data-table table {
    min-width: 360px; 
    max-width: 700px;
    margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #EBEBEB;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }
    .highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
    }
    .highcharts-data-table th {
        font-weight: 600;
    padding: 0.5em;
    }
    .highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
    }
    .highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
    }
    .highcharts-data-table tr:hover {
    background: #f1f7ff;
    }

    #view-link {
        color: #ddd;
    }
</style>
<br/>
<div class="card-bx">
    <div class="row">
        <div class="col-lg-3 col-md-3 p-r-0">
            <div class="card-box" style="height: 80px; background-color: #85C1E9; color: white;">
                <div class="widget-chart-1">
                    <div class="widget-detail" style="">
                        <p class="text-muteds" style="color: white;">
                            Total Purchases </p>
                        <h5 class="p-t-4 m-b-0" style="color: white;">
                           
                         {{ number_format($purchases, 2) }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 p-r-0">
            <div class="card-box" style="height: 80px; background-color: #ea4c89; color: white;">
                <div class="widget-chart-1">
                    <div class="widget-detail" style="">
                        <p class="text-muteds" style="color: white;">
                            Total Sales </p> 
                        <h5 class="p-t-4 m-b-0" style="color: white;">
                        {{ number_format($sales, 2) }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 p-r-0">
            <div class="card-box" style="height: 80px; background-color: #2E86C1; color: white;">
                <div class="widget-chart-1">
                    <div class="widget-detail" style="">
                        <p class="text-muteds" style="color: white;">
                            Stock < Min.Requirement </p>
                        <h5 class="p-t-4 m-b-0" style="color: white;">
                          {{ count($less_stocks) }}  <span style="float: right"> <a href="minimum-stock" id="view-link"> <i class="fa fa-arrow-circle-o-right"></i>View </a> </span>
                        </h5>
                    </div>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-lg-3 col-md-3 p-r-0">
            <div class="card-box" style="height: 80px; background-color: #4755ad; color: white;">
                <div class="widget-chart-1">
                    <div class="widget-detail" style="">
                        <p class="text-muteds" style="color: white;">
                            Out of Stocks </p>
                        <h5 class="p-t-4 m-b-0" style="color: white;">
                           
                           {{ count($out_stocks) }} <span style="float: right;"> <a href="stock-out" id="view-link"> <i class="fa fa-arrow-circle-o-right"></i>View </a> </span>
                        </h5>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
    </div>

<div class="row m-t-5">
    <div class="col-md-8"><h4 class="header-title m-t-0 m-b-2"
                              style="font-size: 16px; color: #000000;"> Filters </h4></div>
    <div class="col-md-4"><h4 class="header-title m-t-0 m-b-2"
                              style="font-size: 16px; color: #000000;"> Summary Report </h4></div>
</div>

<!-- start filter -->
<div class="row">
    <div class="col-md-9 col-lg-8 m-t-5">
        <div class="card-box" style="border: 4px solid #CCCCCC; height: 176px;">
            <div class="row m-t-15" style="padding-top: 2px;">
                <div class="col-sm-6">
                    Date
                    <div class="input-daterange input-group doublelined" id="datepicker"
                         data-date-format="yyyy-mm-dd">
                        <input type="text" class="form-control" name="start"
                               placeholder="start date" id="startdate"/>
                        <span class="input-group-addon bg- b-0 text-white"
                              style="background-color: palevioletred; color: white;"> to </span>
                        <input type="text" class="form-control" name="end"
                               placeholder="end date" id="enddate"/>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3">
                     Item Name
                    <select name="type" class="form-control" id="selling_type">
                        <option> ------- All ----- </option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}"> {{ $product->product_name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-3">
                    Model
                    <select id="product_name" class="form-control select2 doublelined" name="product_name">
                    <option> ------- All ----- </option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"> {{ $product->model }} </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">

            </div>

        </div>
      </div><!-- end col -->


    <div class="card-bx">
    <div class="col-lg-4 col-md-6 m-t-5">
        <div class="card-box" style="height: 80px; background-color: #CB0266;">
            <div class="widget-chart-1">
                <div class="widget-chart-box-1" style="padding: 10px 30px;">
                    <span style="color: #7B013E;"> <i class="fa fa-arrow-circle-o-right"></i>  </span>
                </div>
                <div class="widget-detail-1" style="padding-right: 0px;">
                    <p class="text-muteds" style="color: white;">Item In stock </p>
                    <h5 class="m-b-0"
                        style="color: white;" id="">
                        {{ count($in_stocks) }}

                    </h5>
                </div>
            </div>
        </div>
        <div class="card-box" style="height: 80px; background-color: #D35400;">
            <div class="widget-chart-1">
                <div class="widget-chart-box-1" style="padding: 10px 30px;">
                    <span style="color: #7B013E;"><i class="fa fa-arrow-circle-o-right"></i> </span>
                </div>
                <div class="widget-detail-1" style="padding-right: 0px;">
                    <p class="text-muteds" style="color: white;">  This Month's Sales </p>
                    <h5 class="p-t-4 m-b-0"
                        style="color: white;" id="">
                       
                        {{ number_format($month_sales, 2) }}

                    </h5>
                </div>
            </div>
        </div>
    </div><!-- end col -->
   </div>
</div>   <!-- END OF Dashboard  -->

<div class="row">
    <div class="col-md-8 pull-left">
        <button name="filter_by_all" style="width: 150px; background: #E74C3C; color: white;" class="btn btn-rounded pull-right" id="search"><span class="pull-left" style="padding: 5px;"> Search </span>
            <span class="pull-right"> <i class="fa fa-arrow-circle-o-right fa-2x"></i></span></button>
    </div>
    <div class="col-md-4"></div>
</div>


<!-- Graph -->

    <!-- CHART DIV START HERE -->
        
         <div id="container-box">
             <div class="row m-t-30">
                 <div class="col-md-6">
                    <div class="card-box">
                        <div id="bar_chart_o" style="height: 300px; margin: 0 auto"></div>

                        <button id="plain">Plain</button>
                        <button id="inverted">Inverted</button>
                        <button id="polar">Polar</button>
                    </div> 
                   
                </div><!-- end col -->
                 <div class="col-md-6">
                     <div class="card-box">
                         <div id="pie_chart" style="height: 325px; margin: 0 auto">  </div>
                     </div>
                     
                 </div><!-- end col -->
            </div>
            <div class="row">
                 <div class="col-md-12">
                 <div class="card-box">
                 <figure class="highcharts-figure">
                <!-- <div id="container"></div> -->
                </figure>
                 </div>
                 </div>
            </div>
         </div>

<br/><br/>
        

<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/heatmap.js"></script>
<script src="https://code.highcharts.com/modules/tilemap.js"></script>

 <script>
    Highcharts.theme = {
        colors: ['#6886FF', '#CB0266', '#50B432', '#ED561B', '#058DC7', '#24CBE5', '#64E572',
            '#FF9655', '#663399','#800000' ],
        chart: {
        },
        yAxis: [{
            gridLineWidth: 0,
            minorGridLineWidth: 0
        }],
        title: {
            style: {
                color: '#CB0266',
                font: '15px  Verdana, sans-serif'
            }
        },
        subtitle: {
            style: {
                color: '#666666',
                font: '12px  Verdana, sans-serif'
            }
        },
        legend: {
            itemStyle: {
                font: '9pt Verdana, sans-serif',
                color: 'black',
            },
            itemHoverStyle:{
                color: 'gray'
            }
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                borderRadius: 3
            }
        }
    };

 const chart = Highcharts.chart('bar_chart_o', {
    title: {
        text: 'Sales Summary Report For the Year'
    },
    subtitle: {
        text: 'Item Sales Report'
    },
    xAxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    },
    series: [{
        type: 'column',
        colorByPoint: true,
        data: [29.9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        showInLegend: true
    }]
});

document.getElementById('plain').addEventListener('click', () => {
    chart.update({
        chart: {
            inverted: false,
            polar: false
        },
        subtitle: {
            text: 'Plain'
        }
    });
});

document.getElementById('inverted').addEventListener('click', () => {
    chart.update({
        chart: {
            inverted: true,
            polar: false
        },
        subtitle: {
            text: 'Inverted'
        }
    });
});

document.getElementById('polar').addEventListener('click', () => {
    chart.update({
        chart: {
            inverted: false,
            polar: true
        },
        subtitle: {
            text: 'Polar'
        }
    });
});

Highcharts.chart('pie_chart', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Purchases Vs Sales Report in this Year '
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}</b>'
    },
    accessibility: {
        point: {
            valueSuffix: ''
        }
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f}'
            }
        }
    },
    series: [{
        name: 'Type',
        colorByPoint: true,
        data: [{
            name: 'Sales',
            y: 61.00,
            sliced: true,
            selected: true
        }, {
            name: 'Purchases',
            y: 19.00
        }]
      }]
   });

Highcharts.chart('container', {
        chart: {
            type: 'tilemap',
            inverted: true,
            height: '80%'
        },

        accessibility: {
            description: '',
            screenReaderSection: {
            beforeChartFormat:
                '<h5>{chartTitle}</h5>' +
                '<div>{chartSubtitle}</div>' +
                '<div>{chartLongdesc}</div>' +
                '<div>{viewTableButton}</div>'
            },
            point: {
            valueDescriptionFormat: '{index}. {xDescription}, {point.value}.'
            }
        },

        title: {
            text: 'Summary Sales report for each Item '
        },

        subtitle: {
            text: 'Summary for each Item'
        },

        xAxis: {
            visible: false
        },

        yAxis: {
            visible: false
        },

        colorAxis: {
            dataClasses: [{
            from: 0,
            to: 1000000,
            color: '#F9EDB3',
            name: '< 1M'
            }, {
            from: 1000000,
            to: 5000000,
            color: '#FFC428',
            name: '1M - 5M'
            }, {
            from: 5000000,
            to: 20000000,
            color: '#FF7987',
            name: '5M - 20M'
            }, {
            from: 20000000,
            color: '#FF2371',
            name: '> 20M'
            }]
        },

        tooltip: {
            headerFormat: '',
            pointFormat: 'Sales: <b> {point.name}</b> is <b>{point.value}</b>'
        },

        plotOptions: {
            series: {
            dataLabels: {
                enabled: true,
                format: '{point.hc-a2}',
                color: '#000000',
                style: {
                textOutline: false
                }
            }
            }
        },

        series: [{
            name: '',
            data: [{
            'hc-a2': 'Oil Filter',
            name: 'OIL FILTER',
            x: 6,
            y: 7,
            value: 4849377
            }, {
            'hc-a2': 'AK',
            name: 'Alaska',
            region: 'West',
            x: 0,
            y: 0,
            value: 737732
            }, {
            'hc-a2': 'AZ',
            name: 'Arizona',
            region: 'West',
            x: 5,
            y: 3,
            value: 6745408
            }, {
            'hc-a2': 'AR',
            name: 'Arkansas',
            region: 'South',
            x: 5,
            y: 6,
            value: 2994079
            }, {
            'hc-a2': 'CA',
            name: 'California',
            region: 'West',
            x: 5,
            y: 2,
            value: 39250017
            }, {
            'hc-a2': 'CO',
            name: 'Colorado',
            region: 'West',
            x: 4,
            y: 3,
            value: 5540545
            }, {
            'hc-a2': 'CT',
            name: 'Connecticut',
            region: 'Northeast',
            x: 3,
            y: 11,
            value: 3596677
            }, {
            'hc-a2': 'DE',
            name: 'Delaware',
            region: 'South',
            x: 4,
            y: 9,
            value: 935614
            }, {
            'hc-a2': 'DC',
            name: 'District of Columbia',
            region: 'South',
            x: 4,
            y: 10,
            value: 7288000
            }, {
            'hc-a2': 'FL',
            name: 'Florida',
            region: 'South',
            x: 8,
            y: 8,
            value: 20612439
            }, {
            'hc-a2': 'GA',
            name: 'Georgia',
            region: 'South',
            x: 7,
            y: 8,
            value: 10310371
            }, {
            'hc-a2': 'HI',
            name: 'Hawaii',
            region: 'West',
            x: 8,
            y: 0,
            value: 1419561
            }, {
            'hc-a2': 'ID',
            name: 'Idaho',
            region: 'West',
            x: 3,
            y: 2,
            value: 1634464
            }, {
            'hc-a2': 'IL',
            name: 'Illinois',
            region: 'Midwest',
            x: 3,
            y: 6,
            value: 12801539
            }, {
            'hc-a2': 'IN',
            name: 'Indiana',
            region: 'Midwest',
            x: 3,
            y: 7,
            value: 6596855
            }, {
            'hc-a2': 'IA',
            name: 'Iowa',
            region: 'Midwest',
            x: 3,
            y: 5,
            value: 3107126
            }, {
            'hc-a2': 'KS',
            name: 'Kansas',
            region: 'Midwest',
            x: 5,
            y: 5,
            value: 2904021
            }, {
            'hc-a2': 'KY',
            name: 'Kentucky',
            region: 'South',
            x: 4,
            y: 6,
            value: 4413457
            }, {
            'hc-a2': 'LA',
            name: 'Louisiana',
            region: 'South',
            x: 6,
            y: 5,
            value: 4649676
            }, {
            'hc-a2': 'ME',
            name: 'Maine',
            region: 'Northeast',
            x: 0,
            y: 11,
            value: 1330089
            }, {
            'hc-a2': 'MD',
            name: 'Maryland',
            region: 'South',
            x: 4,
            y: 8,
            value: 6016447
            }, {
            'hc-a2': 'MA',
            name: 'Massachusetts',
            region: 'Northeast',
            x: 2,
            y: 10,
            value: 6811779
            }, {
            'hc-a2': 'MI',
            name: 'Michigan',
            region: 'Midwest',
            x: 2,
            y: 7,
            value: 9928301
            }, {
            'hc-a2': 'MN',
            name: 'Minnesota',
            region: 'Midwest',
            x: 2,
            y: 4,
            value: 5519952
            }, {
            'hc-a2': 'MS',
            name: 'Mississippi',
            region: 'South',
            x: 6,
            y: 6,
            value: 2984926
            }, {
            'hc-a2': 'MO',
            name: 'Missouri',
            region: 'Midwest',
            x: 4,
            y: 5,
            value: 6093000
            }, {
            'hc-a2': 'MT',
            name: 'Montana',
            region: 'West',
            x: 2,
            y: 2,
            value: 1023579
            }, {
            'hc-a2': 'NE',
            name: 'Nebraska',
            region: 'Midwest',
            x: 4,
            y: 4,
            value: 1881503
            }, {
            'hc-a2': 'NV',
            name: 'Nevada',
            region: 'West',
            x: 4,
            y: 2,
            value: 2839099
            }, {
            'hc-a2': 'NH',
            name: 'New Hampshire',
            region: 'Northeast',
            x: 1,
            y: 11,
            value: 1326813
            }, {
            'hc-a2': 'NJ',
            name: 'New Jersey',
            region: 'Northeast',
            x: 3,
            y: 10,
            value: 8944469
            }, {
            'hc-a2': 'NM',
            name: 'New Mexico',
            region: 'West',
            x: 6,
            y: 3,
            value: 2085572
            }, {
            'hc-a2': 'NY',
            name: 'New York',
            region: 'Northeast',
            x: 2,
            y: 9,
            value: 19745289
            }, {
            'hc-a2': 'NC',
            name: 'North Carolina',
            region: 'South',
            x: 5,
            y: 9,
            value: 10146788
            }, {
            'hc-a2': 'ND',
            name: 'North Dakota',
            region: 'Midwest',
            x: 2,
            y: 3,
            value: 739482
            }, {
            'hc-a2': 'OH',
            name: 'Ohio',
            region: 'Midwest',
            x: 3,
            y: 8,
            value: 11614373
            }, {
            'hc-a2': 'OK',
            name: 'Oklahoma',
            region: 'South',
            x: 6,
            y: 4,
            value: 3878051
            }, {
            'hc-a2': 'OR',
            name: 'Oregon',
            region: 'West',
            x: 4,
            y: 1,
            value: 3970239
            }, {
            'hc-a2': 'PA',
            name: 'Pennsylvania',
            region: 'Northeast',
            x: 3,
            y: 9,
            value: 12784227
            }, {
            'hc-a2': 'RI',
            name: 'Rhode Island',
            region: 'Northeast',
            x: 2,
            y: 11,
            value: 1055173
            }, {
            'hc-a2': 'SC',
            name: 'South Carolina',
            region: 'South',
            x: 6,
            y: 8,
            value: 4832482
            }, {
            'hc-a2': 'SD',
            name: 'South Dakota',
            region: 'Midwest',
            x: 3,
            y: 4,
            value: 853175
            }, {
            'hc-a2': 'TN',
            name: 'Tennessee',
            region: 'South',
            x: 5,
            y: 7,
            value: 6651194
            }, {
            'hc-a2': 'TX',
            name: 'Texas',
            region: 'South',
            x: 7,
            y: 4,
            value: 27862596
            }, {
            'hc-a2': 'UT',
            name: 'Utah',
            region: 'West',
            x: 5,
            y: 4,
            value: 2942902
            }, {
            'hc-a2': 'VT',
            name: 'Vermont',
            region: 'Northeast',
            x: 1,
            y: 10,
            value: 626011
            }, {
            'hc-a2': 'VA',
            name: 'Virginia',
            region: 'South',
            x: 5,
            y: 8,
            value: 8411808
            }, {
            'hc-a2': 'WA',
            name: 'Washington',
            region: 'West',
            x: 2,
            y: 1,
            value: 7288000
            }, {
            'hc-a2': 'WV',
            name: 'West Virginia',
            region: 'South',
            x: 4,
            y: 7,
            value: 1850326
            }, {
            'hc-a2': 'WI',
            name: 'Wisconsin',
            region: 'Midwest',
            x: 2,
            y: 5,
            value: 5778708
            }, {
            'hc-a2': 'WY',
            name: 'Wyoming',
            region: 'West',
            x: 3,
            y: 3,
            value: 584153
            }]
        }]
        });


      

</script>
@endsection

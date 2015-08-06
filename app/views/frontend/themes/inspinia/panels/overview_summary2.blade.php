<div class="row overview_datacells">     
    <div class="col-md-12 no_margin">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Total Sales</h5>
            </div>
            <div id="visualOne"></div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ number_format($overviews['total_sales_amount']) }}</h1>
              
                  <small>Overall sales in local currency</small>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Sales Lift</h5> 
            </div>
            <div id="mgItemsPerCart"></div>
            <div class="ibox-content">
              <div class="left">
               <div id="saleChart" style="width: 50px; height: 50px; margin: 0 auto"></div>  
            
               
              </div>

              <div class="right">
                <h1 class="no-margins">{{ number_format($overviews['total_sales_recommended']) }}</h1>
                <small>Sales from rec</small>
              </div>
            </div>
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Page Views</h5>
                <span class="label label-warning pull-right">Regular vs Recommended</span>
            </div>
            <div id="visualOne"></div>
            <div class="ibox-content">
              <div class="row">
                <div class="col-md-6">
                  <h1 class="no-margins">{{ number_format($overviews['total_pageviews'] - $overviews['total_pageviews_recommended']) }}</h1>
                
                    <small>Regular Page Views</small> 
                </div>

                <div class="col-md-6">
                  <h1 class="no-margins">{{ number_format($overviews['total_pageviews_recommended']) }}</h1>
                
                    <small>Page Views from rec</small>
                </div>
              </div>
            </div>
        </div>
    </div>

    </div><!-- end of first row -->
  
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Sales</h5>
                <div class="ibox-tools">
                  <a class="collapse-link">
                      <i class="fa fa-chevron-up"></i>
                  </a>
                </div>
            </div>
        
            <div class="ibox-content">
              <div class="row">
              
                <div class="col-md-9" id="mgSalesAmount" style="min-width: 310px; height: 400px; margin: 0 auto"></div> 

                <div class="col-md-3">
                  <div class="left">
                    <h1 class="no-margins">{{ number_format($overviews['total_sales_amount']) }}</h1>
                    <small>Total Sales</small>
                    <br /><br />
                    <h1 class="no-margins">{{ number_format($overviews['total_sales_recommended']) }}</h1>
                    <small>Recommended sales total</small> <br /><br />

                    <h1 class="no-margins">{{ number_format($overviews['total_orders_recommended']) }}</h1>
                    <small>Recommended Orders</small>
                    @if ($overviews['total_sales_amount'] > 0 )  
                      <h1 style="color:green;">{{ ( $overviews['total_sales_recommended'] / $overviews['total_sales_amount'] ) * 100 }} % lift</h1>
                    @else
                      <h1 style="color:green;">Pending Metrics</h1>
                    @endif
                  </div>
                </div>
              </div><!-- end of row -->

              <div class="row">

                <div class="well" style="margin: 40px 30px 0px 30px; vertical-align: baseline;">
                  <i style="font-size: 3em; margin-right: 30px; float: left; color: rgba(124,181,236,1);" class="fa fa-shopping-cart"></i> 
                  <h2 style="margin-top: 10px; font-size: 1.5em; text-align: left;">Your customers who interact with recommendations spend about <span class="label label-primary" style="font-size: 1em;">$112</span> more than users who don't</h2><br />
                </div>        


                <div class="well" style="margin: 40px 30px 0px 30px; vertical-align: baseline;">
                  <i style="font-size: 3em; margin-right: 30px; float: left; color: red;" class="fa fa-heart-o"></i> 
                  <h2 style="margin-top: 10px; font-size: 1.5em; text-align: left;">Your customers who interact with recommendations buy about <span class="label label-primary" style="font-size: 1em;">2</span> more items than users who don't</h2><br />
                </div>        
              </div><!-- end of row -->

            </div>
        </div>
    </div>
    

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Pageviews</h5>
              <div class="ibox-tools">
                  <a class="collapse-link">
                      <i class="fa fa-chevron-up"></i>
                  </a>
              </div>
            </div>
          
 
            <div class="ibox-content">
              <div class="row">
                <div class="col-md-9" id="mgViews" style="min-width: 310px; height: 400px; margin: 0 auto"></div> 
                <div class="col-md-3">
                  <div class="left">
                    <h1 class="no-margins">{{ number_format($overviews['total_pageviews']) }}</h1>
                    <small>total view actions received</small><br /><br />
                    
                    <h1 class="no-margins">{{ number_format($overviews['total_pageviews_recommended']) }}</h1>
                    <small>recommended view actions received</small><br /><br />
                    @if ($overviews['total_pageviews'] > 0 )  
                      <h1 class="boots_no no-margin green" style="color: green;">{{ sprintf("%.2f", ($overviews['total_pageviews_recommended'] / $overviews['total_pageviews'] ) * 100) }} % lift</h1>
                    @else
                      <p class="boots_no">No Data</p>
                    @endif
                  </div>
                </div>
              </div><!-- end of row -->

            </div>
        </div>
    </div>

    
</div>


<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function(){ 

d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/VIEWS/day/OVERALL', function(data) { 
    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];
    for (var i = 0; i < data.length; i++) {
          data[i] = MG.convert.date(data[i], 'date', '%Y-%m-%dT%H:%M:%S');
          for ( var j=0; j < data[i].length ; j++) {
            var d   = data[i][j].date.getDate();
            var m   = data[i][j].date.getMonth(); 
            var sm  = monthNames[m];
            var cs  = d + ' ,' + sm;
            data[i][j].date = cs;
          } 
    }   

  
    var date_array = [];
    var value_array = [];
    var value_array_recommended = [];

    for (var i=0; i< data.length; i++) {
      for ( var j=0; j <data[i].length ; j++) {  
        if ( i == 0 ) { 
          date_array.push(data[i][j].date);   
          value_array.push(data[i][j].value);
        } else {
          value_array_recommended.push(data[i][j].value);
        } 
      }
    }

    var max = Math.max.apply( Math, value_array );
    var steps = 10; 

    var lineData = {
        labels: date_array,
        datasets: [
            {
                label: "Overall",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: value_array
            },
            {
                label: "Recommended",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.8)",
                highlightFill: "rgba(26,179,148,0.75)",
                highlightStroke: "rgba(26,179,148,1)",
                data: value_array_recommended

            },

        ]
    };

    var lineOptions = {
        scaleShowGridLines: false,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1, 
        bezierCurve: true,
        bezierCurveTension: 0.4,
        pointDot: true,
        pointDotRadius: 4,
        pointDotStrokeWidth: 1,
        pointHitDetectionRadius: 20,
        datasetStroke: true,
        datasetStrokeWidth: 2,
        datasetFill: true,
        responsive: true,
        scaleShowHorizontalLines: false
    };

    //  var ctx = document.getElementById("mgViews").getContext("2d");
    //  var myNewChart = new Chart(ctx).Line(lineData, lineOptions);

    var barData = {
        labels: date_array,
        datasets: [
            {
                label: "Recommended Page Views",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,0.8)",
                highlightFill: "rgba(220,220,220,0.75)",
                highlightStroke: "rgba(220,220,220,1)",
                data: value_array
            },
            {
                label: "Regular Page Views",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.8)",
                highlightFill: "rgba(26,179,148,0.75)",
                highlightStroke: "rgba(26,179,148,1)",
                data: value_array_recommended
            }
        ]
    };

    var barOptions = {
        scaleBeginAtZero: true,
        scaleShowGridLines: true,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1,
        barShowStroke: true,
        barStrokeWidth: 2,
        barValueSpacing: 5,
        barDatasetSpacing: 1,
        responsive: true,
        multiTooltipTemplate: "<%= datasetLabel %> - <%= value %>" 
    }

  $('#mgViews').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Recommended Vs Regular Page Views'
        },
        subtitle: {
            text: ''
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: date_array,
            tickmarkPlacement: 'on',
            title: {
                enabled: false
            }
        },
        exporting: { enabled: false },
        yAxis: {
            title: {
                text: 'Page Views'
            },
            labels: {
                formatter: function () {
                    return this.value / 1000;
                }
            }
        },
        tooltip: {
            shared: true,
            valueSuffix: ''
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                lineColor: '#666666',
                lineWidth: 1,
                marker: {
                    lineWidth: 1,
                    lineColor: '#666666'
                }
            }
        },
        series: [{
            name: 'Regular Views',
            data: value_array
        }, 
        {
            name: 'Recommended Views',
            data: value_array_recommended
        }]
    });
    
});


d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/SALES_AMOUNT/day/OVERALL', function(data) {
    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];
    for (var i = 0; i < data.length; i++) {
          data[i] = MG.convert.date(data[i], 'date', '%Y-%m-%dT%H:%M:%S');
          for ( var j=0; j < data[i].length ; j++) {
            var d   = data[i][j].date.getDate();
            var m   = data[i][j].date.getMonth(); 
            var sm  = monthNames[m];
            var cs  = d + ' ,' + sm;
            data[i][j].date = cs;
          } 
    }   

    var date_array = [];
    var value_array = [];
    var value_array_recommended = [];

    for (var i=0; i< data.length; i++) {
      for ( var j=0; j <data[i].length ; j++) {  
        if ( i == 0 ) { 
          date_array.push(data[i][j].date);   
          value_array.push(data[i][j].value);
        } else {
          value_array_recommended.push(data[i][j].value);
        } 
      }
    }
 
    var lineData = {
        labels: date_array,
        datasets: [
            {
                label: "Overall",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: value_array
            },
            {
                label: "Recommended",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.8)",
                highlightFill: "rgba(26,179,148,0.75)",
                highlightStroke: "rgba(26,179,148,1)",
                data: value_array_recommended

            },

        ]
    };

    var lineOptions = {
        scaleShowGridLines: true,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1,
        bezierCurve: true,
        bezierCurveTension: 0.4,
        pointDot: true,
        pointDotRadius: 4,
        pointDotStrokeWidth: 1,
        pointHitDetectionRadius: 20,
        datasetStroke: true,
        datasetStrokeWidth: 2,
        datasetFill: true,
        responsive: true,
    };

    // var ctx = document.getElementById("mgSalesAmount").getContext("2d");
    // var myNewChart = new Chart(ctx).Line(lineData, lineOptions);

    var barData = {
        labels: date_array,
        datasets: [
            {
                label: "Recommended Sales",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,0.8)",
                highlightFill: "rgba(220,220,220,0.75)",
                highlightStroke: "rgba(220,220,220,1)",
                data: value_array
            },
            {
                label: "Regular Sales",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.8)",
                highlightFill: "rgba(26,179,148,0.75)",
                highlightStroke: "rgba(26,179,148,1)",
                data: value_array_recommended
            }
        ]
    };

    var barOptions = {
        scaleBeginAtZero: true,
        scaleShowGridLines: true,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1,
        barShowStroke: true,
        barStrokeWidth: 2,
        barValueSpacing: 5,
        barDatasetSpacing: 1,
        responsive: true,
        multiTooltipTemplate: "<%= datasetLabel %> - <%= value %>" 
    }


    // var ctx = document.getElementById("mgSalesAmount").getContext("2d");
    // var myNewChart = new Chart(ctx).Bar(barData, barOptions);


    $('#mgSalesAmount').highcharts({
        chart: {
            type: 'area'
        },
        title: {
            text: 'Recommended Vs Regular Sales'
        },
        subtitle: {
            text: ''
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: date_array,
            tickmarkPlacement: 'on',
            title: {
                enabled: false
            }
        },
        exporting: { enabled: false },
        yAxis: {
            title: {
                text: 'Sales'
            },
            labels: {
                formatter: function () {
                    return this.value / 1000;
                }
            }
        },
        tooltip: {
            shared: true,
            valueSuffix: ''
        },
        plotOptions: {
            area: {
                stacking: 'normal',
                lineColor: '#666666',
                lineWidth: 1,
                marker: {
                    enabled: false,
                    lineWidth: 0,
                    lineColor: '#666666'
                }
            }
        },
        series: [{
            name: 'Regular Sales',
            data: value_array
        }, {
            name: 'Recommended Sales',
            data: value_array
        }]
    });
});

// pie chart for sale attribute

$('#saleChart').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            margin: [0, 0, 0, 0],
            spacingTop: 0,
            spacingBottom: 0,
            spacingLeft: 0,
            spacingRight: 0
        },
        credits: {
            enabled: false
        },
        exporting: { enabled: false },
        title: {
            text: false
        },
        tooltip: { enabled: false },
        plotOptions: {
            pie: {
                size:'100%',
                dataLabels: {
                  enabled: false
                },
                allowPointSelect: false,
                cursor: false,
                dataLabels: {
                    enabled: false,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: "Brands",
            colorByPoint: true,
            data: [{
                name: "Total Sales Amount",
                y: 9808131
            },
            { 
                name: "Total Sales Recommended",
                y: 1138691
            } 
            ]
        }]
});

d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/ITEM_PURCHASED/day/OVERALL', function(data) {
    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];
    for (var i = 0; i < data.length; i++) {
          data[i] = MG.convert.date(data[i], 'date', '%Y-%m-%dT%H:%M:%S');
          for ( var j=0; j < data[i].length ; j++) {
            var d   = data[i][j].date.getDate();
            var m   = data[i][j].date.getMonth(); 
            var sm  = monthNames[m];
            var cs  = d + ' ,' + sm;
            data[i][j].date = cs;
          } 
    }   

    var date_array = [];
    var value_array = [];
    var value_array_recommended = [];

    for (var i=0; i< data.length; i++) {
      for ( var j=0; j <data[i].length ; j++) {  
        if ( i == 0 ) { 
          date_array.push(data[i][j].date);   
          value_array.push(data[i][j].value);
        } else {
          value_array_recommended.push(data[i][j].value);
        } 
      }
    }
 
    var lineData = {
        labels: date_array,
        datasets: [
            {
                label: "Overall",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: value_array
            },
            {
                label: "Recommended",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.8)",
                highlightFill: "rgba(26,179,148,0.75)",
                highlightStroke: "rgba(26,179,148,1)",
                data: value_array_recommended

            },

        ]
    };

    var lineOptions = {
        scaleShowGridLines: true,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1,
        bezierCurve: true,
        bezierCurveTension: 0.4,
        pointDot: true,
        pointDotRadius: 4,
        pointDotStrokeWidth: 1,
        pointHitDetectionRadius: 20,
        datasetStroke: true,
        datasetStrokeWidth: 2,
        datasetFill: true,
        responsive: true,
    };

    // var ctx = document.getElementById("mgItemsPurchased").getContext("2d");
    // var myNewChart = new Chart(ctx).Line(lineData, lineOptions);

    var barData = {
        labels: date_array,
        datasets: [
            {
                label: "Items Purchased Recommended",
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,0.8)",
                highlightFill: "rgba(220,220,220,0.75)",
                highlightStroke: "rgba(220,220,220,1)",
                data: value_array
            },
            {
                label: "Items Purchased Regular",
                fillColor: "rgba(26,179,148,0.5)",
                strokeColor: "rgba(26,179,148,0.8)",
                highlightFill: "rgba(26,179,148,0.75)",
                highlightStroke: "rgba(26,179,148,1)",
                data: value_array_recommended
            }
        ]
    };

    var barOptions = {
        scaleBeginAtZero: true,
        scaleShowGridLines: true,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1,
        barShowStroke: true,
        barStrokeWidth: 2,
        barValueSpacing: 5,
        barDatasetSpacing: 1,
        responsive: true,
        multiTooltipTemplate: "<%= datasetLabel %> - <%= value %>" 
    }


    // var ctx = document.getElementById("mgItemsPurchased").getContext("2d");
    // var myNewChart = new Chart(ctx).Bar(barData, barOptions);

});

});
</script>

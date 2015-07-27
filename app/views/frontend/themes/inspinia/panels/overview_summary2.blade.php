<div class="row overview_datacells">     
    <div class="col-md-12 no_margin">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Sales Amount per Cart</h5>
            </div>
            <div id="visualOne"></div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ number_format($overviews['total_sales_per_cart']) }} <i class="fa fa-money"></i></h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Average sales amount per cart</small>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Items per Cart</h5>
            </div>
            <div id="mgItemsPerCart"></div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ number_format($overviews['total_item_per_cart']) }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Average number of items in the cart</small>
            </div>
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Cart Boost</h5>
            </div>
            <div id="visualOne"></div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ number_format($overviews['cartBoost']) }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Boosts to average cart</small>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Here is Something</h5>
            </div>
            <div id="mgItemsPerCart"></div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ number_format($overviews['total_item_per_cart']) }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Average of items in the cart</small>
            </div>
        </div>
    </div>
    </div>
  
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-primary pull-right">Today</span>-->
                <h5>Sales Amount</h5>
            </div>
            <canvas id="mgSalesAmount"></canvas>
            <div class="ibox-content">
              <div class="left">
                <h1 class="no-margins">{{ number_format($overviews['total_sales_amount']) }} <i class="fa fa-money"></i></h1>
                <!--<div class="stat-percent font-bold text-navy">44% <i class="fa fa-level-up"></i></div>-->
                <small>Taken from regular sales total</small>
              </div>
              <div class="right">
                <h1 class="no-margins">{{ number_format($overviews['total_sales_recommended']) }}</h1>
                <!--<div class="stat-percent font-bold text-navy">44% <i class="fa fa-level-up"></i></div>-->
                <small>Recommended sales total</small>
                @if ($overviews['total_sales_amount'] > 0 ) 
                  <p class="boots_no">{{ sprintf("%.2f", ($overviews['total_sales_recommended'] / $overviews['total_sales_amount'] ) * 100) }} % boost</p>
                @else
                  <p class="boots_no">No Data</p>
                @endif
              </div>
 
            </div>
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Pageviews</h5>
            </div>
          
            <canvas id="mgViews"></canvas>
         
            <div class="ibox-content">
              <div class="left">
                <h1 class="no-margins">{{ number_format($overviews['total_pageviews']) }}</h1>
                <!-- <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>
                -->
                <small>total view actions received</small>
              </div>
              
              <div class="right">
                <h1 class="no-margins">{{ number_format($overviews['total_pageviews_recommended']) }}</h1>
                <!-- <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>
                -->
                <small>recommended view actions received</small>
                @if ($overviews['total_pageviews'] > 0 )  
                  <p class="boots_no green">{{ sprintf("%.2f", ($overviews['total_pageviews_recommended'] / $overviews['total_pageviews'] ) * 100) }} % boost</p> 
                @else
                  <p class="boots_no">No Data</p>
                @endif
              </div>
            </div>
        </div>
    </div>
     

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-primary pull-right">Today</span>-->
                <h5>Items Purchased</h5>
            </div>
            <canvas id="mgItemsPurchased"></canvas>
            <div class="ibox-content">
              <div class="left">
                <h1 class="no-margins">{{ number_format($overviews['total_item_purchased']) }} / {{ number_format($overviews['unique_item_purchased']) }}</h1>
                <!--<div class="stat-percent font-bold text-navy">44% <i class="fa fa-level-up"></i></div>-->
                <small>Total of items purchased / Unique Items Purchased</small>
              </div>
              <div class="right">
                <h1 class="no-margins">{{ number_format($overviews['total_item_purchased_recommended']) }}</h1>
                <!--<div class="stat-percent font-bold text-navy">44% <i class="fa fa-level-up"></i></div>-->
                <small>Recommended items purchased</small>
                @if ($overviews['total_sales_amount'] > 0 ) 
                  <p class="boots_no">{{ sprintf("%.2f", ($overviews['total_item_purchased_recommended'] / $overviews['total_item_purchased'] ) * 100) }} % </p>
                @else
                  <p class="boots_no">No Data</p>
                @endif
              </div> 
            </div>
        </div>
    </div>

</div>


<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function(){ 

var ef_data = [
  {
    value: {{ $overviews['total_pageviews_recommended'] }},
    color: "#a3e1d4",
    highlight: "#1ab394",
    label: "Total Page Views Recommended"
  },
  
  {
    value: {{ $overviews['total_item_purchased_recommended'] }},
    color: "#dedede",
    highlight: "#1ab394",
    label: "Total Items Purchased from Recommendation"
  }
];
var ef_options = {
  segmentShowStroke: true,
  segmentStrokeColor: "#fff",
  segmentStrokeWidth: 2,
  percentageInnerCutout: 45, // This is 0 for Pie charts
  animationSteps: 100,
  animationEasing: "easeOutBounce",
  animateRotate: true,
  animateScale: false,
  responsive: true
}; 
// var ef_piechart         = document.getElementById("ef_piechart").getContext("2d");
// var new_ef_piechart     = new Chart(ef_piechart).Doughnut(ef_data, ef_options);

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


    var ctx = document.getElementById("mgViews").getContext("2d");
    var myNewChart = new Chart(ctx).Bar(barData, barOptions);
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


    var ctx = document.getElementById("mgSalesAmount").getContext("2d");
    var myNewChart = new Chart(ctx).Bar(barData, barOptions);

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


    var ctx = document.getElementById("mgItemsPurchased").getContext("2d");
    var myNewChart = new Chart(ctx).Bar(barData, barOptions);

});

});
</script>

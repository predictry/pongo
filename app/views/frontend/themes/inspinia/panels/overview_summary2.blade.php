<div class="row overview_datacells">     
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Pageviews</h5>
            </div>
            <div id="mgViewsRecommended"></div>     
            <div id="mgViews"></div>
        
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
     
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-primary pull-right">Today</span>-->
                <h5>Sales Amount</h5>
            </div>
            <div id="mgOrdersRecommended"></div>
            <div id="mgSalesAmount"></div>
            <div class="ibox-content">
              <div class="left">
                <h1 class="no-margins">{{ number_format($overviews['total_sales_amount']) }}</h1>
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
                <!--<span class="label label-primary pull-right">Today</span>-->
                <h5>Items Purchased</h5>
            </div>
            <div id="mgItemsPurchased"></div>
            <div class="ibox-content">
              <div class="left">
                <h1 class="no-margins">{{ number_format($overviews['total_item_purchased']) }}</h1>
                <!--<div class="stat-percent font-bold text-navy">44% <i class="fa fa-level-up"></i></div>-->
                <small>Total of items purchased</small>
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

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 data_cell">
          <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-primary pull-right">Today</span>-->
                <h5>Effectiveness</h5>
            </div>
          
            <div class="ibox-content">
              <canvas id="ef_piechart" ></canvas>
            </div>
        </div>
    </div>

    <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Sales Amount per Cart</h5>
            </div>
            <div id="visualOne"></div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ number_format($overviews['total_sales_per_cart']) }}</h1>
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
                <small>Average of items in the cart</small>
            </div>
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Show Something Here</h5>
            </div>
            <div id="visualOne"></div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ number_format($overviews['conversion_rate']) }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Average sales amount per cart</small>
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
</div>


<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function(){ 
d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/VIEWS/day/OVERALL', function(data) { 
    for (var i = 0; i < data.length; i++) {
        data[i] = MG.convert.date(data[i], 'date', '%Y-%m-%dT%H:%M:%S');
    }   
    
    MG.data_graphic({
        animate_on_load: true,
        y_extended_ticks: true,
        data: data,
        full_width: true,
        height: 300,
        right: 40,
        area: true,
        interpolate: 'basic',
        target: document.getElementById('mgViews'),
        x_accessor: 'date',
        y_accessor: 'value',
        x_extended_ticks: true
    });
});


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

var ef_piechart         = document.getElementById("ef_piechart").getContext("2d");
var new_ef_piechart     = new Chart(ef_piechart).Doughnut(ef_data, ef_options);

d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/UNIQUE_VISITOR/day/OVERALL', function(data) {
    for (var i = 0; i < data.length; i++) {
        data[i] = MG.convert.date(data[i], 'date', '%Y-%m-%dT%H:%M:%S');
    }   
    MG.data_graphic({
        animate_on_load: true,
        y_extended_ticks: true, 
        interpolate: 'basic',
        data: data,
        area: false,
        full_width: true,
        height: 300,
        area: true,
        right: 40,
        target: document.getElementById('mgUniqueVisitor'),
        x_accessor: 'date',
        y_accessor: 'value',
        x_extended_ticks: true
    });
});

var unique_data = [
  {
    value: {{ $overviews['total_uvs'] }} ,   
    color: "#a3e1d4",
    highlight: "#1ab394",
    label: "Overall"
  },
  
  {
    value: {{ $overviews['total_uvs_recommended'] }},
    color: "#dedede",
    highlight: "#1ab394",
    label: "Recommended"
  }
];

var unique_data_options = {
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

// var unique_chart = document.getElementById("unique_chart").getContext("2d");
// var new_unique_chart = new Chart(unique_chart).Doughnut(unique_data, unique_data_options);


d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/SALES_AMOUNT/day/OVERALL', function(data) {
    for (var i = 0; i < data.length; i++) {
          data[i] = MG.convert.date(data[i], 'date', '%Y-%m-%dT%H:%M:%S');
    }   
    MG.data_graphic({
        animate_on_load: true, 
        interpolate: 'basic',
        data: data,
        full_width: true,
        height: 300,
        area: true,
        right: 40,
        target: document.getElementById('mgSalesAmount'),
        x_accessor: 'date',
        y_accessor: 'value',
        x_extended_ticks: true
    });
});


var order_data = [
  {
    value: {{ $overviews['total_sales_amount'] }} ,   
    color: "#a3e1d4",
    highlight: "#1ab394",
    label: "Overall"
  },
  
  {
    value: {{ $overviews['total_sales_recommended'] }},
    color: "#dedede",
    highlight: "#1ab394",
    label: "Recommended"
  }
];

var order_data_options = {
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

// var order_chart = document.getElementById("order_chart").getContext("2d");
// var new_order_chart = new Chart(order_chart).Doughnut(order_data, order_data_options);


d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/ITEM_PURCHASED/day/OVERALL', function(data) {
    for (var i = 0; i < data.length; i++) {
          data[i] = MG.convert.date(data[i], 'date', '%Y-%m-%dT%H:%M:%S');
          console.log(data[i]);
    }  
    MG.data_graphic({
        animate_on_load: true,
        y_extended_ticks: true, 
        data: data, 
        full_width: true,
        interpolate: 'basic',
        height: 200,
        right: 40,
        target: document.getElementById('mgItemsPurchased'),
        x_accessor: 'date',
        y_accessor: 'value',
        x_extended_ticks: true
    });
});

});
</script>

<div class="row overview_datacells">    
    
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 data_cell">
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
              </div>
            </div>
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 pie_chart">
      <div class="ibox float-e-margins">
        
        <div class="ibox-title">
          <p>Page View Boots</p>
        </div>
  
        <div class="ibox-content pie_container">
          <div>
            <canvas id="traffic_chart" style="display: inline-block; width: 100%;"></canvas>
          </div>

          <p class="boots_no">{{ sprintf("%.2f", ($overviews['total_pageviews_recommended'] / $overviews['total_pageviews'] ) * 100) }} %<i class="fa fa-level-up"></i></p>
        </div>
      </div>
    </div>

   <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 data_cell">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Unique Visitors</h5>
                </div>
                
                <div id="mgUniqueVisitorRecommended"></div>
                <div id="mgUniqueVisitor"></div>

                <div class="ibox-content">
                  <div class="left">                
                    <h1 class="no-margins">{{ number_format($overviews['total_uvs']) }}</h1>
                    <!-- <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i></div>
                    -->
                    <small>Determine by number of sessions</small>
                  </div>
                
                  <div class="right">
                    <h1 class="no-margins">{{ number_format($overviews['total_uvs_recommended']) }}</h1>
                    <!-- <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i></div>
                    -->
                    <small>Recommended Unique Visitors</small> 
                  </div>
              </div>
            </div>
    </div>


   <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 pie_chart">
      <div class="ibox float-e-margins">
        
        <div class="ibox-title">
          <p>Unique Visitor Boots</p>
        </div>
  
        <div class="ibox-content pie_container">
          <div>
            <canvas id="unique_chart" style="display: inline-block; width: 100%;"></canvas>
          </div>

          <p class="boots_no">{{ sprintf("%.2f", ($overviews['total_uvs_recommended'] / $overviews['total_uvs'] ) * 100) }} % <i class="fa fa-level-up"></i></p>
        </div>
      </div>
    </div>


    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-primary pull-right">Today</span>-->
                <h5>Sales Amount</h5>
            </div>
            <div id="mgOrdersRecommended"></div>
            <div id="mgSalesAmount"></div>
            <div class="ibox-content">
              <div class="left">
                <h1 class="no-margins">{{ $overviews['total_sales_amount'] }}</h1>
                <!--<div class="stat-percent font-bold text-navy">44% <i class="fa fa-level-up"></i></div>-->
                <small>Taken from regular sales total</small>
              </div>
              <div class="right">
                <h1 class="no-margins">{{ $overviews['total_sales_recommended'] }}</h1>
                <!--<div class="stat-percent font-bold text-navy">44% <i class="fa fa-level-up"></i></div>-->
                <small>Recommended sales total</small>
              </div>

        
            </div>
        </div>
    </div>


    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 pie_chart">
      <div class="ibox float-e-margins">
        
        <div class="ibox-title">
          <p>Sale Boots</p>
        </div>
  
        <div class="ibox-content pie_container">
          <div>
            <canvas id="order_chart" style="display: inline-block; width: 100%;"></canvas>
          </div>

          <p class="boots_no">{{ sprintf("%.2f", ($overviews['total_sales_recommended'] / $overviews['total_sales_amount'] ) * 100) }} % <i class="fa fa-level-up"></i></p>
        </div>
      </div>
    </div>

</div>


<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function(){ 
d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/VIEWS/hour/OVERALL', function(data) { 
    data = MG.convert.date(data, 'date', '%Y-%m-%dT%H:%M:%S'); 
    MG.data_graphic({
        animate_on_load: true,
        y_extended_ticks: true,
        data: data,
        full_width: true,
        height: 300,
        right: 40,
        area: false,
        target: document.getElementById('mgViews'),
        x_accessor: 'date',
        y_accessor: 'value',
        x_label: 'date',
        y_label: 'value'
    });
});


var traffic_data = [
  {
    value: {{ $overviews['total_pageviews'] }},
    color: "#a3e1d4",
    highlight: "#1ab394",
    label: "Overall"
  },
  
  {
    value: {{ $overviews['total_pageviews_recommended'] }},
    color: "#dedede",
    highlight: "#1ab394",
    label: "Recommended"
  }
];

var traffic_data_options = {
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

var traffic_chart = document.getElementById("traffic_chart").getContext("2d");
var new_traffic_chart = new Chart(traffic_chart).Doughnut(traffic_data, traffic_data_options);

d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/UNIQUE_VISITOR/hour/OVERALL', function(data) {
    data = MG.convert.date(data, 'date','%Y-%m-%dT%H:%M:%S');
    MG.data_graphic({
        animate_on_load: true,
        y_extended_ticks: true, 
        data: data,
        area: false,
        full_width: true,
        height: 300,
        right: 40,
        target: document.getElementById('mgUniqueVisitor'),
        x_accessor: 'date',
        y_accessor: 'value'
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

var unique_chart = document.getElementById("unique_chart").getContext("2d");
var new_unique_chart = new Chart(unique_chart).Doughnut(unique_data, unique_data_options);


d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/SALES_AMOUNT/day/OVERALL', function(data) {
    data = MG.convert.date(data, 'date','%Y-%m-%dT%H:%M:%S');
    MG.data_graphic({
        animate_on_load: true, 
        data: data,
        full_width: true,
        height: 300,
        right: 40,
        target: document.getElementById('mgSalesAmount'),
        x_accessor: 'date',
        y_accessor: 'value'
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

var order_chart = document.getElementById("order_chart").getContext("2d");
var new_order_chart = new Chart(order_chart).Doughnut(order_data, order_data_options);

});
</script>

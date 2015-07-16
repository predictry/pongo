<div class="row overview_datacells">    
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Pageviews</h5>
            </div>
            <div id="mgViews"></div>
            <div class="ibox-content">
              <div class="left">
                <h1 class="no-margins">{{ $overviews['total_pageviews'] }}</h1>
                <!-- <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>
                -->
                <small>total view actions received</small>
              </div>
              
              <div class="right">
                <h1 class="no-margins">{{ $overviews['total_pageviews_recommended'] }}</h1>
                <!-- <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>
                -->
                <small>recommended view actions received</small>
              </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 data_cell">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Unique Visitors</h5>
                </div>

                <div id="mgUniqueVisitor"></div>

                <div class="ibox-content">
                  <div class="left">                
                    <h1 class="no-margins">{{ $overviews['total_uvs'] }}</h1>
                    <!-- <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i></div>
                    -->
                    <small>Determine by number of sessions</small>
                  </div>
                
                  <div class="right">
                    <h1 class="no-margins">{{ $overviews['total_uvs_recommended'] }}</h1>
                    <!-- <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i></div>
                    -->
                    <small>Recommended Unique Visitors</small> 
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
    
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-danger pull-right">Low value</span>-->
                <h5>Conversion Rate</h5>
            </div>
            <div id="visualOne"></div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ number_format((($overviews['conversion_rate']) * 100), 4, '.','') }}%</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>(orders / pageviews) * 100</small>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-danger pull-right">Low value</span>-->
                <h5>Orders</h5>
            </div>
            <div id="mgOrders"></div>
            <div class="ibox-content">
              <div class="left">
                <h1 class="no-margins">{{ $overviews['total_orders'] }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Total of orders</small>
              </div>
              <div class="right">
                <h1 class="no-margins">{{ $overviews['total_orders_recommended'] }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Recommended Orders</small> 
              </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-danger pull-right">Low value</span>-->
                <h5>Items Purchased</h5>
            </div>
            <div id="mgItemsPurchased"></div>
            <div class="ibox-content">
              <div class="left">
                <h1 class="no-margins">{{ $overviews['total_item_purchased'] }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Total of items purchased</small>
              </div>
              <div class="right">
                <h1 class="no-margins">{{ $overviews['total_item_purchased_recommended'] }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Recommended items purchased</small>
              </div>
            </div>
        </div>
    </div>
     
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Items per Cart</h5>
            </div>
            <div id="mgItemsPerCart"></div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_item_per_cart'] }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Average of items in the cart</small>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 data_cell">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Sales Amount per Cart</h5>
            </div>
            <div id="visualOne"></div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_sales_per_cart'] }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Average sales amount per cart</small>
            </div>
        </div>
    </div>
 
</div>

<script type="text/javascript">
d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/VIEWS', function(data) { 
    data = MG.convert.date(data, 'date', '%Y-%m-%dT%H:%M:%S'); 
    MG.data_graphic({
        animate_on_load: true,
        y_extended_ticks: true,
        data: data,
        full_width: true,
        height: 200,
        right: 40,
        target: document.getElementById('mgViews'),
        x_accessor: 'date',
        y_accessor: 'value'
    });
});
d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/UNIQUE_VISITOR', function(data) {
    data = MG.convert.date(data, 'date','%Y-%m-%dT%H:%M:%S');
    MG.data_graphic({
        animate_on_load: true,
        y_extended_ticks: true,
        description: "This is a simple line chart. You can remove the area portion by adding area: false to the arguments list.",
        data: data,
        full_width: true,
        height: 200,
        right: 40,
        target: document.getElementById('mgUniqueVisitor'),
        x_accessor: 'date',
        y_accessor: 'value'
    });
});

d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/SALES_AMOUNT', function(data) {
    data = MG.convert.date(data, 'date','%Y-%m-%dT%H:%M:%S');
    MG.data_graphic({
        animate_on_load: true,
        y_extended_ticks: true, 
        description: "This is a simple line chart. You can remove the area portion by adding area: false to the arguments list.",
        data: data,
        full_width: true,
        height: 200,
        right: 40,
        target: document.getElementById('mgSalesAmount'),
        x_accessor: 'date',
        y_accessor: 'value'
    });
});
d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/ORDERS', function(data) {
    data = MG.convert.date(data, 'date','%Y-%m-%dT%H:%M:%S');
    MG.data_graphic({
        animate_on_load: true,
        y_extended_ticks: true, 
        description: "This is a simple line chart. You can remove the area portion by adding area: false to the arguments list.",
        data: data,
        full_width: true,
        height: 200,
        right: 40,
        target: document.getElementById('mgOrders'),
        x_accessor: 'date',
        y_accessor: 'value'
    });
});

d3.json('/v2/bucket/{{ $dt_start }}/{{ $dt_end }}/ITEM_PURCHASED', function(data) {
    data = MG.convert.date(data, 'date','%Y-%m-%dT%H:%M:%S');
    MG.data_graphic({
        animate_on_load: true,
        y_extended_ticks: true, 
        description: "This is a simple line chart. You can remove the area portion by adding area: false to the arguments list.",
        data: data, 
        full_width: true,
        height: 200,
        right: 40,
        target: document.getElementById('mgItemsPurchased'),
        x_accessor: 'date',
        y_accessor: 'value'
    });
});
</script>

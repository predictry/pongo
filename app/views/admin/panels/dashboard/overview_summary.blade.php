<div class="row">
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-success pull-right">Pageviews</span>-->
                <h5>Pageviews</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_pageviews'] }}</h1>
                <!--<div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>-->
                <small>total view actions received</small>
            </div>
        </div>
    </div>
<!--    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-info pull-right">Annual</span>
                <h5>Unique Visitors</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_uvs'] }}</h1>
                <div class="stat-percent font-bold text-info">20% <i class="fa fa-level-up"></i></div>
                <small>Determine by number of sessions</small>
            </div>
        </div>
    </div>-->
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-primary pull-right">Today</span>-->
                <h5>Sales Amount</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_sales_amount'] }}</h1>
                <!--<div class="stat-percent font-bold text-navy">44% <i class="fa fa-level-up"></i></div>-->
                <small>Taken from regular sales total</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-danger pull-right">Low value</span>-->
                <h5>Orders</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_orders'] }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Total of orders</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-danger pull-right">Low value</span>-->
                <h5>Items Purchased</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_item_purchased'] }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Total of items purchased</small>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <!--<span class="label label-danger pull-right">Low value</span>-->
                <h5>Conversion Rate</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ number_format($overviews['conversion_rate'],2) }}%</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>(orders / pageviews) * 100</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Sales Amount per Cart</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_sales_per_cart'] }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Average sales amount per cart</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Items per Cart</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_item_per_cart'] }}</h1>
                <!--<div class="stat-percent font-bold text-danger">38% <i class="fa fa-level-down"></i></div>-->
                <small>Average of items in the cart</small>
            </div>
        </div>
    </div>
</div>
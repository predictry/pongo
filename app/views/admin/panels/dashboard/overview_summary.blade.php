<div class="row">
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Pageviews</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_pageviews'] }}</h1>
                <small>Total view actions received</small>
            </div>
        </div>
    </div>
    <!--    <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Unique Visitors</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ $overviews['total_uvs'] }}</h1>
                    <small>Determine by number of sessions</small>
                </div>
            </div>
        </div>-->
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Sales Amount</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_sales_amount'] }}</h1>
                <small>Taken from regular sales total</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Orders</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_orders'] }}</h1>
                <small>Total of orders</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Items Purchased</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_item_purchased'] }}</h1>
                <small>Total of items purchased</small>
            </div>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Conversion Rate</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ number_format($overviews['conversion_rate'],2) }}%</h1>
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
                <small>Average of items in the cart</small>
            </div>
        </div>
    </div>   
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Total SKUs</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $overviews['total_skus'] }}</h1>
                <small>Total of SKUs</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        @include('admin.panels.dashboard.top_items', ['tableHeader' => 'Top 10 (Most Purchased Items)', 'contents' => $overviews['top_bought_items']])
    </div>

</div>

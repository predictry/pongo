<div class="row">

    <div class="col-sm-3">
        <div class="the-box no-border bg-success">
            <div class="tiles-inner text-center">
                <p class="tiles-title">Pageviews</p>
                <h1 class="bolded">{{ $overviews['total_pageviews'] }}</h1>
                <p class="tiles-sub-title">(total actions &amp; tracking received)</p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="the-box no-border bg-success">
            <div class="tiles-inner text-center">
                <p class="tiles-title">Unique Visitors</p>
                <h1 class="bolded">{{ $overviews['total_uvs'] }}</h1>
                <p class="tiles-sub-title">(determine by number of sessions)</p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="the-box no-border bg-success">
            <div class="tiles-inner text-center">
                <p class="tiles-title">Sales Amount</p>
                <h1 class="bolded">{{ $overviews['total_sales_amount'] }}</h1>
                <p class="tiles-sub-title">(taken from regular sales total)</p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="the-box no-border bg-success">
            <div class="tiles-inner text-center">
                <p class="tiles-title">Orders</p>
                <h1 class="bolded">{{ $overviews['total_orders'] }}</h1>
                <p class="tiles-sub-title">Total of orders</p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="the-box no-border bg-success">
            <div class="tiles-inner text-center">
                <p class="tiles-title">Items Purchased</p>
                <h1 class="bolded">{{ $overviews['total_item_purchased'] }}</h1>
                <p class="tiles-sub-title">(Total of items purchased)</p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="the-box no-border bg-success">
            <div class="tiles-inner text-center">
                <p class="tiles-title">Conversion Rate</p>
                <h1 class="bolded">{{ number_format($overviews['conversion_rate'],2) }}%</h1>
                <p class="tiles-sub-title">(orders / pageviews) * 100</p>
            </div>
        </div>
    </div>
</div>
@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', ['scripts' => array(
HTML::script('assets/js/script.helper.js'), 
HTML::script('assets/js/data_collection.js'),
HTML::script('assets/js/prism.js')),
HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'), 
HTML::script('assets/js/moment.min.js'), 
HTML::script('assets/js/daterangepicker.js'), 
HTML::script('assets/js/highcharts.js'), 
HTML::script('assets/inspinia/js/plugins/chartJs/Chart.min.js'),
HTML::script('assets/js/bootstrap-datetimepicker.min.js'), 
HTML::script('assets/js/script.helper.js'), 
HTML::script('assets/js/script.panel.filters.js'),
HTML::script('assets/js/visual.js')])
@section('content')
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => ['Sites' => 'v2/sites']])

<div class="sint_wrapper row">

  <h1>Integration Guide</h1>
  <p>Just follow the given steps one by one and then your website would start flying with predictry's recommendation engine.</p>

  <!-- Step One -->
  <div id="step_one" class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12"> 

    <h2 id="step-1-getting-started-embed-js"><em>STEP 1:</em> GETTING STARTED (EMBED JS)</h2>

    <p>To start tracking with the Predictry JavaScript library, just paste the following code into the page you want to track just before the <code>&lt;/head&gt;</code> tags. Make sure to change <strong><em>‘YOUR_API_KEY’</em></strong> and <strong><em>‘YOUR_TENANT_ID’</em></strong> accordingly that have been provided to you.
    This snippet of code will load our library asynchronously on your page which doesn’t slow down the loading time of you page<br />
    We create a variable called _predictry that will be available on any pages. You will use it to send any data to us.<br />
    Note: You need to include this on every page of your website.</p>

    <pre class="line-numbers prettyprint"><code class="language-javascript">&lt;script type=<span class="hljs-string">"text/javascript"</span>&gt;
    <span class="hljs-keyword">var</span> _predictry = _predictry || [];
    (<span class="hljs-function"><span class="hljs-keyword">function</span><span class="hljs-params">()</span> {</span>
        _predictry.push([<span class="hljs-string">'setTenantId'</span>, <span class="hljs-string">"{{ $site->name }}"</span>], [<span class="hljs-string">'setApiKey'</span>, <span class="hljs-string">"{{ $site->api_key }}"</span>]);
        <span class="hljs-keyword">var</span> d = document, g = d.createElement(<span class="hljs-string">'script'</span>), s = d.getElementsByTagName(<span class="hljs-string">'script'</span>)[<span class="hljs-number">0</span>];
        g.type = <span class="hljs-string">'text/javascript'</span>;
        g.defer = <span class="hljs-literal">true</span>;
        g.async = <span class="hljs-literal">true</span>;
        g.src = <span class="hljs-string">'//d2gq0qsnoi5tbv.cloudfront.net/v3/p.min.js'</span>;
        s.parentNode.insertBefore(g, s);
    })();
    <span class="xml"><span class="hljs-tag">&lt;/<span class="hljs-title">script</span>&gt;</span>
    </span></code></pre>   
    <span class="xml"><span class="hljs-tag"><span class="xml"><span class="hljs-tag">&lt;/</span></span><span class="hljs-title"><span class="xml"><span class="hljs-tag"><span class="hljs-title">script</span></span></span></span><span class="xml"><span class="hljs-tag">&gt;</span></span></span><span class="xml">
    </span></span></code>
    </pre>

    <p>If you prefer you can opt for a minified version</p>

    <pre class="prettyprint"><code class="language-js hljs  hljs "><span class="hljs-keyword"><span class="hljs-keyword">var</span></span> _predictry=_predictry||[];(<span class="hljs-function"><span class="hljs-keyword"><span class="hljs-function"><span class="hljs-keyword">function</span></span></span><span class="hljs-params"><span class="hljs-function"><span class="hljs-params">()</span></span></span><span class="hljs-function">{</span></span>_predictry.push([<span class="hljs-string"><span class="hljs-string">"setTenantId"</span></span>,<span class="hljs-string"><span class="hljs-string">"YOUR_TENANT_ID"</span></span>],[<span class="hljs-string"><span class="hljs-string">"setApiKey"</span></span>,<span class="hljs-string"><span class="hljs-string">"YOUR_API_KEY"</span></span>]);<span class="hljs-keyword"><span class="hljs-keyword">var</span></span> e=document,c=e.createElement(<span class="hljs-string"><span class="hljs-string">"script"</span></span>),b=e.getElementsByTagName(<span class="hljs-string"><span class="hljs-string">"script"</span></span>)[<span class="hljs-number"><span class="hljs-number">0</span></span>];c.type=<span class="hljs-string"><span class="hljs-string">"text/javascript"</span></span>;c.defer=<span class="hljs-literal"><span class="hljs-literal">true</span></span>;c.async=<span class="hljs-literal"><span class="hljs-literal">true</span></span>;c.src=<span class="hljs-string"><span class="hljs-string">"//d2gq0qsnoi5tbv.cloudfront.net/latest/p.min.js"</span></span>;b.parentNode.insertBefore(c,b)})();</code></pre>

    <h2 id="step-2-track-view"><em>STEP 2:</em> TRACK VIEW</h2>

    <p>On every product/item page that you would like to track, include this also.</p>

    <pre class="prettyprint"><code class=" hljs coffeescript hljs "><span class="hljs-reserved"><span class="hljs-reserved">var</span></span> view_data = {
        <span class="hljs-attribute"><span class="hljs-attribute">action</span></span>: {
            <span class="hljs-attribute"><span class="hljs-attribute">name</span></span>: <span class="hljs-string"><span class="hljs-string">"view"</span></span>
        },
        <span class="hljs-regexp"><span class="hljs-regexp">//</span></span> If user <span class="hljs-keyword"><span class="hljs-keyword">is</span></span> <span class="hljs-keyword"><span class="hljs-keyword">not</span></span> logged <span class="hljs-keyword"><span class="hljs-keyword">in</span></span>, <span class="hljs-keyword"><span class="hljs-keyword">this</span></span> object <span class="hljs-keyword"><span class="hljs-keyword">is</span></span> <span class="hljs-keyword"><span class="hljs-keyword">not</span></span> required
        <span class="hljs-attribute"><span class="hljs-attribute">user</span></span>: {
            <span class="hljs-attribute"><span class="hljs-attribute">user_id</span></span>: <span class="hljs-string"><span class="hljs-string">"100"</span></span>, <span class="hljs-regexp"><span class="hljs-regexp">//</span></span> identifier <span class="hljs-keyword"><span class="hljs-keyword">of</span></span> user
            <span class="hljs-attribute"><span class="hljs-attribute">email</span></span>: <span class="hljs-string"><span class="hljs-string">"user@email.com"</span></span> <span class="hljs-regexp"><span class="hljs-regexp">//</span></span> optional
        },
        <span class="hljs-attribute"><span class="hljs-attribute">items</span></span>:
        [
            {
                <span class="hljs-attribute"><span class="hljs-attribute">item_id</span></span>: <span class="hljs-string"><span class="hljs-string">"2300"</span></span>, <span class="hljs-regexp"><span class="hljs-regexp">//</span></span>alphanumeric (unique)
                <span class="hljs-attribute"><span class="hljs-attribute">name</span></span>: <span class="hljs-string"><span class="hljs-string">"Item name"</span></span>,
                <span class="hljs-attribute"><span class="hljs-attribute">price</span></span>: <span class="hljs-number"><span class="hljs-number">250.12</span></span>,
                <span class="hljs-attribute"><span class="hljs-attribute">img_url</span></span>: <span class="hljs-string"><span class="hljs-string">"http://www.predictry.com/123.png"</span></span>,
                <span class="hljs-attribute"><span class="hljs-attribute">item_url</span></span>: <span class="hljs-string"><span class="hljs-string">"http://www.predictry.com/123"</span></span>, <span class="hljs-regexp"><span class="hljs-regexp">//</span></span>without trailing slash. If you have another mobile domain, m.domain.com but the rest of the URL is the same, you can use a relative URL here
                <span class="hljs-attribute"><span class="hljs-attribute">discount</span></span>: <span class="hljs-string"><span class="hljs-string">"23%"</span></span>, <span class="hljs-regexp"><span class="hljs-regexp">//</span></span>the discount that <span class="hljs-keyword"><span class="hljs-keyword">is</span></span> being offered. If the discount <span class="hljs-keyword"><span class="hljs-keyword">is</span></span> <span class="hljs-keyword"><span class="hljs-keyword">in</span></span> amount <span class="hljs-number"><span class="hljs-number">23.10</span></span> without the percentage
                <span class="hljs-attribute"><span class="hljs-attribute">net_price</span></span>: <span class="hljs-string"><span class="hljs-number">193</span></span>, <span class="hljs-regexp">//</span>price after discount
                <span class="hljs-attribute"><span class="hljs-attribute">description</span></span>: <span class="hljs-string"><span class="hljs-string">"Description of the item"</span></span>,
                <span class="hljs-attribute"><span class="hljs-attribute">inventory_qty</span></span>: <span class="hljs-number"><span class="hljs-number">100</span></span>, <span class="hljs-regexp"><span class="hljs-regexp">//</span></span>how many items left
                <span class="hljs-attribute"><span class="hljs-attribute">categories</span></span>: <span class="hljs-string">[<span class="hljs-string">"Electronics"</span>,<span class="hljs-string">"Accessories"</span>,<span class="hljs-string">"Watches"</span>]</span>,
                <span class="hljs-attribute"><span class="hljs-attribute">tags</span></span>: [<span class="hljs-string"><span class="hljs-string">"iphone"</span></span>, <span class="hljs-string"><span class="hljs-string">"5s"</span></span>, <span class="hljs-string"><span class="hljs-string">"gold"</span></span>], <span class="hljs-regexp"><span class="hljs-regexp">//</span></span><span class="hljs-keyword"><span class="hljs-keyword">this</span></span> <span class="hljs-keyword"><span class="hljs-keyword">is</span></span> an array. If there <span class="hljs-keyword"><span class="hljs-keyword">is</span></span> only one item also enclosed <span class="hljs-keyword"><span class="hljs-keyword">in</span></span> array [<span class="hljs-string"><span class="hljs-string">"iphone"</span></span>]
                <span class="hljs-attribute"><span class="hljs-attribute">brand</span></span>: <span class="hljs-string"><span class="hljs-string">"apple"</span></span>

            }
        ]
        };

        _predictry.push([<span class="hljs-string"><span class="hljs-string">'track'</span></span>, view_data]);</code>
    </pre>

    <h2 id="step-4b-track-buy"><em>STEP 3:</em> TRACK BUY</h2>

    <p>To track successful purchases, you can include this on the thank you page or on any page after a purchase is completed.</p>

    <pre class="prettyprint"><code class="language-js hljs  hljs "><span class="hljs-keyword"><span class="hljs-keyword">var</span></span> buy_data = {
        action: {
            name: <span class="hljs-string"><span class="hljs-string">"buy"</span></span>,
            total: <span class="hljs-number"><span class="hljs-number">1730.5</span></span>
        },
        user: {
            user_id: <span class="hljs-string"><span class="hljs-string">"100"</span></span>, <span class="hljs-comment"><span class="hljs-comment">// identifier of user</span></span>
            email: <span class="hljs-string"><span class="hljs-string">"user@email.com"</span></span> <span class="hljs-comment"><span class="hljs-comment">// optional</span></span>
        },
        items: [
            {
                item_id: <span class="hljs-string"><span class="hljs-string">"4339"</span></span>, <span class="hljs-comment"><span class="hljs-comment">// identifier of item</span></span>
                qty: <span class="hljs-number"><span class="hljs-number">12</span></span>,
                sub_total: <span class="hljs-number"><span class="hljs-number">380</span></span>
            },
            {
                item_id: <span class="hljs-string"><span class="hljs-string">"4335"</span></span>,
                qty: <span class="hljs-number"><span class="hljs-number">20</span></span>,
                sub_total: <span class="hljs-number"><span class="hljs-number">1350.5</span></span>
            }
        ]
    };

    _predictry.push([<span class="hljs-string"><span class="hljs-string">'track'</span></span>, buy_data]);</code></pre>

    <h2 id="step-5-item-deletion"><em>STEP 4:</em> Item Deletion / Delisted / Sold out</h2>

    <p>When item removed from the catalog, or maybe sold out. Remove it from recommendations.</p>

    <pre class="prettyprint"><code class="language-js hljs  hljs "><span class="hljs-keyword"><span class="hljs-keyword">var</span></span> item_id = <span class="hljs-number"><span class="hljs-number">["100", "ABC299"]</span></span>; <span class="hljs-comment"><span class="hljs-comment">//item_ids that will be removed. If you have only one, also keep it as an array.</span></span>

    _predictry.push([<span class="hljs-string"><span class="hljs-string">'removeItem'</span></span>, item_id]);</code></pre>
    <p><br/>

    <hr/><p><br/>

<h2 id="step-6-tracking-other-events-optional">TRACKING OTHER EVENTS (Optional)</h2>

<h2 id="step-4a-track-started-checkout">1. TRACK ADD TO CART</h2>

<p>To track add to cart action, you can include this on add to cart button event click.</p>

<pre class="prettyprint"><code class="language-js hljs  hljs "><span class="hljs-keyword"><span class="hljs-keyword">var</span></span> add_to_cart_data = {
    action: {
        name: <span class="hljs-string"><span class="hljs-string">"add_to_cart"</span></span>
    },
    <span class="hljs-comment"><span class="hljs-comment">// If user is not logged in, this object is not required</span></span>
    user: {
        user_id: <span class="hljs-string"><span class="hljs-string">"100"</span></span>, <span class="hljs-comment"><span class="hljs-comment">// identifier of user</span></span>
        email: <span class="hljs-string"><span class="hljs-string">"user@email.com"</span></span> <span class="hljs-comment"><span class="hljs-comment">// optional</span></span>
    },
    items: [
        {
            item_id: <span class="hljs-string"><span class="hljs-string">"4457"</span></span>, <span class="hljs-comment"><span class="hljs-comment">//alphanumeric (unique)</span></span>
            qty: <span class="hljs-number"><span class="hljs-number">12</span></span>
        }
    ]
};

_predictry.push([<span class="hljs-string"><span class="hljs-string">'track'</span></span>, add_to_cart_data]);</code></pre>

<h2 id="step-4a-track-started-checkout">2. TRACK STARTED CHECKOUT</h2>

<p>To track started checkout action, you can include this on checkout button event click after user completed the form. So in this matter, user information is compulsory.</p>

<pre class="prettyprint"><code class=" hljs cs hljs "><span class="hljs-keyword"><span class="hljs-keyword">var</span></span> started_checkout_data = {
    action: {
        name: <span class="hljs-string"><span class="hljs-string">"started_checkout"</span></span>
    },
    <span class="hljs-comment"><span class="hljs-comment">// User info below are compulsory.</span></span>
    user: {
        user_id: <span class="hljs-string"><span class="hljs-string">"100"</span></span>, <span class="hljs-comment"><span class="hljs-comment">// identifier of user</span></span>
        email: <span class="hljs-string"><span class="hljs-string">"user@email.com"</span></span> <span class="hljs-comment"><span class="hljs-comment">// optional, email of user</span></span>
    },
    items: [
        {
            item_id: <span class="hljs-string"><span class="hljs-string">"4339"</span></span>
        },
        {
            item_id: <span class="hljs-string"><span class="hljs-string">"2300"</span></span>
        }
    ]
};

_predictry.push([<span class="hljs-string"><span class="hljs-string">'track'</span></span>, started_checkout_data]);</code></pre>

<h2 id="1-track-search-event">3. TRACK SEARCH EVENT</h2>

<p>To track search event, you can place the code below after search form submit.</p>

<pre class="prettyprint"><code class="language-js hljs  hljs "><span class="hljs-keyword"><span class="hljs-keyword">var</span></span> search_data = {
    action: {
        name: <span class="hljs-string"><span class="hljs-string">"search"</span></span>,
        keywords: <span class="hljs-string"><span class="hljs-string">"nexus 6 release date"</span></span>,
        category: <span class="hljs-string"><span class="hljs-string">"all"</span></span>
    },
    <span class="hljs-comment"><span class="hljs-comment">// If user is not logged in, this object is not required</span></span>
    user: {
        user_id: <span class="hljs-string"><span class="hljs-string">"100"</span></span>, <span class="hljs-comment"><span class="hljs-comment">// identifier of user</span></span>
        email: <span class="hljs-string"><span class="hljs-string">"user@email.com"</span></span> <span class="hljs-comment"><span class="hljs-comment">// optional</span></span>
    }
};

_predictry.push([<span class="hljs-string"><span class="hljs-string">'track'</span></span>, search_data]);</code></pre>

</div>

<script type="text/javascript">
  var action_names = <?php echo json_encode($action_names); ?>;
</script>
@stop

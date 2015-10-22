@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', ['scripts' => array(
HTML::script('assets/js/prism.js')),
HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'), 
HTML::script('assets/js/moment.min.js'), 
HTML::script('assets/js/daterangepicker.js'), 
HTML::script('assets/js/highcharts.js'), 
HTML::script('assets/inspinia/js/plugins/chartJs/Chart.min.js'),
HTML::script('assets/js/bootstrap-datetimepicker.min.js'), 
HTML::script('assets/js/script.helper.js'), 
HTML::script('assets/js/script.panel.filters.js'),
HTML::script('assets/js/script.panel.sites.js'),
HTML::script('assets/js/data_collection.js'),
HTML::script('assets/js/visual.js')])
@section('content')
@include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => ['Sites' => 'v2/sites']])

<div class="sint_wrapper row">

  <h1>Integration Guide</h1>
  <p>Just follow the given steps one by one and then your website would start flying with predictry's recommendation engine.</p>

  <!-- Step One -->
  <div id="step_one" class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12"> 
    <h2 class="step_head">STEP 1: GETTING STARTED (EMBED JS)</h2>
    <p>To start tracking with the Predictry JavaScript library, just paste the following code into the page you want to track just before the <code>&lt;/head&gt;</code> tags.</p>
    <p>This snippet of code will load our library asynchronously on your page which doesn’t slow down the loading time of you page.</p>
    <p>We create a variable called _predictry that will be available on any pages. You will use it to send any data to us.</p>
    <p><strong>Note: You need to include this on every page of your website.</strong></p>
    
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

    <!-- Second option *minified ver -->
    <p>If you prefer you can opt for a minified version</p>
    
    <pre class="line-numbers prettyprint"><code class="language-javascript hljs "><span class="hljs-keyword">var</span> _predictry=_predictry||[];(<span class="hljs-function"><span class="hljs-keyword">function</span><span class="hljs-params">()</span>{</span>_predictry.push([<span class="hljs-string">"setTenantId"</span>,<span class="hljs-string">"{{ $site->name }}"</span>],[<span class="hljs-string">"setApiKey"</span>,<span class="hljs-string">"{{ $site->api_key }}"</span>]);<span class="hljs-keyword">var</span> e=document,c=e.createElement(<span class="hljs-string">"script"</span>),b=e.getElementsByTagName(<span class="hljs-string">"script"</span>)[<span class="hljs-number">0</span>];c.type=<span class="hljs-string">"text/javascript"</span>;c.defer=<span class="hljs-literal">true</span>;c.async=<span class="hljs-literal">true</span>;c.src=<span class="hljs-string">"//d2gq0qsnoi5tbv.cloudfront.net/v3/p.min.js"</span>;b.parentNode.insertBefore(c,b)})();</code></pre>
    
    <div class="checkbox">
        <input id="step_one" type="checkbox" value="step_one" name="step_one"> 
        <label for="step_one" class="checkbox_label">Check this if you have done step one.</label> 
    </div>
  
  </div><!-- end of step one -->
  
  <div id="step_two" class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12">
    <h2 class="step_head">STEP 2: TRACK VIEW</h2>
    <p>On every product/item page that you would like to track, include this also.</p>

    <h4 id="product-page">Product page</h4>



    <pre class="line-numbers prettyprint"><code class="language-javascript hljs coffeescript"><span class="hljs-reserved">var</span> view_data = {
        <span class="hljs-attribute">action</span>: {
            <span class="hljs-attribute">name</span>: <span class="hljs-string">"view"</span>
        },
        <span class="hljs-regexp">//</span> If user <span class="hljs-keyword">is</span> <span class="hljs-keyword">not</span> logged <span class="hljs-keyword">in</span>, <span class="hljs-keyword">this</span> object <span class="hljs-keyword">is</span> <span class="hljs-keyword">not</span> required
        <span class="hljs-attribute">user</span>: {
            <span class="hljs-attribute">user_id</span>: <span class="hljs-string">"100"</span>, <span class="hljs-regexp">//</span> identifier <span class="hljs-keyword">of</span> user
            <span class="hljs-attribute">email</span>: <span class="hljs-string">"user@email.com"</span> <span class="hljs-regexp">//</span> optional
        },
        <span class="hljs-attribute">items</span>: [
            {
                <span class="hljs-attribute">item_id</span>: <span class="hljs-string">"2300"</span>, <span class="hljs-regexp">//</span>alphanumeric (unique)
                <span class="hljs-attribute">name</span>: <span class="hljs-string">"Item name"</span>,
                <span class="hljs-attribute">price</span>: <span class="hljs-number">250.12</span>,
                <span class="hljs-attribute">img_url</span>: <span class="hljs-string">"http://www.predictry.com/123.png"</span>,
                <span class="hljs-attribute">item_url</span>: <span class="hljs-string">"http://www.predictry.com/123"</span>, <span class="hljs-regexp">//</span>without trailing slash
    
                <span class="hljs-regexp">//</span>OPTIONALS - Provide <span class="hljs-keyword">if</span> available so that recommendations would be better
               <span class="hljs-attribute">discount</span>: <span class="hljs-string">"23%"</span>, <span class="hljs-regexp">//</span>the discount that <span class="hljs-keyword">is</span> being offered. If the discount <span class="hljs-keyword">is</span> <span class="hljs-keyword">in</span> amount <span class="hljs-number">23.10</span> without the percentage
                <span class="hljs-attribute">description</span>: <span class="hljs-string">"Description of the item"</span>,
                <span class="hljs-attribute">inventory_qty</span>: <span class="hljs-number">100</span>, <span class="hljs-regexp">//</span>how many items left
                <span class="hljs-attribute">category</span>: <span class="hljs-string">"Electronics"</span>,
                <span class="hljs-attribute">sub_category</span>: [<span class="hljs-string">"Accessories"</span>, <span class="hljs-string">"Watches"</span>], <span class="hljs-regexp">//</span> Electronics &gt; Accessories &gt; Watches
                <span class="hljs-attribute">tags</span>: [<span class="hljs-string">"iphone"</span>, <span class="hljs-string">"5s"</span>, <span class="hljs-string">"gold"</span>], <span class="hljs-regexp">//</span><span class="hljs-keyword">this</span> <span class="hljs-keyword">is</span> an array. If there <span class="hljs-keyword">is</span> only one item also enclosed <span class="hljs-keyword">in</span> array [<span class="hljs-string">"iphone"</span>] 
                <span class="hljs-attribute">brand</span>: <span class="hljs-string">"apple"</span>
            }
        ]
    };
    
    _predictry.push([<span class="hljs-string">'track'</span>, view_data]);</code></pre>
    
    <div class="checkbox"> 
        <input id="step_two" type="checkbox" value="step_two" name="step_one"> 
        <label for="step_two" class="checkbox_label">Check this if you have done step two.
      </label> 
    </div>

  </div><!-- end of step two -->

  <div id="step_three" class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12">
    <h2 class="step_head">STEP 3: TRACK ADD TO CART</h2>
    <p>To track add to cart action, you can include this on add to cart button event click.</p>



    <pre class="line-numbers prettyprint"><code class="language-javascript hljs "><span class="hljs-keyword">var</span> add_to_cart_data = {
        action: {
            name: <span class="hljs-string">"add_to_cart"</span>
        },
        <span class="hljs-comment">// If user is not logged in, this object is not required</span>
        user: {
            user_id: <span class="hljs-string">"100"</span>, <span class="hljs-comment">// identifier of user</span>
            email: <span class="hljs-string">"user@email.com"</span> <span class="hljs-comment">// optional</span>
        },
        items: [
            {
                item_id: <span class="hljs-string">"4457"</span>, <span class="hljs-comment">//alphanumeric (unique)</span>
                qty: <span class="hljs-number">12</span>
            }
        ]
    };
    
    _predictry.push([<span class="hljs-string">'track'</span>, add_to_cart_data]);</code></pre>
    
    <div class="checkbox">
        <input id="step_three" type="checkbox" value="step_three" name="step_three"> 
        <label for="step_three" class="checkbox_label">Check this if you have done step three.</label> 
    </div>
  </div>

  <div id="step_four" class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12">
    <h2 class="step_head">STEP 4A: TRACK STARTED CHECKOUT (Optional)</h2>
    <p>To track started checkout action, you can include this on checkout button event click after user completed the form. So in this matter, user information is compulsory.</p>


    
    <pre class="line-numbers prettyprint"><code class="language-javascript hljs cs"><span class="hljs-keyword">var</span> started_checkout_data = {
        action: {
            name: <span class="hljs-string">"started_checkout"</span>
        },
        <span class="hljs-comment">// User info below are compulsory.</span>
        user: {
            user_id: <span class="hljs-string">"100"</span>, <span class="hljs-comment">// identifier of user</span>
            email: <span class="hljs-string">"user@email.com"</span> <span class="hljs-comment">// optional, email of user</span>
        },
        items: [
            {
                item_id: <span class="hljs-string">"4339"</span>
            },
            {
                item_id: <span class="hljs-string">"2300"</span>
            }
        ]
    };
    
    _predictry.push([<span class="hljs-string">'track'</span>, started_checkout_data]);</code></pre>
    
    <div class="checkbox">
        <input id="step_four" type="checkbox" value="step_four" name="step_four"> 
        <label for="step_four" class="checkbox_label">Check this if you have done step four.</label> 
    </div>
  </div>
  
  <div step="step_fourb" class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12">
    <h2 class="step_head">STEP 4B: TRACK BUY</h2>
    <p>To track successful purchases, you can include this on the thank you page or on any page after a purchase is completed.</p>
    
    <pre class="line-numbers prettyprint"><code class="language-javascript hljs "><span class="hljs-keyword">var</span> buy_data = {
        action: {
            name: <span class="hljs-string">"buy"</span>,
            total: <span class="hljs-number">1730.5</span>
        },
        user: {
            user_id: <span class="hljs-string">"100"</span>, <span class="hljs-comment">// identifier of user</span>
            email: <span class="hljs-string">"user@email.com"</span> <span class="hljs-comment">// optional</span>
        },
        items: [
            {
                item_id: <span class="hljs-string">"4339"</span>, <span class="hljs-comment">// identifier of item</span>
                qty: <span class="hljs-number">12</span>,
                sub_total: <span class="hljs-number">380</span>
            },
            {
                item_id: <span class="hljs-string">"4335"</span>,
                qty: <span class="hljs-number">20</span>,
                sub_total: <span class="hljs-number">1350.5</span>
            }
        ]
    };
    
    _predictry.push([<span class="hljs-string">'track'</span>, buy_data]);</code></pre>
    
    <div class="checkbox">
        <input id="step_fourb" type="checkbox" value="step_fourb" name="step_fourb"> 
        <label for="step_fourb" class="checkbox_label">Check this if you have done step four[B].</label> 
    </div>
  </div>
  
  <div id="step_five" class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12">
    <h2 class="step_head">STEP 5: Item Deletion (Optional)</h2>
    <p>When item removed from the catalog, or maybe sold out. To make it possible disappear from recommendation results is remove it.</p>
    <pre class="line-numbers prettyprint"><code class="language-javascript hljs "><span class="hljs-keyword">var</span> item_id = <span class="hljs-number">100</span>; <span class="hljs-comment">//item_id that will be removed</span>

    _predictry.push([<span class="hljs-string">'removeItem'</span>, item_id]);</code></pre>
    <div class="checkbox">
        <input id="step_five" type="checkbox" value="step_five" name="step_five"> 
        <label for="step_five" class="checkbox_label">Check this if you have done step five.</label> 
    </div>
  </div>
  
  <div class="modular_step sint_save col-sm-12 col-xs-12 col-md-12 col-lg-12">
    <a id="s_save" class="btn btn-primary btn-lg btn-block" onclick="saveintConfig('{{ $tenant_id }}', '{{ $site->api_key }}')">Save Configuration</a>
  </div>
</div>

<script type="text/javascript">
  var action_names = <?php echo json_encode($action_names); ?>;
</script>
@stop

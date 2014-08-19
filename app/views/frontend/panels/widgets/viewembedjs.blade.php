<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum, eligendi odio ut illo dolorum! Suscipit, dolores, eligendi, ea sit quia soluta doloremque quis est ducimus praesentium consequatur debitis modi quaerat!</p>
<p class="pt20">Put this JS code before closing <code>&lt;/head&gt;</code> </p>
<textarea class="form-control js-code" rows="10">
<script type="text/javascript">
	var _predictry = _predictry || [];
	(function() {
		var u = (("https:" === document.location.protocol) ? "https://" : "http://") + "api.predictry.com/v2/";
		_predictry.push(['setTenantId', "{$TENANT_ID}"], ['setApiKey', "{$API_KEY}"], ['setSessionID']);
		var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
		g.type = 'text/javascript', g.defer = true, g.async = true, g.src = u + 'p.js';
		s.parentNode.insertBefore(g, s);
	})();
</script>
</textarea>
<div class="clearfix "></div>
<p class="pt20">Put this JS code below wherever you want to place your Recommendation results.</p>
<?php
$reco_js_url = asset('reco.js');
$reco_js_url = str_replace("https", "", $reco_js_url);
$reco_js_url = str_replace("http", "", $reco_js_url);
?>
<textarea class="form-control js-code" rows="10">
<div data-user-id="USER_ID HERE" data-item-id="ITEM_ID HERE" class="PREDICTRY"></div>
<script type="text/javascript">
	(function() {
		var u = (("https:" === document.location.protocol) ? "https://" : "http://") + "api.predictry.com/v2/";
		_predictry.push(['setWidgetId', "{$WIDGET_ID}"]);
		var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
		g.type = 'text/javascript', g.defer = true, g.async = true, g.src = u + 'r.js';
		s.parentNode.insertBefore(g, s);
	})();
</script>
</textarea>
<div class="clearfix"></div>
<br>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum, eligendi odio ut illo dolorum! Suscipit, dolores, eligendi, ea sit quia soluta doloremque quis est ducimus praesentium consequatur debitis modi quaerat!</p>

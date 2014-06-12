<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum, eligendi odio ut illo dolorum! Suscipit, dolores, eligendi, ea sit quia soluta doloremque quis est ducimus praesentium consequatur debitis modi quaerat!</p>
<p class="pt20">Put this JS code before closing <code>&lt;/head&gt;</code> </p>
<textarea class="form-control js-code" rows="3"><script type="text/javascript">window.PE_tenantId = "{{ $site->name }}"; window.PE_apiKey = "{{ $site->api_key }}";</script><script src="{{ asset('api/v2/p.js') }}"></script></textarea>
<div class="clearfix "></div>
<p class="pt20">Put this JS code below wherever you want to place your Recommendation results.</p>
<?php
	$reco_js_url = asset('reco.js');
	$reco_js_url = str_replace("https", "", $reco_js_url);
	$reco_js_url = str_replace("http", "", $reco_js_url);
?>
<textarea class="form-control js-code" rows="6"><div data-item-id="ITEM_ID HERE" data-user-id="USER_ID HERE" data-session-id="SESSION_ID HERE" class="PREDICTRY"></div><script type="text/javascript">(function(){window.PE_platformVer = 1; window.PE_widgetId = "{{ $widget->id }}"; window.PE_recMode = "pe_text"; window.PE_recoType = "otherusersalsoviewed"; var d = document.createElement("script"); d.type = "text/javascript"; d.async = true; d.src = "http" + ("https:" === document.location.protocol?"s":"") + "{{ $reco_js_url }}"; var c = document.getElementsByTagName("script")[0]; c.parentNode.insertBefore(d, c)})();</script></textarea>
<div class="clearfix"></div>
<br>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum, eligendi odio ut illo dolorum! Suscipit, dolores, eligendi, ea sit quia soluta doloremque quis est ducimus praesentium consequatur debitis modi quaerat!</p>

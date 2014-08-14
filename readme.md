## Predictry Front End and Web Services API "pongo"

Trivial: *Pongo* is the scientific name of orangutans.

## Documentation


#Predictry JS SDK
---
##Usage
Documentation of how to implement Predictry JS SDK on the site.

##Version 
###2.0.0

###JS Embed Code
```js
<script type="text/javascript">
	var _predictry = _predictry || [];
	(function() {
		var u = (("https:" === document.location.protocol) ? "https://" : "http://") + "api.predictry.dev/v2/";
		_predictry.push(['setTenantId', "{$TENANT_ID}"], ['setApiKey', "{$API_KEY}"], ['setSessionID']);
		var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
		g.type = 'text/javascript';
		g.defer = true;
		g.async = true;
		g.src = u + 'p.js';
		s.parentNode.insertBefore(g, s);
	})();
</script>

```
###JS Embed Code (Minified)

```js
var _predictry=_predictry||[];(function(){var a=(("https:"===document.location.protocol)?"https://":"http://")+"api.predictry.dev/v2/";_predictry.push(["setTenantId","{$TENANT_ID}"],["setApiKey","{$API_KEY}"],["setSessionID"]);var e=document,c=e.createElement("script"),b=e.getElementsByTagName("script")[0];c.type="text/javascript";c.defer=true;c.async=true;c.src=a+"p.js";b.parentNode.insertBefore(c,b)})();
```

###Send View Action

```js
var data = {
    action: 'view',
    item_id: 105,
    user_id: 100,
    description: "Page title",
    item_properties: {
        img_url: "http://www.predictry.com/123.png",
        item_url: "http://www.predictry.com/123/",
        price: 250,
        inventory_qty: 100,
        category: "product",
        sub_category: "gadget",
        tags: "iphone, 5s, gold",
        brand: "apple",
        location: "malaysia",
        currency: "RM",
        start_date: 1407921883, //unix timestamp
        end_date: 1413763200 //unix timestamp
    },
    action_properties: {
        agent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36',
        ip_address: '175.143.39.120',
        email: 'user@website.com',
        rec: true //flag if the visitor viewed the item from recommendation, if not remove key (rec)
    }
};

_predictry.push(['trackView', data.user_id, data.item_id, data.description, data.item_properties, data.action_properties ]);
```

###Send Add To Cart Action
```js
var data = {
    action: 'view',
    item_id: 105,
    user_id: 100,
    description: "Page title",
    item_properties: {
        img_url: "http://www.predictry.com/123.png",
        item_url: "http://www.predictry.com/123/",
        price: 250,
        inventory_qty: 100,
        category: "product",
        sub_category: "gadget",
        tags: "iphone, 5s, gold",
        brand: "apple",
        location: "malaysia"
    },
    action_properties: {
        agent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36',
        ip_address: '175.143.39.120',
        qty: 5
    }
};

_predictry.push(['trackAddToCart', data.user_id, data.item_id, data.description, data.item_properties, data.action_properties ]);
```
####Methods and Params (Tracking)
With the exception of fields that are surrounded by `[]`, all fields specified are required for each request.

| Method | Params |
|---|---|
| trackView | user_id:{string}, item_id:{string}, description:{string}, [item_properties:{object}], [action_properties:{object}] | 
| trackAddToCart | user_id:{string}, item_id:{string}, description:{string}, [item_properties:{object}], [action_properties:{object}] | 
| trackStartedCheckout | user_id:{string}, [items:{object}] | 
| trackStartedPayment | user_id:{string}, [items:{object}] | 
| trackBuy | user_id:{string}, [items:{object}] | 
| sendAction | action_data:{object} | 
| sendBulkActions | action_data:{object} | 

###Send Buy Action
```js
var user_id = 200;
var items = [
    {
        item_id: 105,
        action_properties: {
            qty: 10,
            sub_total: 500,
            rec: true //flag if the product's coming from recommendation
        }
    },
    {
        item_id: 106,
        action_properties: {
            qty: 1,
            sub_total: 200
        }
    }
];

_predictry.push(['trackBuy', user_id, items]);
```
####Methods and Params (Tracking)

| Method | Params |
|---|---|
| getRecommendedItems | widget_id:{string}, user_id:{string}, item_id:{string}, callback:{function} | 
| setWidgetId | widget_id:{string} | 

###Pull Recommendation (Manual)
```js
var data = {
    widget_id: 18, //generated from the dashboard, contain (widget detail, algorithm, filters) 
    user_id: 100,
    item_id: 105
};

var recommendation_data;
_predictry.push(['getRecommendedItems', data.widget_id, data.user_id, data.item_id, function() {
        recommendation_data = predict.getRecentRecommendedItems();
        if (recommendation_data !== undefined) {
            //@todo add your line here to extract the recommendation as anything you want
        }
    }
]);
```

Sample Response (Object)
```json
{
   "status":"success",
   "recomm":[
      {
         "id":"9153",
         "alias_id":2396,
         "description":"Take One Original Baby Bites",
         "created_at":"2014-05-08 07:32:36",
         "item_properties":{
            "item_url":"predictry.com\/product\/take-one-original-baby-bites",
            "img_url":"\/\/s3-ap-southeast-1.amazonaws.com\/media.predictry.com\/newmedia\/460x\/i\/m\/IMG_7468.JPG",
            "price":"2.65"
            ....
         }
      },
      {
         "id":"6205",
         "alias_id":754,
         "description":"Heinz Teething Rusks ",
         "created_at":"2014-05-08 07:32:21",
         "item_properties":{
            "item_url":"alpha.predictry.com\/product\/heinz-teething-rusks",
            "price":"4.95"
            ...
         }
      },
      {
         "id":"8095",
         "alias_id":1808,
         "description":"Merries Diapers - L",
         "created_at":"2014-05-08 07:32:31",
         "item_properties":{
            "item_url":"predictry.com\/product\/merries-diapers-l",
            "img_url":"\/\/s3-ap-southeast-1.amazonaws.com\/media.predictry.com\/newmedia\/460x\/i\/m\/IMG_4177.JPG",
            "price":"24.25"
            ...
         }
      },
      {
         "id":"11653",
         "alias_id":4158,
         "description":"Pampers Baby Dry Diapers L Size 4",
         "created_at":"2014-05-08 07:32:56",
         "item_properties":{
            "item_url":"predictry.com\/product\/pampers-baby-dry-diapers-l-size-4",
            "img_url":"\/\/s3-ap-southeast-1.amazonaws.com\/media.predictry.com\/newmedia\/460x\/i\/m\/RM_0415F_11653.JPG",
            "price":"22"
            ...
         }
      }
   ],
   "widget_instance_id":501
}
```
####Pull Recommendation (Automatic)
Place the code below on any part of the site that you want the recommendation to be appear. The code below will generate simple list elements that will be append on DOM.
```js
<div data-user-id="{$USER_ID}" data-item-id="{$ITEM_ID}" class="PREDICTRY"></div>
<script type="text/javascript">
    (function() {
        var u = (("https:" === document.location.protocol) ? "https://" : "http://") + "api.predictry.dev/v2/";
        _predictry.push(['setWidgetId', "{$WIDGET_ID}"]);
        var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
        g.type = 'text/javascript', g.defer = true, g.async = true, g.src = u + 'r.min.js';
        s.parentNode.insertBefore(g, s);
    })();
</script>
```

##Changelog
- 2.0.
    - Revamp the class
    - Asynchronous
    - Added single function for single action
- 1.0.0
    - Initial
    - Alpha Release


> Any questions, please contact [rifki@predictry.com](rifki@predictry.com)

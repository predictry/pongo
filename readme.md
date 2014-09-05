## Predictry Front End and Web Services API "pongo"

Trivial: *Pongo* is the scientific name of orangutans.

## Documentation


#Predictry JS SDK (v.0.2.2)
---
##Usage
Documentation of how to implement Predictry JS SDK on the site.

##Version 
###2.0.0

###*STEP 1*: GETTING STARTED (EMBED JS)
To start tracking with the Predictry JavaScript library, just paste the following code into the page you want to track just before the `</head>` tags. Make sure to change ***‘YOUR_API_KEY’*** and ***‘YOUR_TENANT_ID’*** accordingly that have been provided to you. 

This snippet of code will load our library asynchronously on your page which doesn’t slow down the loading time of you page

We create a variable called _predictry that will be available on any pages. You will use it to send any data to us.

Note: You need to include this on every page of your website.

```js
<script type="text/javascript">
	var _predictry = _predictry || [];
	(function() {
		var u = (("https:" === document.location.protocol) ? "https://" : "http://") + "dpv9g3v1sio9.cloudfront.net/v2/";
		_predictry.push(['setTenantId', "YOUR_TENANT_ID"], ['setApiKey', "YOUR_API_KEY"]);
		var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
		g.type = 'text/javascript';
		g.defer = true;
		g.async = true;
		g.src = u + 'p.min.js';
		s.parentNode.insertBefore(g, s);
	})();
</script>

```
If you prefer you can opt for a minified version
```js
var _predictry=_predictry||[];(function(){var a=(("https:"===document.location.protocol)?"https://":"http://")+"dpv9g3v1sio9.cloudfront.net/v2/";_predictry.push(["setTenantId","YOUR_TENANT_ID"],["setApiKey","YOUR_API_KEY"]);var e=document,c=e.createElement("script"),b=e.getElementsByTagName("script")[0];c.type="text/javascript";c.defer=true;c.async=true;c.src=a+"p.min.js";b.parentNode.insertBefore(c,b)})();
```


###*STEP 2*: TRACK VIEW 
On every product/item page that you would like to track, include this also.

```js
var view_item_data = {
    // If user is not logged in, this object is not required
    action: {
        name: "view"
    },
    user: {
        user_id: "100", //alphanumeric (unique)
        email: "user@email.com"
    },
    items: [
        {
            item_id: "105", //alphanumeric (unique)
            name: "Item name",
            price: 250.12,
            img_url: "http://www.predictry.com/123.png",
            item_url: "http://www.predictry.com/123", //without trailing slash

            //OPTIONALS - Provide if available so that recommendations would be better
            discount: "23%", //the discount that is being offered. If the discount is in amount 23.10 without the percentage
            description: "Description of the item",
            inventory_qty: 100, //how many items left
            category: "Electronics",
            sub_category: "Phone",
            tags: ["iphone", "5s", "gold"], //this is an array. If there's only one item also enclosed in array ["iphone"] 
            brand: "apple",
            locations: ["kuala lumpur", "jakarta"], //cities that this is sold if applicable
            start_date: 1407921883, //unix timestamp - when is the first that this will be sold? If applicable, if not, ignore.
            end_date: 1417921883 //unix timestamp - when is the last day that it will be sold? If applicable, if not, ignore.	
        }
    ]
};

_predictry.push(['track', view_item_data]);
```


###*STEP 3*: TRACK BUY 
To track successful purchases, you can include this on the thank you page or on any page after a purchase is completed.
```js
var buy_data = {
    action: {
        name: "buy",
        total: 1730.5
    },
    user: {
        user_id: "100", //alphanumeric (unique)
        email: "user@email.com"
    },
    items: [
        {
            item_id: "105", //alphanumeric (unique)
            qty: 12,
            sub_total: 380
        },
        {
            item_id: "106",
            qty: 20,
            sub_total: 1350.5
        }
    ]
};

_predictry.push(['track', buy_data]);
```



##Changelog
- 0.2.1
    - Revamp the class
    - Asynchronous
    - Added single function for single action
- 0.1.0
    - Initial
    - Alpha Release


> Any questions, please contact [rifki@predictry.com](rifki@predictry.com)
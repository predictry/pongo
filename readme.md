## Predictry Front End and Web Services API "pongo"

Trivial: *Pongo* is the scientific name of orangutans.

## Documentation


#Predictry JS SDK
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

```
	<script type="text/javascript">
		var _predictry = _predictry || [];
		(function() {
		    _predictry.push(['setTenantId', "YOUR_TENANT_ID"], ['setApiKey', "YOUR_API_KEY"]);
		    var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
		    g.type = 'text/javascript';
		    g.defer = true;
		    g.async = true;
		    g.src = '//d2gq0qsnoi5tbv.cloudfront.net/v2/p.min.js';
		    s.parentNode.insertBefore(g, s);
		})();
	</script>
	
```

If you prefer you can opt for a minified version

```
var _predictry=_predictry||[];(function(){_predictry.push(["setTenantId","YOUR_TENANT_ID"],["setApiKey","YOUR_API_KEY"]);var e=document,c=e.createElement("script"),b=e.getElementsByTagName("script")[0];c.type="text/javascript";c.defer=true;c.async=true;c.src="//d2gq0qsnoi5tbv.cloudfront.net/v2/p.min.js";b.parentNode.insertBefore(c,b)})();
```

###*STEP 2*: TRACK VIEW 
On every product/item page that you would like to track, include this also.

```
var view_data = {
    action: {
        name: "view"
    },
    // If user is not logged in, this object is not required
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
            locations: {
                country: "Indonesia", 
                city: "Jakarta"
            }, //locations that this is sold if applicable
            start_date: 1407921883, //unix timestamp - when is the first that this will be sold? If applicable, if not, ignore.
            end_date: 1417921883 //unix timestamp - when is the last day that it will be sold? If applicable, if not, ignore.	
        }
    ]
};

_predictry.push(['track', view_data]);
```

###*STEP 3*: TRACK ADD TO CART 
To track add to cart action, you can include this on add to cart button event click.

```
var add_to_cart_data = {
    action: {
        name: "add_to_cart"
    },
    // If user is not logged in, this object is not required
    user: {
        user_id: "100", //alphanumeric (unique)
        email: "user@email.com"
    },
    items: [
        {
            item_id: "105", //alphanumeric (unique)
            qty: 12
        }
    ]
};

_predictry.push(['track', add_to_cart_data]);
```

###*STEP 4*: TRACK STARTED CHECKOUT
To track started checkout action, you can include this on checkout button event click.

```
var started_checkout_data = {
    action: {
        name: "started_checkout"
    },
    // If user is not logged in, this object is not required
    user: {
        user_id: "100", //alphanumeric (unique)
        email: "user@email.com"
    },
    items: [
        {
            item_id: "105"
        },
        {
            item_id: "106"
        }
    ]
};

_predictry.push(['track', started_checkout_data]);
```

###*STEP 5*: TRACK STARTED PAYMENT
To track started checkout action, you can include this on checkout button event click.

```
var started_payment_data = {
    action: {
        name: "started_payment",
		
		//OPTIONALS - Provide if available, for better preview on the dashboard.
        payment_method: "paypal",
	    store_id: "2500", //alphanumeric (unique), applicable for marketplace site.
    },
    user: {
        user_id: "100", //alphanumeric (unique)
        email: "user@email.com"
    },
    items: [
        {
            item_id: "105" //alphanumeric (unique)
        },
        {
            item_id: "106"
        }
    ]
};
_predictry.push(['track', started_payment_data]);
```

###*STEP 6*: TRACK BUY 
To track successful purchases, you can include this on the thank you page or on any page after a purchase is completed.

```
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
- 0.2.2
	- Add more actions (add_to_cart, started_checkout, started_payment)
- 0.2.1
    - Revamp the class
    - Asynchronous
    - Added one global function to track.
    - Revamp the data format.
    
- 0.1.0
    - Initial
    - Alpha Release


> Any questions, please contact [rifki@predictry.com](rifki@predictry.com)
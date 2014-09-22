## Predictry Front End and Web Services API "pongo"

Trivial: *Pongo* is the scientific name of orangutans.

## Documentation

##JS SDK Implementation Document For Predictry Recommendation Engine##

###Part 1: (Sending Actions)###
###Version 0.2.3###
---

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
        user_id: "100", // identifier of user
        email: "user@email.com"
    },
    items: [
        {
            item_id: "2300", //alphanumeric (unique)
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
            tags: ["iphone", "5s", "gold"], //this is an array. If there is only one item also enclosed in array ["iphone"] 
            brand: "apple",
            locations: {
                country: "Indonesia", 
                city: "Jakarta"
            } //locations that this is sold if applicable
    
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
        user_id: "100", // identifier of user
        email: "user@email.com"
    },
    items: [
        {
            item_id: "4457", //alphanumeric (unique)
            qty: 12
        }
    ]
};

_predictry.push(['track', add_to_cart_data]);
```

###*STEP 4*: TRACK STARTED CHECKOUT
To track started checkout action, you can include this on checkout button event click after user completed the form. So in this matter, user information is compulsory.

```
var started_checkout_data = {
    action: {
        name: "started_checkout"
    },
    // User info below are compulsory.
    user: {
        user_id: "100", // identifier of user
        email: "user@email.com" // email of user
    },
    items: [
        {
            item_id: "4339"
        },
        {
            item_id: "2300"
        }
    ]
};

_predictry.push(['track', started_checkout_data]);
```

###*STEP 5*: TRACK BUY 
To track successful purchases, you can include this on the thank you page or on any page after a purchase is completed.

```
var buy_data = {
    action: {
        name: "buy",
        total: 1730.5
    },
    user: {
        user_id: "100", // identifier of user
        email: "user@email.com"
    },
    items: [
        {
            item_id: "4339", // identifier of item
            qty: 12,
            sub_total: 380
        },
        {
            item_id: "4335",
            qty: 20,
            sub_total: 1350.5
        }
    ]
};

_predictry.push(['track', buy_data]);
```

###Part 2: (Getting Recommendation)###

Place the code below on any part of the site that you want the recommendation to be appearing. The code below will generate simple list elements that will be append on DOM.

Noted: You have to first place predictry Embed JS code on </head>. Refer to send actions documentation.

###*STEP 1*: Display recommendations
```
<ins class="predictry" data-predictry-widget-id="{$WIDGET_ID}" data-predictry-user-id="{$USER_ID}" data-predictry-item-id="{$ITEM_ID}"></ins>
<script type="text/javascript">_predictry.push(['getWidget']);</script>
```

###*STEP 2*: Get Recommendations in JSON
```
<script type="text/javascript">
    function testCallback(response) {
        console.log(response);
    }
</script>

<ins class="predictry" data-predictry-widget-id="{$WIDGET_ID}" data-predictry-user-id="{$USER_ID}" data-predictry-item-id="{$ITEM_ID}" data-predictry-callback="testCallback"></ins>
<script type="text/javascript">_predictry.push(['getWidget']);</script>
```

####Sample Response
```
{
  "status":200,
  "data":{
    "items":[
      {
        "id":"6014",
        "name":"Kodomo Baby Shampoo ",
        "item_url":" predictry.com /product/kodomo-baby-shampoo",
        "img_url":"// predictry.com /150x/i/m/img_7091.jpg",
        "price":"7.6"
      },
      {
        "id":"5864",
        "name":"Pokka Jasmine Green Tea ",
        "item_url":"predictry.com/product/pokka-jasmine-green-tea",
        "img_url":"// predictry.com /150x/i/m/img_6986.jpg",
        "price":"4.05"
      }
    ]
  },
    "widget_instance_id": 50
}
```

Both {$ITEM_ID} and {$USER_ID} are optional for some algorithms. 

FAQ:

Q: Can I have multiple widgets on the same page?
A: Yes you can.


Q: Can I have my own css style of the recommendation lists?
A: Yes, of course. Basic displays of recommendation will be normal list that is using basic ``<ul>`` wrapped with ``<ins class='predictry'></ins>``. If you need more advance styling / layout, you can get recommendation in JSON by using callback function to handle the response.

##Changelog
- 0.2.3
	- Getting recommendation along with filtering.
- 0.2.2
	- Add more actions (add_to_cart, started_checkout)
- 0.2.1
    - Revamp the class
    - Asynchronous
    - Added one global function to track.
    - Revamp the data format.
    
- 0.1.0
    - Initial
    - Alpha Release


> Any questions, please contact [rifki@predictry.com](rifki@predictry.com)

var bulk_action = {
    action: 'buy',
    user_id: 100,
    session_id: "jZG1pZnYnAY61Zs",
    actions: [
        {
            item_id: 105,
            description: "Product title",
            item_properties: {
                img_url: "http://www.predictry.com/123.jpg",
                item_url: "http://www.predictry.com/123.php",
                price: 50
            },
            action_properties: {
                rec: true
            }
        },
        {
            item_id: 106,
            description: "Product title 106",
            item_properties: {
                img_url: "http://www.predictry.com/106.jpg",
                item_url: "http://www.predictry.com/106.php",
                price: 150
            }
        }
    ]
};
var action_data = {
    action: 'view',
    item_id: 105,
    user_id: 100,
    session_id: "CifaBq62QMnU9QP",
    description: "Page title",
    item_properties: {
        img_url: "http://www.predictry.com/123.png",
        item_url: "http://www.predictry.com/123/",
        price: 250
    }
};
//_predictry.push(['trackView', USER_ID, ITEM_ID, DESCRIPTION, ITEM_PROPERTIES, ACTION_PROPERTIES ]);
//_predictry.push(['trackView', action_data.user_id, action_data.item_id, action_data.description, action_data.item_properties, {} ]);
//_predictry.push(['trackAddToCart', action_data.user_id, action_data.item_id, action_data.description, action_data.item_properties, {} ]);

var bulk_action_data_2 = {
    action: 'buy',
    user_id: 100,
    session_id: "jZG1pZnYnAY61Zs",
    actions: [
        {
            item_id: 105,
            action_properties: {
                qty: 10,
                sub_total: 500
            }
        },
        {
            item_id: 106,
            action_properties: {
                qty: 1,
                sub_total: 200
            }
        }
    ]
};
var actions = [
    {
        item_id: 105,
        action_properties: {
            qty: 10,
            sub_total: 500,
            rec: true
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
//_predictry.push(['trackBuy', 200, actions]);


var str_recommendation_data = "";
predict.getRecommendedItems(18, 35851, 9154, function() {
    str_recommendation_data = predict.getRecentRecommendedItems();
});
//_predictry.push(['getRecommendedItems', WIDGET_ID, USER_ID, ITEM_ID, CALLBACK]);

var recommendation_data;
_predictry.push(['getRecommendedItems', 18, 35851, 9154, function() {
        recommendation_data = predict.getRecentRecommendedItems();
        if (recommendation_data !== undefined && typeof recommendation_data === 'object') {

            //@todo add function to extract the recommendation as anything you want
            console.log(recommendation_data);
            alert("got recommendation");
        }
    }
]);
var view_item_data = {
    // If user is not logged in, this object is not required
    user: {
        user_id: "100", //alphanumeric (unique)
        email: "user@email.com",
        //OPTIONALS - Provide if available so that recommendations would be better
        name: "John Doe",
        country: "Australia"

    },
    item: {
        item_id: "105", //alphanumeric (unique)
        name: "Item name",
        price: 250.12,
        currency: "MYR", //currency code based on http://www.xe.com/iso4217.php
        img_url: "http://www.predictry.com/123.png",
        item_url: "http://www.predictry.com/123", //without trailing slash


        //OPTIONALS - Provide if available so that recommendations would be better
        discount: "23%", //the discount that is being offered. If the discount is in amount 23.10 without the percentage and quotes
        description: "Description of the item",
        inventory_qty: 100, //how many items left
        category: "Electronics",
        sub_category: "Phone",
        tags: ["iphone", "5s", "gold"], //this is an array. If there's only one item also enclosed in array ["iphone"] 
        brand: "apple",
        locations: ["kuala lumpur", "jakarta"], //cities that this is sold if applicable
        start_date: 1407921883, //unix timestamp - when is the first that this will be sold? If applicable, if not, ignore.
        end_date: 1417921883, //unix timestamp - when is the last day that it will be sold? If applicable, if not, ignore.  

    }
};
_predictry.push(['trackView', view_item_data]);


var buy_data = {
    user: {
        user_id: "100", //alphanumeric (unique)
        email: "user@email.com",
        //OPTIONALS - Provide if available so that recommendations would be better
        name: "John Doe",
        country: "Australia"
    },
    buy: {
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
        ],
        total: 1730.5,
        currency: "MYR", //currency code based on http://www.xe.com/iso4217.php

        //OPTIONALS
        promo_code: "ABC123", //alphanumeric
        promo_code_discount: "23%" //the discount that is being offered by the promo code. If the discount is in amount 23.10 without the percentage and quotes
    }
};

_predictry.push(['trackBuy', buy_data]);


/**
 * NEW PAYLOADS
 */

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
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
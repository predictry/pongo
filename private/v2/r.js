(function() {
    var elem_predictries = document.getElementsByClassName("PREDICTRY");
    var elem_predictry = null;
    var pe_recommendation = null;
    var isPredictObjExists = false;

    for (var i = 0; i < elem_predictries.length; ++i) {
        var elem_predictry = elem_predictries[i];
        break;
    }

    function isPredictObjExistsCallback(x) {
        if (!isPredictObjExists) {
            isPredictObjExists = (typeof predict === 'object') ? true : false;
            if (isPredictObjExists) {
                fetchRecomendation();
            }
        }
    }

    function fetchRecomendation() {
        if (elem_predictry !== null && typeof predict === 'object') {
            var pe_item_id = elem_predictry.getAttribute("data-item-id");
            var pe_user_id = (elem_predictry.getAttribute("data-user-id") !== null) ? elem_predictry.getAttribute("data-user-id") : Predictry.getSessionUserID();

            var data = {
                item_id: pe_item_id,
                user_id: pe_user_id,
                widget_id: predict.getWidgetID()
            };

            var recommendation_data;
            _predictry.push(['getRecommendedItems', data.widget_id, data.user_id, data.item_id, function() {
                    recommendation_data = predict.getRecentRecommendedItems();
                    if (recommendation_data !== undefined && typeof recommendation_data === 'object') {
                        _predictry.push(['drawList', elem_predictry, recommendation_data]);
                    }
                }
            ]);
        }
    }

//try to check if the predict object already available for 5 times (1.5s, 3s, 4.5s, 6s);
    for (var i = 0; i < 5; i++) {
        setTimeout(function(x) {
            return function() {
                isPredictObjExistsCallback(x);
            };
        }(i), 1500 * i);
    }
})();

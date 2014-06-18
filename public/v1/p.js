var Predictry = (function() {

    var PREDICTRY_API_URL = "http://dashboard.predictry.dev/api/v1/";
    var widget_instance_id = null;
    var win = window;

    var PE_defaults = {
        action: null,
        user_id: null,
        session_id: null,
        item_id: null,
        description: ""
    };

    var PE_options = {
        widgetId: null,
        platformVer: null,
        recoType: null
    };

    var response = data = reco_data = {};
    var compulsary_params = optional_params = null;
    var cart_id = -1;

    function init()
    {
        win = window;
        reco_data = data = {};
        this.cart_id = getCartID();

        PE_options.widgetId = win.PE_widgetId;
        PE_options.platformVer = win.PE_platformVer;
        PE_options.recoType = win.PE_recoType;
        PE_defaults.session_id = this.getSessionID();

        compulsary_params = new Array("user_id", "action", "item_id", "session_id", "description");
        optional_params = new Array("item_properties", "action_properties");
        response = {
            "status": 'success',
            "message": ''
        };

        var query_params = getQueryParams(document.location.search);
        if (query_params !== undefined && typeof query_params === 'object' && query_params.predictry_src !== undefined) {
            widget_instance_id = query_params.predictry_src;
        }
    }

    /**
     * Set Action Data
     * 
     * @param {type} data
     * @returns {Boolean}
     */
    function setActionData(data)
    {
        data = eExtend(data, PE_defaults);

        if (widget_instance_id !== null)
        {
            if (data.action_properties !== undefined)
            {
                data.action_properties.rec = true;
            }
            else if (data.action_properties === undefined)
            {
                data.action_properties = {rec: true};
            }
        }

        if (data.action === 'complete_purchase')
        {
            if (data.action_properties !== undefined)
                data.action_properties.cart_id = this.cart_id;
            else if (data.action_properties === undefined)
                data.action_properties = {cart_id: this.cart_id};

            this.cart_id = getCartID();
        }

        for (var key in data)
        {
            if (!inArray(key, optional_params))
            {
                if (((data[key] === "" || (data[key] === null)) && inArray(key, compulsary_params)))
                {
                    response.status = "failed";
                    response.message = key + ' cannot be empty';
                    return false;
                }
            }

            if (key === "item_properties") {
                for (var key2 in data[key]) {
                    data['item_properties[' + key2 + ']'] = data[key][key2];
                }
                delete data.item_properties;
            }

            if (key === "action_properties") {
                for (var key2 in data[key]) {
                    data['action_properties[' + key2 + ']'] = data[key][key2];
                }
                delete data.action_properties;
            }
        }

        data.action_type = 'single';
        return data;
    }

    /**
     * Set Tracking Data
     * 
     * @param {type} data
     * @returns {Boolean}
     */
    function setTrackingData(data) {
        reco_data = eExtend(data, PE_defaults);
        for (var key in data)
        {
            if (!inArray(key, optional_params)) {
                if (((data[key] === "" || (data[key] === null)) && inArray(key, compulsary_params)))
                {
                    response.status = "failed";
                    response.message = key + ' cannot be empty';
                    return false;
                }
            }

            if (key === "action_properties") {
                for (var key2 in data[key]) {
                    data['action_properties[' + key2 + ']'] = data[key][key2];
                }
                delete data.action_properties;
            }
        }
        return true;
    }

    /**
     * Send Action
     * 
     * @param {type} data
     * @returns {undefined}
     */
    function sendAction(data) {
        var ready_data = false;
        if (data !== undefined) {
            ready_data = setActionData(clone(data));
        } else {
            response.message = "action data undefined";
            response.status = "failed";
        }

        if (ready_data !== false) {
            var response = callPredictry(ready_data);
            //this is to set cart log, and set cart session
            if (response.status === 'success' && ready_data.action === 'add_to_cart')
            {
                if (widget_instance_id !== null)
                {
                    if (data.action_properties !== undefined)
                        data.action_properties.rec = true;
                    else if (data.action_properties === undefined)
                        data.action_properties = {rec: true};
                }

                if (data.action_properties !== undefined) {
                    var qty = (data.action_properties.qty !== undefined) ? data.action_properties.qty : 1;
                    setCartLog(data.item_id, qty, 'added');

                    //cart session only for item that coming from recommendation
                    if (data.action_properties.rec !== undefined && data.action_properties.rec)
                        setCartSession(data.item_id);
                } else
                    setCartLog(data.item_id, 1, 'added');

            }
            if (response.status === 'success' && ready_data.action === 'complete_purchase')
                createPredictrySession();

            return response;
        } else {
            return JSON.stringify(response);
        }
    }

    /**
     * Send Tracking 
     * 
     * @param {type} data
     * @returns {undefined}
     */
    function sendTracking(data) {
        sendAction(data);
    }

    /**
     * Predictry Calling 
     * 
     * @param {object} action_data
     * @returns {object}
     */
    function callPredictry(action_data) {
        return makeJqueryAjaxCall("POST", PREDICTRY_API_URL + "predictry?", action_data);
    }

    /**
     * Make A Call
     * 
     * @param {String} method
     * @param {String} url
     * @returns {object}
     */
    function makeACall(method, url) {
        if (url === undefined)
            return;

        var http = new XMLHttpRequest();
        http.overrideMimeType("application/json");
        http.open(method, url, false);
        http.setRequestHeader("X-Predictry-Server-Tenant-ID", win.PE_tenantId);
        http.setRequestHeader("X-Predictry-Server-Api-Key", win.PE_apiKey);
        http.onreadystatechange = function() {//Call a function when the state changes.
            if (http.readyState === 4 && http.status === 200) {
                jsonResponse = JSON.parse(http.responseText);
                return jsonResponse;
            }
        };
        http.send(null);
    }

    /**
     * jQuery Ajax Call
     * 
     * @param {string} method
     * @param {string} url
     * @param {object} params
     * @returns {undefined|ready_data|String}
     */
    function makeJqueryAjaxCall(method, url, params)
    {
        var result = "";

        if (typeof jQuery !== 'undefined') {
            jQuery.ajax({
                type: method,
                url: url,
                async: false,
                data: params,
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader("X-Predictry-Server-Tenant-ID", win.PE_tenantId);
                    xhr.setRequestHeader("X-Predictry-Server-Api-Key", win.PE_apiKey);
                },
                success: function(data) {
                    result = data;
                },
                error: function(data) {
                    result = data;

                }
            });
        } else {
            console.log("error >> jQuery required ");
            return;
        }

        return result;
    }

    /**
     * Set Bulk Actions
     * 
     * @param {object} bulk_action_data
     * @returns {Boolean}
     */
    function setBulkActionData(bulk_action_data) {
        var bulk_compulsary_params = new Array("item_id", "description");
        var bulk_optional_params = new Array("item_properties", "action_properties");

        for (var i = 0; i < bulk_action_data.actions.length; i++)
        {
            for (var key in bulk_action_data.actions[i])
            {
                if (!inArray(key, bulk_optional_params))
                {
                    if (((bulk_action_data.actions[i][key] === "" || (bulk_action_data.actions[i][key] === null)) && inArray(key, bulk_compulsary_params)))
                    {
                        response.status = "failed";
                        response.message = key + ' cannot be empty';
                        return false;
                    }
                }

                if (bulk_action_data.action === 'complete_purchase' && key === 'item_id')
                {
                    var item_id = bulk_action_data.actions[i][key];
                    var cartSession = getCartSession();
                    var cartItemIDs = cartSession.c;
                    var action = bulk_action_data.actions[i];

                    if (action.action_properties !== undefined)
                        action.action_properties.cart_id = this.cart_id;
                    else if (action.action_properties === undefined)
                        action.action_properties = {cart_id: this.cart_id};

                    if (inArray(item_id, cartItemIDs))
                    {
                        if (action.action_properties !== undefined)
                            action.action_properties.rec = true;
                        else if (action.action_properties === undefined)
                            action.action_properties = {rec: true};
                    }
                }
            }
        }

        this.cart_id = getCartID();

        bulk_action_data.session_id = getSessionID();
        bulk_action_data.action_type = 'bulk';
        return bulk_action_data;
    }

    /**
     * Send Bulk Actions
     * 
     * @param {type} data
     * @returns {object}
     */
    function sendBulkActions(data) {

        var ready_data = false;

        if (data !== undefined) {
            if (data.action === undefined) {
                response.message = "action name undefined";
                response.status = "failed";
            }

            if (data.user_id === undefined) {
                response.message = "user id undefined";
                response.status = "failed";
            }
        } else {
            response.message = "action data undefined";
            response.status = "failed";
        }

        ready_data = setBulkActionData(data);
        if (ready_data !== false) {
            return makeJqueryAjaxCall("POST", PREDICTRY_API_URL + "predictry?", ready_data);
        } else {
            console.log(JSON.stringify(response));
        }
    }


    /**
     * Get Recommendation 
     * 
     * @param {type} data
     * @param {type} options
     * @returns {object|undefined}
     */
    function getRecommendedItems(data)
    {
        reco_data = eExtend(data, PE_defaults);
        reco_data = eExtend(reco_data, PE_options);

        if (reco_data.algo === undefined)
            reco_data.algo = "otherusersalsoviewed";

        var drawingCallback;
        var params = buildUrl(reco_data);
        reco_url = PREDICTRY_API_URL + "recommendation?" + params;

        if (typeof (noDrawingNeeded) === 'undefined')
        {
            if (window.PE_recoType === "pe_grid")
            {
                drawingCallback = drawListGridRecommendation;
            } else {
                drawingCallback = drawTextListRecommendation;
            }
        } else {
            drawingCallback = null;
        }

        return makeJqueryAjaxCall("GET", PREDICTRY_API_URL + "recommendation?", reco_data);
    }

    /**
     * Drawing recommendation as LIST
     * 
     * @param {type} json
     * @returns {undefined}
     */
    function drawTextListRecommendation(json)
    {
        if (json !== null) { // if no error show recommendations

            try {
                var items = json.recomm;
            } catch (e) {
                return;
            }

            /* when the object is already in array format, this block will not execute */
            if ("undefined" == typeof (items.length)) {
                items = new Array(items);
            }

            // display recommendations in the DIV layer 'recommendation'
            if (items.length > 0) {
                listString = "<ul>";

                for (x = 0; x < items.length; x++) {
                    listString +=
                            "<li><a href=\"" + items[x].item_properties.item_url + "\">"
                            + items[x].description +
                            "</a>" +
                            "</li>";
                }
                elem_predictry.innerHTML += listString + "</ul>";
            }
        }
    }

    function drawListGridRecommendation(json)
    {
        return "work in progress";
    }

    /**
     * 
     * @param {type} item_id
     * @param {type} description
     * @param {type} properties
     * @returns {undefined}
     */
    function setItemMeta(item_id, description, properties) {
        var data = {
            item_id: item_id,
            description: description,
            properties: properties
        };
        return makeJqueryAjaxCall("POST", PREDICTRY_API_URL + "item?", data);
    }

    /**
     * Get Widget Instance ID
     */
    function getWidgetInstance(widget_id, properties, rec_items) {

        var data = {
            widget_id: widget_id,
            properties: properties,
            rec_items: rec_items,
            session: getSessionID()
        };

        return makeJqueryAjaxCall("POST", PREDICTRY_API_URL + "widget?", data);
    }

    /**
     * Get Cart ID
     */
    function getCartID() {
        var result = makeJqueryAjaxCall("POST", PREDICTRY_API_URL + "cart?", {session: getSessionID()});
        var cart_id = false;
        if (result.status === 'success')
            cart_id = result.response.cart_id;

        return cart_id;
    }

    /**
     * Set Cart Log
     * 
     * @param {type} item_id
     * @param {type} qty
     * @param {type} event
     * @returns {ready_data|undefined|data|_L1|PE_defaults|@exp;reco_data|@exp;copy|@exp;eExtend@pro;options|reco_data|copy|type|String}
     */
    function setCartLog(item_id, qty, event) {
        var data = {
            cart_id: cart_id,
            item_id: item_id,
            qty: qty,
            event: event
        };
        return makeJqueryAjaxCall("POST", PREDICTRY_API_URL + "cartlog?", data);
    }

    /**
     * Update Cart Item Session
     */
    function setCartSession(item_id) {
        var cartSession = getCartSession();
        cartItemIDs = cartSession.c; //array
        if (item_id !== undefined && !inArray(item_id, cartItemIDs)) {
            cartItemIDs.push(item_id);
        }
        return createPredictrySession(cartItemIDs);
    }

    /**
     * Remove item cart session
     * 
     * @param {type} item_id
     * @returns {undefined}
     */
    function removeItemCartSession(item_id) {
        var cartSession = getCartSession();
        cartItemIDs = cartSession.c; //array

        if (item_id !== undefined && inArray(item_id, cartItemIDs))
            cartItemIDs.pop(item_id);
        return createPredictrySession(cartItemIDs);
    }

    /**
     * Get Cart Session
     */
    function getCartSession() {
        var PECookieName = "predictry";
        var result = document.cookie.match(new RegExp(PECookieName + '=([^;]+)'));
        if (result && JSON.parse(result[1]))
            return JSON.parse(result[1]);
        else
            return createPredictrySession();
    }

    /**
     * Create Predictry Session
     * @param {array} cart_value
     * @returns {_L1.createPredictrySession.value}
     */
    function createPredictrySession(cart_value) {
        var name = "predictry";
        var value = {c: [], s: getSessionID()};

        if (cart_value !== undefined)
            value = {c: cart_value, s: getSessionID()};

        String((new Date()).getTime()).replace(/\D/gi, '');
        document.cookie = name + "=" + JSON.stringify(value) + "; path=/";
        return value;
    }

    /**
     * Extend object
     * 
     * @param {type} options
     * @param {type} defaults
     * @returns {PE_defaults|copy}
     */
    function eExtend(options, defaults) {
        var target = options;

        for (var propertyName in defaults) {
            src = target[ propertyName ];
            copy = defaults[ propertyName ];

            if (src != null) {
                continue;
            } else if (copy !== undefined) {
                target[ propertyName ] = copy;
            }
        }
        // Return the modified object
        return target;
    }

    /**
     * Build URL params
     * 
     * @param {type} parameters
     * @param {type} bulk
     * @returns {String|buildUrl.qs}
     */
    function buildUrl(parameters, bulk) {
        var qs = "";
        for (var key in parameters) {
            var value = parameters[key];
            if (bulk !== undefined)
                qs += encodeURIComponent(key) + "[]=" + encodeURIComponent(value) + "&";
            else
                qs += encodeURIComponent(key) + "=" + encodeURIComponent(value) + "&";
        }
        if (qs.length > 0) {
            qs = qs.substring(0, qs.length - 1); //chop off last "&"
        }
        return qs;
    }

    /**
     * Check needle in array haystack
     * 
     * @param {String|Number} needle
     * @param {Array} haystack
     * @returns {Boolean}
     */
    function inArray(needle, haystack) {
        var length = haystack.length;
        for (var i = 0; i < length; i++)
        {
            if (haystack[i] === needle)
                return true;
        }
        return false;
    }

    /**
     * Translate Action to Rating
     * 
     * @param {String} action
     * @returns {Number}
     */
    function translateActionToRating(action) {
        var rating = 0;
        switch (action.toLowerCase()) {
            case 'view':
                rating = 1;
                break;
            case 'rate':
                rating = 2;
                break;
            case 'add_to_cart':
                rating = 3;
                break;
            case 'buy':
                rating = 4;
                break;
            default:
                rating = 1;
                break;
        }
        return rating;
    }

    /**
     * Create Session ID
     * 
     * @returns {returnValue|String}
     */
    function createSessionID() {
        var name = "predictry_session";
        var value = generateSessionID(15);
        String((new Date()).getTime()).replace(/\D/gi, '');
        document.cookie = name + "=" + value + "; path=/";

        createPredictrySession();
        return value;
    }

    /**
     * Generate Session ID
     * 
     * @param {type} length
     * @returns {returnValue|String}
     */
    function generateSessionID(length) {
        chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        returnValue = "";

        for (x = 0; x < length; x++) {
            i = Math.floor(Math.random() * 62);
            returnValue += chars.charAt(i);
        }

        return returnValue;
    }

    /**
     * Get Session ID
     * 
     * @returns {returnValue|String}
     */
    function getSessionID() {
        var nameEQ = "predictry_session=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ')
                c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0)
                return c.substring(nameEQ.length, c.length);
        }

        return createSessionID();
    }

    /**
     * Delete Cookie
     * 
     * @param {string} name
     * @returns {undefined}
     */
    function deleteSession(name) {
        document.cookie = [name, '=; expires=Thu, 01-Jan-1970 00:00:01 GMT; path=/; domain=.', window.location.host.toString()].join('');
        return;
    }

    function clone(obj) {
        if (null == obj || "object" != typeof obj)
            return obj;
        var copy = obj.constructor();
        for (var attr in obj) {
            if (obj.hasOwnProperty(attr))
                copy[attr] = obj[attr];
        }
        return copy;
    }

    function getQueryParams(qs) {
        qs = qs.split("+").join(" ");

        var params = {}, tokens,
                re = /[?&]?([^=]+)=([^&]*)/g;

        while (tokens = re.exec(qs)) {
            params[decodeURIComponent(tokens[1])]
                    = decodeURIComponent(tokens[2]);
        }

        return params;
    }


    return {
        init: init,
        getSessionID: getSessionID,
        sendAction: sendAction,
        sendBulkActions: sendBulkActions,
        getRecommendedItems: getRecommendedItems,
        updateItem: setItemMeta,
        getCartID: getCartID,
        getCartSession: getCartSession,
        setCartSession: setCartSession,
        removeItemCartSession: removeItemCartSession,
        setCartLog: setCartLog,
        buildUrl: buildUrl,
        drawGrid: drawListGridRecommendation,
        drawList: drawTextListRecommendation,
        clone: clone,
        widget_instance_id: widget_instance_id,
        cart_id: cart_id
    };

}(Predictry || function() {
    return undefined;
}));

if (typeof Predictry !== undefined && typeof Predictry === 'object')
{
    Predictry.init();
}

/**
 * Author       : Rifki Yandhi
 * Date Created : April 12, 2014 6:07:35 PM
 * File         : predictry-api-2.0.js
 * Function     : Predictry recommendation engine API
 */

//var PREDICTRY_API_URL = "http://localhost/predictry-pongo/public/index.php/api/v1/";
//var PREDICTRY_API_URL = "http://demo.predictry.com/api/v1/";
var PREDICTRY_API_URL = "http://api.predictry.dev/v1/";

var compulsary_params = new Array("user_id", "action", "item_id", "session_id", "description");
var optional_params = new Array("item_properties", "action_properties");
var default_action_types = new Array("view", "rate", "add_to_cart", "buy");
var win = window;

var PE_defaults = {
    action: null,
    user_id: null,
    session_id: getSessionID(),
    item_id: null,
    description: ""
};

var PE_options = {
    placementId: win.PE_placementId,
    platformVer: win.PE_platformVer,
    recoType: win.PE_recoType,
    numberOfResults: win.PE_numberOfResults
};

var response = {
    "status": 'success',
    "message": ''
};

var data = {};
var reco_data = {};

/**
 * Set Action Data
 * 
 * @param {type} data
 * @returns {Boolean}
 */
function setActionData(data) {
    data = eExtend(data, PE_defaults);

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
        ready_data = setActionData(data);
    } else {
        response.message = "action data undefined";
        response.status = "failed";
    }

    if (ready_data !== false) {
        callPredictry(ready_data);
    } else {
        console.log(JSON.stringify(response));
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
 * @returns {void}
 */
function callPredictry(action_data) {
    var params = '';
    if (action_data === undefined) {
        params = buildUrl(data);
    }
    else
        params = buildUrl(action_data);

    if (params !== '')
//        makeJqueryAjaxCall("POST", PREDICTRY_API_URL + "predictry?", params);
        makeACall("POST", PREDICTRY_API_URL + "predictry?" + params); //call predictry
    else
        return;
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
 * @param {type} method
 * @param {type} url
 * @param {type} params
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
 * 
 * @param {type} data
 * @returns {unresolved}
 */
function ajaxResultCallBack(data)
{
    return data;
}

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
        }
    }

    bulk_action_data.session_id = getSessionID();
    bulk_action_data.action_type = 'bulk';
    return bulk_action_data;
}

/**
 * Send Bulk Actions
 * 
 * @param {String} action
 * @param {Integer} user_id
 * @param {Array} data
 * @returns {void}
 */
function sendBulkAction(data) {

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
        makeJqueryAjaxCall("POST", PREDICTRY_API_URL + "predictry?", ready_data);
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
function getRecommendation(data)
{
    reco_data = eExtend(data, PE_defaults);

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

//    var response = makeACall("GET", reco_url, drawingCallback);
    var response = makeJqueryAjaxCall("GET", reco_url, reco_data);
    return response;
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
/************This should be from predictry-api.js*******************/

var predictry_tenant_id = "JOHN_SNOW";
var predictry_api_key = "9c768029791f5a0d92744dfe33c76b3e";

/******************************************************************/

var recommendationUrl = "http://localhost/predictry-pongo/public/api/v1/recommendation";
var win = window;
var defaults = {
    userId: null,
    itemId: "-1",
    sessionId: "123asvasdf",
    itemUrl: "",
    itemDescription: "",
    itemImageUrl: "",
    numberOfResults: 10,
    tenantId: win.PE_tenantId,
    apiKey: win.PE_apiKey,
    placementId: win.PE_placementId,
    platformVer: win.PE_platformVer,
    recoType: win.PE_recoType
};


/**
 * Translate Action to Rating
 * 
 * @param {Array} parameters
 * @returns {buildUrl.qs|String}
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
 * Make A Call
 * 
 * @param {String} method
 * @param {String} url
 * @returns {object}
 */
function makeACall(method, url, callback) {
    if (url === undefined)
        return;
    var http = new XMLHttpRequest();
    http.overrideMimeType("application/json");
    http.open(method, url, true);
    http.setRequestHeader("X-Predictry-Server-Api-Key", predictry_api_key);
    http.setRequestHeader("X-Predictry-Server-Tenant-ID", predictry_tenant_id);
    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState === 4 && http.status === 200) {
            jsonResponse = JSON.parse(http.responseText);
            if (callback !== null)
            {
                callback(jsonResponse, "PREDICTRY");
            }
        }
    };
    http.send(null);
}


function getRecommendation(reco_data, options)
{
    var drawingCallback;
    var params = buildUrl(reco_data);
    reco_url = recommendationUrl + "?" + params;

    if (typeof (noDrawingNeeded) === 'undefined')
    {
        if (options.recoType === "pe_grid")
        {
            drawingCallback = drawListGridRecommendation;
        } else {
            drawingCallback = drawTextListRecommendation;
        }
    } else {
        drawingCallback = null;
    }

    var response = makeACall("GET", reco_url, drawingCallback);
    return response;
}

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
                        "<li><a href=\"" + items[x].recommendation + "\">"
                        + items[x].recommendation +
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

var elem_predictries = document.getElementsByClassName("PREDICTRY");
var elem_predictry = null;

for (var i = 0; i < elem_predictries.length; ++i) {
    var elem_predictry = elem_predictries[i];
    break;
}

if (elem_predictry !== null)
{
    var item_id = elem_predictry.getAttribute("data-item-id");
    var user_id = (elem_predictry.getAttribute("data-user-id") !== null) ? elem_predictry.getAttribute("data-user-id") : 0;
    var session_id = "123asd"; //getSessionID();
    var numberOfResults = (elem_predictry.getAttribute("data-number-of-results") !== null) ? elem_predictry.getAttribute("data-number-of-results") : defaults.numberOfResults;

    var reco_data = {
        item_id: item_id,
        user_id: user_id,
        session_id: session_id,
        number_of_results: numberOfResults
    };


    var json_result = getRecommendation(reco_data, defaults);

    if (defaults.recoType === "pe_text")
    {
        drawTextListRecommendation(json_result, "PREDICTRY");
    }
    else if (defaults.recoType === "pe_grid")
    {
        drawListGridRecommendation(json_result);
    }
    else {
        drawTextListRecommendation(json_result);
    }
}


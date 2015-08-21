if (typeof _predictry !== 'object') {
    _predictry = [];
}

if (typeof Predictry !== 'object') {
    Predictry = (function () {
        'use strict';

        /************************************************************
         * Private data
         ************************************************************/
        var
                /* alias frequently used globals for added minification */
                window_alias = window,
                document_alias = document,
                navigator_alias = navigator, // what is navigator ?
                /* encode */
                encode_wrapper = window_alias.encodeURIComponent, // encode the url
                /* decode */
                decode_wrapper = window_alias.decodeURIComponent, // decode url
                /* urldecode */
                url_decode = unescape,
                /* asynchronous executor */
                async_executor,
                /* iterator */
                iterator,
                /* local Predictry */
                Predictry;

        /************************************************************
         * Private methods
         ************************************************************/

        /**
         * Is property[ele] check undefined ?
         * @param {type} property
         * @returns {Boolean}
         */
        function isDefined(property) {
            var property_type = typeof property;
            return property_type !== 'undefined';
        }

        /**
         * Is property function ?
         * @param {type} property
         * @returns {Boolean}
         */
        function isFunction(property) {
            var property_type = typeof property;
            return property_type === 'function';
        }

        /**
         * Is property object ?
         * 
         * @param {type} property
         * @returns {Boolean}
         */
        function isObject(property) {
            var property_type = typeof property;
            return property_type === 'object';
        }

        /**
         * Is property string ?
         * 
         * @param {type} property
         * @returns {Boolean}
         */
        function isString(property) {
            return typeof property === 'string' || property instanceof String;
        }

        /**
         * 
         * @param {string} url,
         * @param {callback} callback
         * @returns {Boolean}
         */
        function isUrlExists(url, callback) {
            var http = new XMLHttpRequest();
            http.open('HEAD', url);
            http.onreadystatechange = function () {
                var exists = false;
                if (http.readyState === 4) {
                    if (http.status != 404 && http.status != 403) {
                        exists = true;
                    }

                    if (isDefined(callback)) {
                        if (isFunction(callback))
                            callback(exists);
                        else if (isString(callback)) {
                            var func = eval(callback);
                            if (isFunction(func)) {
                                func(exists);
                            }
                        }
                    } else
                        return exists;
                }
            };

            http.send(null);
        }

        /**
         * apply()
         * @description everytime _predictry.push[*] send
         *                 out the actions, apply() catch and
         *                 send them to async_executor
         *
         * @param array parameter_array An array comprising either:
         *      [ 'methodName', optional_parameters ]
         * or:
         *      [ functionObject, optional_parameters ]
         *
         *
         */
        function apply() {
            var i, f, parameter_array;
            for (i = 0; i < arguments.length; i += 1) {
                parameter_array = arguments[i];
                f = parameter_array.shift();
                if (isString(f)) {
                    try {
                        async_executor[f].apply(async_executor, parameter_array);
                    } catch (e) {
                        console.log(e);
                    }
                } else {
                    f.apply(async_executor, parameter_array);
                }
            }

        }

        /*
         * Load JavaScript file (asynchronously) not used
         */
        function loadScript(src, onLoad) {
            var script = document_alias.createElement('script');

            script.type = 'text/javascript';
            script.src = src;

            if (script.readyState) {
                script.onreadystatechange = function () {
                    var state = this.readyState;

                    if (state === 'loaded' || state === 'complete') {
                        script.onreadystatechange = null;
                        onLoad();
                    }
                };
            } else {
                script.onload = onLoad;
            }

            document_alias.getElementsByTagName('head')[0].appendChild(script);
        }

        /*
         * Get page referrer / not used
         */
        function getReferrer() {
            var referrer = '';

            try {
                referrer = window_alias.top.document.referrer;
            } catch (e) {
                if (window_alias.parent) {
                    try {
                        referrer = window_alias.parent.document.referrer;
                    } catch (e2) {
                        referrer = '';
                    }
                }
            }

            if (referrer === '') {
                referrer = document_alias.referrer;
            }

            return referrer;
        }

        /*
         * Extract scheme/protocol from URL / not used
         */
        function getProtocolScheme(url) {
            var e = new RegExp('^([a-z]+):'),
                    matches = e.exec(url);

            return matches ? matches[1] : null;
        }

        /*
         * Extract hostname from URL / not used
         */
        function getHostName(url) {
            // scheme : // [username [: password] @] hostame [: port] [/ [path] [? query] [# fragment]]
            var e = new RegExp('^(?:(?:https?|ftp):)/*(?:[^@]+@)?([^:/#]+)'),
                    matches = e.exec(url);

            return matches ? matches[1] : url;
        }

        /*
         * Extract parameter from URL
         */
        function getParameter(url, name) {
            var regex_search = "[\\?&#]" + name + "=([^&#]*)";
            var regex = new RegExp(regex_search);
            var results = regex.exec(url);
            return results ? decode_wrapper(results[1]) : '';
        }

        /*
         * UTF-8 encoding
         */
        function utf8_encode(argString) {
            return url_decode(encode_wrapper(argString));
        }

        /**
         *
         * @param arr
         * @param item
         */
        function removeEle(arr, item) {
            for(var i = arr.length; i--;) {
                if(arr[i] === item) {
                    arr.splice(i, 1);
                }
            }
            return arr;
        }

        /**
         * Extend object
         * 
         * @param {type} options
         * @param {type} defaults
         * @returns {type} target
         */
        function eExtend(options, defaults) {
            var target = options;

            for (var propertyName in defaults) {
                var src = target[ propertyName ];
                var copy = defaults[ propertyName ];

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
         * @param {object} obj
         * @param {string} prefix
         * @returns {String}
         */
        function buildUrl(obj, prefix) {
            var str = [];
            for (var p in obj) {
                var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
                str.push(typeof v == "object" ?
                        buildUrl(v, k) :
                        encodeURIComponent(k) + "=" + encodeURIComponent(v));
            }
            return str.join("&");
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
         * Clone object
         * 
         * @param {object} obj
         * @returns {object}
         */
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

        /**
         * Add parameter to a url
         * @param {string} url
         * @param {string} parameterName
         * @param {string} parameterValue
         * @param {boolean} atStart
         * @returns {string}
         */
        function addParameter(url, parameterName, parameterValue, atStart) {
            var replaceDuplicates = true;
            var urlhash = '';
            var sourceUrl = '';

            if (url.indexOf('#') > 0) {
                var cl = url.indexOf('#');
                urlhash = url.substring(url.indexOf('#'), url.length);
            } else {
                urlhash = '';
                cl = url.length;
            }
            sourceUrl = url.substring(0, cl);

            var urlParts = sourceUrl.split("?");
            var newQueryString = "";

            if (urlParts.length > 1)
            {
                var parameters = urlParts[1].split("&");
                for (var i = 0; (i < parameters.length); i++)
                {
                    var parameterParts = parameters[i].split("=");
                    if (!(replaceDuplicates && parameterParts[0] == parameterName))
                    {
                        if (newQueryString == "")
                            newQueryString = "?";
                        else
                            newQueryString += "&";
                        newQueryString += parameterParts[0] + "=" + (parameterParts[1] ? parameterParts[1] : '');
                    }
                }
            }
            if (newQueryString == "")
                newQueryString = "?";

            if (atStart) {
                newQueryString = '?' + parameterName + "=" + parameterValue + (newQueryString.length > 1 ? '&' + newQueryString.substring(1) : '');
            } else {
                if (newQueryString !== "" && newQueryString != '?')
                    newQueryString += "&";
                newQueryString += parameterName + "=" + (parameterValue ? parameterValue : '');
            }
            return urlParts[0] + newQueryString + urlhash;
        }

        /**
         * decodeUriParam()
         * @param value
         * @returns {{key: string, value: string}}
         */
        function decodeUriParam(value) {
            var values = value.split("=");

            return {"key": decodeURIComponent(values[0]), "value": decodeURIComponent(values[1])};
        }

        /**
         * mapJSONToUriParams
         * @param data
         * @param encode
         * @param prefix
         * @param call
         * @returns {string}
         */
        function mapJSONToUriParams(data, encode, prefix, call) {

            prefix = typeof prefix !== 'undefined' ? prefix : "";
            call = typeof call !== 'undefined' ? call : 0;
            encode = typeof encode !== 'undefined' ? encode : true;

            var map = [];

            if (Object.prototype.toString.call(data) === '[object Array]') {

                for (var ik = 0; ik < data.length; ik++) {
                    map.push(mapJSONToUriParams(data[ik], encode, prefix + "[" + ik + "]", call + 1));
                }

            } else if (Object.prototype.toString.call(data) === '[object Object]') {
                Object.keys(data).map(function (k) {
                    var sep = "";

                    //not empty
                    if (prefix !== "") {

                        if (prefix.slice(-1) !== "]") {
                            sep = ":";
                        }
                    }

                    map.push(mapJSONToUriParams(data[k], encode, prefix + sep + k, call + 1));
                });

            } else {
                map.push(prefix + "=" + encodeURIComponent(data));
            }

            if (call == 0 && encode == true) {

                for (var i = 0; i < map.length; i++) {
                    map[i] = encodeURIComponent(map[i]);
                }
            }

            return map.join("&");
        }

        /**
         * mapObjectKey
         * @param key
         * @param value
         * @param object
         * @returns {*}
         */
        function mapObjectKey(key, value, object) {

            var indexOfObjectSep = key.indexOf(":");
            var indexOfArray = key.indexOf("[");

            if ((indexOfObjectSep > -1 && indexOfObjectSep < indexOfArray) || (indexOfObjectSep > -1 && indexOfArray === -1)) {

                var extractedKey = key.substr(0, indexOfObjectSep);
                var remainingKey = key.substr(indexOfObjectSep + 1);

                if (!(extractedKey in object)) {
                    object[extractedKey] = {};
                }

                if (remainingKey === "") {
                    object[extractedKey] = value;
                } else {
                    return mapObjectKey(remainingKey, value, object[extractedKey]);
                }

            } else if ((indexOfArray > -1 && indexOfArray < indexOfObjectSep) || (indexOfArray > -1 && indexOfObjectSep === -1)) {

                var extractedKey = key.substr(0, indexOfArray);
                var remainingKey = key.substr(key.indexOf("]") + 1);

                if (!(extractedKey in object)) {
                    object[extractedKey] = [];
                }

                var index = parseInt(key.substr(indexOfArray + 1, key.indexOf("]") - 1));

                if (!(index in object[extractedKey])) {
                    object[extractedKey][index] = {};
                }

                if (remainingKey === "") {
                    object[extractedKey][index] = value;
                } else {
                    return mapObjectKey(remainingKey, value, object[extractedKey][index]);
                }

            } else {
                object[key] = value;
            }
        }

        /**
         *
         * @param data
         * @param object
         * @param call
         * @returns {*}
         */
        function mapUriParamsToJSON(data, object, call) {

            call = typeof call !== 'undefined' ? call : 0;
            object = typeof object !== 'undefined' ? object : {};

            if (call === 0) {
                data = decodeURIComponent(data).split("&");

                for (var key in data) {
                    data[key] = decodeURI(data[key]);
                }

                for (var i = 0; i < data.length; i++) {
                    mapUriParamsToJSON(data[i], object, call + 1);
                }
            } else {
                //decode data
                var pair = decodeUriParam(data);
                //build object recursively
                mapObjectKey(pair["key"], pair["value"], object);
            }

            return object;
        }
        /* end of Gui */

        /**
         * Predictry Executor Class
         * 
         * @param {string} tenant_id
         * @param {string} api_key
         * @returns {Predictry.Executor.Anonym$0}
         */
        function Executor(tenant_id, api_key) {
            var
                    tenant_id = tenant_id,
                    api_key = api_key,
                    baseURL;

            var
                    response = {},
                    data = {};

            var
                    widget_id = 0,
                    widget_instance_id = 0,
                    temp_cart_id = -1,
                    tracking_params = null;

            var
                    predictry_nodes = {},
                    widgets = [],
                    is_lookup_widget = false;


            /**
             * Config values
             */
            var
                    config_cookie_name_prefix = "_predictry_",
                    config_cookie_disabled = false,
                    config_cookie_path = "/",
                    config_default_request_method = "POST",
                    config_request_method = config_default_request_method,
                    config_default_request_content_type = "application/x-www-form-urlencoded; charset=UTF-8",
                    config_request_content_type = config_default_request_content_type,
                    config_api_url = "https://api.predictry.com/",
                    config_cf_trackings_url = "https://d1j642hg7oh3vx.cloudfront.net/",
                    config_s3_resource_url = "https://s3-ap-southeast-1.amazonaws.com/predictry/",
                    config_api_resources = ["actions", "users", "items", "carts", "cartlogs", "recommendation"],
                    config_default_actions = ["view", "add_to_cart", "buy", "started_checkout", "started_payment", "check_delete_item", "delete_item", "custom"],
                    config_session_cookie_timeout = 63072000000, // 2 years
                    config_tracking_session_cookie_timeout = 1200000, //20 minutes
                    config_recently_session_cookie_timeout = 2592000000, // 30 days
                    config_do_not_track = false,
                    config_s3_data_recommendation_path = "data/tenants/{tenant}/recommendations/",
                    config_s3_data_items_path = "data/tenants/{tenant}/items/",
                    config_s3_data_category_items_path = "data/tenants/{tenant}/categories/",
                    config_default_s3_resource_ext = ".json",
                    config_cls_prefix = "pry-",
                    config_prefix_param = "p_",
                    config_fisher_endpoint = "http://119.81.208.244:8090/fisher/items",
                    config_widget_type = [ "similar", "oivt", "oipt", "duo"],
                    config_recent_limit = 20,
                    config_typed_titles = { similar: "Similar Items", oivt: "Viewed also viewed", oipt: "Bought also bought", duo: "Both" }
                ;

            var recent_response;
            var recent_xhr = null;

            var draw_reco_seq = 1;

            /**
             * Append Style
             * @returns {void}
             */
            function appendStyle() {
                var css = "ins.predictry{text-decoration:none}.predictry{width:auto;overflow:hidden;display:block}.predictry .pry-header{border-bottom:1px solid #CCC;margin-bottom:10px}.predictry .pry-content{margin-top:10px}.predictry ul{list-style:none;margin:0;padding:0;font-family:inherit}.predictry li.pry-column{display:inline-block;vertical-align:top;min-width:50px;max-width:180px;margin-right:10px;margin-bottom:20px}.predictry li.pry-column:last-child{margin-left:0}.predictry li.pry-column div.pry-thumb>a>img{max-width:100%;height:auto;display:block}.predictry li.pry-column div.pry-item-wrapper>div.pry-desc,.predictry li.pry-column div.pry-item-wrapper>div.pry-price{margin-top:10px}.predictry li.pry-column div.pry-item-wrapper>div.pry-desc>a{display:inline-block;color:#000;text-decoration:none}.predictry div.pry-footer{text-align:right;border-bottom:1px solid #CCC}.predictry div.pry-footer a{color:#000;text-decoration:none}.predictry div.pry-footer a>span.powered{font-size:10px;color:#000;font-weight:normal;display:inline}.predictry span.powered{text-decoration:none}";
                var style = document, s = style.createElement('style');
                var head = document.head || document.getElementsByTagName('link')[0];
                s.type = 'text/css';

                if (s.styleSheet) {
                    s.styleSheet.csstext = css;
                }
                else
                    s.appendChild(document.createTextNode(css));

                head.appendChild(s);
            }

            /**
             *
             * @returns {*}
             */
            function hasCookies() {
                if (config_cookie_disabled) {
                    return '0';
                }

                if (!isDefined(navigator_alias.cookieEnabled)) {
                    var testCookieName = getCookieName('testcookie');
                    setCookie(testCookieName, '1');

                    return getCookie(testCookieName) === '1' ? '1' : '0';
                }

                return navigator_alias.cookieEnabled ? '1' : '0';
            }

            /**
             * Generate Session ID
             * 
             * @param {type} length
             * @returns {String} returnValue
             */
            function generateSessionID(length) {
                var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
                var returnValue = "";
                var i = 0;

                for (var x = 0; x < length; x++) {
                    i = Math.floor(Math.random() * 62);
                    returnValue += chars.charAt(i);
                }

                return returnValue;
            }

            /**
             * Drawing recommendation as LIST
             * 
             * @param {DOM} elem
             * @param {object} obj
             * @returns {void}
             */
            function drawTextListRecommendation(elem, obj) {
                if (obj !== null) { // if no error show recommendations
                    try {
                        var items = obj.data.items;
                        widget_instance_id = obj.data.widget_instance_id;
                    } catch (e) {
                        return;
                    }

                    /* when the object is already in array format, this block will not execute */
                    if ("undefined" == typeof (items.length)) {
                        items = new Array(items);
                    }

                    var listString = "";
                    // display recommendations in the DIV layer 'recommendation'
                    if (items.length > 0) {
                        listString = "<ul style='width: 100%;'>";

                        for (var x = 0; x < items.length; x++) {
                            listString +=
                                    "<li style='list-style: none; float: left; padding-left: 10px; padding-right: 10px; width: 160px; '>"
                                    + "<img src=\"" + items[x].img_url + "\" style='clear: both; display: block; margin-right: auto; margin-left: auto; width: 150px;'>"
                                    + "<a href=\"" + items[x].item_url + "\">" + items[x].name + "</a>"
                                    + "</li>";
                        }
                        elem.innerHTML += listString + "</ul>";
                    }
                }
            }

            /**
             * draw text list in async
             * @param {object} elem
             * @param {object} item_ids
             * @param {object} params
             * @returns {void}
             */
            function drawAsyncTextListRecommendation(elem, item_ids, params) {
                if (isDefined(item_ids)) {
                    if ("undefined" == typeof (item_ids.length)) {
                        item_ids = new Array(item_ids);
                    }

                    if (item_ids.length > 0) {
                        var predictryList = document.createElement("ul");
                        elem.appendChild(predictryList);
                        for (var i = 0; i < item_ids.length; i++) {
                            var s3_data_tenant_item_path = config_s3_data_items_path.replace("{tenant}", tenant_id);
                            var url = config_s3_resource_url + s3_data_tenant_item_path + item_ids[i] + config_default_s3_resource_ext;
                            getJSON(url, function (response) {
                                if (isDefined(response)) {
                                    var obj = JSON.parse(response);
                                    var node = document.createElement("li");
                                    node.innerHTML = "<a href=\"product.php?" + obj.id + "\">" + obj.name + "</a>";
                                    predictryList.appendChild(node);
                                }
                            });
                        }
                    }
                }

            }

            /**
             * drawAsyncThumbListRecommendation
             * @param {element} elem
             * @param {array} item_ids
             * @param {string} params
             * @returns {void}
             */
            function drawAsyncThumbListRecommendation(elem, item_ids, params, algo) {
                if (isDefined(item_ids)) {

                    if (!isDefined(item_ids.length)) {
                        item_ids = new Array(item_ids);
                    }

                    if (isDefined(item_ids.length) && item_ids.length > 0) {
                        var params_tmp = params;
                        var len = (isDefined(params_tmp.limit) && (params_tmp.limit !== "") && (item_ids.length > params_tmp.limit)) ? params_tmp.limit : item_ids.length;
                        var random_ids = [];
                        var params = {
                            id: (isDefined(params_tmp.item_id)) ? params_tmp.item_id : 0,
                            len: len,
                            algo: (isDefined(algo) && algo !== "") ? algo : ''
                        };

                        var s3_data_tenant_item_path = config_s3_data_items_path.replace("{tenant}", tenant_id);
                        var predictryList = document.createElement("ul");
                        predictryList.className = config_cls_prefix + "content";

                        for (var i = 0; i < len; i++) {
                            var item_id = 0;

                            if (isDefined(params_tmp.limit) && (item_ids.length > params_tmp.limit)) {
                                item_id = item_ids[Math.floor(Math.random() * item_ids.length)]; //random id
                            } else
                                item_id = item_ids[i];

                            if (item_id && !inArray(item_id, random_ids)) {
                                var url = config_s3_resource_url + s3_data_tenant_item_path + item_id + config_default_s3_resource_ext;
                                getJSON(url, function (responseText) {
                                    if (isDefined(responseText)) {
                                        var response = JSON.parse(responseText);
                                        if (baseURL) {
                                            if (response.img_url.slice(0,4) !== "http") {
                                               response.img_url = baseURL+ response.img_url;
                                            }
                                            if (response.item_url.slice(0,4) !== "http") {
                                               response.item_url= baseURL+ response.item_url;
                                            }
                                        }

                                        if (isObject(response)) {
                                            var node = createThumbRecoNode(response, draw_reco_seq, params, params_tmp.currency);
                                            predictryList.appendChild(node);
                                        }
                                    }

                                });

                                if (isDefined(params_tmp.limit))
                                    random_ids.push(item_id);
                            } else
                                i -= 1;
                        }

                        if (item_ids.length > 0) {
                            if (isDefined(params_tmp.hide_title)) {
                                if (!params_tmp.hide_title)
                                    elem.appendChild(createHeaderDiv((isDefined(params_tmp.title)) ? params_tmp.title : "Recommended for you"));
                            } else {
                                elem.appendChild(createHeaderDiv((isDefined(params_tmp.title)) ? params_tmp.title : "Recommended for you"));
                            }

                            elem.appendChild(predictryList);
                            if (!params_tmp.typed) {
                                elem.appendChild(createFooterDiv("by Predictry"));
                            }
                        }
                    }

                }
            }

            /**
             * 
             * @param {object} response
             * @param {integer} seq
             * @param {object} params
             * @param {string} price_currency
             * @returns {elem}
             */
            function createThumbRecoNode(response, seq, params, price_currency) {
                if (isDefined(response) && isObject(response)) {
                    var cls_li_prefix_id = config_cls_prefix + "recIdx";
                    var cls_odd = (seq % 2 === 1) ? config_cls_prefix + "odd" : config_cls_prefix + "even";

                    /* Create Element */
                    var node = document.createElement("li");
                    var div_item_wrapper = document.createElement("div");
                    var div_thumb = document.createElement("div");
                    var div_description = document.createElement("div");
                    var div_price = document.createElement("div");

                    /* Add class name */
                    node.className = config_cls_prefix + "column " + cls_li_prefix_id + "-" + seq + " " + cls_odd;
                    div_item_wrapper.className = config_cls_prefix + "item-wrapper";
                    div_thumb.className = config_cls_prefix + "thumb";
                    div_description.className = config_cls_prefix + "desc";
                    div_price.className = config_cls_prefix + "price";

                    price_currency = isDefined(price_currency) ? price_currency.trim() + " " : '';
                    var adj_item_url = response.item_url;

                    for (var prop in params) {
                        if (params.hasOwnProperty(prop) && (params[prop] !== 0) && (params[prop] !== '')) {
                            adj_item_url = addParameter(adj_item_url, config_prefix_param + prop, params[prop]);
                        }
                    }
                    adj_item_url = addParameter(adj_item_url, config_prefix_param + 'seq', seq);
                    if (isDefined(response.id) && (response.id !== ""))
                        adj_item_url = addParameter(adj_item_url, config_prefix_param + 'tid', response.id); //add target id

                    var str_thumbs = "";
                    str_thumbs += "<a href='" + adj_item_url + "' target='_self' title='" + response.name + "'" + ">";
                    str_thumbs += "<img src='" + response.img_url + "'/>";
                    str_thumbs += "</a>";
                    div_thumb.innerHTML = str_thumbs;

                    var str_desc = "";
                    str_desc += "<a href='" + adj_item_url + "' target='_self' title='" + response.name + "'" + ">";
                    str_desc += "<span class='name'>" + response.name + "</span>";
                    str_desc += "</a>";
                    div_description.innerHTML = str_desc;

                    var str_price = "";
                    str_price += "<span class='priceCurrency'>" + price_currency + "</span>";
                    str_price += "<span class='price'>" + response.price + "</span>";
                    div_price.innerHTML = str_price;

                    div_item_wrapper.appendChild(div_thumb);
                    div_item_wrapper.appendChild(div_description);
                    div_item_wrapper.appendChild(div_price);

                    node.appendChild(div_item_wrapper);
                    draw_reco_seq += 1;
                    return node;
                }

                return false;
            }

            /**
             * Create Header
             * 
             * @param {string} text
             * @returns {Element}
             */
            function createHeaderDiv(text) {
                var div = document.createElement("div");
                div.className = config_cls_prefix + "header";

                var str = "<h3>" + text + "</h3>";
                div.innerHTML = str;

                return div;

            }

            /*
             * Create div footer
             * @param {string} text
             * @returns {Element}
             */
            function createFooterDiv(text) {
                var div = document.createElement("div");
                div.className = config_cls_prefix + "footer";
                var str = "<a href='http://www.predictry.com' target='_blank'><span class='powered'>" + text + "</span></a>";
                div.innerHTML = str;

                return div;
            }

            /**
             * Create an object from a string
             * @description unused function :3
             * @param {string} response
             * @returns {object}
             */
            function toObject(response) {
                var obj_response = eval("(" + response + ")");
                return obj_response;
            }

            /**
             * Delete cookies setting all the cookies
             * value to '' ( which is none )
             * @returns {void}
             */
            function deleteCookies() {
                setCookie(getCookieName('session'), '', -86400, config_cookie_path);
                setCookie(getCookieName('cart'), '', -86400, config_cookie_path);
                setCookie(getCookieName('view'), '', -86400, config_cookie_path);
            }

            /**
             * Set cookie value
             * @params {string} cookie name
             * @params {string} value
             * @params {string} ms_to_expire
             * @params {string} path
             * @params {string} domain
             * @params {boolean} secure
             * @returns {void}
             */
            function setCookie(cookie_name, value, ms_to_expire, path, domain, secure) {
                if (config_cookie_disabled) {
                    return;
                }

                var expiry_date;

                // relative time to expire in milliseconds
                if (ms_to_expire) {
                    expiry_date = new Date();
                    expiry_date.setTime(expiry_date.getTime() + ms_to_expire);
                }

                document_alias.cookie = cookie_name + '=' + encode_wrapper(value) +
                        (ms_to_expire ? ';expires=' + expiry_date.toGMTString() : '') +
                        ';path=' + (path || '/') +
                        (domain ? ';domain=' + domain : '') +
                        (secure ? ';secure' : '');
            }

            /**
             * Set Session Cookie ID
             * 
             * @param {string} unique_id
             * @returns {string}
             */
            function setSessionCookieId(unique_id) {
                setCookie(getCookieName("session"), unique_id, config_tracking_session_cookie_timeout, config_cookie_path);
                return unique_id;
            }

            /**
             * Set Session User ID
             * 
             * @param {string} unique_id
             * @returns {void}
             */
            function setUserSessionCookieId(unique_id) {
                setCookie(getCookieName("user"), unique_id, config_tracking_session_cookie_timeout, config_cookie_path);
                return unique_id;

            }

            /**
             * Set Browser Session Cookie ID
             * @param {string} unique_id
             * @returns {string}
             */
            function setBrowserSessionCookieId(unique_id) {
                setCookie(config_cookie_name_prefix + "browser", unique_id, config_session_cookie_timeout, config_cookie_path);
                return unique_id;
            }

            /**
             * Set Cart Session
             * @param {object} c_obj
             * @returns {object}
             */
            function setCartSessionCookie(c_obj) {
                var cart_id = getCartID();
                if (!isDefined(c_obj) || !isObject(c_obj))
                    c_obj = {c: [], cart_id: cart_id};
                else
                    c_obj.cart_id = cart_id;

                var value = JSON.stringify(c_obj);
                setCookie(getCookieName("cart"), value, config_tracking_session_cookie_timeout, config_cookie_path);
                return c_obj;
            }

            /**
             * Set view session cookie
             * 
             * @param {object} v_obj
             * @returns {_L15.Executor.setViewSessionCookie.v_obj}
             */
            function setViewSessionCookie(v_obj) {
                if (!isDefined(v_obj) || !isObject(v_obj))
                    var v_obj = {v: []};
                var value = JSON.stringify(v_obj);
                setCookie(getCookieName("view"), value, config_tracking_session_cookie_timeout, config_cookie_path);
                return v_obj;
            }

            /**
             * Set Cart Log
             * 
             * @param {type} item_id
             * @param {type} qty
             * @param {type} event
             *
             */
            function setCartLog(item_id, qty, event) {
                temp_cart_id = getCartID();
                var data = {
                    cart_id: temp_cart_id,
                    item_id: item_id,
                    qty: qty,
                    event: event
                };
                sendRequest(config_api_url + config_api_resources[3], buildUrl(data));
            }

            /**
             * Update Cart Item Session
             * 
             * @param {string} item_id
             * @returns {void}
             */
            function setItemIntoCartSession(item_id) {
                var cartSession = eval("(" + getCookie(getCookieName("cart")) + ")");
                var cartItemIDs = cartSession.c; //array
                if (isDefined(cartItemIDs) && (item_id !== undefined && !inArray(item_id, cartItemIDs))) {
                    cartSession.c.push(item_id);
                }
                var value = JSON.stringify(cartSession);
                setCookie(getCookieName("cart"), value, config_session_cookie_timeout, config_cookie_path);
            }

            /**
             * Get Session ID
             * 
             * @returns {String|Number}
             */
            function getSessionID() {
                var val = getCookie(getCookieName("session"));
                return (val) ? val : setSessionCookieId(getUUID());
            }

            /**
             * Get Session User ID
             * @returns {Number|String}
             */
            function getSessionUserID() {
                var val = getCookie(getCookieName("user"));
                return (val) ? val : setUserSessionCookieId(getUUID());
            }

            /**
             * Set Browser ID
             */
            function getSessionBrowserID() {
                var val = getCookie(config_cookie_name_prefix + "browser");
                return (val) ? val : setBrowserSessionCookieId(getUUID());
            }

            /**
             * getSessionCart 
             * @description get the current cart items
             *              from cookies
             */
            function getSessionCart() {
                var val = getCookie(getCookieName("cart"));
                return (val) ? val : setCartSessionCookie();
            }

            /**
             * Get Cart ID
             */
            function getCartID() {
                var cart_id = -1;
                var session = getCookie(getCookieName("session"));
                var cartSession = eval("(" + getCookie(getCookieName("cart")) + ")");

                if (session === undefined)
                    session = setSessionCookieId();

                if (isDefined(cartSession) && isObject(cartSession) && isDefined(cartSession.cart_id) && cartSession.cart_id !== -1) {
                    return cartSession.cart_id;
                }
                return cart_id;
            }

            /**
             * Get UUID
             * @returns {String}
             */
            function getUUID() {

                return generateSessionID(4) + generateSessionID(4) +
                        '-' +
                        generateSessionID(4) + '-' + generateSessionID(4) + '-' + generateSessionID(4) +
                        '-' +
                        generateSessionID(4) + generateSessionID(4) + generateSessionID(4);
            }

            /**
             * Call the callback
             * 
             */
            function callCallback(callback, p){
                if (isDefined(callback)) {
                    if (isFunction(callback))
                        callback(p);
                    else if (isString(callback)) {
                        var func = eval(callback);
                        if (isFunction(func)) {
                            func(p);
                        }
                    }
                }
            }

            /*
             * 
             * @param {string} url
             * @param {function} callback
             * @returns {void|array}
             */
            function getJSON(url, callback) {
                var http = new XMLHttpRequest();
                http.open('get', url, true);
                http.onreadystatechange = function () {
                    if (http.readyState == 4 && http.status == 200) {
                        if (isDefined(callback)) {
                            if (isFunction(callback))
                                callback(http.responseText);
                            else if (isString(callback)) {
                                var func = eval(callback);
                                if (isFunction(func)) {
                                    func(http.responseText);
                                }
                            }
                        } else {
                            recent_response = JSON.parse(http.responseText);
                            return;
                        }
                        var myArr = JSON.parse(http.responseText);
                        return myArr;
                    }
                    else if (http.readyState == 4 && http.status == 403) {
                        if (isDefined(callback)) {
                            if (isFunction(callback))
                                callback(false);
                            else if (isString(callback)) {
                                var func = eval(callback);
                                if (isFunction(func)) {
                                    func(false);
                                }
                            }
                        }
                    }
                    else {
                        return;
                    }
                }
                http.send();
            }

            /**
             * 
             * @param {object} reco_data
             * @param {function} callback
             * @returns {object}
             */
            function getRecommendedItems(reco_data, callback) {
                var data = prepareRecoRequestData(reco_data);
                config_request_method = "GET";
                var url = config_api_url + config_api_resources[5] + "?" + buildUrl(data);
                sendRequest(url, buildUrl(data), true, callback);
                config_request_method = config_default_request_method;
                return response;
            }

            /**
             * 
             * @param {object} data
             * @param {function} callback
             * @returns {object}
             */
            function getPreComputedRecommendedItems(data, callback) {
                var response = false;
                var s3_data_tenant_recommendation_path = config_s3_data_recommendation_path.replace("{tenant}", tenant_id);
                if (isDefined(data.item_id)) { //make sure the item resource exist on the bucket dir
                    isUrlExists(config_s3_resource_url + s3_data_tenant_recommendation_path + data.item_id + config_default_s3_resource_ext, function (is_exists) {
                        if (is_exists) {
                            var url = config_s3_resource_url + s3_data_tenant_recommendation_path + data.item_id + config_default_s3_resource_ext;
                            if (isDefined(callback)) {

                                //get the response, and apply params
                                response = getJSON(url, function (response) {

                                    if (isDefined(response)) {
                                        var obj = JSON.parse(response);
                                        if (typeof obj === 'object' && isDefined(obj.items)) {
                                            var item_ids = obj.items;
                                            var new_item_ids = [];

                                            if (isDefined(data.limit)) {

                                                //no input source for A/B testing in the moment, so just grab random(limit)
                                                if (item_ids.length > data.limit) {
                                                    for (var i = 0; i < data.limit; i++) {
                                                        var item_id = 0;
                                                        if (item_ids.length > data.limit) {
                                                            item_id = item_ids[Math.floor(Math.random() * item_ids.length)]; //random id
                                                        } else
                                                            item_id = item_ids[i];

                                                        if (inArray(item_id, new_item_ids))
                                                            i -= 1;
                                                        else
                                                            new_item_ids.push(item_id);
                                                    }

                                                    obj.items = new_item_ids;
                                                }
                                            }

                                            if (isFunction(callback))
                                                callback(JSON.stringify(obj));
                                            else if (isString(callback)) {
                                                var func = eval(callback);
                                                if (isFunction(func)) {
                                                    func(JSON.stringify(obj));
                                                }
                                            }
                                        }

                                    }
                                });

                            } else
                                response = getJSON(url, callback);
                        } else if(isDefined(data.category)) { //json doesn't exist, get categories if exists
                            response = getPreComputedCategoryBasedRecommendedItems(data, callback);
                        }else{ //json doesn't exist, just callback with undefined response
                            callCallback(callback);
                        }
                    });
                }
                return response;
            }

            /**
             * 
             * @param {object} data
             * @param {function} callback
             * @returns {undefined}
             */
            function getPreComputedCategoryBasedRecommendedItems(data, callback) {
                var response = false;

                getItemDetail(data.item_id, function (responseText) {
                    var response = JSON.parse(responseText);
                    if (isObject(response) && isDefined(response.category) && (response.category !== "")) {
                        var s3_data_tenant_category_items_path = config_s3_data_category_items_path.replace("{tenant}", tenant_id);
                        isUrlExists(config_s3_resource_url + s3_data_tenant_category_items_path + response.category + config_default_s3_resource_ext, function (is_exists) {
                            if (is_exists) {
                                var url = config_s3_resource_url + s3_data_tenant_category_items_path + response.category + config_default_s3_resource_ext;
                                response = getJSON(url, callback);
                            }
                        });
                    }
                });

                return response;
            }

            /**
             * 
             * @param {string} item_id
             * @param {function} callback
             * @returns {object}
             */
            function getItemDetail(item_id, callback) {
                var data = [];
                var s3_data_tenant_item_path = config_s3_data_items_path.replace("{tenant}", tenant_id);
                isUrlExists(config_s3_resource_url + s3_data_tenant_item_path + item_id + config_default_s3_resource_ext, function (is_exists) {
                    if (is_exists) {
                        var url = config_s3_resource_url + s3_data_tenant_item_path + item_id + config_default_s3_resource_ext;
                        data = getJSON(url, callback);
                    }
                });
                return isFunction(callback) ? recent_response : data;
            }

            /** getCookie
             * Get cookie value
             */
            function getCookie(cookie_name) {
                if (config_cookie_disabled) {
                    return 0;
                }

                var cookie_pattern = new RegExp('(^|;)[ ]*' + cookie_name + '=([^;]*)'),
                        cookie_match = cookie_pattern.exec(document_alias.cookie);

                return cookie_match ? decode_wrapper(cookie_match[2]) : 0;
            }

            /** getCookieName
             *
             * @description Get cookie name with prefix and domain hash
             */
            function getCookieName(base_name) {
                return config_cookie_name_prefix + base_name + '.' + tenant_id;
            }

            /** getImage
             * Send image request to Predictry server using GET.
             * The infamous web bug (or beacon) is a transparent, single pixel (1x1) image
             */
            function getImage(resource, queries, callback) {
                var image = new Image(1, 1);
                image.onload = function () {
                    iterator = 0; // To avoid JSLint warning of empty block 
                    if (typeof callback === 'function') {
                        callback();
                    }
                };

                resource = !inArray(resource, config_default_actions) ? "custom" : resource;
                image.src = config_cf_trackings_url + resource + ".gif?" + queries;
            }

            /** getPredictryParmas(uri)
             * 
             * @param {string} uri
             * @returns {void}
             */
            function getPredictryParams(uri) {

                if (isDefined(uri)) {
                    tracking_params = {
                        id: (getParameter(uri, config_prefix_param + "id") !== "") ? (getParameter(uri, config_prefix_param + "id")) : -1, //source item_id
                        tid: (getParameter(uri, config_prefix_param + "tid") !== "") ? (getParameter(uri, config_prefix_param + "tid")) : -1, //target item_id
                        len: (getParameter(uri, config_prefix_param + "len") !== "") ? (getParameter(uri, config_prefix_param + "len")) : -1, //Recommendation result length
                        seq: (getParameter(uri, config_prefix_param + "seq") !== "") ? (getParameter(uri, config_prefix_param + "seq")) : -1, //Seq of displayed result
                        algo: (getParameter(uri, config_prefix_param + "algo") !== "") ? (getParameter(uri, config_prefix_param + "algo")) : '' //Algo
                    };

                    if ((tracking_params.id !== -1) && (tracking_params.tid !== -1) && (tracking_params.algo !== "")) {
                        trackDeleteItem(tracking_params.tid);
                    }
                }


                return tracking_params;
            }

            /**
             * Make A Call
             * 
             * @param {string} url
             * @param {object} data
             * @param {boolean} async
             * @param {function} callback
             * @returns {void}
             */
            function sendXmlHttpRequest(url, data, async, callback) {
                if (url === undefined)
                    return;
                if (data === undefined)
                    return;
                if (async === undefined)
                    async = true;

                var http = new XMLHttpRequest();
                http.overrideMimeType("application/json");
                http.open(config_request_method, url, async);
                http.setRequestHeader("Content-Type", config_request_content_type);
                http.setRequestHeader("X-Predictry-Server-Tenant-ID", tenant_id);
                http.setRequestHeader("X-Predictry-Server-Api-Key", api_key);

                recent_xhr = http;
                http.onreadystatechange = function () {//Call a function when the state changes.
                    if (http.readyState === 4) {
                        if (isDefined(callback)) {
                            if (isFunction(callback))
                                callback(http.responseText);
                            else if (isString(callback)) {
                                var func = eval(callback);
                                if (isFunction(func)) {
                                    func(http.responseText);
                                }
                            }
                        } else {
                            recent_response = JSON.parse(http.responseText);
                            return;
                        }
                    }
                };

                http.send(data);
            }

            /*
             * Send request
             */
            function sendRequest(url, data, async, callback, isImage) {
                recent_response = null;
                if (!config_do_not_track) {
                    if (!isDefined(isImage)) {
                        sendXmlHttpRequest(url, data, async, callback);
                        return recent_response;
                    } else {
                        var queries = mapJSONToUriParams(data);
                        getImage(data.action.name, queries, callback);
                    }
                }
            }

            /**
             * 
             * @param {object} data
             * @param {function} callback
             * @returns {void}
             */
            function sendImage(data, callback) {
                if (isDefined(data) && isObject(data) && isDefined(data.action) && isDefined(data.action.name)) {
                    var queries = mapJSONToUriParams(data);
                    getImage(data.action.name, queries, callback);
                }
            }

            /**
             * Prepare data
             * @description append default params for
             *                every request
             * @param {object} data
             * @returns {object}
             */
            function appendPredictryData(data) {
                data.session_id = getSessionID();
                data.user_id = getSessionUserID();
                data.browser_id = getSessionBrowserID();
                data.tenant_id = tenant_id;
                data.api_key = api_key;
                return data;
            }

            /**
             * 
             * @param {object} reco_data
             * @returns {object}
             */
            function prepareRecoRequestData(reco_data) {
                var widget_id, item_id, user_id;

                if (isDefined(reco_data) && isDefined(reco_data.widget_id))
                    widget_id = reco_data.widget_id;

                if (isDefined(reco_data) && isDefined(reco_data.item_id) && reco_data.item_id !== "")
                    item_id = reco_data.item_id;
                else
                    item_id = 0;

                if (isDefined(reco_data) && isDefined(reco_data.user_id) && reco_data.user_id !== "")
                    user_id = reco_data.user_id;
                else
                    user_id = 0;

                return {
                    widget_id: widget_id,
                    user_id: user_id,
                    item_id: item_id,
                    session_id: getSessionID()
                };

            }

            /**
             *
             * @param data
             */
            function sendDeleteToFisher(data) {
                var idString ="";
                for ( var i = 0; i < data.items.length; i++) {
                    idString += data.items[i];
                    if (i !== data.items.length - 1) {
                        idString += ",";
                    }
                }

                var payLoad = config_fisher_endpoint + "/" + data.tenant_id + "/" + idString;

                var http = new XMLHttpRequest();
                http.open('DELETE', payLoad, true);
                http.onreadystatechange = function() {
                    if(http.readyState == 4 && http.status == 200) {
                        console.log(http.responseText);
                    }
                };
                http.send();
            }

            /**
             *
             * @param data
             * @param realtime
             */
            function track(data, realtime) {
                if (!isDefined(data) || !isObject(data))
                    return;

                if (!isDefined(data.action))
                    return;

                data = appendPredictryData(data);

                if (isDefined(data.action.name))
                {
                    if (data.action.name === "view" && data.items.length === 1) {
                        //check if the viewed item is from reco or not
                        if (isDefined(tracking_params.algo) && (tracking_params.algo !== "") && isDefined(data.action)) {
                            data.action.rec = true;
                            data.action.ori = tracking_params;
                        }
                        else if (isDefined(tracking_params.algo) && (tracking_params.algo !== "") && !isDefined(data.action))
                            data.action = {rec: true, ori: tracking_params};

                        trackView(data);
                    }
                    else if (data.action.name == "delete_item") {
                        // send to delete_item.gif
                        if (isDefined(realtime)) {
                            sendDeleteToFisher(data);
                        }
                        sendImage(data);
                    }
                    else if (data.action.name === "add_to_cart") {
                        //check if the viewed item is from reco or not
                        if (isDefined(tracking_params.algo) && (tracking_params.algo !== "") && isDefined(data.action)) {
                            data.action.rec = true;
                            data.action.ori = tracking_params;
                        }
                        else if (isDefined(tracking_params.algo) && (tracking_params.algo !== "") && !isDefined(data.action))
                            data.action = {rec: true, ori: tracking_params};

                        trackAddToCart(data);
                    }
                    else if (data.action.name === "started_checkout" || data.action.name === "started_payment") {
                        trackBulk(data);
                    }
                    else if (data.action.name === "buy" && data.items.length >= 1) {
                        trackBuy(data);
                        deleteCookies();
                    }
                    else
                        sendImage(data);

                }
            }

            /**
             * setItemIntoViewSession(item_id)
             * @description add item id into view session
             * @param {string} item_id
             */
            function setItemIntoViewSession(item_id) {
                var viewSession = eval("(" + getCookie(getCookieName("view")) + ")");
                if (isDefined(viewSession) && isObject(viewSession))
                    var viewItemIDs = viewSession.v;
                else {
                    setViewSessionCookie();
                    viewSession = eval("(" + getCookie(getCookieName("view")) + ")");
                }

                if (isDefined(viewItemIDs) && (item_id !== undefined && !inArray(item_id, viewItemIDs))) {
                    viewSession.v.push(item_id);
                }
                var value = JSON.stringify(viewSession);
                setCookie(getCookieName("view"), value, config_tracking_session_cookie_timeout, config_cookie_path);
            }

            /**
             * setRecentlyViewedSessionCookie
             * @description set the recently viewed item cookies
             *               if there was none
             * @param {object} item_object
             */
            function setRecentlyViewedSessionCookie(item_object) {
                if (!isDefined(item_object) || !isObject(item_object))
                    var item_object = {v: []};

                // create a temp array
                // to wrap the incoming
                // item objects
                var new_array = [];
                new_array.push(item_object);

                // stringify and set it inside cookie value
                var value = JSON.stringify(new_array);
                localStorage.setItem("recentlyViewedItems", value);
            }

            /**
             * Push this item id into view session
             * @params (object) data
             */
            function addItemIntoRecentlyViewedSession(data) {
                var getItems = eval(localStorage.getItem("recentlyViewedItems"));
                if (getItems == null)
                {
                    setRecentlyViewedSessionCookie(data);
                }
                else {
                    var items = getItems;
                    items.push(data);
                    var value = JSON.stringify(items);
                    localStorage.setItem("recentlyViewedItems", value);
                }
            }

            /**
             *
             * @param id
             */
            function removeItemDetails(id) {
                var recentlyItems = eval(localStorage.getItem("recentlyViewedItems"));
                var temp_items = [];
                [].forEach.call(recentlyItems, function(item) {
                    if ((item.item_id) !== id) {
                        temp_items.push(item);
                    }
                });
                var value = JSON.stringify(temp_items);
                localStorage.setItem("recentlyViewedItems", value);


            }

            /**
             * setRecentlyViewedItemIdCookie
             * @description set the recently viewed item id cookies
             *               if there was none
             * @param {object} id
             */
            function setRecentlyViewedItemIdCookie(id) {
                if (!isDefined(id) || !isObject(id))
                    var id = {v: []};

                // write something to explain the below
                var value = JSON.stringify(id);
                setCookie(getCookieName("recentlyViewedItemIds"), value, config_recently_session_cookie_timeout, config_cookie_path);
                return id;
            }

            /**
             * addRecentlyViewedItemId
             * @description to track the items id for
             *                  checking the unique id
             * @params (object) id
             */
            function addRecentlyViewedItemId(id) {
                var recentlyIds  = eval("("+ getCookie(getCookieName("recentlyViewedItemIds")) +")");
                if (isDefined(recentlyIds) && isObject(recentlyIds)) {
                    var recentlyViewedIds = recentlyIds.v;
                    if (!inArray(id, recentlyViewedIds))
                    {
                        // ok can't find id
                        // so push it to the array
                        recentlyViewedIds.push(id);

                        // make that array into a string
                        var value = JSON.stringify(recentlyIds);
                        setCookie(getCookieName("recentlyViewedItemIds"), value, config_recently_session_cookie_timeout, config_cookie_path);
                    }
                }
                else
                {
                    // create cookie for recently viewed item ids
                    // and check if the id was already there or not
                    setRecentlyViewedItemIdCookie(id);
                }

            }

            /**
             * checkItemId(id)
             * @description check if the item_id is
             *                 already in item_id_cookie or not
             * @params (string) id
             * @return (boolean)
             */
            function checkItemId(id) {
                var recentlyIds = eval ("("+getCookie(getCookieName("recentlyViewedItemIds")) +")");
                if (isDefined(recentlyIds) && isObject(recentlyIds))
                {
                    var itemIds = recentlyIds.v;

                    if (inArray(id, itemIds)) {
                        return true;
                    } else {
                        return false;
                    }
                }
                else {
                    setRecentlyViewedItemIdCookie(id);
                    return false;
                }
            }

            /**
             *  showRecentlyViewedItems
             *  @description get the list of items
             *                  from stored cookies and show it
             */
            function showRecentlyViewedItems(id) {
                if (eval(localStorage.getItem("recentlyViewedItems"))) {
                    var recentlyViewedItems = eval(localStorage.getItem("recentlyViewedItems"));
                    if (predictryRecent = document.querySelectorAll(".predictry-recent")[0]) {
                        var predictryRecent = document.querySelectorAll(".predictry-recent")[0];
                        var r_head = document.createElement("h2");
                        r_head.className = "title";
                        r_head.innerHTML = "Recently Viewed Items";

                        var ul_obj = document.createElement("ul");
                        ul_obj.className = "pryRecent pry-content";

                        if ( (recentlyViewedItems.length > 0) && (!inArray(id, recentlyViewedItems)) )
                            predictryRecent.appendChild(r_head);

                        predictryRecent.appendChild(ul_obj);
                        var i = 0;
                        [].forEach.call(recentlyViewedItems, function(item) {
                            if (item.item_id != id) {
                                var p = document.createElement("p");
                                var li_obj = document.createElement("li");
                                li_obj.className = "pry-column pry-recIdx-" + (i + 1) + " pry-odd";

                                var div_obj = document.createElement("div");
                                div_obj.className = "pry-item-wrapper";

                                var thumb_obj = document.createElement("div");
                                thumb_obj.className = "pry-thumb";

                                var thumb_a = document.createElement("a");
                                thumb_a.href = item.item_url;

                                var thumb_img = document.createElement("img");
                                thumb_img.src = item.img_url;

                                var desc_obj = document.createElement("div");
                                desc_obj.className = "pry-desc";

                                var desc_a = document.createElement("a");
                                desc_a.href = item.item_url;

                                var desc_head = document.createElement("span");
                                desc_head.className = "name";
                                desc_head.innerHTML= item.name;

                                ul_obj.appendChild(li_obj);

                                li_obj.appendChild(div_obj);

                                div_obj.appendChild(thumb_obj);

                                thumb_obj.appendChild(thumb_a);

                                thumb_a.appendChild(thumb_img);

                                div_obj.appendChild(desc_obj);

                                desc_obj.appendChild(desc_a);

                                desc_a.appendChild(desc_head);

                            }
                            i++;
                        });
                    }
                }
            }

            /**
             * Track (data)
             * @param data
             */
            function trackView(data) {
                //if data.action.rec is true store it into cookie
                if (isDefined(data.action) && isDefined(data.action.rec))
                    setItemIntoViewSession(data.items[0].item_id);

                // display recently viewed items
                // before adding current item into cookie
                showRecentlyViewedItems(data.items[0].item_id);

                // add the whole item object into cookie
                // only if the items is not there inside
                // itemIds array
                if (!checkItemId(data.items[0].item_id)) {
                    var recentlyItems = eval(localStorage.getItem("recentlyViewedItems"));
                    var recentlyIds   = eval ("("+getCookie(getCookieName("recentlyViewedItemIds")) +")");
                    if ( recentlyItems !== null ) {
                        if(recentlyItems.length == config_recent_limit ) {
                            var id = recentlyItems[0].item_id;
                            recentlyItems.shift();
                            localStorage.setItem(recentlyItems);
                            var updatedRecentIds = removeEle(recentlyIds, id);
                            var value = JSON.stringify(updatedRecentIds);
                            setCookie(getCookieName("recentlyViewedItemIds"), value, config_recently_session_cookie_timeout, config_cookie_path);
                        }
                    }
                    addItemIntoRecentlyViewedSession(data.items[0]);

                    // also create another cookie to keep track unique items
                    // by their IDs
                    addRecentlyViewedItemId(data.items[0].item_id);
                } else {
                    // remove the old details
                    removeItemDetails(data.items[0].item_id);

                    // add new details
                    addItemIntoRecentlyViewedSession(data.items[0]);
                }

                // fireup the payload then
                // view.gif?*parmas
                sendImage(data);
            }

            /** recentlyItemsCallback
             * @description give out an arry of
             *                  recently viewed items
             * @return {object} recentlyItemsCallback
             */
            function recentlyItemsCallback(callback, max) {
                var recentlyViewedItems = eval(localStorage.getItem("recentlyViewedItems"));
                if (max) {
                    var limitedItems = [];
                    for (var i = 0; i < max; i++) {
                        if (recentlyViewedItems[i]) {
                            limitedItems.push(recentlyViewedItems[i]);
                        }
                    }
                    callback(limitedItems);
                } else {
                    // return this object
                    callback(recentlyViewedItems);
                }
            }

            /**
             * trackBuy(data)
             * @description track buy action dom
             * @param data
             */
            function trackBuy(data) {
                if (!isDefined(data) || !isObject(data))
                    return;

                data = appendPredictryData(data);

                var cartSession = eval("(" + getCookie(getCookieName("cart")) + ")");
                var cartItemIDs = cartSession.c;

                var viewSession = eval("(" + getCookie(getCookieName("view")) + ")");
                var viewItemIDs = viewSession.v;

                for (var i = 0; i < data.items.length; i++)
                {
                    for (var key in data.items[i])
                    {
                        var item_id = data.items[i][key];
                        if (key === "item_id" && isDefined(cartItemIDs) && (cartItemIDs.length > 0)) {
                            if (inArray(item_id, cartItemIDs))
                                data.items[i].rec = true;
                        }
                        else if (key === "item_id" && isDefined(viewItemIDs) && (viewItemIDs.length > 0)) {
                            if (inArray(item_id, viewItemIDs))
                                data.items[i].rec = true;
                        }
                    }
                }

                sendImage(data);
            }

            /**
             *
             * @param data
             */
            function trackAddToCart(data) {
                if (isDefined(data.action) && isDefined(data.action.rec))
                    setItemIntoCartSession(data.items[0].item_id);

                sendImage(data);
            }

            /**
             *
             * @param data
             */
            function trackBulk(data) {
                if (!isDefined(data) || !isObject(data))
                    return;

                var cartSession = eval("(" + getCookie(getCookieName("cart")) + ")");
                var cartItemIDs = cartSession.c;

                for (var i = 0; i < data.items.length; i++)
                {
                    for (var key in data.items[i])
                    {
                        var item_id = data.items[i][key];
                        if (key === "item_id" && isDefined(cartItemIDs) && (cartItemIDs.length > 0) && inArray(item_id, cartItemIDs))
                            data.items[i].rec = true;
                    }
                }

                sendImage(data);
            }

            /**
             *
             * @param item_id
             */
            function trackDeleteItem(item_id) {

                var data = {
                    "action": {"name": "check_delete_item"},
                    "items": [
                        {id: item_id}
                    ]
                };

                track(data);
            }

            /** getDynamicConfig
             * @description get preloaded config
             */
            function getConfig() {
                if (typeof GlobalPreidctry == 'object') {
                    console.log("Config instance is here");
                } else {
                    console.log(GlobalPredictry);
                }
            }

            /**
             * checkPredictryType(typename)
             * @param typename
             * @returns {boolean}
             */
            function checkPredictryType(typename) {
                var dom = document.querySelectorAll(".predictry-" + typename)[0];
                return dom ? true : false;
            }

            /**
             *
             * @param item_id
             * @param typename
             * @param callback
             * @returns {Array}
             */
            function getItems(item_id, typename, callback) {
                if (typename == 'similar') {

                    var end_point = config_fisher_endpoint + "/" + tenant_id + "/related/" + item_id ;

                    var http = new XMLHttpRequest();
                    var item_ids = [];

                    http.open("GET", end_point, true);
                    http.onreadystatechange = function () {
                        if (http.readyState == 4 && http.status == 200) {
                            item_ids = JSON.parse(http.responseText).items;
                            callback(item_ids);
                        }
                    }
                    http.send();
                    return item_ids;
                } else {

                    var s3_data_tenant_recommendation_path = config_s3_data_recommendation_path.replace("{tenant}", tenant_id);
                    var end_point = config_s3_resource_url + s3_data_tenant_recommendation_path + typename + "/" + item_id + ".json";

                    var http = new XMLHttpRequest();
                    var item_ids = [];

                    http.open("GET", end_point, true);
                    http.onreadystatechange = function () {
                        if (http.readyState == 4 && http.status == 200) {
                            item_ids = JSON.parse(http.responseText).items;
                            callback(item_ids);
                        }
                    }
                    http.send();
                    return item_ids;
                }
            }

            /**
             *
             * @param ele
             * @param item_ids
             * @param params
             */
            function loadItems(ele, item_ids, params){
                drawAsyncThumbListRecommendation(ele, item_ids, params);
            }

            /**
             *
             * @param typename
             */
            function showTypedItems(typename) {
                var ele = document.querySelectorAll(".predictry-" + typename)[0];
                var item_id = ele.attributes['data-predictry-item-id'].value;
                var params = {
                    limit: ele.attributes['data-predictry-limit'].value,
                    typed: typename,
                    title: config_typed_titles[typename]
                };

                var load = function (items) {
                    loadItems(ele, items, params, typename);
                }
                getItems(item_id, typename, load);
            }

            /**
             * checkWidget
             * @description check the widget tpyes are on dom
             *                  and if it is, show them
             */
            function checkWidget() {
                [].forEach.call(config_widget_type , function(typename) {
                    if (checkPredictryType(typename)) {
                        showTypedItems(typename);
                    }
                });
            }

            return {
                setTenantId: function (id) {
                    tenant_id = id;
                },
                setApiKey: function (key) {
                    api_key = key;
                },
                setWidgetId: function (id) {
                    widget_id = id;
                },
                setSessionID: function (session_id) {
                    var val = getCookie(getCookieName("session"));
                    if (val) {
                        setSessionCookieId(val); //extend lifetime
                        return;
                    }
                    else {
                        session_id = isDefined(session_id) ? session_id : getSessionID();
                        setSessionCookieId(session_id);
                    }
                },
                setSessionUserID: function (session_user_id) {
                    var val = getCookie(getCookieName("user"));
                    if (val) {
                        setUserSessionCookieId(val); //extend lifetime
                        return;
                    }
                    else {
                        session_user_id = isDefined(session_user_id) ? session_user_id : getSessionUserID();
                        setUserSessionCookieId(session_user_id);
                    }
                },
                setSessionBrowserID: function (session_browser_id) {
                    var val = getCookie(config_cookie_name_prefix + "browser");
                    if (val)
                        return;
                    else {
                        session_browser_id = isDefined(session_browser_id) ? session_browser_id : getSessionBrowserID();
                        setBrowserSessionCookieId(session_browser_id);
                    }
                },
                setSessionCart: function () {
                    var val = getCookie(getCookieName("cart"));
                    if (val) {
                        var cartSession = eval("(" + getCookie(getCookieName("cart")) + ")");
                        setCartSessionCookie(cartSession);
                    }
                    return (val) ? val : setCartSessionCookie();
                },
                setSessionView: function () {
                    var val = getCookie(getCookieName("view"));
                    if (val) {
                        var viewSession = eval("(" + getCookie(getCookieName("view")) + ")");
                        setViewSessionCookie(viewSession);
                    }
                    return (val) ? val : setViewSessionCookie();
                },
                setItemIntoCartSession: setItemIntoCartSession,
                setItemIntoViewSession: setItemIntoViewSession,
                setCartLog: setCartLog,
                setDoNotTrack: function (enable) {
                    config_do_not_track = (enable);
                },
                getSessionID: getSessionID,
                getSessionUserID: getSessionUserID,
                getSessionBrowserID: getSessionBrowserID,
                getSessionCart: getSessionCart,
                getCartID: getCartID,
                getPredictryParams: getPredictryParams,
                getWidgetID: function () {
                    return widget_id;
                },
                _deprecated_getWidget: function () {
                    if (!is_lookup_widget) {
                        predictry_nodes = document.querySelectorAll(".predictry");
                        [].forEach.call(predictry_nodes, function (elem) {
                            var i = 0, ds = elem.dataset;
                            if (ds.predictryWidgetId === undefined || ds.predictryWidgetId === "")
                                return;

                            var data = {item_id: ds.predictryItemId, user_id: ds.predictryUserId, widget_id: ds.predictryWidgetId};
                            if (ds.predictryCallback === undefined) {
                                _predictry.push(['getRecommendedItems', data, function (response) {
                                        if (response !== undefined && i === 0) {
                                            var obj = JSON.parse(response);
                                            if (typeof obj === 'object') {
                                                widgets.push({widget_id: ds.predictryWidgetId, response: obj.data.items});
                                                _predictry.push(['drawList', elem, obj]);
                                            }
                                            i++;
                                        }
                                    }
                                ]);
                            }
                            else
                                _predictry.push(['getRecommendedItems', data, ds.predictryCallback]);
                        });
                        is_lookup_widget = true;
                    }
                },
                getRecommendedItems: getRecommendedItems,
                getPreComputedRecommendedItems: getPreComputedRecommendedItems,
                getPreComputedCategoryBasedRecommendedItems: getPreComputedCategoryBasedRecommendedItems,
                getWidget: function () {
                    if (!is_lookup_widget) {
                        predictry_nodes = document.querySelectorAll(".predictry");
                        [].forEach.call(predictry_nodes, function (elem) {
                            var i = 0, ds = elem.dataset;
                            var params = {
                                item_id: ds.predictryItemId,
                                user_id: ds.predictryUserId,
                                widget_id: ds.predictryWidgetId,
                                theme: ds.predictryTheme,
                                title: ds.predictryTitle,
                                currency: ds.predictryCurrency,
                                category: ds.predictryCategory,
                                hide_title: true,
                                limit: ds.predictryLimit
                            };


                            if (ds.predictryCallback === undefined) {
                                _predictry.push(['getPreComputedRecommendedItems', params, function (responseText) {
                                        if (responseText !== undefined && i === 0) {
                                            var obj = JSON.parse(responseText);
                                            if (isObject(obj) && isDefined(obj.items)) {
                                                if (obj.items.length === 0) {
                                                    _predictry.push(['getPreComputedCategoryBasedRecommendedItems', params, function (responseText2) {
                                                            var obj_category_items = JSON.parse(responseText2);
                                                            if (isObject(obj_category_items) && isDefined(obj_category_items.items)) {
                                                                widgets.push({widget_id: ds.predictryWidgetId, response: obj_category_items});
                                                                _predictry.push(['drawAsyncThumb', elem, obj_category_items.items, params, 'cat']);
                                                            }
                                                        }
                                                    ]);
                                                }
                                                else {
                                                    widgets.push({widget_id: ds.predictryWidgetId, response: obj});
                                                    _predictry.push(['drawAsyncThumb', elem, obj.items, params, obj.algo]);
                                                }
                                            }
                                            else {
                                                _predictry.push(['getPreComputedCategoryBasedRecommendedItems', params, function (responseText2) {
                                                        var obj_category_items = JSON.parse(responseText2);
                                                        if (isObject(obj_category_items) && isDefined(obj_category_items.items)) {
                                                            widgets.push({widget_id: ds.predictryWidgetId, response: obj_category_items});
                                                            _predictry.push(['drawAsyncThumb', elem, obj_category_items.items, params, 'cat']);
                                                        }
                                                    }
                                                ]);
                                            }
                                            i++;
                                        }
                                    }]);
                            } else {
                                _predictry.push(['getPreComputedRecommendedItems', params, ds.predictryCallback]);
                            }
                        });
                        is_lookup_widget = true;
                    }
                },
                track: track,
                drawList: drawTextListRecommendation,
                drawAsyncList: drawAsyncTextListRecommendation,
                drawAsyncThumb: drawAsyncThumbListRecommendation,
                removeItem: function (id, realtime) {
                    if (isDefined(id)){
                        if (realtime) {
                            data = {
                                action: {
                                    name: "delete_item",
                                    realtime: true
                                },
                                items: id
                            };
                        }
                        else {
                                data = {
                                    action: {
                                        name: "delete_item"
                                    },

                                    items: id
                                };
                        }
                        track(data, realtime);
                    } else {
                        return;
                    }
                },
                updateItem: function (data) {
                    if (isDefined(data) && isObject(data)) {
                        if (isDefined(data.item)) {
                            config_request_content_type = "application/json; charset=utf-8";
                            config_request_method = "PUT";
                            return sendRequest(config_api_url + config_api_resources[2] + "/" + data.item.item_id, data, true, null, true);
                        }
                    }
                    return;
                },
                addItem: function (data) {
                    if (isDefined(data) && isObject(data)) {
                        if (isDefined(data.item)) {
                            config_request_content_type = "application/json; charset=utf-8";
                            return sendRequest(config_api_url + config_api_resources[2], data, true, null, true);
                        }
                    }
                },
                appendStyle: appendStyle,
                getSerializedPayload: function (data) {
                    return mapJSONToUriParams(data);
                },
                cart_id: temp_cart_id,
                widget_id: widget_id,
                widget_instance_id: widget_instance_id,
                is_lookup_widget: is_lookup_widget,
                predictry_nodes: predictry_nodes,
                widgets: widgets,
                recentlyViewed: function (callback, max) {
                    recentlyItemsCallback(callback, max);
                },
                getConfig: getConfig,
                checkWidget: checkWidget,
                setBaseURL: function(url) {
                    baseURL = url;
                }
            };
        }

        function ExecutorProxy() {
            return {
                push: apply
            };
        }

        //INIT CALLS
        // _predictry.push(['appendStyle']);
        _predictry.push(['setSessionID']);
        _predictry.push(['setSessionBrowserID']);
        _predictry.push(['setSessionUserID']);
        _predictry.push(['setSessionCart']);
        _predictry.push(['setSessionView']);
        _predictry.push(['checkWidget']);
        _predictry.push(['getPredictryParams', document.location.search]);

        async_executor = new Executor(window_alias.PE_tenantId, window_alias.PE_apiKey);

        var execute_first = {setTenantId: 1, setApiKey: 1, setSessionID: 1, setSessionBrowserID: 1, setSessionUserID: 1, setSessionCart: 1, setSessionView: 1, getPredictryParams: 1};
        var method_name;

        for (iterator = 0; iterator < _predictry.length; iterator++) {
            method_name = _predictry[iterator][0];

            if (execute_first[method_name]) {
                apply(_predictry[iterator]);
                delete _predictry[iterator];

                if (execute_first[method_name] > 1) {
                    if (console !== undefined && console && console.error) {
                        console.error('The method ' + method_name + ' is registered more than once in "_predictry" variable. Only the last call has an effect.');
                    }
                }
                execute_first[method_name]++;
            }
        }

        // apply the queue of actions
        for (iterator = 0; iterator < _predictry.length; iterator++) {
            if (_predictry[iterator]) {
                apply(_predictry[iterator]);
            }
        }

        // replace initialization array with proxy object
        _predictry = new ExecutorProxy();

        /************************************************************
         * Public data and methods
         ************************************************************/
        Predictry = {
            getAsyncExecutor: function () {
                return async_executor;
            },
            getExecutor: function (tenant_id, api_key) {
                return new Executor(tenant_id, api_key);
            }
        };

        if (typeof define === 'function' && define.amd) {
            define('predictry', [], function () {
                return Predictry;
            });
        }

        return Predictry;
    }
    ());
}

if (typeof predict !== 'object') {
    predict = Predictry.getAsyncExecutor();
}

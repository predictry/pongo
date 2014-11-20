/**
 * Author       : Rifki Yandhi
 * Date Created : Aug 11, 2014 9:10:30 AM
 * Email        : rifkiyandhi@gmail.com 
 * Function     : Predictry JS SDK
 * Version      : 0.2.0
 * Revision     : 3
 */

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
                navigator_alias = navigator,
                /* encode */
                encode_wrapper = window_alias.encodeURIComponent,
                /* decode */
                decode_wrapper = window_alias.decodeURIComponent,
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
         * Is property undefined ?
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

        /*
         * apply wrapper
         *
         * @param array parameter_array An array comprising either:
         *      [ 'methodName', optional_parameters ]
         * or:
         *      [ functionObject, optional_parameters ]
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
         * Load JavaScript file (asynchronously)
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
         * Get page referrer
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
         * Extract scheme/protocol from URL
         */
        function getProtocolScheme(url) {
            var e = new RegExp('^([a-z]+):'),
                    matches = e.exec(url);

            return matches ? matches[1] : null;
        }

        /*
         * Extract hostname from URL
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
         * Extend object
         * 
         * @param {type} options
         * @param {type} defaults
         * @returns {PE_defaults|copy}
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
         * @returns {unresolved}
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

        /*
         * guidj@bitbucket
         */

        function decodeUriParam(value) {
            var values = value.split("=");

            return {"key": decodeURIComponent(values[0]), "value": decodeURIComponent(values[1])};
        }

        function mapJSONToUriParams(data, prefix, call) {
            prefix = typeof prefix !== 'undefined' ? prefix : "";
            call = typeof call !== 'undefined' ? call : 0;

            var map = [];

            if (Object.prototype.toString.call(data) === '[object Array]') {
                for (var ik = 0; ik < data.length; ik++) {
                    map.push(mapJSONToUriParams(data[ik], prefix + "[" + ik + "]", call + 1));
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

                    map.push(mapJSONToUriParams(data[k], prefix + sep + k, call + 1));
                });

            } else {
                map.push(prefix + "=" + data);
            }

            return map.join("&");
        }

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

        function mapUriParamsToJSON(data, object, call) {

            call = typeof call !== 'undefined' ? call : 0;
            object = typeof object !== 'undefined' ? object : {};

            if (call === 0) {
                data = decodeURIComponent(data).split("&");

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
         * Predictry Executor class
         * 
         * @param {string} tenant_id
         * @param {string} api_key
         * @returns {Predictry.Executor.Anonym$0}
         */
        function Executor(tenant_id, api_key) {
            var
                    tenant_id = window_alias.PE_tenantId,
                    api_key = window_alias.PE_apiKey;

            var
                    response = {},
                    data = {};

            var
                    compulsary_params = new Array("user_id", "action", "item_id", "session_id", "description"),
                    optional_params = new Array("item_properties", "action_properties");

            var
                    widget_id = 0,
                    widget_instance_id = 0,
                    temp_cart_id = -1;

            var call_url = null;

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
                    config_api_url = "http://api-aws.predictry.com/api/v2/",
                    config_img_url = "https://d1j642hg7oh3vx.cloudfront.net/",
                    config_api_resources = ["actions", "users", "items", "carts", "cartlogs", "recommendation"],
                    config_default_actions = ["view", "add_to_cart", "buy", "started_checkout", "started_payment"],
                    config_session_cookie_timeout = 63072000000, // 2 years
                    config_tracking_session_cookie_timeout = 1200000, //20 minutes
                    config_do_not_track = false,
                    recent_xhr = null;

            var recent_response;

            /*
             * Does browser have cookies enabled (for this site)?
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
             * @returns {returnValue|String}
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
            function drawTextListRecommendation(elem, obj)
            {
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

            function toObject() {
                var obj_response = eval("(" + recent_response + ")");
                return obj_response;
            }

            function deleteCookies() {
                setCookie(getCookieName('session'), '', -86400, config_cookie_path);
                setCookie(getCookieName('cart'), '', -86400, config_cookie_path);
                setCookie(getCookieName('view'), '', -86400, config_cookie_path);
            }

            /*
             * Set cookie value
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
                setCookie(getCookieName("browser"), unique_id, config_session_cookie_timeout, config_cookie_path);
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
             * @returns {ready_data|undefined|data|_L1|PE_defaults|@exp;reco_data|@exp;copy|@exp;eExtend@pro;options|reco_data|copy|type|String}
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
             * Update View Item Session
             * 
             * @param {string} item_id
             * @returns {void}
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
             * Get Session ID
             * 
             * @returns {String|Number}
             */
            function getSessionID() {
                var val = getCookie(getCookieName("session"));
                return (val) ? val : setSessionCookieId(generateSessionID(15));
            }

            /**
             * Get Session User ID
             * @returns {Number|String}
             */
            function getSessionUserID() {
                var val = getCookie(getCookieName("user"));
                return (val) ? val : setUserSessionCookieId(generateSessionID(15));
            }

            /**
             * Set Browser ID
             */
            function getSessionBrowserID() {
                var val = getCookie(getCookieName("browser"));
                return (val) ? val : setBrowserSessionCookieId(generateSessionID(15));
            }

            /**
             * Get Session Cart
             * 
             * @returns {object|Number|_L14.Executor.setCartSessionCookie.c_obj}
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

//            function getRecommendedItems(widget_id, user_id, item_id, callback) {
            function getRecommendedItems(reco_data, callback) {
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
//                    user_id = getSessionUserID();

                var data = {
                    widget_id: widget_id,
                    user_id: user_id,
                    item_id: item_id,
                    session_id: getSessionID()
                };

                config_request_method = "GET";
                var url = config_api_url + config_api_resources[5] + "?" + buildUrl(data);
                sendRequest(url, buildUrl(data), true, callback);
                config_request_method = config_default_request_method;
                return response;
            }

            /*
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

            /*
             * Get cookie name with prefix and domain hash
             */
            function getCookieName(base_name) {
                return config_cookie_name_prefix + base_name + '.' + tenant_id;
            }

            /*
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
                image.src = config_img_url + resource + ".gif?" + queries;
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
                    if (http.readyState === 4 && http.status === 200) {
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

            function appendPredictryData(data) {
                data.session_id = getSessionID();
                data.user_id = getSessionUserID();
                data.browser_id = getSessionBrowserID();
                data.tenant_id = tenant_id;
                data.api_key = api_key;
                return data;
            }

            function getWidgetInstanceID(uri) {
                if (isDefined(uri) && isDefined(getParameter(uri, "predictry_src")))
                    widget_instance_id = getParameter(uri, "predictry_src");
                else
                    widget_instance_id = -1;
                return widget_instance_id;
            }

            function track(data) {
                if (!isDefined(data) || !isObject(data))
                    return;

                if (!isDefined(data.action))
                    return;

                data = appendPredictryData(data);

                if (isDefined(data.action.name))
                {
                    if (data.action.name === "view" && data.items.length === 1) {
                        //check if the viewed item is from reco or not
                        if (widget_instance_id > 0 && isDefined(data.action))
                            data.action.rec = true;
                        else if (widget_instance_id > 0 && !isDefined(data.action))
                            data.action = {rec: true};

                        trackView(data);
                    } else if (data.action.name === "buy" && data.items.length >= 1) {
                        trackBuy(data);
                        deleteCookies();
                    } else if (data.action.name === "started_checkout" || data.action.name === "started_payment") {
                        trackBulk(data);
                    }
                    else {
                        config_request_content_type = "application/json; charset=utf-8";
                        return sendRequest(config_api_url + config_api_resources[0], data, true, null, true);
                    }
                }
            }

            function trackView(data) {

                //if data.action.rec is true store it into cookie
                if (isDefined(data.action) && isDefined(data.action.rec))
                    setItemIntoViewSession(data.items[0].item_id);

                config_request_content_type = "application/json; charset=utf-8";
                return sendRequest(config_api_url + config_api_resources[0], data, true, null, true);
            }

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

                config_request_content_type = "application/json; charset=utf-8";
                return sendRequest(config_api_url + config_api_resources[0], data, true, null, true);
            }

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

                config_request_content_type = "application/json; charset=utf-8";
                return sendRequest(config_api_url + config_api_resources[0], data, true, null, true);
            }

            /************************************************************
             * Constructor
             ************************************************************/

            /************************************************************
             * Public data methods
             ************************************************************/

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
                        session_id = isDefined(session_id) ? session_id : generateSessionID(15);
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
                        session_user_id = isDefined(session_user_id) ? session_user_id : generateSessionID(15);
                        setUserSessionCookieId(session_user_id);
                    }
                },
                setSessionBrowserID: function (session_browser_id) {
                    var val = getCookie(getCookieName("browser"));
                    if (val)
                        return;
                    else {
                        session_browser_id = isDefined(session_browser_id) ? session_browser_id : generateSessionID(15);
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
                getSessionCart: getSessionCart,
                getCartID: getCartID,
                getWidgetInstanceID: getWidgetInstanceID,
                getWidgetID: function () {
                    return widget_id;
                },
                getWidget: function () {
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
                track: track,
                drawList: drawTextListRecommendation,
                cart_id: temp_cart_id,
                widget_id: widget_id,
                widget_instance_id: widget_instance_id,
                is_lookup_widget: is_lookup_widget,
                predictry_nodes: predictry_nodes,
                widgets: widgets
            };
        }

        function ExecutorProxy() {
            return {
                push: apply
            };
        }

        //INIT CALLS
        _predictry.push(['setSessionID']);
        _predictry.push(['setSessionBrowserID']);
        _predictry.push(['setSessionUserID']);
        _predictry.push(['setSessionCart']);
        _predictry.push(['setSessionView']);
        _predictry.push(['getWidgetInstanceID', document.location.search]);

        async_executor = new Executor(window_alias.PE_tenantId, window_alias.PE_apiKey);
        var execute_first = {setTenantId: 1, setApiKey: 1, setSessionID: 1, setSessionBrowserID: 1, setSessionUserID: 1, setSessionCart: 1, getWidgetInstanceID: 1};
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
    }());
}

if (typeof predict !== 'object') {
    predict = Predictry.getAsyncExecutor();
}
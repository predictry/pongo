(function() {
    window.PE_platformVer = 1;
//    window.PE_langJS = "http" + ("https:" === document.location.protocol ? "s" : "") + "://api.predictry.com/lang-EN.min.js";
    window.PE_placementId = 10;
    window.PE_recMode = "pe_text"; //list text "pe_box" for widget list
    window.PE_apiKey = 10;
    var a = document.createElement("script");
    a.type = "text/javascript";
    a.async = true;
    a.src = "http" + ("https:" === document.location.protocol ? "s" : "") + "://api.predictry.com/predictry-api.min.js";
    var b = document.getElementsByTagName("script")[0];
    b.parentNode.insertBefore(a, b)
})();

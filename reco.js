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
    var numberOfResults = (elem_predictry.getAttribute("data-number-of-results") !== null) ? elem_predictry.getAttribute("data-number-of-results") : window.numberOfResults;

    var reco_data = {
        item_id: item_id,
        user_id: user_id,
        session_id: getSessionID(),
        number_of_results: window.PE_numberOfResults,
        algo: window.PE_recoType
    };

    var json_result = getRecommendation(reco_data);

    if (window.PE_recoType === "pe_text")
    {
        drawTextListRecommendation(json_result, "PREDICTRY");
    }
    else if (window.PE_recoType === "pe_grid")
    {
        drawListGridRecommendation(json_result);
    }
    else {
        drawTextListRecommendation(json_result);
    }
}
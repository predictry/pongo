var elem_predictries = document.getElementsByClassName("PREDICTRY");
var elem_predictry = null;
var pe_recommendation = null;

for (var i = 0; i < elem_predictries.length; ++i) {
    var elem_predictry = elem_predictries[i];
    break;
}

if (elem_predictry !== null)
{
    var item_id = elem_predictry.getAttribute("data-item-id");
    var user_id = (elem_predictry.getAttribute("data-user-id") !== null) ? elem_predictry.getAttribute("data-user-id") : Predictry.getSessionUserID();
    var numberOfResults = (elem_predictry.getAttribute("data-number-of-results") !== null) ? elem_predictry.getAttribute("data-number-of-results") : window.numberOfResults;

    var reco_data = {
        item_id: item_id,
        user_id: user_id,
        widget_id: window.PE_widgetId
    };

    var pe_reco_json_result = Predictry.getRecommendedItems(reco_data);
    pe_recommendation = pe_reco_json_result;

    console.log("reco results");
    console.log(pe_reco_json_result);

    if (pe_reco_json_result && pe_reco_json_result.status === 'success')
    {
        if (window.PE_recoType === "pe_text")
            Predictry.drawList(pe_reco_json_result, "PREDICTRY");
        else if (window.PE_recoType === "pe_grid")
            Predictry.drawGrid(pe_reco_json_result);
        else
            Predictry.drawList(pe_reco_json_result);
    }
}

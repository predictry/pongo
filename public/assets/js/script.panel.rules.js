var numOfItems = 1;
var indexOfItem = 1;
var indexes = new Array();

function addItemRule(is_modal, container) {
    var bool_is_modal = false;
    var div_container = "item_rules_container";
    var item_identifier = "#item";

    if (is_modal !== undefined) {
        bool_is_modal = true;
        item_identifier = "#modalItem";
        type_identifier = "#modalType";
    } else {
        item_identifier = "#item";
        type_identifier = "#type";
    }

    if (container !== undefined)
        div_container = container;

    var btn = $("a.btnAddItemRule");
    btn.removeClass("btnAddItemRule btn-default").addClass("btnRemoveItemRule btn-danger").html("Remove");
    btn.attr("onClick", "removeItem(" + indexOfItem + ", 'item_rule');");
    indexOfItem += 1;

    var form_data = {
        index: indexOfItem,
        is_ajax: 1
    };

    var url = site_url + "/rules/item";
    if (bool_is_modal)
        url = site_url + "/rules/modalItem";

    $.ajax({
        url: url,
        type: 'GET',
        data: form_data,
        dataType: 'json',
        success: function (data)
        {
            if (data.status === "success") {
                $("#" + div_container).append(data.response);
                $(".chosen-select-rules").chosen({width: '300px'});
                $(".chosen-select").chosen({width: '200px'});
                numOfItems += 1;

                $(item_identifier + indexOfItem).chosen();
                $(type_identifier + indexOfItem).chosen();
                $(item_identifier + indexOfItem).val("").trigger('chosen:updated');

                $(item_identifier + indexOfItem).on('change', function (evt, params) {
                    if (!validateSelectedItem(params.selected, "item_id")) {
                        $(item_identifier + indexOfItem).val("").trigger('chosen:updated');
                    }
                });
            }
        },
        error: function () {
            alert('error!');
        }
    });
}

function editItemRule(obj, index) {
    var form_data = {
        obj: obj,
        index: index,
        is_ajax: 1
    };

    $.ajax({
        url: site_url + "/rules/itemEdit",
        type: 'GET',
        data: form_data,
        dataType: 'json',
        success: function (data)
        {
            if (data.status === "success") {
                $("#item_rules_container").append(data.response);
                $(".chosen-select-rules").chosen({width: '300px'});
                $(".chosen-select").chosen({width: '200px'});
                $("#item" + index).chosen();
                $("#type" + index).chosen();
                $("#item" + index).val(obj.item_id).trigger('chosen:updated');

                $("#item" + index).on('change', function (evt, params) {
                    if (!validateSelectedItem(params.selected, "item_id")) {
                        $("#item" + index).val("").trigger('chosen:updated');
                    }
                });

                indexOfItem = index;
            }
        },
        error: function () {
            alert('error!');
        }
    });
}

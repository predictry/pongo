function validateSelectedItem(selected, name) {
    excludeOptionIDs = gatherSelectedOption(name);
    if (excludeOptionIDs.length > 1) {
        var counter = 0;
        for (var i = 0; i < excludeOptionIDs.length; i++) {
            if (excludeOptionIDs[i] === selected)
                counter += 1;

            if (counter === 2)
            {
                alert("This option already selected");
                return false;
            }
        }

    }
    return true;
}

function editItemWidgetRuleset(obj, index) {
    var form_data = {
        obj: obj,
        index: index,
        is_ajax: 1
    };

    $.ajax({
        url: site_url + "/widgets/itemEdit",
        type: 'GET',
        data: form_data,
        dataType: 'json',
        success: function (data)
        {
            if (data.status === "success") {
                $("#item_rules_container").append(data.response);
                $(".chosen-select").chosen({width: '300px'});
                $("#item" + index).chosen();
                $("#type" + index).chosen();
                $("#item" + index).val(obj.ruleset_id).trigger('chosen:updated');

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

function editItemWidgetFilter(obj, index) {
    var form_data = {
        obj: obj,
        index: index,
        is_ajax: 1
    };

    $.ajax({
        url: site_url + "/widgets/itemFilterEdit",
        type: 'GET',
        data: form_data,
        dataType: 'json',
        success: function (data)
        {
            if (data.status === "success") {
                $("#item_filters_container").append(data.response);
                $(".chosen-select").chosen({width: '300px'});
                $("#itemfilter" + index).chosen();
                $("#type" + index).chosen();
                $("#itemfilter" + index).val(obj.filter_id).trigger('chosen:updated');

                $("#itemfilter" + index).on('change', function (evt, params) {
                    if (!validateSelectedItem(params.selected, "item_id")) {
                        $("#itemfilter" + index).val("").trigger('chosen:updated');
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


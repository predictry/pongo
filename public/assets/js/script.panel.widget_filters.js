var numOfItems = 1;
var indexOfItem = 1;

function getFilterType(id, val) {
    var dd = $("#type" + id);
    var input = $("#value" + id);
    var inputDiv = input.parent();
    var value = (val !== undefined) ? val : "";

    //when adding date input, it seems that the parent has changed. So need to go to parent parent
    if ($("#inputDate" + id).val() !== undefined) {
        inputDiv = $("#inputDate" + id).parent();
    }

    if ($("#propertyKeyWrapper" + id).val() !== undefined) {
        inputDiv = $("#propertyKeyWrapper" + id).parent().parent();
    }

    switch (dd.val()) {
        case "date":
            var inputDate = "<div class='input-group date' id='inputDate" + id + "' data-date-format='YYYY-MM-DD hh:mm A'>"
                    + "<input name='value[]' id='value" + id + "' type='text' class='form-control disabled' readonly='' value='" + value + "'/>"
                    + "<span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span>"
                    + "</span></div>";

//            var inputDate = "<input class='form-control date' id='value" + id + "' name='value[]' type='text' data-date-format='YYYY-MM-DD hh:mm:ss A'/>";
            inputDiv.html(inputDate);
            input = $("#inputDate" + id);
            input.datetimepicker();
            break;

        case "str":
        case "list":
            var inputStr = "<input class='form-control' id='value" + id + "' name='value[]' type='text' value='" + value + "'/>";
            inputDiv.html(inputStr);
            break;

        case "location":

            var propertyName = $("#action" + id).val();
            $.ajax({
                url: site_url + "/items/key/" + propertyName + "/metas",
                type: 'GET',
                data: {
                    isDropDown: true,
                    isView: true,
                    id: id,
                    val: value
                },
                dataType: 'json',
                success: function (response)
                {
                    if (response.status === "success") {
                        data = response.data;
//                        var wrapperDiv = "<div class='row ' id='" + propertyName + "Wrapper" + id + "'><div class='col-sm-6' id='propertyKeyWrapper" + id + "'>";
                        var wrapperDiv = "<div class='row ' id='" + propertyName + "Wrapper" + id + "'><div class='col-sm-12' id='propertyKeyWrapper" + id + "'>";
                        wrapperDiv += response.view + "</div></div>";
                        inputDiv.html(wrapperDiv);

                        propertyMetas[propertyName] = response.data;
//                        viewMetaDetail(id, propertyName, value);
                        $(".chosen-select").chosen();
                    }
                },
                error: function () {
                    alert('error!');
                }
            });

            break;

        case "num":
            var inputNum = "<input class='form-control' onkeydown='javascript:maskInputToNumeric()' value='" + value + "' id='value" + id + "' name='value[]' type='number'/>";
            inputDiv.html(inputNum);
            break;
    }
}

function addItemFilter() {
    var btn = $('a.btnAddItem');
    btn.removeClass("btnAddItem btn-default").addClass("btnRemoveItem btn-danger").html("Remove");
    btn.attr("onClick", "removeItem(" + indexOfItem + ", 'item_funel');");
    indexOfItem += 1;

    var form_data = {
        index: indexOfItem,
        is_ajax: 1
    };

    $.ajax({
        url: site_url + "/filters/item",
        type: 'GET',
        data: form_data,
        dataType: 'json',
        success: function (data)
        {
            if (data.status === "success") {
                $("#filter_item_container").append(data.response);
                numOfItems += 1;
                $(".chosen-select").chosen();
            }
        },
        error: function () {
            alert('error!');
        }
    });
}

function editItemFilter(obj, index) {
    var form_data = {
        obj: obj,
        index: index,
        is_ajax: 1
    };

    $.ajax({
        url: site_url + "/filters/itemEdit",
        type: 'GET',
        data: form_data,
        dataType: 'json',
        success: function (data)
        {
            if (data.status === "success") {
                $("#filter_item_container").append(data.response);
                $(".chosen-select").chosen();
                $("#action" + index).chosen();
                $("#type" + index).chosen();

                var input = $("#value" + index);
                getFilterType(index, input.val());
                if (index >= indexOfItem)
                    indexOfItem = index;
            }
        },
        error: function () {
            alert('error!');
        }
    });
}

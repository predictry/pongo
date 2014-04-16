jQuery(document).ready(function() {
    $(".alert").alert();
    $('.dropdown-toggle').dropdown();
    $('.tt').tooltip();
    if (typeof graph_data !== 'undefined') {
        new Morris.Line({
            element: 'myfirstchart',
            data: graph_data,
            xkey: graph_x_keys,
            ykeys: graph_y_keys,
            labels: graph_y_keys,
            xLabels: graph_x_keys
        });
    }

    $('a.btnViewModal').on('click', function(e) {
        var target_modal = $(e.currentTarget).data('target');
        var remote_content = e.currentTarget.href;

        var modal = $(target_modal);
        var modalBody = $(target_modal + ' .modal-body');
        var e = 1;
        modal.on('show.bs.modal', function() {
            if (e)
                modalBody.load(remote_content);
            e = 0;
        }).modal();
        return false;
    });
    $('#viewModal').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });


    $("#item1").chosen();
    $("#type1").chosen();

    $('#item1').on('change', function(evt, params) {
        if (!validateSelectedItem(params.selected)) {
            $('#item1').val("").trigger("chosen:updated");
        }
    });
});

var numOfItemRules = 1;
var indexOfItemRule = 1;
var excludeItemRuleIDs = new Array();
var indexes = new Array();

indexes.push(1);

function gatherSelectedOption(name)
{
    var values = [];
    $("select[name='" + name + "[]'] option:selected").each(function() {
        values.push(this.value);
    });
    return values;
}

function validateSelectedItem(selected)
{
    excludeItemRuleIDs = gatherSelectedOption("item_id");
    if (excludeItemRuleIDs.length > 1) {
        var counter = 0;
        for (var i = 0; i < excludeItemRuleIDs.length; i++) {
            if (excludeItemRuleIDs[i] === selected)
                counter += 1;

            if (counter === 2)
            {
                alert("This item already selected");
                return false;
            }
        }

    }
    return true;
}

function removeItemRule(id)
{
    $("#item_rule" + id).remove();
    var index = indexes.indexOf(id);

    if (index > -1)
        indexes.splice(index, 1);
}

function addItemRule()
{
    var btn = $("a.btnAddItemRule");
    btn.removeClass("btnAddItemRule btn-default").addClass("btnRemoveItemRule btn-danger").html("Remove");
    btn.attr("onClick", "removeItemRule(" + indexOfItemRule + ");");
    indexOfItemRule += 1;

    var form_data = {
        index: indexOfItemRule,
        is_ajax: 1
    };

    $.ajax({
        url: site_url + "/rules/item",
        type: 'GET',
        data: form_data,
        dataType: 'json',
        success: function(data)
        {
            if (data.status === "success") {
                $("#item_rules_container").append(data.response);
                $(".chosen-select").chosen();
                numOfItemRules += 1;

                $("#item" + indexOfItemRule).chosen();
                $("#type" + indexOfItemRule).chosen();
                $("#item" + indexOfItemRule).val("").trigger('chosen:updated');

                $("#item" + indexOfItemRule).on('change', function(evt, params) {
                    if (!validateSelectedItem(params.selected)) {
                        $("#item" + indexOfItemRule).val("").trigger('chosen:updated');
                    }
                });
            }
        },
        error: function() {
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
        success: function(data)
        {
            if (data.status === "success") {
                $("#item_rules_container").append(data.response);
                $(".chosen-select").chosen();
                $("#item" + index).chosen();
                $("#type" + index).chosen();
                $("#item" + index).val(obj.item_id).trigger('chosen:updated');

                $("#item" + index).on('change', function(evt, params) {
                    if (!validateSelectedItem(params.selected)) {
                        $("#item" + index).val("").trigger('chosen:updated');
                    }
                });

                indexOfItemRule = index;
            }
        },
        error: function() {
            alert('error!');
        }
    });
}
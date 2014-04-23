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

    //initilize 1st item rule that appear first time
    $("#item1").chosen();
    $("#type1").chosen();

    $('#item1').on('change', function(evt, params) {
        if (!validateSelectedItem(params.selected)) {
            $('#item1').val("").trigger("chosen:updated");
        }
    });

    $("#expiry_type").bind('change', function(e) {
        var valueSelected = this.value;
        var box = $("#expiry_value_box");

        if (valueSelected === "date/time") {

            box.find("input#expiry_value").addClass("hide").attr("name", "expiry_value_temp");
            var dtpicker = box.find("div#datetimepicker");
            dtpicker.find("input.form-control").attr("name", "expiry_value");
            dtpicker.removeClass("hide");

            $(function() {
                $('#datetimepicker').datetimepicker();
                if (expiry_date !== '')
                    $('#datetimepicker').data("DateTimePicker").setDate(expiry_date);
            });
        }
        else {
            box.find("div#datetimepicker").addClass("hide").attr("name", "expiry_value_temp");
            var dtpicker = box.find("div#datetimepicker");
            dtpicker.find("input.form-control").attr("name", "expiry_value_temp");
            dtpicker.addClass("hide");

            box.find("input#expiry_value").removeClass("hide").attr("name", "expiry_value");
            var val = box.find("input#expiry_value").val();
            if (!$.isNumeric(val))
                box.find("input#expiry_value").val(0);
        }
    });

    $("#datetimepicker").bind("dp.change", function() {
        var dt = $('#datetimepicker').data("DateTimePicker").getDate();
        $("#expiry_value_dt").val(dt);

    });

    $("#expiry_type").change(function() {
    }).trigger("change");

    $(".multiple-chosen-select").chosen({no_results_text: "Oops, nothing found!"});

    //wizard
    var navListItems = $('ul.setup-panel li a'),
            allWells = $('.setup-content');

    allWells.hide();

    navListItems.click(function(e)
    {
        e.preventDefault();
        var $target = $($(this).attr('href')),
                $item = $(this).closest('li');

        if (!$item.hasClass('disabled')) {
            navListItems.closest('li').removeClass('active');
            $item.addClass('active');
            allWells.hide();
            $target.show();
        }
    });

    $('ul.setup-panel li.active a').trigger('click');

    // WIZARD (PLACEMENT INFO) //
    $('#btnWizardPlacementInfo').on('click', function(e) {
        e.preventDefault();
        var form = $(".wizardPlacementForm");
        $.ajax({
            url: site_url + "/placements/ajaxSubmitWizardPlacement",
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(data)
            {
                if (data.status === "success") {
                    $('ul.setup-panel li:eq(1)').removeClass('disabled');
                    $('ul.setup-panel li a[href="#step-2"]').trigger('click');
                }
                else {
                    $(".wizardPlacmeent").html(data.response);
                }
            },
            error: function() {
            }
        });

        return;
    });

    // WIZARD (RULESETS INFO) //
    $("#btnWizardComplete").on("click", function(e) {
        //show loading
        var form = $(".wizardRulesetForm");

        $.ajax({
            url: site_url + "/placements/ajaxSubmitCompleteWizard",
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(data)
            {
                if (data.status === "success") {
                    $('ul.setup-panel li:eq(2)').removeClass('disabled');
                    $('ul.setup-panel li:eq(0)').addClass('disabled');
                    $('ul.setup-panel li:eq(1)').addClass('disabled');
                    $('ul.setup-panel li a[href="#step-3"]').trigger('click');
                    $("#wizardEmbedJS").html(data.response);
                }
                //hide loading
            },
            error: function() {
            }
        });

        return;
    });

    // DEMO ONLY //
    $('#activate-step-3').on('click', function(e) {
        $('ul.setup-panel li:eq(2)').removeClass('disabled');
        $('ul.setup-panel li a[href="#step-3"]').trigger('click');
        $(this).remove();
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
    console.log(excludeItemRuleIDs);
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

function addItemRule(is_modal, container)
{
    var bool_is_modal = false;
    var div_container = "item_rules_container";
    var item_identifier = "#item";

    if (is_modal !== "undefined") {
        bool_is_modal = true;
        item_identifier = "#modalItem";
        type_identifier = "#modalType";
    }

    if (container !== "undefined")
        div_container = container;

    var btn = $("a.btnAddItemRule");
    btn.removeClass("btnAddItemRule btn-default").addClass("btnRemoveItemRule btn-danger").html("Remove");
    btn.attr("onClick", "removeItemRule(" + indexOfItemRule + ");");
    indexOfItemRule += 1;

    var form_data = {
        index: indexOfItemRule,
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
        success: function(data)
        {
            console.log(data);
            if (data.status === "success") {
                $("#" + div_container).append(data.response);
                $(".chosen-select").chosen();
                numOfItemRules += 1;

                $(item_identifier + indexOfItemRule).chosen();
                $(type_identifier + indexOfItemRule).chosen();
                $(item_identifier + indexOfItemRule).val("").trigger('chosen:updated');

                $(item_identifier + indexOfItemRule).on('change', function(evt, params) {
                    if (!validateSelectedItem(params.selected)) {
                        $(item_identifier + indexOfItemRule).val("").trigger('chosen:updated');
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

function addItemPlacementRuleset()
{
    var btn = $("a.btnAddItemPlacementRuleset");
    btn.removeClass("btnAddItemPlacementRuleset btn-default").addClass("btnRemoveItemPlacementRuleset btn-danger").html("Remove");
    btn.attr("onClick", "removeItemRule(" + indexOfItemRule + ");");
    indexOfItemRule += 1;

    var form_data = {
        index: indexOfItemRule,
        is_ajax: 1
    };

    $.ajax({
        url: site_url + "/placements/item",
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

function editItemPlacementRuleset(obj, index)
{
    var form_data = {
        obj: obj,
        index: index,
        is_ajax: 1
    };

    $.ajax({
        url: site_url + "/placements/itemEdit",
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
                $("#item" + index).val(obj.ruleset_id).trigger('chosen:updated');

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
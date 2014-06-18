jQuery(document).ready(function() {
    $(".alert").alert();
    $('.dropdown-toggle').dropdown();
    $('.tt').tooltip();

    if (typeof graph_data !== 'undefined') {
        new Morris.Bar({
            element: 'defaultActionsChart',
            data: graph_data,
            xkey: graph_x_keys,
            ykeys: graph_y_keys,
            labels: graph_y_keys,
            xLabels: "day",
            hideHover: true
        });
    }
    if (typeof graph_non_default_data !== 'undefined') {
        new Morris.Bar({
            element: 'nonDefaultActionsChart',
            data: graph_non_default_data,
            xkey: graph_x_keys,
            ykeys: graph_y_non_default_keys,
            labels: graph_y_non_default_keys,
            xLabels: "day"
        });
    }

//    if (typeof graph_comparison_data !== 'undefined')
//    {
//        new Morris.Bar({
//            element: 'comparisonGraph',
//            data: graph_comparison_data,
//            xkey: comparison_graph_x_keys,
//            ykeys: comparison_graph_y_keys,
//            stacked: bar_type,
//            ymax: y_max,
//            labels: comparison_labels,
//            hideHover: true
//        });
//    }

    if (typeof highchart_pie_data !== 'undefined')
    {
        getGraphPieComparison(highchart_pie_data);
    }

    if (typeof donut_average_recommended_items_data !== 'undefined')
    {
        new Morris.Donut({
            element: 'averageRecommendedItemsDonut',
            data: donut_average_recommended_items_data,
            colors: ["#005dff", "#afafaf"]
        }).select(0);
    }

    if (typeof highchart_series_data !== 'undefined')
    {
        getGraphBarComparison();

        $('div#graph_type.btn-group .btn').click(function() {
            $(this).find('input:radio').attr('checked', true);
            var type = $(this).find('input[name=options]').val();

            $('.options_graph_style').each(function() {
                if ($(this).val() !== type)
                    $(this).attr('checked', false);
            });

            if (type === "bar")
            {
                getGraphBarComparison();
            }
            else {
                getGraphLineComparison();
            }

            return;
        });

    }


    $('div#range-type.btn-group ul.dropdown-menu li a').click(function(e) {
        var $div = $(this).parent().parent().parent();
        var $btn = $div.find('button');
        $btn.html($(this).text() + ' <span class="caret"></span>');
        $div.removeClass('open');
//        e.preventDefault();
//        return false;
    });

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
    $("#action1").chosen();
    $("#itemfilter1").chosen();
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
    $('#btnWizardWidgetInfo').on('click', function(e) {
        e.preventDefault();
        var form = $(".wizardWidgetForm");
        $.ajax({
            url: site_url + "/widgets/ajaxSubmitWizardWidget",
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
                    $(".wizardWidget").html(data.response);
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
            url: site_url + "/widgets/ajaxSubmitCompleteWizard",
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

    //CHOSEN ACTION FOR TRACKING COMPARISON
    $("#btnSubmitActionNonDefaultSelector").on("click", function(e) {
        e.preventDefault();
        //show loading
        var form = $(".actionNoDefaultSelectorForm");

        $.ajax({
            url: site_url + "/panel/ajaxRenderGraph",
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(data)
            {
                if (data.status === "success") {
                    window.location = site_url + data.response;
                } else {
                    $(".modal-body").html(data.response);
                }
            },
            error: function() {
            }
        });

        return;
    });

    $("#funel_preference_id").change(function() {
        $('#funelSelector').submit();
    });

    $('div.btn-group .btn').click(function() {
        $(this).find('input:radio').attr('checked', true);
        var type = $(this).find('input[name=options]').val();

        $('.options_trend').each(function() {
            if ($(this).val() !== type)
                $(this).attr('checked', false);
        });

        $.ajax({
            url: site_url + "/panel/trends",
            type: 'POST',
            data: {type: type},
            dataType: 'json',
            success: function(data)
            {
                if (data.status === "success") {
                    $("#trendsContent").html(data.response);
                }
            },
            error: function() {
            }
        });

        return;
    });
});

var numOfItems = 1;
var indexOfItem = 1;
var excludeOptionIDs = new Array();
var indexes = new Array();

indexes.push(1);

/**
 * Load Bar Graph of Comparison
 */
function getGraphBarComparison() {
    $("#highChartComparisonGraph").highcharts({
        title: {
            text: graph_title,
            x: -20, //center
            y: 5
        },
        chart: {
            type: 'column'
        },
        xAxis: {
            categories: highchart_categories_data
        },
        yAxis: {
            min: 0,
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }
        },
        legend: {
            align: 'right',
            x: -70,
            verticalAlign: 'top',
            y: 20,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            formatter: function() {
                return '<b>' + this.x + '</b><br/>' +
                        this.series.name + ': ' + this.y + '<br/>' +
                        'Total: ' + this.point.stackTotal;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black, 0 0 3px black'
                    }
                }
            }
        },
        series: highchart_series_data,
        credits: {
            enabled: false
        }
    });
}

/**
 * get graph line comparison
 */
function getGraphLineComparison() {
    $('#highChartComparisonGraph').highcharts({
        title: {
            text: graph_title,
            x: -20, //center
            y: 5
        },
        xAxis: {
            categories: highchart_categories_data
        },
        yAxis: {
            plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
        },
        legend: {
            align: 'right',
            x: -70,
            verticalAlign: 'top',
            y: 20,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        series: highchart_series_data,
        credits: {
            enabled: false
        }
    });
}

/**
 * get pie chart
 */
function getGraphPieComparison(chart_data) {
    $('#comparisonDonut').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: graph_title,
            style: 'font-size:14px;'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y}</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false,
                    format: '<b>{point.name}</b>: {y:.1f}',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                },
                showInLegend: true
            }
        },
        series: [{
                type: 'pie',
                name: 'Total',
                data: chart_data
            }],
        legend: {
            x: 0,
            y: -10,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        credits: {
            enabled: false
        }
    });
}

function gatherSelectedOption(name)
{
    var values = [];
    $("select[name='" + name + "[]'] option:selected").each(function() {
        values.push(this.value);
    });
    return values;
}

function validateSelectedItem(selected, name)
{
    excludeOptionIDs = gatherSelectedOption(name);
    console.log(excludeOptionIDs);
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

function removeItem(id, name)
{
    $("#" + name + id).remove();
    var index = indexes.indexOf(id);

    if (index > -1)
        indexes.splice(index, 1);
}

function addItemRule(is_modal, container)
{
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
        success: function(data)
        {
            if (data.status === "success") {
                $("#" + div_container).append(data.response);
                $(".chosen-select").chosen();
                numOfItems += 1;

                $(item_identifier + indexOfItem).chosen();
                $(type_identifier + indexOfItem).chosen();
                $(item_identifier + indexOfItem).val("").trigger('chosen:updated');

                $(item_identifier + indexOfItem).on('change', function(evt, params) {
                    if (!validateSelectedItem(params.selected, "item_id")) {
                        $(item_identifier + indexOfItem).val("").trigger('chosen:updated');
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
                    if (!validateSelectedItem(params.selected, "item_id")) {
                        $("#item" + index).val("").trigger('chosen:updated');
                    }
                });

                indexOfItem = index;
            }
        },
        error: function() {
            alert('error!');
        }
    });
}

function addItemWidgetRuleset()
{
    var btn = $("a.btnAddItemWidgetRuleset");
    btn.removeClass("btnAddItemWidgetRuleset btn-default").addClass("btnRemoveItemWidgetRuleset btn-danger").html("Remove");
    btn.attr("onClick", "removeItem(" + indexOfItem + ", 'item_rule');");
    indexOfItem += 1;

    var form_data = {
        index: indexOfItem,
        is_ajax: 1
    };

    $.ajax({
        url: site_url + "/widgets/item",
        type: 'GET',
        data: form_data,
        dataType: 'json',
        success: function(data)
        {
            if (data.status === "success") {
                $("#item_rules_container").append(data.response);
                $(".chosen-select").chosen();
                numOfItems += 1;

                $("#item" + indexOfItem).chosen();
                $("#item" + indexOfItem).val("").trigger('chosen:updated');
                $("#item" + indexOfItem).on('change', function(evt, params) {
                    if (!validateSelectedItem(params.selected, "item_id")) {
                        $("#item" + indexOfItem).val("").trigger('chosen:updated');
                    }
                });
            }
        },
        error: function() {
            alert('error!');
        }
    });

}

function editItemWidgetRuleset(obj, index)
{
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
        success: function(data)
        {
            if (data.status === "success") {
                $("#item_rules_container").append(data.response);
                $(".chosen-select").chosen();
                $("#item" + index).chosen();
                $("#type" + index).chosen();
                $("#item" + index).val(obj.ruleset_id).trigger('chosen:updated');

                $("#item" + index).on('change', function(evt, params) {
                    if (!validateSelectedItem(params.selected, "item_id")) {
                        $("#item" + index).val("").trigger('chosen:updated');
                    }
                });

                indexOfItem = index;
            }
        },
        error: function() {
            alert('error!');
        }
    });
}

function editItemWidgetFilter(obj, index)
{
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
        success: function(data)
        {
            if (data.status === "success") {
                $("#item_filters_container").append(data.response);
                $(".chosen-select").chosen();
                $("#itemfilter" + index).chosen();
                $("#type" + index).chosen();
                $("#itemfilter" + index).val(obj.filter_id).trigger('chosen:updated');

                $("#itemfilter" + index).on('change', function(evt, params) {
                    if (!validateSelectedItem(params.selected, "item_id")) {
                        $("#itemfilter" + index).val("").trigger('chosen:updated');
                    }
                });

                indexOfItem = index;
            }
        },
        error: function() {
            alert('error!');
        }
    });
}

function addItemFunel()
{
    var btn = $("a.btnAddItem");
    btn.removeClass("btnAddItem btn-default").addClass("btnRemoveItem btn-danger").html("Remove");
    btn.attr("onClick", "removeItem(" + indexOfItem + ", 'item_funel');");
    indexOfItem += 1;

    var form_data = {
        index: indexOfItem,
        is_ajax: 1
    };

    $.ajax({
        url: site_url + "/panel/itemFunel",
        type: 'GET',
        data: form_data,
        dataType: 'json',
        success: function(data)
        {
            if (data.status === "success") {
                $("#item_funel_action_container").append(data.response);
                numOfItems += 1;

                $(".chosen-select").chosen();
                $("#action" + indexOfItem).chosen();

                $("#item_funel" + indexOfItem).on('change', function(evt, params) {
                    if (!validateSelectedItem(params.selected, "action_id")) {
                        $("#action" + indexOfItem).val("").trigger('chosen:updated');
                    }
                });
            }
        },
        error: function() {
            alert('error!');
        }
    });
}

/**
 * Add Item Filter
 */
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
        success: function(data)
        {
            if (data.status === "success") {
                $("#filter_item_container").append(data.response);
                numOfItems += 1;

                $(".chosen-select").chosen();
                $("#action" + indexOfItem).chosen();

                $("#item_funel" + indexOfItem).on('change', function(evt, params) {
                    if (!validateSelectedItem(params.selected, "action_id")) {
                        $("#action" + indexOfItem).val("").trigger('chosen:updated');
                    }
                });
            }
        },
        error: function() {
            alert('error!');
        }
    });
}

/**
 * Edit Item Filter
 */
function editItemFilter(obj, index)
{
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
        success: function(data)
        {
            if (data.status === "success") {
                $("#filter_item_container").append(data.response);
                $(".chosen-select").chosen();
                $("#action" + index).chosen();
                $("#type" + index).chosen();
                $("#item" + index).val(obj.ruleset_id).trigger('chosen:updated');

                $("#item_funel" + index).on('change', function(evt, params) {
                    if (!validateSelectedItem(params.selected, "action_id")) {
                        $("#action" + index).val("").trigger('chosen:updated');
                    }
                });

                indexOfItem = index;
            }
        },
        error: function() {
            alert('error!');
        }
    });
}

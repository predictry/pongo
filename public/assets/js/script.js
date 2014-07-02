var numOfItems = 1;
var indexOfItem = 1;
var excludeOptionIDs = new Array();
var indexes = new Array();
indexes.push(1);

/**
 * Add Item Filter
 */
function getGraphData() {
    loadBar.showPleaseWait();
    $.ajax({
        url: site_url + "/panel/ajaxGraphComparison",
        type: 'POST',
        data: filters,
        dataType: 'json',
        success: function(data)
        {
            if (data.status === "success") {
                console.log(data);

                highchart_categories_data = data.response.highchart_categories;
                highchart_combination_graph_of_comparison = data.response.highchart_options_series;
                if (typeof highchart_combination_graph_of_comparison !== 'undefined') {
                    y_title = data.response.y_title;
                    getGraphBarComparison("highChartComparisonGraph", highchart_combination_graph_of_comparison);
                }

                if (filters.comparison_type === "sales")
                {
                    getGraphPie("comparisonDonut", "Total Regular Sales VS Sales \nfrom Recommended Items", data.response.highchart_pie_data);
                    getGraphPie("comparisonDonut2", "Total Regular Page Views VS Page Views \nfrom Recommended Items", data.response.highchart_other_pie_data);
                } else {
                    getGraphPie("comparisonDonut2", "Total Regular Sales VS Sales \nfrom Recommended Items", data.response.highchart_pie_data);
                    getGraphPie("comparisonDonut", "Total Regular Page Views VS Page Views \nfrom Recommended Items", data.response.highchart_other_pie_data);
                }

                var average_cart_sales_and_qty = data.response.average_cart_sales_and_qty;
                if (average_cart_sales_and_qty.average_regular_qty_items > 0)
                    var percentage_of_qty = parseFloat((average_cart_sales_and_qty.average_recommended_qty_items));
                else
                    var percentage_of_qty = parseFloat(0);

                if (average_cart_sales_and_qty.average_regular_sub_totals > 0)
                    var percentage_of_sub_totals = parseFloat((average_cart_sales_and_qty.average_recommended_sub_totals));
                else
                    var percentage_of_sub_totals = parseFloat(0);

                var whole = Math.floor(percentage_of_qty);
                console.log(percentage_of_qty);
                console.log(whole);
                console.log(percentage_of_qty - whole);

                if (percentage_of_qty - whole > 0)
                    $("div#itemInCartStat").html("<span class='text-float2 percentageOfAverageRecommendationQty'>" + percentage_of_qty.toFixed(2) + "</span>");
                else
                    $("div#itemInCartStat").html("<span class='text-float percentageOfAverageRecommendationQty'>" + percentage_of_qty + "</span>");

                whole = Math.floor(percentage_of_sub_totals);
                if (percentage_of_sub_totals - whole > 0)
                    $("div#itemSalesInCartStat").html("<span class='text-float2 percentageOfAverageRecommendationSales'>" + percentage_of_sub_totals.toFixed(2) + "</span>");
                else
                    $("div#itemSalesInCartStat").html("<span class='text-float percentageOfAverageRecommendationSales'>" + percentage_of_sub_totals + "</span>");

                $(".percentageOfAverageRecommendationQty").html(percentage_of_qty.toFixed(2));
                $(".percentageOfAverageRecommendationSales").html(percentage_of_sub_totals.toFixed(2));

                $("#qtySummaryInfo").text(average_cart_sales_and_qty.average_recommended_qty_items + " out of " + average_cart_sales_and_qty.total_combination_of_qty + " items in the carts from recommended items");
                $("#salesSummaryInfo").text("RM " + average_cart_sales_and_qty.average_recommended_sub_totals + " out of RM " + average_cart_sales_and_qty.total_combination_of_sub_totals + " with recommended items");

            } else {
                alert("something wrong");
            }
        },
        error: function() {
            alert('error!');
        }
    });
    return;
}

/**
 *  Load Bar Graph of Comparison
 *  
 * @param {type} divelem
 * @param {type} series_data
 * @returns {undefined}
 */
function getGraphBarComparison(divelem, series_data) {
    $("#" + divelem).highcharts({
        title: {
            text: "",
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
                enabled: false,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            },
            title: {
                text: y_title
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
                        this.series.name + ': ' + this.y;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black, 0 0 3px black'
                    }
                }
            }
        },
        series: series_data,
        credits: {
            enabled: false
        }
    });
    loadBar.hidePleaseWait();
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
        series: highchart_series_data,
        credits: {
            enabled: false
        }
    });
}

/**
 * get pie chart
 */
function getGraphPie(divelem, chart_title, chart_data) {
    $(function() {
        $('#' + divelem).highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: chart_title,
                style: 'font-size:18px;'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    size: '60%',
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b class="text-center">{point.name}</b><br/>{point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: [{
                    type: 'pie',
                    name: 'Total',
                    data: chart_data
                }],
            credits: {
                enabled: false
            },
            colors: ["#0077CC", "#FF9900"]
        });
    });
}

/**
 * get pie chart
 */
function getGraphPieWithText(divelem, chart_title, chart_data) {
    $('#' + divelem).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: '',
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
                showInLegend: false
            }
        },
        series: [{
                type: 'pie',
                name: 'Total',
                data: chart_data
            }],
        credits: {
            enabled: false
        }
    },
    function(chart) { // on complete
        var textX = chart.plotLeft + (chart.plotWidth * 0.5);
        var textY = chart.plotTop + (chart.plotHeight * 0.5);

        var span = '<span id="pieChartInfoText" style="position:absolute; text-align:center;">';
        span += '<span style="font-size: 42px; color: #000;">' + ctr_of_recommendation + '%</span>';
        span += '</span>';

        $("#addText").append(span);
        span = $('#pieChartInfoText');
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

/**
 * 
 * @param {string} comparison_type
 * @returns {void}
 */
function setDefaultComparisonType(comparison_type) {
    filters.comparison_type = comparison_type;
    getGraphData();
    return;
}

var loadBar;
loadBar = loadBar || (function() {
    var pleaseWaitDiv = $('<div class="modal fade" style="margin-top: 21%; overflow: hidden; z-index: 1060;" id="loadingModal">\n\
<div class="modal-dialog"><div class="col-sm-offset-4 col-sm-4"><div class="progress progress-striped active"><div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"><span class="sr-only"></span></div></div></div></div></div>');
    return {
        showPleaseWait: function() {
            pleaseWaitDiv.modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-backdrop").first().css("z-index", "1050");
        },
        hidePleaseWait: function() {
            pleaseWaitDiv.modal('hide');
            $(".modal-backdrop").first().css("z-index", "1040");

        }
    };
})();

jQuery(document).ready(function() {
    $(".alert").alert();
    $('.dropdown-toggle').dropdown();
    $('.tt').tooltip();

    //MORRIS GRAPH
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

//    if (typeof donut_average_recommended_items_data !== 'undefined')
//    {
//        new Morris.Donut({
//            element: 'averageRecommendedItemsDonut',
//            data: donut_average_recommended_items_data,
//            colors: ["#005dff", "#afafaf"]
//        }).select(0);
//    }


    //HIGHCHART GRAPH
    if (typeof highchart_pie_data !== 'undefined') {
        getGraphPie("comparisonDonut", "Total Regular Sales VS Sales from Recommended Items", highchart_pie_data);
        getGraphPie("comparisonDonut2", "Total Regular Page Views VS Page Views from Recommended Items", highchart_pageview_pie_data);
    }
    if (typeof highchart_ctr_data !== 'undefined')
        getGraphPieWithText("ctrDonut", "Click Through Rate (CTR) on Recommendations", highchart_ctr_data);
    if (typeof highchart_average_recommended_items_data !== 'undefined')
        getGraphPie("averageRecommendedItemsDonut", "Average Cart Items with Recommended Items", highchart_average_recommended_items_data);
    if (typeof highchart_average_recommended_sales_data !== 'undefined')
        getGraphPie("averageRecommendedSalesDonut", "Average Cart Value with Recommended Items", highchart_average_recommended_sales_data);
    if (typeof highchart_series_data !== 'undefined') {
        getGraphBarComparison("highChartComparisonGraph", highchart_combination_graph_of_comparison);
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

    //ACTION BINDING
    $('div#range-type ul.dropdown-menu li a').click(function(e) {
        var $div = $(this).parent().parent().parent();
        var $btn = $div.find('.dropdown-toggle');
        $btn.html($(this).text() + ' <span class="caret"></span>');
        $div.removeClass('open');
        e.preventDefault();
        return false;
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

    $('div#type_options.btn-group .btn').click(function() {
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

    $('#reportrange').daterangepicker({
        ranges: {
            '31 Days Ago': [moment().subtract('days', 31), moment()],
            '36 Weeks Ago': [moment().subtract('weeks', 36), moment()],
            '12 Months Ago': [moment().subtract('months', 12), moment()]
        },
        startDate: moment().subtract('days', 31),
        endDate: moment(),
        maxDate: moment(),
        applyClass: 'btnApplyRange btn btn-primary btn-sm',
        cancelClass: 'btnCancelRange btn btn-default btn-sm pull-right'
    },
    function(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    });
    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        console.log(picker.startDate.format('YYYY-MM-DD'));
        console.log(picker.endDate.format('YYYY-MM-DD'));
//        console.log(site_url + "/home2/" + selected_comparison + "/range/" + type_by + "/" + picker.startDate.format('YYYY-MM-DD') + "/" + picker.endDate.format('YYYY-MM-DD'));
//        window.location = site_url + "/home2/" + selected_comparison + "/range/" + type_by + "/" + picker.startDate.format('YYYY-MM-DD') + "/" + picker.endDate.format('YYYY-MM-DD');

    });

    $('#reportrange2').daterangepicker({
        ranges: {
            '31 Days Ago': [moment().subtract('days', 31), moment()],
            '36 Weeks Ago': [moment().subtract('weeks', 36), moment()],
            '12 Months Ago': [moment().subtract('months', 12), moment()]
        },
        startDate: moment().subtract('days', 31),
        endDate: moment(),
        maxDate: moment(),
        applyClass: 'btnApplyRange btn btn-primary btn-sm',
        cancelClass: 'btnCancelRange btn btn-default btn-sm pull-right'
    },
    function(start, end) {
        filters.dt_start = start.format('YYYY-MM-DD');
        filters.dt_end = end.format('YYYY-MM-DD');
        $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    });
    $('#reportrange2').on('apply.daterangepicker', function(ev, picker) {
        filters.dt_start = picker.startDate.format('YYYY-MM-DD');
        filters.dt_end = picker.endDate.format('YYYY-MM-DD');
        getGraphData();
//        console.log(picker.startDate.format('YYYY-MM-DD'));
//        console.log(picker.endDate.format('YYYY-MM-DD'));
    });

    $("#date_unit").bind('change', function(e) {
        var valueSelected = this.value;
        filters.date_unit = valueSelected;
        getGraphData();
    });
});

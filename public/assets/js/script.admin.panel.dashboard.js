$(document).ready(function () {
    
    var elem = document.querySelector('.js-switch');
    console.log(elem);
    
    var switchery = new Switchery(elem, {color: '#1AB394'});

    var date_filter = {};
    var chosenLabel = "today";

    $('#reportrange').daterangepicker({
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract('days', 1), moment()],
            '7 Days Ago': [moment().subtract('days', 7), moment()],
            '31 Days Ago': [moment().subtract('days', 31), moment()]
        },
        startDate: moment(),
        endDate: moment(),
        minDate: moment().subtract(32, 'days'),
        maxDate: moment(),
        dateLimit: {month: 1},
        applyClass: 'btnApplyRange btn btn-primary btn-sm',
        cancelClass: 'btnCancelRange btn btn-default btn-sm pull-right'
    }, function (start, end) {
        date_filter.dt_start = start.format('YYYY-MM-DD');
        date_filter.dt_end = end.format('YYYY-MM-DD');
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    });

    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
        date_filter.dt_start = picker.startDate.format('YYYY-MM-DD');
        date_filter.dt_end = picker.endDate.format('YYYY-MM-DD');
        chosenLabel = picker.chosenLabel.toLowerCase().replace(/ /g, "_");

        var $collapse_in = $(".collapse.in");
        var elem = $("#accorSiteOverview").find($collapse_in);
        elem.collapse('hide');
        elem.collapse('show');
    });

    $('#accorSiteOverview').on('shown.bs.collapse', function () {
        var $collapse_in = $(".collapse.in");
        var elem = $("#accorSiteOverview").find($collapse_in);
        if (typeof elem !== 'undefined' && elem.length > 0) {
            var site = elem.data("site");
            if (site !== "" && (typeof site_url !== 'undefined')) {
                var $panel_body = $(".panel-body");
                var elem_panel_body = elem.find($panel_body);

                if (typeof elem_panel_body !== 'undefined') {
                    var date_filter_type = "today";
                    elem_panel_body.html('<p class="text-center mt10"><i class="fa fa-2x fa-refresh fa-spin"></i></p>');

                    var url = site_url + "/panel/ajaxSiteOverviewSummary/";
                    if (chosenLabel !== "custom_range") {
                        url += chosenLabel;
                    }
                    else if (chosenLabel === "custom_range" && (date_filter.dt_start !== "") && (date_filter.dt_end !== ""))
                        url += chosenLabel + "/" + date_filter.dt_start + "/" + date_filter.dt_end;
                    else
                        url += date_filter_type;


                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            tenant: site
                        },
                        dataType: 'json',
                        success: function (response)
                        {
                            if (!response.error) {
                                data = response.data;
                                elem_panel_body.html(data);
                            }
                        },
                        error: function () {
                            alert('error!');
                        }
                    });
                }

            }
        }
    });
});
jQuery(document).ready(function () {

    var date_filter = {};

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
        loadBar.showPleaseWait();
        date_filter.dt_start = picker.startDate.format('YYYY-MM-DD');
        date_filter.dt_end = picker.endDate.format('YYYY-MM-DD');
        console.log(picker);

        var chosenLabel = picker.chosenLabel.toLowerCase().replace(/ /g, "_");
        if (chosenLabel !== "custom_range")
            window.location = site_url + '/v2/home/' + chosenLabel;
        else
            window.location = site_url + '/v2/home/' + chosenLabel + "/" + picker.startDate.format('YYYY-MM-DD') + "/" + picker.endDate.format('YYYY-MM-DD');
    });


});
var loadBar;
loadBar = loadBar || (function () {
    var pleaseWaitDiv = $('<div class="modal fade" style="margin-top: 21%; overflow: hidden; z-index: 1060;" id="loadingModal">\n\
<div class="modal-dialog"><div class="col-sm-offset-4 col-sm-4"><div class="progress progress-striped active"><div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"><span class="sr-only"></span></div></div></div></div></div>');
    return {
        showPleaseWait: function () {
            pleaseWaitDiv.modal({
                backdrop: 'static',
                keyboard: false
            });
            $(".modal-backdrop").first().css("z-index", "1050");
        },
        hidePleaseWait: function () {
            pleaseWaitDiv.modal('hide');
            $(".modal-backdrop").first().css("z-index", "1040");

        }
    };
})();
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
        minDate: moment().subtract(220, 'days'),
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

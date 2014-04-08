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
});


jQuery(document).ready(function() {
    $(".alert").alert();
    $('.dropdown-toggle').dropdown();

    new Morris.Line({
        // ID of the element in which to draw the chart.
        element: 'myfirstchart',
        // Chart data records -- each entry in this array corresponds to a point on
        // the chart.
        data: graph_data,
        // The name of the data record attribute that contains x-values.
        xkey: graph_x_keys,
        // A list of names of data record attributes that contain y-values.
        ykeys: graph_y_keys,
        // Labels for the ykeys -- will be displayed when you hover over the
        // chart.
        labels: graph_y_keys,
        xLabels: graph_x_keys
    });

});


d3.json('/data/fake_users1.json', function(data) {
    data = MG.convert.date(data, 'date');
    MG.data_graphic({
        title: "",
        description: "This is a simple line chart. You can remove the area portion by adding area: false to the arguments list.",
        data: data,
        width: 600,
        height: 200,
        right: 40,
        target: document.getElementById('visualOne'),
        x_accessor: 'date',
        y_accessor: 'value'
    });
});

d3.json('/data/fake_users1.json', function(data) {
    data = MG.convert.date(data, 'date');
    MG.data_graphic({
        title: "",
        description: "This is a simple line chart. You can remove the area portion by adding area: false to the arguments list.",
        data: data,
        width: 600,
        height: 200,
        right: 40,
        target: document.getElementById('visualTwo'),
        x_accessor: 'date',
        y_accessor: 'value'
    });
});

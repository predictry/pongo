var loadBar;
loadBar = loadBar || (function () {
    var pleaseWaitDiv = $('<div class="modal fade" style="margin-top: 21%; overflow: hidden; z-index: 1060;" id="loadingModal">\n\<div class="modal-dialog"><div class="col-sm-offset-4 col-sm-4"><div class="progress progress-striped active"><div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"><span class="sr-only"></span></div></div></div></div></div>');
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


    function gatherSelectedOption(name) {
        var values = [];
        $("select[name='" + name + "[]'] option:selected").each(function () {
            values.push(this.value);
        });
        return values;
    }


    function validateSelectedItem(selected, name) {
        excludeOptionIDs = gatherSelectedOption(name);
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
});

function removeItem(id, name) {
    $("#" + name + id).remove();
    var index = indexes.indexOf(id);

    if (index > -1)
        indexes.splice(index, 1);
}


function testCallback(response) {
    var data = response.data;

    var $recommended_items = $("#recommended_items");

    for (i = 0; i < data.items.length; i++) {
        console.log(data.items[i]);

        var div = '<div class="col-sm-3"><div class="panel product">';
        div += '<img src="' + data.items[i].img_url + '" class="img-rounded img-responsive"/>';
        div += '<h4 class="mt10" style="padding-left: 10px; padding-right: 10px;">' + data.items[i].name + '</h4>';
        div += '<h5 style="padding-left: 10px; padding-right: 10px;">Price: ' + data.items[i].price + '</h5>';
        div += '<a href="' + site_url + '/demo/show/' + data.items[i].id + '"' + 'class="btn btn-primary btn-block text-capitalize btn-sm">Show me recommendation</a>';
        div += '</div></div>';

        $recommended_items.append(div);
    }
}
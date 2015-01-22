jQuery(document).ready(function () {
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



var selected_action_name = '';
var action = [];
var excluded_properties = {};

jQuery(document).ready(function () {

    $('[id^=detail-]').hide();
    $('.toggle').click(function () {
        $input = $(this);
        $target = $('#' + $input.attr('data-toggle'));
        $target.slideToggle();
    });
    $('[data-toggle="tooltip"]').tooltip();

    var navListItems = $('ul.setup-panel li a'),
            allWells = $('.setup-content');

    allWells.hide();

    navListItems.click(function (e)
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

    // Btn Embed Continue
    $('#btn-embed-continue').on('click', function (e) {
        $('ul.setup-panel li:eq(1)').removeClass('disabled');
        $('ul.setup-panel li a[href="#step-data-collection"]').trigger('click');
        $(this).remove();
    });

    // Btn Data Collection Continue
//    $('#btn-data-collection-continue').on('click', function (e) {
//        $('ul.setup-panel li:eq(1)').removeClass('disabled');
//        $('ul.setup-panel li a[href="#data-collection"]').trigger('click');
//        $(this).remove();
//    });

    for (var i = 0; i < action_names.length; i++) {
        excluded_properties[action_names[i]] = [];
    }
});

var loadBar;
loadBar = loadBar || (function () {
    var pleaseWaitDiv = $('<div class="modal fade" style="margin-top: 0%; overflow: hidden; z-index: 1060;" id="loadingModal">\n\
<div class="modal-dialog"><div class="col-sm-offset-4 col-sm-4"><div class="progress progress-striped active"><div class="progress-bar"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%; height: 100vh;"><span class="sr-only"></span></div></div></div></div></div>');
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

function getActionProperties(tenant_name, action_name) {
    loadBar.showPleaseWait();
    selected_action_name = action_name;

    $.ajax({
        url: site_url + "/sites/" + tenant_name + "/actions/" + action_name + "/properties",
        type: 'GET',
        dataType: 'json',
        success: function (data)
        {
            if (!data.error) {
                action = data.action;
                $("#action_properties").html(data.response);
                getSnippedJSData(tenant_name, action_name, []);
            }
            loadBar.hidePleaseWait();
        },
        error: function () {
            action = [];
            alert('error!');
        }
    });
}

function getUncheckedList(nodes) {
    var excludedProperties = [];
    excluded_properties[selected_action_name] = [];

    nodes.each(function () {
        if (!$(this).is(":checked")) {
            excludedProperties.push($(this).val());
            excluded_properties[selected_action_name].push($(this).val());
        }
    });

    if (selected_action_name !== '') {
        excluded_properties[selected_action_name] = excludedProperties;
    }

    console.log(selected_action_name);
    console.log(excluded_properties);
    console.log(excluded_properties[selected_action_name]);

    return excludedProperties;
}

function getSnippedJSData(tenant_name, action_name, excludedProperties) {
    $.ajax({
        url: site_url + "/sites/" + tenant_name + "/actions/" + action_name + "/snipped",
        type: 'GET',
        data: {excluded_properties: excludedProperties},
        dataType: 'json',
        success: function (response)
        {
            if (!response.error) {
                var textarea = $("#tab_" + action_name).find("textarea");
                var str_var = "var " + action_name + "_action_data = " + response.data.snipped + ";";
                str_var += "\n_predictry.push(['track', " + action_name + "_action_data]);";
                textarea.html(str_var);
                $('a[href="#tab_' + action_name + '"]').tab('show');
            }
        },
        error: function () {
            action = [];
            alert('error!');
        }
    });
}

function checkIfActionImplemented(tenant_name, action_name) {
    loadBar.showPleaseWait();
    $.ajax({
        url: site_url + "/sites/" + tenant_name + "/actions/" + action_name + "/validate",
        type: 'GET',
        dataType: 'json',
        success: function (response)
        {
            console.log(response);
            if (!response.error) {
                var elem_item_action = $("#item-action-" + action_name);
                var elem_received = '<i class="fa fa-check-circle action-received" data-toggle="tooltip" role="tooltip" title="We have receive your ' + action_name + ' action"></i>';
                var elem_have_not_received_yet = '<i class="fa fa-minus-circle action-not-received" data-toggle="tooltip" role="tooltip" title="We have not receive any ' + action_name + ' action"></i>';
                if (elem_item_action !== undefined) {
                    var elem_status = elem_item_action.find('.status');

                    if (response.data.action_recieved) {
                        elem_status.html(elem_received);
                    } else
                        elem_status.html(elem_have_not_received_yet);
                }
            }
            $('[data-toggle="tooltip"]').tooltip();
            loadBar.hidePleaseWait();
        },
        error: function () {
            alert('error!');
        }
    });
}

function saveIntegrationConfiguration(tenant_name, api_key) {
    
  /// $.ajax({
  ///   url: site_url + "/check/config/",
  ///   type: 'POST',
  ///   data: { "t_name" : tenant_name, "t_key": api_key },
  ///   dataType: 'json',
  ///   success: function(response) {
  ///     if ( response.status == true ) {
  ///       saveItNow();
  ///     } else { 
  ///       console.log(response);
  ///     }
  ///   },
  ///   error: function(error) {
  ///     alert(error);
  ///   }
  /// });
    
  $.ajax({
        url: site_url + "/sites/" + tenant_name + "/integration/submit",
        type: 'POST',
        data: {'excluded_properties': excluded_properties, "action_names": action_names},
        dataType: 'json',
        success: function (response)
        {
            console.log(response);
            // if (!response.error) {
            //     window.location = response.data.redirect;
            // }
            // loadBar.hidePleaseWait();
        },
        error: function (error) {
          console.log(error);
        }
    }); 
}

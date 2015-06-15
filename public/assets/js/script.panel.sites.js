jQuery(document).ready(function () {
    
   
    var navListItems = $('ul.setup-panel li a'),
            allWells = $('.setup-content');

    allWells.hide();

    navListItems.click(function (e) {
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

    $('.navbar-minimalize').trigger('click');

    // CREATE SITE (FIRST TIME USER) //
    $("#btnWizardSubmitSite").on("click", function (e) {
        e.preventDefault();
        var form = $(".wizardModalSiteForm");
        $.ajax({
            url: site_url + "/sites/ajaxSubmitSite",
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function (data)
            {
                if (data.status === "success") {
                    window.location = site_url + data.response;
                } else {
                    $(".wizardModalSiteFormContainer").html(data.response);
                }
                //hide loading
            },
            error: function () {
            }
        });

        return;
    });
});

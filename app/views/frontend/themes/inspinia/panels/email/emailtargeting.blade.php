@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', [
    'scripts' => [
        HTML::script('assets/js/script.helper.js'),
        HTML::script('assets/js/data_collection.js'),
        HTML::script('assets/js/prism.js'),
        HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'),
        HTML::script('assets/js/moment.min.js'),
        HTML::script('assets/js/daterangepicker.js'),
        HTML::script('assets/js/highcharts.js'),
        HTML::script('assets/inspinia/js/plugins/chartJs/Chart.min.js'),
        HTML::script('assets/js/bootstrap-datetimepicker.min.js'),
        HTML::script('assets/js/script.helper.js'),
        HTML::script('assets/js/script.panel.filters.js'),
        HTML::script('assets/js/visual.js'),
        HTML::script('assets/js/ace/ace.js'),
        HTML::script('assets/js/ace/ext-beautify.js')
    ]
])
@section('content')
    @include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => ['Sites' => 'v2/sites']])
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="float-e-margins">
                    <div class="ibox-title"><h5>Email Targeting Details</h5></div>
                    <div class="ibox-content">

                        <form method="get" class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="font-weight: bold;">Campaign Name:</label>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="Your Campaign Name" id="campaignname"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="ibox-title">
                                    <h5>Target</h5>
                                </div>
                                <div class="ibox-content">
                                    <form class="form-horizontal">
                                        <p>Customers with ALL the following Conditions:</p>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"
                                                   style="font-weight: bold;">Purchased: </label>
                                            <div class="col-sm-6">
                                                <select class="form-control m-b" id="timeframe" name="timeframe">
                                                    <option selected="selected" style="display:none; font-size:15px;">
                                                        Timeframe
                                                    </option>
                                                    <option value="7">7 days ago</option>
                                                    <option value="14">14 days ago</option>
                                                    <option value="30">30 days ago</option>
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="font-weight: bold;">Delivery
                                    Account: </label>
                                <div class="col-sm-6">
                                    <input type="text" id="apikey" class="form-control"
                                           placeholder="Your API Mandrill Key">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="float-e-margins">
                    <div class="ibox-title"><h5>Email Composer</h5></div>
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="font-weight: bold;">From:</label>
                                <div class="col-sm-6">
                                    <input type="text" id="usersname" placeholder="Your Email" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="font-weight: bold;">Subject:</label>
                                <div class="col-sm-6">
                                    <input type="text" id="subject" placeholder="Subject" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="font-weight: bold;">REC Block: </label>
                                <div class="col-sm-6">
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle">
                                            Choose REC Block
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a id="insert_rec_4">Recommended For You ( 4 )</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#email-html">HTML</a></li>
                        <li><a data-toggle="tab" href="#email-preview">Preview</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="email-html" class="tab-pane active">
                            <div class="panel-body">
                                <div id="editor" style="width: 100%; height: 300px; margin-bottom: 10px;"></div>
                            </div>
                        </div>
                        <div id="email-preview" class="tab-pane">
                            <div class="panel-body">
                                <iframe id="email-preview-frame" style="width: 100%; height: 300px; background-color: white; border: 0px;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="pull-right">
                    <button type="button" class="btn btn-warning" id="sdraft">Save as Draft</button>
                    <button type="button" class="btn btn-primary" id="send_email">Send Email</button>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {

                var defaultMailMessage = '<!DOCTYPE html>\n<html lang="en">\n<head>\n\t<meta charset="UTF-8">\n</head>\n<body>\n\t<h1>Replace your message here!</h1>\n</body>\n</html>';
                var previewFrameHtml = $('#email-preview-frame').contents().find('html');
                var editor = ace.edit("editor");
                editor.setTheme("ace/theme/chrome");
                editor.getSession().setMode("ace/mode/html");
                editor.setValue(defaultMailMessage);
                editor.getSession().on('change', function() {
                    previewFrameHtml.html(editor.getSession().getValue());
                });
                previewFrameHtml.html(defaultMailMessage);

                $("#insert_rec_4").click(function() {
                    editor.insert(
                        '<div id="duo4">\n' +
                        '\t<div>\n' +
                        '\t\t<h3>You Bought</h3>\n' +
                        '\t\t<div>\n' +
                        '\t\t\t<a th:href="${products[0].item_url}"><img th:src="${products[0].img_url}" src="https://placehold.it/350x150"/></a>\n' +
                        '\t\t\t<div><a th:href="${products[0].item_url}"><span th:text="${products[0].name}">Item1</span></a></div>\n' +
                        '\t\t\t<div>RM <span th:text="${products[0].price}">123</span></div>\n' +
                        '\t\t</div>\n' +
                        '\t</div>\n' +
                        '\t<div class="recommendations">\n' +
                        '\t\t<h3>Other People Also Bought</h3>\n' +
                        '\t\t<ul>\n' +
                        '\t\t\t<li th:each="rec : ${products[0].recs}">\n' +
                        '\t\t\t\t<div>\n' +
                        '\t\t\t\t\t<a th:href="${rec.item_url}"><img th:src="${rec.img_url}" src="https://placehold.it/350x150"/></a>\n' +
                        '\t\t\t\t\t<div><a th:href="${rec.item_url}"><span th:text="${rec.name}">Item2</span></a></div>\n' +
                        '\t\t\t\t\t<div>RM <span th:text="${rec.price}">123</span></div>\n' +
                        '\t\t\t\t</div>\n' +
                        '\t\t\t</li>\n' +
                        '\t\t</ul>\n' +
                        '\t</div>\n' +
                        '</div>');
                });

                $("#send_email").click(function() {
                    var data = {
                        "pongoUserId": {{ Auth::user()->id }},
                        "campaignName": document.getElementById("campaignname").value,
                        "targets": [{"action": "BUY", "day": document.getElementById("timeframe").value}],
                        "mandrillAPIKey": document.getElementById("apikey").value,
                        "emailFrom": document.getElementById("usersname").value,
                        "emailSubject": document.getElementById("subject").value,
                        "template": editor.getValue()
                    };
                    $.post("fisher.predictry.com:8090/oms", function (data) {
                        if (data.status == 'created') {
                            toastr.success('Your Email Campaign has Started', 'Success!');
                        } else {
                            toastr.warning('Your Email Campaign has failed to Start', 'Error');
                        }
                    });
                });
            });

        </script>
    </div>

@stop

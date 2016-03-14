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
    @include(getenv('FRONTEND_SKINS') . $theme . '.partials.page_heading_without_action', ['upper' => ['Email Targeting' => 'v2/email/new']])
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="float-e-margins">
                    <a class="btn btn-w-m btn-primary" href="{{route('emailHome')}}">Back</a>

                    <div class="ibox-title">
                        <h5>Email Targeting Details</h5>
                    </div>
                    <div class="ibox-content">

                        <form id="entry-form" name="entry-form" method="post" action="{{ URL::action('emailSave') }}" class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="font-weight: bold;">Campaign Name:</label>
                                <div class="col-sm-6">
                                    <input type="text" placeholder="Your Campaign Name" id="campaignname" name="campaignname"
                                           value="{{{$campaignDraft->campaignname or ''}}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="ibox-title">
                                    <h5>Target</h5>
                                </div>
                                <div class="ibox-content">
                                    <p>Customers with ALL the following Conditions:</p>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"
                                               style="font-weight: bold;">Purchased: </label>
                                        <div class="col-sm-6">
                                            <select class="form-control m-b" id="timeframe" name="timeframe">
                                                <option {{ $campaignDraft->timeframe ? '' : "selected='selected'" }} style="display:none; font-size:15px;">
                                                    Timeframe
                                                </option>
                                                <option {{ $campaignDraft->timeframe == 7 ? "selected='selected'" : ''}} value="7">7 days ago</option>
                                                <option {{ $campaignDraft->timeframe == 14 ? "selected='selected'" : '' }} value="14">14 days ago</option>
                                                <option {{ $campaignDraft->timeframe == 30 ? "selected='selected'" : ''}} value="30">30 days ago</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>
                            <div class="form-group">
                                <div class="ibox-title">
                                    <h5>Email Composer</h5>
                                </div>
                                <label class="col-sm-2 control-label" style="font-weight: bold;">Delivery
                                    Account: </label>
                                <div class="col-sm-6">
                                    <input type="text" id="apikey" class="form-control" name="apikey" value="{{{$campaignDraft->apikey or ''}}}"
                                           placeholder="Your API Mandrill Key">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="font-weight: bold;">From:</label>
                                <div class="col-sm-6">
                                    <input type="text" id="usersname" name="usersname" placeholder="Your Email" value="{{{$campaignDraft->usersname or ''}}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="font-weight: bold;">Subject:</label>
                                <div class="col-sm-6">
                                    <input type="text" id="subject" name="subject" placeholder="Subject" value="{{{$campaignDraft->subject or ''}}}" class="form-control">
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
                                <div id="step_one" class="modular_step col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                    <h5 style="font-size:15px;"> Just a tip to customize your own email: </h5>
                                <p>In order to customize your own message, replace the <code>&lt;h1&gt;</code>Replace your message here!<code>&lt;/h1&gt;</code> with anything you want,<br> DO NOT REMOVE THE <code>&lt;h1&gt;&lt;/h1&gt;</code> though.<br> After you have replaced the message with your message, click the part below <code>&lt;/html&gt;</code> and insert your rec block there.</p>
                                </div>
                            </div>
                            <input type="hidden" name="template" id="template" />
                            <input type="hidden" name="id" id="id" value="{{$campaignDraft->id}}" />
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
                                <div id="editor" style="width: 100%; height: 300px; margin-bottom: 10px;">{{{$campaignDraft->template or ''}}}</div>
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
                var defaultHtml = "";
                defaultHtml += "<!DOCTYPE html>\n";
                defaultHtml += "<html lang=\"en\">\n";
                defaultHtml += " <head>\n";
                defaultHtml += " 	<meta charset=\"UTF-8\" />\n";
                defaultHtml += "	<link rel=\"stylesheet\" href=\"https:\/\/maxcdn.bootstrapcdn.com\/bootstrap\/3.3.6\/css\/bootstrap.min.css\" integrity=\"sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7\" crossorigin=\"anonymous\" />\n";;
                defaultHtml += " <\/head>\n";
                defaultHtml += " <body>\n";
                defaultHtml += "	<h1>Replace this your template<\/h1>\n";
                defaultHtml += " <\/body>\n";
                defaultHtml += "<\/html>\n";

                var previewFrameHtml = $('#email-preview-frame').contents().find('html');
                var isEmpty = ($("#editor").html() == '');
                var editor = ace.edit("editor");
                editor.setTheme("ace/theme/chrome");
                editor.getSession().setMode("ace/mode/html");
                editor.getSession().on('change', function() {
                    previewFrameHtml.html(editor.getSession().getValue());
                });
                if (isEmpty) {
                    editor.setValue(defaultHtml);
                }
                previewFrameHtml.html(editor.getSession().getValue());

                $("#insert_rec_4").click(function() {
                    var defaultTemplate="";
                    defaultTemplate += "	<div class=\"row\">\n";
                    defaultTemplate += " 		<div class=\"product col-md-3 text-center\" style=\"background-color: #eee;\">\n";
                    defaultTemplate += " 			<h4>You Bought<\/h4>\n";
                    defaultTemplate += " 			<a th:href=\"${products[0].item_url}\">\n";
                    defaultTemplate += "				<img src=\"https:\/\/placehold.it\/200x200\" th:src=\"${products[0].img_url}\" class=\"img-responsive center-block\" \/>\n";
                    defaultTemplate += "			<\/a>\n";
                    defaultTemplate += " 			<a th:href=\"${products[0].item_url}\">\n";
                    defaultTemplate += "				<div th:text=\"${products[0].name}\">Item name<\/div>\n";
                    defaultTemplate += "			<\/a>\n";
                    defaultTemplate += " 			RM <span th:text=\"${products[0].price}\">99.99<\/span>\n";
                    defaultTemplate += " 		<\/div>\n";
                    defaultTemplate += "		<div class=\"recommendations col-md-9\">\n";
                    defaultTemplate += "			<h4>Other People Also Bought<\/h4>\n";
                    defaultTemplate += "			<div class=\"row\">\n";
                    defaultTemplate += "				<div class=\"col-md-3 text-center\" th:each=\"rec,i: ${products[0].recs}\" th:if=\"${i.index} lt 3\">\n";
                    defaultTemplate += "					<a th:href=\"${rec.item_url}\">\n";
                    defaultTemplate += "						<img src=\"https:\/\/placehold.it\/200x200\" th:src=\"${rec.img_url}\" class=\"img-responsive center -block\" \/>\n";
                    defaultTemplate += "					<\/a>\n";
                    defaultTemplate += "					<a th:href=\"${rec.item_url}\">\n";
                    defaultTemplate += "						<div th:text=\"${rec.name}\">Recommended Item Name<\/div>\n";
                    defaultTemplate += "					<\/a>\n";
                    defaultTemplate += "					RM <span th:text=\"${rec.price}\">99.99<\/span>\n";
                    defaultTemplate += "				<\/div>\n";
                    defaultTemplate += "			<\/div>\n";
                    defaultTemplate += "		<\/div>\n";
                    defaultTemplate += " 	<\/div>\n";
                    editor.insert(defaultTemplate);
                });

                $("#send_email").click(function() {
                    $("#entry-form").attr("action", "{{ URL::action('emailSave') }}");
                    $("#template").val(editor.getValue());
                    $("#entry-form").submit();
                });

                $("#sdraft").click(function(){
                    $("#entry-form").attr("action", "{{ URL::action('dataHandling') }}");
                    $("#template").val(editor.getValue());
                    $("#entry-form").submit();
                });
            });

        </script>
    </div>

@stop

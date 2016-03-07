@extends(getenv('FRONTEND_SKINS') . $theme . '.layouts.dashboard', ['scripts' => array(
HTML::script('assets/js/script.helper.js'),
HTML::script('assets/js/data_collection.js'),
HTML::script('assets/js/prism.js')),
HTML::script('assets/js/chosen-1.1.0/chosen.jquery.min.js'),
HTML::script('assets/js/moment.min.js'),
HTML::script('assets/js/daterangepicker.js'),
HTML::script('assets/js/highcharts.js'),
HTML::script('assets/inspinia/js/plugins/chartJs/Chart.min.js'),
HTML::script('assets/js/bootstrap-datetimepicker.min.js'),
HTML::script('assets/js/script.helper.js'),
HTML::script('assets/js/script.panel.filters.js'),
HTML::script('assets/js/visual.js'),
HTML::script('http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js')])
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
                                                <a onclick="rBlocks()">Recommended For You ( 4 )</a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a onclick="rBlocks()">Recommended For You ( 8 )</a>
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
    <div id="summernote"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <button type="button" class="btn btn-warning" id="sdraft">Save as Draft</button>
                <button type="button" class="btn btn-primary" id="semail" onclick="sendEmail()">Send Email</button>
            </div>
        </div>
    </div>
    <script>

        function rBlocks() {
            var node = document.createElement('div');
            var t = document.createTextNode("Block of 8");
            node.appendChild(t)
            node.style.cssText = "width:100%;height:100px;background:grey;color: #FFF;text-align: center; margin-top: 20px; margin-left: 5px;line-height: 90px;";


            $('#summernote').summernote('insertNode', node);

        }

        function sendEmail(){
            var data = {
                "pongoUserId": {{ Auth::user()->id }},
                "campaignName": document.getElementById("campaignname").value,
                "targets": [{"action": "BUY", "day": document.getElementById("timeframe").value}],
                "mandrillAPIKey": document.getElementById("apikey").value,
                "emailFrom": document.getElementById("usersname").value,
                "emailSubject": document.getElementById("subject").value,
                "template": $('#summernote').summernote('code')

            };

            $.post( "fisher.predictry.com:8090/oms", function( data ) {
                if(data.status == 'created'){
                    toastr.success('Your Email Campaign has Started','Success!');
                }else{
                    toastr.warning('Your Email Campaign has failed to Start','Error');
                }
            });
            console.log("data is" + JSON.stringify(data));
        }
        document.onreadystatechange = function () {
            $('#summernote').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['mybutton', ['recblocks']]
                ]
            });
        }
    </script>
    </div>

@stop

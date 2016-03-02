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
    <div class="wrapper wrapper-content">
        <form class="form-horizontal">
            <div class="form-group">
            <label style="font-size:20px; font-weight: bold; padding-bottom: 20px; margin-right: 5px;">Campaign Name:</label>
            <input type="text" id="campaignname" style="display:none;" onblur="doneEdit('campaignname', 'ed1')">
                <span onclick="editText('campaignname', 'ed1')" id="ed1" style="font-size:15px;">Click to Edit</span>
        <p><label style="font-size: 20px; font-weight: bold; padding-bottom: 20px;">Target</label></p>
        <label style="font-size: 15px; padding-bottom: 10px;">Customers with ALL the following Conditions: </label>
        <table>
            <tr>
                <td>
                        <select name="type" id ="ptype" style="margin-right: 10px">
                            <option value="1">Purchased</option>
                            <option value="2">Viewed</option>
                        </select>
                </td>
                <td>
                        <select name="type" id="timeframe">
                            <option value="1">7 days ago</option>
                            <option value="2">14 days ago</option>
                            <option value="3">30 days ago</option>
                            <option value="4">Since the Start</option>
                        </select><br>
                </td>
            </tr>
        </table><br>
            <label style="font-size:20px; padding-bottom:20px; font-weight: bold; margin-right: 5px;">Delivery Account:</label>
            <input type="text" id="apikey" style="display: none;" onblur="doneEdit('apikey', 'ed2')">
                <span onclick="editText('apikey', 'ed2')" id="ed2" style="font-size:15px;">Your API Mandrill Key</span>
              <p><label style="font-size: 20px; font-weight:bold; padding-bottom: 10px;">Email</label></p>
                <label class="col-lg-1 control-label" style="text-align:left; font-size:15px;">From:</label>
                <div class="col-lg-3"><input type="text" id="usersname" placeholder="Your Name" class="form-control"></div>
                <label class="col-lg-1 control-label" style="text-align:left; font-size:15px;">Subject:</label>
                <div class="col-lg-3"><input type="text" id="subject" placeholder="Subject" class="form-control"></div>
            </div>
        <div id="summernote"></div>
        <script>
            function editText(text, label) {

                var text1 = document.getElementById(text);
                var label1 = document.getElementById(label);
                text1.style.display = "inline";
                label1.style.display = "none";
                text1.value = label1.innerText;
                text1.focus();
            }

            function doneEdit(text, label) {
                var text1 = document.getElementById(text);
                var label1 = document.getElementById(label);
                text1.style.display = "none";
                label1.style.display = "inline";
                label1.innerText = text1.value;
            }
            var markupStr = $('#summernote').summernote('code')
            document.onreadystatechange=function(){
                $('#summernote').summernote({
                    toolbar: [
// [groupName, [list of button]]
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
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-right">
                        <button type="button" class="btn btn-warning" id="sdraft">Save as Draft</button>
                        <button type="button" class="btn btn-primary" id="semail">Send Email</button>
                    </div>
                </div>
            </div>

        </form>
    </div>

@stop

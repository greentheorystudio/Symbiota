<link href="../../css/external/bootstrap.min.css?ver=20220225" rel="stylesheet" />
<link href="../../css/external/jquery.fileupload.css" rel="stylesheet" type="text/css" />
<link href="../../css/external/jquery.fileupload-ui.css" rel="stylesheet" type="text/css" />
<style>
    #fileDropZone {
        border: 3px solid black;
        border-radius: 25px;
        height: 100px;
        width: 90%;
        margin: 0 auto 20px;
        background: repeating-linear-gradient(
                -45deg,
                white 0 20px,
                #a9a9a9 20px 40px
        );
        display: flex;
        align-items: center;
        align-content: center;
    }

    #dropZoneLabel {
        border: 10px solid #a9a9a9;
        background-color: white;
        margin: 0 auto;
        padding: 10px;
        font-size: 18px;
        font-weight: bold;
        color: #a9a9a9;
    }
</style>
<script src="../../js/external/jquery.ui.widget.js"></script>
<script src="../../js/external/tmpl.min.js"></script>
<script src="../../js/external/load-image.all.min.js"></script>
<script src="../../js/external/jquery.fileupload.js"></script>
<script src="../../js/external/jquery.fileupload-process.js"></script>
<script src="../../js/external/jquery.fileupload-image.js"></script>
<script src="../../js/external/jquery.fileupload-audio.js"></script>
<script src="../../js/external/jquery.fileupload-video.js"></script>
<script src="../../js/external/jquery.fileupload-validate.js"></script>
<script src="../../js/external/jquery.fileupload-ui.js"></script>
<script src="../../js/taxa.batchmedialoader.js?ver=20221025"></script>
<script id="template-upload" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-upload fade{%=o.options.loadImageFileTypes.test(file.type)?' image':''%}">
                <td>
                    <span class="preview"></span>
                </td>
                <td>
                    <div class="name">{%=file.name%}</div>
                    <label>
                        <strong>Scientific Name:</strong><br>
                        <input name="scientificname" value="{%=file.scientificname?file.scientificname:''%}" class="form-control" style="width:250px;" onchange="validateSciNameChange(this.value,'{%=file.name%}');">
                    </label>
                    <div>
                        <strong class="goodUploadMessage" style="color:green;{%=file.errorMessage?'display:none;':'display:block;'%}">Linked to the thesaurus and ready to upload</strong>
                        <strong class="errorUploadMessage" style="color:red;{%=file.errorMessage?'display:block;':'display:none;'%}width:350px;">Not linked to the thesaurus</strong>
                    </div>
                    <div>
                        <strong class="linkedDataMessage" style="display:none;">Additional data: </strong><span class="linkedDataDisplay"></span>
                    </div>
                    <input type="hidden" name="tid" value="{%=file.tid?file.tid:''%}">
                    <input type="hidden" name="photographer" value="{%=file.photographer?file.photographer:''%}">
                    <input type="hidden" name="caption" value="{%=file.caption?file.caption:''%}">
                    <input type="hidden" name="owner" value="{%=file.owner?file.owner:''%}">
                    <input type="hidden" name="sourceurl" value="{%=file.sourceurl?file.sourceurl:''%}">
                    <input type="hidden" name="copyright" value="{%=file.copyright?file.copyright:''%}">
                    <input type="hidden" name="locality" value="{%=file.locality?file.locality:''%}">
                    <input type="hidden" name="notes" value="{%=file.notes?file.notes:''%}">
                </td>
                <td>
                    <p class="size">Processing...</p>
                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
                </td>
                <td style="display:flex;justify-content:flex-end;align-items:center;align-content:center;gap:5px;">
                    {% if (!i) { %}
                        <button name="startButton" class="btn btn-primary start {%=file.tid?'':'disabled'%}">
                            <i style="height:15px;width:15px;" class="fas fa-upload"></i>
                            <span>Start</span>
                        </button>
                    {% } %}
                    {% if (!i) { %}
                        <button name="cancelButton" class="btn btn-warning cancel">
                            <i style="height:15px;width:15px;" class="fas fa-ban"></i>
                            <span>Cancel</span>
                        </button>
                    {% } %}
                </td>
            </tr>
        {% } %}
    </script>
<script type="text/javascript">
    let fileData = [];
    let taxaNameArr = [];
    let taxaDataArr = {};

    window.addEventListener("dragover",function(e){
        e.preventDefault();
    },false);
    window.addEventListener("drop",function(e){
        e.preventDefault();
    },false);

    $(function () {
        $('#fileupload').fileupload({
            url: '../../api/taxa/uploadimage.php',
            dropZone: $('#fileDropZone'),
            paramName: 'imgfile',
            filesContainer: '#uploadList',
            downloadTemplateId: '',
            add: function (e, data) {
                const fileName = data.files[0].name;
                const fileType = fileName.split('.').pop().toLowerCase();
                if(fileType === 'csv'){
                    processCsvFile(e, data.files[0]);
                    data.files.splice(data, 1);
                }
                else if(fileType === 'jpeg' || fileType === 'jpg' || fileType === 'png'){
                    let imageFileData = fileData.find((obj) => obj.filename.toLowerCase() === fileName.toLowerCase());
                    if(!imageFileData){
                        imageFileData = fileData.find((obj) => obj.filename.toLowerCase() === fileName.substring(0, fileName.lastIndexOf('.')).toLowerCase());
                    }
                    if(imageFileData){
                        data.files[0].scientificname = imageFileData.scientificname;
                        if(imageFileData.hasOwnProperty('tid') && imageFileData.tid){
                            data.files[0].tid = imageFileData.tid;
                        }
                        data.files[0].photographer = imageFileData.photographer;
                        data.files[0].caption = imageFileData.caption;
                        data.files[0].owner = imageFileData.owner;
                        data.files[0].sourceurl = imageFileData.sourceurl;
                        data.files[0].copyright = imageFileData.copyright;
                        data.files[0].locality = imageFileData.locality;
                        data.files[0].notes = imageFileData.notes;
                    }
                    if(!data.files[0].hasOwnProperty('scientificname') || !data.files[0].scientificname){
                        parseScinameFromFilename(fileName);
                        data.files[0].errorMessage = 'Scientific name required';
                    }
                    else if(!data.files[0].hasOwnProperty('tid') || !data.files[0].tid){
                        if(taxaDataArr.hasOwnProperty(imageFileData.scientificname)){
                            data.files[0].tid = taxaDataArr[imageFileData.scientificname];
                        }
                        else{
                            data.files[0].tid = '';
                            data.files[0].errorMessage = 'Validating name...';
                        }
                    }
                    const initializedObj = $(this);
                    const fileuploadObj = initializedObj.data('blueimp-fileupload') || initializedObj.data('fileupload');
                    const fileuploadObjOptions = fileuploadObj.options;
                    data.context = fileuploadObj
                        ._renderUpload(data.files)
                        .data('data', data)
                        .addClass('processing');
                    fileuploadObjOptions.filesContainer[fileuploadObjOptions.prependFiles ? 'prepend' : 'append'](
                        data.context
                    );
                    fileuploadObj._forceReflow(data.context);
                    fileuploadObj._transition(data.context);
                    data
                        .process(function () {
                            return initializedObj.fileupload('process', data);
                        })
                        .always(function () {
                            data.context
                                .each(function (index) {
                                    $(this)
                                        .find('.size')
                                        .text(fileuploadObj._formatFileSize(data.files[index].size));
                                })
                                .removeClass('processing');
                            fileuploadObj._renderPreviews(data);
                        })
                        .done(function () {
                            data.context.find('.edit,.start').prop('disabled', false);
                            if (
                                fileuploadObj._trigger('added', e, data) !== false &&
                                (fileuploadObjOptions.autoUpload || data.autoUpload) &&
                                data.autoUpload !== false
                            ) {
                                data.submit();
                            }
                        })
                        .fail(function () {
                            if (data.files.error) {
                                data.context.each(function (index) {
                                    var error = data.files[index].error;
                                    if (error) {
                                        $(this).find('.error').text(error);
                                    }
                                });
                            }
                        });
                }
            },
            done: function (e, data) {
                const that = $(this).data('blueimp-fileupload') || $(this).data('fileupload');
                const getFilesFromResponse = data.getFilesFromResponse || that.options.getFilesFromResponse;
                const files = getFilesFromResponse(data);
                let template;
                let deferred;
                if(data._response.jqXHR.responseJSON.files[0]){
                    data.context.each(function () {
                        $(this)
                            .find('.progress')
                            .attr('aria-valuenow', '0')
                            .children()
                            .first()
                            .css('width', '0%');
                    });
                    const fileName = data.files[0].name;
                    const fileNodeArr = document.getElementById('uploadList').childNodes;
                    for(let n in fileNodeArr){
                        if(fileNodeArr.hasOwnProperty(n)){
                            const fileNode = fileNodeArr[n];
                            const nodeFileName = fileNode.getElementsByClassName('name')[0].innerHTML;
                            if(nodeFileName === fileName){
                                fileNode.getElementsByClassName('errorUploadMessage')[0].innerHTML = data._response.jqXHR.responseJSON.files[0].error;
                                fileNode.getElementsByClassName('errorUploadMessage')[0].style.display = 'block';
                                fileNode.getElementsByClassName('goodUploadMessage')[0].style.display = 'none';
                                fileNode.querySelectorAll('button[name="startButton"]')[0].disabled = false;
                            }
                        }
                    }
                }
                else {
                    data.context.each(function (index) {
                        const file = files[index] || { error: 'Empty file upload result' };
                        deferred = that._addFinishedDeferreds();
                        that._transition($(this)).done(function () {
                            const node = $(this);
                            template = that._renderDownload([file]).replaceAll(node);
                            that._forceReflow(template);
                            that._transition(template).done(function () {
                                data.context = $(this);
                                that._trigger('completed', e, data);
                                that._trigger('finished', e, data);
                                deferred.resolve();
                            });
                        });
                    });
                }
                if(document.getElementById('uploadList').childNodes.length === 0){
                    resetUploader();
                }
            }
        }).on('fileuploadsubmit', function (e, data) {
            if(data.context.find(':input[name="tid"]')[0].value){
                data.formData = data.context.find(':input').serializeArray();
            }
            else{
                return false;
            }
        });
    });
</script>
<div>
    <div style="margin-bottom:20px;font-size:16px;">
        To batch upload taxa images, either click the Add files button to select the files to be uploaded or drag and
        drop the files onto the box below. A csv spreadheet can also be uploaded to provide further metadata for the files.
        <a href="../../templates/batchTaxaImageData.csv"><b>Use this template for the csv spreadsheet.</b></a> For each
        row in the spreadsheet, the value in the filename column must match the filename of the associated file being uploaded.
    </div>
    <form id="fileupload" method="POST" enctype="multipart/form-data">
        <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                <div style="display:flex;justify-content:space-between;align-items: center;">
                    <div style="display:flex;gap:4px;">
                        <span class="fileinput-button">
                            <button type="button" class="btn btn-success fileinput-button">
                                <i style="height:15px;width:15px;" class="fas fa-plus"></i>
                                <span>Add files</span>
                            </button>
                            <input type="file" id="batchUploadedElement" name="imgfile" multiple/>
                        </span>
                        <button type="submit" class="btn btn-primary start">
                            <i style="height:15px;width:15px;" class="fas fa-upload"></i>
                            <span>Start upload</span>
                        </button>
                        <button type="reset" class="btn btn-warning cancel" onclick="resetUploader();">
                            <i style="height:15px;width:15px;" class="fas fa-ban"></i>
                            <span>Cancel upload</span>
                        </button>
                    </div>
                    <div id="csvDataMessage" style="display:none;">
                        <strong style="color:red;font-size:16px;">CSV Data Uploaded</strong>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 fileupload-progress fade" style="width:100%;margin: 15px 0;">
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="margin-bottom:0;">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
            </div>
            <div id="fileDropZone">
                <span id="dropZoneLabel">Drag & Drop files here</span>
            </div>
        </div>
        <table role="presentation" class="table table-striped">
            <tbody id="uploadList"></tbody>
        </table>
    </form>
</div>

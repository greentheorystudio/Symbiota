<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonomyUpload.php');
include_once(__DIR__ . '/../../classes/TaxonomyUtilities.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' . $GLOBALS['CLIENT_ROOT'] . '/taxa/admin/batchimageloader.php');
}
ini_set('max_execution_time', 7200);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
	$isEditor = true;
}

$loaderManager = new TaxonomyUpload();
$taxaUtilities = new TaxonomyUtilities();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxa Batch Image Loader</title>
    <link href="../../css/bootstrap.min.css?ver=20220225"  rel="stylesheet" />
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/jquery.fileupload.css" type="text/css" rel="stylesheet" />
    <link href="../../css/jquery.fileupload-ui.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
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
    <script src="../../js/symb/shared.js?ver=20220310" type="text/javascript"></script>
    <script src="../../js/jquery.js"></script>
    <script src="../../js/jquery.ui.widget.js"></script>
    <script src="../../js/tmpl.min.js"></script>
    <script src="../../js/all.min.js"></script>
    <script src="../../js/load-image.all.min.js"></script>
    <script src="../../js/jquery.fileupload.js"></script>
    <script src="../../js/jquery.fileupload-process.js"></script>
    <script src="../../js/jquery.fileupload-image.js"></script>
    <script src="../../js/jquery.fileupload-audio.js"></script>
    <script src="../../js/jquery.fileupload-video.js"></script>
    <script src="../../js/jquery.fileupload-validate.js"></script>
    <script src="../../js/jquery.fileupload-ui.js"></script>
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
                        <strong class="goodMessage" style="color:green;{%=file.errorMessage?'display:none;':'display:block;'%}">Linked to thesaurus and ready to upload</strong>
                        <strong class="errorMessage" style="color:red;{%=file.errorMessage?'display:block;':'display:none;'%}width:350px;">Not inked to thesaurus</strong>
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
                url: 'rpc/uploadimage.php',
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
                                    fileNode.getElementsByClassName('errorMessage')[0].innerHTML = data._response.jqXHR.responseJSON.files[0].error;
                                    fileNode.getElementsByClassName('errorMessage')[0].style.display = 'block';
                                    fileNode.getElementsByClassName('goodMessage')[0].style.display = 'none';
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

        function resetUploader(){
            fileData = [];
            taxaNameArr = [];
            taxaDataArr = {};
            document.getElementById('csvDataMessage').style.display = 'none';
        }

        function parseScinameFromFilename(fileName){
            let adjustedFileName = fileName.replace(/_/g, ' ');
            adjustedFileName = adjustedFileName.replace(/\s+/g, ' ').trim();
            const lastDotIndex = adjustedFileName.lastIndexOf('.');
            adjustedFileName = adjustedFileName.substr(0, lastDotIndex);
            const lastSpaceIndex = adjustedFileName.lastIndexOf(' ');
            if(lastSpaceIndex){
                const lastPartAfterSpace = adjustedFileName.substr(lastSpaceIndex);
                if(!isNaN(lastPartAfterSpace)){
                    adjustedFileName = adjustedFileName.substr(0, lastSpaceIndex);
                }
            }
            taxaNameArr.push(adjustedFileName);
            getNewTaxaDataArr(adjustedFileName,fileName,false);
        }

        function getNewTaxaDataArr(value,fileName,validate){
            const fileNodeArr = document.getElementById('uploadList').childNodes;
            const http = new XMLHttpRequest();
            const url = "rpc/getbatchimageuploadtaxadata.php";
            const params = 'taxa='+encodeURIComponent(JSON.stringify(taxaNameArr));
            //console.log(url+'?'+params);
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function() {
                if(http.readyState === 4 && http.status === 200) {
                    if(http.responseText) {
                        taxaDataArr = {
                            ...taxaDataArr,
                            ...JSON.parse(http.responseText)
                        };
                        taxaNameArr = [];
                        if(taxaDataArr.hasOwnProperty(value)){
                            for(let n in fileNodeArr){
                                if(fileNodeArr.hasOwnProperty(n)){
                                    const fileNode = fileNodeArr[n];
                                    const nodeFileName = fileNode.getElementsByClassName('name')[0].innerHTML;
                                    if(nodeFileName === fileName){
                                        if(!validate){
                                            fileNode.querySelectorAll('input[name="scientificname"]')[0].value = value;
                                        }
                                        fileNode.querySelectorAll('input[name="tid"]')[0].value = taxaDataArr[value];
                                        fileNode.getElementsByClassName('errorMessage')[0].innerHTML = '';
                                        fileNode.getElementsByClassName('errorMessage')[0].style.display = 'none';
                                        fileNode.getElementsByClassName('goodMessage')[0].style.display = 'block';
                                        fileNode.querySelectorAll('button[name="startButton"]')[0].classList.remove('disabled');
                                        fileNode.querySelectorAll('button[name="startButton"]')[0].disabled = false;
                                    }
                                }
                            }
                            updateFileDataArrTid();
                            updateDisplay();
                        }
                        else if(validate){
                            for(let n in fileNodeArr){
                                if(fileNodeArr.hasOwnProperty(n)){
                                    const fileNode = fileNodeArr[n];
                                    const nodeFileName = fileNode.getElementsByClassName('name')[0].innerHTML;
                                    if(nodeFileName === fileName){
                                        fileNode.getElementsByClassName('errorMessage')[0].innerHTML = 'Scientific name not found in taxonomic thesaurus';
                                        fileNode.getElementsByClassName('errorMessage')[0].style.display = 'block';
                                        fileNode.getElementsByClassName('goodMessage')[0].style.display = 'none';
                                        fileNode.querySelectorAll('button[name="startButton"]')[0].classList.add('disabled');
                                    }
                                }
                            }
                        }
                    }
                }
            };
            http.send(params);
        }

        function updateFileDataArrTid(){
            for(let i in fileData){
                if(fileData.hasOwnProperty(i)){
                    const sciname = fileData[i].scientificname;
                    if(!fileData[i].hasOwnProperty('tid') || fileData[i].tid === ''){
                        if(sciname){
                            if(taxaDataArr.hasOwnProperty(sciname)){
                                fileData[i].tid = taxaDataArr[sciname];
                                fileData[i].errorMessage = '';
                            }
                            else{
                                fileData[i].errorMessage = 'Scientific name not found in taxonomic thesaurus';
                            }
                        }
                    }
                    else{
                        fileData[i].errorMessage = '';
                    }
                }
            }
        }

        function updateDisplay(){
            const fileNodeArr = document.getElementById('uploadList').childNodes;
            for(let n in fileNodeArr){
                if(fileNodeArr.hasOwnProperty(n)){
                    const fileNode = fileNodeArr[n];
                    const fileName = fileNode.getElementsByClassName('name')[0].innerHTML;
                    let imageFileData = fileData.find((obj) => obj.filename.toLowerCase() === fileName.toLowerCase());
                    if(imageFileData){
                        if(imageFileData.hasOwnProperty('scientificname') && imageFileData.scientificname && !fileNode.querySelectorAll('input[name="scientificname"]')[0].value){
                            fileNode.querySelectorAll('input[name="scientificname"]')[0].value = imageFileData.scientificname;
                        }
                        if(imageFileData.hasOwnProperty('tid') && imageFileData.tid && !fileNode.querySelectorAll('input[name="tid"]')[0].value){
                            fileNode.querySelectorAll('input[name="tid"]')[0].value = imageFileData.tid;
                        }
                        if(imageFileData.hasOwnProperty('photographer') && imageFileData.photographer && !fileNode.querySelectorAll('input[name="photographer"]')[0].value){
                            fileNode.querySelectorAll('input[name="photographer"]')[0].value = imageFileData.photographer;
                        }
                        if(imageFileData.hasOwnProperty('caption') && imageFileData.caption && !fileNode.querySelectorAll('input[name="caption"]')[0].value){
                            fileNode.querySelectorAll('input[name="caption"]')[0].value = imageFileData.caption;
                        }
                        if(imageFileData.hasOwnProperty('owner') && imageFileData.owner && !fileNode.querySelectorAll('input[name="owner"]')[0].value){
                            fileNode.querySelectorAll('input[name="owner"]')[0].value = imageFileData.owner;
                        }
                        if(imageFileData.hasOwnProperty('sourceurl') && imageFileData.sourceurl && !fileNode.querySelectorAll('input[name="sourceurl"]')[0].value){
                            fileNode.querySelectorAll('input[name="sourceurl"]')[0].value = imageFileData.sourceurl;
                        }
                        if(imageFileData.hasOwnProperty('copyright') && imageFileData.copyright && !fileNode.querySelectorAll('input[name="copyright"]')[0].value){
                            fileNode.querySelectorAll('input[name="copyright"]')[0].value = imageFileData.copyright;
                        }
                        if(imageFileData.hasOwnProperty('locality') && imageFileData.locality && !fileNode.querySelectorAll('input[name="locality"]')[0].value){
                            fileNode.querySelectorAll('input[name="locality"]')[0].value = imageFileData.locality;
                        }
                        if(imageFileData.hasOwnProperty('notes') && imageFileData.notes && !fileNode.querySelectorAll('input[name="notes"]')[0].value){
                            fileNode.querySelectorAll('input[name="notes"]')[0].value = imageFileData.notes;
                        }
                        if(imageFileData.hasOwnProperty('errorMessage') && imageFileData.errorMessage){
                            fileNode.getElementsByClassName('errorMessage')[0].innerHTML = imageFileData.errorMessage;
                            fileNode.getElementsByClassName('errorMessage')[0].style.display = 'block';
                            fileNode.getElementsByClassName('goodMessage')[0].style.display = 'none';
                            fileNode.querySelectorAll('button[name="startButton"]')[0].classList.add('disabled');
                        }
                        else{
                            fileNode.getElementsByClassName('errorMessage')[0].innerHTML = '';
                            fileNode.getElementsByClassName('errorMessage')[0].style.display = 'none';
                            fileNode.getElementsByClassName('goodMessage')[0].style.display = 'block';
                            fileNode.querySelectorAll('button[name="startButton"]')[0].classList.remove('disabled');
                        }
                    }
                }
            }
        }

        function processCsvFile(e, file){
            const reader = new FileReader();
            reader.onload = function (e) {
                const text = e.target.result;
                fileData = csvToArray(text);
                if(taxaNameArr.length > 0){
                    setTaxaDataObjFromTaxaArr();
                }
                updateDisplay();
            };
            reader.readAsText(file);
        }

        function validateSciNameChange(value,fileName) {
            const fileNodeArr = document.getElementById('uploadList').childNodes;
            for(let n in fileNodeArr){
                if(fileNodeArr.hasOwnProperty(n)){
                    const fileNode = fileNodeArr[n];
                    const nodeFileName = fileNode.getElementsByClassName('name')[0].innerHTML;
                    if(nodeFileName === fileName){
                        fileNode.getElementsByClassName('errorMessage')[0].innerHTML = 'Validating name...';
                        fileNode.getElementsByClassName('errorMessage')[0].style.display = 'block';
                        fileNode.getElementsByClassName('goodMessage')[0].style.display = 'none';
                        fileNode.querySelectorAll('button[name="startButton"]')[0].classList.add('disabled');
                    }
                }
            }
            taxaNameArr.push(value);
            getNewTaxaDataArr(value,fileName,true);
        }

        function csvToArray(str) {
            const headers = str.slice(0, str.indexOf("\n")).split(',');
            if(str.endsWith("\n")){
                str = str.substring(0, str.length - 2);
            }
            const rows = str.slice(str.indexOf("\n") + 1).split("\n");
            return rows.map(function (row) {
                if (row) {
                    document.getElementById('csvDataMessage').style.display = 'inline-block';
                    const values = row.split(',');
                    return headers.reduce(function (object, header, index) {
                        const fieldName = header.trim();
                        const fieldValue = values[index].replace('\r', '');
                        if (fieldName === 'scientificname' && fieldValue && !taxaNameArr.includes(fieldValue) && !taxaDataArr.hasOwnProperty(fieldValue)) {
                            taxaNameArr.push(fieldValue);
                        }
                        object[fieldName] = fieldValue;
                        return object;
                    }, {});
                }
            });
        }

        function setTaxaDataObjFromTaxaArr(){
            const http = new XMLHttpRequest();
            const url = "rpc/getbatchimageuploadtaxadata.php";
            const params = 'taxa='+encodeURIComponent(JSON.stringify(taxaNameArr));
            //console.log(url+'?'+params);
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function() {
                if(http.readyState === 4 && http.status === 200) {
                    if(http.responseText) {
                        taxaDataArr = {
                            ...taxaDataArr,
                            ...JSON.parse(http.responseText)
                        };
                        taxaNameArr = [];
                        updateFileDataArrTid();
                        updateDisplay();
                    }
                }
            };
            http.send(params);
        }
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../../header.php');
?>
<div class="navpath">
    <a href="../../index.php">Home</a> &gt;&gt;
    <a href="batchimageloader.php"><b>Taxa Batch Image Loader</b></a>
</div>
<?php

if($isEditor){
    ?>
    <div id="innertext">
        <h1>Taxa Batch Image Loader</h1>
        <div style="margin-bottom:20px;font-size:16px;">
            To batch upload taxa images, either click the Add files button to select the files to be uploaded or drag and
            drop the files onto the box below. A csv spreadheet can also be uploaded to provide further metadata for the files.
            <a href="../../templates/batchTaxaImageData.csv"><b>Use this template for the csv spreadsheet.</b></a> For each
            row in the spreadsheet, the value in the filename column must match the filename of the associated file being uploaded.
        </div>
        <form id="fileupload" method="POST" enctype="multipart/form-data">
            <div class="row fileupload-buttonbar">
                <div class="col-lg-7" style="margin-bottom:15px;">
                    <span class="btn btn-success fileinput-button">
                        <i style="height:15px;width:15px;" class="fas fa-plus"></i>
                        <span>Add files</span>
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
                    <div id="csvDataMessage" style="display:none;">
                        <strong style="color:red;">CSV Data Uploaded</strong>
                    </div>
                    <span class="fileupload-process"></span>
                </div>
                <div class="col-lg-5 fileupload-progress fade" style="margin-bottom:0;">
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
    <?php
}
else{
    ?>
    <div style='font-weight:bold;margin:30px;'>
        You do not have permissions to batch upload taxa images
    </div>
    <?php
}
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>

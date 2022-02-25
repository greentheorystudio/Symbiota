<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonomyUpload.php');
include_once(__DIR__ . '/../../classes/TaxonomyUtilities.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' . $GLOBALS['CLIENT_ROOT'] . '/taxa/admin/batchloader.php');
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
    <script src="../../js/symb/shared.js?ver=20220221" type="text/javascript"></script>
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
                    <p class="name">{%=file.name%}</p>
                    <strong class="error text-danger"></strong>
                </td>
                <td>
                    <p class="size">Processing...</p>
                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
                </td>
                <td style="display:flex;justify-content:flex-end;align-items:center;align-content:center;gap:5px;">
                    {% if (!i && !o.options.autoUpload) { %}
                        <button class="btn btn-primary start">
                            <i style="height:15px;width:15px;" class="fas fa-upload"></i>
                            <span>Start</span>
                        </button>
                    {% } %}
                    {% if (!i) { %}
                        <button class="btn btn-warning cancel">
                            <i style="height:15px;width:15px;" class="fas fa-ban"></i>
                            <span>Cancel</span>
                        </button>
                    {% } %}
                </td>
            </tr>
        {% } %}
    </script>
    <script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-download fade{%=file.thumbnailUrl?' image':''%}">
                <td>
                    <span class="preview">
                        {% if (file.thumbnailUrl) { %}
                            <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                        {% } %}
                    </span>
                </td>
                <td>
                    <p class="name">
                        {% if (file.url) { %}
                            <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                        {% } else { %}
                            <span>{%=file.name%}</span>
                        {% } %}
                    </p>
                    {% if (file.error) { %}
                        <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                    {% } %}
                </td>
                <td>
                    <span class="size">{%=o.formatFileSize(file.size)%}</span>
                </td>
                <td>
                    <button class="btn btn-warning cancel">
                        <i style="height:15px;width:15px;" class="fas fa-ban"></i>
                        <span>Cancel</span>
                    </button>
                </td>
            </tr>
        {% } %}
    </script>
    <script type="text/javascript">
        $(function () {
            $('#fileupload').fileupload({
                url: 'rpc/uploadimage.php'
            });

            /*$('#fileupload').addClass('fileupload-processing');
            $.ajax({
                url: $('#fileupload').fileupload('option', 'url'),
                dataType: 'json',
                context: $('#fileupload')[0]
            }).always(function () {
                $(this).removeClass('fileupload-processing');
            }).done(function (result) {
                $(this).fileupload('option', 'done').call(this, $.Event('done'), {result: 'test'});
            });*/
        });
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../../header.php');
?>
<div class="navpath">
    <a href="../../index.php">Home</a> &gt;&gt;
    <a href="batchloader.php"><b>Taxa Batch Image Loader</b></a>
</div>
<?php

if($isEditor){
	?>
	<div id="innertext">
		<h1>Taxa Batch Image Loader</h1>
        <form id="fileupload" action="" method="POST" enctype="multipart/form-data">
            <div class="row fileupload-buttonbar">
                <div class="col-lg-7">
                    <span class="btn btn-success fileinput-button">
                        <i style="height:15px;width:15px;" class="fas fa-plus"></i>
                        <span>Add files</span>
                        <input type="file" name="files[]" multiple/>
                    </span>
                    <button type="submit" class="btn btn-primary start">
                        <i style="height:15px;width:15px;" class="fas fa-upload"></i>
                        <span>Start upload</span>
                    </button>
                    <button type="reset" class="btn btn-warning cancel">
                        <i style="height:15px;width:15px;" class="fas fa-ban"></i>
                        <span>Cancel upload</span>
                    </button>
                    <span class="fileupload-process"></span>
                </div>
                <div class="col-lg-5 fileupload-progress fade">
                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar progress-bar-success" style="width: 0%;"></div>
                    </div>
                    <div class="progress-extended">&nbsp;</div>
                </div>
            </div>
            <table role="presentation" class="table table-striped">
                <tbody class="files"></tbody>
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

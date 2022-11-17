<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonomyEditorManager.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;
$status = array_key_exists('statusstr',$_REQUEST)?$_REQUEST['statusstr']:'';

$loaderObj = new TaxonomyEditorManager();

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($isEditor && array_key_exists('sciname', $_POST)) {
    $status = $loaderObj->loadNewName($_POST);
    if(is_int($status)){
        header('Location: taxonomyeditor.php?tid=' .$status);
    }
}
$tRankArr = $loaderObj->getRankArr();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomy Editor</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/external/jquery-ui.css?ver=20220720" type="text/css" rel="stylesheet" />
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
	<script src="../../js/external/jquery.js" type="text/javascript"></script>
	<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
	<script src="../../js/shared.js?ver=20221116" type="text/javascript"></script>
	<script>
        $(document).ready(function() {
            $('#tabs').tabs({
                active: <?php echo $tabIndex; ?>,
                beforeLoad: function( event, ui ) {
                    $(ui.panel).html("<p>Loading...</p>");
                }
            });

        });
    </script>
</head>
<body>
<?php
	include(__DIR__ . '/../../header.php');
?>
<div class="navpath">
    <a href="../../index.php">Home</a> &gt;&gt;
    <b>Taxonomy Editor</b>
</div>
<div id="innertext">
	<h1>Taxonomy Editor</h1>
	<?php
	if($isEditor){
		?>
        <div id="tabs">
            <ul>
                <li><a href="#new">Add New Taxon</a></li>
                <li><a href="#edit">Edit Existing Taxon</a></li>
            </ul>

            <div id="new">
                <?php include_once(__DIR__ . '/taxonomyloader.php'); ?>
            </div>

            <div id="edit">
                <?php include_once(__DIR__ . '/taxonomydisplay.php'); ?>
            </div>
        </div>
        <?php
	}
	else{
        echo '<div style="font-weight:bold;font-size:120%;">You do not have permissions to access this tool</div>';
    }
	?>
</div>
<?php
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>

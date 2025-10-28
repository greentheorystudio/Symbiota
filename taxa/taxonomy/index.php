<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonomyEditorManager.php');
include_once(__DIR__ . '/../../models/TaxonHierarchy.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;
$status = array_key_exists('statusstr',$_REQUEST)?$_REQUEST['statusstr']:'';
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$loaderObj = new TaxonomyEditorManager();
$taxUtilities = new TaxonHierarchy();

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($isEditor && $action === 'Submit New Name') {
    $tid = $loaderObj->loadNewName($_POST);
    if($tid){
        if($taxUtilities->primeHierarchyTable($tid)){
            $hierarchyAdded = 1;
            do {
                $hierarchyAdded = $taxUtilities->populateHierarchyTable();
            } while($hierarchyAdded > 0);
        }
        $loaderObj->updateOccurrencesNewTaxon($_POST);
        header('Location: taxonomyeditor.php?tid=' .$tid);
    }
}
$tRankArr = $loaderObj->getRankArr();
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomy Editor</title>
    <meta name="description" content="Taxonomy editor for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
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
<div id="mainContainer" style="padding: 10px 15px 15px;">
    <div id="breadcrumbs">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" tabindex="1">Home</a> &gt;&gt;
        <b>Taxonomy Editor</b>
    </div>
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
        echo '<div style="font-weight:bold;">You do not have permissions to access this tool</div>';
    }
	?>
</div>
<?php
include_once(__DIR__ . '/../../config/footer-includes.php');
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>

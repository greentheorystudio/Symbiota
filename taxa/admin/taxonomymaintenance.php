<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/TaxonomyHarvester.php');
include_once(__DIR__ . '/../../classes/TaxonomyUtilities.php');
header('Content-Type: text/html; charset=' .$CHARSET);

if(!$SYMB_UID) {
    header('Location: ../../profile/index.php?refurl=../taxa/admin/taxonomymaintenance.php?' . $_SERVER['QUERY_STRING']);
}

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']: '';

$harvesterManager = new TaxonomyHarvester();
$utilitiesManager = new TaxonomyUtilities();
 
$isEditor = false;
if($IS_ADMIN || array_key_exists('Taxonomy',$USER_RIGHTS)){
	$isEditor = true;
}

if($isEditor){
	if($action === 'buildenumtree'){
		if($utilitiesManager->buildHierarchyEnumTree()){
			$statusStr = 'SUCCESS building Taxonomic Index';
		}
		else{
			$statusStr = 'ERROR building Taxonomic Index: '.$harvesterManager->getErrorMessage();
		}
	}
	elseif($action === 'rebuildenumtree'){
		if($utilitiesManager->buildHierarchyEnumTree()){
			$statusStr = 'SUCCESS building Taxonomic Index';
		}
		else{
			$statusStr = 'ERROR building Taxonomic Index: '.$harvesterManager->getErrorMessage();
		}
	}
}

?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<title><?php echo $DEFAULT_TITLE. ' Taxonomy Maintenance '; ?></title>
	<link href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<link type="text/css" href="../../css/jquery-ui.css" rel="Stylesheet" />
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript">
	</script>
</head>
<body>
<?php
include(__DIR__ . '/../../header.php');
?>
<div class="navpath">
    <a href="../../index.php">Home</a> &gt;&gt;
    <a href="taxonomyloader.php"><b>Taxonomic Tree Viewer</b></a>
</div>
	<div id="innertext">
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="color:<?php echo (strpos($statusStr,'SUCCESS') !== false?'green':'red'); ?>;margin:15px;">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
			<?php 
		}
		if($isEditor){
			?>


			<?php 
		}
		else{
			?>
			<div style="margin:30px;font-weight:bold;font-size:120%;">
				You do not have permission to view this page. Please contact your portal administrator
			</div>
			<?php 
		}
		?>
	</div>
	<?php 
	include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>

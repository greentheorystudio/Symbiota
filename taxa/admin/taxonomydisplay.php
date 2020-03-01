<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/TaxonomyDisplayManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$target = array_key_exists('target',$_REQUEST)?$_REQUEST['target']: '';
$displayAuthor = array_key_exists('displayauthor',$_REQUEST)?$_REQUEST['displayauthor']:0;
$displayFullTree = array_key_exists('displayfulltree',$_REQUEST)?$_REQUEST['displayfulltree']:0;
$displaySubGenera = array_key_exists('displaysubgenera',$_REQUEST)?$_REQUEST['displaysubgenera']:0;
$taxAuthId = array_key_exists('taxauthid',$_REQUEST)?$_REQUEST['taxauthid']:1;
$statusStr = array_key_exists('statusstr',$_REQUEST)?$_REQUEST['statusstr']:'';

$taxonDisplayObj = new TaxonomyDisplayManager();
$taxonDisplayObj->setTargetStr($target);
$taxonDisplayObj->setTaxAuthId($taxAuthId);
$taxonDisplayObj->setDisplayAuthor($displayAuthor);
$taxonDisplayObj->setDisplayFullTree($displayFullTree);
$taxonDisplayObj->setDisplaySubGenera($displaySubGenera);

$isEditor = false;
if($IS_ADMIN || array_key_exists('Taxonomy',$USER_RIGHTS)){
	$isEditor = true;
} 
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<title><?php echo $DEFAULT_TITLE. ' Taxonomy Display: ' .$taxonDisplayObj->getTargetStr(); ?></title>
	<link href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<link type="text/css" href="../../css/jquery-ui.css" rel="Stylesheet" />
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {

			$("#taxontarget").autocomplete({
				source: function( request, response ) {
					$.getJSON( "rpc/gettaxasuggest.php", { term: request.term, taid: document.tdform.taxauthid.value }, response );
				}
			},{ minLength: 3 }
			);

		});
	</script>
</head>
<body>
<?php
include(__DIR__ . '/../../header.php');
?>
<div class="navpath">
    <a href="../../index.php">Home</a> &gt;&gt;
    <a href="taxonomydisplay.php"><b>Taxonomic Tree Viewer</b></a>
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
			<div style="float:right;" title="Add a New Taxon">
				<a href="taxonomyloader.php">
					<img style='border:0;width:15px;' src='../../images/add.png'/>
				</a>
			</div>
			<?php
		}
		?>
		<div>
			<form id="tdform" name="tdform" action="taxonomydisplay.php" method='POST'>
				<fieldset style="padding:10px;width:550px;">
					<legend><b>Enter a taxon</b></legend>
					<div>
						<b>Taxon:</b> 
						<input id="taxontarget" name="target" type="text" style="width:400px;" value="<?php echo $taxonDisplayObj->getTargetStr(); ?>" /> 
					</div>
					<div style="float:right;margin:15px 80px 15px 15px;">
						<input name="tdsubmit" type="submit" value="Display Taxon Tree"/>
						<input name="taxauthid" type="hidden" value="<?php echo $taxAuthId; ?>" /> 
					</div>
					<div style="margin:15px 15px 0 60px;">
						<input name="displayauthor" type="checkbox" value="1" <?php echo ($displayAuthor?'checked':''); ?> /> Display authors
					</div>
					<div style="margin:3px 15px 0 60px;">
						<input name="displayfulltree" type="checkbox" value="1" <?php echo ($displayFullTree?'checked':''); ?> /> Display full tree below family 
					</div>
					<div style="margin:3px 15px 15px 60px;">
						<input name="displaysubgenera" type="checkbox" value="1" <?php echo ($displaySubGenera?'checked':''); ?> /> Display species with subgenera 
					</div>
				</fieldset>
			</form>
		</div>
		<?php 
		if($target){
			$taxonDisplayObj->displayTaxonomyHierarchy();
		}
		?>
	</div>
	<?php 
	include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>

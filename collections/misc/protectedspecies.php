<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceProtectedSpecies.php');
include_once(__DIR__ . '/../../classes/OccurrenceTaxonomyCleaner.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$action = array_key_exists('submitaction',$_REQUEST)?htmlspecialchars($_REQUEST['submitaction']):'';
$searchTaxon = array_key_exists('searchtaxon',$_POST)?htmlspecialchars($_POST['searchtaxon']):'';

$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || array_key_exists('RareSppAdmin',$GLOBALS['USER_RIGHTS'])){
    $isEditor = 1;
}

$rsManager = new OccurrenceProtectedSpecies($isEditor?'write':'readonly');

if($isEditor){
	if($action === 'addspecies'){
		$rsManager->addSpecies($_POST['tidtoadd']);
	}
	elseif($action === 'deletespecies'){
		$rsManager->deleteSpecies($_REQUEST['tidtodel']);
	}
}
if($searchTaxon) {
    $rsManager->setTaxonFilter($searchTaxon);
}
$rsArr = $rsManager->getProtectedSpeciesList();
?>
<html>
<head>
    <title>Protected Species</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
    <script src="../../js/external/jquery.js" type="text/javascript"></script>
    <script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../js/shared.js?ver=20221207"></script>
	<script>
		$(document).ready(function() {
			$("#speciestoadd").autocomplete({ source: "../../api/taxa/speciessuggest.php" },{ minLength: 3, autoFocus: true });
			$("#searchtaxon").autocomplete({ source: "../../api/taxa/speciessuggest.php" },{ minLength: 3 });
		});

		function submitAddSpecies(f){
			var sciName = f.speciestoadd.value;
			if(sciName == ""){
				alert("Enter the scientific name of species you wish to add");
				return false;
			}

			$.ajax({
				type: "POST",
				url: "../../api/taxa/gettid.php",
				dataType: "json",
				data: { sciname: sciName }
			}).done(function( data ) {
				f.tidtoadd.value = data;
				f.submit();
			}).fail(function(jqXHR){
				alert("ERROR: Scientific name does not exist in database. Did you spell it correctly? If so, it may have to be added to taxa table.");
			});
		}
	</script>
</head>
<body>
<?php
include(__DIR__ . '/../../header.php');
?>
<div id="innertext">
	<?php
	if($isEditor){
		?>
		<div style="float:right;cursor:pointer;" onclick="toggle('editobj');toggle('editobjspan','inline');" title="Toggle Editing Functions">
            <i style='width:20px;height:20px;' class="far fa-edit"></i>
		</div>
		<?php
	}
	?>
	<h1>Protected Species</h1>
	<div style="float:right;">
		<fieldset style="margin:0px 15px;padding:10px">
			<legend><b>Filter</b></legend>
			<form name="searchform" action="protectedspecies.php" method="post">
				<div style="margin:3px">
					Taxon Search:
					<input id="searchtaxon" name="searchtaxon" type="text" value="<?php echo $searchTaxon; ?>" />
				</div>
				<div style="margin:3px">
					<input name="submitaction" type="submit" value="Search" />
				</div>
			</form>
		</fieldset>
	</div>
	<div>
		<?php
		$occurCnt = $rsManager->getOccRecordCnt();
		if($occurCnt) {
            echo '<div style="margin:0px 40px 0px 20px;float:left">Occurrences protected: ' . number_format($occurCnt) . '</div>';
        }
		if($isEditor){
			if($action === 'checkstats'){
                $cleanManager = new OccurrenceTaxonomyCleaner();
                echo '<div>Number of records affected: '.$cleanManager->protectGlobalSpecies().'</div>';
			}
			else{
				echo '<div><a href="protectedspecies.php?submitaction=checkstats"><button>Verify protections</button></a></div>';
			}
		}
		?>
	</div>
	<div style="clear:both">
		<fieldset style="padding:15px;margin:15px">
			<legend><b>Global Protections</b></legend>
			<?php
			if($isEditor){
				?>
				<div id="editobj" style="display:none;width:400px;">
					<form name="addspeciesform" action='protectedspecies.php' method='post'>
						<fieldset style='margin:5px;background-color:#FFFFCC;'>
							<legend><b>Add Species to List</b></legend>
							<div style="margin:3px;">
								Scientific Name:
								<input type="text" id="speciestoadd" name="speciestoadd" style="width:300px" />
								<input type="hidden" id="tidtoadd" name="tidtoadd" value="" />
							</div>
							<div style="margin:3px;">
								<input type="hidden" name="submitaction" value="addspecies" />
								<input type="button" value="Add Species" onclick="submitAddSpecies(this.form)" />
							</div>
						</fieldset>
					</form>
				</div>
				<?php
			}
			if($rsArr){
				foreach($rsArr as $family => $speciesArr){
					?>
					<h3><?php echo $family; ?></h3>
					<div style='margin-left:20px;'>
						<?php
						foreach($speciesArr as $tid => $nameArr){
							echo '<div id="tid-'.$tid.'"><a href="../../taxa/index.php?taxon='.$tid.'" target="_blank"><i>'.$nameArr['sciname'].'</i> '.$nameArr['author'].'</a> ';
							if($isEditor){
								?>
								<span class="editobjspan" style="display:none;">
									<a href="protectedspecies.php?submitaction=deletespecies&tidtodel=<?php echo $tid;?>">
										<img src="../../images/del.png" style="width:13px;border:0px;" title="remove species from list" />
									</a>
								</span>
								<?php
							}
							echo '</div>';
						}
						?>
					</div>
					<?php
				}
			}
			else{
				?>
				<div style="margin:20px;font-weight:bold;">
					No species were returned marked for global protection.
				</div>
				<?php
			}
			?>
		</fieldset>
		<fieldset style="padding:15px;margin:15px;">
			<legend><b>State/Province Level Protections</b></legend>
			<?php
			$stateList = $rsManager->getStateList();
			$emptyList = true;
			foreach($stateList as $clid => $stateArr){
				if($isEditor || $stateArr['access'] === 'public'){
					echo '<div>';
					echo '<a href="../../checklists/checklist.php?clid='.$clid.'">';
					echo $stateArr['locality'].': '.$stateArr['name'];
					echo '</a>';
					if($stateArr['access'] === 'private') {
                        echo ' (private)';
                    }
					echo '</div>';
					$emptyList = false;
				}
			}
			if($emptyList){
				?>
				<div style="margin:20px;font-weight:bold;">
					 No checklists returned
				</div>
				<?php
			}
			?>
		</fieldset>
	</div>
</div>
<?php
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>

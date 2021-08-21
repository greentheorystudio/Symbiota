<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/TaxonomyCleaner.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=../collections/cleaning/taxonomycleaner.php?' . $_SERVER['QUERY_STRING']);
}

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$autoClean = array_key_exists('autoclean',$_POST)?(int)$_POST['autoclean']:0;
$targetKingdom = array_key_exists('targetkingdom',$_POST)?(int)$_POST['targetkingdom']:0;
$taxResource = array_key_exists('taxresource',$_POST)?$_POST['taxresource']:'';
$startIndex = array_key_exists('startindex',$_POST)?$_POST['startindex']:'';
$limit = array_key_exists('limit',$_POST)?(int)$_POST['limit']:20;
$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';

$cleanManager = new TaxonomyCleaner();
if(is_array($collid)) {
    $collid = implode(',', $collid);
}
$activeCollArr = explode(',', $collid);

foreach($activeCollArr as $k => $id){
	if(!isset($GLOBALS['USER_RIGHTS']['CollAdmin']) || !in_array($id, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) {
        if($activeCollArr){
            unset($activeCollArr[$k]);
        }
    }
}
if(!$activeCollArr && strpos($collid, ',')) {
    $collid = 0;
}
$cleanManager->setCollId($GLOBALS['IS_ADMIN']?$collid:implode(',',$activeCollArr));

$isEditor = false;
if($GLOBALS['IS_ADMIN']){
	$isEditor = true;
}
elseif($activeCollArr){
	$isEditor = true;
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Taxon Cleaner</title>
		<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/jquery-ui.css?ver=3" type="text/css" rel="stylesheet" />
        <script src="../../js/all.min.js" type="text/javascript"></script>
		<script src="../../js/jquery.js?ver=3" type="text/javascript"></script>
		<script src="../../js/jquery-ui.js?ver=3" type="text/javascript"></script>
		<script>
            const cache = {};

            $( document ).ready(function() {
				$(".displayOnLoad").show();
				$(".hideOnLoad").hide();

				$(".taxon").each(function(){
					$( this ).autocomplete({
						minLength: 2,
						autoFocus: true,
						source: function( request, response ) {
                            const term = request.term;
                            if ( term in cache ) {
								response( cache[ term ] );
								return;
							}
							$.getJSON( "rpc/taxasuggest.php", request, function( data ) {
								cache[ term ] = data;
								response( data );
							});
						},
						change: function(event,ui) {
							if(ui.item == null && this.value.trim() !== ""){
								alert("Scientific name not found in Thesaurus.");
								this.focus();
								this.form.tid.value = "";
							}
						},
						focus: function( event, ui ) {
							this.form.tid.value = ui.item.id;
						},
						select: function( event, ui ) {
							this.form.tid.value = ui.item.id;
						}
					});
				});
			});

			function remappTaxon(oldName,targetTid,idQualifier,msgCode){
				$.ajax({
					type: "POST",
					url: "rpc/remaptaxon.php",
					dataType: "json",
					data: { collid: "<?php echo $collid; ?>", oldsciname: oldName, tid: targetTid, idq: idQualifier }
				}).done(function( res ) {
					if(Number(res) === 1){
						$("#remapSpan-"+msgCode).text(" >>> Occurrences remapped successfully!");
						$("#remapSpan-"+msgCode).css('color', 'green');
					}
					else{
						$("#remapSpan-"+msgCode).text(" >>> Occurrence remapping failed!");
						$("#remapSpan-"+msgCode).css('color', 'orange');
					}
				});
				return false;
			}

			function batchUpdate(f, oldName, itemCnt){
				if(f.tid.value === ""){
					alert("Taxon not found within taxonomic thesaurus");
					return false;
				}
				else{
					remappTaxon(oldName, f.tid.value, '', itemCnt+"-c");
				}
			}

			function checkSelectCollidForm(f){
                let formVerified = false;
                for(let h=0; h<f.length; h++){
					if(f.elements[h].name === "collid[]" && f.elements[h].checked){
						formVerified = true;
						break;
					}
				}
				if(!formVerified){
					alert("Please choose at least one collection!");
					return false;
				}
				return true;
			}

			function selectAllCollections(cbObj){
                const cbStatus = cbObj.checked;
                const f = cbObj.form;
                for(let i=0; i<f.length; i++){
					if(f.elements[i].name === "collid[]") f.elements[i].checked = cbStatus;
				}
			}

			function verifyCleanerForm(f){
				if(f.targetkingdom.value === ""){
					alert("Select target kingdom for collection");
					return false;
				}
				return true;
			}
		</script>
		<script src="../../js/symb/shared.js?ver=20210621" type="text/javascript"></script>
	</head>
	<body>
		<?php
		include(__DIR__ . '/../../header.php');
		?>
		<div class='navpath'>
			<a href="../../index.php">Home</a> &gt;&gt;
			<?php
			if($collid && is_numeric($collid)){
				?>
				<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management</a> &gt;&gt;
				<a href="index.php?collid=<?php echo $collid; ?>&emode=1">Data Cleaning Tools</a> &gt;&gt;
				<?php
			}
			else{
				?>
				<a href="../../profile/viewprofile.php?tabindex=1">Specimen Management</a> &gt;&gt;
				<?php
			}
			?>
			<b>Taxonomic Name Resolution Module</b>
		</div>
		<div id="innertext">
			<?php
			$collMap = $cleanManager->getCollMap();
			if($collid){
				if($isEditor){
					?>
					<div style="float:left;font-weight: bold; font-size: 130%; margin-bottom: 10px">
						<?php
						if(is_numeric($collid)){
							echo $collMap[$collid]['collectionname'].' ('.$collMap[$collid]['code'].')';
						}
						else{
							echo 'Multiple Collection Cleaning Tool (<a href="#" onclick="$(\'#collDiv\').show()" style="color:blue;text-decoration:underline">'.count($activeCollArr).' collections</a>)';
						}
						?>
					</div>
					<?php
					if($activeCollArr && count($collMap) > 1){
						?>
						<div style="float:left;margin-left:5px;"><a href="#" onclick="toggle('mult_coll_fs')"><i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i></a></div>
						<div style="clear:both">
							<fieldset id="mult_coll_fs" style="display:none;padding: 15px;margin:20px;">
								<legend><b>Multiple Collection Selector</b></legend>
								<form name="selectcollidform" action="taxonomycleaner.php" method="post" onsubmit="return checkSelectCollidForm(this)">
									<div><input name="selectall" type="checkbox" onclick="selectAllCollections(this);" /> Select / Unselect All</div>
									<?php
									foreach($collMap as $id => $collArr){
										if(in_array($id, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
											echo '<div>';
											echo '<input name="collid[]" type="checkbox" value="'.$id.'" '.(in_array($id, $activeCollArr, true) ?'CHECKED':'').' /> ';
											echo $collArr['collectionname'].' ('.$collArr['code'].')';
											echo '</div>';
										}
									}
									?>
									<div style="margin: 15px">
										<button name="submitaction" type="submit" value="EvaluateCollections">Evaluate Collections</button>
									</div>
								</form>
								<div>* Only collections with administrative access are shown</div>
							</fieldset>
						</div>
						<?php
					}
					if(count($activeCollArr) > 1){
						echo '<div id="collDiv" style="display:none;margin:0 20px;clear:both;">';
						foreach($activeCollArr as $activeCollid){
							echo '<div>'.$collMap[$activeCollid]['collectionname'].' ('.$collMap[$activeCollid]['code'].')</div>';
						}
						echo '</div>';
					}
					?>
					<div style="margin:20px;clear:both;">
						<?php
						if($action){
							if($action === 'deepindex'){
								$cleanManager->deepIndexTaxa();
							}
							elseif($action === 'AnalyzingNames'){
								echo '<ul>';
								$cleanManager->setAutoClean($autoClean);
								$cleanManager->setTargetKingdom($targetKingdom);
                                $cleanManager->setTargetKingdom($targetKingdom);
								$startIndex = $cleanManager->analyzeTaxa($taxResource, $startIndex, $limit);
								echo '</ul>';
							}
						}
						$badTaxaCount = $cleanManager->getBadTaxaCount();
						$badSpecimenCount = $cleanManager->getBadSpecimenCount();
						?>
					</div>
					<div style="margin:20px;">
						<fieldset style="padding:20px;">
							<form name="maincleanform" action="taxonomycleaner.php" method="post" onsubmit="return verifyCleanerForm(this)">
								<div style="margin-bottom:15px;">
									<b>Occurrence records with scientific names that are not associated with the taxonomic thesaurus</b>
									<div style="margin-left:10px;margin-top:8px;">
										<u>Records</u>: <?php echo $badSpecimenCount; ?><br/>
										<u>Unique scientific names</u>: <?php echo $badTaxaCount; ?>
									</div>
								</div>
								<hr/>
								<div style="margin:20px 10px">
									<div style="margin:10px 0;">
										Use this tool to attempt to resolve unassociated scientific names through a selected taxonomic data source and
                                        add resolved names to the taxonomic thesaurus.
									</div>
									<div style="margin:10px;">
										<div style="margin-bottom:5px;">
											<fieldset style="padding:15px;margin:10px 0;">
												<legend><b>Taxonomic Data Source</b></legend>
												<?php
												$taxResourceList = $cleanManager->getTaxonomicResourceList();
												foreach($taxResourceList as $taKey => $taValue){
													echo '<input name="taxresource" type="radio" value="'.$taKey.'" '.($taxResource === $taKey ?'checked':'').' /> '.$taValue.'<br/>';
												}
												?>
											</fieldset>
										</div>
										<div style="margin-bottom:5px;">
											Target Kingdom:
											<select name="targetkingdom">
												<option value="">Select Target Kingdom</option>
												<option value="">--------------------------</option>
												<?php
												$kingdomArr = $cleanManager->getKingdomArr();
												foreach($kingdomArr as $kTid => $kSciname){
													echo '<option value="'.$kTid.':'.$kSciname.'" '.($targetKingdom === (int)$kTid?'SELECTED':'').'>'.$kSciname.'</option>';
												}
												?>
											</select>
										</div>
										<div style="margin-bottom:5px;">
											Number of names to process per run: <input name="limit" type="text" value="<?php echo $limit; ?>" style="width:40px" />
										</div>
										<div style="margin-bottom:5px;">
											Start index: <input name="startindex" type="text" value="<?php echo $startIndex; ?>" title="Enter a taxon name or letter of the alphabet to indicate where the processing should start" />
										</div>
										<div style="margin-bottom:5px;">
                                            Processing:
                                            <span style="margin-left:15px;"><input name="autoclean" type="radio" value="0" <?php echo (!$autoClean?'checked':''); ?> /> Manual</span>
                                            <span style="margin-left:10px;"><input name="autoclean" type="radio" value="1" <?php echo ($autoClean === 1?'checked':''); ?> /> Automatic</span>
										</div>
										<div style="clear:both;">
											<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
											<button name="submitaction" type="submit" value="AnalyzingNames" ><?php echo ($startIndex?'Continue Resolving Names':'Resolve Taxonomic Names'); ?></button>
										</div>
									</div>
								</div>
							</form>
							<hr/>
							<form name="deepindexform" action="taxonomycleaner.php" method="post">
								<div style="margin:20px 10px">
									<div style="margin:10px 0;">
										Following tool will run a set of algorithms that will run names through several filters to improve linkages to taxonomic thesaurus
									</div>
									<div style="margin:10px">
										<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
										<button name="submitaction" type="submit" value="deepindex">Deep Index Specimen Taxa</button>
									</div>
								</div>
							</form>
						</fieldset>
					</div>
					<?php
				}
				else{
					echo '<div><b>ERROR: you do not have permission to edit this collection</b></div>';
				}
			}
			elseif($collMap){
				?>
				<div style="margin:0 0 20px 20px;font-weight:bold;font-size:120%;">Batch Taxonomic Cleaning Tool</div>
				<fieldset style="padding: 15px;margin:20px;">
					<legend><b>Collection Selector</b></legend>
					<form name="selectcollidform" action="taxonomycleaner.php" method="post" onsubmit="return checkSelectCollidForm(this)">
						<div><input name="selectall" type="checkbox" onclick="selectAllCollections(this);" /> Select / Unselect All</div>
						<?php
						foreach($collMap as $id => $collArr){
							echo '<div>';
							echo '<input name="collid[]" type="checkbox" value="'.$id.'" /> ';
							echo $collArr['collectionname'].' ('.$collArr['code'].')';
							echo '</div>';
						}
						?>
						<div style="margin: 15px">
							<button name="submitaction" type="submit" value="EvaluateCollections">Evaluate Collections</button>
						</div>
					</form>
					<div>* Only collections with administrative access are shown</div>
				</fieldset>
				<?php
			}
			else{
				?>
				<div style='font-weight:bold;font-size:120%;'>
					ERROR: Collection identifier is null
				</div>
				<?php
			}
			?>
		</div>
		<?php include(__DIR__ . '/../../footer.php');?>
	</body>
</html>

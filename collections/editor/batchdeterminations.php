<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceEditorManager.php');
include_once(__DIR__ . '/../../classes/SOLRManager.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = (int)$_REQUEST['collid'];
$tabTarget = array_key_exists('tabtarget',$_REQUEST)?(int)$_REQUEST['tabtarget']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?htmlspecialchars($_POST['formsubmit']):'';

$occManager = new OccurrenceEditorDeterminations();
$solrManager = new SOLRManager();

$occManager->setCollId($collid);
$occManager->getCollMap();

$isEditor = 0;
$catTBody = '';
$nomTBody = '';
$catArr = array();
$jsonCatArr = '';
$occArr = array();
if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}
elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
	$isEditor = 1;
}
if($isEditor){
	if($formSubmit === 'Add New Determinations'){
		$occidArr = $_REQUEST['occid'];
		$occStr = implode(',',$occidArr);
		$catArr = $occManager->getCatNumArr($occStr);
		$jsonCatArr = json_encode($catArr);
		foreach($occidArr as $k){
			$occManager->setOccId($k);
			$occManager->addDetermination($_REQUEST,$isEditor);
		}
		$catTBody = $occManager->getBulkDetRows($collid,'','',$occStr);
        if($GLOBALS['SOLR_MODE']) {
            $solrManager->updateSOLR();
        }
	}
	if($formSubmit === 'Adjust Nomenclature'){
		$occidArr = $_REQUEST['occid'];
		$occStr = implode(',',$occidArr);
		foreach($occidArr as $k){
			$occManager->setOccId($k);
			$occManager->addNomAdjustment($_REQUEST,$isEditor);
		}
		$nomTBody = $occManager->getBulkDetRows($collid,'','',$occStr);
        if($GLOBALS['SOLR_MODE']) {
            $solrManager->updateSOLR();
        }
	}
}
?>

<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
	    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Batch Determinations/Nomenclatural Adjustments</title>
		<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
        <script src="../../js/all.min.js" type="text/javascript"></script>
		<script src="../../js/jquery.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#tabs").tabs({
					active: <?php echo (is_numeric($tabTarget)?$tabTarget:'0'); ?>
				});
			});

			let catalogNumbers = <?php echo ($jsonCatArr?:'[]'); ?>;

			function adjustAccTab(){
				if(catalogNumbers.length > 0){
					document.getElementById("accrecordlistdviv").style.display = "block";
				}
				else{
					document.getElementById("accrecordlistdviv").style.display = "none";
				}
			}
			
			function clearAccForm(){
				if(confirm("Clearing the form will clear the form and restart the process. Are you sure you want to do this?") === true){
					catalogNumbers.length = 0;
					adjustAccTab();
					document.getElementById("catrecordstbody").innerHTML = '';
					document.getElementById("fcatalognumber").value = '';
					document.getElementById("accselectall").checked = false;
				}
			}
			
			function clearNomForm(){
				if(confirm("Clearing the form will clear the form and restart the process. Are you sure you want to do this?") === true){
					document.getElementById("nomrecordlistdviv").style.display = "none";
					document.getElementById("nomrecordstbody").innerHTML = '';
					document.getElementById("nomsciname").value = '';
					document.getElementById("nomselectall").checked = false;
				}
			}
			
			function submitAccForm(){
                const continueSubmit = true;
                const catNum = document.getElementById("fcatalognumber").value;
                if(catalogNumbers.length < 401){
					if(continueSubmit && $( "#fcatalognumber" ).val() !== ""){
						if(catalogNumbers.indexOf(catNum) < 0){
							$.ajax({
								type: "POST",
								url: "rpc/getnewdetspeclist.php",
								data: { 
									catalognumber: $( "#fcatalognumber" ).val(),
									collid: $( "#fcollid" ).val()
								}
							}).done(function( retStr ) {
								if(retStr){
                                    const oldList = document.getElementById("catrecordstbody").innerHTML;
                                    document.getElementById("catrecordstbody").innerHTML = retStr + oldList;
									catalogNumbers.push(catNum);
									adjustAccTab();
									document.getElementById("fcatalognumber").value = '';
									document.getElementById("accselectall").checked = false;
								}
								else{
									alert("That catalog number does not exist in the database.");
								}
							});
						}
						else{
							alert("That catalog number has already been added to the list.");
						}
					}
				}
				else{
					alert("You cannot add more than 400 occurrences to the list.");
				}
				
				$( "#fcatalognumber" ).focus();
				return false;
			}
			
			function submitNomForm(){
				document.getElementById("nomrecordsubmit").disabled = true;
				document.getElementById("workingcircle").style.display = "inline";
                const continueSubmit = true;
                const sciName = document.getElementById("nomsciname").value;
                if(continueSubmit && $( "#nomsciname" ).val() !== ""){
					$.ajax({
						type: "POST",
						url: "rpc/getnewdetspeclist.php",
						data: { 
							sciname: $( "#nomsciname" ).val(),
							collid: $( "#nomcollid" ).val()
						}
					}).done(function( retStr ) {
						if(retStr){
							document.getElementById("nomrecordstbody").innerHTML = retStr;
							document.getElementById("nomrecordlistdviv").style.display = "block";
							document.getElementById("nomselectall").checked = false;
						}
						else{
							document.getElementById("nomrecordlistdviv").style.display = "none";
							document.getElementById("nomrecordstbody").innerHTML = '';
							document.getElementById("nomsciname").value = '';
							alert("There are no occurrences identified to that taxon.");
						}
					});
				}
				
				$( "#nomsciname" ).focus();
				document.getElementById("workingcircle").style.display = "none";
				document.getElementById("nomrecordsubmit").disabled = false;
				return false;
			}
			
			function selectAll(cb){
                const boxesChecked = cb.checked;
                const dbElements = document.getElementsByName("occid[]");
                for(let i = 0; i < dbElements.length; i++){
                    const dbElement = dbElements[i];
                    dbElement.checked = boxesChecked;
				}
			}

			function validateSelectForm(){
                const dbElements = document.getElementsByName("occid[]");
                for(let i = 0; i < dbElements.length; i++){
                    const dbElement = dbElements[i];
                    if(dbElement.checked) {
                        return true;
                    }
				}
			   	alert("Please select at least one occurrence!");
		      	return false;
			}

			function openIndPopup(occid){
				openPopup('../individual/index.php?occid=' + occid);
			}

			function openEditorPopup(occid){
				openPopup('occurrenceeditor.php?occid=' + occid);
			}

			function openPopup(urlStr){
                let wWidth = 900;
                if(document.getElementById('innertext').offsetWidth){
					wWidth = document.getElementById('innertext').offsetWidth*1.05;
				}
				else if(document.body.offsetWidth){
					wWidth = document.body.offsetWidth*0.9;
				}
                const newWindow = window.open(urlStr, 'popup', 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
                if (newWindow.opener == null) {
                    newWindow.opener = self;
                }
				return false;
			}
			
			function initNomAdjAutocomplete(f){
				$( f.sciname ).autocomplete({ 
					source: "rpc/getspeciessuggest.php", 
					minLength: 3
				});
			}
			
			function initDetAutocomplete(f){
				$( f.sciname ).autocomplete({ 
					source: "rpc/getspeciessuggest.php", 
					minLength: 3,
					change: function() {
						if(f.sciname.value){
							pauseSubmit = true;
							verifyDetSciName(f);
						}
						else{
							f.scientificnameauthorship.value = "";
							f.family.value = "";
							f.tidtoadd.value = "";
						}				
					}
				});
			}
			
			function verifyDetSciName(f){
				$.ajax({
					type: "POST",
					url: "rpc/verifysciname.php",
					dataType: "json",
					data: { term: f.sciname.value }
				}).done(function( data ) {
					if(data){
						f.scientificnameauthorship.value = data.author;
						f.family.value = data.family;
						f.tidtoadd.value = data.tid;
					}
					else{
						alert("WARNING: Taxon not found. It may be misspelled or needs to be added to taxonomic thesaurus by a taxonomic editor.");
						f.scientificnameauthorship.value = "";
						f.family.value = "";
						f.tidtoadd.value = "";
					}
				});
			}
			
			function verifyCatDet(f){
				if(f.sciname.value === ""){
					alert("Scientific Name field must have a value");
					return false;
				}
				if(f.identifiedby.value === ""){
					alert("Determiner field must have a value (enter 'unknown' if not defined)");
					return false;
				}
				if(f.dateidentified.value === ""){
					alert("Determination Date field must have a value (enter 'unknown' if not defined)");
					return false;
				}
				if(pauseSubmit){
                    const date = new Date();
                    let curDate = null;
                    do{
						curDate = new Date(); 
					}
                    while(curDate - date < 5000 && pauseSubmit);
				}
				return true;
			}
			
			function verifyNomDet(f){
                const firstTaxon = document.getElementById("nomsciname").value;
                if(f.sciname.value === ""){
					alert("Scientific Name field must have a value");
					return false;
				}
				if(f.sciname.value === firstTaxon){
					f.sciname.value = '';
					alert("Taxon must be different than taxon to be adjusted.");
					return false;
				}
				if(pauseSubmit){
                    const date = new Date();
                    let curDate = null;
                    do{
						curDate = new Date(); 
					}
                    while(curDate - date < 5000 && pauseSubmit);
				}
				return true;
			}
		</script>
	</head>
	<body>
	<?php
	include(__DIR__ . '/../../header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'>Home</a> &gt;&gt; 
		<?php
        echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Management Panel</a> &gt;&gt; ';
		?>
		<b>Batch Determinations/Nomenclatural Adjustments</b>
	</div>
	<div id="innertext">
		<?php 
		if($isEditor){
			echo '<h2>'.$occManager->getCollName().'</h2>';
			?>
			<div id="tabs" style="margin:0;">
				<ul>
					<li><a href="#batchdet">Batch Determinations</a></li>
					<li><a href="#nomadjust">Nomenclatural Adjustments</a></li>
				</ul>
				
				<div id="batchdet">
					<form name="accqueryform" action="batchdeterminations.php" method="post" onsubmit="return submitAccForm();">
						<fieldset>
							<legend><b>Define Occurrence Recordset</b></legend>
							<div style="margin:3px;">
								<div style="clear:both;padding:8px 0 0 0;">
									*Occurrence list is limited to 400 records
								</div>
								<div style="clear:both;padding:15px 0 0 20px;">
									<div style="float:right;">
										<button name="clearaccform"  type="button" style="margin-right:40px" onclick='clearAccForm();' >Clear Form</button>
									</div>
									<b>Catalog Number:</b>
									<input id="fcatalognumber" name="catalognumber" type="text" style="border-color:green;" />
									<input id="fcollid" name="collid" type="hidden" value="<?php echo $collid; ?>" />
									<input name="recordsubmit" type="submit" value="Add Record" />
								</div>
							</div>
						</fieldset>
					</form>
					<div id="accrecordlistdviv" style="display:<?php echo ($catTBody?'block;':'none;'); ?>none;">
						<form name="accselectform" id="accselectform" action="batchdeterminations.php" method="post" onsubmit="return validateSelectForm();">
							<div style="margin-top: 15px; margin-left: 15px;">
								<input name="accselectall" value="" type="checkbox" onclick="selectAll(this);" checked />
								Select/Deselect all Occurrences
							</div>
							<table class="styledtable" style="font-family:Arial,serif;font-size:12px;">
								<thead>
									<tr>
										<th style="width:25px;text-align:center;">&nbsp;</th>
										<th style="width:125px;text-align:center;">Catalog Number</th>
										<th style="width:300px;text-align:center;">Scientific Name</th>
										<th style="text-align:center;">Collector/Locality</th>
									</tr>
								</thead>
								<tbody id="catrecordstbody"><?php echo ($catTBody?:''); ?></tbody>
							</table>
							<div id="newdetdiv" style="">
								<fieldset style="margin: 15px 15px 0 15px;padding:15px;">
									<legend><b>Add a New Determination</b></legend>
									<div style='margin:3px;'>
										<b>Identification Qualifier:</b>
										<input type="text" name="identificationqualifier" title="e.g. cf, aff, etc" />
									</div>
									<div style='margin:3px;'>
										<b>Scientific Name:</b> 
										<input type="text" id="dafsciname" name="sciname" style="background-color:lightyellow;width:350px;" onfocus="initDetAutocomplete(this.form)" />
										<input type="hidden" id="daftidtoadd" name="tidtoadd" value="" />
										<input type="hidden" name="family" value="" />
									</div>
									<div style='margin:3px;'>
										<b>Author:</b> 
										<input type="text" name="scientificnameauthorship" style="width:200px;" />
									</div>
									<div style='margin:3px;'>
										<b>Confidence of Determination:</b> 
										<select name="confidenceranking">
											<option value="8">High</option>
											<option value="5" selected>Medium</option>
											<option value="2">Low</option>
										</select>
									</div>
									<div style='margin:3px;'>
										<b>Determiner:</b> 
										<input type="text" name="identifiedby" id="identifiedby" style="background-color:lightyellow;width:200px;" />
									</div>
									<div style='margin:3px;'>
										<b>Date:</b> 
										<input type="text" name="dateidentified" id="dateidentified" style="background-color:lightyellow;" onchange="detDateChanged(this.form);" />
									</div>
									<div style='margin:3px;'>
										<b>Reference:</b> 
										<input type="text" name="identificationreferences" style="width:350px;" />
									</div>
									<div style='margin:3px;'>
										<b>Notes:</b> 
										<input type="text" name="identificationremarks" style="width:350px;" />
									</div>
									<div style='margin:3px;'>
										<input type="checkbox" name="makecurrent" value="1" /> Make this the current determination
									</div>
									<div style='margin:3px;'>
										<input type="checkbox" name="printqueue" value="1" /> Add to Annotation Queue
									</div>
									<div style='margin:15px;'>
										<div style="float:left;">
											<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
											<input name="tabtarget" type="hidden" value="0" />
											<input type="submit" name="formsubmit" onclick="verifyCatDet(this.form);" value="Add New Determinations" />
										</div>
									</div>
								</fieldset>
							</div>
						</form>
					</div>
				</div>
				
				<div id="nomadjust">
					<form name="nomqueryform" action="batchdeterminations.php" method="post" onsubmit="return submitNomForm();">
						<fieldset>
							<legend><b>Taxon To Be Adjusted</b></legend>
							<div style="margin:3px;">
								<div style="clear:both;padding:8px 0 0 0;">
									*Occurrence list is limited to 400 records
								</div>
								<div style="clear:both;padding:15px 0 0 20px;">
									<div style="float:right;">
										<button name="clearnomform"  type="button" style="margin-right:15px" onclick='clearNomForm();' >Clear Form</button>
									</div>
									<div style="float:left;width:675px;">
										<b>Taxon:</b>
										<input type="text" id="nomsciname" name="sciname" style="background-color:lightyellow;width:450px;" onfocus="initNomAdjAutocomplete(this.form)" />
										<input id="nomcollid" name="collid" type="hidden" value="<?php echo $collid; ?>" />
										<input name="recordsubmit" id="nomrecordsubmit" type="submit" value="Find Records" />
										<img id="workingcircle" src="../../images/workingcircle.gif" style="display:none;" />
									</div>
								</div>
							</div>
						</fieldset>
					</form>
					<div id="nomrecordlistdviv" style="display:<?php echo ($nomTBody?'block;':'none;'); ?>none;">
						<form name="nomselectform" id="accselectform" action="batchdeterminations.php" method="post" onsubmit="return validateSelectForm();">
							<div style="margin-top: 15px; margin-left: 15px;">
								<input type="checkbox" name="nomselectall" value="" onclick="selectAll(this);" checked />
								Select/Deselect all Occurrences
							</div>
							<table class="styledtable" style="font-family:Arial,serif;font-size:12px;">
								<thead>
									<tr>
										<th style="width:25px;text-align:center;">&nbsp;</th>
										<th style="width:125px;text-align:center;">Catalog Number</th>
										<th style="width:300px;text-align:center;">Scientific Name</th>
										<th style="text-align:center;">Collector/Locality</th>
									</tr>
								</thead>
								<tbody id="nomrecordstbody"><?php echo ($nomTBody?:''); ?></tbody>
							</table>
							<div id="newdetdiv">
								<fieldset style="margin: 15px 15px 0 15px;padding:15px;">
									<legend><b>Adjust To Taxon</b></legend>
									<div style='margin:3px;'>
										<b>Scientific Name:</b> 
										<input type="text" id="dafsciname" name="sciname" style="background-color:lightyellow;width:350px;" onfocus="initDetAutocomplete(this.form);" />
										<input type="hidden" id="daftidtoadd" name="tidtoadd" value="" />
										<input type="hidden" name="family" value="" />
									</div>
									<div style='margin:3px;'>
										<b>Author:</b> 
										<input type="text" name="scientificnameauthorship" style="width:200px;" />
									</div>
									<div style='margin:3px;'>
										<b>Reference:</b> 
										<input type="text" name="identificationreferences" style="width:350px;" />
									</div>
									<div style='margin:3px;'>
										<b>Notes:</b> 
										<input type="text" name="identificationremarks" style="width:350px;" value="" />
									</div>
									<div style='margin:3px;'>
										<input type="checkbox" name="printqueue" value="1" /> Add to Annotation Queue
									</div>
									<div style='margin:15px;'>
										<div style="float:left;">
											<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
											<input name="tabtarget" type="hidden" value="1" />
											<input name="makecurrent" type="hidden" value="1" />
											<input type="submit" name="formsubmit" onclick="verifyNomDet(this.form);" value="Adjust Nomenclature" />
										</div>
									</div>
								</fieldset>
							</div>
						</form>
					</div>
				</div>
			</div>
			<?php
		}
		else{
			?>
			<div style="font-weight:bold;margin:20px;font-size:150%;">
				You do not have permissions to set batch determinations for this collection. 
				Please contact the site administrator to obtain the necessary permissions.
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

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceAttributes.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ' . $GLOBALS['CLIENT_ROOT'] . '/profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:'';
$selectAll = array_key_exists('selectall',$_POST)?$_POST['selectall']:'';
$taxonFilter = array_key_exists('taxonfilter',$_POST)?$_POST['taxonfilter']:'';
$stringFilter = array_key_exists('stringfilter',$_POST)?$_POST['stringfilter']:'';
$tidFilter = array_key_exists('tidfilter',$_POST)?(int)$_POST['tidfilter']:0;
$fieldName = array_key_exists('fieldname',$_POST)?$_POST['fieldname']:'';
$traitID = array_key_exists('traitid',$_POST)?(int)$_POST['traitid']:0;
$submitForm = array_key_exists('submitform',$_POST)?$_POST['submitform']:'';

$collRights = array();
if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS'])) {
    $collRights = $GLOBALS['USER_RIGHTS']['CollAdmin'];
}
if(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS'])) {
    $collRights = array_merge($collRights, $GLOBALS['USER_RIGHTS']['CollEditor']);
}

$isEditor = 0; 
if($GLOBALS['SYMB_UID']){
	if(!$GLOBALS['IS_ADMIN'] && count($collRights) === 1){
        $collid = current($collRights);
	}
	elseif($selectAll){
		$collid = 'all';
	}
	elseif(is_array($collid)){
		if(!$GLOBALS['IS_ADMIN']) {
            $collid = array_intersect($collid, $collRights);
        }
		$collid = implode(',',$collid);
	}
	if($GLOBALS['IS_ADMIN']){
		$isEditor = 1;
	}
	elseif(is_numeric($collid)){
		if(in_array($collid, $collRights, true)) {
            $isEditor = 1;
        }
	}
	elseif($collid){
		$isEditor = 1;
	}
}

$attrManager = new OccurrenceAttributes();
$attrManager->setCollid($collid);
$collArr = $attrManager->getCollectionList($GLOBALS['IS_ADMIN']?'':$collRights);

$statusStr = '';
if($isEditor && $submitForm === 'Batch Assign State(s)' && $collid && $fieldName) {
    $fieldValueArr = array_key_exists('fieldvalue',$_POST)?$_POST['fieldvalue']:'';
    if(!is_array($fieldValueArr)) {
        $fieldValueArr = array($fieldValueArr);
    }
    $stateIDArr = array();
    foreach($_POST as $postKey => $postValue){
        if(strncmp($postKey, 'stateid-', 8) === 0){
            if(is_array($postValue)){
                foreach($postValue as $post){
                    $stateIDArr[] = $post;
                }
            }
            else{
                $stateIDArr[] = $postValue;
            }
        }
    }
    if($stateIDArr && $fieldValueArr && !$attrManager->submitBatchAttributes($traitID, $fieldName, $tidFilter, $stateIDArr, $fieldValueArr, $_POST['notes'], $_POST['reviewstatus'])) {
        $statusStr = $attrManager->getErrorMessage();
    }
}

$fieldArr = array('habitat' => 'Habitat', 'substrate' => 'Substrate', 'occurrenceremarks' => 'Occurrence Remarks (notes)',
	'dynamicproperties' => 'Dynamic Properties', 'verbatimattributes' => 'Verbatim Attributes (description)',
	'behavior' => 'Behavior', 'reproductivecondition' => 'Reproductive Condition', 'lifestage' => 'Life Stage', 
	'sex' => 'Sex');
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<title>Occurrence Attribute Mining Tool</title>
		<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script src="../../js/jquery.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">
            function verifyFilterForm(f){
				if(f.traitid.value === ""){
					alert("You must select a trait");
					return false;
				}
				if(f.fieldname.value === ""){
					alert("A source field must be selected");
					return false;
				}
				return true;
			}

			function verifyMiningForm(f){
				if(f.elements["fieldvalue[]"].selectedIndex === -1){
					alert("You muct select at least one field value");
					return false;
				}

                let formVerified = false;
                $('input[name^="stateid-"]').each(function(){
					if(this.checked === true){
						formVerified = true;
						return false;
					}
				});
				if(!formVerified){
					alert("Please choose at least one state to assign");
					return false;
				}
				return true;
			}

			function selectAll(cb){
                let boxesChecked = true;
                if(!cb.checked) {
                    boxesChecked = false;
                }
                const dbElements = cb.form.elements["collid[]"];
                for(let i = 0; i < dbElements.length; i++){
                    const dbElement = dbElements[i];
                    dbElement.checked = boxesChecked;
				}
			}

			function verifyCollForm(f){
                const dbElements = f.elements["collid[]"];
                for(let i = 0; i < dbElements.length; i++){
                    const dbElement = dbElements[i];
                    if(dbElement.checked === true) {
                        return true;
                    }
				}
				alert('Select at last on collect to harvest from');
				return false;
			}

			function collidChanged(f){
				f.selectall.checked = false;
			}

			function toggleCollections(){
				toggle("collDiv");
				toggle("displayDiv");
			}
		</script>
		<script src="../../js/symb/collections.traitattr.js" type="text/javascript"></script>
		<script src="../../js/symb/shared.js?ver=20220310" type="text/javascript"></script>
	</head>
	<body style="width:900px">
		<?php
		include(__DIR__ . '/../../header.php');
		?>
		<div class="navpath">
			<a href="../../index.php">Home</a> &gt;&gt;
			<?php 
			if(is_numeric($collid)) {
                echo '<a href="../misc/collprofiles.php?collid=' . $collid . '&emode=1">Collection Management</a> &gt;&gt;';
            }
			if($GLOBALS['IS_ADMIN'] || count($collRights) > 1) {
                echo '<a href="attributemining.php">Adjust Collection Selection</a> &gt;&gt;';
            }
			?>
			<b>Attribute Mining Tool</b>
		</div>
		<?php 
		if($statusStr){
			echo '<div style="color:red">';
			echo $statusStr;
			echo '</div>';
		}
		?>
		<div id="innertext">
			<?php 
			if($collid){
				if($collid === 'all'){
					echo '<h2 class="heading">Searching All Collections</h2>';
				}
				elseif(is_numeric($collid)){
					echo '<h2 class="heading">'.$collArr[$collid].'</h2>';
				}
				else{
					$collIdArr = explode(',',$collid);
					echo '<fieldset>';
					echo '<legend style="font-weight:bold;font-size:130%"><a href="#" style="" onclick="toggleCollections()">Searching '.count($collIdArr).' Collections</a></legend>';
					echo '<div id="collDiv" style="display:none;padding:10px;">';
					foreach($collIdArr as $id){
						echo '<div>'.$collArr[$id].'</div>';
					}
					echo '</div>';
					echo '<div id="displayDiv" style="margin:0 20px;"><a href="#" onclick="toggleCollections()">click to display collection list</a></div>';
					echo '</fieldset>';
				}
				?>
				<div style="width:650px;">
					<fieldset style="margin:15px;padding:15px;">
						<legend><b>Harvesting Filter</b></legend>
						<form name="filterform" method="post" action="attributemining.php" onsubmit="return verifyFilterForm(this)" >
							<div>
								Occurrence trait: 
								<select name="traitid">
									<option value="">Select Target Trait (required)</option>
									<option value="">------------------------------------</option>
									<?php 
									$traitNameArr = $attrManager->getTraitNames();
									if($traitNameArr){
										foreach($traitNameArr as $ID => $aName){
											echo '<option value="'.$ID.'" '.($traitID === $ID?'SELECTED':'').'>'.$aName.'</option>';
										}
									}
									else{
										echo '<option value="0">No attributes are available</option>';
									}
									?>
								</select>
							</div>
							<div>
								Verbatim text source: 
								<select name="fieldname">
									<option value="">Select Source Field (required)</option>
									<option value="">------------------------------------</option>
									<?php 
									foreach($fieldArr as $k => $fName){
										echo '<option value="'.$k.'" '.($k === $fieldName?'SELECTED':'').'>'.$fName.'</option>';
									}
									?>
								</select>
							</div>
							<div>
								Filter by text (optional): 
								<input name="stringfilter" type="text" value="<?php echo $stringFilter; ?>" />
							</div>
							<div style="float:right;margin-right:20px">
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input id="filtersubmit" name="submitform" type="submit" value="Get Field Values" />
							</div>
							<div>
								Filter by taxon (optional): 
								<input id="taxonfilter" name="taxonfilter" type="text" value="<?php echo $taxonFilter; ?>" /> 
								<input id="tidfilter" name="tidfilter" type="hidden" value="<?php echo $tidFilter; ?>" />
								<span id="verify-span" style="display:none;font-weight:bold;color:green;">verifying taxonomy...</span>
								<span id="notvalid-span" style="display:none;font-weight:bold;color:red;">taxon not valid...</span>
							</div>
						</form>
					</fieldset>
				</div>
				<?php 
				if($traitID && $fieldName){
					$valueArr = $attrManager->getFieldValueArr($traitID, $fieldName, $tidFilter, $stringFilter);
					?>
					<div style="width:600px">
						<fieldset style="margin:15px;padding:15px">
							<legend><b><?php echo $fieldArr[$fieldName]; ?></b></legend>
							<form name="miningform" method="post" action="attributemining.php" onsubmit="return verifyMiningForm(this)">
								<div style="margin:5px;">
									<b>Select Source Field Value(s)</b> - hold down control or shift buttons to select more than one value<br/>
									<select name="fieldvalue[]" size="15" multiple="multiple" style="width:100%">
										<?php 
										foreach($valueArr as $v){
											if($v) {
                                                echo '<option value="' . $v . '">' . $v . '</option>';
                                            }
										}
										?>
									</select>
								</div>
								<div>
									<?php 
									$traitArr = $attrManager->getTraitArr($traitID);
									$attrManager->echoFormTraits($traitID);
									?>
								</div>
								<div style="margin: 5px">
									Status: <select name="reviewstatus">
										<option value="0">----------------------</option>
										<option value="5">Expert Needed</option>
									</select>
								</div>
								<div style="margin:15px;">
									<input name="stringfilter" type="hidden" value="<?php echo $stringFilter; ?>" />
									<input name="taxonfilter" type="hidden" value="<?php echo $taxonFilter; ?>" />
									<input name="tidfilter" type="hidden" value="<?php echo $tidFilter; ?>" />
									<input name="traitid" type="hidden" value="<?php echo $traitID; ?>" />
									<input name="fieldname" type="hidden" value="<?php echo $fieldName; ?>" />
									<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
									<input name="submitform" type="submit" value="Batch Assign State(s)" />
								</div>
							</form>
						</fieldset>
					</div>
					<?php
				}
			}
			else{
				?>
				<div style="font-weight:bold;">Select the collections you wish to code for:</div>
				<div style="margin:15px">
					<form name="collform" method="post" action="attributemining.php" onsubmit="return verifyCollForm(this)">
						<input name="selectall" type="checkbox" value="1" onchange="selectAll(this)" /> <b>Select/Deselect All</b><br/>
						<?php 
						foreach($collArr as $id => $collName){
							echo '<input name="collid[]" type="checkbox" value="'.$id.'" onchange="collidChanged(this.form)" />';
							echo $collName;
							echo '<br/>';
						}
						?>
						<div style="margin:15px">
							<input type="submit" name="submitform" value="Harvest from Collections" />
						</div>
					</form>
				</div>
				<?php 
			} 
			?> 
		</div>
	</body>
</html>

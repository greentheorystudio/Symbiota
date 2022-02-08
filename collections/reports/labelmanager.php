<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceLabel.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = (int)$_REQUEST['collid'];
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);

$reportsWritable = false;
if(is_writable($GLOBALS['SERVER_ROOT'].'/temp/report')){
	$reportsWritable = true;
}

$isEditor = 0;
$occArr = array();
$annoArr = array();
if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}
elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
	$isEditor = 1;
}
if($isEditor){
	if($action === 'Filter Occurrence Records'){
		$occArr = $labelManager->queryOccurrences($_POST);
	}
}
$labelFormatArr = $labelManager->getLabelFormatArr(true);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
	    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Print Labels</title>
		<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
        <style>
            .checkboxLabel{
                font-weight: bold;
                margin-left: 3px;
            }
        </style>
        <script src="../../js/all.min.js" type="text/javascript"></script>
		<script src="../../js/jquery.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">
            $(document).ready(function() {
				function split( val ) {
					return val.split( /,\s*/ );
				}
				function extractLast( term ) {
					return split( term ).pop();
				}

				$( "#taxa" )
				.bind( "keydown", function( event ) {
					if ( event.keyCode === $.ui.keyCode.TAB &&
							$( this ).data( "autocomplete" ).menu.active ) {
						event.preventDefault();
					}
				})
				.autocomplete({
					source: function( request, response ) {
						$.getJSON( "../rpc/taxalist.php", {
							term: extractLast( request.term )
						}, response );
					},
					search: function() {
                        const term = extractLast(this.value);
                        if ( term.length < 4 ) {
							return false;
						}
					},
					focus: function() {
						return false;
					},
					select: function( event, ui ) {
                        const terms = split(this.value);
                        terms.pop();
						terms.push( ui.item.value );
						this.value = terms.join( ", " );
						return false;
					}
				},{});
			});

			function selectAll(cb){
                let boxesChecked = true;
                if(!cb.checked){
					boxesChecked = false;
				}
                const dbElements = document.getElementsByName("occid[]");
                for(let i = 0; i < dbElements.length; i++){
                    const dbElement = dbElements[i];
                    dbElement.checked = boxesChecked;
				}
			}

			function validateQueryForm(f){
				return validateDateFields(f);
			}

			function validateDateFields(f){
                let status = true;
                const validformat1 = /^\s*\d{4}-\d{2}-\d{2}\s*$/;
                if(f.date1.value !== "" && !validformat1.test(f.date1.value)) {
                    status = false;
                }
				if(f.date2.value !== "" && !validformat1.test(f.date2.value)) {
				    status = false;
				}
				if(!status) {
				    alert("Date entered must follow the format YYYY-MM-DD");
				}
				return status;
			}

            function validateSelectForm(f){
                let checkvalid = false;
                let formatvalid = false;
                const dbElements = document.getElementsByName("occid[]");
                for(i = 0; i < dbElements.length; i++){
                    var dbElement = dbElements[i];
                    if(dbElement.checked){
                        const quantityObj = document.getElementsByName("q-"+dbElement.value);
                        if(quantityObj && quantityObj[0].value > 0) {
                            checkvalid = true;
                        }
                    }
                }
                if(document.getElementById('labelformatindex').value){
                    formatvalid = true;
                }
                if(checkvalid && formatvalid){
                    return true;
                }
                alert("Please select at least one occurrence record and a label format.");
                return false;
            }

			function openIndPopup(occid){
				openPopup('../individual/index.php?occid=' + occid);
			}

			function openEditorPopup(occid){
				openPopup('../editor/occurrenceeditor.php?occid=' + occid);
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

            function changeFormExport(buttonElem, action, target){
                const f = document.getElementById('selectform');
                if(action == "labelsbrowser.php" && buttonElem.value == "Print in Browser"){
                    if(!f["labelformatindex"] || f["labelformatindex"].value == ""){
                        alert("Please select a Label Format");
                        return false;
                    }
                }
                f.action = action;
                f.target = target;
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
        if(stripos(strtolower($labelManager->getMetaDataTerm('colltype')), 'observation') !== false){
            echo '<a href="../../profile/viewprofile.php?tabindex=1">Personal Management Menu</a> &gt;&gt; ';
        }
        else{
            echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Management Panel</a> &gt;&gt; ';
        }
		?>
		<b>Print Labels</b>
	</div>
	<div id="innertext">
		<?php 
		if($isEditor){
			if(!$reportsWritable){
				?>
				<div style="padding:5px;">
					<span style="color:red;">Please contact the site administrator to make temp/report folder writable in order to export to docx files.</span>
				</div>
				<?php 
			}
			$isGeneralObservation = ($labelManager->getMetaDataTerm('colltype') === 'General Observations');
			echo '<h2>'.$labelManager->getCollName().'</h2>';
			?>
            <div id="labels">
                <form name="datasetqueryform" action="labelmanager.php" method="post" onsubmit="return validateQueryForm(this)">
                    <fieldset>
                        <legend><b>Define Occurrence Recordset</b></legend>
                        <div style="clear:both;width:100%;display:flex;">
                            <div title="Scientific name as entered in database.">
                                Scientific Name:
                                <input type="text" name="taxa" id="taxa" size="60" value="<?php echo (array_key_exists('taxa',$_REQUEST)?$_REQUEST['taxa']:''); ?>" />
                            </div>
                        </div>
                        <div style="margin-top:3px;clear:both;width:100%;display:flex;">
                            <div title="Full or last name of collector as entered in database.">
                                Collector:
                                <input type="text" name="recordedby" style="width:150px;" value="<?php echo (array_key_exists('recordedby',$_REQUEST)?$_REQUEST['recordedby']:''); ?>" />
                            </div>
                            <div style="margin-left:20px;" title="Separate multiple terms by comma and ranges by ' - ' (space before and after dash required), e.g.: 3542,3602,3700 - 3750">
                                Record Number(s):
                                <input type="text" name="recordnumber" style="width:150px;" value="<?php echo (array_key_exists('recordnumber',$_REQUEST)?$_REQUEST['recordnumber']:''); ?>" />
                            </div>
                            <div style="margin-left:20px;" title="Separate multiple terms by comma and ranges by ' - ' (space before and after dash required), e.g.: 3542,3602,3700 - 3750">
                                Catalog Number(s):
                                <input type="text" name="identifier" style="width:150px;" value="<?php echo (array_key_exists('identifier',$_REQUEST)?$_REQUEST['identifier']:''); ?>" />
                            </div>
                        </div>
                        <div style="margin-top:3px;clear:both;width:100%;display:flex;">
                            <div>
                                Entered by:
                                <input type="text" name="recordenteredby" value="<?php echo (array_key_exists('recordenteredby',$_REQUEST)?$_REQUEST['recordenteredby']:''); ?>" style="width:100px;" title="login name of data entry person" />
                            </div>
                            <div style="margin-left:20px;">
                                Date range:
                                <input type="text" name="date1" style="width:100px;" value="<?php echo (array_key_exists('date1',$_REQUEST)?$_REQUEST['date1']:''); ?>" onchange="validateDateFields(this.form)" /> to
                                <input type="text" name="date2" style="width:100px;" value="<?php echo (array_key_exists('date2',$_REQUEST)?$_REQUEST['date2']:''); ?>" onchange="validateDateFields(this.form)" />
                                <select name="datetarget">
                                    <option value="dateentered">Date Entered</option>
                                    <option value="datelastmodified" <?php echo (isset($_POST['datetarget']) && $_POST['datetarget'] === 'datelastmodified'?'SELECTED':''); ?>>Date Modified</option>
                                    <option value="eventdate"<?php echo (isset($_POST['datetarget']) && $_POST['datetarget'] === 'eventdate'?'SELECTED':''); ?>>Date Collected</option>
                                </select>
                            </div>
                        </div>
                        <div style="margin-top:3px;clear:both;width:100%;display:flex;">
                            Label Projects:
                            <select name="labelproject" >
                                <option value="">All Projects</option>
                                <option value="">-------------------------</option>
                                <?php
                                $lProj = '';
                                if(array_key_exists('labelproject',$_REQUEST)) {
                                    $lProj = $_REQUEST['labelproject'];
                                }
                                $lProjArr = $labelManager->getLabelProjects();
                                foreach($lProjArr as $projStr){
                                    echo '<option '.($lProj === $projStr?'SELECTED':'').'>'.$projStr.'</option>'."\n";
                                }
                                ?>
                            </select>
                            <!--
                                Dataset Projects:
                                <select name="datasetproject" >
                                    <option value=""></option>
                                    <option value="">-------------------------</option>
                                    <?php
                                    /*
                                    $datasetProj = '';
                                    if(array_key_exists('datasetproject',$_REQUEST)) {
                                        $datasetProj = $_REQUEST['datasetproject'];
                                    }
                                    $dProjArr = $labelManager->getDatasetProjects();
                                    foreach($dProjArr as $dsid => $dsProjStr){
                                        echo '<option id="'.$dsid.'" '.($datasetProj==$dsProjStr?'SELECTED':'').'>'.$dsProjStr.'</option>'."\n";
                                    }
                                    */
                                    ?>
                                </select>
                            -->
                            <?php
                            echo '<span style="margin-left:15px;"><input name="extendedsearch" type="checkbox" value="1" '.(array_key_exists('extendedsearch', $_POST)?'checked':'').' /></span> ';
                            if($isGeneralObservation) {
                                echo 'Search outside user profile';
                            }
                            else {
                                echo 'Search within all collections';
                            }
                            ?>
                        </div>
                        <div style="margin-top:3px;clear:both;width:100%;display:flex;">
                            <div>
                                <input type="hidden" name="collid" value="<?php echo $collid; ?>" />
                                <input type="submit" name="submitaction" value="Filter Occurrence Records" />
                            </div>
                            <div style="margin-left:20px;">
                                *Occurrence return is limited to 400 records
                            </div>
                        </div>
                    </fieldset>
                </form>
                <div style="clear:both;">
                    <?php
                    if($action === 'Filter Occurrence Records'){
                        if($occArr){
                            ?>
                            <form name="selectform" id="selectform" action="labelsbrowser.php" method="post" onsubmit="return validateSelectForm(this);">
                                <table class="styledtable" style="font-family:Arial;font-size:12px;">
                                    <tr>
                                        <th title="Select/Deselect all Occurrences"><input type="checkbox" onclick="selectAll(this);" /></th>
                                        <th title="Label quantity">Qty</th>
                                        <th>Collector</th>
                                        <th>Scientific Name</th>
                                        <th>Locality</th>
                                    </tr>
                                    <?php
                                    $trCnt = 0;
                                    foreach($occArr as $occId => $recArr){
                                        $trCnt++;
                                        ?>
                                        <tr <?php echo ($trCnt%2?'class="alt"':''); ?>>
                                            <td>
                                                <input type="checkbox" name="occid[]" value="<?php echo $occId; ?>" />
                                            </td>
                                            <td>
                                                <input type="text" name="q-<?php echo $occId; ?>" value="<?php echo $recArr['q']; ?>" style="width:35px;border:inset;" title="Label quantity" />
                                            </td>
                                            <td>
                                                <a href="#" onclick="openIndPopup(<?php echo $occId; ?>); return false;">
                                                    <?php echo $recArr['c']; ?>
                                                </a>
                                                <?php
                                                if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($recArr['collid'], $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) || (array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($recArr['collid'], $GLOBALS['USER_RIGHTS']['CollEditor'], true))){
                                                    if(!$isGeneralObservation || (int)$recArr['uid'] === (int)$GLOBALS['SYMB_UID']){
                                                        ?>
                                                        <a href="#" onclick="openEditorPopup(<?php echo $occId; ?>); return false;">
                                                            <i style="width:15px;height:15px;" class="far fa-edit"></i>
                                                        </a>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $recArr['s']; ?>
                                            </td>
                                            <td>
                                                <?php echo $recArr['l']; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <fieldset style="margin-top:15px;">
                                    <legend><b>Label Printing</b></legend>
                                    <div style="clear:both;width:100%;">
                                        <div>
                                            <b>Label Format:</b>
                                            <?php
                                            echo '<span title="Open label format manager"><a href="labelprofile.php?collid='.$collid.'"><i style="width:15px;height:15px;" class="far fa-edit"></i></a></span>';
                                            ?>
                                            <span style="margin-left: 15px;">
                                                    <select name="labelformatindex" id="labelformatindex">
                                                        <option value="">Select a Label Format</option>
                                                        <?php
                                                        foreach($labelFormatArr as $cat => $catArr){
                                                            foreach($catArr as $k => $labelArr){
                                                                echo '<option value="'.$cat.'-'.$k.'">'.$labelArr['title'].'</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </span>
                                        </div>
                                    </div>
                                    <div style="margin-top:3px;clear:both;width:100%;display:flex;">
                                        <input type="hidden" name="collid" value="<?php echo $collid; ?>" />
                                        <div>
                                            <input type="submit" name="submitaction" onclick="return changeFormExport(this,'labelsbrowser.php','_blank');" value="Print in Browser" />
                                        </div>
                                        <div style="margin-left:10px">
                                            <input type="submit" name="submitaction" onclick="return changeFormExport(this,'labelsbrowser.php','_self');" value="Export to CSV" />
                                        </div>
                                        <?php
                                        if($reportsWritable){
                                            ?>
                                            <div style="margin-left:10px">
                                                <input type="submit" name="submitaction" onclick="return changeFormExport(this,'labelsword.php','_self');" value="Export to DOCX" />
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </fieldset>
                            </form>
                            <?php
                        }
                        else{
                            ?>
                            <div style="font-weight:bold;margin:20px;font-size:150%;">
                                No records matched query parameters.
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
			<?php
		}
		else{
			?>
			<div style="font-weight:bold;margin:20px;font-size:150%;">
				You do not have permissions to print labels for this collection.
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

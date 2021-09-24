<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceLabel.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = (int)$_REQUEST['collid'];
$tabTarget = array_key_exists('tabtarget',$_REQUEST)?(int)$_REQUEST['tabtarget']:0;
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
	$annoArr = $labelManager->getAnnoQueue();
	if($action === 'Filter Specimen Records'){
		$occArr = $labelManager->queryOccurrences($_POST);
	}
}
$labelFormatArr = $labelManager->getLabelFormatArr(true);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
	    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Specimen Label Manager</title>
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
            <?php
            if($labelFormatArr) {
                echo 'var labelFormatObj = ' . json_encode($labelFormatArr) . ';';
            }
            ?>

            $(document).ready(function() {
				if(!navigator.cookieEnabled){
					alert("Your browser cookies are disabled. To be able to login and access your profile, they must be enabled for this domain.");
				}

				function split( val ) {
					return val.split( /,\s*/ );
				}
				function extractLast( term ) {
					return split( term ).pop();
				}

				$("#tabs").tabs({
					active: <?php echo (is_numeric($tabTarget)?$tabTarget:'0'); ?>
				});

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

			function selectAllAnno(cb){
                let boxesChecked = true;
                if(!cb.checked){
					boxesChecked = false;
				}
                const dbElements = document.getElementsByName("detid[]");
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
                var dbElements = document.getElementsByName("occid[]");
                for(i = 0; i < dbElements.length; i++){
                    var dbElement = dbElements[i];
                    if(dbElement.checked){
                        var quantityObj = document.getElementsByName("q-"+dbElement.value);
                        if(quantityObj && quantityObj[0].value > 0) return true;
                    }
                }
                alert("At least one specimen checkbox needs to be selected with a label quantity greater than 0");
                return false;
            }

			function validateAnnoSelectForm(){
                const dbElements = document.getElementsByName("detid[]");
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
                const f = buttonElem.form;
                if(action == "labelsbrowser.php" && buttonElem.value == "Print in Browser"){
                    if(!f["labelformatindex"] || f["labelformatindex"].value == ""){
                        alert("Please select a Label Format Profile");
                        return false;
                    }
                }
                else if(action == "labelsword.php" && f.labeltype.valye == "packet"){
                    alert("Packet labels are not yet available as a Word document");
                    return false;
                }
                if(f.bconly && f.bconly.checked && action == "labelsbrowser.php") {
                    action = "barcodes.php";
                }
                f.action = action;
                f.target = target;
                return true;
            }

			function changeAnnoFormExport(action,target){
				document.annoselectform.action = action;
				document.annoselectform.target = target;
			}

			function checkPrintOnlyCheck(f){
				if(f.bconly.checked){
					f.speciesauthors.checked = false;
					f.catalognumbers.checked = false;
					f.bc.checked = false;
				}
			}

			function checkBarcodeCheck(f){
				if(f.bc.checked || f.speciesauthors.checked || f.catalognumbers.checked){
					f.bconly.checked = false;
				}
			}

			function labelFormatChanged(selObj){
				if(selObj && labelFormatObj){
					const catStr = selObj.value.substring(0,1);
					const labelIndex = selObj.value.substring(2);
					const f = document.selectform;
					if(catStr != ''){
						f.hprefix.value = labelFormatObj[catStr][labelIndex].labelHeader.prefix;
						const midIndex = labelFormatObj[catStr][labelIndex].labelHeader.midText;
						document.getElementById("hmid"+midIndex).checked = true;
						f.hsuffix.value = labelFormatObj[catStr][labelIndex].labelHeader.suffix;
						f.lfooter.value = labelFormatObj[catStr][labelIndex].labelFooter.textValue;
						f.labeltype.value = labelFormatObj[catStr][labelIndex].pageLayout;
					}
				}
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
		<b>Label/Annotation Printing</b>
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
			<div id="tabs" style="margin:0;">
				<ul>
					<li><a href="#labels">Labels</a></li>
					<li><a href="#annotations">Annotations</a></li>
				</ul>

                <div id="labels">
                    <form name="datasetqueryform" action="labelmanager.php" method="post" onsubmit="return validateQueryForm(this)">
                        <fieldset>
                            <legend><b>Define Specimen Recordset</b></legend>
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
                                    if(array_key_exists('datasetproject',$_REQUEST)) $datasetProj = $_REQUEST['datasetproject'];
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
                                    <input type="submit" name="submitaction" value="Filter Specimen Records" />
                                </div>
                                <div style="margin-left:20px;">
                                    * Specimen return is limited to 400 records
                                </div>
                            </div>
                        </fieldset>
                    </form>
                    <div style="clear:both;">
                        <?php
                        if($action === 'Filter Specimen Records'){
                            if($occArr){
                                ?>
                                <form name="selectform" id="selectform" action="labelsbrowser.php" method="post" onsubmit="return validateSelectForm(this);">
                                    <table class="styledtable" style="font-family:Arial;font-size:12px;">
                                        <tr>
                                            <th title="Select/Deselect all Specimens"><input type="checkbox" onclick="selectAll(this);" /></th>
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
                                                    <input type="text" name="q-<?php echo $occId; ?>" value="<?php echo $recArr['q']; ?>" style="width:20px;border:inset;" title="Label quantity" />
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
                                                <b>Label Profiles:</b>
                                                <?php
                                                echo '<span title="Open label profile manager"><a href="labelprofile.php?collid='.$collid.'"><i style="width:15px;height:15px;" class="far fa-edit"></i></a></span>';
                                                ?>
                                            </div>
                                            <div style="clear:both;margin-top:2px;">
                                                <div>
                                                    <select name="labelformatindex" onchange="labelFormatChanged(this)">
                                                        <option value="">Select a Label Format</option>
                                                        <?php
                                                        foreach($labelFormatArr as $cat => $catArr){
                                                            foreach($catArr as $k => $labelArr){
                                                                echo '<option value="'.$cat.'-'.$k.'">'.$labelArr['title'].'</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <?php
                                                if(!$labelFormatArr) {
                                                    echo '<b>label profiles have not yet been set within portal</b>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div style="margin-top:3px;clear:both;width:100%;display:flex;">
                                            <div><b>Label Type:</b></div>
                                            <div style="margin-left:5px;">
                                                <select name="labeltype">
                                                    <option value="1">1 columns per page</option>
                                                    <option value="2" selected>2 columns per page</option>
                                                    <option value="3">3 columns per page</option>
                                                    <option value="4">4 columns per page</option>
                                                    <option value="5">5 columns per page</option>
                                                    <option value="6">6 columns per page</option>
                                                    <option value="7">7 columns per page</option>
                                                    <option value="packet">Packet labels</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div style="margin-top:3px;clear:both;width:100%;display:flex;">
                                            <input type="hidden" name="collid" value="<?php echo $collid; ?>" />
                                            <div>
                                                <input type="submit" name="submitaction" onclick="return changeFormExport(this,'labelsbrowser.php','_blank');" value="Print in Browser" <?php echo ($labelFormatArr?'':'DISABLED title="Browser based label printing has not been activated within the portal. Contact Portal Manager to activate this feature."'); ?> />
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

				<div id="annotations">
					<div>
						<?php 
						if($annoArr){
							?>
							<form name="annoselectform" id="annoselectform" action="defaultannotations.php" method="post" onsubmit="return validateAnnoSelectForm();">
								<div style="margin-top: 15px; margin-left: 15px;">
									<input name="" value="" type="checkbox" onclick="selectAllAnno(this);" />
									Select/Deselect all Specimens
								</div>
								<table class="styledtable" style="font-family:Arial,serif;font-size:12px;">
									<tr>
										<th style="width:25px;text-align:center;"></th>
										<th style="width:25px;text-align:center;">#</th>
										<th style="width:125px;text-align:center;">Collector</th>
										<th style="width:300px;text-align:center;">Scientific Name</th>
										<th style="width:400px;text-align:center;">Determination</th>
									</tr>
									<?php 
									$trCnt = 0;
									foreach($annoArr as $detId => $recArr){
										$trCnt++;
										?>
										<tr <?php echo (($trCnt%2)?'class="alt"':''); ?>>
											<td>
												<input type="checkbox" name="detid[]" value="<?php echo $detId; ?>" />
											</td>
											<td>
												<input type="text" name="q-<?php echo $detId; ?>" value="1" style="width:20px;border:inset;" />
											</td>
											<td>
												<a href="#" onclick="openIndPopup(<?php echo $recArr['occid']; ?>); return false;">
													<?php echo $recArr['collector']; ?>
												</a>
												<a href="#" onclick="openEditorPopup(<?php echo $recArr['occid']; ?>); return false;">
                                                    <i style="height:20px;width:20px;" class="far fa-edit"></i>
												</a>
											</td>
											<td>
												<?php echo $recArr['sciname']; ?>
											</td>
											<td>
												<?php echo $recArr['determination']; ?>
											</td>
										</tr>
										<?php 
									}
									?>
								</table>
								<fieldset style="margin-top:15px;">
									<legend><b>Annotation Printing</b></legend>
									<div style="float:left;">
										<div style="margin:4px;">
											<b>Header:</b>
											<input type="text" name="lheading" value="<?php echo $labelManager->getAnnoCollName(); ?>" style="width:450px" />
										</div>
										<div style="margin:4px;">
											<b>Footer:</b> 
											<input type="text" name="lfooter" value="" style="width:450px" />
										</div>
										<div style="margin:4px;">
											<input type="checkbox" name="speciesauthors" value="1" onclick="" />
											<b>Print species authors for infraspecific taxa</b> 
										</div>
										<div style="margin:4px;">
											<input type="checkbox" name="clearqueue" value="1" onclick="" />
											<b>Remove selected annotations from queue</b> 
										</div>
									</div>
									<div style="float:right;">
										<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
										<input type="submit" name="submitaction" onclick="changeAnnoFormExport('defaultannotations.php','_blank');" value="Print in Browser" />
										<?php
										if($reportsWritable){
											?>
											<br/><br/>
											<input type="submit" name="submitaction" onclick="changeAnnoFormExport('defaultannotationsexport.php','_self');" value="Export to DOCX" />
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
								There are no annotations queued to be printed.
							</div>
							<?php 
						}
						?>
					</div>
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

<?php
/** @var string $qCustomField1 */
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceEditorManager.php');
include_once(__DIR__ . '/../../classes/SOLRManager.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$collId = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$recLimit = array_key_exists('reclimit',$_REQUEST)?(int)$_REQUEST['reclimit']:1000;
$occIndex = array_key_exists('occindex',$_REQUEST)?(int)$_REQUEST['occindex']:null;
$ouid = array_key_exists('ouid',$_REQUEST)?(int)$_REQUEST['ouid']:0;
$crowdSourceMode = array_key_exists('csmode',$_REQUEST)?(int)$_REQUEST['csmode']:0;
$reset = (array_key_exists('reset', $_REQUEST) && $_REQUEST['reset']);
$action = array_key_exists('submitaction',$_REQUEST)?htmlspecialchars($_REQUEST['submitaction']):'';

$occManager = new OccurrenceEditorManager();
$solrManager = new SOLRManager();

if($crowdSourceMode) {
    $occManager->setCrowdSourceMode(1);
}

$isEditor = 0;
$displayQuery = 0;
$isGenObs = 0;
$collMap = array();
$recArr = array();
$headerMapBase = array('dbpk' => 'dbpk','institutioncode'=>'Institution Code (override)','collectioncode'=>'Collection Code (override)',
	'ownerinstitutioncode'=>'Owner Code (override)','catalognumber' => 'Catalog Number',
	'othercatalognumbers' => 'Other Catalog #','family' => 'Family','identificationqualifier' => 'ID Qualifier',
	'sciname' => 'Scientific Name','scientificnameauthorship'=>'Author','recordedby' => 'Collector','recordnumber' => 'Number',
	'associatedcollectors' => 'Associated Collectors','eventdate' => 'Event Date','verbatimeventdate' => 'Verbatim Date',
	'identificationremarks' => 'Identification Remarks','taxonremarks' => 'Taxon Remarks','identifiedby' => 'Identified By',
	'dateidentified' => 'Date Identified', 'identificationreferences' => 'Identification References',
	'country' => 'Country','stateprovince' => 'State/Province','county' => 'County','municipality' => 'Municipality',
	'locality' => 'Locality','decimallatitude' => 'Latitude', 'decimallongitude' => 'Longitude','verbatimcoordinates' => 'Verbatim Coordinates',
    'minimumelevationinmeters' => 'Elev. Min. (m)','maximumelevationinmeters' => 'Elev. Max. (m)','verbatimelevation' => 'Verbatim Elev.',
    'geodeticdatum' => 'Datum','coordinateuncertaintyinmeters' => 'Uncertainty In Meters',
	'georeferencedby' => 'Georeferenced By','georeferenceprotocol' => 'Georeference Protocol','georeferencesources' => 'Georeference Sources',
	'georeferenceverificationstatus' => 'Georef Verification Status','georeferenceremarks' => 'Georef Remarks',
	'minimumdepthinmeters' => 'Depth. Min. (m)','maximumdepthinmeters' => 'Depth. Max. (m)','verbatimdepth' => 'Verbatim Depth',
	'habitat' => 'Habitat','verbatimsciname' => 'Host','substrate' => 'Substrate','occurrenceremarks' => 'Notes (Occurrence Remarks)','associatedtaxa' => 'Associated Taxa',
	'verbatimattributes' => 'Verbatim Attributes','lifestage' => 'Life Stage', 'sex' => 'Sex', 'individualcount' => 'Individual Count',
	'samplingprotocol' => 'Sampling Protocol', 'preparations' => 'Preparations', 'reproductivecondition' => 'Reproductive Condition',
	'typestatus' => 'Type Status','cultivationstatus' => 'Cultivation Status','establishmentmeans' => 'Establishment Means',
	'disposition' => 'Disposition','duplicatequantity' => 'Duplicate Qty','datelastmodified' => 'Date Last Modified',
	'processingstatus' => 'Processing Status','recordenteredby' => 'Entered By','basisofrecord' => 'Basis Of Record');
$headMap = array();

$qryCnt = 0;
$statusStr = '';
$navStr = '';

if($GLOBALS['SYMB_UID']){
	$occManager->setCollId($collId);
	$collMap = $occManager->getCollMap();
	if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
		$isEditor = 1;
	}

	if($collMap && $collMap['colltype'] === 'General Observations') {
        $isGenObs = 1;
    }
	if(!$isEditor && ((array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollEditor'], true)) || ($isGenObs && ($action || $occManager->getObserverUid() === $GLOBALS['SYMB_UID'])))){
        $isEditor = 2;
	}

	if(array_key_exists('bufieldname',$_POST)){
		if($ouid){
			$occManager->setQueryVariables(array('ouid' => $ouid));
		}
		else{
			$occManager->setQueryVariables();
		}
		$occManager->setSqlWhere();
		$statusStr = $occManager->batchUpdateField($_POST['bufieldname'],$_POST['buoldvalue'],$_POST['bunewvalue'],$_POST['bumatch']);
        if($GLOBALS['SOLR_MODE']) {
            $solrManager->updateSOLR();
        }
	}

	if($ouid){
		$occManager->setQueryVariables(array('ouid' => $ouid));
		$occManager->setSqlWhere(0,$recLimit);
		$qryCnt = $occManager->getQueryRecordCount();
	}
	elseif($occIndex !== null){
        if(!$reset) {
            $occManager->setQueryVariables();
        }
		$occManager->setSqlWhere($occIndex,$recLimit);
		$qryCnt = $occManager->getQueryRecordCount(1);
	}
	else{
        if(isset($_SESSION['editorquery'])){
            unset($_SESSION['editorquery']);
        }
        $occManager->setSqlWhere(0,$recLimit);
        $qryCnt = $occManager->getQueryRecordCount();
    }

	$recArr = $occManager->getOccurMap();
	$navStr = '<div style="float:right;">';
	if($occIndex >= $recLimit){
		$navStr .= '<a href="#" onclick="return submitQueryForm('.($occIndex-$recLimit). ')" title="Previous ' .$recLimit.' records">&lt;&lt;</a>';
	}
	$navStr .= ' | ';
	$navStr .= ($occIndex+1).'-'.($qryCnt<$recLimit+$occIndex?$qryCnt:$recLimit+$occIndex).' of '.$qryCnt.' records';
	$navStr .= ' | ';
	if($qryCnt > ($recLimit+$occIndex)){
		$navStr .= '<a href="#" onclick="return submitQueryForm('.($occIndex+$recLimit). ')" title="Next ' .$recLimit.' records">&gt;&gt;</a>';
	}
	$navStr .= '</div>';
}
else{
	header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Table View</title>
    <style type="text/css">
		table.styledtable td {
		    white-space: nowrap;
		}
    </style>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <script src="../../js/all.min.js" type="text/javascript"></script>
	<script src="../../js/jquery.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../js/symb/collections.occureditorshare.js?ver=20210901"></script>
</head>
<body style="margin-left: 0; margin-right: 0;background-color:white;">
	<div id="">
		<?php
		if($collMap){
			echo '<div>';
			echo '<h2>'.$collMap['collectionname'].' ('.$collMap['institutioncode'].($collMap['collectioncode']?':'.$collMap['collectioncode']:'').')</h2>';
			echo '</div>';
		}
		if(($isEditor || $crowdSourceMode)){
			?>
			<div style="text-align:right;width:790px;margin:-30px 15px 5px 0;">
				<a href="#" title="Search / Filter" onclick="toggleSearch();return false;"><i style="height:15px;width:15px;" class="fas fa-search"></i></a>
				<?php
				if($isEditor === 1 || $isGenObs){
					?>
					<a href="#" title="Batch Update Tool" onclick="toggleBatchUpdate();return false;"><i style="height:15px;width:15px;" class="far fa-plus-square"></i></a>
					<?php
				}
				?>
			</div>
			<?php
			if(!$recArr) {
                $displayQuery = 1;
            }
			include __DIR__ . '/includes/queryform.php';
			if($recArr){
				$headerArr = array();
				foreach($recArr as $id => $occArr){
					foreach($occArr as $k => $v){
						if(!array_key_exists($k,$headerArr) && trim($v)){
							$headerArr[$k] = $k;
						}
					}
				}
				if($qCustomField1 && !array_key_exists(strtolower($qCustomField1),$headerArr)){
					$headerArr[strtolower($qCustomField1)] = strtolower($qCustomField1);
				}
				if(isset($qCustomField2) && !array_key_exists(strtolower($qCustomField2),$headerArr)){
					$headerArr[strtolower($qCustomField2)] = strtolower($qCustomField2);
				}
				if(isset($qCustomField3) && !array_key_exists(strtolower($qCustomField3),$headerArr)){
					$headerArr[strtolower($qCustomField3)] = strtolower($qCustomField3);
				}
				$headerMap = array_intersect_key($headerMapBase, $headerArr);
			}
			if($isEditor === 1 || $isGenObs){
				$buFieldName = (array_key_exists('bufieldname',$_REQUEST)?$_REQUEST['bufieldname']:'');
				?>
				<div id="batchupdatediv" style="width:600px;clear:both;display:<?php echo ($buFieldName?'block':'none'); ?>;">
					<form name="batchupdateform" action="occurrencetabledisplay.php" method="post" onsubmit="return false;">
						<fieldset>
							<legend><b>Batch Update</b></legend>
							<div style="float:left;">
								<div style="margin:2px;">
									Field name:
									<select name="bufieldname" id="bufieldname" onchange="detectBatchUpdateField();">
										<option value="">Select Field Name</option>
										<option value="">----------------------</option>
										<?php
										foreach($headerMapBase as $k => $v){
											if($k !== 'scientificnameauthorship' && $k !== 'sciname' && $k !== 'verbatimsciname'){
												echo '<option value="'.$k.'" '.($buFieldName === $k?'SELECTED':'').'>'.$v.'</option>';
											}
										}
										?>
									</select>
								</div>
								<div style="margin:2px;">
									Current Value:
									<input name="buoldvalue" type="text" value="<?php echo (array_key_exists('buoldvalue',$_REQUEST)?$_REQUEST['buoldvalue']:''); ?>" />
								</div>
								<div style="margin:2px;">
									New Value:
									<span id="bunewvaluediv">
										<?php
										if($buFieldName === 'processingstatus'){
											?>
											<select name="bunewvalue">
												<option value="unprocessed" <?php echo (array_key_exists('bunewvalue',$_REQUEST)&&$_REQUEST['bunewvalue'] === 'unprocessed'?'SELECTED':''); ?>>Unprocessed</option>
												<option value="unprocessed/nlp" <?php echo (array_key_exists('bunewvalue',$_REQUEST)&&$_REQUEST['bunewvalue'] === 'unprocessed/nlp'?'SELECTED':''); ?>>Unprocessed/NLP</option>
												<option value="stage 1" <?php echo (array_key_exists('bunewvalue',$_REQUEST)&&$_REQUEST['bunewvalue'] === 'stage 1'?'SELECTED':''); ?>>Stage 1</option>
												<option value="stage 2" <?php echo (array_key_exists('bunewvalue',$_REQUEST)&&$_REQUEST['bunewvalue'] === 'stage 2'?'SELECTED':''); ?>>Stage 2</option>
												<option value="stage 3" <?php echo (array_key_exists('bunewvalue',$_REQUEST)&&$_REQUEST['bunewvalue'] === 'stage 3'?'SELECTED':''); ?>>Stage 3</option>
												<option value="pending review-nfn" <?php echo (array_key_exists('bunewvalue',$_REQUEST)&&$_REQUEST['bunewvalue'] === 'pending review-nfn'?'SELECTED':''); ?>>Pending Review-NfN</option>
												<option value="pending review" <?php echo (array_key_exists('bunewvalue',$_REQUEST)&&$_REQUEST['bunewvalue'] === 'pending review'?'SELECTED':''); ?>>Pending Review</option>
												<option value="expert required" <?php echo (array_key_exists('bunewvalue',$_REQUEST)&&$_REQUEST['bunewvalue'] === 'expert required'?'SELECTED':''); ?>>Expert Required</option>
												<option value="reviewed" <?php echo (array_key_exists('bunewvalue',$_REQUEST)&&$_REQUEST['bunewvalue'] === 'reviewed'?'SELECTED':''); ?>>Reviewed</option>
												<option value="closed" <?php echo (array_key_exists('bunewvalue',$_REQUEST)&&$_REQUEST['bunewvalue'] === 'closed'?'SELECTED':''); ?>>Closed</option>
												<option value="" <?php echo (array_key_exists('bunewvalue',$_REQUEST)&&$_REQUEST['bunewvalue'] === 'no set status'?'SELECTED':''); ?>>No Set Status</option>
											</select>
											<?php
										}
										else{
											?>
											<input name="bunewvalue" type="text" value="<?php echo (array_key_exists('bunewvalue',$_POST)?$_POST['bunewvalue']:''); ?>" />
											<?php
										}
										?>
									</span>
								</div>
							</div>
							<div style="float:left;margin-left:30px;">
								<div style="margin:2px;">
									<input name="bumatch" type="radio" value="0" checked />
									Match Whole Field<br/>
									<input name="bumatch" type="radio" value="1" />
									Match Any Part of Field
								</div>
								<div style="margin:2px;">
									<input name="collid" type="hidden" value="<?php echo $collId; ?>" />
									<input name="ouid" type="hidden" value="<?php echo $ouid; ?>" />
									<input name="occid" type="hidden" value="" />
									<input name="occindex" type="hidden" value="0" />
									<input name="submitaction" type="submit" value="Batch Update Field" onclick="submitBatchUpdate(this.form); return false;" />
								</div>
							</div>
						</fieldset>
					</form>
				</div>
				<?php
			}
			?>
			<div style="width:790px;clear:both;">
				<span class='navpath'>
						<a href="../../index.php">Home</a> &gt;&gt;
						<?php
                        if($crowdSourceMode){
                            ?>
                            <a href="../specprocessor/crowdsource/index.php">Crowd Sourcing Central</a> &gt;&gt;
                            <?php
                        }
                        else{
                            if(!$isGenObs || $GLOBALS['IS_ADMIN']){
                                ?>
                                <a href="../misc/collprofiles.php?collid=<?php echo $collId; ?>&emode=1">Collection Management</a> &gt;&gt;
                                <?php
                            }
                            if($isGenObs){
                                ?>
                                <a href="../../profile/viewprofile.php?tabindex=1">Personal Management</a> &gt;&gt;
                                <?php
                            }
                        }
                        ?>
						<b>Occurrence Record Table View</b>
					</span>
                <?php
				echo $navStr; ?>
			</div>
			<?php
			if($recArr){
				?>
				<table class="styledtable" style="font-family:Arial,serif;font-size:12px;">
					<tr>
						<th>Symbiota ID</th>
						<?php
						foreach($headerMap as $k => $v){
							echo '<th>'.$v.'</th>';
						}
						?>
					</tr>
					<?php
					$recCnt = 0;
					foreach($recArr as $id => $occArr){
						if($occArr['sciname']){
							$occArr['sciname'] = '<i>'.$occArr['sciname'].'</i> ';
						}
						echo '<tr ' .(($recCnt%2)?'class="alt"':'').">\n";
						echo '<td>';
						echo '<a href="occurrenceeditor.php?csmode='.$crowdSourceMode.'&occindex='.($recCnt+$occIndex).'&occid='.$id.'&collid='.$collId.'" title="open in same window">'.$id.'</a> ';
						echo '<a href="occurrenceeditor.php?csmode='.$crowdSourceMode.'&occindex='.($recCnt+$occIndex).'&occid='.$id.'&collid='.$collId.'" target="_blank" title="open in new window">';
						echo '<i style="height:15px;width:15px;" class="fas fa-external-link-alt"></i>';
						echo '</a>';
						echo '</td>'."\n";
						foreach($headerMap as $k => $v){
							$displayStr = $occArr[$k];
							if(strlen($displayStr) > 60){
								$displayStr = substr($displayStr,0,60).'...';
							}
							if(!$displayStr) {
                                $displayStr = '&nbsp;';
                            }
							echo '<td>'.$displayStr.'</td>'."\n";
						}
						echo "</tr>\n";
						$recCnt++;
					}
					?>
				</table>
				<div style="width:790px;">
					<?php echo $navStr; ?>
				</div>
				*Click on the Symbiota identifier in the first column to open the editor.
				<?php
			}
			else{
				?>
				<div style="font-weight:bold;font-size:120%;">
					No records found matching the query
				</div>
				<?php
			}
		}
		else if(!$isEditor){
            echo '<h2>You are not authorized to access this page</h2>';
        }
		?>
	</div>
</body>
</html>

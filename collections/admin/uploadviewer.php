<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/SpecUpload.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$recLimit = array_key_exists('reclimit',$_REQUEST)?(int)$_REQUEST['reclimit']:1000;
$pageIndex = array_key_exists('pageindex',$_REQUEST)?(int)$_REQUEST['pageindex']:0;
$searchVar = array_key_exists('searchvar',$_REQUEST)?htmlspecialchars($_REQUEST['searchvar']):'';

$uploadManager = new SpecUpload();
$uploadManager->setCollId($collid);
$collMap = $uploadManager->getCollInfo();

$headerMapBase = array('catalognumber' => 'Catalog Number','occurrenceid' => 'Occurrence ID',
	'othercatalognumbers' => 'Other Catalog #','family' => 'Family','identificationqualifier' => 'ID Qualifier',
	'sciname' => 'Scientific name','scientificnameauthorship'=>'Author','recordedby' => 'Collector','recordnumber' => 'Number',
	'associatedcollectors' => 'Associated Collectors','eventdate' => 'Event Date','verbatimeventdate' => 'Verbatim Date',
	'identificationremarks' => 'Identification Remarks','taxonremarks' => 'Taxon Remarks','identifiedby' => 'Identified By',
	'dateidentified' => 'Date Identified', 'identificationreferences' => 'Identification References',
	'country' => 'Country','stateprovince' => 'State/Province','county' => 'county','municipality' => 'municipality',
	'locality' => 'locality','decimallatitude' => 'Latitude', 'decimallongitude' => 'Longitude','geodeticdatum' => 'Datum',
	'coordinateuncertaintyinmeters' => 'Uncertainty In Meters','verbatimcoordinates' => 'Verbatim Coordinates',
	'georeferencedby' => 'Georeferenced By','georeferenceprotocol' => 'Georeference Protocol','georeferencesources' => 'Georeference Sources',
	'georeferenceverificationstatus' => 'Georef Verification Status','georeferenceremarks' => 'Georef Remarks',
	'minimumelevationinmeters' => 'Min. Elev. (m)','maximumelevationinmeters' => 'Max. Elev. (m)','verbatimelevation' => 'Verbatim Elev.',
	'habitat' => 'Habitat','substrate' => 'Substrate','occurrenceremarks' => 'Notes','associatedtaxa' => 'Associated Taxa',
	'verbatimattributes' => 'Verbatim Attributes','lifestage' => 'Life Stage', 'sex' => 'Sex', 'individualcount' => 'Individual Count',
	'samplingprotocol' => 'Sampling Protocol', 'preparations' => 'Preparations', 'reproductivecondition' => 'Reproductive Condition',
	'typestatus' => 'Type Status','cultivationstatus' => 'Cultivation Status','establishmentmeans' => 'Establishment Means',
	'disposition' => 'disposition','duplicatequantity' => 'Duplicate Qty','datelastmodified' => 'Date Last Modified',
	'processingstatus' => 'Processing Status','recordenteredby' => 'Entered By','basisofrecord' => 'Basis Of Record','occid' => 'targetRecord (occid)');
if($collMap['managementtype'] === 'Snapshot'){
	$headerMapBase['dbpk'] = 'Source Identifier';
}

$isEditor = 0;
if($GLOBALS['SYMB_UID']){
	if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
		$isEditor = 1;
	}
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title>Record Upload Preview</title>
    <style type="text/css">
		table.styledtable td {
		    white-space: nowrap;
		}
    </style>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
</head>
<body style="margin-left:0;margin-right:0;background-color:white;">
	<div id="">
		<?php 
		if($isEditor){
			if($collMap){
				echo '<h2>'.$collMap['name'].' ('.$collMap['institutioncode'].($collMap['collectioncode']?':'.$collMap['collectioncode']:'').')</h2>';
			}
			$recArr = $uploadManager->getPendingImportData(($recLimit*$pageIndex),$recLimit,$searchVar);
			if($recArr){
				$headerArr = array();
				foreach($recArr as $occurArr){
					foreach($occurArr as $k => $v){
						if(!array_key_exists($k,$headerArr) && trim($v)){
							$headerArr[$k] = $k;
						}
					}
				}
				$headerMap = array_intersect_key($headerMapBase, $headerArr);
				?>
				<table class="styledtable" style="font-family:Arial,serif;font-size:12px;">
					<tr>
						<?php 
						foreach($headerMap as $k => $v){
							echo '<th>'.$v.'</th>';
						}
						?>
					</tr>
					<?php 
					$cnt = 0;
					foreach($recArr as $id => $occArr){
						if($occArr['sciname']) {
                            $occArr['sciname'] = '<i>' . $occArr['sciname'] . '</i> ';
                        }
						echo '<tr ' .(($cnt%2)?'class="alt"':'').">\n";
						foreach($headerMap as $k => $v){
							$displayStr = $occArr[$k];
							if(strlen($displayStr) > 60){
								$displayStr = substr($displayStr,0,60).'...';
							}
							if($displayStr) {
								if($k === 'occid') {
                                    $displayStr = '<a href="../editor/occurrenceeditor.php?occid=' . $displayStr . '" target="_blank">' . $displayStr . '</a>';
                                }
							}
							else{
								$displayStr = '&nbsp;';
							}
							echo '<td>'.$displayStr.'</td>'."\n";
						}
						echo "</tr>\n";
						$cnt++;
					}
					?>
				</table>
				<div style="width:790px;">
					<?php //echo $navStr; ?>
				</div>
				<?php 
			}
			else{
				?>
				<div style="font-weight:bold;font-size:120%;margin:25px;">
					No records have been uploaded
				</div>
				<?php 
			}
		}
		else{
			echo '<h2>You are not authorized to access this page</h2>';
		}
		?>
	</div>
</body>
</html>

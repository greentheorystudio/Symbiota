<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceCleaner.php');
include_once(__DIR__ . '/../../classes/SOLRManager.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$action = array_key_exists('action',$_REQUEST)?htmlspecialchars($_REQUEST['action']):'';
$start = array_key_exists('start',$_REQUEST)?(int)$_REQUEST['start']:0;
$limit = array_key_exists('limit',$_REQUEST)?(int)$_REQUEST['limit']:200;

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) {
    $action = '';
}
if(!$limit) {
    $limit = 200;
}

$cleanManager = new OccurrenceCleaner();
$solrManager = new SOLRManager();
if($collid) {
    $cleanManager->setCollId($collid);
}
$collMap = $cleanManager->getCollMap();

$statusStr = '';
$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || ($collMap['colltype'] === 'General Observations') || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}

if($collMap['colltype'] === 'General Observations'){
	$cleanManager->setObsUid($GLOBALS['SYMB_UID']);
}

$dupArr = array();
if($action === 'listdupscatalog'){
	$limit = 1000;
	$dupArr = $cleanManager->getDuplicateCatalogNumber('cat',$start,$limit);
}
if($action === 'listdupsothercatalog'){
	$limit = 1000;
	$dupArr = $cleanManager->getDuplicateCatalogNumber('other',$start,$limit);
}
elseif($action === 'listdupsrecordedby'){
	$dupArr = $cleanManager->getDuplicateCollectorNumber($start);
}

?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Cleaner</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <style type="text/css">
		table.styledtable td { white-space: nowrap; }
    </style>
	<script type="text/javascript">
		function validateMergeForm(){
            const dbElements = document.getElementsByName("dupid[]");
            for(let i = 0; i < dbElements.length; i++){
                const dbElement = dbElements[i];
                if(dbElement.checked) {
                    return true;
                }
			}
		   	alert("Please select occurrences to be merged!");
	      	return false;
		}

		function selectAllDuplicates(f){
            let boxesChecked = true;
            if(!f.selectalldupes.checked){
				boxesChecked = false;
			}
            const dbElements = document.getElementsByName("dupid[]");
            for(let i = 0; i < dbElements.length; i++){
				dbElements[i].checked = boxesChecked;
			}

		}

		function batchSwitchTargetSpecimens(cbElem){
			cbElem.checked = false;
            const dbElements = document.getElementsByTagName("input");
            let elemName = '';
            for(let i = 0; i < dbElements.length; i++){
				if(dbElements[i].type === "radio"){
					if(dbElements[i].checked === false && elemName !== dbElements[i].name){
						dbElements[i].checked = true;
						elemName = dbElements[i].name;
					}
				}
			}
		}
	</script>
</head>
<body style="background-color:white;margin-left:0;margin-right:0;">
	<div class='navpath'>
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management</a> &gt;&gt;
		<a href="index.php?collid=<?php echo $collid; ?>">Data Cleaning Tools</a> &gt;&gt;
		<b>Duplicate Merging Module</b>
	</div>

	<div id="innertext" style="background-color:white;">
		<?php
		echo '<h2>'.$collMap['collectionname'].' ('.$collMap['code'].')</h2>';
		if($isEditor){
			if($action === 'listdupscatalog' || $action === 'listdupsothercatalog' || $action === 'listdupsrecordedby'){
				if($dupArr){
					$recCnt = count($dupArr);
					?>
					<div style="margin-bottom:10px;">
						<b>Use the checkboxes to select the records you would like to merge, and the radio buttons to select which target record to merge into.</b>
					</div>
					<form name="mergeform" action="duplicatesearch.php" method="post" onsubmit="return validateMergeForm();">
						<?php
						if($recCnt > $limit){
							$href = 'duplicatesearch.php?collid='.$collid.'&action='.$action.'&start='.($start+$limit);
							echo '<div style="float:right;"><a href="'.$href.'"><b>NEXT '.$limit.' RECORDS &gt;&gt;</b></a></div>';
						}
						echo '<div><b>'.($start+1).' to '.($start+$recCnt).' Duplicate Clusters </b></div>';
						?>
						<table class="styledtable" style="font-family:Arial,serif;font-size:12px;">
							<tr>
								<th style="width:40px;">ID</th>
								<th style="width:20px;"><input name="selectalldupes" type="checkbox" title="Select/Deselect All" onclick="selectAllDuplicates(this.form)" /></th>
								<th><input type="checkbox" name="batchswitch" onclick="batchSwitchTargetSpecimens(this)" title="Batch switch target occurrences" /></th>
								<th style="width:40px;">Catalog Number</th>
								<th style="width:40px;">Other Catalog Numbers</th>
								<th>Scientific Name</th>
								<th>Collector</th>
								<th>Collection Number</th>
								<th>Associated Collectors</th>
								<th>Collection Date</th>
								<th>Verbatim Date</th>
								<th>Country</th>
								<th>State</th>
								<th>County</th>
								<th>Locality</th>
								<th>Date Last Modified</th>
							</tr>
							<?php
							$setCnt = 0;
							foreach($dupArr as $dupKey => $occurArr){
								$setCnt++;
								$first = true;
								foreach($occurArr as $occId => $occArr){
									echo '<tr '.(($setCnt % 2) === 1?'class="alt"':'').'>';
									echo '<td><a href="../editor/occurrenceeditor.php?occid='.$occId.'" target="_blank">'.$occId.'</a></td>'."\n";
									echo '<td><input name="dupid[]" type="checkbox" value="'.$dupKey.':'.$occId.'" /></td>'."\n";
									echo '<td><input name="dup'.$dupKey.'target" type="radio" value="'.$occId.'" '.($first?'checked':'').'/></td>'."\n";
									echo '<td>'.$occArr['catalognumber'].'</td>'."\n";
									echo '<td>'.$occArr['othercatalognumbers'].'</td>'."\n";
									echo '<td>'.$occArr['sciname'].'</td>'."\n";
									echo '<td>'.$occArr['recordedby'].'</td>'."\n";
									echo '<td>'.$occArr['recordnumber'].'</td>'."\n";
									echo '<td>'.$occArr['associatedcollectors'].'</td>'."\n";
									echo '<td>'.$occArr['eventdate'].'</td>'."\n";
									echo '<td>'.$occArr['verbatimeventdate'].'</td>'."\n";
									echo '<td>'.$occArr['country'].'</td>'."\n";
									echo '<td>'.$occArr['stateprovince'].'</td>'."\n";
									echo '<td>'.$occArr['county'].'</td>'."\n";
									echo '<td>'.$occArr['locality'].'</td>'."\n";
									echo '<td>'.$occArr['datelastmodified'].'</td>'."\n";
									echo '</tr>';
									$first = false;
								}
							}
							?>
						</table>
						<div style="margin:15px;">
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="action" type="submit" value="Merge Duplicate Records" />
						</div>
					</form>
					<?php
				}
				else{
					?>
					<div style="margin:25px;font-weight:bold;font-size:120%;">
						There are no duplicate catalog numbers!
					</div>
					<?php
				}
			}
			elseif($action === 'Merge Duplicate Records'){
				?>
				<ul>
					<li>Duplicate merging process started</li>
					<?php
					$dupArr = array();
					foreach($_POST['dupid'] as $v){
						$vArr = explode(':',$v);
						if($vArr && count($vArr) > 1){
							$target = $_POST['dup'.$vArr[0].'target'];
							if($target !== $vArr[1]) {
                                $dupArr[$target][] = $vArr[1];
                            }
						}
					}
					$cleanManager->mergeDupeArr($dupArr);
                    if($GLOBALS['SOLR_MODE']) {
                        $solrManager->updateSOLR();
                    }
					?>
					<li>Done!</li>
				</ul>
				<?php
			}
			?>
			<div>
				<a href="index.php?collid=<?php echo $collid; ?>">Return to main menu</a>
			</div>
			<?php
		}
		else{
			echo '<h2>You are not authorized to access this page</h2>';
		}
		?>
	</div>
</body>
</html>

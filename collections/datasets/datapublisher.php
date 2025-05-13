<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DwcArchiverPublisher.php');
include_once(__DIR__ . '/../../classes/OccurrenceCollectionProfile.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$collId = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$emode = array_key_exists('emode',$_REQUEST)?(int)$_REQUEST['emode']:0;
$action = array_key_exists('formsubmit',$_REQUEST)?htmlspecialchars($_REQUEST['formsubmit']):'';
$cSet = array_key_exists('cset',$_REQUEST)?htmlspecialchars($_REQUEST['cset']):'';

$dwcaManager = new DwcArchiverPublisher();
$collManager = new OccurrenceCollectionProfile();

$includeDets = 1;
$includeImgs = 1;
$redactLocalities = 1;
$collPubArr = array();
$publishGBIF = false;
$publishIDIGBIO = false;
$installationKey = '';
$datasetKey = '';
$endpointKey = '';
$idigbioKey = '';
if(isset($GLOBALS['GBIF_USERNAME'], $GLOBALS['GBIF_PASSWORD'], $GLOBALS['GBIF_ORG_KEY']) && $collId && $GLOBALS['GBIF_USERNAME'] && $GLOBALS['GBIF_PASSWORD'] && $GLOBALS['GBIF_ORG_KEY']){
    $collPubArr = $collManager->getCollPubArr($collId);
    if($collPubArr[$collId]['publishToGbif']){
        $publishGBIF = true;
    }
    if($collPubArr[$collId]['publishToIdigbio']){
        $publishIDIGBIO = true;
    }
}
if($action){
	if($action === 'Save Key'){
		$collManager->setAggKeys($_POST['aggKeysStr']);
        $collManager->updateAggKeys($collId);
	}
	else{
		if (!array_key_exists('dets', $_POST)) {
			$includeDets = 0;
			$dwcaManager->setIncludeDets(0);
		}
		if (!array_key_exists('imgs', $_POST)) {
			$includeImgs = 0;
			$dwcaManager->setIncludeImgs(0);
		}
		if (!array_key_exists('redact', $_POST)) {
			$redactLocalities = 0;
			$dwcaManager->setRedactLocalities(0);
		}
		$dwcaManager->setTargetPath($GLOBALS['SERVER_ROOT'] . (substr($GLOBALS['SERVER_ROOT'], -1) === '/' ? '' : '/') . 'content/dwca/');
	}
}
if(isset($GLOBALS['GBIF_USERNAME'], $GLOBALS['GBIF_PASSWORD'], $GLOBALS['GBIF_ORG_KEY'])){
	$installationKey = $collManager->getInstallationKey();
    $datasetKey = $collManager->getDatasetKey();
    $endpointKey = $collManager->getEndpointKey();
    $idigbioKey = $collManager->getIdigbioKey();
}

$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}

$collArr = array();
$dwcUri = '';

if($collId){
	$dwcaManager->setCollArr($collId);
	$collArr = $dwcaManager->getCollArr($collId);
}
if($isEditor && array_key_exists('colliddel', $_POST)) {
    $dwcaManager->deleteArchive($_POST['colliddel']);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title>Darwin Core Archive Publisher</title>
    <meta name="description" content="Publish occurrence Darwin Core Archives for collections in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
	<style>
		.nowrap { white-space: nowrap; }
	</style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/collections.gbifpublisher.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script type="text/javascript">
		function verifyDwcaAdminForm(){
            const dbElements = document.getElementsByName("coll[]");
            for(let i = 0; i < dbElements.length; i++){
                const dbElement = dbElements[i];
                if(dbElement.checked) {
                    return true;
                }
			}
		   	alert("Please choose at least one collection!");
			return false;
    	}

		function checkAllColl(cb){
            let boxesChecked = true;
            if(!cb.checked){
				boxesChecked = false;
			}
            const cName = cb.className;
            const dbElements = document.getElementsByName("coll[]");
            for(let i = 0; i < dbElements.length; i++){
                const dbElement = dbElements[i];
                if(dbElement.className === cName){
					if(dbElement.disabled === false) dbElement.checked = boxesChecked;
				}
				else{
					dbElement.checked = false;
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
	<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
	<?php
	if($collId){
		?>
		<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/collprofiles.php?collid=<?php echo $collId; ?>&emode=1">Collection Control Panel</a> &gt;&gt;
		<?php
	}
	else{
		?>
		<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/sitemap.php">Sitemap</a> &gt;&gt;
		<?php
	}
	?>
	<b>Darwin Core Archive Publisher</b>
</div>
<div id="main-container">
	<?php
	if(!$collId && $GLOBALS['IS_ADMIN']){
		?>
		<div style="float:right;">
			<a href="#" title="Display Publishing Control Panel" onclick="toggle('dwcaadmindiv')">
                <i style="height:20px;width:20px;" class="far fa-edit"></i>
			</a>
		</div>
		<?php
	}
	?>
	<h2>Darwin Core Archive Publishing</h2>
	<?php
	if($collId){
		echo '<div style="font-weight:bold;">'.$collArr['collname'].'</div>';
		?>
		<div style="margin:10px;">
			Use the controls below to publish occurrence data within this collection as a
			<a href="https://github.com/gbif/ipt/wiki/DwCAHowToGuide" target="_blank">Darwin Core Archive (DwC-A)</a> file.
			A DwC-A file is a single compressed ZIP file that contains one to several data files along with a meta.xml
			document that describes the content.
			The occurrence data file is required, but identifications (determinations) and image metadata are optional.
			Fields within the occurrences.csv file are defined by the <a href="https://www.tdwg.org/standards/dwc/" target="_blank">Darwin Core standard</a>.
		</div>
		<?php
	}
	else{
		?>
		<div style="margin:10px;">
			The following downloads are occurrence data packages from collections
			that have chosen to publish their complete dataset as a
			<a href="https://github.com/gbif/ipt/wiki/DwCAHowToGuide" target="_blank">Darwin Core Archive (DwC-A)</a> file.
			A DwC-A file is a single compressed ZIP file that contains one to several data files along with a meta.xml
			document that describes the content.
			The archives below contain three comma separated (CSV) files containing occurrences, identifications (determinations), and image metadata.
			Fields within the occurrences.csv file are defined by the <a href="https://www.tdwg.org/standards/dwc/" target="_blank">Darwin Core standard</a>.
            The identification and image files follow the DwC extensions for those data types.
		</div>
		<div style="margin:10px;">
			<h3>Data Usage Policy:</h3>
			Use of these datasets requires agreement with the terms and conditions in our
			<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/usagepolicy.php">Data Usage Policy</a>.
			Locality details for rare, threatened, or sensitive records have been redacted from these data files.
			One must contact the collections directly to obtain access to sensitive locality data.
		</div>
		<?php
	}
	?>
	<div style="margin:20px;">
		<b>RSS Feed:</b>
		<?php
		$urlPrefix = $dwcaManager->getServerDomain().$GLOBALS['CLIENT_ROOT'];
		if(file_exists('../../rss.xml')){
			$feedLink = $urlPrefix.'/rss.xml';
			echo '<a href="'.$feedLink.'" target="_blank">'.$feedLink.'</a>';
		}
		else{
			echo '--feed not published for any of the collections within the portal--';
		}
		?>
	</div>
	<?php
	if($collId){
		if($action === 'Create/Refresh Darwin Core Archive'){
            $dwcaManager->setCollID($collId);
		    echo '<ul>';
			$dwcaManager->setVerboseMode(3);
			$dwcaManager->setLimitToGuids(true);
			$dwcaManager->createDwcArchive();
			$dwcaManager->writeRssFile();
			echo '</ul>';
			if($publishGBIF && $endpointKey){
				$collManager->triggerGBIFCrawl($datasetKey);
			}
		}
		if($dwcaArr = $dwcaManager->getDwcaItems($collId)){
            $dArr = current($dwcaArr);
			$dwcUri = ($dArr['collid'] === $collId?$dArr['link']:'');
			?>
			<div style="margin:10px;">
				<div>
					<b>Title:</b> <?php echo $dArr['title']; ?>
					<form action="datapublisher.php" method="post" style="display:inline;" onsubmit="return window.confirm('Are you sure you want to delete this archive?');">
						<input type="hidden" name="colliddel" value="<?php echo $dArr['collid']; ?>">
						<input type="hidden" name="collid" value="<?php echo $dArr['collid']; ?>">
                        <input type="hidden" name="action" value="DeleteCollid">
						<button type="submit" class="icon-button" style="margin:0;padding:2px;" title="Delete Archive">
                            <i style="height:15px;width:15px;" class="far fa-trash-alt"></i>
                        </button>
					</form>
				</div>
				<div><b>Description:</b> <?php echo $dArr['description']; ?></div>
				<?php
				$emlLink = $urlPrefix.'collections/datasets/emlhandler.php?collid='.$collId;
				?>
				<div><b>EML:</b> <a href="<?php echo $emlLink; ?>"><?php echo $emlLink; ?></a></div>
				<div><b>DwC-Archive File:</b> <a href="<?php echo $dArr['link']; ?>"><?php echo $dArr['link']; ?></a></div>
				<div><b>Publication Date:</b> <?php echo $dArr['pubDate']; ?></div>
			</div>
			<?php
		}
		else{
			echo '<div style="margin:20px;font-weight:bold;color:orange;">No data archives have been published for this collection</div>';
		}
		?>
		<fieldset style="margin:15px;padding:15px;">
			<legend><b>Publishing Information</b></legend>
			<?php
			$blockSubmitMsg = '';
			$recFlagArr = $dwcaManager->verifyCollRecords($collId);
			if($collArr['guidtarget']){
				echo '<div style="margin:10px;"><b>GUID source:</b> '.$collArr['guidtarget'].'</div>';
				if($recFlagArr['nullGUIDs']){
					echo '<div style="margin:10px;">';
					if($collArr['guidtarget'] === 'occurrenceId'){
						echo '<b>Records missing <a href="" target="_blank">OccurrenceID GUIDs</a>:</b> '.$recFlagArr['nullGUIDs'];
						echo ' <span style="color:red;margin-left:15px;">These records will not be published!</span> ';
					}
					elseif($collArr['guidtarget'] === 'catalogNumber'){
						echo '<b>Records missing Catalog Numbers:</b> '.$recFlagArr['nullGUIDs'];
						echo ' <span style="color:red;margin-left:15px;">These records will not be published!</span> ';
					}
					else{
						echo 'Records missing GUIDs: '.$recFlagArr['nullGUIDs'].'<br/>';
						echo 'Please go to the <a href="../admin/guidmapper.php?collid='.$collId.'">Collection GUID Mapper</a> to assign GUIDs.';
					}
					echo '</div>';
				}
				if($collArr['dwcaurl']){
					$serverName = $_SERVER['HTTP_HOST'];
					if(strncmp($serverName, 'www.', 4) === 0) {
                        $serverName = substr($serverName, 4);
                    }
					if(!strpos($collArr['dwcaurl'],$serverName)){
						$baseUrl = substr($collArr['dwcaurl'],0,strpos($collArr['dwcaurl'],'/content')).'/collections/datasets/datapublisher.php';
						$blockSubmitMsg = 'Already published on sister portal (<a href="'.$baseUrl.'" target="_blank">'.substr($baseUrl,0,strpos($baseUrl,'/',10)).'</a>) ';
					}
				}
			}
			else{
				echo '<div style="margin:10px;font-weight:bold;color:red;">The GUID source has not been set for this collection. Please go to the <a href="../misc/collmetadata.php?collid='.$collId.'">Edit Metadata page</a> to set GUID source.</div>';
				$blockSubmitMsg = 'Archive cannot be published until occurrenceID GUID source is set<br/>';
			}
			if($recFlagArr['nullBasisRec']){
				echo '<div style="margin:10px;font-weight:bold;color:red;">There are '.$recFlagArr['nullBasisRec'].' records missing basisOfRecord and will not be published. Please go to <a href="../editor/occurrencetabledisplay.php?q_recordedby=&q_recordnumber=&q_eventdate=&q_catalognumber=&q_othercatalognumbers=&q_observeruid=&q_recordenteredby=&q_dateentered=&q_datelastmodified=&q_processingstatus=&q_customfield1=basisOfRecord&q_customtype1=NULL&q_customvalue1=Something&q_customfield2=&q_customtype2=EQUALS&q_customvalue2=&q_customfield3=&q_customtype3=EQUALS&q_customvalue3=&collid='.$collId.'&csmode=0&occid=&occindex=0&orderby=&orderbydir=ASC">Edit Existing Occurrence Records</a> to correct this.</div>';
			}
			if($dwcUri && isset($GLOBALS['GBIF_USERNAME'], $GLOBALS['GBIF_PASSWORD'], $GLOBALS['GBIF_ORG_KEY']) && ($publishGBIF || $publishIDIGBIO)){
				if($publishGBIF && !$datasetKey) {
					?>
					<div style="margin:10px;">
						You have selected to have this collection's DwC archives published to GBIF. Please go to the
						<a href="https://www.gbif.org/become-a-publisher" target="_blank">GBIF Endorsement Request page</a> to
						register your collection with GBIF and enter the key provided by GBIF below. If your collection is found in the
                        GBIF Organization lookup, there is already a GBIF Key assigned. The key is the remaining part of
						the url after the last backslash of your collection's GBIF Data Provider page. If your collection is found,
                        please ensure that your data is not already published in GBIF. DO NOT PUBLISH your data if there is any chance it is
                        already published. Before activating your GBIF Key in this portal, you will also need to contact GBIF (<a href="mailto:helpdesk@gbif.org">helpdesk@gbif.org</a>) and
                        request that the user: <b><?php echo $GLOBALS['GBIF_USERNAME']; ?></b> has permissions to create and edit datatsets for your collection.
						<form style="margin-top:10px;" name="gbifpubform" action="datapublisher.php" method="post" onsubmit="return processGbifOrgKey(this.form);">
							GBIF Key <input type="text" name="gbifOrgKey" id="gbifOrgKey" value="" style="width:250px;"/>
							<input type="hidden" name="collid" value="<?php echo $collId; ?>"/>
							<input type="hidden" name="portalname" id="portalname" value='<?php echo $GLOBALS['DEFAULT_TITLE']; ?>'/>
							<input type="hidden" name="collname" id="collname" value='<?php echo $collArr['collname']; ?>'/>
							<input type="hidden" name="aggKeysStr" id="aggKeysStr" value=''/>
							<input type="hidden" id="gbifInstOrgKey" value='<?php echo $GLOBALS['GBIF_ORG_KEY']; ?>'/>
							<input type="hidden" id="gbifInstKey" value='<?php echo $installationKey; ?>'/>
							<input type="hidden" id="gbifDataKey" value=''/>
							<input type="hidden" id="gbifEndKey" value=''/>
							<input type="hidden" name="dwcUri" id="dwcUri" value="<?php echo $dwcUri; ?>"/>
							<input type="submit" name="formsubmit" value="Save Key"/>
						</form>
					</div>
					<?php
				}
				if($publishGBIF && $datasetKey){
                    $dataUrl = 'http://www.gbif.org/dataset/'.$datasetKey;
                    ?>
                    <div style="margin:10px;">
                        <div><b>GBIF Dataset page:</b> <a href="<?php echo $dataUrl; ?>"
                                                          target="_blank"><?php echo $dataUrl; ?></a></div>
                    </div>
                    <?php
                }
                if($publishIDIGBIO && $idigbioKey){
                    $dataUrl = 'https://www.idigbio.org/portal/recordsets/'.$idigbioKey;
                    ?>
                    <div style="margin:10px;">
                        <div><b>iDigBio Dataset page:</b> <a href="<?php echo $dataUrl; ?>" target="_blank"><?php echo $dataUrl; ?></a></div>
                    </div>
                    <?php
                }
			}
			?>
		</fieldset>
		<fieldset style="padding:15px;margin:15px;">
			<legend><b>Publish/Refresh DwC-A File</b></legend>
			<form name="dwcaform" action="datapublisher.php" method="post">
				<div>
					<input type="checkbox" name="dets" value="1" <?php echo ($includeDets?'CHECKED':''); ?> /> Include Determination History<br/>
					<input type="checkbox" name="imgs" value="1" <?php echo ($includeImgs?'CHECKED':''); ?> /> Include Image URLs<br/>
					<input type="checkbox" name="redact" value="1" <?php echo ($redactLocalities?'CHECKED':''); ?> /> Redact Sensitive Localities<br/>
				</div>
				<div style="clear:both;margin:10px;">
					<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
					<input type="submit" name="formsubmit" value="Create/Refresh Darwin Core Archive" <?php echo ($blockSubmitMsg?'disabled':''); ?> />
					<?php
					if($blockSubmitMsg){
						echo '<span style="color:red;margin-left:10px;">'.$blockSubmitMsg.'</span>';
					}
					?>
				</div>
				<?php
				if($collArr['managementtype'] !== 'Live Data' || $collArr['guidtarget'] !== 'symbiotaUUID'){
					?>
					<div style="margin:10px;font-weight:bold">
						NOTE: all records lacking occurrenceID GUIDs will be excluded
					</div>
					<?php
				}
				?>
			</form>
		</fieldset>
		<?php
	}
	else{
		$catID = ($GLOBALS['DEFAULTCATID'] ?? 0);
		$catTitle = $dwcaManager->getCategoryName($catID);
		if($GLOBALS['IS_ADMIN']){
			if($action === 'Create/Refresh Darwin Core Archive(s)'){
				echo '<ul>';
				$dwcaManager->setVerboseMode(3);
				$dwcaManager->setLimitToGuids(true);
				$dwcaManager->batchCreateDwca($_POST['coll']);
				echo '</ul>';
				if($publishGBIF){
					$collManager->batchTriggerGBIFCrawl($_POST['coll']);
				}
			}
			?>
			<div id="dwcaadmindiv" style="margin:10px;display:<?php echo ($emode?'block':'none'); ?>;" >
				<form name="dwcaadminform" action="datapublisher.php" method="post" onsubmit="return verifyDwcaAdminForm()">
					<fieldset style="padding:15px;">
						<legend><b>Publish / Refresh <?php echo $catTitle; ?> DwC-A Files</b></legend>
						<div style="margin:10px;">
							<input name="collcheckall" type="checkbox" value="" onclick="checkAllColl(this)" /> Select/Deselect All<br/><br/>
							<?php
							$collList = $dwcaManager->getCollectionList($catID);
							foreach($collList as $k => $v){
								$errMsg = '';
								if(!$v['guid']){
									$errMsg = 'Missing GUID source';
								}
								elseif($v['url'] && !strpos($v['url'],str_replace('www.', '', $_SERVER['HTTP_HOST']))){
									$baseUrl = substr($v['url'],0,strpos($v['url'],'/content')).'/collections/datasets/datapublisher.php';
									$errMsg = 'Already published on different domain (<a href="'.$baseUrl.'" target="_blank">'.substr($baseUrl,0,strpos($baseUrl,'/',10)).'</a>)';
								}
								echo '<input name="coll[]" type="checkbox" value="'.$k.'" '.($errMsg?'DISABLED':'').' />';
								echo '<a href="../misc/collprofiles.php?collid='.$k.'" target="_blank">'.$v['name'].'</a>';
								if($errMsg) {
                                    echo '<span style="color:red;margin-left:15px;">' . $errMsg . '</span>';
                                }
								echo '<br/>';
							}
							?>
						</div>
						<fieldset style="margin:10px;padding:15px;">
							<legend><b>Options</b></legend>
							<input type="checkbox" name="dets" value="1" <?php echo ($includeDets?'CHECKED':''); ?> /> Include Determination History<br/>
							<input type="checkbox" name="imgs" value="1" <?php echo ($includeImgs?'CHECKED':''); ?> /> Include Image URLs<br/>
							<input type="checkbox" name="redact" value="1" <?php echo ($redactLocalities?'CHECKED':''); ?> /> Redact Sensitive Localities<br/>
						</fieldset>
						<div style="clear:both;margin:20px;">
							<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
							<input type="submit" name="formsubmit" value="Create/Refresh Darwin Core Archive(s)" />
						</div>
					</fieldset>
				</form>
			</div>
			<?php
		}
		if($dwcaArr = $dwcaManager->getDwcaItems()){
			if($catTitle) {
                echo '<div style="font-weight:bold;margin:50px 0 15px 0;">' . $catTitle . ' DwC-Archive Files</div>';
            }
			?>
			<table class="styledtable" style="font-family:Arial,serif;margin:10px;">
				<tr><th>Code</th><th>Collection Name</th><th>DwC-Archive</th><th>Metadata</th><th>Pub Date</th></tr>
				<?php
				foreach($dwcaArr as $k => $v){
					?>
					<tr>
						<td><?php echo '<a href="../misc/collprofiles.php?collid='.$v['collid'].'">'.str_replace(' DwC-Archive','',$v['title']).'</a>'; ?></td>
						<td><?php echo substr($v['description'],24); ?></td>
						<td class="nowrap">
							<?php
							echo '<a href="'.$v['link'].'">DwC-A ('.$dwcaManager->humanFileSize($v['link']).')</a>';
							if($GLOBALS['IS_ADMIN']){
								?>
								<form action="datapublisher.php" method="post" style="display:inline;" onsubmit="return window.confirm('Are you sure you want to delete this archive?');">
									<input type="hidden" name="colliddel" value="<?php echo $v['collid']; ?>">
                                    <input type="hidden" name="action" value="DeleteCollid">
									<button type="submit" class="icon-button" style="margin:0;padding:2px;" title="Delete Archive">
                                        <i style="height:15px;width:15px;" class="far fa-trash-alt"></i>
                                    </button>
								</form>
								<?php
							}
							?>
						</td>
						<td>
							<?php
							echo '<a href="'.$urlPrefix.'collections/datasets/emlhandler.php?collid='.$v['collid'].'">EML</a>';
							?>
						</td>
						<td class="nowrap"><?php echo date('Y-m-d', strtotime($v['pubDate'])); ?></td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
		}
		else{
			echo '<div style="margin:10px;font-weight:bold;">There are no publishable collections</div>';
		}
		if($catID && $addDwca = $dwcaManager->getAdditionalDWCA($catID)) {
            echo '<div style="font-weight:bold;margin:50px 0 15px 0;">Additional Data Sources within the Portal Network</div>';
            echo '<ul>';
            foreach($addDwca as $domanName => $domainArr){
                echo '<li><a href="'.$domainArr['url'].'/collections/datasets/datapublisher.php'.'" target="_blank">http://'.$domanName.'</a> - '.$domainArr['cnt'].' Archives</li>';
            }
            echo '</ul>';
        }
	}
	?>
</div>
<?php
include_once(__DIR__ . '/../../config/footer-includes.php');
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>

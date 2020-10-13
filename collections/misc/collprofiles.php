<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceCollectionProfile.php');
include_once(__DIR__ . '/../../classes/SOLRManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);
ini_set('max_execution_time', 180);

$collid = ((array_key_exists('collid',$_REQUEST) && is_numeric($_REQUEST['collid']))?$_REQUEST['collid']:0);
$action = array_key_exists('action',$_REQUEST)?htmlspecialchars($_REQUEST['action']): '';
$eMode = array_key_exists('emode',$_REQUEST)?htmlspecialchars($_REQUEST['emode']):0;

if($eMode && !$SYMB_UID){
	header('Location: ../../profile/index.php?refurl=../collections/misc/collprofiles.php?'.$_SERVER['QUERY_STRING']);
}

$collManager = new OccurrenceCollectionProfile();
if($SOLR_MODE) {
    $solrManager = new SOLRManager();
}
if(!$collManager->setCollid($collid)) {
    $collid = '';
}

$collData = $collManager->getCollectionMetadata();

$collPubArr = array();
$publishGBIF = false;
$publishIDIGBIO = false;
if(isset($GBIF_USERNAME, $GBIF_PASSWORD, $GBIF_ORG_KEY) && $collid){
    $collPubArr = $collManager->getCollPubArr($collid);
    if($collPubArr[$collid]['publishToGbif']){
        $publishGBIF = true;
    }
    if($collPubArr[$collid]['publishToIdigbio']){
        $publishIDIGBIO = true;
    }
    $installationKey = $collManager->getInstallationKey();
    $datasetKey = $collManager->getDatasetKey();
    $endpointKey = $collManager->getEndpointKey();
    $idigbioKey = $collManager->getIdigbioKey();
    if($publishIDIGBIO && !$idigbioKey){
        $idigbioKey = $collManager->findIdigbioKey($collPubArr[$collid]['collectionguid']);
        if($idigbioKey){
            $collManager->updateAggKeys($collid);
        }
    }
}

$editCode = 0;
if($SYMB_UID){
	if($IS_ADMIN){
		$editCode = 3;
	}
	else if($collid){
		if(array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollAdmin'], true)){
			$editCode = 2;
		}
		elseif(array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollEditor'], true)){
			$editCode = 1;
		}
	}
}
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<title><?php echo $DEFAULT_TITLE. ' ' .($collid?$collData[$collid]['collectionname']: '') ; ?> Collection Profiles</title>
	<meta name="keywords" content="Natural history collections,<?php echo ($collid?$collData[$collid]['collectionname']: ''); ?>" />
	<link href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/jquery-ui.css" rel="stylesheet" type="text/css" />
	<script src="../../js/jquery.js?ver=20130917" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js?ver=20130917" type="text/javascript"></script>
	<script>
		function toggleById(target){
			if(target != null){
                const obj = document.getElementById(target);
                if(obj.style.display === "none" || obj.style.display === ""){
					obj.style.display="block";
				}
				else {
					obj.style.display="none";
				}
			}
			return false;
		}
	</script>
</head>
<body>
	<?php
	include(__DIR__ . '/../../header.php');
	echo "<div class='navpath'>";
    echo '<a href="../../index.php">Home</a> &gt;&gt; ';
    echo '<a href="../index.php">Collection Search Page</a> &gt;&gt; ';
    echo '<b>' .($collid?$collData[$collid]['collectionname']: 'Collection Profiles'). ' Details</b>';
	echo '</div>';
	?>

	<div id="innertext">
		<?php
		if($editCode > 1){
			if($action === 'UpdateStatistics'){
				echo '<h2>Updating statistics related to this collection...</h2>';
				$collManager->updateStatistics(true);
				echo '<hr/>';
			}
            if($action === 'cleanSOLR'){
                echo '<h2>Cleaning SOLR Index...</h2>';
                $solrManager->cleanSOLRIndex($collid);
                echo '<hr/>';
            }
		}
		if($editCode > 0 && $collid){
			?>
			<div style="float:right;margin:3px;cursor:pointer;" onclick="toggleById('controlpanel');" title="Toggle Manager's Control Panel">
				<img style='border:0;' src='../../images/edit.png' />
			</div>
			<?php
		}
		if($collid){
			$collData = $collData[$collid];
			$codeStr = ' ('.$collData['institutioncode'];
			if($collData['collectioncode']) {
                $codeStr .= '-' . $collData['collectioncode'];
            }
			$codeStr .= ')';
			echo '<h1>'.$collData['collectionname'].$codeStr.'</h1>';
			if($editCode > 0){
				?>
				<div id="controlpanel" style="clear:both;display:<?php echo ($eMode?'block':'none'); ?>;">
					<fieldset style="padding: 10px 10px 10px 25px;">
						<legend><b>Data Editor Control Panel</b></legend>
						<fieldset style="float:right;margin:5px" title="Quick Search">
							<legend><b>Quick Search</b></legend>
							<b>Catalog Number</b><br/>
							<form name="quicksearch" action="../editor/occurrenceeditor.php" method="post">
								<input name="q_catalognumber" type="text" />
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="occindex" type="hidden" value="0" />
							</form>
						</fieldset>
						<ul>
							<?php
							if(stripos($collData['colltype'],'observation') !== false){
								?>
								<li>
									<a href="../editor/observationsubmit.php?collid=<?php echo $collid; ?>">
                                        Submit an Image Voucher (observation supported by a photo)
									</a>
								</li>
								<?php
							}
							?>
							<li>
								<a href="../editor/occurrenceeditor.php?gotomode=1&collid=<?php echo $collid; ?>">
                                    Add New Occurrence Record
								</a>
							</li>
							<?php
							if($collData['colltype'] === 'Preserved Specimens'){
								?>
								<li style="margin-left:10px">
									<a href="../editor/imageoccursubmit.php?collid=<?php echo $collid; ?>">
                                        Create New Records Using Image
									</a>
								</li>
								<li style="margin-left:10px">
									<a href="../editor/skeletalsubmit.php?collid=<?php echo $collid; ?>">
                                        Add Skeletal Records
									</a>
								</li>
								<?php
							}
							?>
							<li>
								<a href="../editor/occurrenceeditor.php?collid=<?php echo $collid; ?>">
                                    Edit Existing Occurrence Records
								</a>
							</li>
							<li>
								<a href="../editor/batchdeterminations.php?collid=<?php echo $collid; ?>">
                                    Add Batch Determinations/Nomenclatural Adjustments
								</a>
							</li>
							<li>
								<a href="../reports/labelmanager.php?collid=<?php echo $collid; ?>">
                                    Print Labels/Annotations
								</a>
							</li>
							<li>
								<a href="../georef/batchgeoreftool.php?collid=<?php echo $collid; ?>">
                                    Batch Georeference Specimens
								</a>
							</li>
							<?php
							if($collData['colltype'] === 'Preserved Specimens'){
								?>
								<li>
									<a href="../loans/index.php?collid=<?php echo $collid; ?>">
                                        Loan Management
									</a>
								</li>
								<?php
							}
							?>
						</ul>
					</fieldset>
					<?php
					if($editCode > 1){
						?>
						<fieldset style="padding: 10px 10px 10px 25px;">
							<legend><b>Administration Control Panel</b></legend>
							<ul>

                                <li>
                                    <a href="commentlist.php?collid=<?php echo $collid; ?>" >
                                        View Posted Comments
                                    </a>
                                </li>
								<li>
									<a href="collmetadata.php?collid=<?php echo $collid; ?>" >
                                        Edit Metadata and Contact Information
									</a>
								</li>
								<li>
									<a href="collpermissions.php?collid=<?php echo $collid; ?>" >
                                        Manage Permissions
									</a>
								</li>
								<?php
                                if($FIELDGUIDE_ACTIVE){
                                    ?>
                                    <li>
                                        <a href="fgbatch.php?collid=<?php echo $collid; ?>" >
                                            Fieldguide Batch Image Processing
                                        </a>
                                    </li>
                                    <?php
                                }
								if($collData['colltype'] !== 'General Observations'){
									?>
									<li>
										<a href="#" onclick="$('li.importItem').show(); return false;" >
                                            Import/Update Specimen Records
										</a>
									</li>
									<li class="importItem" style="margin-left:10px;display:none;">
										<a href="../admin/specupload.php?uploadtype=7&collid=<?php echo $collid; ?>">
                                            Skeletal File Import
										</a>
									</li>
									<li class="importItem" style="margin-left:10px;display:none">
										<a href="../admin/specupload.php?uploadtype=3&collid=<?php echo $collid; ?>">
                                            Text File Import
										</a>
									</li>
									<li class="importItem" style="margin-left:10px;display:none;">
										<a href="../admin/specupload.php?uploadtype=6&collid=<?php echo $collid; ?>">
                                            DwC-Archive Import
										</a>
									</li>
									<li class="importItem" style="margin-left:10px;display:none;">
										<a href="../admin/specupload.php?uploadtype=8&collid=<?php echo $collid; ?>">
                                            IPT Import
										</a>
									</li>
									<li class="importItem" style="margin-left:10px;display:none;">
										<a href="../admin/specupload.php?uploadtype=9&collid=<?php echo $collid; ?>">
                                            Notes from Nature Import
										</a>
									</li>
									<li class="importItem" style="margin-left:10px;display:none;">
										<a href="../admin/specuploadmanagement.php?collid=<?php echo $collid; ?>">
                                            Saved Import Profiles
										</a>
									</li>
									<li class="importItem" style="margin-left:10px;display:none;">
										<a href="../admin/specuploadmanagement.php?action=addprofile&collid=<?php echo $collid; ?>">
                                            Create a new Import Profile
										</a>
									</li>
									<?php
									if($collData['managementtype'] !== 'Aggregate'){
										?>
										<li>
											<a href="../specprocessor/index.php?collid=<?php echo $collid; ?>">
                                                Processing Toolbox
											</a>
										</li>
										<li>
											<a href="../datasets/datapublisher.php?collid=<?php echo $collid; ?>">
                                                Darwin Core Archive Publishing
											</a>
										</li>
										<?php
									}
									?>
									<li>
										<a href="../editor/editreviewer.php?collid=<?php echo $collid; ?>">
                                            Review/Verify Occurrence Edits
										</a>
									</li>
									<li>
										<a href="../reports/accessreport.php?collid=<?php echo $collid; ?>">
                                            View Access Statistics
										</a>
									</li>
									<?php
								}
								?>
								<li>
									<a href="../datasets/duplicatemanager.php?collid=<?php echo $collid; ?>">
                                        Duplicate Clustering
									</a>
								</li>
								<li>
                                    General Maintenance Tasks
								</li>
								<?php
								if($collData['colltype'] !== 'General Observations'){
									?>
									<li style="margin-left:10px;">
										<a href="../cleaning/index.php?obsuid=0&collid=<?php echo $collid; ?>">
                                            Data Cleaning Tools
										</a>
									</li>
									<?php
								}
								?>
								<li style="margin-left:10px;">
									<a href="#" onclick="newWindow = window.open('collbackup.php?collid=<?php echo $collid; ?>','bucollid','scrollbars=1,toolbar=0,resizable=1,width=600,height=250,left=20,top=20');">
                                        Download Backup Data File
									</a>
								</li>
								<li style="margin-left:10px;">
									<a href="../../imagelib/admin/thumbnailbuilder.php?collid=<?php echo $collid; ?>">
                                        Thumbnail Maintenance
									</a>
								</li>
								<li style="margin-left:10px;">
									<a href="collprofiles.php?collid=<?php echo $collid; ?>&action=UpdateStatistics" >
                                        Update Statistics
									</a>
								</li>
                                <?php
                                if($SOLR_MODE){
                                    ?>
                                    <li style="margin-left:10px;">
                                        <a href="collprofiles.php?collid=<?php echo $collid; ?>&action=cleanSOLR">
                                            Clean SOLR Index
                                        </a>
                                    </li>
                                    <?php
                                }
                                ?>
							</ul>
						</fieldset>
						<?php
					}
					?>
				</div>
				<?php
			}
			?>
			<div style='margin:10px;'>
				<?php
				echo $collManager->getMetadataHtml($collData);
                if($publishGBIF && $datasetKey){
                    $dataUrl = 'http://www.gbif.org/dataset/'.$datasetKey;
                    ?>
                    <div style="margin-top:5px;">
                        <div><b>GBIF Dataset page:</b> <a href="<?php echo $dataUrl; ?>" target="_blank"><?php echo $dataUrl; ?></a></div>
                    </div>
                    <?php
                }
                if($publishIDIGBIO && $idigbioKey){
                    $dataUrl = 'https://www.idigbio.org/portal/recordsets/'.$idigbioKey;
                    ?>
                    <div style="margin-top:5px;">
                        <div><b>iDigBio Dataset page:</b> <a href="<?php echo $dataUrl; ?>" target="_blank"><?php echo $dataUrl; ?></a></div>
                    </div>
                    <?php
                }
                if($addrArr = $collManager->getAddress()){
					?>
					<div style="margin-top:5px;">
						<div style="float:left;font-weight:bold;">Address:</div>
						<div style="float:left;margin-left:10px;">
							<?php
							echo '<div>' .$addrArr['institutionname'];
							if($editCode > 1) {
                                echo ' <a href="../admin/institutioneditor.php?emode=1&targetcollid=' . $collid . '&iid=' . $addrArr['iid'] . '" title="Edit institution information"><img src="../../images/edit.png" style="width:13px;" /></a>';
                            }
							echo '</div>';
							if($addrArr['institutionname2']) {
                                echo '<div>' . $addrArr['institutionname2'] . '</div>';
                            }
							if($addrArr['address1']) {
                                echo '<div>' . $addrArr['address1'] . '</div>';
                            }
							if($addrArr['address2']) {
                                echo '<div>' . $addrArr['address2'] . '</div>';
                            }
							if($addrArr['city']) {
                                echo '<div>' . $addrArr['city'] . ', ' . $addrArr['stateprovince'] . '&nbsp;&nbsp;&nbsp;' . $addrArr['postalcode'] . '</div>';
                            }
							if($addrArr['country']) {
                                echo '<div>' . $addrArr['country'] . '</div>';
                            }
							if($addrArr['phone']) {
                                echo '<div>' . $addrArr['phone'] . '</div>';
                            }
							if($addrArr['url']) {
                                echo '<div><a href="' . $addrArr['url'] . '">' . $addrArr['url'] . '</a></div>';
                            }
							if($addrArr['notes']) {
                                echo '<div>' . $addrArr['notes'] . '</div>';
                            }
							?>
						</div>
					</div>
					<?php
				}
				$statsArr = $collManager->getBasicStats();
				$extrastatsArr = array();
				$georefPerc = 0;
				if($statsArr['georefcnt']&&$statsArr['recordcnt']){
					$georefPerc = (100*($statsArr['georefcnt']/$statsArr['recordcnt']));
				}
				$spidPerc = 0;
				$imgPerc = 0;
				if($statsArr['dynamicProperties']){
					$extrastatsArr = json_decode($statsArr['dynamicProperties'],true);
					if(is_array($extrastatsArr)){
						if($extrastatsArr['SpecimensCountID']){
							$spidPerc = (100*($extrastatsArr['SpecimensCountID']/$statsArr['recordcnt']));
						}
						if($extrastatsArr['imgcnt']){
							$imgPerc = (100*($extrastatsArr['imgcnt']/$statsArr['recordcnt']));
						}
					}
				}
				?>
				<div style="clear:both;margin-top:5px;">
					<div style="font-weight:bold;">Collection Statistics</div>
					<ul style="margin-top:5px;">
						<li><?php echo number_format($statsArr['recordcnt']);?> occurrence</li>
						<li><?php echo ($statsArr['georefcnt']?number_format($statsArr['georefcnt']):0).($georefPerc? ' (' .($georefPerc>1?round($georefPerc):round($georefPerc,2)). '%)' :'');?> georeferenced</li>
						<?php
						if($extrastatsArr){
							if($extrastatsArr['imgcnt']) {
                                echo '<li>' . number_format($extrastatsArr['imgcnt']) . ($imgPerc ? ' (' . ($imgPerc > 1 ? round($imgPerc) : round($imgPerc, 2)) . '%)' : '') . ' with images</li>';
                            }
							if($extrastatsArr['gencnt']) {
                                echo '<li>' . number_format($extrastatsArr['gencnt']) . ' GenBank references</li>';
                            }
							if($extrastatsArr['boldcnt']) {
                                echo '<li>' . number_format($extrastatsArr['boldcnt']) . ' BOLD references</li>';
                            }
							if($extrastatsArr['refcnt']) {
                                echo '<li>' . number_format($extrastatsArr['refcnt']) . ' publication references</li>';
                            }
							if($extrastatsArr['SpecimensCountID']) {
                                echo '<li>' . number_format($extrastatsArr['SpecimensCountID']) . ($spidPerc ? ' (' . ($spidPerc > 1 ? round($spidPerc) : round($spidPerc, 2)) . '%)' : '') . ' identified to species</li>';
                            }
						}
						?>
						<li><?php echo number_format($statsArr['familycnt']);?> families</li>
						<li><?php echo number_format($statsArr['genuscnt']);?> genera</li>
						<li><?php echo number_format($statsArr['speciescnt']);?> species</li>
						<?php
						if($extrastatsArr&&$extrastatsArr['TotalTaxaCount']) {
                            echo '<li>' . number_format($extrastatsArr['TotalTaxaCount']) . ' total taxa (including subsp. and var.)</li>';
                        }
						?>
					</ul>
				</div>
			</div>
			<fieldset style='margin:20px;padding:10px;width:300px;background-color:#FFFFCC;'>
				<legend><b>Extra Statistics</b></legend>
				<div style="margin:3px;">
					<a href="collprofiles.php?collid=<?php echo $collid; ?>&stat=geography#geographystats" >Show Geographic Distribution</a>
				</div>
				<div style="margin:3px;">
					<a href="collprofiles.php?collid=<?php echo $collid; ?>&stat=taxonomy#taxonomystats" >Show Family Distribution</a>
				</div>
			</fieldset>
			<?php
			include('collprofilestats.php');
		}
		else{
			?>
			<h2><?php echo $DEFAULT_TITLE; ?> Natural History Collections and Observation Projects</h2>
			<div style='margin:10px;clear:both;'>
				<?php
				$serverDomain = 'http://';
				if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
                    $serverDomain = 'https://';
                }
				$serverDomain .= $_SERVER['HTTP_HOST'];
				if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80) {
                    $serverDomain .= ':' . $_SERVER['SERVER_PORT'];
                }
				echo 'RSS feed: <a href="../datasets/rsshandler.php" target="_blank">'.$serverDomain.$CLIENT_ROOT.'collections/datasets/rsshandler.php</a>';
				?>
				<hr/>
			</div>
			<table style='margin:10px;'>
				<?php
				foreach($collData as $cid => $collArr){
					?>
					<tr>
						<td style='text-align:center;vertical-align:top;'>
							<?php
							$iconStr = $collArr['icon'];
							if($iconStr){
								if(strpos($iconStr, 'images') === 0) {
                                    $iconStr = '../../' . $iconStr;
                                }
								?>
								<img src='<?php echo $iconStr; ?>' style='border-width:1px;height:30px;width:30px;' /><br/>
								<?php
								echo $collArr['institutioncode'];
								if($collArr['collectioncode']) {
                                    echo '-' . $collArr['collectioncode'];
                                }
							}
							?>
						</td>
						<td>
							<h3>
								<a href='collprofiles.php?collid=<?php echo $cid;?>'>
									<?php echo $collArr['collectionname']; ?>
								</a>
							</h3>
							<div style='margin:10px;'>
								<?php
								echo $collManager->getMetadataHtml($collArr);
								?>
							</div>
							<div style='margin:5px 0 15px 10px;'>
								<a href='collprofiles.php?collid=<?php echo $cid; ?>'>More Information</a>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='2'><hr/></td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
		}
		?>
	</div>
	<?php
	include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>

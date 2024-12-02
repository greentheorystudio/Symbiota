<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceCollectionProfile.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$action = array_key_exists('action',$_REQUEST)?htmlspecialchars($_REQUEST['action']): '';
$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;

$statusStr = '';

$collManager = new OccurrenceCollectionProfile();
if(!$collManager->setCollid($collid)) {
    $collid = '';
}

$isEditor = 0;
$collPubArr = array();
$publishGBIF = false;
$publishIDIGBIO = false;
$collData = array();

if($GLOBALS['IS_ADMIN']){
	$isEditor = 1;
}
elseif($collid){
	if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
		$isEditor = 1;
	}
}

if($isEditor){
	if($action === 'Save Edits'){
		$statusStr = $collManager->submitCollEdits($_POST);
	}
	elseif($action === 'Create New Collection'){
		if($GLOBALS['IS_ADMIN']){
			$newCollid = $collManager->submitCollAdd($_POST);
			if(is_numeric($newCollid)){
				header('Location: collprofiles.php?collid='.$newCollid);
			}
			else{
				$statusStr = $collid;
			}
		}
	}
	elseif($action === 'Link Address'){
		if(!$collManager->linkAddress($_POST['iid'])){
			$statusStr = $collManager->getErrorStr();
		}
	}
	elseif(array_key_exists('removeiid',$_GET)){
		if(!$collManager->removeAddress($_GET['removeiid'])){
			$statusStr = $collManager->getErrorStr();
		}
	}
}
if(isset($GLOBALS['GBIF_USERNAME'], $GLOBALS['GBIF_PASSWORD'], $GLOBALS['GBIF_ORG_KEY']) && $GLOBALS['GBIF_USERNAME'] && $GLOBALS['GBIF_PASSWORD'] && $GLOBALS['GBIF_ORG_KEY'] && $collid){
	$collPubArr = $collManager->getCollPubArr($collid);
	if($collPubArr[$collid]['publishToGbif']){
		$publishGBIF = true;
	}
	if($collPubArr[$collid]['publishToIdigbio']){
		$publishIDIGBIO = true;
	}
}
if($collid){
    $collDataFull = $collManager->getCollectionMetadata();
    $collData = SanitizerService::cleanOutArray($collDataFull[$collid]);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE'].' '.($collid?'Edit Collection Metadata':'Create New Collection Profile'); ?></title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
    <style>
        fieldset {
            background-color: #f9f9f9;
            padding:15px;
        }
        legend {
            font-weight: bold;
        }
        .field-block {
            margin: 5px 0;
        }
        .field-label {
            font-weight: bold;
        }
    </style>
    <script src="../../js/external/jquery.js" type="text/javascript"></script>
	<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dialogArr = ["instcode", "collcode", "pedits", "pubagg", "rights", "rightsholder", "accessrights", "guid", "colltype", "management", "icon", "collectionguid", "sourceurl", "collectionid"];
            let dialogStr = "";
            for(let i=0;i<dialogArr.length;i++){
				dialogStr = dialogArr[i]+"info";
				$( "#"+dialogStr+"dialog" ).dialog({
					autoOpen: false,
					modal: true,
					position: { my: "left top", at: "right bottom", of: "#"+dialogStr }
				});

				$( "#"+dialogStr ).click(function() {
					$( "#"+this.id+"dialog" ).dialog( "open" );
				});
			}

            $('#tabs').tabs({});
		});

        function openSpatialInputWindow(type) {
            let mapWindow = open("../../spatial/index.php?windowtype=" + type,"input","resizable=0,width=900,height=700,left=100,top=20");
            if (mapWindow.opener == null) {
                mapWindow.opener = self;
            }
            mapWindow.addEventListener('blur', function(){
                mapWindow.close();
                mapWindow = null;
            });
        }

		function verifyCollEditForm(f){
			if(f.collectionname.value === ''){
				alert("Collection Name must have a value");
				return false;
			}
			else if(f.managementtype.value === "Snapshot" && f.guidtarget.value === "symbiotaUUID"){
				alert("The Generated GUID option cannot be selected for a collection that is managed locally outside of the data portal (e.g. Snapshot management type). In this case, the GUID must be generated within the source collection database and delivered to the data portal as part of the upload process.");
				return false;
			}
			else if(isNaN(f.latitudedecimal.value) || isNaN(f.longitudedecimal.value)){
				alert("Latitdue and longitude values must be in the decimal format (numeric only)");
				return false;
			}
			else if(f.rights.value === ""){
				alert("Rights field (e.g. Creative Commons license) must have a selection");
				return false;
			}
			return true;
		}

		function mtypeguidChanged(f){
			if(f.managementtype.value === "Snapshot" && f.guidtarget.value === "symbiotaUUID"){
				alert("The Generated GUID option cannot be selected for a collection that is managed locally outside of the data portal (e.g. Snapshot management type). In this case, the GUID must be generated within the source collection database and delivered to the data portal as part of the upload process.");
			}
			else if(f.managementtype.value === "Aggregate" && f.guidtarget.value !== "" && f.guidtarget.value !== "occurrenceId"){
				alert("An Aggregate dataset (e.g. occurrences coming from multiple collections) can only have occurrenceID selected for the GUID source");
				f.guidtarget.value = 'occurrenceId';
			}
			if(!f.guidtarget.value){
				f.publishToGbif.checked = false;
			}
		}
		
		function checkGUIDSource(f){
			if(f.publishToGbif.checked === true){
				if(!f.guidtarget.value){
					alert("You must select a GUID source in order to publish to data aggregators.");
					f.publishToGbif.checked = false;
				}
			}
		}

		function verifyAddAddressForm(f){
			if(f.iid.value === ""){
				alert("Select an institution to be linked");
				return false;
			}
			return true;
		}
		
		function verifyIconImage(){
            const iconImageFile = document.getElementById("iconfile").value;
            if(iconImageFile){
                let iconExt = iconImageFile.substring(iconImageFile.length - 4);
                iconExt = iconExt.toLowerCase();
				if((iconExt !== '.jpg') && (iconExt !== 'jpeg') && (iconExt !== '.png') && (iconExt !== '.gif')){
					document.getElementById("iconfile").value = '';
					alert("The file you have uploaded is not a supported image file. Please upload a jpg, png, or gif file.");
				}
				else{
                    const fr = new FileReader;
                    fr.onload = function(){
                        let img = new Image;
                        img.onload = function(){
							if((img.width>350) || (img.height>350)){
								document.getElementById("iconfile").value = '';
								img = '';
								alert("The image file must be less than 350 pixels in both width and height.");
							}
						};
						img.src = fr.result;
					};
					fr.readAsDataURL(document.getElementById("iconfile").files[0]);
				}
			}
		}
		
		function verifyIconURL(){
            const iconImageFile = document.getElementById("iconurl").value;
            if((iconImageFile.substring(iconImageFile.length-4) !== '.jpg') && (iconImageFile.substring(iconImageFile.length-4) !== '.png') && (iconImageFile.substring(iconImageFile.length-4) !== '.gif')){
				document.getElementById("iconurl").value = '';
				alert("The url you have entered is not for a supported image file. Please enter a url for a jpg, png, or gif file.");
			}
		}

        function processDataCollectionMethodChange(){
            const selectedValue = document.getElementById("datarecordingmethod").value;
            if(selectedValue === 'benthic'){
                document.getElementById('defaultRepCountBlock').style.display = "block";
            }
            else{
                document.getElementById('defaultRepCountBlock').style.display = "none";
                document.getElementById("defaultRepCount").value = '';
            }
        }
    </script>
</head>
<body>
	<?php
	include(__DIR__ . '/../../header.php');
	echo '<div class="navpath">';
    echo '<a href="../../index.php">Home</a> &gt;&gt; ';
    if($collid){
        echo '<a href="collprofiles.php?collid='.$collid.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
        echo '<b>Edit Collection Metadata</b>';
    }
    else{
        echo '<b>Create New Collection Profile</b>';
    }
	echo '</div>';
	?>

	<div id="innertext">
		<?php
		if($statusStr){ 
			?>
			<hr />
			<div style="margin:20px;font-weight:bold;color:red;">
				<?php echo $statusStr; ?>
			</div>
			<hr />
			<?php 
		}
        ?>
        <div id="tabs" style="margin:0px;">
            <?php
            if($isEditor){
                if($collid){
                    echo '<h1>'.$collData['collectionname'].($collData['institutioncode']?' ('.$collData['institutioncode'].')':'').'</h1>';
                }
                ?>
                <div id="colledit">
                    <fieldset>
                        <legend><b><?php echo ($collid?'Edit':'Add New'); ?> Collection Information</b></legend>
                        <form id="colleditform" name="colleditform" action="collmetadata.php" method="post" enctype="multipart/form-data" onsubmit="return verifyCollEditForm(this)">
                            <div class="field-block">
                                <span class="field-label">Institution Code:</span>
                                <span class="field-elem">
									<input type="text" name="institutioncode" value="<?php echo ($collid?$collData['institutioncode']:'');?>" />
                                    <a id="instcodeinfo" href="#" onclick="return false" title="More information about Institution Code">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
									<span id="instcodeinfodialog">
										The name (or acronym) in use by the institution having custody of the occurrence records.
                                        For more details, see <a href="https://dwc.tdwg.org/terms/#institutionCode" target="_blank">Darwin Core definition</a>
									</span>
								</span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Collection Code:</span>
                                <span class="field-elem">
									<input type="text" name="collectioncode" value="<?php echo ($collid?$collData['collectioncode']:'');?>" style="width:75px;" />
                                    <a id="collcodeinfo" href="#" onclick="return false" title="More information about Collection Code">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
									<span id="collcodeinfodialog">
										The name, acronym, or code identifying the collection or data set from which the record was derived.
                                        For more details, see <a href="https://dwc.tdwg.org/terms/#collectionCode" target="_blank">Darwin Core definition</a>.
									</span>
								</span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Collection Name:</span>
                                <span class="field-elem">
									<input type="text" name="collectionname" value="<?php echo ($collid?$collData['collectionname']:'');?>" style="width:600px;" title="Required field" />
                                </span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Description (2000 character max):</span>
                                <span class="field-elem">
									<textarea name="fulldescription" style="width:95%;height:90px;"><?php echo ($collid?$collData['fulldescription']:'');?></textarea>
                                </span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Homepage:</span>
                                <span class="field-elem">
									<input type="text" name="homepage" value="<?php echo ($collid?$collData['homepage']:'');?>" style="width:600px;" />
                                </span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Contact:</span>
                                <span class="field-elem">
									<input type="text" name="contact" value="<?php echo ($collid?$collData['contact']:'');?>" style="width:600px;" />
                                </span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Email:</span>
                                <span class="field-elem">
									<input type="text" name="email" value="<?php echo ($collid?$collData['email']:'');?>" style="width:600px;" />
                                </span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Latitude:</span>
                                <span class="field-elem">
									<input id="decimallatitude" type="text" name="latitudedecimal" value="<?php echo ($collid?$collData['latitudedecimal']:'');?>" />
                                    <span style="cursor:pointer;" onclick="openSpatialInputWindow('input-point');">
                                        <i style="height:15px;width:15px;" class="fas fa-globe"></i>
                                    </span>
                                </span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Longitude:</span>
                                <span class="field-elem">
									<input id="decimallongitude" type="text" name="longitudedecimal" value="<?php echo ($collid?$collData['longitudedecimal']:'');?>" />
                                </span>
                            </div>
                            <?php
                            $fullCatArr = $collManager->getCategoryArr();
                            if($fullCatArr){
                                ?>
                                <div class="field-block">
                                    <span class="field-label">Category:</span>
                                    <span class="field-elem">
                                        <select name="ccpk">
                                            <option value="">No Category</option>
                                            <option value="">-------------------------------------------</option>
                                            <?php
                                            $catArr = $collManager->getCollectionCategories();
                                            foreach($fullCatArr as $ccpk => $category){
                                                echo '<option value="'.$ccpk.'" '.($collid && array_key_exists($ccpk, $catArr)?'SELECTED':'').'>'.$category.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </span>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="field-block">
                                <span class="field-label">License:</span>
                                <span class="field-elem">
									<?php
                                    if(isset($GLOBALS['RIGHTS_TERMS']) && $GLOBALS['RIGHTS_TERMS']){
                                        ?>
                                        <select name="rights">
                                            <?php
                                            foreach($GLOBALS['RIGHTS_TERMS'] as $k => $v){
                                                echo '<option value="'.$k.'" '.(strtolower($collData['rights']) === strtolower($k)?'SELECTED':'').'>'.$v['title'].'</option>'."\n";
                                            }
                                            ?>
                                        </select>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <input type="text" name="rights" value="<?php echo ($collid?$collData['rights']:'');?>" style="width:90%;" />
                                        <?php
                                    }
                                    ?>
                                    <a id="rightsinfo" href="#" onclick="return false" title="More information about Rights">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
									<span id="rightsinfodialog">
										A legal document giving official permission to do something with the resource.
                                        This field can be limited to a set of values by modifying the portal's central configuration file.
                                        For more details, see <a href="https://dwc.tdwg.org/terms/#dcterms:license" target="_blank">Darwin Core definition</a>.
									</span>
								</span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Rights Holder:</span>
                                <span class="field-elem">
									<input type="text" name="rightsholder" value="<?php echo ($collid?$collData['rightsholder']:'');?>" style="width:600px" />
                                    <a id="rightsholderinfo" href="#" onclick="return false" title="More information about Rights Holder">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
									<span id="rightsholderinfodialog">
										The organization or person managing or owning the rights of the resource.
                                        For more details, see <a href="https://dwc.tdwg.org/terms/#dcterms:rightsHolder" target="_blank">Darwin Core definition</a>.
									</span>
								</span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Access Rights:</span>
                                <span class="field-elem">
									<input type="text" name="accessrights" value="<?php echo ($collid?$collData['accessrights']:'');?>" style="width:600px" />
                                    <a id="accessrightsinfo" href="#" onclick="return false" title="More information about Access Rights">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
									<span id="accessrightsinfodialog">
										Informations or a URL link to page with details explaining how one can use the data.
                                        See <a href="https://dwc.tdwg.org/terms/#dcterms:accessRights" target="_blank">Darwin Core definition</a>.
									</span>
								</span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Dataset Type:</span>
                                <span class="field-elem">
                                    <select name="colltype">
                                        <option value="PreservedSpecimen" <?php echo ($collid && ($collData['colltype'] === 'PreservedSpecimen')?'SELECTED':''); ?>>Preserved Specimens</option>
                                        <option value="HumanObservation" <?php echo ($collid && ($collData['colltype'] === 'HumanObservation')?'SELECTED':''); ?>>Observations</option>
                                        <option value="FossilSpecimen" <?php echo ($collid && $collData['colltype'] === 'FossilSpecimen'?'SELECTED':''); ?>>Fossil Specimens</option>
                                        <option value="LivingSpecimen" <?php echo ($collid && $collData['colltype'] === 'LivingSpecimen'?'SELECTED':''); ?>>Living Specimens</option>
                                        <option value="MaterialSample" <?php echo ($collid && $collData['colltype'] === 'MaterialSample'?'SELECTED':''); ?>>Material Samples</option>
                                    </select>
                                    <a id="colltypeinfo" href="#" onclick="return false" title="More information about Collection Type">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
                                    <span id="colltypeinfodialog">
                                        Preserved Specimens signify a collection type that contains physical samples that are available for inspection by researchers and taxonomic experts.
                                        Observations are collections where records are not based on a physical specimens.
                                    </span>
                                </span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Management:</span>
                                <span class="field-elem">
                                    <select name="managementtype" onchange="mtypeguidChanged(this.form)">
                                        <option>Snapshot</option>
                                        <option <?php echo ($collid && $collData['managementtype'] === 'Live Data'?'SELECTED':''); ?>>Live Data</option>
                                        <option <?php echo ($collid && $collData['managementtype'] === 'Aggregate'?'SELECTED':''); ?>>Aggregate</option>
                                    </select>
                                    <a id="managementinfo" href="#" onclick="return false" title="More information about Management Type">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
                                    <span id="managementinfodialog">
                                        Use Snapshot when there is a separate in-house database maintained in the collection and the dataset
                                        within the portal is only a periodically updated snapshot of the central database.
                                        A Live dataset is when the data is managed directly within the portal and the central database is the portal data.
                                    </span>
                                </span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Occurrence Recording Format:</span>
                                <span class="field-elem">
                                    <select name="datarecordingmethod" id="datarecordingmethod" onchange="processDataCollectionMethodChange();">
                                        <option value="specimen" <?php echo ($collid && $collData['datarecordingmethod'] === 'specimen'?'SELECTED':''); ?>>Specimen</option>
                                        <option value="observation" <?php echo ($collid && $collData['datarecordingmethod'] === 'observation'?'SELECTED':''); ?>>Observation</option>
                                        <option value="skeletal" <?php echo ($collid && $collData['datarecordingmethod'] === 'skeletal'?'SELECTED':''); ?>>Skeletal</option>
                                        <option value="lot" <?php echo ($collid && $collData['datarecordingmethod'] === 'lot'?'SELECTED':''); ?>>Lot</option>
                                        <option value="benthic" <?php echo ($collid && $collData['datarecordingmethod'] === 'benthic'?'SELECTED':''); ?>>Benthic</option>
                                    </select>
                                </span>
                            </div>
                            <div class="field-block" id="defaultRepCountBlock" style="display:<?php echo (($collid && $collData['datarecordingmethod'] === 'benthic')?'block':'none'); ?>;">
                                <span class="field-label">Default Rep Count:</span>
                                <span class="field-elem">
                                    <input type="text" name="defaultRepCount" id="defaultRepCount" value="<?php echo ($collid?$collData['defaultRepCount']:'');?>" />
                                </span>
                            </div>
                            <div class="field-block">
                                <span class="field-label" title="Source of Global Unique Identifier">GUID source:</span>
                                <span class="field-elem">
									<select name="guidtarget" onchange="mtypeguidChanged(this.form)">
                                        <option value="">Not defined</option>
                                        <option value="">-------------------</option>
                                        <option value="occurrenceId" <?php echo ($collid && $collData['guidtarget'] === 'occurrenceId'?'SELECTED':''); ?>>Occurrence Id</option>
                                        <option value="catalogNumber" <?php echo ($collid && $collData['guidtarget'] === 'catalogNumber'?'SELECTED':''); ?>>Catalog Number</option>
                                        <option value="symbiotaUUID" <?php echo ($collid && $collData['guidtarget'] === 'symbiotaUUID'?'SELECTED':''); ?>>Generated GUID (UUID)</option>
                                    </select>
                                    <a id="guidinfo" href="#" onclick="return false" title="More information about Global Unique Identifier">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
									<span id="guidinfodialog">
										Occurrence Id is generally used for Snapshot datasets when a Global Unique Identifier (GUID) field
                                        is supplied by the source database (e.g. Specify database) and the GUID is mapped to the
                                        <a href="https://dwc.tdwg.org/terms/#occurrenceID" target="_blank">occurrenceId</a> field.
                                        The use of the Occurrence Id as the GUID is not recommended for live datasets.
                                        Catalog Number can be used when the value within the catalog number field is globally unique.
                                        The Generated GUID (UUID) option will trigger the portal to automatically
                                        generate UUID GUIDs for each record. This option is recommended for many for Live Datasets
                                        but not allowed for Snapshot collections that are managed in local management system.
									</span>
								</span>
                            </div>
                            <?php
                            if(isset($GLOBALS['GBIF_USERNAME'], $GLOBALS['GBIF_PASSWORD'], $GLOBALS['GBIF_ORG_KEY']) && $GLOBALS['GBIF_USERNAME'] && $GLOBALS['GBIF_PASSWORD'] && $GLOBALS['GBIF_ORG_KEY']) {
                                ?>
                                <div class="field-block">
                                    <span class="field-label">Publish to Aggregators:</span>
                                    <span class="field-elem">
                                        GBIF <input type="checkbox" name="publishToGbif" value="1" onchange="checkGUIDSource(this.form);" <?php echo($publishGBIF ? 'CHECKED' : ''); ?> />
                                        <a id="pubagginfo" href="#" onclick="return false" title="More information about Publishing to Aggregators">
                                            <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                        </a>
                                        <span id="pubagginfodialog">
                                            Activates the GBIF publishing tools within the Darwin Core Archive Publishing module.
                                        </span>
                                    </span>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="field-block">
                                <span class="field-label">Source Record URL:</span>
                                <span class="field-elem">
									<input type="text" name="individualurl" style="width:600px" value="<?php echo ($collid?$collData['individualurl']:'');?>" title="Dynamic link to source database individual record page" />
                                    <a id="sourceurlinfo" href="#" onclick="return false" title="More information about Source Records URL">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
									<span id="sourceurlinfodialog">
										Adding a URL template here will dynamically generate and add the occurrence details page a link to the
                                        source record. For example, &quot;http://sweetgum.nybg.org/vh/specimen.php?irn=--DBPK--&quot;
                                        will generate a url to the NYBG collection with &quot;--DBPK--&quot; being replaced with the
                                        NYBG's Primary Key (dbpk data field within the ommoccurrence table).
                                        Template pattern --CATALOGNUMBER-- can also be used in place of --DBPK--
									</span>
								</span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Icon URL:</span>
                                <span class="field-elem">
									<span class="targetelem" style="<?php echo (($collid&&$collData['icon'])?'display:none;':''); ?>">
										<input name='iconfile' id='iconfile' type='file' size='70' onchange="verifyIconImage();" />
									</span>
									<span class="targetelem" style="<?php echo (($collid&&$collData['icon'])?'':'display:none;'); ?>">
										<input style="width:600px;" type='text' name='iconurl' id='iconurl' value="<?php echo ($collid?$collData['icon']:'');?>" onchange="verifyIconURL();" />
									</span>
                                    <a id="iconinfo" href="#" onclick="return false" title="What is an Icon?">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
									<span id="iconinfodialog">
										Upload an icon image file or enter the URL of an image icon that represents the collection. If entering the URL of an image already located
                                        on a server, click on &quot;Enter URL&quot;. The URL path can be absolute or relative. The use of icons are optional.
									</span>
								</span>
                                <span class="targetelem" style="<?php echo (($collid&&$collData['icon'])?'display:none;':''); ?>">
									<a href="#" onclick="toggle('targetelem','inline-block');return false;">Enter URL</a>
								</span>
                                <span class="targetelem" style="<?php echo (($collid&&$collData['icon'])?'':'display:none;'); ?>">
									<a href="#" onclick="toggle('targetelem','inline-block');return false;">Upload Local Image</a>
								</span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Collection ID (GUID):</span>
                                <span class="field-elem">
                                    <input type="text" name="collectionid" value="<?php echo ($collid?$collData['collectionid']:'');?>" style="width:400px" />
                                    <a id="collectionidinfo" href="#" onclick="return false" title="More information">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
                                    <span id="collectionidinfodialog">
                                        If your collection already has a previously assigned GUID, that identifier should be entered here.
										For physical specimens, the recommended best practice is to use an identifier from a collections registry such as the
										Global Registry of Biodiversity Repositories (<a href="http://grbio.org" target="_blank">http://grbio.org</a>).
                                    </span>
                                </span>
                            </div>
                            <div class="field-block">
                                <span class="field-label">Is Public:</span>
                                <span class="field-elem">
									<input type="checkbox" name="isPublic" value="1" <?php echo ((!$collid || (int)$collData['isPublic'] === 1)?'CHECKED':''); ?> />
                                </span>
                            </div>
                            <?php
                            if($collid){
                                ?>
                                <div class="field-block">
                                    <span class="field-label">Security Key:</span>
                                    <span class="field-elem">
										<?php echo $collData['skey']; ?>
									</span>
                                </div>
                                <div class="field-block">
                                    <span class="field-label">Record ID:</span>
                                    <span class="field-elem">
										<?php echo $collData['guid']; ?>
									</span>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="field-block">
                                <div style="margin:20px;">
                                    <?php
                                    if($collid){
                                        ?>
                                        <input type="hidden" name="collid" value="<?php echo $collid;?>" />
                                        <button type="submit" name="action" value="Save Edits">Save Edits</button>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <button type="submit" name="action" value="Create New Collection">Create New Collection</button>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                </div>
                <div>
                    <fieldset>
                        <legend><b>Mailing Address</b></legend>
                        <?php
                        if($instArr = $collManager->getAddress()){
                            ?>
                            <div style="margin:25px;">
                                <?php
                                echo '<div>';
                                echo $instArr['institutionname'].($instArr['institutioncode']?' ('.$instArr['institutioncode'].')':'');
                                ?>
                                <a href="institutioneditor.php?emode=1&targetcollid=<?php echo $collid.'&iid='.$instArr['iid']; ?>" title="Edit institution address">
                                    <i style="height:15px;width:15px;" class="far fa-edit"></i>
                                </a>
                                <a href="collmetadata.php?collid=<?php echo $collid.'&removeiid='.$instArr['iid']; ?>" title="Unlink institution address">
                                    <i style="height:15px;width:15px;" class="far fa-trash-alt"></i>
                                </a>
                                <?php
                                echo '</div>';
                                if($instArr['address1']) {
                                    echo '<div>' . $instArr['address1'] . '</div>';
                                }
                                if($instArr['address2']) {
                                    echo '<div>' . $instArr['address2'] . '</div>';
                                }
                                if($instArr['city'] || $instArr['stateprovince']) {
                                    echo '<div>' . $instArr['city'] . ', ' . $instArr['stateprovince'] . ' ' . $instArr['postalcode'] . '</div>';
                                }
                                if($instArr['country']) {
                                    echo '<div>' . $instArr['country'] . '</div>';
                                }
                                if($instArr['phone']) {
                                    echo '<div>' . $instArr['phone'] . '</div>';
                                }
                                if($instArr['contact']) {
                                    echo '<div>' . $instArr['contact'] . '</div>';
                                }
                                if($instArr['email']) {
                                    echo '<div>' . $instArr['email'] . '</div>';
                                }
                                if($instArr['url']) {
                                    echo '<div><a href="' . $instArr['url'] . '">' . $instArr['url'] . '</a></div>';
                                }
                                if($instArr['notes']) {
                                    echo '<div>' . $instArr['notes'] . '</div>';
                                }
                                ?>
                            </div>
                            <?php
                        }
                        else{
                            ?>
                            <div style="margin:40px;"><b>No addesses linked</b></div>
                            <div style="margin:20px;">
                                <form name="addaddressform" action="collmetadata.php" method="post" onsubmit="return verifyAddAddressForm(this)">
                                    <select name="iid" style="width:425px;">
                                        <option value="">Select Institution Address</option>
                                        <option value="">------------------------------------</option>
                                        <?php
                                        $addrArr = $collManager->getInstitutionArr();
                                        foreach($addrArr as $iid => $name){
                                            echo '<option value="'.$iid.'">'.$name.'</option>';
                                        }
                                        ?>
                                    </select>
                                    <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
                                    <input name="action" type="submit" value="Link Address" />
                                </form>
                                <div style="margin:15px;">
                                    <a href="institutioneditor.php?emode=1&targetcollid=<?php echo $collid; ?>" title="Add a new address not on the list">
                                        <b>Add an institution not on list</b>
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </fieldset>
                </div>
                <?php
            }
            ?>
        </div>
	</div>
	<?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>

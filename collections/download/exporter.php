<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceDownload.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;

$customField = array();
$customType = array();
$customValue = array();

for($h=1;$h<4;$h++){
	$customField[$h] = array_key_exists('customfield'.$h,$_REQUEST)?$_REQUEST['customfield'.$h]:'';
	$customType[$h] = array_key_exists('customtype'.$h,$_REQUEST)?$_REQUEST['customtype'.$h]:'';
	$customValue[$h] = array_key_exists('customvalue'.$h,$_REQUEST)?$_REQUEST['customvalue'.$h]:'';
}

$dlManager = new OccurrenceDownload();
$collMeta = $dlManager->getCollectionMetadata($collid);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
 	$isEditor = true;
}

$advFieldArr = array('family'=>'Family','sciname'=>'Scientific Name','identifiedBy'=>'Identified By','typeStatus'=>'Type Status',
	'catalogNumber'=>'Catalog Number','otherCatalogNumbers'=>'Other Catalog Numbers','occurrenceId'=>'Occurrence ID (GUID)',
	'recordedBy'=>'Collector/Observer','recordNumber'=>'Collector Number','associatedCollectors'=>'Associated Collectors',
	'eventDate'=>'Collection Date','verbatimEventDate'=>'Verbatim Date','habitat'=>'Habitat','substrate'=>'Substrate','occurrenceRemarks'=>'Occurrence Remarks',
	'associatedTaxa'=>'Associated Taxa','verbatimAttributes'=>'Description','reproductiveCondition'=>'Reproductive Condition',
	'establishmentMeans'=>'Establishment Means','lifeStage'=>'Life Stage','sex'=>'Sex',
	'individualCount'=>'Individual Count','samplingProtocol'=>'Sampling Protocol','country'=>'Country',
	'stateProvince'=>'State/Province','county'=>'County','municipality'=>'Municipality','locality'=>'Locality',
	'decimalLatitude'=>'Decimal Latitude','decimalLongitude'=>'Decimal Longitude','geodeticDatum'=>'Geodetic Datum',
	'coordinateUncertaintyInMeters'=>'Uncertainty (m)','verbatimCoordinates'=>'Verbatim Coordinates',
	'georeferencedBy'=>'Georeferenced By','georeferenceProtocol'=>'Georeference Protocol','georeferenceSources'=>'Georeference Sources',
	'georeferenceVerificationStatus'=>'Georeference Verification Status','georeferenceRemarks'=>'Georeference Remarks',
	'minimumElevationInMeters'=>'Elevation Minimum (m)','maximumElevationInMeters'=>'Elevation Maximum (m)',
	'verbatimElevation'=>'Verbatim Elevation','disposition'=>'Disposition');
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<title>Occurrence Export Manager</title>
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/external/jquery-ui.css?ver=20220720" type="text/css" rel="stylesheet" />
        <script src="../../js/external/all.min.js" type="text/javascript"></script>
		<script src="../../js/external/jquery.js" type="text/javascript"></script>
		<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
		<script src="../../js/shared.js?ver=20221114" type="text/javascript"></script>
		<script>
            $(function() {
                const dialogArr = ["schemanative", "schemadwc", "newrecs"];
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

			});

			function validateDownloadForm(f){
				if(f.newrecs && f.newrecs.checked === true && (f.processingstatus.value === "unprocessed" || f.processingstatus.value === "")){
					alert("New records cannot have an unprocessed or undefined processing status. Please select a valid processing status.");
					return false;
				}
				return true;
			}

			function extensionSelected(obj){
				if(obj.checked === true){
					obj.form.zip.checked = true;
				}
			}

			function zipChanged(cbObj){
				if(cbObj.checked === false){
					cbObj.form.identifications.checked = false;
					cbObj.form.images.checked = false;
				}
			}
		</script>
	</head>
	<body>
		<div id="innertext" style="background-color:white;">
			<?php
			if($collid && $isEditor){
				echo '<div style="clear:both;">';
				$filterOptions = array('EQUALS'=>'EQUALS','NOTEQUALS'=>'NOT EQUALS','STARTS'=>'STARTS WITH','LESSTHAN'=>'LESS THAN','GREATERTHAN'=>'GREATER THAN','LIKE'=>'CONTAINS','NOTLIKE'=>'NOT CONTAINS','NULL'=>'IS NULL','NOTNULL'=>'IS NOT NULL');
                ?>
                <form name="downloadform" action="downloadhandler.php" method="post" onsubmit="return validateDownloadForm(this);">
                    <fieldset>
                        <legend><b>Download Occurrence Records</b></legend>
                        <table>
                            <tr>
                                <td>
                                    <div style="margin:10px;">
                                        <b>Processing Status:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <select name="processingstatus">
                                            <option value="">All Records</option>
                                            <?php
                                            $statusArr = $dlManager->getProcessingStatusList($collid);
                                            foreach($statusArr as $v){
                                                echo '<option value="'.$v.'">'.ucwords($v).'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            if($collMeta['manatype'] === 'Snapshot'){
                                ?>
                                <tr>
                                    <td>
                                        <div style="margin:10px;">
                                            <b>New Records Only:</b>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="margin:10px 0;">
                                            <input type="checkbox" name="newrecs" value="1" /> (e.g. records processed within portal)
                                            <a id="newrecsinfo" href="#" onclick="return false" title="More Information">
                                                <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                            </a>
                                            <div id="newrecsinfodialog">
                                                Limit to new records entered and processed directly within the
                                                portal which have not yet imported into and synchonized with
                                                the central database. Avoid importing unprocessed skeletal records since
                                                future imports will involve more complex data coordination.
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td>
                                    <div style="margin:10px;">
                                        <b>Additional<br/>Filters:</b>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    for($i=1;$i<4;$i++){
                                        ?>
                                        <div style="margin:10px 0;">
                                            <select name="customfield<?php echo $i; ?>" style="width:200px">
                                                <option value="">Select Field Name</option>
                                                <option value="">---------------------------------</option>
                                                <?php
                                                foreach($advFieldArr as $k => $v){
                                                    echo '<option value="'.$k.'" '.($k === $customField[1]?'SELECTED':'').'>'.$v.'</option>';
                                                }
                                                ?>
                                            </select>
                                            <select name="customtype<?php echo $i; ?>">
                                                <?php
                                                foreach($filterOptions as $filterValue => $filterDisplay){
                                                    echo '<option '.($customType[1] === '.$filterValue.'?'SELECTED':'').' value="'.$filterValue.'">'.$filterDisplay.'</option>';
                                                }
                                                ?>
                                            </select>
                                            <input name="customvalue<?php echo $i; ?>" type="text" value="<?php echo $customValue[1]; ?>" style="width:200px;" />
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                            if($traitArr = $dlManager->getAttributeTraits($collid)){
                                ?>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div style="margin:10px;">
                                            <b>Occurrence Trait<br/>Filter:</b>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="margin:10px;">
                                            <select name="traitid[]" multiple>
                                                <?php
                                                foreach($traitArr as $traitID => $tArr){
                                                    echo '<option value="'.$traitID.'">'.$tArr['name'].' [ID:'.$traitID.']</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div style="margin:10px;">
                                            -- OR select a specific Attribute State --
                                        </div>
                                        <div style="margin:10px;">
                                            <select name="stateid[]" multiple>
                                                <?php
                                                foreach($traitArr as $traitID => $tArr){
                                                    $stateArr = $tArr['state'];
                                                    foreach($stateArr as $stateID => $stateName){
                                                        echo '<option value="'.$stateID.'">'.$tArr['name'].': '.$stateName.'</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div style="">
                                            * Hold down the control (ctrl) or command button to select multiple options
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td style="vertical-align: top;">
                                    <div style="margin:10px;">
                                        <b>Structure:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <input type="radio" name="schema" value="native" CHECKED />
                                        Native
                                        <a id="schemanativeinfo" href="#" onclick="return false" title="More Information">
                                            <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                        </a><br/>
                                        <div id="schemanativeinfodialog">
                                            Native is very similar to Darwin Core except with the addtion of a few fields
                                            such as substrate, associated collectors, verbatim description.
                                        </div>
                                        <input type="radio" name="schema" value="dwc" />
                                        Darwin Core
                                        <a id="schemainfodwc" href="#" target="" title="More Information">
                                            <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                        </a><br/>
                                        <div id="schemadwcinfodialog">
                                            Darwin Core is a TDWG endorsed exchange standard specifically for biodiversity datasets.
                                            For more information, visit the <a href="">Darwin Core Documentation</a> website.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">
                                    <div style="margin:10px;">
                                        <b>Data Extensions:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <input type="checkbox" name="identifications" value="1" onchange="extensionSelected(this)" checked /> include Determination History<br/>
                                        <input type="checkbox" name="images" value="1" onchange="extensionSelected(this)" checked /> include Image Records<br/>
                                        <input type="checkbox" name="attributes" value="1" onchange="extensionSelected(this)" checked /> include Occurrence Trait Attributes (MeasurementOrFact extension)<br/>
                                        *Output must be a compressed archive
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">
                                    <div style="margin:10px;">
                                        <b>Compression:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <input type="checkbox" name="zip" value="1" onchange="zipChanged(this)" checked /> Archive Data Package (ZIP file)<br/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">
                                    <div style="margin:10px;">
                                        <b>File Format:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <input type="radio" name="format" value="csv" CHECKED /> Comma Delimited (CSV)<br/>
                                        <input type="radio" name="format" value="tab" /> Tab Delimited<br/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">
                                    <div style="margin:10px;">
                                        <b>Character Set:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <?php
                                        $cSet = 'iso-8859-1';
                                        ?>
                                        <input type="radio" name="cset" value="iso-8859-1" <?php echo ($cSet === 'iso-8859-1'?'checked':''); ?> /> ISO-8859-1 (western)<br/>
                                        <input type="radio" name="cset" value="utf-8" <?php echo ($cSet === 'utf-8'?'checked':''); ?> /> UTF-8 (unicode)
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div style="margin:10px;">
                                        <input name="targetcollid" type="hidden" value="<?php echo $collid; ?>" />
                                        <input name="extended" type="hidden" value="1" />
                                        <input name="submitaction" type="submit" value="Download Records" />
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </form>
                <form name="exportgeorefform" action="downloadhandler.php" method="post" onsubmit="return validateExportGeorefForm(this);">
                    <fieldset>
                        <legend><b>Export Batch Georeferenced Data</b></legend>
                        <div style="margin:15px;">
                            This module extracts coordinate data only for the records that have been georeferenced using the
                            <a href="../georef/batchgeoreftool.php?collid=<?php echo $collid; ?>" target="_blank">batch georeferencing tools</a>
                            or the GeoLocate Community tools.
                            These downloads are particularly tailored for importing the new coordinates into their local database.
                            If no records have been georeferenced within the portal, the output file will be empty.
                        </div>
                        <table>
                            <tr>
                                <td>
                                    <div style="margin:10px;">
                                        <b>Processing Status:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <select name="processingstatus">
                                            <option value="">All Records</option>
                                            <?php
                                            $statusArr = $dlManager->getProcessingStatusList($collid);
                                            foreach($statusArr as $v){
                                                echo '<option value="'.$v.'">'.ucwords($v).'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">
                                    <div style="margin:10px;">
                                        <b>Compression:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <input type="checkbox" name="zip" value="1" checked /> Archive Data Package (ZIP file)<br/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">
                                    <div style="margin:10px;">
                                        <b>File Format:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <input type="radio" name="format" value="csv" CHECKED /> Comma Delimited (CSV)<br/>
                                        <input type="radio" name="format" value="tab" /> Tab Delimited<br/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">
                                    <div style="margin:10px;">
                                        <b>Character Set:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <?php
                                        $cSet = 'iso-8859-1';
                                        ?>
                                        <input type="radio" name="cset" value="iso-8859-1" <?php echo ($cSet === 'iso-8859-1'?'checked':''); ?> /> ISO-8859-1 (western)<br/>
                                        <input type="radio" name="cset" value="utf-8" <?php echo ($cSet === 'utf-8'?'checked':''); ?> /> UTF-8 (unicode)
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div style="margin:10px;">
                                        <input name="customfield1" type="hidden" value="georeferenceSources" />
                                        <input name="customtype1" type="hidden" value="STARTS" />
                                        <input name="customvalue1" type="hidden" value="georef batch tool" />
                                        <input name="targetcollid" type="hidden" value="<?php echo $collid; ?>" />
                                        <input name="schema" type="hidden" value="georef" />
                                        <input name="extended" type="hidden" value="1" />
                                        <input name="submitaction" type="submit" value="Download Records" />
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </form>
                <form name="expgeoform" action="downloadhandler.php" method="post" onsubmit="return validateExpGeoForm(this);">
                    <fieldset>
                        <legend><b>Export Occurrences Lacking Georeferencing Data</b></legend>
                        <div style="margin:15px;">
                            This module extracts occurrences that lack decimal coordinates or have coordinates that needs to be verified.
                            This download will result in a Darwin Core Archive containing a UTF-8 encoded CSV file containing
                            only georeferencing relevant data columns for the occurrences. By default, occurrences
                            will be limited to records containing locality information but no decimal coordinates.
                            This output is particularly useful for creating data extracts that will georeferenced using external tools.
                        </div>
                        <table>
                            <tr>
                                <td>
                                    <div style="margin:10px;">
                                        <b>Processing Status:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <select name="processingstatus">
                                            <option value="">All Records</option>
                                            <?php
                                            $statusArr = $dlManager->getProcessingStatusList($collid);
                                            foreach($statusArr as $v){
                                                echo '<option value="'.$v.'">'.ucwords($v).'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="margin:10px;">
                                        <b>Coordinates:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <input name="customtype2" type="radio" value="NULL" checked /> are empty (is null)<br/>
                                        <input name="customtype2" type="radio" value="NOTNULL" /> have values (e.g. need verification)
                                        <input name="customfield2" type="hidden" value="decimallatitude" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="margin:10px;">
                                        <b>Additional<br/>Filters:</b>
                                    </div>
                                </td>
                                <td>
                                    <div style="margin:10px 0;">
                                        <select name="customfield1" style="width:200px">
                                            <option value="">Select Field Name</option>
                                            <option value="">---------------------------------</option>
                                            <?php
                                            foreach($advFieldArr as $k => $v){
                                                echo '<option value="'.$k.'" '.($k === $customField[1]?'SELECTED':'').'>'.$v.'</option>';
                                            }
                                            ?>
                                        </select>
                                        <select name="customtype1">
                                            <?php
                                            foreach($filterOptions as $filterValue => $filterDisplay){
                                                echo '<option '.($customType[1] === '.$filterValue.'?'SELECTED':'').' value="'.$filterValue.'">'.$filterDisplay.'</option>';
                                            }
                                            ?>
                                        </select>
                                        <input name="customvalue1" type="text" value="<?php echo $customValue[1]; ?>" style="width:200px;" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div style="margin:10px;">
                                        <input name="customfield3" type="hidden" value="locality" />
                                        <input name="customtype3" type="hidden" value="NOTNULL" />
                                        <input name="format" type="hidden" value="csv" />
                                        <input name="cset" type="hidden" value="utf-8" />
                                        <input name="zip" type="hidden" value="1" />
                                        <input name="targetcollid" type="hidden" value="<?php echo $collid; ?>" />
                                        <input name="schema" type="hidden" value="dwc" />
                                        <input name="extended" type="hidden" value="1" />
                                        <input name="submitaction" type="submit" value="Download Records" />
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </form>
                <?php
				echo '</div>';
			}
			else{
				echo '<div style="font-weight:bold;">Access denied</div>';
			}
			?>
		</div>
	</body>
</html>

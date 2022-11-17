<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceTaxonomyCleaner.php');
include_once(__DIR__ . '/../../classes/TaxonomyUtilities.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');
ini_set('max_execution_time', 6000);

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$autoClean = array_key_exists('autoclean',$_POST)?(int)$_POST['autoclean']:0;
$targetKingdom = array_key_exists('targetkingdom',$_POST)?(int)$_POST['targetkingdom']:0;
$taxResource = array_key_exists('taxresource',$_POST)?htmlspecialchars($_POST['taxresource']):'';
$startIndex = array_key_exists('startindex',$_POST)?$_POST['startindex']:'';
$limit = array_key_exists('limit',$_POST)?(int)$_POST['limit']:20;

$cleanManager = new OccurrenceTaxonomyCleaner();
$utilitiesManager = new TaxonomyUtilities();
$cleanManager->setCollId($collid);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = true;
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomy Management Module</title>
		<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/external/jquery-ui.css?ver=20220720?ver=3" type="text/css" rel="stylesheet" />
        <style>
            .processor-container {
                width: 95%;
                margin: 20px auto;
                display: flex;
                justify-content: space-between;
                gap: 10px;
            }
            .processor-control-container {
                width: 40%;
                padding:20px 30px;
                font-size: 1.2em;
                border: 2px #aaaaaa solid;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
            }
            .processor-display {
                width: 50%;
                height: 650px;
                padding: 15px;
                overflow-x: hidden;
                overflow-y: auto;
                font-family: Arial, sans-serif;
                background-color: #f5f5f5;
                border: 2px black solid;
                -webkit-border-radius: 10px;
                -moz-border-radius: 10px;
                border-radius: 10px;
            }
            div.processor-display ul {
                padding-left: 15px;
            }
            .success-status {
                display: block;
                color: green;
                font-weight: bold;
            }
            .error-status {
                display: block;
                color: red;
                font-weight: bold;
            }
            #error-status {
                display: block;
                color: red;
                font-weight: bold;
            }
        </style>
        <script src="../../js/external/all.min.js" type="text/javascript"></script>
		<script src="../../js/external/jquery.js" type="text/javascript"></script>
		<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
        <script src="../../js/shared.js?ver=20221116" type="text/javascript"></script>
        <script src="../../js/collections.taxonomytools.js?ver=20221103" type="text/javascript"></script>
		<script>
            const collId = <?php echo $collid; ?>;
            const occTaxonomyApi = "../../api/collections/occTaxonomyController.php";
            const taxaApi = "../../api/taxa/taxaController.php";
            const processStatus = '<span class="current-status"><img src="../../images/workingcircle.gif" style="width:15px;" /></span>';
            const recognizedRanks = JSON.parse('<?php echo $GLOBALS['TAXONOMIC_RANKS']; ?>');

            $( document ).ready(function() {
				setUnlinkedRecordCounts();
            });

			function remappTaxon(oldName,targetTid,idQualifier,msgCode){
				$.ajax({
					type: "POST",
					url: "../../api/taxa/remaptaxon.php",
					dataType: "json",
					data: { collid: "<?php echo $collid; ?>", oldsciname: oldName, tid: targetTid, idq: idQualifier }
				}).done(function( res ) {
					if(Number(res) === 1){
						$("#remapSpan-"+msgCode).text(" >>> Occurrences remapped successfully!");
						$("#remapSpan-"+msgCode).css('color', 'green');
					}
					else{
						$("#remapSpan-"+msgCode).text(" >>> Occurrence remapping failed!");
						$("#remapSpan-"+msgCode).css('color', 'orange');
					}
				});
				return false;
			}

			function batchUpdate(f, oldName, itemCnt){
				if(f.tid.value === ""){
					alert("Taxon not found within taxonomic thesaurus");
					return false;
				}
				else{
					remappTaxon(oldName, f.tid.value, '', itemCnt+"-c");
				}
			}

            function initializeDataSourceSearch(){
                if(targetKingdomId){
                    processCancelled = false;
                    setDataSource();
                    adjustUIStart('resolveFromTaxaDataSource');
                    addProgressLine('<li>Setting rank data for processing search returns ' + processStatus + '</li>');
                    const params = 'action=getRankNameArr';
                    //console.log(occTaxonomyApi+'?'+params);
                    sendAPIPostRequest(taxaApi,params,function(status,res){
                        if(status === 200 && !processCancelled) {
                            processSuccessResponse('Complete');
                            rankArr = JSON.parse(res);
                            setDataSourceSearchTaxaList();
                        }
                        else{
                            processErrorResponse();
                        }
                    },http);
                }
                else{
                    alert('Please select a Target Kingdom from the dropdown menu above.');
                }
            }

            function setDataSourceSearchTaxaList(){
                addProgressLine('<li>Getting unlinked occurrence record scientific names ' + processStatus + '</li>');
                const params = 'collid=' + collId + '&action=getUnlinkedOccSciNames';
                //console.log(occTaxonomyApi+'?'+params);
                sendAPIPostRequest(occTaxonomyApi,params,function(status,res){
                    if(status === 200) {
                        processSuccessResponse('Complete');
                        unlinkedNamesArr = JSON.parse(res);
                        callDataSourceSearchController();
                    }
                    else{
                        processErrorResponse();
                    }
                },http);
            }

            function callDataSourceSearchController(){
                const sciname = unlinkedNamesArr[0];
                unlinkedNamesArr.splice(0, 1);
                if(dataSource === 'col'){
                    loadCOLTaxon(sciname);
                }
            }

            function loadCOLTaxon(sciname){
                const url = 'http://webservice.catalogueoflife.org/col/webservice?response=full&format=json&name=' + sciname;
                sendAPIGetRequest(url,function(status,res){
                    if(status === 200) {
                        console.log(res);
                    }
                    else{
                        processErrorResponse();
                    }
                },http);
            }
        </script>
	</head>
	<body>
		<?php
		include(__DIR__ . '/../../header.php');
		?>
		<div class='navpath'>
			<a href="../../index.php">Home</a> &gt;&gt;
			<?php
			if($collid && is_numeric($collid)){
				?>
				<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Control Panel</a> &gt;&gt;
				<?php
			}
			?>
			<b>Taxonomy Management Module</b>
		</div>
		<div id="innertext">
			<?php
			$collMap = $cleanManager->getCollMap();
			if($collid && $isEditor){
                ?>
                <div style="float:left;font-weight: bold; font-size: 130%; margin-bottom: 10px">
                    <?php
                    echo $collMap[(int)$collid]['collectionname'].' ('.$collMap[(int)$collid]['code'].')';
                    ?>
                </div>
                <div style="margin:20px;clear:both;">
                    <?php
                    /*if($action){
                        if($action === 'AnalyzingNames'){
                            echo '<ul>';
                            $cleanManager->setAutoClean($autoClean);
                            $cleanManager->setTargetKingdom($targetKingdom);
                            $cleanManager->setTargetKingdom($targetKingdom);
                            $startIndex = $cleanManager->analyzeTaxa($taxResource, $startIndex, $limit);
                            echo '</ul>';
                        }
                    }*/
                    ?>
                </div>
                <div style="margin:15px 0;padding:10px;">
                    <div style="margin-left:10px;margin-top:8px;font-weight:bold;font-size:1.3em;">
                        <u>Occurrences not linked to taxonomic thesaurus</u>: <span id="unlinkedOccCnt"></span><br/>
                        <u>Unique scientific names</u>: <span id="unlinkedTaxaCnt"></span><br/>
                        <div style="margin-top:5px;">
                            Target Kingdom:
                            <select id="targetkingdomselect" onchange="setKingdomId();">
                                <option value="">Select Target Kingdom</option>
                                <option value="">--------------------------</option>
                                <?php
                                $kingdomArr = $utilitiesManager->getKingdomArr();
                                foreach($kingdomArr as $kTid => $kSciname){
                                    echo '<option value="'.$kTid.':'.$kSciname.'" '.($targetKingdom === (int)$kTid?'SELECTED':'').'>'.$kSciname.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="processor-container">
                    <div class="processor-control-container">
                        Update locality security settings for occurrence records of protected species.
                        <div style="clear:both;display:flex;justify-content:flex-end;margin-top:5px;">
                            <div>
                                <div class="start-div" id="updateOccLocalitySecurityStart">
                                    <button class="start-button" onclick="updateOccLocalitySecurity();">Start</button>
                                </div>
                                <div class="cancel-div" id="updateOccLocalitySecurityCancel" style="display:none;">
                                    <img src="../../images/workingcircle.gif" style="width:15px;margin-right:10px;" />
                                    <button onclick="cancelProcess();">Cancel</button>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 10px 0;"/>
                        Run cleaning processes on occurrence record scientific names for records that are not linked to
                        the Taxonomic Thesaurus.
                        <div style="clear:both;display:flex;justify-content:flex-end;margin-top:5px;">
                            <div>
                                <div class="start-div" id="cleanProcessesStart">
                                    <button class="start-button" onclick="callCleaningController('leading-trailing-spaces');">Start</button>
                                </div>
                                <div class="cancel-div" id="cleanProcessesCancel" style="display:none;">
                                    <img src="../../images/workingcircle.gif" style="width:15px;margin-right:10px;" />
                                    <button onclick="cancelProcess();">Cancel</button>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 10px 0;"/>
                        Set or update occurrence record linkages to the Taxonomic Thesaurus.
                        <div style="clear:both;margin-top:5px;">
                            <input type='checkbox' id='updatedetimage' /> Also update associated determination, image, and media linkages.
                        </div>
                        <div style="clear:both;display:flex;justify-content:flex-end;margin-top:5px;">
                            <div>
                                <div class="start-div" id="updateWithTaxThesaurusStart">
                                    <button class="start-button" onclick="callTaxThesaurusLinkController();">Start</button>
                                </div>
                                <div class="cancel-div" id="updateWithTaxThesaurusCancel" style="display:none;">
                                    <img src="../../images/workingcircle.gif" style="width:15px;margin-right:10px;" />
                                    <button onclick="cancelProcess();">Cancel</button>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 10px 0;"/>
                        <div style="margin-bottom:10px;">
                            Search for occurrence record scientific names that are not currently linked to the Taxonomic Thesaurus
                            from an external Taxonomic Data Source.
                        </div>
                        <div style="margin-bottom:10px;">
                            <fieldset style="padding:5px;">
                                <legend><b>Taxonomic Data Source</b></legend>
                                <input id="colradio" name="taxresource" type="radio" value="col" checked /> Catalogue of Life (COL)<br/>
                                <input id="itisradio" name="taxresource" type="radio" value="itis" /> Integrated Taxonomic Information System (ITIS)<br/>
                                <input id="wormsradio" name="taxresource" type="radio" value="worms" /> World Register of Marine Species (WoRMS)
                            </fieldset>
                        </div>
                        <div style="clear:both;display:flex;justify-content:flex-end;margin-top:5px;">
                            <div>
                                <div class="start-div" id="resolveFromTaxaDataSourceStart">
                                    <button class="start-button" onclick="initializeDataSourceSearch();">Start</button>
                                </div>
                                <div class="cancel-div" id="resolveFromTaxaDataSourceCancel" style="display:none;">
                                    <img src="../../images/workingcircle.gif" style="width:15px;margin-right:10px;" />
                                    <button onclick="cancelProcess();">Cancel</button>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 10px 0;"/>
                        Get fuzzy matches to occurrence record scientific names that are not yet linked to the Taxonomic Thesaurus
                        with taxa currently in the Taxonomic Thesaurus.
                        <div style="clear:both;margin-top:5px;">
                            Character difference tolerance: <input type='text' id='levvalue' />
                        </div>
                        <div style="clear:both;display:flex;justify-content:flex-end;margin-top:5px;">
                            <div>
                                <div class="start-div" id="resolveFromTaxThesaurusFuzzyStart">
                                    <button class="start-button" onclick="resolveFromTaxThesaurusFuzzy();">Start</button>
                                </div>
                                <div class="cancel-div" id="resolveFromTaxThesaurusFuzzyCancel" style="display:none;">
                                    <img src="../../images/workingcircle.gif" style="width:15px;margin-right:10px;" />
                                    <button onclick="cancelProcess();">Cancel</button>
                                </div>
                            </div>
                        </div>
                        <hr style="margin: 10px 0;"/>
                    </div>

                    <div class="processor-display" id="processing-display">
                        <ul id="progressDisplayList"></ul>
                    </div>
                </div>
                <?php
            }
			?>
		</div>
		<?php include(__DIR__ . '/../../footer.php');?>
	</body>
</html>

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
		<link href="../../css/external/jquery-ui.css?ver=20220720" type="text/css" rel="stylesheet" />
        <style>
            .processor-container {
                width: 95%;
                height: 700px;
                margin: 20px auto;
                display: flex;
                justify-content: space-between;
                gap: 10px;
            }
            .processor-control-container {
                width: 40%;
                height: 650px;
                padding: 15px;
                border: 2px #aaaaaa solid;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
            }
            .processor-accordion-panel {
                width: 100%-2px;
                height: 650px;
            }
            .processor-display-container {
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
            #processor-display {
                height: 610px;
                margin: auto;
                padding: 15px;
                overflow-x: hidden;
                overflow-y: auto;
                border: 1px black solid;
                font-family: Arial, sans-serif;
                background-color: #f5f5f5;
            }
            #processor-display ul {
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
            .current-status {
                margin-left: 10px;
            }
            .current-status {
                margin-left: 10px;
            }
        </style>
        <script src="../../js/external/all.min.js" type="text/javascript"></script>
		<script src="../../js/external/jquery.js" type="text/javascript"></script>
		<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
        <script src="../../js/shared.js?ver=20221126" type="text/javascript"></script>
        <script src="../../js/collections.taxonomytools.js?ver=20221122" type="text/javascript"></script>
		<script>
            const collId = <?php echo $collid; ?>;
            const sessionId = '<?php echo session_id(); ?>';
            const occTaxonomyApi = "../../api/collections/occTaxonomyController.php";
            const taxaApi = "../../api/taxa/taxaController.php";
            const taxaTidLookupApi = "../../api/taxa/gettid.php";
            const proxyUrl = "../../api/proxy.php";
            const processStatus = '<span class="current-status">' + getSmallWorkingSpinnerHtml(11) + '</span>';
            const recognizedRanks = JSON.parse('<?php echo $GLOBALS['TAXONOMIC_RANKS']; ?>');

            $( document ).ready(function() {
				$("#processor-accordion").accordion({
                    icons: null,
                    collapsible: true,
                    heightStyle: "fill"
                });
                setUnlinkedRecordCounts();
            });
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
                                foreach($kingdomArr as $kid => $kSciname){
                                    echo '<option value="'.$kid.'">'.$kSciname.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div style="margin-top:5px;">
                            Processing start index: <input type="text" id="startIndex" style="width:250px;" value="" />
                        </div>
                        <div style="margin-top:5px;">
                            Processing batch limit: <input type="text" id="processingLimit" style="width:50px;" value="" onchange="verifyBatchLimitChange();" />
                        </div>
                    </div>
                </div>
                <div class="processor-container">
                    <div class="processor-control-container">
                        <div id="processor-accordion">
                            <h3 class="tabtitle">Cleaning Utilities</h3>
                            <div class="processor-accordion-panel">
                                Run cleaning processes on occurrence record scientific names that are not linked to
                                the Taxonomic Thesaurus to remove unnecessary endings, identification qualifiers, and normalize
                                infraspecific rank references.
                                <div style="clear:both;display:flex;justify-content:flex-end;margin-top:5px;">
                                    <div>
                                        <div class="start-div" id="cleanProcessesStart">
                                            <button class="start-button" onclick="callCleaningController('leading-trailing-spaces');">Start</button>
                                        </div>
                                        <div class="cancel-div" id="cleanProcessesCancel" style="display:none;">
                                            <span style="margin-right:10px;">
                                                <span class="sm-native-spinner" style="width:12px;height:12px;"></span>
                                            </span>
                                            <button onclick="cancelProcess();">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0;"/>
                                Run cleaning processes to remove the scientific name authors from occurrence record scientific
                                names that are not linked to the Taxonomic Thesaurus.
                                <div style="clear:both;display:flex;justify-content:flex-end;margin-top:5px;">
                                    <div>
                                        <div class="start-div" id="cleanScinameAuthorStart">
                                            <button class="start-button" onclick="initializeCleanScinameAuthor();">Start</button>
                                        </div>
                                        <div class="cancel-div" id="cleanScinameAuthorCancel" style="display:none;">
                                            <span style="margin-right:10px;">
                                                <span class="sm-native-spinner" style="width:12px;height:12px;"></span>
                                            </span>
                                            <button onclick="cancelProcess();">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0;"/>
                                Set occurrence record linkages to the Taxonomic Thesaurus.
                                <div style="clear:both;margin-top:5px;">
                                    <input type='checkbox' id='updatedetimage' /> Also set associated determination, image, and media linkages.
                                </div>
                                <div style="clear:both;display:flex;justify-content:flex-end;margin-top:5px;">
                                    <div>
                                        <div class="start-div" id="updateWithTaxThesaurusStart">
                                            <button class="start-button" onclick="callTaxThesaurusLinkController();">Start</button>
                                        </div>
                                        <div class="cancel-div" id="updateWithTaxThesaurusCancel" style="display:none;">
                                            <span style="margin-right:10px;">
                                                <span class="sm-native-spinner" style="width:12px;height:12px;"></span>
                                            </span>
                                            <button onclick="cancelProcess();">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0;"/>
                                Update locality security settings for occurrence records of protected species.
                                <div style="clear:both;display:flex;justify-content:flex-end;margin-top:5px;">
                                    <div>
                                        <div class="start-div" id="updateOccLocalitySecurityStart">
                                            <button class="start-button" onclick="updateOccLocalitySecurity();">Start</button>
                                        </div>
                                        <div class="cancel-div" id="updateOccLocalitySecurityCancel" style="display:none;">
                                            <span style="margin-right:10px;">
                                                <span class="sm-native-spinner" style="width:12px;height:12px;"></span>
                                            </span>
                                            <button onclick="cancelProcess();">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0;"/>
                            </div>

                            <h3 class="tabtitle">Search Utilities</h3>
                            <div class="processor-accordion-panel">
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
                                            <span style="margin-right:10px;">
                                                <span class="sm-native-spinner" style="width:12px;height:12px;"></span>
                                            </span>
                                            <button onclick="cancelProcess(false);">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0;"/>
                                Get fuzzy matches to occurrence record scientific names that are not yet linked to the Taxonomic Thesaurus
                                with taxa currently in the Taxonomic Thesaurus.
                                <div style="clear:both;margin-top:5px;">
                                    Character difference tolerance: <input type="text" id="levvalue" style="width:30px;" value="2" />
                                </div>
                                <div style="clear:both;display:flex;justify-content:flex-end;margin-top:5px;">
                                    <div>
                                        <div class="start-div" id="taxThesaurusFuzzyMatchStart">
                                            <button class="start-button" onclick="initializeTaxThesaurusFuzzyMatch();">Start</button>
                                        </div>
                                        <div class="cancel-div" id="taxThesaurusFuzzyMatchCancel" style="display:none;">
                                            <span style="margin-right:10px;">
                                                <span class="sm-native-spinner" style="width:12px;height:12px;"></span>
                                            </span>
                                            <button onclick="cancelProcess();">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0;"/>
                            </div>
                        </div>
                    </div>

                    <div class="processor-display-container">
                        <div id="processor-display">
                            <ul id="progressDisplayList"></ul>
                        </div>
                    </div>
                </div>
                <?php
            }
			?>
		</div>
		<?php include(__DIR__ . '/../../footer.php');?>
	</body>
</html>

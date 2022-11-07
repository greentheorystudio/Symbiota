<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonomyCleaner.php');
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
$action = array_key_exists('submitaction',$_POST)?htmlspecialchars($_POST['submitaction']):'';

$cleanManager = new TaxonomyCleaner();
$cleanManager->setCollId($collid);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = true;
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> $Taxonomy Resolution Module</title>
		<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/external/jquery-ui.css?ver=20220720?ver=3" type="text/css" rel="stylesheet" />
        <style>
            .processor-container {
                width: 95%;
                height: 700px;
                margin: 20px auto;
                display: flex;
                justify-content: space-between;
            }
            .processor-control-container {
                width: 40%;
                height: 650px;
                padding:5px;
                border: 2px #aaaaaa solid;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
            }
            .processor-accordion-panel {
                width: 100%-2px;
                height: 650px;
                padding: 15px;
            }
            .processor-display {
                width: 50%;
                height: 650px;
                overflow-x: hidden;
                overflow-y: auto;
                font-family: Andale Mono, monospace;
                background-color: #f5f5f5;
                border: 2px black solid;
                -webkit-border-radius: 10px;
                -moz-border-radius: 10px;
                border-radius: 10px;
            }
        </style>
        <script src="../../js/external/all.min.js" type="text/javascript"></script>
		<script src="../../js/external/jquery.js" type="text/javascript"></script>
		<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
        <script src="../../js/shared.js?ver=20220809" type="text/javascript"></script>
		<script>
            $( document ).ready(function() {
				$("#processor-accordion").accordion({
                    icons: null,
                    collapsible: true,
                    heightStyle: "fill"
                });
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
			<b>$Taxonomy Resolution Module</b>
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
                    if($action){
                        if($action === 'deepindex'){
                            $cleanManager->deepIndexTaxa();
                        }
                        elseif($action === 'AnalyzingNames'){
                            echo '<ul>';
                            $cleanManager->setAutoClean($autoClean);
                            $cleanManager->setTargetKingdom($targetKingdom);
                            $cleanManager->setTargetKingdom($targetKingdom);
                            $startIndex = $cleanManager->analyzeTaxa($taxResource, $startIndex, $limit);
                            echo '</ul>';
                        }
                    }
                    $badTaxaCount = $cleanManager->getBadTaxaCount();
                    $badSpecimenCount = $cleanManager->getBadSpecimenCount();
                    ?>
                </div>
                <div style="margin:15px 0;padding:10px;">
                    <div style="margin-left:10px;margin-top:8px;font-weight:bold;font-size:1.3em;">
                        <u>Occurrences not linked to taxonomic thesaurus</u>: <?php echo $badSpecimenCount; ?><br/>
                        <u>Unique scientific names</u>: <?php echo $badTaxaCount; ?><br/>
                        <div style="margin-top:5px;">
                            Target Kingdom:
                            <select id="targetkingdom">
                                <option value="">Select Target Kingdom</option>
                                <option value="">--------------------------</option>
                                <?php
                                $kingdomArr = $cleanManager->getKingdomArr();
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
                        <div id="processor-accordion">
                            <h3 class="tabtitle">Resolve Names From Taxonomic Thesaurus</h3>
                            <div class="processor-accordion-panel">
                                Resolve occurrence record scientific names that are not yet linked with the Taxonomic Thesaurus
                                to taxa currently in the Taxonomic Thesaurus.
                                <div style="clear:both;display:flex;justify-content:flex-end;">
                                    <div>
                                        <button id="resolveFromTaxThesaurusStart" onclick="resolveFromTaxThesaurus();">Start</button>
                                        <button id="resolveFromTaxThesaurusCancel" onclick="cancelResolveFromTaxThesaurus();" style="display:none;">Cancel</button>
                                    </div>
                                </div>
                                <hr/>
                                Update occurrence records already linked to the Taxonomic Thesaurus.
                                <div style="clear:both;display:flex;justify-content:flex-end;">
                                    <div>
                                        <button id="updateWithTaxThesaurusStart" onclick="updateWithTaxThesaurus();">Start</button>
                                        <button id="updateWithTaxThesaurusCancel" onclick="cancelUpdateWithTaxThesaurus();" style="display:none;">Cancel</button>
                                    </div>
                                </div>
                                <hr/>
                                Resolve occurrence record scientific names that are not yet linked with the Taxonomic Thesaurus
                                to taxa currently in the Taxonomic Thesaurus using extra cleaning on scientific names.
                                <div style="clear:both;display:flex;justify-content:flex-end;">
                                    <div>
                                        <button id="cleanResolveFromTaxThesaurusStart" onclick="cleanResolveFromTaxThesaurus();">Start</button>
                                        <button id="cleanResolveFromTaxThesaurusCancel" onclick="cancelCleanResolveFromTaxThesaurus();" style="display:none;">Cancel</button>
                                    </div>
                                </div>
                                <hr/>
                                Get fuzzy matches of occurrence record scientific names that are not yet linked with the Taxonomic Thesaurus
                                to taxa currently in the Taxonomic Thesaurus.
                                <div style="clear:both;display:flex;justify-content:flex-end;">
                                    <div>
                                        <button id="resolveFromTaxThesaurusFuzzyStart" onclick="resolveFromTaxThesaurusFuzzy();">Start</button>
                                        <button id="resolveFromTaxThesaurusFuzzyCancel" onclick="cancelResolveFromTaxThesaurusFuzzy();" style="display:none;">Cancel</button>
                                    </div>
                                </div>
                                <hr/>
                            </div>

                            <h3 class="tabtitle">Resolve Names From Taxonomic Data Sources</h3>
                            <div class="processor-accordion-panel">
                                <div style="margin-bottom:10px;">
                                    <fieldset style="padding:15px;">
                                        <legend><b>Taxonomic Data Source</b></legend>
                                        <input name="taxresource" type="radio" value="col" checked /> Catalogue of Life (COL)<br/>
                                        <input name="taxresource" type="radio" value="itis" /> Integrated Taxonomic Information System (ITIS)<br/>
                                        <input name="taxresource" type="radio" value="worms" /> World Register of Marine Species (WoRMS)
                                    </fieldset>
                                </div>
                                <div style="clear:both;display:flex;justify-content:flex-end;">
                                    <div>
                                        <button id="resolveFromTaxDataSourceStart" onclick="resolveFromTaxDataSource();">Start</button>
                                        <button id="resolveFromTaxDataSourceCancel" onclick="cancelResolveFromTaxDataSource();" style="display:none;">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
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

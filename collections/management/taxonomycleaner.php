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
		<link href="../../css/external/jquery-ui.css?ver=20220720" type="text/css" rel="stylesheet" />
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
            .processor-display-container {
                width: 50%;
                height: 650px;
                padding: 15px;
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
        </style>
        <script src="../../js/external/all.min.js" type="text/javascript"></script>
		<script src="../../js/external/jquery.js" type="text/javascript"></script>
		<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
        <script src="../../js/shared.js?ver=20221117" type="text/javascript"></script>
        <script src="../../js/collections.taxonomytools.js?ver=20221108" type="text/javascript"></script>
		<script>
            const collId = <?php echo $collid; ?>;
            const sessionId = '<?php echo session_id(); ?>';
            const occTaxonomyApi = "../../api/collections/occTaxonomyController.php";
            const taxaApi = "../../api/taxa/taxaController.php";
            const taxaTidLookupApi = "../../api/taxa/gettid.php";
            const proxyUrl = "../../api/proxy.php";
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
                    nameTidIndex = {};
                    setDataSource();
                    adjustUIStart('resolveFromTaxaDataSource');
                    addProgressLine('<li>Setting rank data for processing search returns ' + processStatus + '</li>');
                    const params = 'action=getRankNameArr';
                    //console.log(occTaxonomyApi+'?'+params);
                    sendAPIPostRequest(taxaApi,params,function(status,res){
                        if(status === 200) {
                            processSuccessResponse(15,'Complete');
                            rankArr = JSON.parse(res);
                            setDataSourceSearchTaxaList();
                        }
                        else{
                            processErrorResponse(15,true);
                        }
                    },http);
                }
                else{
                    alert('Please select a Target Kingdom from the dropdown menu above.');
                }
            }

            function setDataSourceSearchTaxaList(){
                if(!processCancelled){
                    addProgressLine('<li>Getting unlinked occurrence record scientific names ' + processStatus + '</li>');
                    const params = 'collid=' + collId + '&action=getUnlinkedOccSciNames';
                    //console.log(occTaxonomyApi+'?'+params);
                    sendAPIPostRequest(occTaxonomyApi,params,function(status,res){
                        if(status === 200) {
                            processSuccessResponse(15,'Complete');
                            unlinkedNamesArr = JSON.parse(res);
                            runScinameDataSourceSearch();
                        }
                        else{
                            processErrorResponse(15,true);
                        }
                    },http);
                }
            }

            function runScinameDataSourceSearch(){
                if(!processCancelled){
                    if(unlinkedNamesArr.length > 0){
                        nameSearchResults = new Array();
                        currentSciname = unlinkedNamesArr[0];
                        unlinkedNamesArr.splice(0, 1);
                        if(dataSource === 'col'){
                            colInitialSearchResults = new Array();
                            addProgressLine('<li>Searching the Catalogue of Life (COL) for ' + currentSciname + ' ' + processStatus + '</li>');
                            const url = 'http://webservice.catalogueoflife.org/col/webservice?response=full&format=json&name=' + currentSciname;
                            sendProxyGetRequest(proxyUrl,url,sessionId,function(status,res){
                                if(status === 200){
                                    processGetCOLTaxonByScinameResponse(res);
                                }
                                else{
                                    processErrorResponse(15,false);
                                }
                            },http);
                        }
                        else if(dataSource === 'itis'){
                            addProgressLine('<li>Searching the Integrated Taxonomic Information System (ITIS) for ' + currentSciname + ' ' + processStatus + '</li>');
                            const url = 'https://www.itis.gov/ITISWebService/jsonservice/ITISService/searchByScientificName?srchKey=' + currentSciname;
                            sendProxyGetRequest(proxyUrl,url,sessionId,function(status,res){
                                if(status === 200){
                                    processGetITISTaxonByScinameResponse(res);
                                }
                                else{
                                    processErrorResponse(15,false);
                                }
                            },http);
                        }
                        else if(dataSource === 'worms'){
                            addProgressLine('<li>Searching the World Register of Marine Species (WoRMS) for ' + currentSciname + ' ' + processStatus + '</li>');
                            const url = 'https://www.marinespecies.org/rest/AphiaIDByName/' + currentSciname + '?marine_only=false';
                            sendProxyGetRequest(proxyUrl,url,sessionId,function(status,res){
                                if(status === 200 && res){
                                    getWoRMSNameSearchResultsRecord(res);
                                }
                                else if(status === 204 || !res){
                                    processErrorResponse(15,false,'Not found');
                                    runScinameDataSourceSearch();
                                }
                                else{
                                    processErrorResponse(15,false);
                                }
                            },http);
                        }
                    }
                    else{
                        adjustUIEnd();
                    }
                }
            }

            function processGetCOLTaxonByScinameResponse(res){
                const resObj = JSON.parse(res);
                if(resObj['total_number_of_results'] > 0){
                    const resultArr = resObj['result'];
                    for(let i in resultArr){
                        if(resultArr.hasOwnProperty(i)){
                            const taxResult = resultArr[i];
                            const status = taxResult['name_status'];
                            if(status !== 'common name'){
                                const resultObj = {};
                                resultObj['id'] = taxResult['id'];
                                resultObj['sciname'] = taxResult['name'];
                                resultObj['author'] = taxResult.hasOwnProperty('author') ? taxResult['author'] : '';
                                resultObj['rankname'] = taxResult['rank'].toLowerCase();
                                resultObj['rankid'] = rankArr.hasOwnProperty(resultObj['rankname']) ? rankArr[resultObj['rankname']] : null;
                                if(status === 'accepted name'){
                                    resultObj['accepted'] = true;
                                }
                                else if(status === 'synonym'){
                                    const acceptedObj = taxResult['accepted_name'];
                                    resultObj['accepted'] = false;
                                    resultObj['accepted_id'] = acceptedObj['id'];
                                    resultObj['accepted_sciname'] = acceptedObj['name'];
                                }
                                colInitialSearchResults.push(resultObj);
                            }
                        }
                    }
                    if(colInitialSearchResults.length > 0){
                        validateCOLInitialNameSearchResults();
                    }
                    else{
                        processErrorResponse(15,false,'Not found');
                        runScinameDataSourceSearch();
                    }
                }
                else{
                    processErrorResponse(15,false,'Not found');
                    runScinameDataSourceSearch();
                }
            }

            function validateCOLInitialNameSearchResults(){
                if(!processCancelled){
                    if(colInitialSearchResults.length > 0){
                        let id;
                        const taxon = colInitialSearchResults[0];
                        colInitialSearchResults.splice(0, 1);
                        if(taxon['accepted']){
                            id = taxon['id'];
                        }
                        else{
                            id = taxon['accepted_id'];
                        }
                        const url = 'https://api.catalogueoflife.org/dataset/9840/taxon/' + id + '/classification';
                        sendProxyGetRequest(proxyUrl,url,sessionId,function(status,res){
                            if(status === 200){
                                const resArr = JSON.parse(res);
                                const kingdomObj = resArr.find(rettaxon => rettaxon['rank'].toLowerCase() === 'kingdom');
                                if(kingdomObj && kingdomObj['name'].toLowerCase() === targetKingdomName.toLowerCase()){
                                    const hierarchyArr = [];
                                    for(let i in resArr){
                                        if(resArr.hasOwnProperty(i)){
                                            const taxResult = resArr[i];
                                            if(taxResult['name'] !== taxon['sciname']){
                                                const rankname = taxResult['rank'].toLowerCase();
                                                const rankid = Number(rankArr[rankname]);
                                                if(recognizedRanks.includes(rankid)){
                                                    const resultObj = {};
                                                    resultObj['id'] = taxResult['id'];
                                                    resultObj['sciname'] = taxResult['name'];
                                                    resultObj['author'] = taxResult.hasOwnProperty('authorship') ? taxResult['authorship'] : '';
                                                    resultObj['rankname'] = rankname;
                                                    resultObj['rankid'] = rankid;
                                                    if(rankname === 'family'){
                                                        taxon['family'] = resultObj['sciname'];
                                                    }
                                                    hierarchyArr.push(resultObj);
                                                }
                                            }
                                        }
                                    }
                                    taxon['hierarchy'] = hierarchyArr;
                                    nameSearchResults.push(taxon);
                                }
                                validateCOLInitialNameSearchResults();
                            }
                            else{
                                validateCOLInitialNameSearchResults();
                            }
                        },http);
                    }
                    else if(nameSearchResults.length === 1){
                        processSuccessResponse(0);
                        validateNameSearchResults();
                    }
                    else if(nameSearchResults.length === 0){
                        processErrorResponse(15,false,'Not found');
                        runScinameDataSourceSearch();
                    }
                    else if(nameSearchResults.length > 1){
                        processErrorResponse(15,false,'Unable to distinguish taxon by name');
                        runScinameDataSourceSearch();
                    }
                }
            }

            function processGetITISTaxonByScinameResponse(res){
                const resObj = JSON.parse(res);
                const resultArr = resObj['scientificNames'];
                if(resultArr.length > 0 && resultArr[0]){
                    for(let i in resultArr){
                        if(resultArr.hasOwnProperty(i)){
                            const taxResult = resultArr[i];
                            if(taxResult['combinedName'] === currentSciname && taxResult['kingdom'].toLowerCase() === targetKingdomName.toLowerCase()){
                                const resultObj = {};
                                resultObj['id'] = taxResult['tsn'];
                                resultObj['sciname'] = taxResult['combinedName'];
                                resultObj['author'] = taxResult['author'];
                                nameSearchResults.push(resultObj);
                            }
                        }
                    }
                    if(nameSearchResults.length === 1){
                        getITISNameSearchResultsRecord();
                    }
                    else if(nameSearchResults.length === 0){
                        processErrorResponse(15,false,'Not found');
                        runScinameDataSourceSearch();
                    }
                    else if(nameSearchResults.length > 1){
                        processErrorResponse(15,false,'Unable to distinguish taxon by name');
                        runScinameDataSourceSearch();
                    }
                }
                else{
                    processErrorResponse(15,false,'Not found');
                    runScinameDataSourceSearch();
                }
            }

            function getITISNameSearchResultsRecord(){
                if(!processCancelled){
                    const id = nameSearchResults[0]['id'];
                    const url = 'https://www.itis.gov/ITISWebService/jsonservice/getFullRecordFromTSN?tsn=' + id;
                    sendProxyGetRequest(proxyUrl,url,sessionId,function(status,res){
                        if(status === 200){
                            const resObj = JSON.parse(res);
                            const taxonRankData = resObj['taxRank'];
                            nameSearchResults[0]['rankname'] = taxonRankData['rankName'].toLowerCase().trim();
                            nameSearchResults[0]['rankid'] = Number(taxonRankData['rankId']);
                            const coreMetadata = resObj['coreMetadata'];
                            const namestatus = coreMetadata['taxonUsageRating'];
                            if(namestatus === 'accepted'){
                                nameSearchResults[0]['accepted'] = true;
                                getITISNameSearchResultsHierarchy();
                            }
                            else if(namestatus === 'not accepted'){
                                nameSearchResults[0]['accepted'] = false;
                                const acceptedNameList = resObj['acceptedNameList'];
                                const acceptedNameArr = acceptedNameList['acceptedNames'];
                                if(acceptedNameArr.length > 0){
                                    const acceptedName = acceptedNameArr[0];
                                    nameSearchResults[0]['accepted_id'] = acceptedName['acceptedTsn'];
                                    nameSearchResults[0]['accepted_sciname'] = acceptedName['acceptedName'];
                                    getITISNameSearchResultsHierarchy();
                                }
                                else{
                                    processErrorResponse(15,false,'Unable to distinguish taxon by name');
                                    runScinameDataSourceSearch();
                                }
                            }
                        }
                        else{
                            processErrorResponse(15,false,'Unable to retrieve taxon record');
                            runScinameDataSourceSearch();
                        }
                    },http);
                }
            }

            function getITISNameSearchResultsHierarchy(){
                if(!processCancelled){
                    let id;
                    if(nameSearchResults[0]['accepted']){
                        id = nameSearchResults[0]['id'];
                    }
                    else{
                        id = nameSearchResults[0]['accepted_id'];
                    }
                    const url = 'https://www.itis.gov/ITISWebService/jsonservice/ITISService/getFullHierarchyFromTSN?tsn=' + id;
                    sendProxyGetRequest(proxyUrl,url,sessionId,function(status,res){
                        if(status === 200){
                            const resObj = JSON.parse(res);
                            const resArr = resObj['hierarchyList'];
                            const hierarchyArr = [];
                            const foundNameRank = nameSearchResults[0]['rankid'];
                            for(let i in resArr){
                                if(resArr.hasOwnProperty(i)){
                                    const taxResult = resArr[i];
                                    if(taxResult['taxonName'] !== nameSearchResults[0]['sciname']){
                                        const rankname = taxResult['rankName'].toLowerCase();
                                        const rankid = Number(rankArr[rankname]);
                                        if(rankid < foundNameRank && recognizedRanks.includes(rankid)){
                                            const resultObj = {};
                                            resultObj['id'] = taxResult['tsn'];
                                            resultObj['sciname'] = taxResult['taxonName'];
                                            resultObj['author'] = taxResult['author'] ? taxResult['author'] : '';
                                            resultObj['rankname'] = rankname;
                                            resultObj['rankid'] = rankid;
                                            if(rankname === 'family'){
                                                nameSearchResults[0]['family'] = resultObj['sciname'];
                                            }
                                            hierarchyArr.push(resultObj);
                                        }
                                    }
                                }
                            }
                            nameSearchResults[0]['hierarchy'] = hierarchyArr;
                            processSuccessResponse(0);
                            validateNameSearchResults();
                        }
                        else{
                            processErrorResponse(15,false,'Unable to retrieve taxon hierarchy');
                            runScinameDataSourceSearch();
                        }
                    },http);
                }
            }

            function getWoRMSNameSearchResultsRecord(id){
                if(!processCancelled){
                    const url = 'https://www.marinespecies.org/rest/AphiaRecordByAphiaID/' + id;
                    sendProxyGetRequest(proxyUrl,url,sessionId,function(status,res){
                        if(status === 200){
                            const resObj = JSON.parse(res);
                            if(resObj['kingdom'].toLowerCase() === targetKingdomName.toLowerCase()){
                                const resultObj = {};
                                resultObj['id'] = resObj['AphiaID'];
                                resultObj['sciname'] = resObj['scientificname'];
                                resultObj['author'] = resObj['authority'] ? resObj['authority'] : '';
                                resultObj['rankname'] = resObj['rank'].toLowerCase();
                                resultObj['rankid'] = Number(resObj['taxonRankID']);
                                const namestatus = resObj['status'];
                                if(namestatus === 'accepted'){
                                    resultObj['accepted'] = true;
                                }
                                else if(namestatus === 'unaccepted'){
                                    resultObj['accepted'] = false;
                                    resultObj['accepted_id'] = resObj['valid_AphiaID'];
                                    resultObj['accepted_sciname'] = resObj['valid_name'];
                                }
                                nameSearchResults.push(resultObj);
                                getWoRMSNameSearchResultsHierarchy();
                            }
                            else{
                                processErrorResponse(15,false,'Not found');
                                runScinameDataSourceSearch();
                            }
                        }
                        else{
                            processErrorResponse(15,false,'Unable to retrieve taxon record');
                            runScinameDataSourceSearch();
                        }
                    },http);
                }
            }

            function getWoRMSNameSearchResultsHierarchy(){
                if(!processCancelled){
                    let id;
                    if(nameSearchResults[0]['accepted']){
                        id = nameSearchResults[0]['id'];
                    }
                    else{
                        id = nameSearchResults[0]['accepted_id'];
                    }
                    const url = 'https://www.marinespecies.org/rest/AphiaClassificationByAphiaID/' + id;
                    sendProxyGetRequest(proxyUrl,url,sessionId,function(status,res){
                        if(status === 200){
                            const resObj = JSON.parse(res);
                            const hierarchyArr = [];
                            const foundNameRank = nameSearchResults[0]['rankid']
                            let childObj = resObj['child'];
                            const firstObj = {};
                            const firstrankname = childObj['rank'].toLowerCase();
                            const firstrankid = Number(rankArr[firstrankname]);
                            firstObj['id'] = childObj['AphiaID'];
                            firstObj['sciname'] = childObj['scientificname'];
                            firstObj['author'] = '';
                            firstObj['rankname'] = firstrankname;
                            firstObj['rankid'] = firstrankid;
                            hierarchyArr.push(firstObj);
                            while(childObj = childObj['child']){
                                if(Number(rankArr[childObj['rank'].toLowerCase()]) <= foundNameRank && childObj['scientificname'] !== nameSearchResults[0]['sciname']){
                                    const rankname = childObj['rank'].toLowerCase();
                                    const rankid = Number(rankArr[rankname]);
                                    if(recognizedRanks.includes(rankid)){
                                        const resultObj = {};
                                        resultObj['id'] = childObj['AphiaID'];
                                        resultObj['sciname'] = childObj['scientificname'];
                                        resultObj['author'] = '';
                                        resultObj['rankname'] = rankname;
                                        resultObj['rankid'] = rankid;
                                        if(rankname === 'family'){
                                            nameSearchResults[0]['family'] = resultObj['sciname'];
                                        }
                                        hierarchyArr.push(resultObj);
                                    }
                                }
                            }
                            nameSearchResults[0]['hierarchy'] = hierarchyArr;
                            processSuccessResponse(0);
                            validateNameSearchResults();
                        }
                        else{
                            processErrorResponse(15,false,'Unable to retrieve taxon hierarchy');
                            runScinameDataSourceSearch();
                        }
                    },http);
                }
            }

            function validateNameSearchResults(){
                if(!processCancelled){
                    processingArr = new Array();
                    taxaToAddArr = new Array();
                    if(nameSearchResults.length === 1){
                        if(!nameSearchResults[0]['accepted'] && !nameSearchResults[0]['accepted_sciname']){
                            processErrorResponse(15,false,'Unable to distinguish accepted name');
                            runScinameDataSourceSearch();
                        }
                        else{
                            const addHierchyTemp = nameSearchResults[0]['hierarchy'];
                            addHierchyTemp.sort((a, b) => {
                                return a.rankid - b.rankid;
                            });
                            let parentName = addHierchyTemp[0]['sciname'];
                            for(let i in addHierchyTemp){
                                if(addHierchyTemp.hasOwnProperty(i) && addHierchyTemp[i]['sciname'] !== parentName){
                                    addHierchyTemp[i]['parentName'] = parentName;
                                    addHierchyTemp[i]['family'] = addHierchyTemp[i]['rankid'] >= 140 ? nameSearchResults[0]['family'] : null;
                                    parentName = addHierchyTemp[i]['sciname'];
                                    if(!nameSearchResults[0]['accepted'] && addHierchyTemp[i]['sciname'] === nameSearchResults[0]['accepted_sciname']){
                                        nameSearchResults[0]['parentName'] = addHierchyTemp[i]['parentName'];
                                    }
                                }
                            }
                            if(!nameSearchResults[0].hasOwnProperty('parentName') || nameSearchResults[0]['parentName'] === ''){
                                nameSearchResults[0]['parentName'] = parentName;
                            }
                            processingArr = addHierchyTemp;
                            addProgressLine('<li style="margin-left:15px;">Matching parent and accepted taxa to the Taxonomic Thesaurus ' + processStatus + '</li>');
                            setTaxaToAdd();
                        }
                    }
                    else{
                        processErrorResponse(15,false,'Unable to distinguish taxon by name');
                        runScinameDataSourceSearch();
                    }
                }
            }

            function setTaxaToAdd(){
                if(!processCancelled){
                    if(processingArr.length > 0){
                        const sciname = processingArr[0]['sciname'];
                        if(!nameTidIndex.hasOwnProperty(sciname)){
                            const params = 'sciname=' + sciname + '&kingdomid=' + targetKingdomId;
                            //console.log(taxaTidLookupApi+'?'+params);
                            sendAPIPostRequest(taxaTidLookupApi,params,function(status,res){
                                if(dataSource === 'worms' && !res){
                                    getWoRMSAddTaxonAuthor();
                                }
                                else{
                                    const currentTaxon = processingArr[0];
                                    if(res){
                                        nameTidIndex[currentTaxon['sciname']] = Number(res);
                                    }
                                    else{
                                        taxaToAddArr.push(currentTaxon);
                                    }
                                    processingArr.splice(0, 1);
                                    setTaxaToAdd();
                                }
                            },http);
                        }
                        else{
                            processingArr.splice(0, 1);
                            setTaxaToAdd();
                        }
                    }
                    else{
                        processSuccessResponse(0);
                        processAddTaxaArr();
                    }
                }
            }

            function getWoRMSAddTaxonAuthor(){
                if(!processCancelled){
                    const id = processingArr[0]['id'];
                    const url = 'https://www.marinespecies.org/rest/AphiaRecordByAphiaID/' + id;
                    sendProxyGetRequest(proxyUrl,url,sessionId,function(status,res){
                        const currentTaxon = processingArr[0];
                        if(status === 200){
                            const resObj = JSON.parse(res);
                            currentTaxon['author'] = resObj['authority'] ? resObj['authority'] : '';
                        }
                        taxaToAddArr.push(currentTaxon);
                        processingArr.splice(0, 1);
                        setTaxaToAdd();
                    },http);
                }
            }

            function processAddTaxaArr(){
                if(!processCancelled){
                    if(taxaToAddArr.length > 0){
                        const taxonToAdd = taxaToAddArr[0];
                        addProgressLine('<li style="margin-left:15px;">Adding ' + taxonToAdd['sciname'] + ' to the Taxonomic Thesaurus ' + processStatus + '</li>');
                        const newTaxonObj = {};
                        newTaxonObj['sciname'] = taxonToAdd['sciname'];
                        newTaxonObj['author'] = taxonToAdd['author'];
                        newTaxonObj['kingdomid'] = targetKingdomId;
                        newTaxonObj['rankid'] = taxonToAdd['rankid'];
                        newTaxonObj['acceptstatus'] = 1;
                        newTaxonObj['tidaccepted'] = '';
                        newTaxonObj['parenttid'] = nameTidIndex[taxonToAdd['parentName']];
                        newTaxonObj['family'] = taxonToAdd['family'];
                        newTaxonObj['source'] = getDataSourceName();
                        newTaxonObj['source-name'] = dataSource;
                        newTaxonObj['source-id'] = taxonToAdd['id'];
                        const formData = new FormData();
                        formData.append('taxon', JSON.stringify(newTaxonObj));
                        formData.append('action', 'addTaxon');
                        http.open("POST", taxaApi, true);
                        http.onreadystatechange = function() {
                            if(http.readyState === 4) {
                                if(http.status === 200 && Number(http.responseText) > 0){
                                    nameTidIndex[taxaToAddArr[0]['sciname']] = Number(http.responseText);
                                    taxaToAddArr.splice(0, 1);
                                    processSuccessResponse(0);
                                    processAddTaxaArr();
                                }
                                else{
                                    processErrorResponse(15,false,'Error loading taxon');
                                    runScinameDataSourceSearch();
                                }
                            }
                        };
                        http.send(formData);
                    }
                    else{
                        processAddTaxon();
                    }
                }
            }

            function processAddTaxon(){
                if(!processCancelled){
                    const taxonToAdd = nameSearchResults[0];
                    addProgressLine('<li style="margin-left:15px;">Adding ' + taxonToAdd['sciname'] + ' to the Taxonomic Thesaurus ' + processStatus + '</li>');
                    const newTaxonObj = {};
                    newTaxonObj['sciname'] = taxonToAdd['sciname'];
                    newTaxonObj['author'] = taxonToAdd['author'];
                    newTaxonObj['kingdomid'] = targetKingdomId;
                    newTaxonObj['rankid'] = taxonToAdd['rankid'];
                    newTaxonObj['acceptstatus'] = taxonToAdd['accepted'] ? 1 : 0;
                    newTaxonObj['tidaccepted'] = !taxonToAdd['accepted'] ? nameTidIndex[taxonToAdd['accepted_sciname']] : '';
                    newTaxonObj['parenttid'] = nameTidIndex[taxonToAdd['parentName']];
                    newTaxonObj['family'] = taxonToAdd['family'];
                    newTaxonObj['source'] = getDataSourceName();
                    newTaxonObj['source-name'] = dataSource;
                    newTaxonObj['source-id'] = taxonToAdd['id'];
                    const formData = new FormData();
                    formData.append('taxon', JSON.stringify(newTaxonObj));
                    formData.append('action', 'addTaxon');
                    http.open("POST", taxaApi, true);
                    http.onreadystatechange = function() {
                        if(http.readyState === 4) {
                            if(http.status === 200 && Number(http.responseText) > 0){
                                nameTidIndex[nameSearchResults[0]['sciname']] = Number(http.responseText);
                                processSuccessResponse(15,'Successfully added ' + nameSearchResults[0]['sciname']);
                                updateOccurrenceLinkages();
                            }
                            else{
                                processErrorResponse(15,false,'Error loading taxon');
                                runScinameDataSourceSearch();
                            }
                        }
                    };
                    http.send(formData);
                }
            }

            function updateOccurrenceLinkages(){
                if(!processCancelled){
                    const newSciname = nameSearchResults[0]['sciname'];
                    const newScinameTid = nameTidIndex[nameSearchResults[0]['sciname']];
                    addProgressLine('<li style="margin-left:15px;">Updating linkages of occurrence records to ' + newSciname + ' ' + processStatus + '</li>');
                    let params = 'collid=' + collId + '&sciname=' + newSciname + '&tid=' + newScinameTid + '&kingdomid=' + targetKingdomId + '&action=updateOccWithNewSciname';
                    //console.log(occTaxonomyApi+'?'+params);
                    sendAPIPostRequest(occTaxonomyApi,params,function(status,res){
                        if(status === 200) {
                            processSuccessResponse(15,'Complete: ' + res + ' records updated');
                        }
                        else{
                            processErrorResponse(15,true);
                        }
                        runScinameDataSourceSearch();
                    },http);
                }
            }

            function getDataSourceName(){
                if(dataSource === 'col'){
                    return 'Catalogue of Life';
                }
                else if(dataSource === 'itis'){
                    return 'Integrated Taxonomic Information System';
                }
                else if(dataSource === 'worms'){
                    return 'World Register of Marine Species';
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
                                foreach($kingdomArr as $kTid => $kSciname){
                                    echo '<option value="'.$kTid.'" '.($targetKingdom === (int)$kTid?'SELECTED':'').'>'.$kSciname.'</option>';
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

const http = new XMLHttpRequest();
let processCancelled = false;
let unlinkedNamesArr = [];
let dataSource = '';
let currentSciname = '';
let targetKingdomId = null;
let targetKingdomName = null;
let rankArr = null;
let colInitialSearchResults = [];
let itisInitialSearchResults = [];
let nameSearchResults = [];
let nameTidIndex = {};
let processingArr = [];
let taxaToAddArr = [];
let taxaLoaded = 0;

function addProgressLine(lineHtml){
    document.getElementById("progressDisplayList").innerHTML += lineHtml;
    const processorWindowBounds = document.getElementById('processor-display').getBoundingClientRect();
    const currentStatus = document.getElementsByClassName('current-status')[0];
    if(currentStatus.getBoundingClientRect().bottom > processorWindowBounds.bottom){
        const scroll = (currentStatus.getBoundingClientRect().top - processorWindowBounds.top) - 10;
        document.getElementById('processor-display').scrollTop += scroll;
    }
}

function adjustUIEnd(){
    const cancelButtonDivElements = document.getElementsByClassName('cancel-div');
    for(let i in cancelButtonDivElements){
        if(cancelButtonDivElements.hasOwnProperty(i)){
            cancelButtonDivElements[i].style.display = 'none';
        }
    }
    const startButtonDivElements = document.getElementsByClassName('start-div');
    for(let i in startButtonDivElements){
        if(startButtonDivElements.hasOwnProperty(i)){
            startButtonDivElements[i].style.display = 'block';
        }
    }
    const startButtonElements = document.getElementsByClassName('start-button');
    for(let i in startButtonElements){
        if(startButtonElements.hasOwnProperty(i)){
            startButtonElements[i].disabled = false;
        }
    }

    if(document.getElementById("progressDisplayList").innerHTML !== ''){
        const cancelButtonDivElements = document.getElementsByClassName('cancel-div');
        for(let i in cancelButtonDivElements){
            if(cancelButtonDivElements.hasOwnProperty(i)){
                cancelButtonDivElements[i].style.display = 'none';
            }
        }
        const startButtonDivElements = document.getElementsByClassName('start-div');
        for(let i in startButtonDivElements){
            if(startButtonDivElements.hasOwnProperty(i)){
                startButtonDivElements[i].style.display = 'block';
            }
        }
        const startButtonElements = document.getElementsByClassName('start-button');
        for(let i in startButtonElements){
            if(startButtonElements.hasOwnProperty(i)){
                startButtonElements[i].disabled = false;
            }
        }
    }
    document.getElementById('targetkingdomselect').disabled = false;
    document.getElementById('updatedetimage').disabled = false;
    document.getElementById('colradio').disabled = false;
    document.getElementById('itisradio').disabled = false;
    document.getElementById('wormsradio').disabled = false;
    unlinkedNamesArr = [];
    dataSource = '';
    setUnlinkedRecordCounts();
}

function adjustUIStart(id){
    clearProgressDisplay();
    const startDivId = id + 'Start';
    const cancelDivId = id + 'Cancel';
    const startButtonElements = document.getElementsByClassName('start-button');
    for(let i in startButtonElements){
        if(startButtonElements.hasOwnProperty(i)){
            startButtonElements[i].disabled = true;
        }
    }
    document.getElementById(startDivId).style.display = 'none';
    document.getElementById(cancelDivId).style.display = 'block';
    document.getElementById('targetkingdomselect').disabled = true;
    document.getElementById('updatedetimage').disabled = true;
    document.getElementById('colradio').disabled = true;
    document.getElementById('itisradio').disabled = true;
    document.getElementById('wormsradio').disabled = true;
}

function callCleaningController(step){
    let params = '';
    if(step === 'leading-trailing-spaces'){
        processCancelled = false;
        adjustUIStart('cleanProcesses');
        addProgressLine('<li>Cleaning leading and trailing spaces in scientific names ' + processStatus + '</li>');
        params = 'collid=' + collId + '&action=cleanTrimNames';
    }
    if(!processCancelled){
        if(step === 'clean-sp'){
            addProgressLine('<li>Cleaning scientific names ending in sp. or containing spp. ' + processStatus + '</li>');
            params = 'collid=' + collId + '&action=cleanSpNames';
        }
        else if(step === 'clean-qualifier'){
            addProgressLine('<li>Cleaning scientific names containing cf. or aff. ' + processStatus + '</li>');
            params = 'collid=' + collId + '&action=cleanQualifierNames';
        }
        else if(step === 'double-spaces'){
            addProgressLine('<li>Cleaning scientific names containing double spaces ' + processStatus + '</li>');
            params = 'collid=' + collId + '&action=cleanDoubleSpaces';
        }
        //console.log(occTaxonomyApi+'?'+params);
        sendAPIPostRequest(occTaxonomyApi,params,function(status,res){
            processCleaningControllerResponse(step,status,res);
        },http);
    }
}

function callTaxThesaurusLinkController(step = ''){
    if(targetKingdomId){
        let params = '';
        if(!step){
            processCancelled = false;
            adjustUIStart('updateWithTaxThesaurus');
            addProgressLine('<li>Updating linkages of occurrence records to the Taxonomic Thesaurus ' + processStatus + '</li>');
            params = 'collid=' + collId + '&kingdomid=' + targetKingdomId + '&action=updateOccThesaurusLinkages';
        }
        if(!processCancelled){
            if(step === 'update-det-linkages'){
                addProgressLine('<li>Updating linkages of associated determination records to the Taxonomic Thesaurus ' + processStatus + '</li>');
                params = 'collid=' + collId + '&kingdomid=' + targetKingdomId + '&action=updateDetThesaurusLinkages';
            }
            else if(step === 'update-image-linkages'){
                addProgressLine('<li>Updating linkages of associated media records to the Taxonomic Thesaurus ' + processStatus + '</li>');
                params = 'collid=' + collId + '&kingdomid=' + targetKingdomId + '&action=updateMediaThesaurusLinkages';
            }
            //console.log(occTaxonomyApi+'?'+params);
            sendAPIPostRequest(occTaxonomyApi,params,function(status,res){
                processTaxThesaurusLinkControllerResponse(step,status,res);
            },http);
        }
    }
    else{
        alert('Please select a Target Kingdom from the dropdown menu above.');
    }
}

function cancelProcess(){
    processCancelled = true;
    http.abort();
    adjustUIEnd();
}

function clearProgressDisplay(){
    document.getElementById("progressDisplayList").innerHTML = '';
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
                let foundNameRank = nameSearchResults[0]['rankid'];
                if(!nameSearchResults[0]['accepted']){
                    const acceptedObj = resArr.find(rettaxon => rettaxon['taxonName'] === nameSearchResults[0]['accepted_sciname']);
                    foundNameRank = Number(rankArr[acceptedObj['rankName'].toLowerCase()]);
                }
                for(let i in resArr){
                    if(resArr.hasOwnProperty(i)){
                        const taxResult = resArr[i];
                        if(taxResult['taxonName'] !== nameSearchResults[0]['sciname']){
                            const rankname = taxResult['rankName'].toLowerCase();
                            const rankid = Number(rankArr[rankname]);
                            if(rankid <= foundNameRank && recognizedRanks.includes(rankid)){
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
                const foundNameRank = nameSearchResults[0]['rankid'];
                let childObj = resObj['child'];
                const firstObj = {};
                const firstrankname = childObj['rank'].toLowerCase();
                const firstrankid = Number(rankArr[firstrankname]);
                const newTaxonAccepted = nameSearchResults[0]['accepted'];
                firstObj['id'] = childObj['AphiaID'];
                firstObj['sciname'] = childObj['scientificname'];
                firstObj['author'] = '';
                firstObj['rankname'] = firstrankname;
                firstObj['rankid'] = firstrankid;
                hierarchyArr.push(firstObj);
                let stopLoop = false;
                while((childObj = childObj['child']) && !stopLoop){
                    if(childObj['scientificname'] !== nameSearchResults[0]['sciname']){
                        const rankname = childObj['rank'].toLowerCase();
                        const rankid = Number(rankArr[rankname]);
                        if((newTaxonAccepted && rankid < foundNameRank && recognizedRanks.includes(rankid)) || (!newTaxonAccepted && (childObj['scientificname'] === nameSearchResults[0]['accepted_sciname'] || recognizedRanks.includes(rankid)))){
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
                        if((newTaxonAccepted && rankid === foundNameRank) || (!newTaxonAccepted && childObj['scientificname'] === nameSearchResults[0]['accepted_sciname'])){
                            stopLoop = true;
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
                    else{
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
                setUnlinkedTaxaList();
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
                    if(http.responseText && Number(http.responseText) > 0){
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
        if(nameTidIndex.hasOwnProperty(taxonToAdd['sciname'])){
            processSuccessResponse(15,nameSearchResults[0]['sciname'] + 'already added');
            updateOccurrenceLinkages();
        }
        else{
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
                    if(http.responseText && Number(http.responseText) > 0){
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
}

function processCleaningControllerResponse(step,status,res){
    processUpdateCleanResponse('cleaned',status,res);
    if(step === 'leading-trailing-spaces'){
        callCleaningController('clean-sp');
    }
    else if(step === 'clean-sp'){
        callCleaningController('clean-qualifier');
    }
    else if(step === 'clean-qualifier'){
        callCleaningController('double-spaces');
    }
    else if(step === 'double-spaces'){
        adjustUIEnd();
    }
}

function processErrorResponse(indent,setCounts,messageText = ''){
    const currentStatus = document.getElementsByClassName('current-status')[0];
    currentStatus.className = 'error-status';
    if(indent > 0){
        currentStatus.style.marginLeft = indent + 'px';
    }
    if(messageText){
        currentStatus.innerHTML = messageText;
    }
    else if(http.status === 0){
        currentStatus.innerHTML = 'Cancelled';
    }
    else{
        currentStatus.innerHTML = 'Error: ' + http.status + ' ' + http.statusText;
    }
    if(setCounts){
        setUnlinkedRecordCounts();
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
                    resultObj['author'] = taxResult.hasOwnProperty('author') ? taxResult['author'] : '';
                    let rankName = taxResult['rank'].toLowerCase();
                    if(rankName === 'infraspecies'){
                        resultObj['sciname'] = taxResult['genus'] + ' ' + taxResult['species'] + ' ' + taxResult['infraspeciesMarker'] + ' ' + taxResult['infraspecies'];
                        if(taxResult['infraspeciesMarker'] === 'var.'){
                            rankName = 'variety';
                        }
                        else if(taxResult['infraspeciesMarker'] === 'subsp.'){
                            rankName = 'subspecies';
                        }
                        else if(taxResult['infraspeciesMarker'] === 'f.'){
                            rankName = 'form';
                        }
                    }
                    else{
                        resultObj['sciname'] = taxResult['name'];
                    }
                    resultObj['rankname'] = rankName;
                    resultObj['rankid'] = rankArr.hasOwnProperty(resultObj['rankname']) ? rankArr[resultObj['rankname']] : null;
                    if(status === 'accepted name'){
                        resultObj['accepted'] = true;
                    }
                    else if(status === 'synonym'){
                        const hierarchyArr = [];
                        const resultHObj = {};
                        const acceptedObj = taxResult['accepted_name'];
                        resultObj['accepted'] = false;
                        resultObj['accepted_id'] = acceptedObj['id'];
                        resultHObj['id'] = acceptedObj['id'];
                        resultHObj['author'] = acceptedObj.hasOwnProperty('author') ? acceptedObj['author'] : '';
                        let rankName = acceptedObj['rank'].toLowerCase();
                        if(rankName === 'infraspecies'){
                            resultHObj['sciname'] = acceptedObj['genus'] + ' ' + acceptedObj['species'] + ' ' + acceptedObj['infraspeciesMarker'] + ' ' + acceptedObj['infraspecies'];
                            if(acceptedObj['infraspeciesMarker'] === 'var.'){
                                rankName = 'variety';
                            }
                            else if(acceptedObj['infraspeciesMarker'] === 'subsp.'){
                                rankName = 'subspecies';
                            }
                            else if(acceptedObj['infraspeciesMarker'] === 'f.'){
                                rankName = 'form';
                            }
                        }
                        else{
                            resultHObj['sciname'] = acceptedObj['name'];
                        }
                        resultObj['accepted_sciname'] = resultHObj['sciname'];
                        resultHObj['rankname'] = rankName;
                        resultHObj['rankid'] = rankArr.hasOwnProperty(resultHObj['rankname']) ? rankArr[resultHObj['rankname']] : null;
                        hierarchyArr.push(resultHObj);
                        resultObj['hierarchy'] = hierarchyArr;
                    }
                    const existingObj = colInitialSearchResults.find(taxon => (taxon['sciname'] === resultObj['sciname'] && taxon['accepted_sciname'] === resultObj['accepted_sciname']));
                    if(!existingObj){
                        colInitialSearchResults.push(resultObj);
                    }
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

function processGetITISTaxonByScinameResponse(res){
    itisInitialSearchResults = [];
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
                    itisInitialSearchResults.push(resultObj);
                }
            }
        }
        if(itisInitialSearchResults.length === 1){
            nameSearchResults = itisInitialSearchResults;
            getITISNameSearchResultsRecord();
        }
        else if(itisInitialSearchResults.length === 0){
            processErrorResponse(15,false,'Not found');
            runScinameDataSourceSearch();
        }
        else if(itisInitialSearchResults.length > 1){
            validateITISInitialNameSearchResults();
        }
    }
    else{
        processErrorResponse(15,false,'Not found');
        runScinameDataSourceSearch();
    }
}

function processSuccessResponse(indent,lineHtml = ''){
    const currentStatus = document.getElementsByClassName('current-status')[0];
    currentStatus.className = 'success-status';
    if(lineHtml){
        if(indent > 0){
            currentStatus.style.marginLeft = indent + 'px';
        }
        currentStatus.innerHTML = lineHtml;
    }
    else{
        currentStatus.style.display = 'none';
    }
}

function processTaxThesaurusLinkControllerResponse(step,status,res){
    const includeDetsImages = document.getElementById('updatedetimage').checked;
    processUpdateCleanResponse('updated',status,res);
    if(!step && includeDetsImages){
        callTaxThesaurusLinkController('update-det-linkages');
    }
    else if(step === 'update-det-linkages'){
        callTaxThesaurusLinkController('update-image-linkages');
    }
    else{
        adjustUIEnd();
    }
}

function processUpdateCleanResponse(term,status,res){
    if(status === 200) {
        processSuccessResponse(15,'Complete: ' + res + ' records ' + term);
    }
    else{
        processErrorResponse(15,true);
    }
}

function runScinameDataSourceSearch(){
    if(!processCancelled){
        if(unlinkedNamesArr.length > 0){
            nameSearchResults = new Array();
            currentSciname = unlinkedNamesArr[0];
            unlinkedNamesArr.splice(0, 1);
            if(dataSource === 'col'){
                colInitialSearchResults = [];
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
                itisInitialSearchResults = [];
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
                    if(status === 200 && res && Number(res) > 0){
                        getWoRMSNameSearchResultsRecord(res);
                    }
                    else if(status === 204 || !res || Number(res) <= 0){
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

function setDataSource(){
    if(document.getElementById('colradio').checked === true){
        dataSource = 'col';
    }
    else if(document.getElementById('itisradio').checked === true){
        dataSource = 'itis';
    }
    else if(document.getElementById('wormsradio').checked === true){
        dataSource = 'worms';
    }
}

function setKingdomId(){
    const selector = document.getElementById('targetkingdomselect');
    targetKingdomId = selector.value ? selector.value : null;
    targetKingdomName = targetKingdomId ? selector.options[selector.selectedIndex].text : null;
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

function setUnlinkedRecordCounts(){
    const loadingMessage = '<img src="../../images/workingcircle.gif" style="width:15px;" />';
    document.getElementById("unlinkedOccCnt").innerHTML = loadingMessage;
    document.getElementById("unlinkedTaxaCnt").innerHTML = loadingMessage;
    const recHttp = new XMLHttpRequest();
    const params = 'collid=' + collId + '&action=getUnlinkedScinameCounts';
    //console.log(occTaxonomyApi+'?'+params);
    recHttp.open("POST", occTaxonomyApi, true);
    recHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    recHttp.onreadystatechange = function() {
        if(recHttp.readyState === 4) {
            let retData = {};
            if(recHttp.status === 200) {
                retData = JSON.parse(recHttp.responseText);
            }
            if(retData.hasOwnProperty('occCnt') && retData.hasOwnProperty('taxaCnt')){
                document.getElementById("unlinkedOccCnt").innerHTML = retData['occCnt'];
                document.getElementById("unlinkedTaxaCnt").innerHTML = retData['taxaCnt'];
            }
            else{
                const errorMessage = '<span style="color:red;">Error loading count</span>';
                document.getElementById("unlinkedOccCnt").innerHTML = errorMessage;
                document.getElementById("unlinkedTaxaCnt").innerHTML = errorMessage;
            }
        }
    };
    recHttp.send(params);
}

function setUnlinkedTaxaList(){
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

function updateOccLocalitySecurity(){
    adjustUIStart('updateOccLocalitySecurity');
    addProgressLine('<li>Updating the locality security settings for occurrence records of protected species ' + processStatus + '</li>');
    const params = 'collid=' + collId + '&action=updateLocalitySecurity';
    //console.log(occTaxonomyApi+'?'+params);
    sendAPIPostRequest(occTaxonomyApi,params,function(status,res){
        processUpdateCleanResponse('updated',status,res);
        adjustUIEnd();
    },http);
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
                        let hierarchyArr = [];
                        if(taxon.hasOwnProperty('hierarchy')){
                            hierarchyArr = taxon['hierarchy'];
                        }
                        for(let i in resArr){
                            if(resArr.hasOwnProperty(i)){
                                const taxResult = resArr[i];
                                if(taxResult['name'] !== taxon['sciname']){
                                    const rankname = taxResult['rank'].toLowerCase();
                                    const rankid = Number(rankArr[rankname]);
                                    if(recognizedRanks.includes(rankid) || (!taxon['accepted'] && taxon['accepted_sciname'] === taxResult['name'])){
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

function validateITISInitialNameSearchResults(){
    if(!processCancelled){
        if(itisInitialSearchResults.length > 0){
            const taxon = itisInitialSearchResults[0];
            itisInitialSearchResults.splice(0, 1);
            const id = taxon['id'];
            const url = 'https://www.itis.gov/ITISWebService/jsonservice/getFullRecordFromTSN?tsn=' + id;
            sendProxyGetRequest(proxyUrl,url,sessionId,function(status,res){
                if(status === 200){
                    const resObj = JSON.parse(res);
                    const taxonRankData = resObj['taxRank'];
                    taxon['rankname'] = taxonRankData['rankName'].toLowerCase().trim();
                    taxon['rankid'] = Number(taxonRankData['rankId']);
                    const coreMetadata = resObj['coreMetadata'];
                    const namestatus = coreMetadata['taxonUsageRating'];
                    if(namestatus === 'accepted'){
                        taxon['accepted'] = true;
                        nameSearchResults.push(taxon);
                        validateITISInitialNameSearchResults();
                    }
                    else if(namestatus === 'not accepted'){
                        taxon['accepted'] = false;
                        const acceptedNameList = resObj['acceptedNameList'];
                        const acceptedNameArr = acceptedNameList['acceptedNames'];
                        if(acceptedNameArr.length > 0){
                            const acceptedName = acceptedNameArr[0];
                            if(taxon['sciname'] !== acceptedName['acceptedName']){
                                taxon['accepted_id'] = acceptedName['acceptedTsn'];
                                taxon['accepted_sciname'] = acceptedName['acceptedName'];
                                nameSearchResults.push(taxon);
                            }
                        }
                        validateITISInitialNameSearchResults();
                    }
                }
                else{
                    processErrorResponse(15,false,'Unable to retrieve taxon record');
                    runScinameDataSourceSearch();
                }
            },http);
        }
        else if(nameSearchResults.length === 1){
            getITISNameSearchResultsHierarchy();
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

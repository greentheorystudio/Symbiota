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
let newTidArr = [];
let taxaLoaded = 0;
let rebuildHierarchyLoop = 0;
let levValue = 0;

function addProgressLine(lineHtml,element = null){
    if(element){
        element.innerHTML = lineHtml;
    }
    else{
        document.getElementById("progressDisplayList").innerHTML += lineHtml;
    }
    const processorWindowBounds = document.getElementById('processor-display').getBoundingClientRect();
    const currentStatus = document.getElementsByClassName('current-status')[0];
    if(currentStatus.getBoundingClientRect().bottom > processorWindowBounds.bottom){
        const scroll = (currentStatus.getBoundingClientRect().top - processorWindowBounds.top) - 10;
        document.getElementById('processor-display').scrollTop += scroll;
    }
}

function addRunCleanScinameAuthorUndoButton(oldName,newName){
    const cleanedOldName = oldName.replaceAll("'",'%squot;').replaceAll('"','%dquot;');
    const undoChangedScinameInner = "'" + cleanedOldName + "','" + newName.replaceAll("'",'%squot;').replaceAll('"','%dquot;') + "'";
    const undoButtonHtml = '<button class="undo-button" onclick="undoChangedSciname(' + undoChangedScinameInner + ');" disabled>Undo</button>';
    addProgressLine('<li class="first-indent undo-button" id="undo-' + cleanedOldName + '">' + undoButtonHtml + '<span class="current-status"></span></li>');
    processSuccessResponse(0);
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
        const undoButtonElements = document.getElementsByClassName('undo-button');
        for(let i in undoButtonElements){
            if(undoButtonElements.hasOwnProperty(i)){
                undoButtonElements[i].disabled = false;
            }
        }
    }
    document.getElementById('targetkingdomselect').disabled = false;
    document.getElementById('updatedetimage').disabled = false;
    document.getElementById('colradio').disabled = false;
    document.getElementById('itisradio').disabled = false;
    document.getElementById('wormsradio').disabled = false;
    document.getElementById('levvalue').disabled = false;
    unlinkedNamesArr = [];
    dataSource = '';
    setUnlinkedRecordCounts();
    disableFuzzyMatchButtons();
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
    document.getElementById('levvalue').disabled = true;
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
            addProgressLine('<li>Cleaning scientific names ending in sp., sp. nov., spp., or group ' + processStatus + '</li>');
            params = 'collid=' + collId + '&action=cleanSpNames';
        }
        else if(step === 'clean-infra'){
            addProgressLine('<li>Normalizing infraspecific rank abbreviations ' + processStatus + '</li>');
            params = 'collid=' + collId + '&action=cleanInfra';
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

function cancelProcess(adjustUI = true){
    processCancelled = true;
    http.abort();
    if(adjustUI){
        adjustUIEnd();
    }
}

function clearProgressDisplay(){
    document.getElementById("progressDisplayList").innerHTML = '';
}

function disableFuzzyMatchButtons(){
    const buttonElements = document.getElementsByClassName('fuzzy-button');
    for(let i in buttonElements){
        if(buttonElements.hasOwnProperty(i)){
            buttonElements[i].disabled = true;
        }
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

function getITISNameSearchResultsHierarchy(){
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
    });
}

function getITISNameSearchResultsRecord(){
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
            if(namestatus === 'accepted' || namestatus === 'valid'){
                nameSearchResults[0]['accepted'] = true;
                getITISNameSearchResultsHierarchy();
            }
            else{
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
    });
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
    });
}

function getWoRMSNameSearchResultsRecord(id){
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
    });
}

function initializeCleanScinameAuthor(){
    processCancelled = false;
    adjustUIStart('cleanScinameAuthor');
    addProgressLine('<li>Getting unlinked occurrence record scientific names ' + processStatus + '</li>');
    const params = 'collid=' + collId + '&action=getUnlinkedOccSciNames';
    //console.log(occTaxonomyApi+'?'+params);
    sendAPIPostRequest(occTaxonomyApi,params,function(status,res){
        if(status === 200) {
            processSuccessResponse(15,'Complete');
            unlinkedNamesArr = processUnlinkedNamesArr(JSON.parse(res));
            runCleanScinameAuthorProcess();
        }
        else{
            processErrorResponse(15,true);
        }
    },http);
}

function initializeDataSourceSearch(){
    if(targetKingdomId){
        processCancelled = false;
        nameTidIndex = {};
        taxaLoaded = 0;
        newTidArr = [];
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

function initializeTaxThesaurusFuzzyMatch(){
    levValue = document.getElementById("levvalue").value;
    if(targetKingdomId && levValue && Number(levValue) > 0){
        processCancelled = false;
        adjustUIStart('taxThesaurusFuzzyMatch');
        addProgressLine('<li>Getting unlinked occurrence record scientific names ' + processStatus + '</li>');
        const params = 'collid=' + collId + '&action=getUnlinkedOccSciNames';
        //console.log(occTaxonomyApi+'?'+params);
        sendAPIPostRequest(occTaxonomyApi,params,function(status,res){
            if(status === 200) {
                processSuccessResponse(15,'Complete');
                unlinkedNamesArr = processUnlinkedNamesArr(JSON.parse(res));
                runTaxThesaurusFuzzyMatchProcess();
            }
            else{
                processErrorResponse(15,true);
            }
        },http);
    }
    else if(!targetKingdomId){
        alert('Please select a Target Kingdom from the dropdown menu above.');
    }
    else{
        alert('Please select a character difference tolerance value greater than zero.');
    }
}

function populateTaxonomicHierarchy(){
    if(rebuildHierarchyLoop < 40){
        const formData = new FormData();
        formData.append('tidarr', JSON.stringify(newTidArr));
        formData.append('action', 'populateHierarchyTable');
        const http = new XMLHttpRequest();
        http.open("POST", taxaApi, true);
        http.onreadystatechange = function() {
            if(http.readyState === 4) {
                if(http.status === 200) {
                    if(Number(http.responseText) > 0){
                        rebuildHierarchyLoop++;
                        populateTaxonomicHierarchy();
                    }
                    else{
                        processSuccessResponse(15,'Complete');
                        adjustUIEnd();
                    }
                }
                else{
                    processErrorResponse(15,false,'Error rebuilding the taxonomic hierarchy');
                    adjustUIEnd();
                }
            }
        };
        http.send(formData);
    }
    else{
        processErrorResponse(15,false,'Error rebuilding the taxonomic hierarchy');
        adjustUIEnd();
    }
}

function primeTaxonomicHierarchy(){
    rebuildHierarchyLoop = 0;
    addProgressLine('<li>Populating taxonomic hierarchy with new taxa ' + processStatus + '</li>');
    const formData = new FormData();
    formData.append('tidarr', JSON.stringify(newTidArr));
    formData.append('action', 'primeHierarchyTable');
    const http = new XMLHttpRequest();
    http.open("POST", taxaApi, true);
    http.onreadystatechange = function() {
        if(http.readyState === 4) {
            if(http.status === 200) {
                if(Number(http.responseText) > 0){
                    rebuildHierarchyLoop++;
                    populateTaxonomicHierarchy();
                }
                else{
                    adjustUIEnd();
                }
            }
            else{
                processErrorResponse(15,false,'Error rebuilding the taxonomic hierarchy');
                adjustUIEnd();
            }
        }
    };
    http.send(formData);
}

function processAddTaxaArr(){
    if(taxaToAddArr.length > 0){
        const taxonToAdd = taxaToAddArr[0];
        addProgressLine('<li class="first-indent">Adding ' + taxonToAdd['sciname'] + ' to the Taxonomic Thesaurus ' + processStatus + '</li>');
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
        const addHttp = new XMLHttpRequest();
        addHttp.open("POST", taxaApi, true);
        addHttp.onreadystatechange = function() {
            if(addHttp.readyState === 4) {
                if(addHttp.responseText && Number(addHttp.responseText) > 0){
                    const newTid = Number(addHttp.responseText);
                    nameTidIndex[taxaToAddArr[0]['sciname']] = newTid;
                    newTidArr.push(newTid);
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
        addHttp.send(formData);
    }
    else{
        processAddTaxon();
    }
}

function processAddTaxon(){
    const taxonToAdd = nameSearchResults[0];
    addProgressLine('<li class="first-indent">Adding ' + taxonToAdd['sciname'] + ' to the Taxonomic Thesaurus ' + processStatus + '</li>');
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
        const addHttp = new XMLHttpRequest();
        addHttp.open("POST", taxaApi, true);
        addHttp.onreadystatechange = function() {
            if(addHttp.readyState === 4) {
                if(addHttp.responseText && Number(addHttp.responseText) > 0){
                    const newTid = Number(addHttp.responseText);
                    nameTidIndex[nameSearchResults[0]['sciname']] = newTid;
                    newTidArr.push(newTid);
                    processSuccessResponse(15,'Successfully added ' + nameSearchResults[0]['sciname']);
                    if(currentSciname === nameSearchResults[0]['sciname']){
                        updateOccurrenceLinkages();
                    }
                    else{
                        addProgressLine('<li class="first-indent">Updating occurrence records with cleaned scientific name ' + processStatus + '</li>');
                        updateOccurrencesWithCleanedSciname(currentSciname,nameSearchResults[0]['sciname'],function(status,res,current,parsed){
                            if(status === 200) {
                                processSuccessResponse(15,(res + ' records updated'));
                                addRunCleanScinameAuthorUndoButton(current,parsed);
                                updateOccurrenceLinkages();
                            }
                            else{
                                processErrorResponse(15,true,'Error updating occurrence records');
                                updateOccurrenceLinkages();
                            }
                        });
                    }
                }
                else{
                    processErrorResponse(15,false,'Error loading taxon');
                    runScinameDataSourceSearch();
                }
            }
        };
        addHttp.send(formData);
    }
}

function processCleaningControllerResponse(step,status,res){
    processUpdateCleanResponse('cleaned',status,res);
    if(step === 'leading-trailing-spaces'){
        callCleaningController('clean-sp');
    }
    else if(step === 'clean-sp'){
        callCleaningController('clean-infra');
    }
    else if(step === 'clean-infra'){
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

function processFuzzyMatches(fuzzyMatches){
    for(let i in fuzzyMatches){
        if(fuzzyMatches.hasOwnProperty(i)){
            const fuzzyMatchName = fuzzyMatches[i];
            const selectFuzzyMatchInner = "'" + currentSciname.replaceAll("'",'%squot;').replaceAll('"','%dquot;') + "','" + fuzzyMatchName.replaceAll("'",'%squot;').replaceAll('"','%dquot;') + "'";
            const selectButtonHtml = '<button class="fuzzy-button" onclick="selectFuzzyMatch(' + selectFuzzyMatchInner + ');">Select</button>';
            addProgressLine('<li style="margin-left:15px;margin-top:10px;"><b>Match: ' + fuzzyMatchName + '</b> ' + selectButtonHtml + '<span class="current-status"></span></li>');
            processSuccessResponse(0);
        }
    }
    const skipButtonHtml = '<button class="fuzzy-button" onclick="runTaxThesaurusFuzzyMatchProcess();">Skip to Next Scientific Name</button>';
    addProgressLine('<li style="margin-left:15px;margin-top:10px;margin-bottom:10px;">' + skipButtonHtml + '<span class="current-status"></span></li>');
    processSuccessResponse(0);
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

function processUnlinkedNamesArr(inArr){
    if(Array.isArray(inArr) && inArr.length > 0){
        const startIndex = document.getElementById("startIndex").value;
        const limitValue = document.getElementById("processingLimit").value;
        if(startIndex){
            let nameArrLength = inArr.length;
            let startIndexVal = 0;
            for(let i = 0 ; i < nameArrLength; i++) {
                if(inArr.hasOwnProperty(i) && inArr[i] > startIndex){
                    startIndexVal = i;
                    break;
                }
            }
            inArr = inArr.splice(startIndexVal, (nameArrLength - startIndexVal));
        }
        if(limitValue){
            inArr = inArr.splice(0, limitValue);
        }
    }
    return inArr;
}

function processUpdateCleanResponse(term,status,res){
    if(status === 200) {
        processSuccessResponse(15,'Complete: ' + res + ' records ' + term);
    }
    else{
        processErrorResponse(15,true);
    }
}

function runCleanScinameAuthorProcess(){
    if(!processCancelled){
        if(unlinkedNamesArr.length > 0){
            currentSciname = unlinkedNamesArr[0];
            unlinkedNamesArr.splice(0, 1);
            addProgressLine('<li>Attempting to parse author name from: ' + currentSciname + ' ' + processStatus + '</li>');
            const formData = new FormData();
            formData.append('sciname', currentSciname);
            formData.append('action', 'parseSciName');
            const http = new XMLHttpRequest();
            http.open("POST", taxaApi, true);
            http.onreadystatechange = function() {
                if(http.readyState === 4 && http.status === 200) {
                    const parsedName = JSON.parse(http.responseText);
                    if(parsedName.hasOwnProperty('author') && parsedName['author'] !== ''){
                        processSuccessResponse(15,'Found author: ' + parsedName['author']);
                        addProgressLine('<li class="first-indent">Updating occurrence records with cleaned scientific name ' + processStatus + '</li>');
                        updateOccurrencesWithCleanedSciname(currentSciname,parsedName['sciname'],function(status,res,current,parsed){
                            if(status === 200) {
                                processSuccessResponse(15,(res + ' records updated'));
                                addRunCleanScinameAuthorUndoButton(current,parsed);
                                runCleanScinameAuthorProcess();
                            }
                            else{
                                processErrorResponse(15,false,'Error updating occurrence records');
                                runCleanScinameAuthorProcess();
                            }
                        });
                    }
                    else{
                        processErrorResponse(15,false,'No author found in scientific name');
                        runCleanScinameAuthorProcess();
                    }
                }
            };
            http.send(formData);
        }
        else{
            adjustUIEnd();
        }
    }
    else{
        adjustUIEnd();
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
                        runScinameDataSourceSearch();
                    }
                });
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
                        runScinameDataSourceSearch();
                    }
                });
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
                        runScinameDataSourceSearch();
                    }
                });
            }
        }
        else if(newTidArr.length > 0){
            primeTaxonomicHierarchy();
        }
        else{
            adjustUIEnd();
        }
    }
    else if(newTidArr.length > 0){
        primeTaxonomicHierarchy();
    }
    else{
        adjustUIEnd();
    }
}

function runTaxThesaurusFuzzyMatchProcess(){
    disableFuzzyMatchButtons();
    if(!processCancelled){
        if(unlinkedNamesArr.length > 0){
            currentSciname = unlinkedNamesArr[0];
            unlinkedNamesArr.splice(0, 1);
            addProgressLine('<li>Finding fuzzy matches for: ' + currentSciname + ' ' + processStatus + '</li>');
            const formData = new FormData();
            formData.append('kingdomid', targetKingdomId);
            formData.append('sciname', currentSciname);
            formData.append('lev', levValue);
            formData.append('action', 'getSciNameFuzzyMatches');
            const http = new XMLHttpRequest();
            http.open("POST", taxaApi, true);
            http.onreadystatechange = function() {
                if(http.readyState === 4 && http.status === 200) {
                    const fuzzyMatches = JSON.parse(http.responseText);
                    if(checkObjectNotEmpty(fuzzyMatches)){
                        processSuccessResponse(0);
                        processFuzzyMatches(fuzzyMatches);
                    }
                    else{
                        processErrorResponse(15,false,'No fuzzy matched found');
                        runTaxThesaurusFuzzyMatchProcess();
                    }
                }
            };
            http.send(formData);
        }
        else{
            adjustUIEnd();
        }
    }
    else{
        adjustUIEnd();
    }
}

function selectFuzzyMatch(sciName,newName){
    disableFuzzyMatchButtons();
    addProgressLine('<li class="first-indent">Updating occurrence records with selected scientific name ' + processStatus + '</li>');
    updateOccurrencesWithCleanedSciname(sciName,newName,function(status,res,current,parsed){
        if(status === 200) {
            processSuccessResponse(15,(res + ' records updated'));
            addRunCleanScinameAuthorUndoButton(current,parsed);
            runTaxThesaurusFuzzyMatchProcess();
        }
        else{
            processErrorResponse(15,false,'Error updating occurrence records');
            runTaxThesaurusFuzzyMatchProcess();
        }
    });
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
            });
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

function setUnlinkedRecordCounts(){
    const loadingMessage = getSmallWorkingSpinnerHtml(12);
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
                unlinkedNamesArr = processUnlinkedNamesArr(JSON.parse(res));
                runScinameDataSourceSearch();
            }
            else{
                processErrorResponse(15,true);
            }
        },http);
    }
}

function undoChangedSciname(oldName,newName){
    const progressLineElementId = 'undo-' + oldName;
    const progressLineElement = document.getElementById(progressLineElementId);
    const progressLineElementHtml = 'Reverting scientific name change from ' + oldName.replaceAll('%squot;',"'").replaceAll('%dquot;','"') + ' to ' + newName.replaceAll('%squot;',"'").replaceAll('%dquot;','"') + ' ' + processStatus;
    addProgressLine(progressLineElementHtml,progressLineElement);
    const formData = new FormData();
    formData.append('collid', collId);
    formData.append('oldsciname', oldName);
    formData.append('newsciname', newName);
    formData.append('action', 'undoOccScinameChange');
    const http = new XMLHttpRequest();
    http.open("POST", occTaxonomyApi, true);
    http.onreadystatechange = function() {
        if(http.readyState === 4) {
            if(http.status === 200) {
                processSuccessResponse(15,(http.responseText + ' records reverted'));
            }
            else{
                processErrorResponse(15,false,'Error undoing name change');
            }
        }
    };
    http.send(formData);
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

function updateOccurrenceLinkages(){
    const newSciname = nameSearchResults[0]['sciname'];
    const newScinameTid = nameTidIndex[nameSearchResults[0]['sciname']];
    addProgressLine('<li class="first-indent">Updating linkages of occurrence records to ' + newSciname + ' ' + processStatus + '</li>');
    let params = 'collid=' + collId + '&sciname=' + newSciname + '&tid=' + newScinameTid + '&kingdomid=' + targetKingdomId + '&action=updateOccWithNewSciname';
    //console.log(occTaxonomyApi+'?'+params);
    sendAPIPostRequest(occTaxonomyApi,params,function(status,res){
        if(status === 200) {
            processSuccessResponse(15, res + ' records updated');
        }
        else{
            processErrorResponse(15,true);
        }
        taxaLoaded++;
        if(taxaLoaded > 30){
            setUnlinkedRecordCounts();
            taxaLoaded = 0;
        }
        runScinameDataSourceSearch();
    });
}

function updateOccurrencesWithCleanedSciname(oldName,cleanedName,callback){
    const formData = new FormData();
    formData.append('collid', collId);
    formData.append('sciname', oldName);
    formData.append('cleanedsciname', cleanedName);
    formData.append('action', 'updateOccWithCleanedName');
    const http = new XMLHttpRequest();
    http.open("POST", occTaxonomyApi, true);
    http.onreadystatechange = function() {
        if(http.readyState === 4) {
            callback(http.status,http.responseText,oldName,cleanedName);
        }
    };
    http.send(formData);
}

function validateCOLInitialNameSearchResults(){
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
        });
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

function validateITISInitialNameSearchResults(){
    if(itisInitialSearchResults.length > 0){
        const taxon = itisInitialSearchResults[0];
        itisInitialSearchResults.splice(0, 1);
        const id = taxon['id'];
        const url = 'https://www.itis.gov/ITISWebService/jsonservice/getFullRecordFromTSN?tsn=' + id;
        sendProxyGetRequest(proxyUrl,url,sessionId,function(status,res){
            if(status === 200){
                const resObj = JSON.parse(res);
                const coreMetadata = resObj['coreMetadata'];
                const namestatus = coreMetadata['taxonUsageRating'];
                if(namestatus === 'accepted'){
                    const taxonRankData = resObj['taxRank'];
                    taxon['rankname'] = taxonRankData['rankName'].toLowerCase().trim();
                    taxon['rankid'] = Number(taxonRankData['rankId']);
                    taxon['accepted'] = true;
                    nameSearchResults.push(taxon);
                }
                validateITISInitialNameSearchResults();
            }
            else{
                processErrorResponse(15,false,'Unable to retrieve taxon record');
                runScinameDataSourceSearch();
            }
        });
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

function validateNameSearchResults(){
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
            addProgressLine('<li class="first-indent">Matching parent and accepted taxa to the Taxonomic Thesaurus ' + processStatus + '</li>');
            setTaxaToAdd();
        }
    }
    else{
        processErrorResponse(15,false,'Unable to distinguish taxon by name');
        runScinameDataSourceSearch();
    }
}

function verifyBatchLimitChange(){
    const limitValue = document.getElementById("processingLimit").value;
    if(limitValue && (isNaN(limitValue) || Number(limitValue) <= 0)){
        alert('Processing batch limit must be a number greater than zero.');
        document.getElementById("processingLimit").value = '';
    }
}

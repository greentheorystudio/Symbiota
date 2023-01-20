let processCancelled = false;
let unlinkedNamesArr = [];
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
let changedCurrentSciname = '';
let changedParsedSciname = '';
let undoId = '';

function addSubprocessToProcessorDisplay(id,type,text){
    const parentProcObj = processorDisplayArr.value.find(proc => proc['id'] === id);
    parentProcObj['subs'].push(getNewSubprocessObject(currentSciname.value,type,text));
}

function adjustUIEnd(){
    currentProcess.value = null;
    undoButtonsDisabled.value = false;
    uppercontrolsdisabled.value = false;
    unlinkedNamesArr = [];
    setUnlinkedRecordCounts();
}

function adjustUIStart(id){
    processorDisplayArr.value = [];
    currentProcess.value = id;
    uppercontrolsdisabled.value = true;
    undoButtonsDisabled.value = true;
}

function callCleaningController(step){
    let params = {
        collid: collId
    };
    if(step === 'question-marks'){
        processCancelled = false;
        adjustUIStart('cleanProcesses');
        const text = 'Cleaning question marks from scientific names';
        processorDisplayArr.value.push(getNewProcessObject('cleanQuestionMarks','single',text));
        params['action'] = 'cleanQuestionMarks';
    }
    if(!processCancelled){
        if(step === 'clean-sp'){
            const text = 'Cleaning scientific names ending in sp., sp. nov., spp., or group';
            processorDisplayArr.value.push(getNewProcessObject('cleanSpNames','single',text));
            params['action'] = 'cleanSpNames';
        }
        else if(step === 'clean-infra'){
            const text = 'Normalizing infraspecific rank abbreviations';
            processorDisplayArr.value.push(getNewProcessObject('cleanInfra','single',text));
            params['action'] = 'cleanInfra';
        }
        else if(step === 'clean-qualifier'){
            const text = 'Cleaning scientific names containing cf. or aff.';
            processorDisplayArr.value.push(getNewProcessObject('cleanQualifierNames','single',text));
            params['action'] = 'cleanQualifierNames';
        }
        else if(step === 'double-spaces'){
            const text = 'Cleaning scientific names containing double spaces';
            processorDisplayArr.value.push(getNewProcessObject('cleanDoubleSpaces','single',text));
            params['action'] = 'cleanDoubleSpaces';
        }
        else if(step === 'leading-trailing-spaces'){
            const text = 'Cleaning leading and trailing spaces in scientific names';
            processorDisplayArr.value.push(getNewProcessObject('cleanTrimNames','single',text));
            params['action'] = 'cleanTrimNames';
        }
        sendAPIPostRequest(occurrenceTaxonomyApiUrl,params,function(status,res,statusText){
            processCleaningControllerResponse(step,status,res,statusText);
        },http);
    }
}

function callTaxThesaurusLinkController(step = ''){
    if(selectedKingdomId){
        let params = {
            collid: collId,
            kingdomid: selectedKingdomId
        };
        if(!step){
            processCancelled = false;
            adjustUIStart('updateWithTaxThesaurus');
            const text = 'Updating linkages of occurrence records to the Taxonomic Thesaurus';
            processorDisplayArr.value.push(getNewProcessObject('updateOccThesaurusLinkages','single',text));
            params['action'] = 'updateOccThesaurusLinkages';
        }
        if(!processCancelled){
            if(step === 'update-det-linkages'){
                const text = 'Updating linkages of associated determination records to the Taxonomic Thesaurus';
                processorDisplayArr.value.push(getNewProcessObject('updateDetThesaurusLinkages','single',text));
                params['action'] = 'updateDetThesaurusLinkages';
            }
            //console.log(url+'?'+params);
            sendAPIPostRequest(occurrenceTaxonomyApiUrl,params,function(status,res,statusText){
                processTaxThesaurusLinkControllerResponse(step,status,res,statusText);
            },http);
        }
    }
    else{
        alert('Please select a Target Kingdom from the dropdown menu above.');
    }
}

function cancelProcess(adjustUI = true){
    processCancelled = true;
    cancelAPIRequest();
    if(adjustUI){
        adjustUIEnd();
    }
}

function getDataSourceName(){
    if(dataSource.value === 'col'){
        return 'Catalogue of Life';
    }
    else if(dataSource.value === 'itis'){
        return 'Integrated Taxonomic Information System';
    }
    else if(dataSource.value === 'worms'){
        return 'World Register of Marine Species';
    }
}

function getErrorResponseText(status,statusText){
    let text;
    if(status === 0){
        text = 'Cancelled';
    }
    else{
        text = 'Error: ' + status + ' ' + statusText;
    }
    return text;
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
    const params = {
        url: url,
        action: 'get'
    };
    sendAPIPostRequest(proxyApiUrl,params,function(status,res){
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
                        if(rankid <= foundNameRank && TAXONOMIC_RANKS.includes(rankid)){
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
            processSuccessResponse(false);
            validateNameSearchResults();
        }
        else{
            processErrorResponse(false,'Unable to retrieve taxon hierarchy');
            runScinameDataSourceSearch();
        }
    });
}

function getITISNameSearchResultsRecord(){
    const id = nameSearchResults[0]['id'];
    const url = 'https://www.itis.gov/ITISWebService/jsonservice/getFullRecordFromTSN?tsn=' + id;
    const params = {
        url: url,
        action: 'get'
    };
    sendAPIPostRequest(proxyApiUrl,params,function(status,res){
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
                    processErrorResponse(false,'Unable to distinguish taxon by name');
                    runScinameDataSourceSearch();
                }
            }
        }
        else{
            processErrorResponse(false,'Unable to retrieve taxon record');
            runScinameDataSourceSearch();
        }
    });
}

function getNewProcessObject(id,type,text){
    const procObj = {
        id: id,
        procText: text,
        type: type,
        loading: true,
        current: true,
        result: '',
        resultText: ''
    };
    if(type === 'multi'){
        procObj['subs'] = [];
    }
    return procObj;
}

function getNewSubprocessObject(id,type,text){
    return {
        id: id,
        procText: text,
        type: type,
        loading: true,
        result: '',
        undoOrigName: '',
        undoChangedName: '',
        changedTid: 0,
        resultText: ''
    };
}

function getWoRMSAddTaxonAuthor(){
    if(!processCancelled){
        const id = processingArr[0]['id'];
        const url = 'https://www.marinespecies.org/rest/AphiaRecordByAphiaID/' + id;
        const params = {
            url: url,
            action: 'get'
        };
        sendAPIPostRequest(proxyApiUrl,params,function(status,res){
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
    const params = {
        url: url,
        action: 'get'
    };
    sendAPIPostRequest(proxyApiUrl,params,function(status,res){
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
                    if((newTaxonAccepted && rankid < foundNameRank && TAXONOMIC_RANKS.includes(rankid)) || (!newTaxonAccepted && (childObj['scientificname'] === nameSearchResults[0]['accepted_sciname'] || TAXONOMIC_RANKS.includes(rankid)))){
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
            processSuccessResponse(false);
            validateNameSearchResults();
        }
        else{
            processErrorResponse(false,'Unable to retrieve taxon hierarchy');
            runScinameDataSourceSearch();
        }
    });
}

function getWoRMSNameSearchResultsRecord(id){
    const url = 'https://www.marinespecies.org/rest/AphiaRecordByAphiaID/' + id;
    const params = {
        url: url,
        action: 'get'
    };
    sendAPIPostRequest(proxyApiUrl,params,function(status,res){
        if(status === 200){
            const resObj = JSON.parse(res);
            if(resObj['kingdom'].toLowerCase() === selectedKingdomName.toLowerCase()){
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
                processErrorResponse(false,'Not found');
                runScinameDataSourceSearch();
            }
        }
        else{
            processErrorResponse(false,'Unable to retrieve taxon record');
            runScinameDataSourceSearch();
        }
    });
}

function initializeCleanScinameAuthor(){
    processCancelled = false;
    adjustUIStart('cleanScinameAuthor');
    const text = 'Getting unlinked occurrence record scientific names';
    processorDisplayArr.value.push(getNewProcessObject('cleanScinameAuthor','multi',text));
    const params = {
        collid: collId,
        action: 'getUnlinkedOccSciNames'
    };
    sendAPIPostRequest(occurrenceTaxonomyApiUrl,params,function(status,res,statusText){
        if(status === 200) {
            processSuccessResponse(true,'Complete');
            unlinkedNamesArr = processUnlinkedNamesArr(JSON.parse(res));
            runCleanScinameAuthorProcess();
        }
        else{
            const text = getErrorResponseText(status,statusText);
            processErrorResponse(true,text);
        }
    },http);
}

function initializeDataSourceSearch(){
    if(selectedKingdomId){
        processCancelled = false;
        nameTidIndex = {};
        taxaLoaded = 0;
        newTidArr = [];
        adjustUIStart('resolveFromTaxaDataSource');
        const text = 'Setting rank data for processing search returns';
        processorDisplayArr.value.push(getNewProcessObject('resolveFromTaxaDataSource','multi',text));
        const url = taxonomyApiUrl + '?action=getRankNameArr'
        sendAPIGetRequest(url,function(status,res,statusText){
            if(status === 200) {
                processSuccessResponse(true, 'Complete');
                rankArr = JSON.parse(res);
                setUnlinkedTaxaList();
            }
            else{
                const text = getErrorResponseText(status,statusText);
                processErrorResponse(true,text);
            }
        },http);
    }
    else{
        alert('Please select a Target Kingdom from the dropdown menu above.');
    }
}

function initializeTaxThesaurusFuzzyMatch(){
    if(selectedKingdomId && levValue.value && Number(levValue.value) > 0){
        processCancelled = false;
        adjustUIStart('taxThesaurusFuzzyMatch');
        const text = 'Getting unlinked occurrence record scientific names';
        processorDisplayArr.value.push(getNewProcessObject('taxThesaurusFuzzyMatch','multi',text));
        const params = {
            collid: collId,
            action: 'getUnlinkedOccSciNames'
        };
        sendAPIPostRequest(occurrenceTaxonomyApiUrl,params,function(status,res,statusText){
            if(status === 200) {
                processSuccessResponse(true,'Complete');
                unlinkedNamesArr = processUnlinkedNamesArr(JSON.parse(res));
                runTaxThesaurusFuzzyMatchProcess();
            }
            else{
                const text = getErrorResponseText(status,statusText);
                processErrorResponse(true,text);
            }
        },http);
    }
    else if(!selectedKingdomId){
        alert('Please select a Target Kingdom from the dropdown menu above.');
    }
    else{
        alert('Please select a character difference tolerance value greater than zero.');
    }
}

function populateTaxonomicHierarchy(){
    if(rebuildHierarchyLoop < 40){
        const params = {
            tidarr: JSON.stringify(newTidArr),
            action: 'populateHierarchyTable'
        };
        sendAPIPostRequest(taxonomyApiUrl,params,function(status,res){
            if(status === 200) {
                if(Number(res) > 0){
                    rebuildHierarchyLoop++;
                    populateTaxonomicHierarchy();
                }
                else{
                    processSuccessResponse(true,'Complete');
                    adjustUIEnd();
                }
            }
            else{
                processErrorResponse(false,'Error rebuilding the taxonomic hierarchy');
                adjustUIEnd();
            }
        });
    }
    else{
        processErrorResponse(false,'Error rebuilding the taxonomic hierarchy');
        adjustUIEnd();
    }
}

function primeTaxonomicHierarchy(){
    rebuildHierarchyLoop = 0;
    const text = 'Populating taxonomic hierarchy with new taxa';
    processorDisplayArr.value.push(getNewProcessObject('primeHierarchyTable','multi',text));
    const params = {
        tidarr: JSON.stringify(newTidArr),
        action: 'primeHierarchyTable'
    };
    sendAPIPostRequest(taxonomyApiUrl,params,function(status,res){
        if(status === 200) {
            if(Number(res) > 0){
                rebuildHierarchyLoop++;
                populateTaxonomicHierarchy();
            }
            else{
                adjustUIEnd();
            }
        }
        else{
            processErrorResponse(false,'Error rebuilding the taxonomic hierarchy');
            adjustUIEnd();
        }
    });
}

function processAddTaxaArr(){
    if(taxaToAddArr.length > 0){
        const taxonToAdd = taxaToAddArr[0];
        const text = 'Adding ' + taxonToAdd['sciname'] + ' to the Taxonomic Thesaurus';
        addSubprocessToProcessorDisplay(currentSciname.value,'text',text);
        const newTaxonObj = {};
        newTaxonObj['sciname'] = taxonToAdd['sciname'];
        newTaxonObj['author'] = taxonToAdd['author'];
        newTaxonObj['kingdomid'] = selectedKingdomId;
        newTaxonObj['rankid'] = taxonToAdd['rankid'];
        newTaxonObj['acceptstatus'] = 1;
        newTaxonObj['tidaccepted'] = '';
        newTaxonObj['parenttid'] = nameTidIndex[taxonToAdd['parentName']];
        newTaxonObj['family'] = taxonToAdd['family'];
        newTaxonObj['source'] = getDataSourceName();
        newTaxonObj['source-name'] = dataSource.value;
        newTaxonObj['source-id'] = taxonToAdd['id'];
        const params = {
            taxon: JSON.stringify(newTaxonObj),
            action: 'addTaxon'
        };
        sendAPIPostRequest(taxonomyApiUrl,params,function(status,res){
            if(res && Number(res) > 0){
                const newTid = Number(res);
                nameTidIndex[taxaToAddArr[0]['sciname']] = newTid;
                newTidArr.push(newTid);
                taxaToAddArr.splice(0, 1);
                processSubprocessSuccessResponse(currentSciname.value,false);
                processAddTaxaArr();
            }
            else{
                processSubprocessErrorResponse(currentSciname.value,false,'Error loading taxon');
                runScinameDataSourceSearch();
            }
        });
    }
    else{
        processAddTaxon();
    }
}

function processAddTaxon(){
    const taxonToAdd = nameSearchResults[0];
    const text = 'Adding ' + taxonToAdd['sciname'] + ' to the Taxonomic Thesaurus';
    addSubprocessToProcessorDisplay(currentSciname.value,'text',text);
    if(nameTidIndex.hasOwnProperty(taxonToAdd['sciname'])){
        processSubprocessSuccessResponse(currentSciname.value,false,nameSearchResults[0]['sciname'] + 'already added');
        updateOccurrenceLinkages();
    }
    else{
        const newTaxonObj = {};
        newTaxonObj['sciname'] = taxonToAdd['sciname'];
        newTaxonObj['author'] = taxonToAdd['author'];
        newTaxonObj['kingdomid'] = selectedKingdomId;
        newTaxonObj['rankid'] = taxonToAdd['rankid'];
        newTaxonObj['acceptstatus'] = taxonToAdd['accepted'] ? 1 : 0;
        newTaxonObj['tidaccepted'] = !taxonToAdd['accepted'] ? nameTidIndex[taxonToAdd['accepted_sciname']] : '';
        newTaxonObj['parenttid'] = nameTidIndex[taxonToAdd['parentName']];
        newTaxonObj['family'] = taxonToAdd['family'];
        newTaxonObj['source'] = getDataSourceName();
        newTaxonObj['source-name'] = dataSource.value;
        newTaxonObj['source-id'] = taxonToAdd['id'];
        const params = {
            taxon: JSON.stringify(newTaxonObj),
            action: 'addTaxon'
        };
        sendAPIPostRequest(taxonomyApiUrl,params,function(status,res){
            if(res && Number(res) > 0){
                const newTid = Number(res);
                nameTidIndex[nameSearchResults[0]['sciname']] = newTid;
                newTidArr.push(newTid);
                processSubprocessSuccessResponse(currentSciname.value,false,'Successfully added ' + nameSearchResults[0]['sciname']);
                if(currentSciname.value === nameSearchResults[0]['sciname']){
                    updateOccurrenceLinkages();
                }
                else{
                    const text = 'Updating occurrence records with cleaned scientific name';
                    addSubprocessToProcessorDisplay(currentSciname.value,'undo',text);
                    changedCurrentSciname = currentSciname.value;
                    changedParsedSciname = nameSearchResults[0]['sciname'];
                    const params = {
                        collid: collId,
                        sciname: currentSciname.value,
                        cleanedsciname: nameSearchResults[0]['sciname'],
                        tid: newTid,
                        action: 'updateOccWithCleanedName'
                    };
                    sendAPIPostRequest(occurrenceTaxonomyApiUrl,params,function(status,res){
                        if(status === 200) {
                            setSubprocessUndoNames(currentSciname.value,changedCurrentSciname,changedParsedSciname);
                            processSubprocessSuccessResponse(currentSciname.value,false,(res + ' records updated'));
                            updateOccurrenceLinkages();
                        }
                        else{
                            processSubprocessErrorResponse(currentSciname.value,true,'Error updating occurrence records');
                            updateOccurrenceLinkages();
                        }
                    });
                }
            }
            else{
                processSubprocessErrorResponse(currentSciname.value,false,'Error loading taxon');
                runScinameDataSourceSearch();
            }
        });
    }
}

function processCleaningControllerResponse(step,status,res,statusText){
    processUpdateCleanResponse('cleaned',status,res,statusText);
    if(step === 'question-marks'){
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
        callCleaningController('leading-trailing-spaces');
    }
    else if(step === 'leading-trailing-spaces'){
        adjustUIEnd();
    }
}

function processErrorResponse(setCounts,text){
    const procObj = processorDisplayArr.value.find(proc => proc['current'] === true);
    procObj['current'] = false;
    if(procObj['loading'] === true){
        procObj['loading'] = false;
        procObj['result'] = 'error';
        procObj['resultText'] = text;
    }
    if(setCounts){
        setUnlinkedRecordCounts();
    }
}

function processFuzzyMatches(fuzzyMatches){
    for(let i in fuzzyMatches){
        if(fuzzyMatches.hasOwnProperty(i)){
            const fuzzyMatchName = fuzzyMatches[i];
            const text = 'Match: ' + fuzzyMatchName;
            addSubprocessToProcessorDisplay(currentSciname.value,'fuzzy',text);
            setSubprocessUndoNames(currentSciname.value,currentSciname.value,fuzzyMatchName,i);
            processSubprocessSuccessResponse(currentSciname.value,false);
        }
    }
    const text = 'skip';
    addSubprocessToProcessorDisplay(currentSciname.value,'fuzzy',text);
    processSubprocessSuccessResponse(currentSciname.value,true);
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
            processErrorResponse(false,'Not found');
            runScinameDataSourceSearch();
        }
    }
    else{
        processErrorResponse(false,'Not found');
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
                if(taxResult['combinedName'] === currentSciname.value && taxResult['kingdom'].toLowerCase() === selectedKingdomName.toLowerCase()){
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
            processErrorResponse(false,'Not found');
            runScinameDataSourceSearch();
        }
        else if(itisInitialSearchResults.length > 1){
            validateITISInitialNameSearchResults();
        }
    }
    else{
        processErrorResponse(false,'Not found');
        runScinameDataSourceSearch();
    }
}

function processSubprocessErrorResponse(id,setCounts,text){
    const parentProcObj = processorDisplayArr.value.find(proc => proc['id'] === id);
    parentProcObj['current'] = false;
    const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
    subProcObj['loading'] = false;
    subProcObj['result'] = 'error';
    subProcObj['resultText'] = text;
    if(setCounts){
        setUnlinkedRecordCounts();
    }
}

function processSubprocessSuccessResponse(id,complete,text = null){
    const parentProcObj = processorDisplayArr.value.find(proc => proc['id'] === id);
    parentProcObj['current'] = !complete;
    const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
    subProcObj['loading'] = false;
    subProcObj['result'] = 'success';
    subProcObj['resultText'] = text;
}

function processSuccessResponse(complete,text = null){
    const procObj = processorDisplayArr.value.find(proc => proc['current'] === true);
    procObj['current'] = !complete;
    if(procObj['loading'] === true){
        procObj['loading'] = false;
        procObj['result'] = 'success';
        procObj['resultText'] = text;
    }
}

function processTaxThesaurusLinkControllerResponse(step,status,res,statusText){
    processUpdateCleanResponse('updated',status,res,statusText);
    if(!step && updatedet.value){
        callTaxThesaurusLinkController('update-det-linkages');
    }
    else{
        adjustUIEnd();
    }
}

function processUnlinkedNamesArr(inArr){
    if(Array.isArray(inArr) && inArr.length > 0){
        if(processingStartIndex.value){
            let nameArrLength = inArr.length;
            let startIndexVal = null;
            for(let i = 0 ; i < nameArrLength; i++) {
                if(inArr.hasOwnProperty(i) && inArr[i].toLowerCase() > processingStartIndex.value.toLowerCase()){
                    startIndexVal = i;
                    break;
                }
            }
            if(!startIndexVal){
                startIndexVal = nameArrLength;
            }
            inArr = inArr.splice(startIndexVal, (nameArrLength - startIndexVal));
        }
        if(processingLimit.value){
            inArr = inArr.splice(0, processingLimit.value);
        }
    }
    return inArr;
}

function processUpdateCleanResponse(term,status,res,statusText){
    if(status === 200) {
        processSuccessResponse(true,'Complete: ' + res + ' records ' + term);
    }
    else{
        const text = getErrorResponseText(status,statusText);
        processErrorResponse(true,text);
    }
}

function runCleanScinameAuthorProcess(){
    if(!processCancelled){
        if(unlinkedNamesArr.length > 0){
            currentSciname.value = unlinkedNamesArr[0];
            unlinkedNamesArr.splice(0, 1);
            const text = 'Attempting to parse author name from: ' + currentSciname.value;
            processorDisplayArr.value.push(getNewProcessObject(currentSciname.value,'multi',text));
            const params = {
                sciname: currentSciname.value,
                action: 'parseSciName'
            };
            sendAPIPostRequest(taxonomyApiUrl,params,function(status,res){
                if(status === 200) {
                    const parsedName = JSON.parse(res);
                    if(parsedName.hasOwnProperty('author') && parsedName['author'] !== ''){
                        processSuccessResponse(false,'Parsed author: ' + parsedName['author'] + '; Cleaned scientific name: ' + parsedName['sciname']);
                        const text = 'Updating occurrence records with cleaned scientific name';
                        addSubprocessToProcessorDisplay(currentSciname.value,'undo',text);
                        changedCurrentSciname = currentSciname.value;
                        changedParsedSciname = parsedName['sciname'];
                        const params = {
                            collid: collId,
                            sciname: currentSciname.value,
                            cleanedsciname: parsedName['sciname'],
                            tid: null,
                            action: 'updateOccWithCleanedName'
                        };
                        sendAPIPostRequest(occurrenceTaxonomyApiUrl,params,function(status,res){
                            if(status === 200) {
                                setSubprocessUndoNames(currentSciname.value,changedCurrentSciname,changedParsedSciname);
                                processSubprocessSuccessResponse(currentSciname.value,true,(res + ' records updated'));
                                runCleanScinameAuthorProcess();
                            }
                            else{
                                processSubprocessErrorResponse(currentSciname.value,false,'Error updating occurrence records');
                                runCleanScinameAuthorProcess();
                            }
                        });
                    }
                    else{
                        processErrorResponse(false,'No author found in scientific name');
                        runCleanScinameAuthorProcess();
                    }
                }
            });
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
            nameSearchResults = [];
            currentSciname.value = unlinkedNamesArr[0];
            unlinkedNamesArr.splice(0, 1);
            if(dataSource.value === 'col'){
                colInitialSearchResults = [];
                const text = 'Searching the Catalogue of Life (COL) for ' + currentSciname.value;
                processorDisplayArr.value.push(getNewProcessObject(currentSciname.value,'multi',text));
                const url = 'http://webservice.catalogueoflife.org/col/webservice?response=full&format=json&name=' + currentSciname.value;
                const params = {
                    url: url,
                    action: 'get'
                };
                sendAPIPostRequest(proxyApiUrl,params,function(status,res,statusText){
                    if(status === 200){
                        processGetCOLTaxonByScinameResponse(res);
                    }
                    else{
                        const text = getErrorResponseText(status,statusText);
                        processErrorResponse(false,text);
                        runScinameDataSourceSearch();
                    }
                });
            }
            else if(dataSource.value === 'itis'){
                itisInitialSearchResults = [];
                const text = 'Searching the Integrated Taxonomic Information System (ITIS) for ' + currentSciname.value;
                processorDisplayArr.value.push(getNewProcessObject(currentSciname.value,'multi',text));
                const url = 'https://www.itis.gov/ITISWebService/jsonservice/ITISService/searchByScientificName?srchKey=' + currentSciname.value;
                const params = {
                    url: url,
                    action: 'get'
                };
                sendAPIPostRequest(proxyApiUrl,params,function(status,res,statusText){
                    if(status === 200){
                        processGetITISTaxonByScinameResponse(res);
                    }
                    else{
                        const text = getErrorResponseText(status,statusText);
                        processErrorResponse(false,text);
                        runScinameDataSourceSearch();
                    }
                });
            }
            else if(dataSource.value === 'worms'){
                const text = 'Searching the World Register of Marine Species (WoRMS) for ' + currentSciname.value;
                processorDisplayArr.value.push(getNewProcessObject(currentSciname.value,'multi',text));
                const url = 'https://www.marinespecies.org/rest/AphiaIDByName/' + currentSciname.value + '?marine_only=false';
                const params = {
                    url: url,
                    action: 'get'
                };
                sendAPIPostRequest(proxyApiUrl,params,function(status,res,statusText){
                    if(status === 200 && res && Number(res) > 0){
                        getWoRMSNameSearchResultsRecord(res);
                    }
                    else if(status === 204 || !res || Number(res) <= 0){
                        processErrorResponse(false,'Not found');
                        runScinameDataSourceSearch();
                    }
                    else{
                        const text = getErrorResponseText(status,statusText);
                        processErrorResponse(false,text);
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
    changedCurrentSciname = '';
    changedParsedSciname = '';
    if(!processCancelled){
        if(unlinkedNamesArr.length > 0){
            currentSciname.value = unlinkedNamesArr[0];
            unlinkedNamesArr.splice(0, 1);
            const text = 'Finding fuzzy matches for ' + currentSciname.value;
            processorDisplayArr.value.push(getNewProcessObject(currentSciname.value,'multi',text));
            const params = {
                kingdomid: selectedKingdomId,
                sciname: currentSciname.value,
                lev: levValue.value,
                action: 'getSciNameFuzzyMatches'
            };
            sendAPIPostRequest(taxonomyApiUrl,params,function(status,res){
                if(status === 200) {
                    const fuzzyMatches = JSON.parse(res);
                    if(checkObjectNotEmpty(fuzzyMatches)){
                        processSuccessResponse(false);
                        processFuzzyMatches(fuzzyMatches);
                    }
                    else{
                        processErrorResponse(false,'No fuzzy matches found');
                        runTaxThesaurusFuzzyMatchProcess();
                    }
                }
            });
        }
        else{
            adjustUIEnd();
        }
    }
    else{
        adjustUIEnd();
    }
}

function selectFuzzyMatch(sciName,newName,newtid){
    changedCurrentSciname = sciName;
    changedParsedSciname = newName;
    clearSubprocesses(currentSciname.value);
    const text = 'Updating occurrence records with selected scientific name';
    addSubprocessToProcessorDisplay(currentSciname.value,'undo',text);
    const params = {
        collid: collId,
        sciname: sciName,
        cleanedsciname: newName,
        tid: newtid,
        action: 'updateOccWithCleanedName'
    };
    sendAPIPostRequest(occurrenceTaxonomyApiUrl,params,function(status,res){
        if(status === 200) {
            setSubprocessUndoNames(currentSciname.value,changedCurrentSciname,changedParsedSciname);
            processSubprocessSuccessResponse(currentSciname.value,true,(res + ' records updated'));
            runTaxThesaurusFuzzyMatchProcess();
        }
        else{
            processSubprocessErrorResponse(currentSciname.value,false,'Error updating occurrence records');
            runTaxThesaurusFuzzyMatchProcess();
        }
    });
}

function setSubprocessUndoNames(id,origName,newName,tid = null){
    const parentProcObj = processorDisplayArr.value.find(proc => proc['id'] === id);
    const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
    subProcObj['undoOrigName'] = origName.replaceAll("'",'%squot;').replaceAll('"','%dquot;');
    subProcObj['undoChangedName'] = newName.replaceAll("'",'%squot;').replaceAll('"','%dquot;');
    if(tid){
        subProcObj['changedTid'] = tid;
    }
}

function setTaxaToAdd(){
    if(processingArr.length > 0){
        const sciname = processingArr[0]['sciname'];
        if(!nameTidIndex.hasOwnProperty(sciname)){
            const url = CLIENT_ROOT + '/api/taxa/gettid.php';
            const params = {
                sciname: sciname,
                kingdomid: selectedKingdomId
            };
            sendAPIPostRequest(url,params,function(status,res){
                if(dataSource.value === 'worms' && !res){
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
        processSubprocessSuccessResponse(currentSciname.value,false);
        processAddTaxaArr();
    }
}

function setUnlinkedRecordCounts(){
    unlinkedOccCnt.value = null;
    unlinkedTaxaCnt.value = null;
    unlinkedLoading.value = true;
    const params = {
        collid: collId,
        action: 'getUnlinkedScinameCounts'
    };
    sendAPIPostRequest(occurrenceTaxonomyApiUrl,params,function(status,res){
        let retData = {};
        if(status === 200) {
            retData = JSON.parse(res);
        }
        if(retData.hasOwnProperty('occCnt')){
            unlinkedOccCnt.value = retData['occCnt'];
        }
        if(retData.hasOwnProperty('taxaCnt')){
            unlinkedTaxaCnt.value = retData['taxaCnt'];
        }
        unlinkedLoading.value = false;
    });
}

function setUnlinkedTaxaList(){
    if(!processCancelled){
        const text = 'Getting unlinked occurrence record scientific names';
        processorDisplayArr.value.push(getNewProcessObject('getUnlinkedOccSciNames','multi',text));
        const params = {
            collid: collId,
            action: 'getUnlinkedOccSciNames'
        };
        sendAPIPostRequest(occurrenceTaxonomyApiUrl,params,function(status,res,statusText){
            if(status === 200) {
                processSuccessResponse(true,'Complete');
                unlinkedNamesArr = processUnlinkedNamesArr(JSON.parse(res));
                runScinameDataSourceSearch();
            }
            else{
                const text = getErrorResponseText(status,statusText);
                processErrorResponse(true,text);
            }
        },http);
    }
}

function undoChangedSciname(id,oldName,newName){
    const parentProcObj = processorDisplayArr.value.find(proc => proc['id'] === id);
    const subProcObj = parentProcObj['subs'].find(subproc => subproc['undoChangedName'] === newName);
    subProcObj['type'] = 'text';
    const text = 'Reverting scientific name change from ' + oldName.replaceAll('%squot;',"'").replaceAll('%dquot;','"') + ' to ' + newName.replaceAll('%squot;',"'").replaceAll('%dquot;','"');
    addSubprocessToProcessorDisplay(id,'text',text);
    undoId = id;
    const params = {
        collid: collId,
        oldsciname: oldName,
        newsciname: newName,
        action: 'undoOccScinameChange'
    };
    sendAPIPostRequest(occurrenceTaxonomyApiUrl,params,function(status,res){
        if(status === 200) {
            processSubprocessSuccessResponse(undoId,true,(res + ' records reverted'));
        }
        else{
            processSubprocessErrorResponse(undoId,false,'Error undoing name change');
        }
    });
}

function updateOccLocalitySecurity(){
    adjustUIStart('updateOccLocalitySecurity');
    const text = 'Updating the locality security settings for occurrence records of protected species';
    processorDisplayArr.value.push(getNewProcessObject('updateLocalitySecurity','single',text));
    const params = {
        collid: collId,
        action: 'updateLocalitySecurity'
    };
    sendAPIPostRequest(occurrenceTaxonomyApiUrl,params,function(status,res,statusText){
        processUpdateCleanResponse('updated',status,res,statusText);
        adjustUIEnd();
    },http);
}

function updateOccurrenceLinkages(){
    const newSciname = nameSearchResults[0]['sciname'];
    const newScinameTid = nameTidIndex[nameSearchResults[0]['sciname']];
    const text = 'Updating linkages of occurrence records to ' + newSciname;
    addSubprocessToProcessorDisplay(currentSciname.value,'text',text);
    const params = {
        collid: collId,
        sciname: newSciname,
        tid: newScinameTid,
        kingdomid: selectedKingdomId,
        action: 'updateOccWithNewSciname'
    };
    sendAPIPostRequest(occurrenceTaxonomyApiUrl,params,function(status,res,statusText){
        if(status === 200) {
            processSubprocessSuccessResponse(currentSciname.value,true,res + ' records updated');
        }
        else{
            const text = getErrorResponseText(status,statusText);
            processSubprocessErrorResponse(currentSciname.value,true,text);
        }
        taxaLoaded++;
        if(taxaLoaded > 30){
            setUnlinkedRecordCounts();
            taxaLoaded = 0;
        }
        runScinameDataSourceSearch();
    });
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
        const params = {
            url: url,
            action: 'get'
        };
        sendAPIPostRequest(proxyApiUrl,params,function(status,res){
            if(status === 200){
                const resArr = JSON.parse(res);
                const kingdomObj = resArr.find(rettaxon => rettaxon['rank'].toLowerCase() === 'kingdom');
                if(kingdomObj && kingdomObj['name'].toLowerCase() === selectedKingdomName.toLowerCase()){
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
                                if(TAXONOMIC_RANKS.includes(rankid) || (!taxon['accepted'] && taxon['accepted_sciname'] === taxResult['name'])){
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
        processSuccessResponse(false);
        validateNameSearchResults();
    }
    else if(nameSearchResults.length === 0){
        processErrorResponse(false,'Not found');
        runScinameDataSourceSearch();
    }
    else if(nameSearchResults.length > 1){
        processErrorResponse(false,'Unable to distinguish taxon by name');
        runScinameDataSourceSearch();
    }
}

function validateITISInitialNameSearchResults(){
    if(itisInitialSearchResults.length > 0){
        const taxon = itisInitialSearchResults[0];
        itisInitialSearchResults.splice(0, 1);
        const id = taxon['id'];
        const url = 'https://www.itis.gov/ITISWebService/jsonservice/getFullRecordFromTSN?tsn=' + id;
        const params = {
            url: url,
            action: 'get'
        };
        sendAPIPostRequest(proxyApiUrl,params,function(status,res){
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
                processErrorResponse(false,'Unable to retrieve taxon record');
                runScinameDataSourceSearch();
            }
        });
    }
    else if(nameSearchResults.length === 1){
        getITISNameSearchResultsHierarchy();
    }
    else if(nameSearchResults.length === 0){
        processErrorResponse(false,'Not found');
        runScinameDataSourceSearch();
    }
    else if(nameSearchResults.length > 1){
        processErrorResponse(false,'Unable to distinguish taxon by name');
        runScinameDataSourceSearch();
    }
}

function validateNameSearchResults(){
    processingArr = [];
    taxaToAddArr = [];
    if(nameSearchResults.length === 1){
        if(!nameSearchResults[0]['accepted'] && !nameSearchResults[0]['accepted_sciname']){
            processErrorResponse(false,'Unable to distinguish accepted name');
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
            const text = 'Matching parent and accepted taxa to the Taxonomic Thesaurus';
            addSubprocessToProcessorDisplay(currentSciname.value,'text',text);
            setTaxaToAdd();
        }
    }
    else{
        processErrorResponse(false,'Unable to distinguish taxon by name');
        runScinameDataSourceSearch();
    }
}

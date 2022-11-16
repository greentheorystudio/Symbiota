const http = new XMLHttpRequest();
let processCancelled = false;

function addProgressLine(lineHtml){
    document.getElementById("progressDisplayList").innerHTML += lineHtml;
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
}

function callCleaningController(step){
    let params = '';
    if(step === 'leading-trailing-spaces'){
        processCancelled = false;
        adjustUIStart('cleanProcesses');
        addProgressLine('<li>Cleaning leading and trailing spaces in scientific names ' + processStatus + '</li>');
        params = 'collid=' + collId + '&action=cleanTrimNames';
    }
    if(processCancelled === false){
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
    const targetKingdom = document.getElementById('targetkingdomselect').value;
    if(targetKingdom){
        let params = '';
        if(!step){
            processCancelled = false;
            adjustUIStart('updateWithTaxThesaurus');
            addProgressLine('<li>Updating linkages of occurrence records to the Taxonomic Thesaurus ' + processStatus + '</li>');
            params = 'collid=' + collId + '&kingdomid=' + targetKingdom + '&action=updateOccThesaurusLinkages';
        }
        if(processCancelled === false){
            if(step === 'update-det-linkages'){
                addProgressLine('<li>Updating linkages of associated determination records to the Taxonomic Thesaurus ' + processStatus + '</li>');
                params = 'collid=' + collId + '&kingdomid=' + targetKingdom + '&action=updateDetThesaurusLinkages';
            }
            else if(step === 'update-image-linkages'){
                addProgressLine('<li>Updating linkages of associated media records to the Taxonomic Thesaurus ' + processStatus + '</li>');
                params = 'collid=' + collId + '&kingdomid=' + targetKingdom + '&action=updateMediaThesaurusLinkages';
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

function processErrorResponse(){
    const currentStatus = document.getElementsByClassName('current-status')[0];
    currentStatus.className = 'error-status';
    if(http.status === 0){
        currentStatus.innerHTML = 'Cancelled';
    }
    else{
        currentStatus.innerHTML = 'Error: ' + http.status + ' ' + http.statusText;
    }
    setUnlinkedRecordCounts();
}

function processSuccessResponse(lineHtml){
    const currentStatus = document.getElementsByClassName('current-status')[0];
    currentStatus.className = 'success-status';
    currentStatus.innerHTML = lineHtml;
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
        processSuccessResponse('Complete: ' + res + ' records ' + term);
    }
    else{
        processErrorResponse();
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

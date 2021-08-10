const SOLRFields = 'occid,collid,catalogNumber,otherCatalogNumbers,family,sciname,tidinterpreted,scientificNameAuthorship,identifiedBy,' +
    'dateIdentified,typeStatus,recordedBy,recordNumber,eventDate,displayDate,coll_year,coll_month,coll_day,habitat,associatedTaxa,' +
    'cultivationStatus,country,StateProvince,county,municipality,locality,localitySecurity,localitySecurityReason,geo,minimumElevationInMeters,' +
    'maximumElevationInMeters,labelProject,InstitutionCode,CollectionCode,CollectionName,CollType,thumbnailurl,accFamily';
let taxaArr = [];
let taxontype = '';
let thes = false;
let pageLoaded = true;

function getTimestringIdentifier(){
    return Date.now().toString();
}

function getDatestringIdentifier(){
    const day = new Date().getDate().toString();
    const month = new Date().getMonth()+1;
    const year = new Date().getFullYear().toString();
    return day + month + year;
}

function clearLocalStorageSearchTerms(){
    localStorage.removeItem('searchTermsArr');
}

function setLocalStorageSearchTerms(dateStr){
    const searchTermsArr = {};
    searchTermsArr[dateStr] = {};
    localStorage.setItem('searchTermsArr', JSON.stringify(searchTermsArr));
}

function initializeLocalStorage(){
    const dateStr = getDatestringIdentifier();
    if(localStorage.hasOwnProperty('searchTermsArr')){
        const stArr = JSON.parse(localStorage['searchTermsArr']);
        if(!stArr.hasOwnProperty(dateStr)){
            clearLocalStorageSearchTerms();
            setLocalStorageSearchTerms(dateStr);
        }
    }
    else{
        setLocalStorageSearchTerms(dateStr);
    }
}

function setQueryId(){
    const queryId = getTimestringIdentifier();
    setQueryIdInSearchTermsArr(queryId);
    if(document.getElementById('queryId')){
        document.getElementById('queryId').value = queryId;
    }
}

function setQueryIdInSearchTermsArr(queryId){
    const dateStr = getDatestringIdentifier();
    const stArr = JSON.parse(localStorage['searchTermsArr']);
    stArr[dateStr][queryId] = {};
    localStorage.setItem('searchTermsArr', JSON.stringify(stArr));
}

function initializeQueryId(queryId){
    const dateStr = getDatestringIdentifier();
    const stArr = JSON.parse(localStorage['searchTermsArr']);
    if(!queryId){
        setQueryId();
    }
    else if(!stArr[dateStr].hasOwnProperty(queryId)){
        setQueryIdInSearchTermsArr(queryId);
    }
}

function initializeSearchStorage(queryId){
    initializeLocalStorage();
    initializeQueryId(queryId);
}

function getSearchTermsArr(){
    const dateId = getDatestringIdentifier();
    const queryId = document.getElementById('queryId').value;
    const searchTermsArr = JSON.parse(localStorage['searchTermsArr']);
    return ((searchTermsArr.hasOwnProperty(dateId) && searchTermsArr[dateId].hasOwnProperty(queryId))?searchTermsArr[dateId][queryId]:{});
}

function loadSearchTermsArrFromJson(stArrJson){
    const dateId = getDatestringIdentifier();
    const queryId = document.getElementById('queryId').value;
    const searchTermsArr = JSON.parse(localStorage['searchTermsArr']);
    searchTermsArr[dateId][queryId] = JSON.parse(stArrJson);
    localStorage.setItem('searchTermsArr', JSON.stringify(searchTermsArr));
}

function getSearchTermsArrKeyValue(key){
    const queryArr = getSearchTermsArr();
    return (queryArr.hasOwnProperty(key)?queryArr[key]:undefined);
}

function clearSearchTermsArrKey(key){
    const dateId = getDatestringIdentifier();
    const queryId = document.getElementById('queryId').value;
    const searchTermsArr = JSON.parse(localStorage['searchTermsArr']);
    if(searchTermsArr[dateId][queryId].hasOwnProperty(key)){
        delete searchTermsArr[dateId][queryId][key];
    }
    localStorage.setItem('searchTermsArr', JSON.stringify(searchTermsArr));
}

function validateSearchTermsArr(stArr){
    let populated = false;
    if(stArr.hasOwnProperty('db') ||
        stArr.hasOwnProperty('clid') ||
        stArr.hasOwnProperty('taxa') ||
        stArr.hasOwnProperty('country') ||
        stArr.hasOwnProperty('state') ||
        stArr.hasOwnProperty('county') ||
        stArr.hasOwnProperty('local') ||
        stArr.hasOwnProperty('elevlow') ||
        stArr.hasOwnProperty('elevhigh') ||
        stArr.hasOwnProperty('collector') ||
        stArr.hasOwnProperty('collnum') ||
        stArr.hasOwnProperty('eventdate1') ||
        stArr.hasOwnProperty('eventdate2') ||
        stArr.hasOwnProperty('occurrenceRemarks') ||
        stArr.hasOwnProperty('catnum') ||
        stArr.hasOwnProperty('othercatnum') ||
        stArr.hasOwnProperty('typestatus') ||
        stArr.hasOwnProperty('hasimages') ||
        stArr.hasOwnProperty('hasgenetic') ||
        stArr.hasOwnProperty('upperlat') ||
        stArr.hasOwnProperty('pointlat') ||
        stArr.hasOwnProperty('circleArr') ||
        stArr.hasOwnProperty('polyArr')
    ){
        populated = true;
    }
    return populated;
}

function setSearchTermsArrKeyValue(key,value){
    const dateId = getDatestringIdentifier();
    const queryId = document.getElementById('queryId').value;
    const searchTermsArr = JSON.parse(localStorage['searchTermsArr']);
    searchTermsArr[dateId][queryId][key] = value;
    localStorage.setItem('searchTermsArr', JSON.stringify(searchTermsArr));
}

function copySearchUrl(){
    if(document.getElementById('urlFullBox')){
        const copyBox = document.getElementById('urlFullBox');
        copyBox.focus();
        copyBox.setSelectionRange(0,copyBox.value.length);
        document.execCommand("copy");
        copyBox.value = '';
    }
}

function processCollectionParamChange(f){
    if(pageLoaded){
        const dbElements = f.getElementsByTagName("input");
        let cl = false;
        let all = true;
        const collidArr = [];
        const clidArr = [];
        for(let i = 0; i < dbElements.length; i++){
            const dbElement = dbElements[i];
            if(dbElement.name === 'db[]' && !isNaN(dbElement.value)){
                if(dbElement.checked){
                    collidArr.push(dbElement.value);
                }
                else{
                    all = false;
                }
            }
            if(dbElement.name === 'clid[]' && !isNaN(dbElement.value) && dbElement.checked){
                clidArr.push(dbElement.value);
                cl = true;
            }
        }
        if(all === false && collidArr.length > 0){
            setSearchTermsArrKeyValue('db',collidArr.join(","));
        }
        else{
            clearSearchTermsArrKey('db');
        }
        if(clidArr.length > 0){
            setSearchTermsArrKeyValue('clid',clidArr.join(","));
        }
        else{
            clearSearchTermsArrKey('clid');
        }
    }
}

function processTaxaParamChange(){
    let taxaval = document.getElementById("taxa")?document.getElementById("taxa").value.trim():null;
    const taxontype = document.getElementById("taxontype")?document.getElementById("taxontype").value:null;
    const thes = document.getElementById("thes")?!!document.getElementById("thes").checked:null;
    taxaval = taxaval.replaceAll(",", ";");
    if(taxaval){
        setSearchTermsArrKeyValue('usethes',thes);
        setSearchTermsArrKeyValue('taxontype',taxontype);
        setSearchTermsArrKeyValue('taxa',taxaval);
    }
    else{
        clearSearchTermsArrKey('usethes');
        clearSearchTermsArrKey('taxontype');
        clearSearchTermsArrKey('taxa');
    }
}

function processTextParamChange(){
    let countryval = document.getElementById("country")?document.getElementById("country").value.trim():null;
    let stateval = document.getElementById("state")?document.getElementById("state").value.trim():null;
    let countyval = document.getElementById("county")?document.getElementById("county").value.trim():null;
    let localityval = document.getElementById("locality")?document.getElementById("locality").value.trim():null;
    let elevlowval = document.getElementById("elevlow")?document.getElementById("elevlow").value.trim():null;
    let elevhighval = document.getElementById("elevhigh")?document.getElementById("elevhigh").value.trim():null;
    let collectorval = document.getElementById("collector")?document.getElementById("collector").value.trim():null;
    let collnumval = document.getElementById("collnum")?document.getElementById("collnum").value.trim():null;
    let colldate1 = document.getElementById("eventdate1")?document.getElementById("eventdate1").value.trim():null;
    let colldate2 = document.getElementById("eventdate2")?document.getElementById("eventdate2").value.trim():null;
    let occurrenceremarksval = document.getElementById("occurrenceRemarks")?document.getElementById("occurrenceRemarks").value.trim():null;
    let catnumval = document.getElementById("catnum")?document.getElementById("catnum").value.trim():null;
    let othercatnumval = document.getElementById("othercatnum")?document.getElementById("othercatnum").checked:null;
    const typestatus = document.getElementById("typestatus")?document.getElementById("typestatus").checked:null;
    const hasimages = document.getElementById("hasimages")?document.getElementById("hasimages").checked:null;
    const hasgenetic = document.getElementById("hasgenetic")?document.getElementById("hasgenetic").checked:null;
    let imagedisplayval = document.getElementById("imagedisplay")?document.getElementById("imagedisplay").value:null;

    if(countryval){
        countryval = countryval.replaceAll(",", ";");
        if(countryval.indexOf('USA') !== -1 || countryval.indexOf('United States') !== -1 || countryval.indexOf('U.S.A.') !== -1 || countryval.indexOf('United States of America') !== -1){
            if(countryval.indexOf('USA') === -1){
                countryval += ';USA';
            }
            if(countryval.indexOf('United States') === -1){
                countryval += ';United States';
            }
            if(countryval.indexOf('U.S.A.') === -1){
                countryval += ';U.S.A.';
            }
            if(countryval.indexOf('United States of America') === -1){
                countryval += ';United States of America';
            }
        }
        setSearchTermsArrKeyValue('country',countryval);
    }
    else{
        clearSearchTermsArrKey('country');
    }
    if(stateval){
        stateval = stateval.replaceAll(",", ";");
        setSearchTermsArrKeyValue('state',stateval);
    }
    else{
        clearSearchTermsArrKey('state');
    }
    if(countyval){
        countyval = countyval.replaceAll(" Co.", "");
        countyval = countyval.replaceAll(" County", "");
        countyval = countyval.replaceAll(",", ";");
        setSearchTermsArrKeyValue('county',countyval);
    }
    else{
        clearSearchTermsArrKey('county');
    }
    if(localityval){
        localityval = localityval.replaceAll(",", ";");
        setSearchTermsArrKeyValue('local',localityval);
    }
    else{
        clearSearchTermsArrKey('local');
    }
    if(elevlowval){
        elevlowval = elevlowval.replaceAll(",", ";");
        setSearchTermsArrKeyValue('elevlow',elevlowval);
    }
    else{
        clearSearchTermsArrKey('elevlow');
    }
    if(elevhighval){
        elevhighval = elevhighval.replaceAll(",", ";");
        setSearchTermsArrKeyValue('elevhigh',elevhighval);
    }
    else{
        clearSearchTermsArrKey('elevhigh');
    }
    if(collectorval){
        collectorval = collectorval.replaceAll(",", ";");
        setSearchTermsArrKeyValue('collector',collectorval);
    }
    else{
        clearSearchTermsArrKey('collector');
    }
    if(collnumval){
        collnumval = collnumval.replaceAll(",", ";");
        setSearchTermsArrKeyValue('collnum',collnumval);
    }
    else{
        clearSearchTermsArrKey('collnum');
    }
    if(colldate1 || colldate2){
        setSearchTermsArrKeyValue('eventdate1',colldate1);
        if(colldate1 && colldate2){
            setSearchTermsArrKeyValue('eventdate2',colldate2);
        }
        else{
            clearSearchTermsArrKey('eventdate2');
        }
    }
    else{
        clearSearchTermsArrKey('eventdate1');
        clearSearchTermsArrKey('eventdate2');
    }
    if(occurrenceremarksval){
        occurrenceremarksval = occurrenceremarksval.replaceAll(",", ";");
        setSearchTermsArrKeyValue('occurrenceRemarks',occurrenceremarksval);
    }
    else{
        clearSearchTermsArrKey('occurrenceRemarks');
    }
    if(catnumval){
        catnumval = catnumval.replaceAll(",", ";");
        setSearchTermsArrKeyValue('catnum',catnumval);
    }
    else{
        clearSearchTermsArrKey('catnum');
    }
    if(othercatnumval){
        setSearchTermsArrKeyValue('othercatnum',true);
    }
    else{
        clearSearchTermsArrKey('othercatnum');
    }
    if(typestatus){
        setSearchTermsArrKeyValue('typestatus',true);
    }
    else{
        clearSearchTermsArrKey('typestatus');
    }
    if(hasimages){
        setSearchTermsArrKeyValue('hasimages',true);
    }
    else{
        clearSearchTermsArrKey('hasimages');
    }
    if(hasgenetic){
        setSearchTermsArrKeyValue('hasgenetic',true);
    }
    else{
        clearSearchTermsArrKey('hasgenetic');
    }
    if(imagedisplayval){
        setSearchTermsArrKeyValue('imagedisplay',imagedisplayval);
    }
    else{
        clearSearchTermsArrKey('imagedisplay');
    }
}

function redirectWithQueryId(url){
    const queryId = document.getElementById('queryId').value;
    window.location.href = url + '?queryId=' + queryId;
}

function processDownloadRequest(selection,rows){
    const searchTermsArr = getSearchTermsArr();
    document.getElementById("dh-fl").value = '';
    document.getElementById("dh-type").value = '';
    document.getElementById("dh-filename").value = '';
    document.getElementById("dh-contentType").value = '';
    document.getElementById("dh-selections").value = '';
    document.getElementById("dh-rows").value = rows;
    document.getElementById("starrjson").value = JSON.stringify(searchTermsArr);
    const dlType = (selection ? document.getElementById("selectdownloadselect").value : document.getElementById("querydownloadselect").value);
    if(dlType){
        const filename = 'spatialdata_' + getDateTimeString();
        let contentType = '';
        if(dlType === 'kml') {
            contentType = 'application/vnd.google-earth.kml+xml';
        }
        else if(dlType === 'geojson') {
            contentType = 'application/vnd.geo+json';
        }
        else if(dlType === 'gpx') {
            contentType = 'application/gpx+xml';
        }
        document.getElementById("dh-type").value = dlType;
        document.getElementById("dh-filename").value = filename;
        document.getElementById("dh-contentType").value = contentType;
        if(selection) {
            document.getElementById("dh-selections").value = selections.join();
        }
        if(!selection && dlType === 'csv'){
            document.getElementById("dh-fl").value = 'occid';
        }
        else{
            document.getElementById("dh-fl").value = SOLRFields;
        }
        if(dlType === 'csv'){
            if(rows > 150000){
                toggleDownloadCompressionOption();
            }
            $("#csvoptions").popup("show");
        }
        else if(dlType === 'kml' || dlType === 'geojson' || dlType === 'gpx'){
            document.getElementById("datadownloadform").submit();
        }
        else if(dlType === 'png'){
            const imagefilename = 'map_' + getDateTimeString() + '.png';
            exportMapPNG(imagefilename,false);
        }
    }
    else{
        alert('Please select a download type.')
    }
}

function toggleDownloadCompressionOption(){
    if(document.getElementById("zipSelectionRow")){
        document.getElementById("zipSelectionRow").style.display = "none";
    }
}

function processDownloadChecklist(){
    const searchTermsArr = getSearchTermsArr();
    const filename = 'taxalist_' + getDateTimeString();
    document.getElementById("dh-fl").value = '';
    document.getElementById("dh-type").value = 'csv';
    document.getElementById("schemacsv").value = 'checklist';
    document.getElementById("starrjson").value = JSON.stringify(searchTermsArr);
    document.getElementById("dh-filename").value = filename;
    document.getElementById("csetcsv").value = 'utf-8';
    document.getElementById("dh-contentType").value = 'text/csv; charset=utf-8';
    document.getElementById("datadownloadform").submit();
}

function getDateTimeString(){
    const now = new Date();
    let dateTimeString = now.getFullYear().toString();
    dateTimeString += (((now.getMonth()+1) < 10)?'0':'')+(now.getMonth()+1).toString();
    dateTimeString += ((now.getDate() < 10)?'0':'')+now.getDate().toString();
    dateTimeString += ((now.getHours() < 10)?'0':'')+now.getHours().toString();
    dateTimeString += ((now.getMinutes() < 10)?'0':'')+now.getMinutes().toString();
    dateTimeString += ((now.getSeconds() < 10)?'0':'')+now.getSeconds().toString();
    return dateTimeString;
}

function prepCsvControlForm(){
    let cset;
    let format;
    let schema;
    if (document.getElementById('csvschemasymb').checked) {
        schema = document.getElementById('csvschemasymb').value;
    }
    if (document.getElementById('csvschemadwc').checked) {
        schema = document.getElementById('csvschemadwc').value;
    }
    if (document.getElementById('csvformatcsv').checked) {
        format = document.getElementById('csvformatcsv').value;
    }
    if (document.getElementById('csvformattab').checked) {
        format = document.getElementById('csvformattab').value;
    }
    if (document.getElementById('csvcsetiso').checked) {
        cset = document.getElementById('csvcsetiso').value;
    }
    if (document.getElementById('csvcsetutf').checked) {
        cset = document.getElementById('csvcsetutf').value;
    }
    document.getElementById("schemacsv").value = schema;
    document.getElementById("dh-filename").value = document.getElementById("dh-filename").value+'.'+schema;
    if(document.getElementById("csvidentifications").checked === true){
        document.getElementById("identificationscsv").value = 1;
    }
    if(document.getElementById("csvimages").checked === true){
        document.getElementById("imagescsv").value = 1;
    }
    document.getElementById("formatcsv").value = format;
    document.getElementById("csetcsv").value = cset;
    if(document.getElementById("csvzip").checked === true){
        document.getElementById("zipcsv").value = 1;
        document.getElementById("dh-type").value = 'zip';
        document.getElementById("dh-contentType").value = 'application/zip';
    }
    else{
        document.getElementById("zipcsv").value = false;
        document.getElementById("dh-type").value = 'csv';
        document.getElementById("dh-contentType").value = 'text/csv; charset='+cset;
    }
    $("#csvoptions").popup("hide");
    document.getElementById("datadownloadform").submit();
}

function extensionSelected(obj){
    if(obj.checked === true){
        document.getElementById('csvzip').checked = true;
    }
}

function zipSelected(obj){
    if(obj.checked === false){
        document.getElementById("csvimages").checked = false;
        document.getElementById("csvidentifications").checked = false;
    }
}

function selectAllPid(cb){
    let boxesChecked = cb.checked;
    const target = "pid-" + cb.value;
    const inputObjs = document.getElementsByTagName("input");
    for (let i = 0; i < inputObjs.length; i++) {
        const inputObj = inputObjs[i];
        if(inputObj.getAttribute("class") == target || inputObj.getAttribute("className") == target){
            inputObj.checked = boxesChecked;
        }
    }
    processCheckAllCheckboxes();
}

function selectAll(cb){
    let boxesChecked = cb.checked;
    const f = cb.form;
    for(let i=0; i<f.length; i++){
        if(f.elements[i].name === "db[]" || f.elements[i].name === "cat[]" || f.elements[i].name === "occid[]"){
            f.elements[i].checked = boxesChecked;
        }
        if(f.elements[i].name === "occid[]"){
            f.elements[i].onchange();
        }
    }
}

function processCheckAllCheckboxes(){
    if(document.getElementById('collform1')){
        const collForm1 = document.getElementById('collform1');
        const collForm1Elements = collForm1.getElementsByTagName("input");
        let collForm1AllSelected = true;
        for(let i = 0; i < collForm1Elements.length; i++){
            const dbElement = collForm1Elements[i];
            if(dbElement.name === 'db[]' && !dbElement.checked && !isNaN(dbElement.value)){
                collForm1AllSelected = false;
            }
        }
        document.getElementById('dballcb').checked = collForm1AllSelected;
    }
    if(document.getElementById('collform2')){
        const collForm2 = document.getElementById('collform2');
        const collForm2Elements = collForm2.getElementsByTagName("input");
        let collForm2AllSelected = true;
        for(let i = 0; i < collForm2Elements.length; i++){
            const dbElement = collForm2Elements[i];
            if(dbElement.name === 'db[]' && !dbElement.checked && !isNaN(dbElement.value)){
                collForm2AllSelected = false;
            }
        }
        document.getElementById('dballspeccb').checked = collForm2AllSelected;
    }
    if(document.getElementById('collform3')){
        const collForm3 = document.getElementById('collform3');
        const collForm3Elements = collForm3.getElementsByTagName("input");
        let collForm3AllSelected = true;
        for(let i = 0; i < collForm3Elements.length; i++){
            const dbElement = collForm3Elements[i];
            if(dbElement.name === 'db[]' && !dbElement.checked && !isNaN(dbElement.value)){
                collForm3AllSelected = false;
            }
        }
        document.getElementById('dballobscb').checked = collForm3AllSelected;
    }
    if(document.getElementById('spatialcollsearchform')){
        const collForm = document.getElementById('spatialcollsearchform');
        const collFormElements = collForm.getElementsByTagName("input");
        let collFormAllSelected = true;
        for(let i = 0; i < collFormElements.length; i++){
            const dbElement = collFormElements[i];
            if(dbElement.name === 'db[]' && !dbElement.checked && !isNaN(dbElement.value)){
                collFormAllSelected = false;
            }
        }
        document.getElementById('dballcb').checked = collFormAllSelected;
    }
}

function processCatCheckboxes(catId){
    let allChecked = true;
    const catDivId = 'cat-' + catId;
    const catExpandAllId = 'cat-' + catId + '-Input';
    const catDiv = document.getElementById(catDivId);
    const childElements = catDiv.getElementsByTagName("input");
    for(let i = 0; i < childElements.length; i++){
        const dbElement = childElements[i];
        if(dbElement.name === 'db[]' && !dbElement.checked && !isNaN(dbElement.value)){
            allChecked = false;
        }
    }
    document.getElementById(catExpandAllId).checked = allChecked;
    processCheckAllCheckboxes();
}

function processProjCheckboxes(proId){
    let allChecked = true;
    const projDivId = 'pid-' + proId;
    const projExpandAllId = 'pid-' + proId + '-Input';
    const projDiv = document.getElementById(projDivId);
    const childElements = projDiv.getElementsByTagName("input");
    for(let i = 0; i < childElements.length; i++){
        const dbElement = childElements[i];
        if(dbElement.name === 'clid[]' && !dbElement.checked && !isNaN(dbElement.value)){
            allChecked = false;
        }
    }
    document.getElementById(projExpandAllId).checked = allChecked;
}

function togglePid(pid){
    toggle("minus-pid-"+pid);
    toggle("plus-pid-"+pid);
    toggle("pid-"+pid);
}

function toggleCat(catid){
    toggle("minus-"+catid);
    toggle("plus-"+catid);
    toggle("cat-"+catid);
}

function selectAllCat(cb,target){
    let boxesChecked = cb.checked;
    const inputObjs = document.getElementsByTagName("input");
    for (let i = 0; i < inputObjs.length; i++) {
        const inputObj = inputObjs[i];
        if(inputObj.getAttribute("class") === target || inputObj.getAttribute("className") === target){
            inputObj.checked = boxesChecked;
        }
    }
    processCheckAllCheckboxes();
}

function setCollectionForms(){
    pageLoaded = false;
    const stArr = getSearchTermsArr();
    if(stArr['db'] || stArr['clid']){
        const dbArr = (stArr['db']?stArr['db'].split(','):[]);
        const clidArr = (stArr['clid']?stArr['clid'].split(','):[]);
        const inputObjs = document.getElementsByTagName("input");
        for(let i = 0; i < inputObjs.length; i++){
            const dbElement = inputObjs[i];
            if(stArr['db'] && dbElement.name === 'db[]' && !isNaN(dbElement.value)){
                dbElement.checked = !dbArr.includes(dbElement.value);
                dbElement.click();
            }
            if(stArr['clid'] && dbElement.name === 'clid[]' && !isNaN(dbElement.value)){
                dbElement.checked = !clidArr.includes(dbElement.value);
                dbElement.click();
            }
        }
        processCheckAllCheckboxes();
    }
    pageLoaded = true;
}

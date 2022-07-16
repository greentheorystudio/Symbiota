let collSymbology = [];
let taxaSymbology = [];
let collKeyArr = [];
let taxaKeyArr = [];
let taxaCnt = 0;
let mapSymbology = 'coll';
let clusterKey = 'CollectionName';
let zipFile = '';
let zipFolder = '';

function addQueryToDataset(){
    document.getElementById("selectedtargetdatasetid").value = document.getElementById("targetdatasetid").value;
    document.getElementById("dsstarrjson").value = JSON.stringify(searchTermsArr);
    document.getElementById("datasetformaction").value = 'addAllToDataset';
    document.getElementById("datasetform").submit();
}

function addSelectionsToDataset(){
    document.getElementById("selectedtargetdatasetid").value = document.getElementById("targetdatasetid").value;
    document.getElementById("occarrjson").value = JSON.stringify(selections);
    document.getElementById("datasetformaction").value = 'addSelectedToDataset';
    document.getElementById("datasetform").submit();
}

function autoColorColl(){
    document.getElementById("randomColorColl").disabled = true;
    changeMapSymbology('coll');
    const usedColors = [];
    for(let i in collSymbology){
        if(collSymbology.hasOwnProperty(i)){
            let randColor = generateRandColor();
            while (usedColors.indexOf(randColor) > -1) {
                randColor = generateRandColor();
            }
            usedColors.push(randColor);
            changeCollColor(randColor,i);
            const keyName = 'keyColor' + i;
            document.getElementById(keyName).color.fromString(randColor);
        }
    }
    document.getElementById("randomColorColl").disabled = false;
}

function autoColorTaxa(){
    document.getElementById("randomColorTaxa").disabled = true;
    changeMapSymbology('taxa');
    const usedColors = [];
    for(let i in taxaSymbology){
        if(taxaSymbology.hasOwnProperty(i)){
            let randColor = generateRandColor();
            while (usedColors.indexOf(randColor) > -1) {
                randColor = generateRandColor();
            }
            usedColors.push(randColor);
            changeTaxaColor(randColor,i);
            const keyName = 'taxaColor' + i;
            if(document.getElementById(keyName)){
                document.getElementById(keyName).color.fromString(randColor);
            }
        }
    }
    document.getElementById("randomColorTaxa").disabled = false;
}

function buildCollKey(){
    for(let i in collSymbology){
        if(collSymbology.hasOwnProperty(i)){
            buildCollKeyPiece(i);
        }
    }
    keyHTML = '';
    const sortedKeys = arrayIndexSort(collKeyArr).sort();
    for(let i in sortedKeys) {
        if(sortedKeys.hasOwnProperty(i)){
            keyHTML += collKeyArr[sortedKeys[i]];
        }
    }
    document.getElementById("symbologykeysbox").innerHTML = keyHTML;
    jscolor.init();
}

function buildCollKeyPiece(key){
    keyHTML = '';
    keyLabel = "'"+key+"'";
    const color = collSymbology[key]['color'];
    keyHTML += '<div style="display:table-row;">';
    keyHTML += '<div style="display:table-cell;vertical-align:middle;padding-bottom:5px;" ><input data-role="none" id="keyColor'+key+'" class="color" style="cursor:pointer;border:1px black solid;height:12px;width:12px;margin-bottom:-2px;font-size:0;" value="'+color+'" onchange="changeCollColor(this.value,'+keyLabel+');" /></div>';
    keyHTML += '<div style="display:table-cell;vertical-align:middle;padding-left:8px;"> = </div>';
    keyHTML += '<div style="display:table-cell;width:250px;vertical-align:middle;padding-left:8px;">'+key+'</div>';
    keyHTML += '</div>';
    keyHTML += '<div style="display:table-row;height:8px;"></div>';
    collKeyArr[key] = keyHTML;
}

function buildTaxaKey(){
    document.getElementById("taxaCountNum").innerHTML = taxaCnt;
    for(let i in taxaSymbology){
        if(taxaSymbology.hasOwnProperty(i)){
            const family = taxaSymbology[i]['family'];
            const tidinterpreted = taxaSymbology[i]['tidinterpreted'];
            const sciname = taxaSymbology[i]['sciname'];
            buildTaxaKeyPiece(i,family,tidinterpreted,sciname);
        }
    }
    keyHTML = '';
    let famUndefinedArr = [];
    if(taxaKeyArr['undefined']){
        famUndefinedArr = taxaKeyArr['undefined'];
        const undIndex = taxaKeyArr.indexOf('undefined');
        taxaKeyArr.splice(undIndex,1);
    }
    const fsortedKeys = arrayIndexSort(taxaKeyArr).sort();
    for(let f in fsortedKeys){
        if(fsortedKeys.hasOwnProperty(f)){
            let scinameArr = [];
            scinameArr = taxaKeyArr[fsortedKeys[f]];
            const ssortedKeys = arrayIndexSort(scinameArr).sort();
            keyHTML += "<div style='margin-left:5px;'><h3 style='margin-top:8px;margin-bottom:5px;'>"+fsortedKeys[f]+"</h3></div>";
            keyHTML += "<div style='display:table;'>";
            for(let s in ssortedKeys){
                if(ssortedKeys.hasOwnProperty(s)){
                    keyHTML += taxaKeyArr[fsortedKeys[f]][ssortedKeys[s]];
                }
            }
            keyHTML += "</div>";
        }
    }
    if(famUndefinedArr.length > 0){
        const usortedKeys = arrayIndexSort(famUndefinedArr).sort();
        keyHTML += "<div style='margin-left:5px;'><h3 style='margin-top:8px;margin-bottom:5px;'>Family Not Defined</h3></div>";
        keyHTML += "<div style='display:table;'>";
        for(let u in usortedKeys){
            if(usortedKeys.hasOwnProperty(u)){
                keyHTML += taxaKeyArr[usortedKeys[u]];
            }
        }
    }
    document.getElementById("taxasymbologykeysbox").innerHTML = keyHTML;
    jscolor.init();
}

function buildTaxaKeyPiece(key,family,tidinterpreted,sciname){
    let keyHTML = '';
    let keyLabel = "'" + key + "'";
    const color = taxaSymbology[key]['color'];
    keyHTML += '<div id="'+key+'keyrow">';
    keyHTML += '<div style="display:table-row;">';
    keyHTML += '<div style="display:table-cell;vertical-align:middle;padding-bottom:5px;" ><input data-role="none" id="taxaColor'+key+'" class="color" style="cursor:pointer;border:1px black solid;height:12px;width:12px;margin-bottom:-2px;font-size:0;" value="'+color+'" onchange="changeTaxaColor(this.value,'+keyLabel+');" /></div>';
    keyHTML += '<div style="display:table-cell;vertical-align:middle;padding-left:8px;"> = </div>';
    if(!tidinterpreted){
        keyHTML += "<div style='display:table-cell;vertical-align:middle;padding-left:8px;'><i>"+sciname+"</i></div>";
    }
    else{
        keyHTML += "<div style='display:table-cell;vertical-align:middle;padding-left:8px;'><i><a target='_blank' href='../taxa/index.php?taxon="+sciname+"'>"+sciname+"</a></i></div>";
    }
    keyHTML += '</div></div>';
    if(!taxaKeyArr[family]){
        taxaKeyArr[family] = [];
    }
    taxaKeyArr[family][key] = keyHTML;
}

function changeCollColor(color,key){
    changeMapSymbology('coll');
    collSymbology[key]['color'] = color;
    layersObj['pointv'].getSource().changed();
    if(spiderCluster){
        const spiderFeatures = layersObj['spider'].getSource().getFeatures();
        for(let f in spiderFeatures){
            if(spiderFeatures.hasOwnProperty(f)){
                const style = (spiderFeatures[f].get('features') ? setClusterSymbol(spiderFeatures[f]) : setSymbol(spiderFeatures[f]));
                spiderFeatures[f].setStyle(style);
            }
        }
    }
}

function changeMapSymbology(symbology){
    if(symbology !== mapSymbology){
        if(spiderCluster){
            const source = layersObj['spider'].getSource();
            source.clear();
            const blankSource = new ol.source.Vector({
                features: new ol.Collection(),
                useSpatialIndex: true
            });
            layersObj['spider'].setSource(blankSource);
            for(let i in hiddenClusters){
                if(hiddenClusters.hasOwnProperty(i)){
                    showFeature(hiddenClusters[i]);
                }
            }
            hiddenClusters = [];
            spiderCluster = '';
            layersObj['pointv'].getSource().changed();
        }
    }
    if(symbology === 'coll'){
        if(mapSymbology === 'taxa'){
            clearTaxaSymbology();
            clusterKey = 'CollectionName';
            mapSymbology = 'coll';
            if(clusterPoints){
                loadPointsLayer(0);
            }
        }
    }
    if(symbology === 'taxa'){
        if(mapSymbology === 'coll'){
            resetMainSymbology();
            clusterKey = 'namestring';
            mapSymbology = 'taxa';
            if(clusterPoints){
                loadPointsLayer(0);
            }
        }
    }
}

function changeRecordPage(page){
    let params;
    document.getElementById("queryrecords").innerHTML = "<p>Loading...</p>";
    const selJson = JSON.stringify(selections);
    const http = new XMLHttpRequest();
    const url = "rpc/changemaprecordpage.php";
    const jsonStarr = encodeURIComponent(JSON.stringify(searchTermsArr));
    if(SOLRMODE){
        params = 'starr=' + jsonStarr + '&rows='+queryRecCnt+'&page='+page+'&selected='+selJson;
    }
    else{
        params = 'starr='+jsonStarr+'&rows='+queryRecCnt+'&page='+page+'&selected='+selJson;
    }
    //console.log(url+'?'+params);
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function() {
        if(http.readyState === 4 && http.status === 200) {
            document.getElementById("queryrecords").innerHTML = http.responseText;
        }
    };
    http.send(params);
}

function changeTaxaColor(color,tidcode){
    changeMapSymbology('taxa');
    taxaSymbology[tidcode]['color'] = color;
    layersObj['pointv'].getSource().changed();
}

function clearTaxaSymbology(){
    for(let i in taxaSymbology){
        if(taxaSymbology.hasOwnProperty(i)){
            taxaSymbology[i]['color'] = pointLayerFillColor;
            const keyName = 'taxaColor' + i;
            if(document.getElementById(keyName)){
                document.getElementById(keyName).color.fromString(pointLayerFillColor);
            }
        }
    }
}

function exportTaxaCSV(){
    let csvContent = '';
    csvContent = '"ScientificName","Family","RecordCount"'+"\n";
    const sortedTaxa = arrayIndexSort(taxaSymbology).sort();
    for(let i in sortedTaxa){
        if(sortedTaxa.hasOwnProperty(i)){
            let family = taxaSymbology[sortedTaxa[i]]['family'].toLowerCase();
            family = family.charAt(0).toUpperCase()+family.slice(1);
            const row = taxaSymbology[sortedTaxa[i]]['sciname'] + ',' + family + ',' + taxaSymbology[sortedTaxa[i]]['count'] + "\n";
            csvContent += row;
        }
    }
    const filename = 'taxa_list.csv';
    const filetype = 'text/csv; charset=utf-8';
    const blob = new Blob([csvContent], {type: filetype});
    if(window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveBlob(blob,filename);
    }
    else{
        const elem = window.document.createElement('a');
        elem.href = window.URL.createObjectURL(blob);
        elem.download = filename;
        document.body.appendChild(elem);
        elem.click();
        document.body.removeChild(elem);
    }
}

function getPointFeatureInfoHtml(iFeature){
    let infoHTML = '';
    infoHTML += '<b>occid:</b> '+iFeature.get('id')+'<br />';
    infoHTML += '<b>CollectionName:</b> '+(iFeature.get('CollectionName')?iFeature.get('CollectionName'):'')+'<br />';
    infoHTML += '<b>catalogNumber:</b> '+(iFeature.get('catalogNumber')?iFeature.get('catalogNumber'):'')+'<br />';
    infoHTML += '<b>otherCatalogNumbers:</b> '+(iFeature.get('otherCatalogNumbers')?iFeature.get('otherCatalogNumbers'):'')+'<br />';
    infoHTML += '<b>family:</b> '+(iFeature.get('family')?iFeature.get('family'):'')+'<br />';
    infoHTML += '<b>sciname:</b> '+(iFeature.get('sciname')?iFeature.get('sciname'):'')+'<br />';
    infoHTML += '<b>recordedBy:</b> '+(iFeature.get('recordedBy')?iFeature.get('recordedBy'):'')+'<br />';
    infoHTML += '<b>recordNumber:</b> '+(iFeature.get('recordNumber')?iFeature.get('recordNumber'):'')+'<br />';
    infoHTML += '<b>eventDate:</b> '+(iFeature.get('displayDate')?iFeature.get('displayDate'):'')+'<br />';
    infoHTML += '<b>habitat:</b> '+(iFeature.get('habitat')?iFeature.get('habitat'):'')+'<br />';
    infoHTML += '<b>associatedTaxa:</b> '+(iFeature.get('associatedTaxa')?iFeature.get('associatedTaxa'):'')+'<br />';
    infoHTML += '<b>country:</b> '+(iFeature.get('country')?iFeature.get('country'):'')+'<br />';
    infoHTML += '<b>StateProvince:</b> '+(iFeature.get('StateProvince')?iFeature.get('StateProvince'):'')+'<br />';
    infoHTML += '<b>county:</b> '+(iFeature.get('county')?iFeature.get('county'):'')+'<br />';
    infoHTML += '<b>locality:</b> '+(iFeature.get('locality')?iFeature.get('locality'):'')+'<br />';
    if(iFeature.get('thumbnailurl')){
        const thumburl = iFeature.get('thumbnailurl');
        infoHTML += '<img src="'+thumburl+'" style="height:150px" />';
    }
    return infoHTML;
}

function getPointInfoArr(cluster){
    const feature = (cluster.get('features') ? cluster.get('features')[0] : cluster);
    const infoArr = [];
    infoArr['occid'] = Number(feature.get('id'));
    infoArr['institutioncode'] = (feature.get('InstitutionCode')?feature.get('InstitutionCode'):'');
    infoArr['catalognumber'] = (feature.get('catalogNumber')?feature.get('catalogNumber'):'');
    const recordedby = (feature.get('recordedBy') ? feature.get('recordedBy') : '');
    const recordnumber = (feature.get('recordNumber') ? feature.get('recordNumber') : '');
    infoArr['collector'] = (recordedby?recordedby:'')+(recordedby&&recordnumber?' ':'')+(recordnumber?recordnumber:'');
    infoArr['eventdate'] = (feature.get('displayDate')?feature.get('displayDate'):'');
    infoArr['sciname'] = (feature.get('sciname')?feature.get('sciname'):'');
    //var country = (feature.get('country')?feature.get('country'):'');
    //var stateProvince = (feature.get('StateProvince')?feature.get('StateProvince'):'');
    //var county = (feature.get('county')?feature.get('county'):'');
    //infoArr['locality'] = (country?country:'')+(country&&stateProvince?'; ':'')+(stateProvince?stateProvince:'')+(country||stateProvince?'; ':'')+(county?county:'');

    return infoArr;
}

function getQueryRecCnt(callback){
    let params;
    let url;
    let http;
    queryRecCnt = 0;
    const jsonStarr = encodeURIComponent(JSON.stringify(searchTermsArr));
    if(SOLRMODE){
        let qStr = '';
        http = new XMLHttpRequest();
        url = "rpc/SOLRConnector.php";
        params = 'starr=' + jsonStarr + '&rows=0&start=0&wt=json';
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                const resArr = JSON.parse(http.responseText);
                queryRecCnt = resArr['response']['numFound'];
                document.getElementById("dh-rows").value = queryRecCnt;
                callback(1);
            }
        };
        http.send(params);
    }
    else{
        http = new XMLHttpRequest();
        url = "rpc/MYSQLConnector.php";
        params = 'starr=' + jsonStarr + '&rows=0&start=0&type=reccnt';
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                queryRecCnt = http.responseText;
                document.getElementById("dh-rows").value = queryRecCnt;
                callback(1);
            }
        };
        http.send(params);
    }
}

function lazyLoadPoints(index,callback){
    let params;
    let url;
    let startindex = 0;
    loadingComplete = true;
    if(index > 0) {
        startindex = index * lazyLoadCnt;
    }
    const http = new XMLHttpRequest();
    const jsonStarr = encodeURIComponent(JSON.stringify(searchTermsArr));
    if(SOLRMODE){
        url = "rpc/SOLRConnector.php";
        params = 'starr=' + jsonStarr + '&rows='+lazyLoadCnt+'&start='+startindex+'&fl='+SOLRFields+'&wt=geojson';
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                loadingComplete = false;
                callback(http.responseText);
            }
        };
        http.send(params);
    }
    else{
        url = "rpc/MYSQLConnector.php";
        params = 'starr=' + jsonStarr + '&rows=' + lazyLoadCnt + '&start=' + startindex + '&type=geoquery';
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                loadingComplete = false;
                callback(http.responseText);
            }
        };
        http.send(params);
    }
}

function loadPoints(){
    searchTermsArr = getSearchTermsArr();
    if(validateSearchTermsArr(searchTermsArr)){
        taxaCnt = 0;
        collSymbology = [];
        taxaSymbology = [];
        selections = [];
        showWorking();
        pointvectorsource.clear(true);
        layersObj['pointv'].setSource(pointvectorsource);
        getQueryRecCnt(function() {
            if(queryRecCnt > 0){
                loadPointsEvent = true;
                setCopySearchUrlDiv();
                loadPointsLayer(0);
                //cleanSelectionsLayer();
                setRecordsTab();
                changeRecordPage(1);
                $('#recordstab').tabs({active: 1});
                $("#sidepanel-accordion").accordion("option","active",1);
                //selectInteraction.getFeatures().clear();
                if(!pointActive){
                    const infoArr = [];
                    infoArr['id'] = 'pointv';
                    infoArr['type'] = 'userLayer';
                    infoArr['fileType'] = 'vector';
                    infoArr['layerName'] = 'Points';
                    infoArr['layerDescription'] = "This layer contains all of the occurrence points that have been loaded onto the map.",
                        infoArr['removable'] = true;
                    infoArr['sortable'] = false;
                    infoArr['symbology'] = false;
                    infoArr['query'] = false;
                    processAddLayerControllerElement(infoArr,document.getElementById("coreLayers"),true);
                    pointActive = true;
                }
            }
            else{
                setRecordsTab();
                if(pointActive){
                    removeLayerToSelList('pointv');
                    pointActive = false;
                }
                loadPointsEvent = false;
                hideWorking();
                alert('There were no records matching your query.');
            }
        });
    }
    else{
        alert('Please enter search criteria.');
    }
}

function openIndPopup(occid){
    openPopup('../collections/individual/index.php?occid=' + occid);
}

function primeSymbologyData(features){
    for(let f in features) {
        if(features.hasOwnProperty(f)){
            const collName = features[f].get('CollectionName');
            const collid = features[f].get('collid');
            const tidinterpreted = features[f].get('tidinterpreted');
            const sciname = features[f].get('sciname');
            let family = (features[f].get('accFamily') ? features[f].get('accFamily') : features[f].get('family'));
            if(family){
                family = family.toUpperCase();
            }
            else{
                family = 'undefined';
            }
            //var namestring = (sciname?sciname:'')+(tidinterpreted?tidinterpreted:'');
            let namestring = (sciname ? sciname : '');
            namestring = namestring.replaceAll(" ","");
            namestring = namestring.toLowerCase();
            namestring = namestring.replaceAll(/[^A-Za-z\d ]/g,'');
            if(!collSymbology[collName]){
                collSymbology[collName] = [];
                collSymbology[collName]['collid'] = collid;
                collSymbology[collName]['color'] = pointLayerFillColor;
            }
            if(!taxaSymbology[namestring]){
                taxaCnt++;
                taxaSymbology[namestring] = [];
                taxaSymbology[namestring]['sciname'] = sciname;
                taxaSymbology[namestring]['tidinterpreted'] = tidinterpreted;
                taxaSymbology[namestring]['family'] = family;
                taxaSymbology[namestring]['color'] = pointLayerFillColor;
                taxaSymbology[namestring]['count'] = 1;
            }
            else{
                taxaSymbology[namestring]['count'] = taxaSymbology[namestring]['count'] + 1;
            }
            features[f].set('namestring',namestring,true);
        }
    }
}

function refreshLayerOrder(){
    const layerCount = map.getLayers().getArray().length;
    layersObj['dragdrop1'].setZIndex(layerCount-11);
    layersObj['dragdrop2'].setZIndex(layerCount-10);
    layersObj['dragdrop3'].setZIndex(layerCount-9);
    layersObj['dragdrop4'].setZIndex(layerCount-8);
    layersObj['dragdrop5'].setZIndex(layerCount-7);
    layersObj['dragdrop6'].setZIndex(layerCount-6);
    layersObj['uncertainty'].setZIndex(layerCount-5);
    layersObj['rasteranalysis'].setZIndex(layerCount-4);
    layersObj['select'].setZIndex(layerCount-3);
    layersObj['pointv'].setZIndex(layerCount-2);
    layersObj['heat'].setZIndex(layerCount-1);
    layersObj['spider'].setZIndex(layerCount);
}

function resetMainSymbology(){
    for(let i in collSymbology){
        if(collSymbology.hasOwnProperty(i)){
            collSymbology[i]['color'] = pointLayerFillColor;
            const keyName = 'keyColor' + i;
            if(document.getElementById(keyName)){
                document.getElementById(keyName).color.fromString(pointLayerFillColor);
            }
        }
    }
}

function resetSymbology(){
    document.getElementById("symbolizeReset1").disabled = true;
    document.getElementById("symbolizeReset2").disabled = true;
    changeMapSymbology('coll');
    resetMainSymbology();
    for(let i in collSymbology){
        if(collSymbology.hasOwnProperty(i)){
            buildCollKeyPiece(i);
        }
    }
    layersObj['pointv'].getSource().changed();
    if(spiderCluster){
        const spiderFeatures = layersObj['spider'].getSource().getFeatures();
        for(let f in spiderFeatures){
            if(spiderFeatures.hasOwnProperty(f)){
                const style = (spiderFeatures[f].get('features') ? setClusterSymbol(spiderFeatures[f]) : setSymbol(spiderFeatures[f]));
                spiderFeatures[f].setStyle(style);
            }
        }
    }
    document.getElementById("symbolizeReset1").disabled = false;
    document.getElementById("symbolizeReset2").disabled = false;
}

function saveKeyImage(){
    const keyElement = (mapSymbology === 'coll' ? document.getElementById("collSymbologyKey") : document.getElementById("taxasymbologykeysbox"));
    let keyClone = keyElement.cloneNode(true);
    document.body.appendChild(keyClone);
    html2canvas(keyClone).then(function(canvas) {
        if (navigator.msSaveBlob) {
            navigator.msSaveBlob(canvas.msToBlob(),'mapkey.png');
        }
        else {
            canvas.toBlob(function(blob) {
                saveAs(blob,'mapkey.png');
            });
        }
        document.body.removeChild(keyClone);
        keyClone = '';
    });
}

function setClusterSymbol(feature) {
    let clusterindex, hexcolor, radius;
    let style = '';
    let stroke = '';
    let selected = false;
    if(feature.get('features')){
        const size = feature.get('features').length;
        if(size > 1){
            if(selections.length > 0){
                clusterindex = feature.get('identifiers');
                for(let i in selections){
                    if(selections.hasOwnProperty(i)){
                        if(clusterindex.indexOf(selections[i]) !== -1) {
                            selected = true;
                        }
                    }
                }
            }
            clusterindex = feature.get('identifiers');
            const cKey = feature.get('clusterkey');
            if(mapSymbology === 'coll'){
                hexcolor = '#'+collSymbology[cKey]['color'];
            }
            else if(mapSymbology === 'taxa'){
                hexcolor = '#'+taxaSymbology[cKey]['color'];
            }
            const colorArr = hexToRgb(hexcolor);
            if(size < 10) {
                radius = (pointLayerPointRadius + 5);
            }
            else if(size < 100) {
                radius = (pointLayerPointRadius + 10);
            }
            else if(size < 1000) {
                radius = (pointLayerPointRadius + 15);
            }
            else if(size < 10000) {
                radius = (pointLayerPointRadius + 20);
            }
            else if(size < 100000) {
                radius = (pointLayerPointRadius + 25);
            }
            else {
                radius = (pointLayerPointRadius + 30);
            }

            if(selected) {
                stroke = new ol.style.Stroke({color: ('#' + pointLayerSelectionsBorderColor), width: pointLayerSelectionsBorderWidth})
            }

            style = new ol.style.Style({
                image: new ol.style.Circle({
                    radius: radius,
                    stroke: stroke,
                    fill: new ol.style.Fill({
                        color: [colorArr['r'],colorArr['g'],colorArr['b'],0.8]
                    })
                }),
                text: new ol.style.Text({
                    scale: 1,
                    text: size.toString(),
                    fill: new ol.style.Fill({
                        color: '#fff'
                    }),
                    stroke: new ol.style.Stroke({
                        color: 'rgba(0, 0, 0, 0.6)',
                        width: 3
                    })
                })
            });
        }
        else{
            const originalFeature = feature.get('features')[0];
            style = setSymbol(originalFeature);
        }
    }
    return style;
}

function setInputFormBySearchTermsArr(){
    if(searchTermsArr.hasOwnProperty('taxa')){
        document.getElementById("taxa").value = searchTermsArr['taxa'];
        document.getElementById("taxontype").value = searchTermsArr['taxontype'];
        if(searchTermsArr.hasOwnProperty('thes')){
            document.getElementById("thes").checked = true;
        }
    }
    if(searchTermsArr.hasOwnProperty('country')){
        document.getElementById("country").value = searchTermsArr['country'];
    }
    if(searchTermsArr.hasOwnProperty('state')){
        document.getElementById("state").value = searchTermsArr['state'];
    }
    if(searchTermsArr.hasOwnProperty('county')){
        document.getElementById("county").value = searchTermsArr['county'];
    }
    if(searchTermsArr.hasOwnProperty('locality')){
        document.getElementById("locality").value = searchTermsArr['locality'];
    }
    if(searchTermsArr.hasOwnProperty('elevlow')){
        document.getElementById("elevlow").value = searchTermsArr['elevlow'];
    }
    if(searchTermsArr.hasOwnProperty('elevhigh')){
        document.getElementById("elevhigh").value = searchTermsArr['elevhigh'];
    }
    if(searchTermsArr.hasOwnProperty('collector')){
        document.getElementById("collector").value = searchTermsArr['collector'];
    }
    if(searchTermsArr.hasOwnProperty('collnum')){
        document.getElementById("collnum").value = searchTermsArr['collnum'];
    }
    if(searchTermsArr.hasOwnProperty('eventdate1')){
        document.getElementById("eventdate1").value = searchTermsArr['eventdate1'];
    }
    if(searchTermsArr.hasOwnProperty('eventdate2')){
        document.getElementById("eventdate2").value = searchTermsArr['eventdate2'];
    }
    if(searchTermsArr.hasOwnProperty('occurrenceRemarks')){
        document.getElementById("occurrenceRemarks").value = searchTermsArr['occurrenceRemarks'];
    }
    if(searchTermsArr.hasOwnProperty('catnum')){
        document.getElementById("catnum").value = searchTermsArr['catnum'];
    }
    if(searchTermsArr.hasOwnProperty('othercatnum')){
        document.getElementById("othercatnum").checked = true;
    }
    if(searchTermsArr.hasOwnProperty('typestatus')){
        document.getElementById("typestatus").checked = true;
    }
    if(searchTermsArr.hasOwnProperty('hasaudio')){
        document.getElementById("hasaudio").checked = true;
    }
    if(searchTermsArr.hasOwnProperty('hasimages')){
        document.getElementById("hasimages").checked = true;
    }
    if(searchTermsArr.hasOwnProperty('hasvideo')){
        document.getElementById("hasvideo").checked = true;
    }
    if(searchTermsArr.hasOwnProperty('hasmedia')){
        document.getElementById("hasmedia").checked = true;
    }
    if(searchTermsArr.hasOwnProperty('hasgenetic')){
        document.getElementById("hasgenetic").checked = true;
    }
    if(searchTermsArr.hasOwnProperty('upperlat') || searchTermsArr.hasOwnProperty('pointlat') || searchTermsArr.hasOwnProperty('circleArr') || searchTermsArr.hasOwnProperty('polyArr')){
        document.getElementById("noshapecriteria").style.display = "none";
        document.getElementById("shapecriteria").style.display = "block";
    }
}

function setSymbol(feature){
    let fill;
    let color;
    let showPoint = true;
    let style;
    let stroke;
    let selected = false;
    const cKey = feature.get(clusterKey);
    let recType = feature.get('CollType');
    if(!recType) recType = 'observation';
    if(selections.length > 0){
        const occid = Number(feature.get('id'));
        if(selections.indexOf(occid) !== -1) {
            selected = true;
        }
    }
    if(mapSymbology === 'coll'){
        color = '#'+collSymbology[cKey]['color'];
    }
    else if(mapSymbology === 'taxa'){
        color = '#' + taxaSymbology[cKey]['color'];
    }

    if(showPoint){
        if(selected) {
            stroke = new ol.style.Stroke({color: ('#' + pointLayerSelectionsBorderColor), width: pointLayerSelectionsBorderWidth});
        }
        else {
            stroke = new ol.style.Stroke({color: ('#' + pointLayerBorderColor), width: pointLayerBorderWidth});
        }
        fill = new ol.style.Fill({color: color});
    }
    else{
        stroke = new ol.style.Stroke({color: 'rgba(255, 255, 255, 0.01)', width: 0});
        fill = new ol.style.Fill({color: 'rgba(255, 255, 255, 0.01)'});
    }

    if(recType.toLowerCase().indexOf('observation') !== -1){
        style = new ol.style.Style({
            image: new ol.style.RegularShape({
                fill: fill,
                stroke: stroke,
                points: 3,
                radius: pointLayerPointRadius
            })
        });
    }
    else{
        style = new ol.style.Style({
            image: new ol.style.Circle({
                radius: pointLayerPointRadius,
                fill: fill,
                stroke: stroke
            })
        });
    }

    return style;
}

function showDatasetManagementPopup(){
    if(selections.length > 0){
        document.getElementById("datasetselecteddiv").style.display = "block";
    }
    else{
        document.getElementById("datasetselecteddiv").style.display = "none";
    }
    $("#datasetmanagement").popup("show");
}

function updateSelections(seloccid,infoArr){
    let selectionList = '';
    let trfragment = '';
    let selcat = '';
    let sellabel = '';
    let sele = '';
    let sels = '';
    selectionList += document.getElementById("selectiontbody").innerHTML;
    const divid = "sel" + seloccid;
    const trid = "tr" + seloccid;
    if(infoArr){
        selcat = infoArr['catalognumber'];
        const mouseOverLabel = "openRecordInfoBox(" + seloccid + ",'" + infoArr['collector'] + "');";
        let labelHTML = '<a href="#" onmouseover="' + mouseOverLabel + '" onmouseout="closeRecordInfoBox();" onclick="openIndPopup(' + seloccid + '); return false;">';
        labelHTML += infoArr['collector'];
        labelHTML += '</a>';
        sellabel = labelHTML;
        sele = infoArr['eventdate'];
        sels = infoArr['sciname'];
    }
    else if(document.getElementById(trid)){
        const catid = "cat" + seloccid;
        const labelid = "label" + seloccid;
        const eid = "e" + seloccid;
        const sid = "s" + seloccid;
        selcat = document.getElementById(catid).innerHTML;
        sellabel = document.getElementById(labelid).innerHTML;
        sele = document.getElementById(eid).innerHTML;
        sels = document.getElementById(sid).innerHTML;
    }
    if(!document.getElementById(divid)){
        trfragment = '';
        trfragment += '<tr id="sel'+seloccid+'" >';
        trfragment += '<td>';
        trfragment += '<input type="checkbox" id="selch'+seloccid+'" name="occid[]" value="'+seloccid+'" onchange="removeSelection(this);" checked />';
        trfragment += '</td>';
        trfragment += '<td id="selcat'+seloccid+'"  style="width:200px;" >'+selcat+'</td>';
        trfragment += '<td id="sellabel'+seloccid+'"  style="width:200px;" >';
        trfragment += sellabel;
        trfragment += '</td>';
        trfragment += '<td id="sele'+seloccid+'"  style="width:200px;" >'+sele+'</td>';
        trfragment += '<td id="sels'+seloccid+'"  style="width:200px;" >'+sels+'</td>';
        trfragment += '</tr>';
        selectionList += trfragment;
    }
    document.getElementById("selectiontbody").innerHTML = selectionList;
}

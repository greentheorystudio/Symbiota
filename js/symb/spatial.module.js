let spatialModuleInitialising = false;
const coreLayers = ['base','uncertainty','select','pointv','heat','spider','radius','vector'];
let inputResponseData = {};
let geoPolyArr = [];
let geoCircleArr = [];
let geoBoundingBoxArr = {};
let geoPointArr = [];
let layersArr = [];
let rasterLayersArr = [];
let layerOrderArr = [];
let mouseCoords = [];
let selections = [];
let collSymbology = [];
let taxaSymbology = [];
let collKeyArr = [];
let taxaKeyArr = [];
let queryRecCnt = 0;
let draw;
let clustersource;
let loadPointsEvent = false;
let toggleSelectedPoints = false;
let taxaCnt = 0;
let lazyLoadCnt = 20000;
let clusterDistance = 50;
let clusterPoints = true;
let showHeatMap = false;
let heatMapRadius = 5;
let heatMapBlur = 15;
let mapSymbology = 'coll';
let clusterKey = 'CollectionName';
let maxFeatureCount;
let currentResolution;
let activeLayer = 'none';
let shapeActive = false;
let pointActive = false;
let spiderCluster;
let spiderFeature;
let hiddenClusters = [];
let clickedFeatures = [];
let dragDrop1 = false;
let dragDrop2 = false;
let dragDrop3 = false;
let dragDrop4 = false;
let dragDrop5 = false;
let dragDrop6 = false;
let dragDropTarget = '';
let dsOldestDate = '';
let dsNewestDate = '';
let tsOldestDate = '';
let tsNewestDate = '';
let dateSliderActive = false;
let sliderdiv = '';
let loadingComplete = true;
let returnClusters = false;
let dsAnimDuration = '';
let dsAnimTime = '';
let dsAnimImageSave = false;
let dsAnimReverse = false;
let dsAnimDual = false;
let dsAnimLow = '';
let dsAnimHigh = '';
let dsAnimStop = true;
let dsAnimation = '';
let zipFile = '';
let zipFolder = '';
let transformStartAngle = 0;
let transformD = [0,0];
let transformFirstPoint = false;

const mapProjection = new ol.proj.Projection({
    code: 'EPSG:3857'
});

const wgs84Projection = new ol.proj.Projection({
    code: 'EPSG:4326',
    units: 'degrees'
});

const projection = ol.proj.get('EPSG:4326');
const projectionExtent = projection.getExtent();
const tileSize = 512;
const maxResolution = ol.extent.getWidth(projectionExtent) / (tileSize * 2);
const resolutions = new Array(16);
for (let z = 0; z < 16; ++z) {
    resolutions[z] = maxResolution / Math.pow(2, z);
}

const baselayer = new ol.layer.Tile({
    zIndex: 0
});

function addLayerToLayerOrderArr(layerId) {
    layerOrderArr.push(layerId);
    const sortingScrollerId = 'layerOrder-' + layerId;
    $( ('#' + sortingScrollerId) ).spinner( "enable" );
    setLayersOrder();
}

function addLayerToSelList(layer,title,active){
    const origValue = document.getElementById("selectlayerselect").value;
    let selectionList = document.getElementById("selectlayerselect").innerHTML;
    const newOption = '<option value="' + layer + '">' + title + '</option>';
    selectionList += newOption;
    document.getElementById("selectlayerselect").innerHTML = selectionList;
    if(active){
        document.getElementById("selectlayerselect").value = layer;
        setActiveLayer();
    }
    else{
        document.getElementById("selectlayerselect").value = origValue;
    }
}

function addQueryToDataset(){
    document.getElementById("selectedtargetdatasetid").value = document.getElementById("targetdatasetid").value;
    document.getElementById("dsstarrjson").value = JSON.stringify(searchTermsArr);
    document.getElementById("datasetformaction").value = 'addAllToDataset';
    document.getElementById("datasetform").submit();
}

function addRasterLayerToTargetList(layerId,title){
    let selectionList = document.getElementById("targetrasterselect").innerHTML;
    const newOption = '<option value="' + layerId + '">' + title + '</option>';
    selectionList += newOption;
    document.getElementById("targetrasterselect").innerHTML = selectionList;
    document.getElementById("targetrasterselect").value = '';
    rasterLayersArr.push(layerId);
    if(rasterLayersArr.length > 0){
        document.getElementById("rastertoolspanel").style.display = "block";
        document.getElementById("rastertoolstab").style.display = "block";
    }
}

function addSelectionsToDataset(){
    document.getElementById("selectedtargetdatasetid").value = document.getElementById("targetdatasetid").value;
    document.getElementById("occarrjson").value = JSON.stringify(selections);
    document.getElementById("datasetformaction").value = 'addSelectedToDataset';
    document.getElementById("datasetform").submit();
}

function adjustSelectionsTab(){
    if(selections.length > 0){
        document.getElementById("selectionstab").style.display = "block";
    }
    else{
        document.getElementById("selectionstab").style.display = "none";
        const activeTab = $('#recordstab').tabs("option", "active");
        if(activeTab == 3){
            buildCollKey();
            $('#recordstab').tabs({active:0});
        }
    }
}

function animateDS(){
    if(!dsAnimStop){
        let calcHighDate, lowDateValStr, highDateValStr;
        const lowDate = document.getElementById("datesliderearlydate").value;
        const highDate = document.getElementById("datesliderlatedate").value;
        let lowDateVal = new Date(lowDate);
        lowDateVal = new Date(lowDateVal.setTime(lowDateVal.getTime()+86400000));
        let highDateVal = new Date(highDate);
        highDateVal = new Date(highDateVal.setTime(highDateVal.getTime()+86400000));
        if(dsAnimReverse){
            if(dsAnimDual){
                if(lowDateVal.getTime() !== highDateVal.getTime()) highDateVal = new Date(highDateVal.setDate(highDateVal.getDate() - dsAnimDuration));
                const calcLowDate = new Date(lowDateVal.setDate(lowDateVal.getDate() - dsAnimDuration));
                if(calcLowDate.getTime() > dsAnimLow.getTime()){
                    lowDateVal = calcLowDate;
                }
                else{
                    lowDateVal = dsAnimLow;
                    dsAnimStop = true;
                }
            }
            else{
                calcHighDate = new Date(highDateVal.setDate(highDateVal.getDate() - dsAnimDuration));
                if(calcHighDate.getTime() > dsAnimLow.getTime()){
                    highDateVal = calcHighDate;
                }
                else{
                    dsAnimStop = true;
                }
            }
        }
        else{
            if(dsAnimDual && (lowDateVal.getTime() !== highDateVal.getTime())) lowDateVal = new Date(lowDateVal.setDate(lowDateVal.getDate() + dsAnimDuration));
            calcHighDate = new Date(highDateVal.setDate(highDateVal.getDate() + dsAnimDuration));
            if(calcHighDate.getTime() < dsAnimHigh.getTime()){
                highDateVal = calcHighDate;
            }
            else{
                highDateVal = dsAnimHigh;
                dsAnimStop = true;
            }
        }
        tsOldestDate = lowDateVal;
        tsNewestDate = highDateVal;
        lowDateValStr = getISOStrFromDateObj(lowDateVal);
        highDateValStr = getISOStrFromDateObj(highDateVal);
        $("#sliderdiv").slider('values',0,tsOldestDate.getTime());
        $("#sliderdiv").slider('values',1,tsNewestDate.getTime());
        $("#custom-label-min").text(lowDateValStr);
        $("#custom-label-max").text(highDateValStr);
        document.getElementById("datesliderearlydate").value = lowDateValStr;
        document.getElementById("datesliderlatedate").value = highDateValStr;
        layersArr['pointv'].getSource().changed();
        if(dsAnimImageSave){
            const filename = lowDateValStr + '-to-' + highDateValStr + '.png';
            exportMapPNG(filename,true);
        }
        if(!dsAnimStop){
            dsAnimation = setTimeout(animateDS,dsAnimTime);
        }
        else{
            tsOldestDate = dsAnimLow;
            tsNewestDate = dsAnimHigh;
            lowDateValStr = getISOStrFromDateObj(dsAnimLow);
            highDateValStr = getISOStrFromDateObj(dsAnimHigh);
            $("#sliderdiv").slider('values',0,tsOldestDate.getTime());
            $("#sliderdiv").slider('values',1,tsNewestDate.getTime());
            $("#custom-label-min").text(lowDateValStr);
            $("#custom-label-max").text(highDateValStr);
            document.getElementById("datesliderearlydate").value = lowDateValStr;
            document.getElementById("datesliderlatedate").value = highDateValStr;
            layersArr['pointv'].getSource().changed();
            dsAnimation = '';
        }
    }
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

function buildLayerControllerLayerElement(lArr,active){
    const layerDivId = 'layer-' + lArr['id'];
    const layerDiv = document.createElement('div');
    const raster = (lArr['fileType'] === 'tif' || lArr['fileType'] === 'tiff');
    layerDiv.setAttribute("id",layerDivId);
    layerDiv.setAttribute("style","border:1px solid black;padding:5px;margin-bottom:5px;background-color:white;width:100%;font-family:Verdana,Arial,sans-serif;font-size:14px;");
    const layerMainDiv = document.createElement('div');
    layerMainDiv.setAttribute("style","display:flex;flex-direction:column;");
    const layerTitleDiv = document.createElement('div');
    layerTitleDiv.setAttribute("style","font-size:14px;font-weight:bold;");
    layerTitleDiv.innerHTML = lArr['layerName'];
    layerMainDiv.appendChild(layerTitleDiv);
    if(lArr.hasOwnProperty('layerDescription') && lArr['layerDescription']){
        const layerDescDiv = document.createElement('div');
        layerDescDiv.innerHTML = lArr['layerDescription'];
        layerMainDiv.appendChild(layerDescDiv);
    }
    if(lArr.hasOwnProperty('providedBy') || lArr.hasOwnProperty('sourceURL')){
        const layerProvidedDiv = document.createElement('div');
        let innerHtml = '';
        if(lArr.hasOwnProperty('providedBy') && lArr['providedBy']){
            innerHtml += '<span style="font-weight:bold;">Provided by: </span>' + lArr['providedBy'] + ' ';
        }
        if(lArr.hasOwnProperty('sourceURL') && lArr['sourceURL']){
            innerHtml += '<span style="font-weight:bold;"><a href="' + lArr['sourceURL'] + '" target="_blank">(Go to source)</a></span>';
        }
        layerProvidedDiv.innerHTML = innerHtml;
        layerMainDiv.appendChild(layerProvidedDiv);
    }
    if(lArr.hasOwnProperty('dateAquired') || lArr.hasOwnProperty('dateUploaded')){
        const layerAquiredDiv = document.createElement('div');
        let innerHtml = '';
        if(lArr.hasOwnProperty('dateAquired') && lArr['dateAquired']){
            innerHtml += '<span style="font-weight:bold;">Date aquired: </span>' + lArr['dateAquired'] + ' ';
        }
        if(lArr.hasOwnProperty('dateUploaded') && lArr['dateUploaded']){
            innerHtml += '<span style="font-weight:bold;">Date uploaded: </span>' + lArr['dateUploaded'];
        }
        layerAquiredDiv.innerHTML = innerHtml;
        layerMainDiv.appendChild(layerAquiredDiv);
    }
    const layerMainBottomDiv = document.createElement('div');
    layerMainBottomDiv.setAttribute("style","font-size:14px;font-weight:bold;width:100%;display:flex;justify-content:flex-end;align-items:flex-end;margin-top:5px;");
    const dataTypeImageDiv = document.createElement('div');
    dataTypeImageDiv.setAttribute("style","width:30px;height:30px;background-color:black;margin:0 5px;");
    const dataTypeImage = document.createElement('img');
    dataTypeImage.setAttribute("style","width:20px;margin-left:5px;margin-top:5px;");
    if(lArr['fileType'] === 'tif' || lArr['fileType'] === 'tiff'){
        dataTypeImage.setAttribute("src","../images/button_wms.png");
    }
    else{
        dataTypeImage.setAttribute("src","../images/button_wfs.png");
    }
    dataTypeImageDiv.appendChild(dataTypeImage);
    layerMainBottomDiv.appendChild(dataTypeImageDiv);
    if(lArr['sortable']){
        const sortingScrollerDivId = 'layerOrderDiv-' + lArr['id'];
        const sortingScrollerDiv = document.createElement('div');
        sortingScrollerDiv.setAttribute("id",sortingScrollerDivId);
        const sortingScrollerDisplayVal = (active ? 'flex' : 'none');
        sortingScrollerDiv.setAttribute("style","display:" + sortingScrollerDisplayVal + ";align-items:center;margin:0 5px;");
        const sortingScrollerId = 'layerOrder-' + lArr['id'];
        const sortingScrollerLabel = document.createElement('label');
        sortingScrollerLabel.setAttribute("for",sortingScrollerId);
        sortingScrollerLabel.setAttribute("style","margin-top:8px;margin-right:5px;font-weight:bold;");
        sortingScrollerLabel.innerHTML = 'Order:';
        const sortingScroller = document.createElement('input');
        sortingScroller.setAttribute("id",sortingScrollerId);
        sortingScroller.setAttribute("style","width:25px;");
        sortingScrollerDiv.appendChild(sortingScrollerLabel);
        sortingScrollerDiv.appendChild(sortingScroller);
        layerMainBottomDiv.appendChild(sortingScrollerDiv);
    }
    if(lArr['symbology'] && !raster){
        const symbologyButtonId = 'layerSymbologyButton-' + lArr['id'];
        const symbologyButton = document.createElement('button');
        symbologyButton.setAttribute("id",symbologyButtonId);
        const symbologyOnclickVal = "toggleLayerSymbology('" + lArr['id'] + "');";
        const symbologyButtonDisplayVal = (active ? 'block' : 'none');
        symbologyButton.setAttribute("type","button");
        symbologyButton.setAttribute("style","display:" + symbologyButtonDisplayVal + ";margin:0 5px;padding:3px;font-family:Verdana,Arial,sans-serif;font-size:14px;");
        symbologyButton.setAttribute("title","Toggle Symbology");
        symbologyButton.setAttribute("onclick",symbologyOnclickVal);
        symbologyButton.innerHTML = 'Symbology';
        layerMainBottomDiv.appendChild(symbologyButton);
    }
    if(lArr['query'] && !raster){
        const queryButtonId = 'layerQueryButton-' + lArr['id'];
        const queryButton = document.createElement('button');
        queryButton.setAttribute("id",queryButtonId);
        const queryOnclickVal = "toggleLayerQuerySelector('" + lArr['id'] + "');";
        const queryButtonDisplayVal = (active ? 'block' : 'none');
        queryButton.setAttribute("type","button");
        queryButton.setAttribute("style","display:" + queryButtonDisplayVal + ";margin:0 5px;padding:3px;font-family:Verdana,Arial,sans-serif;font-size:14px;");
        queryButton.setAttribute("title","Toggle Symbology");
        queryButton.setAttribute("onclick",queryOnclickVal);
        queryButton.innerHTML = 'Query Selector';
        layerMainBottomDiv.appendChild(queryButton);
    }
    if(lArr['removable']){
        const removeButton = document.createElement('button');
        const removeOnclickVal = "removeUserLayer('" + lArr['id'] + "');";
        removeButton.setAttribute("type","button");
        removeButton.setAttribute("style","margin:0 5px;padding:2px;height:25px;width:25px;");
        removeButton.setAttribute("title","Remove layer");
        removeButton.setAttribute("onclick",removeOnclickVal);
        const removeIcon = document.createElement('i');
        removeIcon.setAttribute("style","height:15px;width:15px;");
        removeIcon.setAttribute("class","far fa-trash-alt");
        removeButton.appendChild(removeIcon);
        layerMainBottomDiv.appendChild(removeButton);
    }
    const visibilityCheckbox = document.createElement('input');
    const visibilityCheckboxId = 'layerVisible-' + lArr['id'];
    visibilityCheckbox.setAttribute("id",visibilityCheckboxId);
    visibilityCheckbox.setAttribute('type','checkbox');
    visibilityCheckbox.setAttribute("style","margin:0 5px;");
    let visibilityOnchangeVal;
    if(lArr['type'] === 'userLayer'){
        visibilityOnchangeVal = "toggleUserLayerVisibility('" + lArr['id'] + "','" + lArr['layerName'] + "',this.checked);";
    }
    else{
        visibilityOnchangeVal = "toggleServerLayerVisibility('" + lArr['id'] + "','" + lArr['layerName'] + "','" + lArr['file'] + "',this.checked);";
    }
    visibilityCheckbox.setAttribute("onchange",visibilityOnchangeVal);
    if(active || lArr['id'] === 'select'){
        visibilityCheckbox.checked = true;
    }
    layerMainBottomDiv.appendChild(visibilityCheckbox);
    layerMainDiv.appendChild(layerMainBottomDiv);
    layerDiv.appendChild(layerMainDiv);
    if(lArr['symbology']){
        const layerSymbologyDivId = 'layerSymbology-' + lArr['id'];
        const layerSymbologyDiv = document.createElement('div');
        layerSymbologyDiv.setAttribute("id",layerSymbologyDivId);
        layerSymbologyDiv.setAttribute("style","border:1px solid black;padding:5px;margin-top:5px;display:none;flex-direction:column;width:60%;margin-left:auto;margin-right:auto;");
        const symbologyTopRow = document.createElement('div');
        symbologyTopRow.setAttribute("style","display:flex;justify-content:space-evenly;");
        const symbologyBorderColorDiv = document.createElement('div');
        symbologyBorderColorDiv.setAttribute("style","display:flex;align-items:center;");
        const symbologyBorderColorSpan = document.createElement('span');
        symbologyBorderColorSpan.setAttribute("style","font-weight:bold;margin-right:10px;font-size:12px;");
        symbologyBorderColorSpan.innerHTML = 'Border color: ';
        symbologyBorderColorDiv.appendChild(symbologyBorderColorSpan);
        const symbologyBorderColorInputId = 'borderColor-' + lArr['id'];
        const symbologyBorderColorOnchangeVal = "changeBorderColor('" + lArr['id'] + "',this.value);";
        const symbologyBorderColorInput = document.createElement('input');
        symbologyBorderColorInput.setAttribute("data-role","none");
        symbologyBorderColorInput.setAttribute("id",symbologyBorderColorInputId);
        symbologyBorderColorInput.setAttribute("class","color");
        symbologyBorderColorInput.setAttribute("style","cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;");
        symbologyBorderColorInput.setAttribute("value",lArr['borderColor']);
        symbologyBorderColorInput.setAttribute("onchange",symbologyBorderColorOnchangeVal);
        symbologyBorderColorDiv.appendChild(symbologyBorderColorInput);
        symbologyTopRow.appendChild(symbologyBorderColorDiv);
        const symbologyFillColorDiv = document.createElement('div');
        symbologyFillColorDiv.setAttribute("style","display:flex;align-items:center;");
        const symbologyFillColorSpan = document.createElement('span');
        symbologyFillColorSpan.setAttribute("style","font-weight:bold;margin-right:10px;font-size:12px;");
        symbologyFillColorSpan.innerHTML = 'Fill color: ';
        symbologyFillColorDiv.appendChild(symbologyFillColorSpan);
        const symbologyFillColorInputId = 'fillColor-' + lArr['id'];
        const symbologyFillColorOnchangeVal = "changeFillColor('" + lArr['id'] + "',this.value);";
        const symbologyFillColorInput = document.createElement('input');
        symbologyFillColorInput.setAttribute("data-role","none");
        symbologyFillColorInput.setAttribute("id",symbologyFillColorInputId);
        symbologyFillColorInput.setAttribute("class","color");
        symbologyFillColorInput.setAttribute("style","cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;");
        symbologyFillColorInput.setAttribute("value",lArr['fillColor']);
        symbologyFillColorInput.setAttribute("onchange",symbologyFillColorOnchangeVal);
        symbologyFillColorDiv.appendChild(symbologyFillColorInput);
        symbologyTopRow.appendChild(symbologyFillColorDiv);
        layerSymbologyDiv.appendChild(symbologyTopRow);
        const symbologyBottomRow = document.createElement('div');
        symbologyBottomRow.setAttribute("style","display:flex;justify-content:space-evenly;margin-top:3px;");
        const symbologyBorderWidthDiv = document.createElement('div');
        symbologyBorderWidthDiv.setAttribute("style","display:flex;align-items:center;");
        const symbologyBorderWidthSpan = document.createElement('span');
        symbologyBorderWidthSpan.setAttribute("style","font-weight:bold;margin-right:10px;font-size:12px;");
        symbologyBorderWidthSpan.innerHTML = 'Border width (px): ';
        symbologyBorderWidthDiv.appendChild(symbologyBorderWidthSpan);
        const symbologyBorderWidthInputId = 'borderWidth-' + lArr['id'];
        const symbologyBorderWidthInput = document.createElement('input');
        symbologyBorderWidthInput.setAttribute("id",symbologyBorderWidthInputId);
        symbologyBorderWidthInput.setAttribute("style","width:25px;");
        symbologyBorderWidthInput.setAttribute("value",lArr['borderWidth']);
        symbologyBorderWidthDiv.appendChild(symbologyBorderWidthInput);
        symbologyBottomRow.appendChild(symbologyBorderWidthDiv);
        const symbologyPointRadiusDiv = document.createElement('div');
        symbologyPointRadiusDiv.setAttribute("style","display:flex;align-items:center;");
        const symbologyPointRadiusSpan = document.createElement('span');
        symbologyPointRadiusSpan.setAttribute("style","font-weight:bold;margin-right:10px;font-size:12px;");
        symbologyPointRadiusSpan.innerHTML = 'Point radius (px): ';
        symbologyPointRadiusDiv.appendChild(symbologyPointRadiusSpan);
        const symbologyPointRadiusInputId = 'pointRadius-' + lArr['id'];
        const symbologyPointRadiusInput = document.createElement('input');
        symbologyPointRadiusInput.setAttribute("id",symbologyPointRadiusInputId);
        symbologyPointRadiusInput.setAttribute("style","width:25px;");
        symbologyPointRadiusInput.setAttribute("value",lArr['pointRadius']);
        symbologyPointRadiusDiv.appendChild(symbologyPointRadiusInput);
        symbologyBottomRow.appendChild(symbologyPointRadiusDiv);
        const symbologyOpacityDiv = document.createElement('div');
        symbologyOpacityDiv.setAttribute("style","display:flex;align-items:center;");
        const symbologyOpacitySpan = document.createElement('span');
        symbologyOpacitySpan.setAttribute("style","font-weight:bold;margin-right:10px;font-size:12px;");
        symbologyOpacitySpan.innerHTML = 'Opacity: ';
        symbologyOpacityDiv.appendChild(symbologyOpacitySpan);
        const symbologyOpacityInputId = 'opacity-' + lArr['id'];
        const symbologyOpacityInput = document.createElement('input');
        symbologyOpacityInput.setAttribute("id",symbologyOpacityInputId);
        symbologyOpacityInput.setAttribute("style","width:25px;");
        symbologyOpacityInput.setAttribute("value",lArr['opacity']);
        symbologyOpacityDiv.appendChild(symbologyOpacityInput);
        symbologyBottomRow.appendChild(symbologyOpacityDiv);
        layerSymbologyDiv.appendChild(symbologyBottomRow);
        layerDiv.appendChild(layerSymbologyDiv);
    }
    if(raster){
        addRasterLayerToTargetList(lArr['id'],lArr['layerName'])
    }
    return layerDiv;
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

function changeBaseMap(){
    let blsource;
    const selection = document.getElementById('base-map').value;
    const baseLayer = map.getLayers().getArray()[0];
    if(selection === 'googleroadmap'){
        blsource = new ol.source.XYZ({
            url: 'http://mt0.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'googlealteredroadmap'){
        blsource = new ol.source.XYZ({
            url: 'http://mt0.google.com/vt/lyrs=r&hl=en&x={x}&y={y}&z={z}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'googleterrain'){
        blsource = new ol.source.XYZ({
            url: 'http://mt0.google.com/vt/lyrs=p&hl=en&x={x}&y={y}&z={z}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'googlehybrid'){
        blsource = new ol.source.XYZ({
            url: 'http://mt0.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'googlesatellite'){
        blsource = new ol.source.XYZ({
            url: 'http://mt0.google.com/vt/lyrs=s&hl=en&x={x}&y={y}&z={z}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'worldtopo'){
        blsource = new ol.source.XYZ({
            url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'openstreet'){
        blsource = new ol.source.OSM();
    }
    if(selection === 'blackwhite'){
        blsource = new ol.source.Stamen({layer: 'toner'});
    }
    if(selection === 'worldimagery'){
        blsource = new ol.source.XYZ({
            url: 'http://services.arcgisonline.com/arcgis/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'ocean'){
        blsource = new ol.source.XYZ({
            url: 'http://services.arcgisonline.com/arcgis/rest/services/Ocean_Basemap/MapServer/tile/{z}/{y}/{x}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'ngstopo'){
        blsource = new ol.source.XYZ({
            url: 'http://services.arcgisonline.com/arcgis/rest/services/USA_Topo_Maps/MapServer/tile/{z}/{y}/{x}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'natgeoworld'){
        blsource = new ol.source.XYZ({
            url: 'http://services.arcgisonline.com/arcgis/rest/services/NatGeo_World_Map/MapServer/tile/{z}/{y}/{x}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'esristreet'){
        blsource = new ol.source.XYZ({
            url: 'http://services.arcgisonline.com/arcgis/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
            crossOrigin: 'anonymous'
        });
    }
    baseLayer.setSource(blsource);
}

function changeBorderColor(layerId,value) {
    if(document.getElementById(('layerVisible-' + layerId)).checked === true){
        const fillColor = document.getElementById(('fillColor-' + layerId)).value;
        const borderWidth = document.getElementById(('borderWidth-' + layerId)).value;
        const pointRadius = document.getElementById(('pointRadius-' + layerId)).value;
        const opacity = document.getElementById(('opacity-' + layerId)).value;
        const style = getVectorLayerStyle(fillColor, value, borderWidth, pointRadius, opacity);
        layersArr[layerId].setStyle(style);
    }
}

function changeBorderWidth(layerId,value) {
    if(document.getElementById(('layerVisible-' + layerId)).checked === true){
        const borderColor = document.getElementById(('borderColor-' + layerId)).value;
        const fillColor = document.getElementById(('fillColor-' + layerId)).value;
        const pointRadius = document.getElementById(('pointRadius-' + layerId)).value;
        const opacity = document.getElementById(('opacity-' + layerId)).value;
        const style = getVectorLayerStyle(fillColor, borderColor, value, pointRadius, opacity);
        layersArr[layerId].setStyle(style);
    }
}

function changeClusterDistance(){
    clusterDistance = document.getElementById("setclusterdistance").value;
    clustersource.setDistance(clusterDistance);
}

function changeClusterSetting(){
    if(document.getElementById("sliderdiv")){
        document.getElementById("clusterswitch").checked = clusterPoints;
        alert('You cannot change the cluster setting while the Date Slider is active.');
    }
    else{
        clusterPoints = document.getElementById("clusterswitch").checked;
        if(clusterPoints){
            removeDateSlider();
            loadPointWFSLayer(0);
        }
        else{
            layersArr['pointv'].setSource(pointvectorsource);
        }
    }
}

function changeCollColor(color,key){
    changeMapSymbology('coll');
    collSymbology[key]['color'] = color;
    layersArr['pointv'].getSource().changed();
    if(spiderCluster){
        const spiderFeatures = layersArr['spider'].getSource().getFeatures();
        for(let f in spiderFeatures){
            if(spiderFeatures.hasOwnProperty(f)){
                const style = (spiderFeatures[f].get('features') ? setClusterSymbol(spiderFeatures[f]) : setSymbol(spiderFeatures[f]));
                spiderFeatures[f].setStyle(style);
            }
        }
    }
}

function changeDraw() {
    const value = typeSelect.value;
    if (value !== 'None') {
        if (value === 'Box') {
            draw = new ol.interaction.Draw({
                source: selectsource,
                type: 'Circle',
                geometryFunction: ol.interaction.Draw.createBox()
            });
        }
        else {
            draw = new ol.interaction.Draw({
                source: selectsource,
                type: value
            });
        }

        draw.on('drawend', function(evt){
            if(INPUTWINDOWMODE && INPUTTOOLSARR.includes('point')){
                const featureClone = evt.feature.clone();
                const geoType = featureClone.getGeometry().getType();
                const geoJSONFormat = new ol.format.GeoJSON();
                if(geoType === 'Point'){
                    selectsource.clear();
                    selectedFeatures.clear();
                    uncertaintycirclesource.clear();
                    const selectiongeometry = featureClone.getGeometry();
                    const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                    const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                    let pointCoords = JSON.parse(geojsonStr).coordinates;
                    const pointObj = {
                        decimalLatitude: pointCoords[1],
                        decimalLongitude: pointCoords[0]
                    };
                    geoPointArr.push(pointObj);
                    selectedFeatures.push(evt.feature);
                    processInputSelections();
                    if((INPUTTOOLSARR.includes('uncertainty') || INPUTTOOLSARR.includes('radius')) && document.getElementById("inputpointuncertainty")){
                        if(document.getElementById("inputpointuncertainty").value && !isNaN(document.getElementById("inputpointuncertainty").value && document.getElementById("inputpointuncertainty").value > 0)){
                            const pointRadius = {};
                            pointRadius.pointlat = pointCoords[1];
                            pointRadius.pointlong = pointCoords[0];
                            pointRadius.radius = document.getElementById("inputpointuncertainty").value;
                            createUncertaintyCircleFromPointRadius(pointRadius);
                        }
                    }
                }
            }
            else{
                evt.feature.set('geoType',typeSelect.value);
            }
            typeSelect.value = 'None';
            map.removeInteraction(draw);
            if(!shapeActive){
                const infoArr = [];
                infoArr['id'] = 'select';
                infoArr['type'] = 'userLayer';
                infoArr['fileType'] = 'vector';
                infoArr['layerName'] = 'Shapes';
                infoArr['layerDescription'] = "This layer contains all of the features created through using the Draw Tool, and those that have been selected from other layers added to the map.",
                infoArr['fillColor'] = shapesFillColor;
                infoArr['borderColor'] = shapesBorderColor;
                infoArr['borderWidth'] = shapesBorderWidth;
                infoArr['pointRadius'] = shapesPointRadius;
                infoArr['opacity'] = shapesOpacity;
                infoArr['removable'] = true;
                infoArr['sortable'] = false;
                infoArr['symbology'] = true;
                infoArr['query'] = true;
                processAddLayerControllerElement(infoArr,document.getElementById("coreLayers"),true);
                shapeActive = true;
                document.getElementById("selectlayerselect").value = 'select';
                setActiveLayer();
            }
            else{
                document.getElementById("selectlayerselect").value = 'select';
                setActiveLayer();
            }
            draw = '';
        });
        map.addInteraction(draw);
    }
    else{
        draw = '';
    }
}

function changeFillColor(layerId,value) {
    if(document.getElementById(('layerVisible-' + layerId)).checked === true){
        const borderColor = document.getElementById(('borderColor-' + layerId)).value;
        const borderWidth = document.getElementById(('borderWidth-' + layerId)).value;
        const pointRadius = document.getElementById(('pointRadius-' + layerId)).value;
        const opacity = document.getElementById(('opacity-' + layerId)).value;
        const style = getVectorLayerStyle(value, borderColor, borderWidth, pointRadius, opacity);
        layersArr[layerId].setStyle(style);
    }
}

function changeHeatMapBlur(){
    heatMapBlur = document.getElementById("heatmapblur").value;
    layersArr['heat'].setBlur(parseInt(heatMapBlur, 10));
}

function changeHeatMapRadius(){
    heatMapRadius = document.getElementById("heatmapradius").value;
    layersArr['heat'].setRadius(parseInt(heatMapRadius, 10));
}

function changeLayerOpacity(layerId,value) {
    if(document.getElementById(('layerVisible-' + layerId)).checked === true){
        const borderColor = document.getElementById(('borderColor-' + layerId)).value;
        const fillColor = document.getElementById(('fillColor-' + layerId)).value;
        const borderWidth = document.getElementById(('borderWidth-' + layerId)).value;
        const pointRadius = document.getElementById(('pointRadius-' + layerId)).value;
        const style = getVectorLayerStyle(fillColor, borderColor, borderWidth, pointRadius, value);
        layersArr[layerId].setStyle(style);
    }
}

function changeLayerOrder(layerId, value) {
    const scrollerId = 'layerOrder-' + layerId;
    const currentIndex = layerOrderArr.indexOf(layerId);
    layerOrderArr.splice(currentIndex,1);
    layerOrderArr.splice((value - 1),0,layerId);
    setLayersOrder();
}

function changeMapSymbology(symbology){
    if(symbology !== mapSymbology){
        if(spiderCluster){
            const source = layersArr['spider'].getSource();
            source.clear();
            const blankSource = new ol.source.Vector({
                features: new ol.Collection(),
                useSpatialIndex: true
            });
            layersArr['spider'].setSource(blankSource);
            for(let i in hiddenClusters){
                if(hiddenClusters.hasOwnProperty(i)){
                    showFeature(hiddenClusters[i]);
                }
            }
            hiddenClusters = [];
            spiderCluster = '';
            layersArr['pointv'].getSource().changed();
        }
    }
    if(symbology === 'coll'){
        if(mapSymbology === 'taxa'){
            clearTaxaSymbology();
            clusterKey = 'CollectionName';
            mapSymbology = 'coll';
            if(clusterPoints){
                loadPointWFSLayer(0);
            }
        }
    }
    if(symbology === 'taxa'){
        if(mapSymbology === 'coll'){
            resetMainSymbology();
            clusterKey = 'namestring';
            mapSymbology = 'taxa';
            if(clusterPoints){
                loadPointWFSLayer(0);
            }
        }
    }
}

function changePointRadius(layerId,value) {
    if(document.getElementById(('layerVisible-' + layerId)).checked === true){
        const borderColor = document.getElementById(('borderColor-' + layerId)).value;
        const fillColor = document.getElementById(('fillColor-' + layerId)).value;
        const borderWidth = document.getElementById(('borderWidth-' + layerId)).value;
        const opacity = document.getElementById(('opacity-' + layerId)).value;
        const style = getVectorLayerStyle(fillColor, borderColor, borderWidth, value, opacity);
        layersArr[layerId].setStyle(style);
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
    layersArr['pointv'].getSource().changed();
}

function checkDateSliderType(){
    if(dateSliderActive){
        document.body.removeChild(sliderdiv);
        sliderdiv = '';
        const dual = document.getElementById("dsdualtype").checked;
        createDateSlider(dual);
    }
}

function checkDSAnimDuration(){
    let lowDate, hLowDate, highDate, hHighDate, difference, diffYears;
    const duration = document.getElementById("datesliderinterduration").value;
    const imageSave = document.getElementById("dateslideranimimagesave").checked;
    if(duration){
        if(!isNaN(duration) && duration > 0){
            lowDate = document.getElementById("datesliderearlydate").value;
            hLowDate = new Date(lowDate);
            hLowDate = new Date(hLowDate.setTime(hLowDate.getTime()+86400000));
            highDate = document.getElementById("datesliderlatedate").value;
            hHighDate = new Date(highDate);
            hHighDate = new Date(hHighDate.setTime(hHighDate.getTime()+86400000));
            difference = (hHighDate-hLowDate)/1000;
            difference /= (60*60*24);
            diffYears = Math.abs(difference/365.25);
            if(duration >= diffYears){
                alert("Interval duration must less than the difference between the earliest and latest dates in years: "+diffYears.toFixed(4));
                document.getElementById("datesliderinterduration").value = '';
            }
            else if(imageSave){
                lowDate = document.getElementById("datesliderearlydate").value;
                hLowDate = new Date(lowDate);
                hLowDate = new Date(hLowDate.setTime(hLowDate.getTime()+86400000));
                highDate = document.getElementById("datesliderlatedate").value;
                hHighDate = new Date(highDate);
                hHighDate = new Date(hHighDate.setTime(hHighDate.getTime()+86400000));
                difference = (hHighDate - hLowDate)/1000;
                difference /= (60*60*24);
                diffYears = difference/365.25;
                const imageCount = Math.ceil(diffYears / duration);
                if(!confirm("You have Save Images checked. With the current interval duration and date settings, this will produce "+imageCount+" images. Click OK to continue.")){
                    document.getElementById("dateslideranimimagesave").checked = false;
                }
            }
        }
        else{
            alert("Interval duration must be a number greater than zero.");
            document.getElementById("datesliderinterduration").value = '';
        }
    }
}

function checkDSAnimTime(){
    const animtime = Number(document.getElementById("datesliderintertime").value);
    if(animtime && (isNaN(animtime) || animtime < 0.1 || animtime > 5)){
        alert("Interval time must be a number greater than or equal to .1, and less than or equal to 5.");
        document.getElementById("datesliderintertime").value = '';
    }
}

function checkDSHighDate(){
    const maxDate = dsNewestDate.getTime();
    const hMaxDate = new Date(maxDate);
    const hMaxDateStr = getISOStrFromDateObj(hMaxDate);
    const currentHighSetting = new Date($("#sliderdiv").slider("values", 1));
    const currentHighSettingStr = getISOStrFromDateObj(currentHighSetting);
    const highDate = document.getElementById("datesliderlatedate").value;
    if(highDate){
        if(formatCheckDate(highDate)){
            const currentLowSetting = new Date($("#sliderdiv").slider("values", 0));
            const currentLowSettingStr = getISOStrFromDateObj(currentLowSetting);
            const hHighDate = new Date(highDate);
            if(hHighDate < hMaxDate && hHighDate < currentLowSetting){
                alert("Date cannot be earlier than the currently set earliest date: "+currentLowSettingStr+'.');
                document.getElementById("datesliderlatedate").value = currentHighSettingStr;
            }
            else{
                alert("Date cannot be later than the latest date on slider: "+hMaxDateStr+'.');
                document.getElementById("datesliderlatedate").value = currentHighSettingStr;
            }
        }
    }
    else{
        document.getElementById("datesliderlatedate").value = currentHighSettingStr;
    }
}

function checkDSLowDate(){
    const minDate = dsOldestDate.getTime();
    const hMinDate = new Date(minDate);
    const hMinDateStr = getISOStrFromDateObj(hMinDate);
    const currentLowSetting = new Date($("#sliderdiv").slider("values", 0));
    const currentLowSettingStr = getISOStrFromDateObj(currentLowSetting);
    const lowDate = document.getElementById("datesliderearlydate").value;
    if(lowDate){
        if(formatCheckDate(lowDate)){
            const currentHighSetting = new Date($("#sliderdiv").slider("values", 1));
            const currentHighSettingStr = getISOStrFromDateObj(currentHighSetting);
            const hLowDate = new Date(lowDate);
            if(hLowDate > hMinDate && hLowDate > currentHighSetting){
                alert("Date cannot be after the currently set latest date: "+currentHighSettingStr+'.');
                document.getElementById("datesliderearlydate").value = currentLowSettingStr;
            }
            else{
                alert("Date cannot be earlier than the earliest date on slider: "+hMinDateStr+'.');
                document.getElementById("datesliderearlydate").value = currentLowSettingStr;
            }
        }
    }
    else{
        document.getElementById("datesliderearlydate").value = currentLowSettingStr;
    }
}

function checkDSSaveImage(){
    const imageSave = document.getElementById("dateslideranimimagesave").checked;
    const duration = document.getElementById("datesliderinterduration").value;
    if(imageSave){
        if(duration){
            const lowDate = document.getElementById("datesliderearlydate").value;
            let hLowDate = new Date(lowDate);
            hLowDate = new Date(hLowDate.setTime(hLowDate.getTime()+86400000));
            const highDate = document.getElementById("datesliderlatedate").value;
            let hHighDate = new Date(highDate);
            hHighDate = new Date(hHighDate.setTime(hHighDate.getTime()+86400000));
            let difference = (hHighDate - hLowDate) / 1000;
            difference /= (60*60*24);
            const diffYears = difference / 365.25;
            const imageCount = Math.ceil(diffYears / duration);
            if(!confirm("With the current interval duration and date settings, this will produce "+imageCount+" images. Click OK to continue.")){
                document.getElementById("dateslideranimimagesave").checked = false;
            }
        }
        else{
            alert("Please enter an interval duration before selecting to save images.");
            document.getElementById("dateslideranimimagesave").checked = false;
        }
    }
}

function checkLoading(){
    if(!loadingComplete){
        loadingComplete = true;
        loadPointsEvent = false;
        hideWorking();
    }
}

function checkObjectNotEmpty(obj){
    for(const i in obj){
        if(obj.hasOwnProperty(i) && obj[i]){
            return true;
        }
    }
    return false;
}

function checkPointToolSource(selector){
    if(!(selections.length >= 3)){
        document.getElementById(selector).value = 'all';
        alert('There must be at least 3 selected points on the map.');
    }
}

function cleanSelectionsLayer(){
    const selLayerFeatures = layersArr['select'].getSource().getFeatures();
    const currentlySelected = selectInteraction.getFeatures().getArray();
    for(let i in selLayerFeatures){
        if(selLayerFeatures.hasOwnProperty(i) && currentlySelected.indexOf(selLayerFeatures[i]) === -1){
            layersArr['select'].getSource().removeFeature(selLayerFeatures[i]);
        }
    }
}

function clearLayerQuerySelector() {
    document.getElementById('spatialQueryFieldSelector').innerHTML = '';
    document.getElementById('spatialQueryOperatorSelector').value = 'equals';
    document.getElementById('spatialQuerySingleValueDiv').style.display = 'block';
    document.getElementById('spatialQueryBetweenValueDiv').style.display = 'none';
    document.getElementById('spatialQuerySingleValueInput').value = '';
    document.getElementById('spatialQueryDoubleValueInput1').value = '';
    document.getElementById('spatialQueryDoubleValueInput2').value = '';
    document.getElementById('spatialQuerySelectorLayerId').value = '';
}

function clearSelections(){
    const selpoints = selections;
    selections = [];
    for(let i in selpoints){
        if(selpoints.hasOwnProperty(i) && !clusterPoints){
            const point = findOccPoint(selpoints[i]);
            const style = setSymbol(point);
            point.setStyle(style);
        }
    }
    layersArr['pointv'].getSource().changed();
    adjustSelectionsTab();
    document.getElementById("selectiontbody").innerHTML = '';
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

function closeOccidInfoBox(){
    finderpopupcloser.onclick();
}

function convertMysqlWKT(wkt) {
    let long;
    let lat;
    let wktStr = '';
    let adjustedStr = '';
    let coordStr = '';
    if(wkt.substring(0,7) === 'POLYGON'){
        adjustedStr = wkt.substring(8,wkt.length-1);
        const adjustedStrArr = adjustedStr.split('),');
        for(let ps in adjustedStrArr){
            if(adjustedStrArr.hasOwnProperty(ps)){
                coordStr += '(';
                let subStr = adjustedStrArr[ps].substring(1,adjustedStrArr[ps].length);
                if(adjustedStrArr[ps].substring(adjustedStrArr[ps].length - 1,adjustedStrArr[ps].length) === ')'){
                    subStr = subStr.substring(0,subStr.length - 1);
                }
                const subStrArr = subStr.split(',');
                for(let ss in subStrArr){
                    if(subStrArr.hasOwnProperty(ss)){
                        const geocoords = subStrArr[ss].split(' ');
                        lat = geocoords[0];
                        long = geocoords[1];
                        coordStr += long+' '+lat+',';
                    }
                }
                coordStr = coordStr.substring(0,coordStr.length-1);
                coordStr += '),';
            }
        }
        coordStr = coordStr.substring(0,coordStr.length-1);
        wktStr = 'POLYGON('+coordStr+')';
    }
    else if(wkt.substring(0,12) === 'MULTIPOLYGON'){
        adjustedStr = wkt.substring(13,wkt.length-1);
        const adjustedStrArr = adjustedStr.split(')),');
        for(let ps in adjustedStrArr){
            if(adjustedStrArr.hasOwnProperty(ps)){
                coordStr += '(';
                const subStr = adjustedStrArr[ps].substring(2,adjustedStrArr[ps].length);
                const subStrArr = subStr.split('),');
                for(let ss in subStrArr){
                    if(subStrArr.hasOwnProperty(ss)){
                        coordStr += '(';
                        if(subStrArr[ss].substring(subStrArr[ss].length - 2,subStrArr[ss].length) === '))'){
                            subStrArr[ss] = subStrArr[ss].substring(0,subStrArr[ss].length - 2);
                        }
                        const subSubStrArr = subStrArr[ss].split(',');
                        for(let sss in subSubStrArr){
                            if(subSubStrArr.hasOwnProperty(sss)){
                                const geocoords = subSubStrArr[sss].split(' ');
                                lat = geocoords[0];
                                long = geocoords[1];
                                coordStr += long+' '+lat+',';
                            }
                        }
                        coordStr = coordStr.substring(0,coordStr.length-1);
                        coordStr += '),';
                    }
                }
                coordStr = coordStr.substring(0,coordStr.length-1);
                coordStr += '),';
            }
        }
        coordStr = coordStr.substring(0,coordStr.length-1);
        wktStr = 'MULTIPOLYGON('+coordStr+')';
    }

    return wktStr;
}

function coordFormat(){
    return(function(coord1){
        mouseCoords = coord1;
        if(coord1[0] < -180){
            coord1[0] = coord1[0] + 360;
        }
        if(coord1[0] > 180){
            coord1[0] = coord1[0] - 360;
        }
        const template = 'Lat: {y} Lon: {x}';
        return ol.coordinate.format(coord1,template,5);
    });
}

function createBuffers(){
    const bufferSize = document.getElementById("bufferSize").value;
    if(bufferSize === '' || isNaN(bufferSize)) {
        alert("Please enter a number for the buffer size.");
    }
    else if(selectInteraction.getFeatures().getArray().length >= 1){
        selectInteraction.getFeatures().forEach(function(feature){
            let turfFeature;
            if(feature){
                const selectedClone = feature.clone();
                const geoType = selectedClone.getGeometry().getType();
                const geoJSONFormat = new ol.format.GeoJSON();
                const selectiongeometry = selectedClone.getGeometry();
                const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                const featCoords = JSON.parse(geojsonStr).coordinates;
                if(geoType === 'Point'){
                    turfFeature = turf.point(featCoords);
                }
                else if(geoType === 'LineString'){
                    turfFeature = turf.lineString(featCoords);
                }
                else if(geoType === 'Polygon'){
                    turfFeature = turf.polygon(featCoords);
                }
                else if(geoType === 'MultiPolygon'){
                    turfFeature = turf.multiPolygon(featCoords);
                }
                else if(geoType === 'Circle'){
                    const center = fixedselectgeometry.getCenter();
                    const radius = fixedselectgeometry.getRadius();
                    const edgeCoordinate = [center[0] + radius, center[1]];
                    let groundRadius = ol.sphere.getDistance(
                        ol.proj.transform(center, 'EPSG:4326', 'EPSG:4326'),
                        ol.proj.transform(edgeCoordinate, 'EPSG:4326', 'EPSG:4326')
                    );
                    groundRadius = groundRadius/1000;
                    turfFeature = getWGS84CirclePoly(center,groundRadius);
                }
                const buffered = turf.buffer(turfFeature, bufferSize, {units: 'kilometers'});
                const buffpoly = geoJSONFormat.readFeature(buffered);
                buffpoly.getGeometry().transform(wgs84Projection,mapProjection);
                selectsource.addFeature(buffpoly);
            }
        });
        document.getElementById("bufferSize").value = '';
    }
    else{
        alert('You must have at least one shape selected in your Shapes layer to create a buffer polygon.');
    }
}

function createCirclesFromCircleArr(circleArr, selected){
    for(let i in circleArr){
        if(circleArr.hasOwnProperty(i)){
            const centerCoords = ol.proj.fromLonLat([circleArr[i].pointlong, circleArr[i].pointlat]);
            const circle = new ol.geom.Circle(centerCoords);
            circle.setRadius(Number(circleArr[i].radius));
            const circleFeature = new ol.Feature(circle);
            selectsource.addFeature(circleFeature);
            document.getElementById("selectlayerselect").value = 'select';
            setActiveLayer();
            if(selected){
                selectedFeatures.push(circleFeature);
            }
        }
    }
    document.getElementById("selectlayerselect").value = 'select';
    setActiveLayer();
}

function createCircleFromPointRadius(prad, selected){
    const centerCoords = ol.proj.fromLonLat([prad.pointlong, prad.pointlat]);
    const circle = new ol.geom.Circle(centerCoords);
    circle.setRadius(Number(prad.radius));
    const circleFeature = new ol.Feature(circle);
    selectsource.addFeature(circleFeature);
    document.getElementById("selectlayerselect").value = 'select';
    setActiveLayer();
    if(selected){
        selectedFeatures.push(circleFeature);
    }
}

function createConcavePoly(){
    const source = document.getElementById('concavepolysource').value;
    const maxEdge = document.getElementById('concaveMaxEdgeSize').value;
    let features = [];
    const geoJSONFormat = new ol.format.GeoJSON();
    if(maxEdge !== '' && !isNaN(maxEdge) && maxEdge > 0){
        if(source === 'all'){
            features = getTurfPointFeaturesetAll();
        }
        else if(source === 'selected'){
            if(selections.length >= 3){
                features = getTurfPointFeaturesetSelected();
            }
            else{
                document.getElementById('concavepolysource').value = 'all';
                alert('There must be at least 3 selected points on the map. Please either select more points or re-run this tool for all points.');
                return;
            }
        }
        if(features){
            let concavepoly = '';
            try{
                const options = {units: 'kilometers', maxEdge: Number(maxEdge)};
                concavepoly = turf.concave(features,options);
            }
            catch(e){
                alert('Concave polygon was not able to be calculated. Perhaps try using a larger value for the maximum edge length.');
            }
            if(concavepoly){
                const cnvepoly = geoJSONFormat.readFeature(concavepoly);
                cnvepoly.getGeometry().transform(wgs84Projection,mapProjection);
                selectsource.addFeature(cnvepoly);
            }
        }
        else{
            alert('There must be at least 3 points on the map to calculate polygon.');
        }
        document.getElementById('concavepolysource').value = 'all';
        document.getElementById('concaveMaxEdgeSize').value = '';
    }
    else{
        alert('Please enter a number for the maximum edge size.');
    }
}

function createConvexPoly(){
    const source = document.getElementById('convexpolysource').value;
    let features = [];
    const geoJSONFormat = new ol.format.GeoJSON();
    if(source === 'all'){
        features = getTurfPointFeaturesetAll();
    }
    else if(source === 'selected'){
        if(selections.length >= 3){
            features = getTurfPointFeaturesetSelected();
        }
        else{
            document.getElementById('convexpolysource').value = 'all';
            alert('There must be at least 3 selected points on the map. Please either select more points or re-run this tool for all points.');
            return;
        }
    }
    if(features){
        const convexpoly = turf.convex(features);
        if(convexpoly){
            const cnvxpoly = geoJSONFormat.readFeature(convexpoly);
            cnvxpoly.getGeometry().transform(wgs84Projection,mapProjection);
            selectsource.addFeature(cnvxpoly);
        }
    }
    else{
        alert('There must be at least 3 points on the map to calculate polygon.');
    }
    document.getElementById('convexpolysource').value = 'all';
}

function createDateSlider(dual){
    if(dsOldestDate && dsNewestDate){
        sliderdiv = document.createElement('div');
        sliderdiv.setAttribute("id","sliderdiv");
        sliderdiv.setAttribute("style","width:calc(95% - 250px);height:30px;bottom:10px;left:50px;display:block;position:absolute;z-index:3;");
        const minhandlediv = document.createElement('div');
        minhandlediv.setAttribute("id","custom-handle-min");
        minhandlediv.setAttribute("class","ui-slider-handle");
        const minlabeldiv = document.createElement('div');
        minlabeldiv.setAttribute("id","custom-label-min");
        minlabeldiv.setAttribute("class","custom-label");
        const minlabelArrowdiv = document.createElement('div');
        minlabelArrowdiv.setAttribute("id","custom-label-min-arrow");
        minlabelArrowdiv.setAttribute("class","label-arrow");
        minhandlediv.appendChild(minlabeldiv);
        minhandlediv.appendChild(minlabelArrowdiv);
        sliderdiv.appendChild(minhandlediv);
        const maxhandlediv = document.createElement('div');
        maxhandlediv.setAttribute("id","custom-handle-max");
        maxhandlediv.setAttribute("class","ui-slider-handle");
        const maxlabeldiv = document.createElement('div');
        maxlabeldiv.setAttribute("id","custom-label-max");
        maxlabeldiv.setAttribute("class","custom-label");
        maxhandlediv.appendChild(maxlabeldiv);
        const maxlabelArrowdiv = document.createElement('div');
        maxlabelArrowdiv.setAttribute("class","label-arrow");
        maxhandlediv.appendChild(maxlabeldiv);
        maxhandlediv.appendChild(maxlabelArrowdiv);
        sliderdiv.appendChild(maxhandlediv);
        document.body.appendChild(sliderdiv);

        const minDate = dsOldestDate.getTime();
        const maxDate = dsNewestDate.getTime();
        tsOldestDate = dsOldestDate;
        tsNewestDate = dsNewestDate;
        const hMinDate = new Date(minDate);
        const minDateStr = getISOStrFromDateObj(hMinDate);
        const hMaxDate = new Date(maxDate);
        const maxDateStr = getISOStrFromDateObj(hMaxDate);

        const minhandle = $("#custom-handle-min");
        const maxhandle = $("#custom-handle-max");
        $("#sliderdiv").slider({
            range: true,
            min: minDate,
            max: maxDate,
            values: [minDate,maxDate],
            create: function() {
                if(dual){
                    const mintextbox = $("#custom-label-min");
                    mintextbox.text(minDateStr);
                }
                const maxtextbox = $("#custom-label-max");
                maxtextbox.text(maxDateStr);
            },
            step: 1000 * 60 * 60 * 24,
            slide: function(event, ui) {
                if(dual){
                    const mintextbox = $("#custom-label-min");
                    tsOldestDate = new Date(ui.values[0]);
                    const newMinDateStr = getISOStrFromDateObj(tsOldestDate);
                    mintextbox.text(newMinDateStr);
                    document.getElementById("datesliderearlydate").value = newMinDateStr;
                }
                const maxtextbox = $("#custom-label-max");
                tsNewestDate = new Date(ui.values[1]);
                const newMaxDateStr = getISOStrFromDateObj(tsNewestDate);
                maxtextbox.text(newMaxDateStr);
                document.getElementById("datesliderlatedate").value = newMaxDateStr;
                layersArr['pointv'].getSource().changed();
            }
        });
        if(!dual){
            document.getElementById("custom-handle-min").style.display = 'none';
            document.getElementById("custom-handle-min").style.position = 'absolute';
            document.getElementById("custom-handle-min").style.left = '-9999px';
        }
        document.getElementById("datesliderearlydate").value = minDateStr;
        document.getElementById("datesliderlatedate").value = maxDateStr;
        document.getElementById("dateslidercontrol").style.display = 'block';
        document.getElementById("maptoolcontainer").style.top = 'initial';
        document.getElementById("maptoolcontainer").style.left = 'initial';
        document.getElementById("maptoolcontainer").style.bottom = '100px';
        document.getElementById("maptoolcontainer").style.right = '-190px';
    }
}

function createPolyDifference(){
    let shapeCount = 0;
    selectInteraction.getFeatures().forEach(function(feature){
        const selectedClone = feature.clone();
        const geoType = selectedClone.getGeometry().getType();
        if(geoType === 'Polygon' || geoType === 'MultiPolygon' || geoType === 'Circle'){
            shapeCount++;
        }
    });
    if(shapeCount === 2){
        const features = [];
        const geoJSONFormat = new ol.format.GeoJSON();
        selectInteraction.getFeatures().forEach(function(feature){
            if(feature){
                const selectedClone = feature.clone();
                const geoType = selectedClone.getGeometry().getType();
                const selectiongeometry = selectedClone.getGeometry();
                const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                const featCoords = JSON.parse(geojsonStr).coordinates;
                if(geoType === 'Polygon'){
                    features.push(turf.polygon(featCoords));
                }
                else if(geoType === 'MultiPolygon'){
                    features.push(turf.multiPolygon(featCoords));
                }
                else if(geoType === 'Circle'){
                    const center = fixedselectgeometry.getCenter();
                    const radius = fixedselectgeometry.getRadius();
                    const edgeCoordinate = [center[0] + radius, center[1]];
                    let groundRadius = ol.sphere.getDistance(
                        ol.proj.transform(center, 'EPSG:4326', 'EPSG:4326'),
                        ol.proj.transform(edgeCoordinate, 'EPSG:4326', 'EPSG:4326')
                    );
                    groundRadius = groundRadius/1000;
                    features.push(getWGS84CirclePoly(center,groundRadius));
                }
            }
        });
        const difference = turf.difference(features[0], features[1]);
        if(difference){
            const diffpoly = geoJSONFormat.readFeature(difference);
            diffpoly.getGeometry().transform(wgs84Projection,mapProjection);
            selectsource.addFeature(diffpoly);
        }
    }
    else{
        alert('You must have two polygons or circles, and only two polygons or circles, selected in your Shapes layer to find the difference.');
    }
}

function createPolygonFromBoundingBox(bbox, selected){
    const coordArr = [];
    const ringArr = [];
    const geoJSONFormat = new ol.format.GeoJSON();
    coordArr.push([bbox.leftlong, bbox.bottomlat]);
    coordArr.push([bbox.rightlong, bbox.bottomlat]);
    coordArr.push([bbox.rightlong, bbox.upperlat]);
    coordArr.push([bbox.leftlong, bbox.upperlat]);
    coordArr.push([bbox.leftlong, bbox.bottomlat]);
    ringArr.push(coordArr);
    const newTurfPolygon = turf.polygon(ringArr);
    const newpoly = geoJSONFormat.readFeature(newTurfPolygon);
    newpoly.getGeometry().transform(wgs84Projection,mapProjection);
    newpoly.set('geoType','Box');
    selectsource.addFeature(newpoly);
    document.getElementById("selectlayerselect").value = 'select';
    setActiveLayer();
    if(selected){
        selectedFeatures.push(newpoly);
    }
}

function createPolyIntersect(){
    let shapeCount = 0;
    selectInteraction.getFeatures().forEach(function(feature){
        const selectedClone = feature.clone();
        const geoType = selectedClone.getGeometry().getType();
        if(geoType === 'Polygon' || geoType === 'MultiPolygon' || geoType === 'Circle'){
            shapeCount++;
        }
    });
    if(shapeCount === 2){
        const featuresOne = [];
        const featuresTwo = [];
        let pass = 1;
        let intersection;
        const geoJSONFormat = new ol.format.GeoJSON();
        selectInteraction.getFeatures().forEach(function(feature){
            if(feature){
                const selectedClone = feature.clone();
                const geoType = selectedClone.getGeometry().getType();
                if(geoType === 'Polygon' || geoType === 'MultiPolygon' || geoType === 'Circle'){
                    const selectiongeometry = selectedClone.getGeometry();
                    const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                    const geojsonStr = geoJSONFormat.writeGeometry(selectiongeometry);
                    const featCoords = JSON.parse(geojsonStr).coordinates;
                    if(geoType === 'Polygon'){
                        if(pass === 1){
                            featuresOne.push(turf.polygon(featCoords));
                        }
                        else{
                            featuresTwo.push(turf.polygon(featCoords));
                        }
                    }
                    else if(geoType === 'MultiPolygon'){
                        for (let e in featCoords) {
                            if(featCoords.hasOwnProperty(e)){
                                if(pass === 1){
                                    featuresOne.push(turf.polygon(featCoords[e]));
                                }
                                else{
                                    featuresTwo.push(turf.polygon(featCoords[e]));
                                }
                            }
                        }
                    }
                    else if(geoType === 'Circle'){
                        const center = fixedselectgeometry.getCenter();
                        const radius = fixedselectgeometry.getRadius();
                        const edgeCoordinate = [center[0] + radius, center[1]];
                        let groundRadius = ol.sphere.getDistance(
                            ol.proj.transform(center, 'EPSG:4326', 'EPSG:4326'),
                            ol.proj.transform(edgeCoordinate, 'EPSG:4326', 'EPSG:4326')
                        );
                        groundRadius = groundRadius/1000;
                        if(pass === 1){
                            featuresOne.push(getWGS84CirclePoly(center,groundRadius));
                        }
                        else{
                            featuresTwo.push(getWGS84CirclePoly(center,groundRadius));
                        }
                    }
                    pass++;
                }
            }
        });
        for (let i in featuresOne) {
            if(featuresOne.hasOwnProperty(i)){
                for (let e in featuresTwo) {
                    if(featuresTwo.hasOwnProperty(e)){
                        const tempPoly = turf.intersect(featuresOne[i], featuresTwo[e]);
                        if(tempPoly){
                            if(intersection){
                                intersection = turf.union(intersection,tempPoly);
                            }
                            else{
                                intersection = tempPoly;
                            }
                        }
                    }
                }
            }
        }
        if(intersection){
            const interpoly = geoJSONFormat.readFeature(intersection);
            interpoly.getGeometry().transform(wgs84Projection,mapProjection);
            selectsource.addFeature(interpoly);
        }
        else{
            alert('The two selected shapes do not intersect.');
        }
    }
    else{
        alert('You must have two polygons or circles, and only two polygons or circles, selected in your Shapes layer to find the intersect.');
    }
}

function createPolysFromPolyArr(polyArr, selected){
    const wktFormat = new ol.format.WKT();
    for(let i in polyArr){
        if(polyArr.hasOwnProperty(i)){
            let wktStr = '';
            if(SOLRMODE){
                wktStr = polyArr[i];
            }
            else{
                wktStr = convertMysqlWKT(polyArr[i]);
            }
            const newpoly = wktFormat.readFeature(wktStr, mapProjection);
            newpoly.getGeometry().transform(wgs84Projection,mapProjection);
            selectsource.addFeature(newpoly);
            if(selected){
                selectedFeatures.push(newpoly);
            }
        }
    }
    document.getElementById("selectlayerselect").value = 'select';
    setActiveLayer();
}

function createPolyUnion(){
    let shapeCount = 0;
    selectInteraction.getFeatures().forEach(function(feature){
        const selectedClone = feature.clone();
        const geoType = selectedClone.getGeometry().getType();
        if(geoType === 'Polygon' || geoType === 'MultiPolygon' || geoType === 'Circle'){
            shapeCount++;
        }
    });
    if(shapeCount > 1){
        const features = [];
        const geoJSONFormat = new ol.format.GeoJSON();
        selectInteraction.getFeatures().forEach(function(feature){
            if(feature){
                const selectedClone = feature.clone();
                const geoType = selectedClone.getGeometry().getType();
                const selectiongeometry = selectedClone.getGeometry();
                const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                const featCoords = JSON.parse(geojsonStr).coordinates;
                if(geoType === 'Polygon'){
                    features.push(turf.polygon(featCoords));
                }
                else if(geoType === 'MultiPolygon'){
                    features.push(turf.multiPolygon(featCoords));
                }
                else if(geoType === 'Circle'){
                    const center = fixedselectgeometry.getCenter();
                    const radius = fixedselectgeometry.getRadius();
                    const edgeCoordinate = [center[0] + radius, center[1]];
                    let groundRadius = ol.sphere.getDistance(
                        ol.proj.transform(center, 'EPSG:4326', 'EPSG:4326'),
                        ol.proj.transform(edgeCoordinate, 'EPSG:4326', 'EPSG:4326')
                    );
                    groundRadius = groundRadius/1000;
                    features.push(getWGS84CirclePoly(center,groundRadius));
                }
            }
        });
        let union = turf.union(features[0], features[1]);
        for (let f in features){
            if(features.hasOwnProperty(f) && f > 1){
                union = turf.union(union,features[f]);
            }
        }
        if(union){
            deleteSelections();
            const unionpoly = geoJSONFormat.readFeature(union);
            unionpoly.getGeometry().transform(wgs84Projection,mapProjection);
            selectsource.addFeature(unionpoly);
            document.getElementById("selectlayerselect").value = 'select';
            setActiveLayer();
        }
    }
    else{
        alert('You must have at least two polygons or circles selected in your Shapes layer to find the union.');
    }
}

function createShapesFromSearchTermsArr(){
    if(searchTermsArr.hasOwnProperty('upperlat')){
        const boundingBox = {};
        boundingBox.upperlat = searchTermsArr['upperlat'];
        boundingBox.bottomlat = searchTermsArr['bottomlat'];
        boundingBox.leftlong = searchTermsArr['leftlong'];
        boundingBox.rightlong = searchTermsArr['rightlong'];
        if(boundingBox.upperlat && boundingBox.bottomlat && boundingBox.leftlong && boundingBox.rightlong){
            createPolygonFromBoundingBox(boundingBox, true);
        }
    }
    if(searchTermsArr.hasOwnProperty('pointlat')){
        const pointRadius = {};
        pointRadius.pointlat = searchTermsArr['pointlat'];
        pointRadius.pointlong = searchTermsArr['pointlong'];
        pointRadius.radius = searchTermsArr['radius'];
        if(pointRadius.pointlat && pointRadius.pointlong && pointRadius.radius){
            createCircleFromPointRadius(pointRadius, true);
        }
    }
    if(searchTermsArr.hasOwnProperty('circleArr')){
        let circleArr;
        if(JSON.parse(searchTermsArr['circleArr'])){
            circleArr = JSON.parse(searchTermsArr['circleArr']);
        }
        else{
            circleArr = searchTermsArr['circleArr'];
        }
        if(Array.isArray(circleArr)){
            createCirclesFromCircleArr(circleArr, true);
        }
    }
    if(searchTermsArr.hasOwnProperty('polyArr')){
        let polyArr;
        if(JSON.parse(searchTermsArr['polyArr'])){
            polyArr = JSON.parse(searchTermsArr['polyArr']);
        }
        else{
            polyArr = searchTermsArr['polyArr'];
        }
        if(Array.isArray(polyArr)){
            createPolysFromPolyArr(polyArr, true);
        }
    }
}

function createUncertaintyCircleFromPointRadius(prad){
    const centerCoords = ol.proj.fromLonLat([prad.pointlong, prad.pointlat]);
    const circle = new ol.geom.Circle(centerCoords);
    circle.setRadius(Number(prad.radius));
    const circleFeature = new ol.Feature(circle);
    uncertaintycirclesource.addFeature(circleFeature);
}

function deactivateClustering(){
    document.getElementById("clusterswitch").checked = false;
    changeClusterSetting();
}

function deleteSelections(){
    selectInteraction.getFeatures().forEach(function(feature){
        layersArr['select'].getSource().removeFeature(feature);
    });
    selectInteraction.getFeatures().clear();
    if(layersArr['select'].getSource().getFeatures().length < 1){
        removeUserLayer('select');
    }
}

function downloadShapesLayer(){
    let filetype;
    const dlType = document.getElementById("shapesdownloadselect").value;
    let format = '';
    if(dlType === ''){
        alert('Please select a download format.');
        return;
    }
    else if(dlType === 'kml'){
        format = new ol.format.KML();
        filetype = 'application/vnd.google-earth.kml+xml';
    }
    else if(dlType === 'geojson'){
        format = new ol.format.GeoJSON();
        filetype = 'application/vnd.geo+json';
    }
    const features = layersArr['select'].getSource().getFeatures();
    const fixedFeatures = setDownloadFeatures(features);
    let exportStr = format.writeFeatures(fixedFeatures, {
        'dataProjection': wgs84Projection,
        'featureProjection': mapProjection
    });
    if(dlType === 'kml'){
        exportStr = exportStr.replaceAll(/<kml xmlns="http:\/\/www.opengis.net\/kml\/2.2" xmlns:gx="http:\/\/www.google.com\/kml\/ext\/2.2" xmlns:xsi="http:\/\/www.w3.org\/2001\/XMLSchema-instance" xsi:schemaLocation="http:\/\/www.opengis.net\/kml\/2.2 https:\/\/developers.google.com\/kml\/schema\/kml22gx.xsd">/g,'<kml xmlns="http://www.opengis.net/kml/2.2"><Document id="root_doc"><Folder><name>shapes_export</name>');
        exportStr = exportStr.replaceAll(/<Placemark>/g,'<Placemark><Style><LineStyle><color>ff000000</color><width>1</width></LineStyle><PolyStyle><color>4DAAAAAA</color><fill>1</fill></PolyStyle></Style>');
        exportStr = exportStr.replaceAll(/<Polygon>/g,'<Polygon><altitudeMode>clampToGround</altitudeMode>');
        exportStr = exportStr.replaceAll(/<\/kml>/g,'</Folder></Document></kml>');
    }
    const filename = 'shapes_' + getDateTimeString() + '.' + dlType;
    const blob = new Blob([exportStr], {type: filetype});
    if(window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveBlob(blob, filename);
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

function exportMapPNG(filename,zip){
    let mapCanvas = document.createElement('canvas');
    const size = map.getSize();
    mapCanvas.width = size[0];
    mapCanvas.height = size[1];
    const mapContext = mapCanvas.getContext('2d');
    Array.prototype.forEach.call(
        map.getViewport().querySelectorAll('.ol-layer canvas, canvas.ol-layer'),
        function (canvas) {
            if (canvas.width > 0) {
                const opacity =
                    canvas.parentNode.style.opacity || canvas.style.opacity;
                mapContext.globalAlpha = opacity === '' ? 1 : Number(opacity);

                const backgroundColor = canvas.parentNode.style.backgroundColor;
                if (backgroundColor) {
                    mapContext.fillStyle = backgroundColor;
                    mapContext.fillRect(0, 0, canvas.width, canvas.height);
                }

                let matrix;
                const transform = canvas.style.transform;
                if (transform) {
                    matrix = transform
                        .match(/^matrix\(([^\(]*)\)$/)[1]
                        .split(',')
                        .map(Number);
                }
                else {
                    matrix = [
                        parseFloat(canvas.style.width) / canvas.width,
                        0,
                        0,
                        parseFloat(canvas.style.height) / canvas.height,
                        0,
                        0,
                    ];
                }
                CanvasRenderingContext2D.prototype.setTransform.apply(
                    mapContext,
                    matrix
                );
                mapContext.drawImage(canvas, 0, 0);
            }
        }
    );
    if (navigator.msSaveBlob) {
        navigator.msSaveBlob(mapCanvas.msToBlob(), filename);
        mapCanvas = '';
    }
    else {
        mapCanvas.toBlob(function(blob) {
            saveAs(blob,filename);
            mapCanvas = '';
        });
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

function findOccCluster(occid){
    const clusters = layersArr['pointv'].getSource().getFeatures();
    for(let c in clusters){
        if(clusters.hasOwnProperty(c)){
            const clusterindex = clusters[c].get('identifiers');
            if(clusterindex.indexOf(Number(occid)) !== -1){
                return clusters[c];
            }
        }
    }
}

function findOccClusterPosition(occid){
    if(spiderCluster){
        const spiderPoints = layersArr['spider'].getSource().getFeatures();
        for(let p in spiderPoints){
            if(spiderPoints.hasOwnProperty(p) && Number(spiderPoints[p].get('features')[0].get('occid')) === occid){
                return spiderPoints[p].getGeometry().getCoordinates();
            }
        }
    }
    else if(clusterPoints){
        const clusters = layersArr['pointv'].getSource().getFeatures();
        for(let c in clusters){
            if(clusters.hasOwnProperty(c)){
                const clusterindex = clusters[c].get('identifiers');
                if(clusterindex.indexOf(occid) !== -1){
                    return clusters[c].getGeometry().getCoordinates();
                }
            }
        }
    }
    else{
        const features = layersArr['pointv'].getSource().getFeatures();
        for(let f in features){
            if(features.hasOwnProperty(f) && Number(features[f].get('occid')) === occid){
                return features[f].getGeometry().getCoordinates();
            }
        }
    }
}

function findOccPoint(occid){
    const features = layersArr['pointv'].getSource().getFeatures();
    for(let f in features){
        if(features.hasOwnProperty(f) && Number(features[f].get('occid')) === occid){
            return features[f];
        }
    }
}

function findOccPointInCluster(cluster,occid){
    const cFeatures = cluster.get('features');
    for (let f in cFeatures) {
        if(cFeatures.hasOwnProperty(f) && Number(cFeatures[f].get('occid')) === occid){
            return cFeatures[f];
        }
    }
}

function generateReclassifySLD(valueArr,layername){
    let sldContent = '';
    sldContent += '<?xml version="1.0" encoding="UTF-8"?>';
    sldContent += '<StyledLayerDescriptor version="1.0.0" ';
    sldContent += 'xsi:schemaLocation="http://www.opengis.net/sld StyledLayerDescriptor.xsd" ';
    sldContent += 'xmlns="http://www.opengis.net/sld" ';
    sldContent += 'xmlns:ogc="http://www.opengis.net/ogc" ';
    sldContent += 'xmlns:xlink="http://www.w3.org/1999/xlink" ';
    sldContent += 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
    sldContent += '<NamedLayer>';
    sldContent += '<Name>'+layername+'</Name>';
    sldContent += '<UserStyle>';
    sldContent += '<FeatureTypeStyle>';
    sldContent += '<Rule>';
    sldContent += '<Name>reclassify_style</Name>';
    sldContent += '<RasterSymbolizer>';
    sldContent += '<Opacity>1.0</Opacity>';
    sldContent += '<ColorMap type="intervals">';
    sldContent += '<ColorMapEntry color="#FFFFFF" quantity="'+valueArr['rasmin']+'"/>';
    sldContent += '<ColorMapEntry color="#'+valueArr['color']+'" quantity="'+valueArr['rasmax']+'"/>';
    sldContent += '</ColorMap>';
    sldContent += '</RasterSymbolizer>';
    sldContent += '</Rule>';
    sldContent += '</FeatureTypeStyle>';
    sldContent += '</UserStyle>';
    sldContent += '</NamedLayer>';
    sldContent += '</StyledLayerDescriptor>';
    return sldContent;
}

function generateWPSPolyExtractXML(valueArr,layername,geojsonstr){
    let xmlContent = '';
    xmlContent += '<?xml version="1.0" encoding="UTF-8"?><wps:Execute version="1.0.0" service="WPS" ';
    xmlContent += 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.opengis.net/wps/1.0.0" ';
    xmlContent += 'xmlns:wfs="http://www.opengis.net/wfs" xmlns:wps="http://www.opengis.net/wps/1.0.0" ';
    xmlContent += 'xmlns:ows="http://www.opengis.net/ows/1.1" xmlns:gml="http://www.opengis.net/gml" ';
    xmlContent += 'xmlns:ogc="http://www.opengis.net/ogc" xmlns:wcs="http://www.opengis.net/wcs/1.1.1" ';
    xmlContent += 'xmlns:xlink="http://www.w3.org/1999/xlink" ';
    xmlContent += 'xsi:schemaLocation="http://www.opengis.net/wps/1.0.0 http://schemas.opengis.net/wps/1.0.0/wpsAll.xsd">';
    xmlContent += '<ows:Identifier>ras:PolygonExtraction</ows:Identifier>';
    xmlContent += '<wps:DataInputs>';
    xmlContent += '<wps:Input>';
    xmlContent += '<ows:Identifier>data</ows:Identifier>';
    xmlContent += '<wps:Reference mimeType="image/tiff" xlink:href="http://geoserver/wcs" method="POST">';
    xmlContent += '<wps:Body>';
    xmlContent += '<wcs:GetCoverage service="WCS" version="1.1.1">';
    xmlContent += '<ows:Identifier>'+layername+'</ows:Identifier>';
    xmlContent += '<wcs:DomainSubset>';
    xmlContent += '<ows:BoundingBox crs="http://www.opengis.net/gml/srs/epsg.xml#4326">';
    xmlContent += '<ows:LowerCorner>-180.0 -90.0</ows:LowerCorner>';
    xmlContent += '<ows:UpperCorner>180.0 90.0</ows:UpperCorner>';
    xmlContent += '</ows:BoundingBox>';
    xmlContent += '</wcs:DomainSubset>';
    xmlContent += '<wcs:Output format="image/tiff"/>';
    xmlContent += '</wcs:GetCoverage>';
    xmlContent += '</wps:Body>';
    xmlContent += '</wps:Reference>';
    xmlContent += '</wps:Input>';
    xmlContent += '<wps:Input>';
    xmlContent += '<ows:Identifier>band</ows:Identifier>';
    xmlContent += '<wps:Data>';
    xmlContent += '<wps:LiteralData>0</wps:LiteralData>';
    xmlContent += '</wps:Data>';
    xmlContent += '</wps:Input>';
    xmlContent += '<wps:Input>';
    xmlContent += '<ows:Identifier>insideEdges</ows:Identifier>';
    xmlContent += '<wps:Data>';
    xmlContent += '<wps:LiteralData>0</wps:LiteralData>';
    xmlContent += '</wps:Data>';
    xmlContent += '</wps:Input>';
    xmlContent += '<wps:Input>';
    xmlContent += '<ows:Identifier>roi</ows:Identifier>';
    xmlContent += '<wps:Data>';
    xmlContent += '<wps:ComplexData mimeType="application/json"><![CDATA['+geojsonstr+']]></wps:ComplexData>';
    xmlContent += '</wps:Data>';
    xmlContent += '</wps:Input>';
    xmlContent += '<wps:Input>';
    xmlContent += '<ows:Identifier>nodata</ows:Identifier>';
    xmlContent += '<wps:Data>';
    xmlContent += '<wps:LiteralData>0</wps:LiteralData>';
    xmlContent += '</wps:Data>';
    xmlContent += '</wps:Input>';
    xmlContent += '<wps:Input>';
    xmlContent += '<ows:Identifier>ranges</ows:Identifier>';
    xmlContent += '<wps:Data>';
    xmlContent += '<wps:LiteralData>('+valueArr['rasmin']+';'+valueArr['rasmax']+')</wps:LiteralData>';
    xmlContent += '</wps:Data>';
    xmlContent += '</wps:Input>';
    xmlContent += '</wps:DataInputs>';
    xmlContent += '<wps:ResponseForm>';
    xmlContent += '<wps:RawDataOutput mimeType="application/json">';
    xmlContent += '<ows:Identifier>result</ows:Identifier>';
    xmlContent += '</wps:RawDataOutput>';
    xmlContent += '</wps:ResponseForm>';
    xmlContent += '</wps:Execute>';
    return xmlContent;
}

function getArrayBuffer(file) {
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.readAsArrayBuffer(file);
        reader.onload = () => {
            const arrayBuffer = reader.result;
            const bytes = new Uint8Array(arrayBuffer);
            resolve(bytes);
        };
    });
}

function getTextBlob(file) {
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.readAsText(file);
        reader.onload = () => {
            resolve(reader.result);
        };
    });
}

function getGeographyParams(){
    geoPolyArr = [];
    geoCircleArr = [];
    let totalArea = 0;
    selectInteraction.getFeatures().forEach(function(feature){
        let turfSimple;
        let options;
        let area_km;
        let area;
        let areaFeat;
        if(feature){
            const selectedClone = feature.clone();
            const geoType = selectedClone.getGeometry().getType();
            const wktFormat = new ol.format.WKT();
            const geoJSONFormat = new ol.format.GeoJSON();
            if(geoType === 'MultiPolygon' || geoType === 'Polygon') {
                const selectiongeometry = selectedClone.getGeometry();
                const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                let polyCoords = JSON.parse(geojsonStr).coordinates;
                if (geoType === 'MultiPolygon') {
                    areaFeat = turf.multiPolygon(polyCoords);
                    area = turf.area(areaFeat);
                    area_km = area/1000/1000;
                    totalArea = totalArea + area_km;
                    for (let e in polyCoords) {
                        if(polyCoords.hasOwnProperty(e)){
                            let singlePoly = turf.polygon(polyCoords[e]);
                            //console.log('start multipolygon length: '+singlePoly.geometry.coordinates.length);
                            if(singlePoly.geometry.coordinates.length > 10){
                                options = {tolerance: 0.001, highQuality: true};
                                singlePoly = turf.simplify(singlePoly,options);
                            }
                            //console.log('end multipolygon length: '+singlePoly.geometry.coordinates.length);
                            polyCoords[e] = singlePoly.geometry.coordinates;
                        }
                    }
                    turfSimple = turf.multiPolygon(polyCoords);
                }
                if (geoType === 'Polygon') {
                    areaFeat = turf.polygon(polyCoords);
                    area = turf.area(areaFeat);
                    area_km = area / 1000 / 1000;
                    totalArea = totalArea + area_km;
                    //console.log('start multipolygon length: '+areaFeat.geometry.coordinates.length);
                    if(areaFeat.geometry.coordinates.length > 10){
                        options = {tolerance: 0.001, highQuality: true};
                        areaFeat = turf.simplify(areaFeat,options);
                    }
                    //console.log('end multipolygon length: '+areaFeat.geometry.coordinates.length);
                    polyCoords = areaFeat.geometry.coordinates;
                    turfSimple = turf.polygon(polyCoords);
                }
                const polySimple = geoJSONFormat.readFeature(turfSimple, {featureProjection: 'EPSG:3857'});
                const simplegeometry = polySimple.getGeometry();
                const fixedgeometry = simplegeometry.transform(mapProjection, wgs84Projection);
                if(SOLRMODE) {
                    const wmswktString = wktFormat.writeGeometry(fixedgeometry);
                    geoPolyArr.push(wmswktString);
                }
                else{
                    const geocoords = fixedgeometry.getCoordinates();
                    const mysqlWktString = writeMySQLWktString(geoType, geocoords);
                    geoPolyArr.push(mysqlWktString);
                }
            }
            if(geoType === 'Circle'){
                const center = selectedClone.getGeometry().getCenter();
                const radius = selectedClone.getGeometry().getRadius();
                const edgeCoordinate = [center[0] + radius, center[1]];
                const fixedcenter = ol.proj.transform(center, 'EPSG:3857', 'EPSG:4326');
                const fixededgeCoordinate = ol.proj.transform(edgeCoordinate, 'EPSG:3857', 'EPSG:4326');
                const groundRadius = turf.distance([fixedcenter[0], fixedcenter[1]], [fixededgeCoordinate[0], fixededgeCoordinate[1]]);
                const circleArea = Math.PI * groundRadius * groundRadius;
                totalArea = totalArea + circleArea;
                const circleObj = {
                    pointlat: fixedcenter[1],
                    pointlong: fixedcenter[0],
                    radius: radius,
                    groundradius: groundRadius
                };
                geoCircleArr.push(circleObj);
            }
        }
    });
    if(totalArea === 0){
        document.getElementById("polyarea").value = totalArea;
    }
    else{
        document.getElementById("polyarea").value = totalArea.toFixed(2);
    }
    if(geoPolyArr.length > 0){
        setSearchTermsArrKeyValue('polyArr',JSON.stringify(geoPolyArr));
    }
    else{
        clearSearchTermsArrKey('polyArr');
    }
    if(geoCircleArr.length > 0){
        setSearchTermsArrKeyValue('circleArr',JSON.stringify(geoCircleArr));
    }
    else{
        clearSearchTermsArrKey('circleArr');
    }
}

function getPointInfoArr(cluster){
    const feature = (cluster.get('features') ? cluster.get('features')[0] : cluster);
    const infoArr = [];
    infoArr['occid'] = Number(feature.get('occid'));
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

function getPointStyle(feature) {
    let style = '';
    if(clusterPoints){
        style = setClusterSymbol(feature);
    }
    else{
        style = setSymbol(feature);
    }
    return style;
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

function getTurfPointFeaturesetAll(){
    let pntCoords;
    let geojsonStr;
    let fixedselectgeometry;
    let selectiongeometry;
    let selectedClone;
    const turfFeatureArr = [];
    const geoJSONFormat = new ol.format.GeoJSON();
    if(clusterPoints){
        const clusters = layersArr['pointv'].getSource().getFeatures();
        for(let c in clusters){
            if(clusters.hasOwnProperty(c)){
                const cFeatures = clusters[c].get('features');
                for (let f in cFeatures) {
                    if(cFeatures.hasOwnProperty(f)){
                        selectedClone = cFeatures[f].clone();
                        selectiongeometry = selectedClone.getGeometry();
                        fixedselectgeometry = selectiongeometry.transform(mapProjection,wgs84Projection);
                        geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                        pntCoords = JSON.parse(geojsonStr).coordinates;
                        turfFeatureArr.push(turf.point(pntCoords));
                    }
                }
            }
        }
    }
    else{
        const features = layersArr['pointv'].getSource().getFeatures();
        for(f in features){
            if(features.hasOwnProperty(f)){
                selectedClone = features[f].clone();
                selectiongeometry = selectedClone.getGeometry();
                fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                pntCoords = JSON.parse(geojsonStr).coordinates;
                turfFeatureArr.push(turf.point(pntCoords));
            }
        }
    }
    if(turfFeatureArr.length >= 3){
        return turf.featureCollection(turfFeatureArr);
    }
    else{
        return false;
    }
}

function getTurfPointFeaturesetSelected(){
    const turfFeatureArr = [];
    const geoJSONFormat = new ol.format.GeoJSON();
    for(let i in selections){
        if(selections.hasOwnProperty(i)){
            let point = '';
            if(clusterPoints){
                const cluster = findOccCluster(selections[i]);
                point = findOccPointInCluster(cluster,selections[i]);
            }
            else{
                point = findOccPoint(selections[i]);
            }
            if(point){
                const selectedClone = point.clone();
                const selectiongeometry = selectedClone.getGeometry();
                const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                const pntCoords = JSON.parse(geojsonStr).coordinates;
                turfFeatureArr.push(turf.point(pntCoords));
            }
        }
    }
    if(turfFeatureArr.length >= 3){
        return turf.featureCollection(turfFeatureArr);
    }
    else{
        return false;
    }
}

function getVectorLayerStyle(fillColor, borderColor, borderWidth, pointRadius, opacity){
    if(Number(borderWidth) !== 0){
        return new ol.style.Style({
            fill: new ol.style.Fill({
                color: getRgbaStrFromHexOpacity(('#' + fillColor),opacity)
            }),
            stroke: new ol.style.Stroke({
                color: ('#' + borderColor),
                width: borderWidth
            }),
            image: new ol.style.Circle({
                radius: pointRadius,
                fill: new ol.style.Fill({
                    color: getRgbaStrFromHexOpacity(('#' + fillColor),opacity)
                }),
                stroke: new ol.style.Stroke({
                    color: ('#' + borderColor),
                    width: borderWidth
                })
            })
        })
    }
    else{
        return new ol.style.Style({
            fill: new ol.style.Fill({
                color: getRgbaStrFromHexOpacity(('#' + fillColor),opacity)
            }),
            image: new ol.style.Circle({
                radius: pointRadius,
                fill: new ol.style.Fill({
                    color: getRgbaStrFromHexOpacity(('#' + fillColor),opacity)
                })
            })
        })
    }
}

function getWGS84CirclePoly(center,radius){
    let turfFeature = '';
    const ciroptions = {steps: 200, units: 'kilometers'};
    turfFeature = turf.circle(center,radius,ciroptions);
    return turfFeature;
}

function hideFeature(feature){
    const invisibleStyle = new ol.style.Style({
        image: new ol.style.Circle({
            radius: feature.get('radius'),
            fill: new ol.style.Fill({
                color: 'rgba(255, 255, 255, 0.01)'
            })
        })
    });
    feature.setStyle(invisibleStyle);
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

function loadInputParentParams(){
    if(opener.document.getElementById('upperlat') && opener.document.getElementById('upperlat').value && INPUTTOOLSARR.includes('box')){
        processInputParentBoxParams();
    }
    if(opener.document.getElementById('pointlat') && opener.document.getElementById('pointlat').value && INPUTTOOLSARR.includes('circle')){
        processInputParentPointRadiusParams();
    }
    if(opener.document.getElementById('polyArr') && opener.document.getElementById('polyArr').value && INPUTTOOLSARR.length === 0){
        processInputParentPolyArrParams();
    }
    if(opener.document.getElementById('circleArr') && opener.document.getElementById('circleArr').value && INPUTTOOLSARR.length === 0){
        processInputParentCircleArrParams();
    }
    if(opener.document.getElementById('decimallatitude') && opener.document.getElementById('decimallatitude').value && opener.document.getElementById('decimallongitude') && opener.document.getElementById('decimallongitude').value && INPUTTOOLSARR.includes('point')){
        processInputParentPointParams();
    }
    if(opener.document.getElementById('footprintWKT') && opener.document.getElementById('footprintWKT').value && INPUTTOOLSARR.includes('polygon') && INPUTTOOLSARR.includes('wkt')){
        processInputParentPolyWKTParams();
    }
}

function loadPoints(){
    searchTermsArr = getSearchTermsArr();
    if(validateSearchTermsArr(searchTermsArr)){
        taxaCnt = 0;
        collSymbology = [];
        taxaSymbology = [];
        selections = [];
        dsOldestDate = '';
        dsNewestDate = '';
        removeDateSlider();
        showWorking();
        pointvectorsource.clear(true);
        layersArr['pointv'].setSource(pointvectorsource);
        getQueryRecCnt(function() {
            if(queryRecCnt > 0){
                loadPointsEvent = true;
                setCopySearchUrlDiv();
                loadPointWFSLayer(0);
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

function loadPointWFSLayer(index){
    pointvectorsource.clear();
    let processed = 0;
    do{
        lazyLoadPoints(index,function(res){
            const format = new ol.format.GeoJSON();
            let features = format.readFeatures(res, {
                featureProjection: 'EPSG:3857'
            });
            if(toggleSelectedPoints){
                features = features.filter(function (feature){
                    const occid = Number(feature.get('occid'));
                    return (selections.indexOf(occid) !== -1);
                });
            }
            primeSymbologyData(features);
            pointvectorsource.addFeatures(features);
            if(loadPointsEvent){
                const pointextent = pointvectorsource.getExtent();
                map.getView().fit(pointextent,map.getSize());
            }
        });
        processed = processed + lazyLoadCnt;
        index++;
    }
    while(processed < queryRecCnt);

    clustersource = new ol.source.PropertyCluster({
        distance: clusterDistance,
        source: pointvectorsource,
        clusterkey: clusterKey,
        indexkey: 'occid',
        geometryFunction: function(feature){
            if(dateSliderActive){
                if(validateFeatureDate(feature)){
                    return feature.getGeometry();
                }
                else{
                    return null;
                }
            }
            else{
                return feature.getGeometry();
            }
        }
    });

    layersArr['pointv'].setStyle(getPointStyle);
    if(clusterPoints){
        layersArr['pointv'].setSource(clustersource);
    }
    else{
        layersArr['pointv'].setSource(pointvectorsource);
    }
    layersArr['heat'].setSource(pointvectorsource);
    if(showHeatMap){
        layersArr['heat'].setVisible(true);
    }
}

function loadServerLayer(id,file){
    showWorking();
    const zIndex = layerOrderArr.length + 1;
    const filenameParts = file.split('.');
    const fileType = filenameParts.pop();
    if(fileType === 'geojson' || fileType === 'kml' || fileType === 'zip'){
        const fillColor = document.getElementById(('fillColor-' + id)).value;
        const borderColor = document.getElementById(('borderColor-' + id)).value;
        const borderWidth = document.getElementById(('borderWidth-' + id)).value;
        const pointRadius = document.getElementById(('pointRadius-' + id)).value;
        const opacity = document.getElementById(('opacity-' + id)).value;
        layersArr[id] = new ol.layer.Vector({
            source: new ol.source.Vector({
                wrapX: true
            }),
            zIndex: zIndex,
            style: getVectorLayerStyle(fillColor, borderColor, borderWidth, pointRadius, opacity)
        });
    }
    else{
        layersArr[id] = new ol.layer.Image({
            zIndex: zIndex,
        });
    }
    if(fileType === 'geojson'){
        layersArr[id].setSource(new ol.source.Vector({
            url: ('../content/spatial/' + file),
            format: new ol.format.GeoJSON(),
            wrapX: true
        }));
        layersArr[id].getSource().on('addfeature', function(evt) {
            map.getView().fit(layersArr[id].getSource().getExtent());
        });
        layersArr[id].on('postrender', function(evt) {
            hideWorking();
        });
    }
    else if(fileType === 'kml'){
        layersArr[id].setSource(new ol.source.Vector({
            url: ('../content/spatial/' + file),
            format: new ol.format.KML({
                extractStyles: false,
            }),
            wrapX: true
        }));
        layersArr[id].getSource().on('addfeature', function(evt) {
            map.getView().fit(layersArr[id].getSource().getExtent());
        });
        layersArr[id].on('postrender', function(evt) {
            hideWorking();
        });
    }
    else if(fileType === 'zip'){
        fetch(('../content/spatial/' + file)).then((fileFetch) => {
            fileFetch.blob().then((blob) => {
                getArrayBuffer(blob).then((data) => {
                    shp(data).then((geojson) => {
                        const format = new ol.format.GeoJSON();
                        const features = format.readFeatures(geojson, {
                            featureProjection: 'EPSG:3857'
                        });
                        layersArr[id].setSource(new ol.source.Vector({
                            features: features,
                            wrapX: true
                        }));
                        map.getView().fit(layersArr[id].getSource().getExtent());
                        layersArr[id].on('postrender', function(evt) {
                            hideWorking();
                        });
                    });
                });
            });
        });
    }
    else if(fileType === 'tif' || fileType === 'tiff'){
        fetch(('../content/spatial/' + file)).then((fileFetch) => {
            fileFetch.blob().then((blob) => {
                blob.arrayBuffer().then((data) => {
                    const extent = ol.extent.createEmpty();
                    const tiff = GeoTIFF.parse(data);
                    const image = tiff.getImage();
                    const imageIndex = id + 'Image';
                    layersArr[imageIndex] = image;
                    const rawBox = image.getBoundingBox();
                    const box = [rawBox[0],rawBox[1] - (rawBox[3] - rawBox[1]), rawBox[2], rawBox[1]];
                    const bands = image.readRasters();
                    const canvasElement = document.createElement('canvas');
                    const minValue = 0;
                    const maxValue = 1200;
                    const plot = new plotty.plot({
                        canvas: canvasElement,
                        data: bands[0],
                        width: image.getWidth(),
                        height: image.getHeight(),
                        domain: [minValue, maxValue],
                        colorScale: 'earth'
                    });
                    plot.render();
                    layersArr[id].setSource(new ol.source.ImageStatic({
                        url: canvasElement.toDataURL("image/png"),
                        imageExtent: box,
                        projection: 'EPSG:4326'
                    }));
                    const topRight = new ol.geom.Point(ol.proj.fromLonLat([box[2], box[3]]));
                    const topLeft = new ol.geom.Point(ol.proj.fromLonLat([box[0], box[3]]));
                    const bottomLeft = new ol.geom.Point(ol.proj.fromLonLat([box[0], box[1]]));
                    const bottomRight = new ol.geom.Point(ol.proj.fromLonLat([box[2], box[1]]));
                    ol.extent.extend(extent, topRight.getExtent());
                    ol.extent.extend(extent, topLeft.getExtent());
                    ol.extent.extend(extent, bottomLeft.getExtent());
                    ol.extent.extend(extent, bottomRight.getExtent());
                    map.getView().fit(extent, map.getSize());
                    hideWorking();
                });
            });
        });
    }
    map.addLayer(layersArr[id]);
    toggleLayerDisplayMessage();
}

function openIndPopup(occid){
    openPopup('../collections/individual/index.php?occid=' + occid);
}

function openOccidInfoBox(occid,label){
    const occpos = findOccClusterPosition(occid);
    finderpopupcontent.innerHTML = label;
    finderpopupoverlay.setPosition(occpos);
}

function primeLayerQuerySelectorFields(layerId) {
    const fieldArr = [];
    const fieldSelector = document.getElementById('spatialQueryFieldSelector');
    const layerFeatures = layersArr[layerId].getSource().getFeatures();
    for(let f in layerFeatures){
        if(layerFeatures.hasOwnProperty(f)){
            const properties = layerFeatures[f].getKeys();
            for(let i in properties){
                if(properties.hasOwnProperty(i) && !fieldArr.includes(String(properties[i])) && String(properties[i]) !== 'geometry' && String(properties[i]) !== 'OBJECTID'){
                    fieldArr.push(String(properties[i]));
                }
            }
        }
    }
    if(fieldArr.length > 0){
        fieldArr.sort(function (a, b) {
            return a.toLowerCase().localeCompare(b.toLowerCase());
        });
        const blankSelectorOption = document.createElement('option');
        blankSelectorOption.setAttribute("value","");
        blankSelectorOption.innerHTML = 'Select data point';
        fieldSelector.appendChild(blankSelectorOption);
        for(let f in fieldArr){
            if(fieldArr.hasOwnProperty(f)){
                const selectorOption = document.createElement('option');
                selectorOption.setAttribute("value",fieldArr[f]);
                selectorOption.innerHTML = fieldArr[f];
                fieldSelector.appendChild(selectorOption);
            }
        }
    }
    else{
        const blankSelectorOption = document.createElement('option');
        blankSelectorOption.setAttribute("value","");
        blankSelectorOption.innerHTML = 'Layer does not include data';
        fieldSelector.appendChild(blankSelectorOption);
    }
}

function primeSymbologyData(features){
    const currentDate = new Date();
    for(let f in features) {
        if(features.hasOwnProperty(f)){
            if(features[f].get('coll_year')){
                const fyear = Number(features[f].get('coll_year'));
                if(fyear.toString().length === 4 && fyear > 1500){
                    const fmonth = (features[f].get('coll_month') ? Number(features[f].get('coll_month')) : 1);
                    const fday = (features[f].get('coll_day') ? Number(features[f].get('coll_day')) : 1);
                    const fDate = new Date();
                    fDate.setFullYear(fyear, fmonth - 1, fday);
                    if(currentDate > fDate){
                        if(!dsOldestDate || (fDate < dsOldestDate)){
                            dsOldestDate = fDate;
                        }
                        if(!dsNewestDate || (fDate > dsNewestDate)){
                            dsNewestDate = fDate;
                        }
                    }
                }
            }
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
            namestring = namestring.replaceAll(/[^A-Za-z0-9 ]/g,'');
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

function processAddLayerControllerElement(lArr,parentElement,active){
    const layerDivId = 'layer-' + lArr['id'];
    if(!document.getElementById(layerDivId)){
        const layerDiv = buildLayerControllerLayerElement(lArr,active);
        if(lArr['id'] === 'pointv'){
            parentElement.insertBefore(layerDiv, parentElement.firstChild);
        }
        else{
            parentElement.appendChild(layerDiv);
        }
        if(lArr['symbology']){
            const symbologyOpacityId = '#opacity-' + lArr['id'];
            const symbologyBorderWidthId = '#borderWidth-' + lArr['id'];
            const symbologyPointRadiusId = '#pointRadius-' + lArr['id'];
            $( symbologyOpacityId ).spinner({
                step: 0.1,
                min: 0,
                max: 1,
                numberFormat: "n",
                spin: function( event, ui ) {
                    changeLayerOpacity(lArr['id'], ui.value);
                }
            });
            $( symbologyBorderWidthId ).spinner({
                step: 1,
                min: 0,
                numberFormat: "n",
                spin: function( event, ui ) {
                    changeBorderWidth(lArr['id'], ui.value);
                }
            });
            $( symbologyPointRadiusId ).spinner({
                step: 1,
                min: 0,
                numberFormat: "n",
                spin: function( event, ui ) {
                    changePointRadius(lArr['id'], ui.value);
                }
            });
            jscolor.init();
        }
        if(lArr['sortable']){
            const sortingScrollerId = '#layerOrder-' + lArr['id'];
            $( sortingScrollerId ).spinner({
                step: 1,
                min: 1,
                disabled: !active,
                numberFormat: "n",
                spin: function( event, ui ) {
                    changeLayerOrder(lArr['id'], ui.value);
                }
            });
            if(active){
                layerOrderArr.push(lArr['id']);
                setLayersOrder();
            }
        }
        if(active || lArr['id'] === 'select'){
            addLayerToSelList(lArr['id'], lArr['layerName'], active);
        }
    }
    else{
        document.getElementById("selectlayerselect").value = lArr['id'];
        setActiveLayer();
    }
    toggleLayerDisplayMessage();
}

function processAddLayerControllerGroup(lArr,parentElement){
    const layerGroupdDivId = 'layerGroup-' + lArr['id'] + '-accordion';
    if(!document.getElementById(layerGroupdDivId)){
        const layersArr = lArr['layers'];
        const layerGroupContainerId = 'layerGroup-' + lArr['id'] + '-layers';
        const layerGroupDiv = document.createElement('div');
        layerGroupDiv.setAttribute("id",layerGroupdDivId);
        layerGroupDiv.setAttribute("style","margin-bottom:5px;");
        const layerGroupLabel = document.createElement('h3');
        layerGroupLabel.setAttribute("style","font-weight:bold;font-family:Verdana,Arial,sans-serif;font-size:14px;");
        layerGroupLabel.innerHTML = lArr['name'];
        layerGroupDiv.appendChild(layerGroupLabel);
        const layerGroupContainerDiv = document.createElement('div');
        layerGroupContainerDiv.setAttribute("id",layerGroupContainerId);
        layerGroupContainerDiv.setAttribute("style","display:flex;flex-direction:column;margin: 5px 0;");
        layerGroupDiv.appendChild(layerGroupContainerDiv);
        parentElement.appendChild(layerGroupDiv);
        $( ('#' + layerGroupdDivId) ).accordion({
            icons: null,
            collapsible: true,
            active: false,
            heightStyle: "content"
        });
        for(let i in layersArr){
            if(layersArr.hasOwnProperty(i)){
                layersArr[i]['removable'] = false;
                layersArr[i]['sortable'] = true;
                layersArr[i]['symbology'] = true;
                layersArr[i]['query'] = true;
                processAddLayerControllerElement(layersArr[i],layerGroupContainerDiv,false)
            }
        }
    }
    toggleLayerDisplayMessage();
}

function processCheckSelection(c){
    let activeTab;
    if(c.checked === true){
        activeTab = $('#recordstab').tabs("option","active");
        if(activeTab === 1){
            if($('.occcheck:checked').length === $('.occcheck').length){
                document.getElementById("selectallcheck").checked = true;
            }
        }
        selections.push(Number(c.value));
        layersArr['pointv'].getSource().changed();
        updateSelections(Number(c.value),false);
    }
    else if(c.checked === false){
        activeTab = $('#recordstab').tabs("option", "active");
        if(activeTab === 1){
            document.getElementById("selectallcheck").checked = false;
        }
        const index = selections.indexOf(Number(c.value));
        selections.splice(index, 1);
        layersArr['pointv'].getSource().changed();
        removeSelectionRecord(Number(c.value));
    }
    adjustSelectionsTab();
}

function processInputParentBoxParams(){
    const boundingBox = {};
    boundingBox.upperlat = opener.document.getElementById('upperlat').value;
    boundingBox.bottomlat = opener.document.getElementById('bottomlat').value;
    boundingBox.leftlong = opener.document.getElementById('leftlong').value;
    boundingBox.rightlong = opener.document.getElementById('rightlong').value;
    if(boundingBox.upperlat && boundingBox.bottomlat && boundingBox.leftlong && boundingBox.rightlong){
        createPolygonFromBoundingBox(boundingBox, true);
    }
}

function processInputParentCircleArrParams(){
    const circleArr = JSON.parse(opener.document.getElementById('circleArr').value);
    if(Array.isArray(circleArr)){
        createCirclesFromCircleArr(circleArr, true);
    }
}

function processInputParentPointParams(){
    let decLat = null;
    let decLong = null;
    if(opener.document.getElementById('decimallatitude')){
        decLat = opener.document.getElementById('decimallatitude').value;
    }
    if(opener.document.getElementById('decimallongitude')){
        decLong = opener.document.getElementById('decimallongitude').value;
    }
    if(decLat && decLong){
        let openerRadius = 0;
        if(opener.document.getElementById('coordinateuncertaintyinmeters') && opener.document.getElementById('coordinateuncertaintyinmeters').value && INPUTTOOLSARR.includes('uncertainty')){
            if(!isNaN(opener.document.getElementById('coordinateuncertaintyinmeters').value)){
                openerRadius = opener.document.getElementById('coordinateuncertaintyinmeters').value;
            }
        }
        if(opener.document.getElementById('pointradiusmeters') && opener.document.getElementById('pointradiusmeters').value && INPUTTOOLSARR.includes('radius')){
            if(!isNaN(opener.document.getElementById('pointradiusmeters').value)){
                openerRadius = opener.document.getElementById('pointradiusmeters').value;
            }
        }
        if(openerRadius > 0){
            document.getElementById('inputpointuncertainty').value = openerRadius;
            const pointRadius = {};
            pointRadius.pointlat = Number(decLat);
            pointRadius.pointlong = Number(decLong);
            pointRadius.radius = Number(openerRadius);
            createUncertaintyCircleFromPointRadius(pointRadius);
        }
        const pointGeom = new ol.geom.Point(ol.proj.fromLonLat([
            decLong, decLat
        ]));
        const pointFeature = new ol.Feature(pointGeom);
        selectsource.addFeature(pointFeature);
        selectedFeatures.push(pointFeature);
        processInputSelections();
        const selectextent = selectsource.getExtent();
        map.getView().fit(selectextent,map.getSize());
        let fittedZoom = map.getView().getZoom();
        if(fittedZoom > 10){
            map.getView().setZoom(fittedZoom - 8);
        }
    }
}

function processInputParentPointRadiusParams(){
    const pointRadius = {};
    pointRadius.pointlat = opener.document.getElementById('pointlat').value;
    pointRadius.pointlong = opener.document.getElementById('pointlong').value;
    pointRadius.radius = opener.document.getElementById('radius').value;
    if(pointRadius.pointlat && pointRadius.pointlong && pointRadius.radius){
        createCircleFromPointRadius(pointRadius, true);
    }
}

function processInputParentPolyArrParams(){
    const polyArr = JSON.parse(opener.document.getElementById('polyArr').value);
    if(Array.isArray(polyArr)){
        createPolysFromPolyArr(polyArr, true);
    }
}

function processInputParentPolyWKTParams(){
    let wktStr = '';
    if(opener.document.getElementById('footprintWKT')){
        wktStr = opener.document.getElementById('footprintWKT').value;
    }
    if(wktStr !== '' && (wktStr.startsWith("POLYGON") || wktStr.startsWith("MULTIPOLYGON"))){
        let wktFormat = new ol.format.WKT();
        const footprintpoly = wktFormat.readFeature(wktStr, mapProjection);
        if(footprintpoly){
            footprintpoly.getGeometry().transform(wgs84Projection,mapProjection);
            selectsource.addFeature(footprintpoly);
            selectedFeatures.push(footprintpoly);
            processInputSelections();
        }
    }
    const selectextent = selectsource.getExtent();
    map.getView().fit(selectextent,map.getSize());
    let fittedZoom = map.getView().getZoom();
    if(fittedZoom > 10){
        map.getView().setZoom(fittedZoom - 8);
    }
}

function processInputPointUncertaintyChange(){
    uncertaintycirclesource.clear();
    const uncertaintyValue = document.getElementById("inputpointuncertainty").value;
    if(uncertaintyValue && !isNaN(uncertaintyValue) && uncertaintyValue > 0){
        selectInteraction.getFeatures().forEach(function(feature){
            if(feature){
                const featureClone = feature.clone();
                const geoType = featureClone.getGeometry().getType();
                const geoJSONFormat = new ol.format.GeoJSON();
                if(geoType === 'Point'){
                    const selectiongeometry = featureClone.getGeometry();
                    const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                    const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                    let pointCoords = JSON.parse(geojsonStr).coordinates;
                    const pointRadius = {};
                    pointRadius.pointlat = pointCoords[1];
                    pointRadius.pointlong = pointCoords[0];
                    pointRadius.radius = document.getElementById("inputpointuncertainty").value;
                    createUncertaintyCircleFromPointRadius(pointRadius);
                }
            }
        });
    }
}

function processInputSelections(){
    inputResponseData = {};
    geoPolyArr = [];
    geoCircleArr = [];
    geoBoundingBoxArr = {};
    geoPointArr = [];
    let totalArea = 0;
    let submitReady = false;
    selectInteraction.getFeatures().forEach(function(feature){
        let turfSimple;
        let options;
        let area_km;
        let area;
        let areaFeat;
        if(feature){
            const selectedClone = feature.clone();
            const geoType = selectedClone.getGeometry().getType();
            const wktFormat = new ol.format.WKT();
            const geoJSONFormat = new ol.format.GeoJSON();
            if(geoType === 'MultiPolygon' || geoType === 'Polygon') {
                const boxType = (selectedClone.values_.hasOwnProperty('geoType') && selectedClone.values_.geoType === 'Box');
                const selectiongeometry = selectedClone.getGeometry();
                const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                let polyCoords = JSON.parse(geojsonStr).coordinates;
                if(INPUTWINDOWMODE && INPUTTOOLSARR.includes('box') && boxType){
                    geoBoundingBoxArr = {
                        upperlat: polyCoords[0][2][1],
                        bottomlat: polyCoords[0][0][1],
                        leftlong: polyCoords[0][0][0],
                        rightlong: polyCoords[0][1][0]
                    };
                }
                else{
                    if (geoType === 'MultiPolygon') {
                        areaFeat = turf.multiPolygon(polyCoords);
                        area = turf.area(areaFeat);
                        area_km = area/1000/1000;
                        totalArea = totalArea + area_km;
                        for (let e in polyCoords) {
                            if(polyCoords.hasOwnProperty(e)){
                                let singlePoly = turf.polygon(polyCoords[e]);
                                //console.log('start multipolygon length: '+singlePoly.geometry.coordinates.length);
                                if(singlePoly.geometry.coordinates.length > 10){
                                    options = {tolerance: 0.001, highQuality: true};
                                    singlePoly = turf.simplify(singlePoly,options);
                                }
                                //console.log('end multipolygon length: '+singlePoly.geometry.coordinates.length);
                                polyCoords[e] = singlePoly.geometry.coordinates;
                            }
                        }
                        turfSimple = turf.multiPolygon(polyCoords);
                    }
                    if (geoType === 'Polygon') {
                        areaFeat = turf.polygon(polyCoords);
                        area = turf.area(areaFeat);
                        area_km = area / 1000 / 1000;
                        totalArea = totalArea + area_km;
                        //console.log('start multipolygon length: '+areaFeat.geometry.coordinates.length);
                        if(areaFeat.geometry.coordinates.length > 10){
                            options = {tolerance: 0.001, highQuality: true};
                            areaFeat = turf.simplify(areaFeat,options);
                        }
                        //console.log('end multipolygon length: '+areaFeat.geometry.coordinates.length);
                        polyCoords = areaFeat.geometry.coordinates;
                        turfSimple = turf.polygon(polyCoords);
                    }
                    const polySimple = geoJSONFormat.readFeature(turfSimple, {featureProjection: 'EPSG:3857'});
                    const simplegeometry = polySimple.getGeometry();
                    const fixedgeometry = simplegeometry.transform(mapProjection, wgs84Projection);
                    if(SOLRMODE || INPUTTOOLSARR.includes('wkt')) {
                        const wmswktString = wktFormat.writeGeometry(fixedgeometry);
                        geoPolyArr.push(wmswktString);
                    }
                    else{
                        const geocoords = fixedgeometry.getCoordinates();
                        const mysqlWktString = writeMySQLWktString(geoType, geocoords);
                        geoPolyArr.push(mysqlWktString);
                    }
                }
            }
            if(geoType === 'Circle'){
                const center = selectedClone.getGeometry().getCenter();
                const radius = selectedClone.getGeometry().getRadius();
                const edgeCoordinate = [center[0] + radius, center[1]];
                const fixedcenter = ol.proj.transform(center, 'EPSG:3857', 'EPSG:4326');
                const fixededgeCoordinate = ol.proj.transform(edgeCoordinate, 'EPSG:3857', 'EPSG:4326');
                const groundRadius = turf.distance([fixedcenter[0], fixedcenter[1]], [fixededgeCoordinate[0], fixededgeCoordinate[1]]);
                const circleArea = Math.PI * groundRadius * groundRadius;
                totalArea = totalArea + circleArea;
                const circleObj = {
                    pointlat: fixedcenter[1],
                    pointlong: fixedcenter[0],
                    radius: radius,
                    groundradius: groundRadius
                };
                geoCircleArr.push(circleObj);
            }
            if(geoType === 'Point'){
                const selectiongeometry = selectedClone.getGeometry();
                const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                let pointCoords = JSON.parse(geojsonStr).coordinates;
                const pointObj = {
                    decimalLatitude: pointCoords[1],
                    decimalLongitude: pointCoords[0]
                };
                geoPointArr.push(pointObj);
            }
        }
    });
    if(totalArea === 0){
        document.getElementById("polyarea").value = totalArea;
    }
    else{
        document.getElementById("polyarea").value = totalArea.toFixed(2);
    }
    if(INPUTWINDOWMODE && ((INPUTTOOLSARR.length === 0) || (INPUTTOOLSARR.length > 0 && selectInteraction.getFeatures().getLength() === 1))){
        if(geoPolyArr.length > 0){
            submitReady = true;
            inputResponseData['polyArr'] = geoPolyArr;
        }
        if(geoCircleArr.length > 0){
            submitReady = true;
            inputResponseData['circleArr'] = geoCircleArr;
        }
        if(geoBoundingBoxArr.hasOwnProperty('upperlat')){
            submitReady = true;
            inputResponseData['boundingBoxArr'] = geoBoundingBoxArr;
        }
        if(geoPointArr.length > 0){
            submitReady = true;
            inputResponseData['pointArr'] = geoPointArr;
        }
    }
    document.getElementById("inputSubmitButton").disabled = !submitReady;
}

function processInputSubmit(){
    const changeEvent = new Event("change");
    if(INPUTWINDOWMODE && INPUTTOOLSARR.length === 0){
        if(opener.document.getElementById('polyArr') && inputResponseData.hasOwnProperty('polyArr')){
            opener.document.getElementById('polyArr').value = JSON.stringify(inputResponseData['polyArr']);
            opener.document.getElementById('polyArr').dispatchEvent(changeEvent);
            if(opener.document.getElementById("spatialParamasNoCriteria")){
                opener.document.getElementById("spatialParamasNoCriteria").style.display = "none";
            }
            if(opener.document.getElementById("spatialParamasCriteria")){
                opener.document.getElementById("spatialParamasCriteria").style.display = "block";
            }
        }
        if(opener.document.getElementById('circleArr') && inputResponseData.hasOwnProperty('circleArr')){
            opener.document.getElementById('circleArr').value = JSON.stringify(inputResponseData['circleArr']);
            opener.document.getElementById('circleArr').dispatchEvent(changeEvent);
            if(opener.document.getElementById("spatialParamasNoCriteria")){
                opener.document.getElementById("spatialParamasNoCriteria").style.display = "none";
            }
            if(opener.document.getElementById("spatialParamasCriteria")){
                opener.document.getElementById("spatialParamasCriteria").style.display = "block";
            }
        }
    }
    if(INPUTWINDOWMODE && INPUTTOOLSARR.includes('box') && inputResponseData.hasOwnProperty('boundingBoxArr')){
        if(opener.document.getElementById('upperlat')){
            opener.document.getElementById('upperlat').value = inputResponseData['boundingBoxArr']['upperlat'];
            opener.document.getElementById('upperlat').dispatchEvent(changeEvent);
        }
        if(opener.document.getElementById('bottomlat')){
            opener.document.getElementById('bottomlat').value = inputResponseData['boundingBoxArr']['bottomlat'];
            opener.document.getElementById('bottomlat').dispatchEvent(changeEvent);
        }
        if(opener.document.getElementById('leftlong')){
            opener.document.getElementById('leftlong').value = inputResponseData['boundingBoxArr']['leftlong'];
            opener.document.getElementById('leftlong').dispatchEvent(changeEvent);
        }
        if(opener.document.getElementById('rightlong')){
            opener.document.getElementById('rightlong').value = inputResponseData['boundingBoxArr']['rightlong'];
            opener.document.getElementById('rightlong').dispatchEvent(changeEvent);
        }
    }
    if(INPUTWINDOWMODE && INPUTTOOLSARR.includes('circle') && inputResponseData.hasOwnProperty('circleArr')){
        if(opener.document.getElementById('pointlat')){
            opener.document.getElementById('pointlat').value = inputResponseData['circleArr'][0]['pointlat'];
        }
        if(opener.document.getElementById('pointlong')){
            opener.document.getElementById('pointlong').value = inputResponseData['circleArr'][0]['pointlong'];
        }
        if(opener.document.getElementById('radiusunits')){
            opener.document.getElementById('radiusunits').value = 'km';
        }
        if(opener.document.getElementById('radiustemp')){
            opener.document.getElementById('radiustemp').value = (inputResponseData['circleArr'][0]['radius'] / 1000);
        }
        if(opener.document.getElementById('radius')){
            opener.document.getElementById('radius').value = inputResponseData['circleArr'][0]['radius'];
        }
        if(opener.document.getElementById('groundradius')){
            opener.document.getElementById('groundradius').value = inputResponseData['circleArr'][0]['groundradius'];
        }
    }
    if(INPUTWINDOWMODE && INPUTTOOLSARR.includes('polygon') && INPUTTOOLSARR.includes('wkt') && inputResponseData.hasOwnProperty('polyArr')){
        if(opener.document.getElementById('footprintWKT')){
            opener.document.getElementById('footprintWKT').value = inputResponseData['polyArr'][0];
            opener.document.getElementById('footprintWKT').dispatchEvent(changeEvent);
        }
    }
    if(INPUTWINDOWMODE && INPUTTOOLSARR.includes('point') && inputResponseData.hasOwnProperty('pointArr')){
        if(opener.document.getElementById('decimallatitude')){
            opener.document.getElementById('decimallatitude').value = inputResponseData['pointArr'][0]['decimalLatitude'];
            opener.document.getElementById('decimallatitude').dispatchEvent(changeEvent);
        }
        if(opener.document.getElementById('decimallongitude')){
            opener.document.getElementById('decimallongitude').value = inputResponseData['pointArr'][0]['decimalLongitude'];
            opener.document.getElementById('decimallongitude').dispatchEvent(changeEvent);
        }
        if(INPUTTOOLSARR.includes('uncertainty')){
            if(opener.document.getElementById('coordinateuncertaintyinmeters')){
                opener.document.getElementById('coordinateuncertaintyinmeters').value = document.getElementById('inputpointuncertainty').value;
                opener.document.getElementById('coordinateuncertaintyinmeters').dispatchEvent(changeEvent);
            }
        }
        if(INPUTTOOLSARR.includes('radius')){
            if(opener.document.getElementById('pointradiusmeters')){
                opener.document.getElementById('pointradiusmeters').value = document.getElementById('inputpointuncertainty').value;
                opener.document.getElementById('pointradiusmeters').dispatchEvent(changeEvent);
            }
        }
    }
    self.close();
}

function processMapPNGDownload(){
    const imagefilename = 'map_' + getDateTimeString() + '.png';
    exportMapPNG(imagefilename,false);
}

function processPointSelection(sFeature){
    const feature = (sFeature.get('features') ? sFeature.get('features')[0] : sFeature);
    const occid = Number(feature.get('occid'));
    if(selections.indexOf(occid) < 0){
        selections.push(occid);
        const infoArr = getPointInfoArr(sFeature);
        updateSelections(occid,infoArr);
    }
    else{
        const index = selections.indexOf(occid);
        selections.splice(index, 1);
        removeSelectionRecord(occid);
    }
    const style = (sFeature.get('features') ? setClusterSymbol(sFeature) : setSymbol(sFeature));
    sFeature.setStyle(style);
    adjustSelectionsTab();
}

function processQuerySelectorQuery() {
    let valid = true;
    const fieldValue = document.getElementById('spatialQueryFieldSelector').value;
    const operatorValue = document.getElementById('spatialQueryOperatorSelector').value;
    const singleVal = document.getElementById('spatialQuerySingleValueInput').value;
    const doubleVal1 = document.getElementById('spatialQueryDoubleValueInput1').value;
    const doubleVal2 = document.getElementById('spatialQueryDoubleValueInput2').value;
    if(fieldValue === ''){
        alert('Please select a field on which to run the query.');
        valid = false;
    }
    else if(operatorValue !== 'between' && singleVal === ''){
        alert('Please enter a value with which to run the query.');
        valid = false;
    }
    else if((operatorValue === 'greaterThan' || operatorValue === 'lessThan') && isNaN(singleVal)){
        alert('A numerical value must be entered for greater than or less than queries.');
        valid = false;
    }
    else if(operatorValue === 'between' && (doubleVal1 === '' || doubleVal2 === '')){
        alert('Two values must be entered for a between query.');
        valid = false;
    }
    else if(operatorValue === 'between' && (isNaN(doubleVal1) || isNaN(doubleVal2))){
        alert('Both values must be numeric for a between query.');
        valid = false;
    }
    if(valid){
        const layerId = document.getElementById('spatialQuerySelectorLayerId').value;
        runQuerySelectorQuery(layerId,fieldValue,operatorValue,singleVal,doubleVal1,doubleVal2);
    }
}

function processSpatialQueryOperatorSelectorChange(value) {
    if(value === 'between'){
        document.getElementById('spatialQuerySingleValueDiv').style.display = 'none';
        document.getElementById('spatialQueryBetweenValueDiv').style.display = 'flex';
    }
    else{
        document.getElementById('spatialQuerySingleValueDiv').style.display = 'block';
        document.getElementById('spatialQueryBetweenValueDiv').style.display = 'none';
    }
}

function processToggleSelectedChange(){
    toggleSelectedPoints = document.getElementById("toggleselectedswitch").checked;
    if(clusterPoints){
        loadPointWFSLayer(0);
    }
    else{
        layersArr['pointv'].setSource(pointvectorsource);
    }
}

function processVectorInteraction(){
    if(!spatialModuleInitialising){
        if(!INPUTWINDOWMODE){
            setSpatialParamBox();
            getGeographyParams();
        }
        else{
            processInputSelections();
        }
    }
}

function refreshLayerOrder(){
    const layerCount = map.getLayers().getArray().length;
    layersArr['dragdrop1'].setZIndex(layerCount-6);
    layersArr['dragdrop2'].setZIndex(layerCount-5);
    layersArr['dragdrop3'].setZIndex(layerCount-4);
    layersArr['select'].setZIndex(layerCount-3);
    layersArr['pointv'].setZIndex(layerCount-2);
    layersArr['heat'].setZIndex(layerCount-1);
    layersArr['spider'].setZIndex(layerCount);
}

function removeDateSlider(){
    if(document.getElementById("sliderdiv")){
        document.body.removeChild(sliderdiv);
        sliderdiv = '';
        document.getElementById("datesliderswitch").checked = false;
        dateSliderActive = false;
    }
    if(returnClusters && !showHeatMap){
        returnClusters = false;
        document.getElementById("clusterswitch").checked = true;
        changeClusterSetting();
    }
    tsOldestDate = '';
    tsNewestDate = '';
    document.getElementById("dateslidercontrol").style.display = 'none';
    document.getElementById("maptoolcontainer").style.top = '10px';
    document.getElementById("maptoolcontainer").style.left = '50%';
    document.getElementById("maptoolcontainer").style.bottom = 'initial';
    document.getElementById("maptoolcontainer").style.right = 'initial';
    document.getElementById("datesliderearlydate").value = '';
    document.getElementById("datesliderlatedate").value = '';
    dsAnimDuration = document.getElementById("datesliderinterduration").value = '';
    dsAnimTime = document.getElementById("datesliderintertime").value = '';
    dsAnimImageSave = document.getElementById("dateslideranimimagesave").checked = false;
    dsAnimReverse = document.getElementById("dateslideranimreverse").checked = false;
    dsAnimDual = document.getElementById("dateslideranimdual").checked = false;
    layersArr['pointv'].getSource().changed();
}

function removeLayerFromLayerOrderArr(layerId) {
    const index = layerOrderArr.indexOf(layerId);
    layerOrderArr.splice(index,1);
    const sortingScrollerId = 'layerOrder-' + layerId;
    $( ('#' + sortingScrollerId) ).spinner( "value", null );
    $( ('#' + sortingScrollerId) ).on( "spin", function( event, ui ) {} );
    $( ('#' + sortingScrollerId) ).spinner( "disable" );
    setLayersOrder();
}

function removeLayerToSelList(layer){
    const selectobject = document.getElementById("selectlayerselect");
    for (let i = 0; i<selectobject.length; i++){
        if(selectobject.options[i].value === layer){
            selectobject.remove(i);
        }
    }
    setActiveLayer();
}

function removeRasterLayerFromTargetList(layerId){
    const selectobject = document.getElementById("targetrasterselect");
    for (let i = 0; i<selectobject.length; i++){
        if(selectobject.options[i].value === layerId) selectobject.remove(i);
    }
    const index = rasterLayersArr.indexOf(layerId);
    rasterLayersArr.splice(index,1);
    if(rasterLayersArr.length === 0){
        document.getElementById("rastertoolspanel").style.display = "none";
        document.getElementById("rastertoolstab").style.display = "none";
    }
}

function removeSelection(c){
    if(c.checked === false){
        const occid = c.value;
        const chbox = 'ch' + occid;
        removeSelectionRecord(occid);
        if(document.getElementById(chbox)){
            document.getElementById(chbox).checked = false;
        }
        const index = selections.indexOf(Number(c.value));
        selections.splice(index, 1);
        layersArr['pointv'].getSource().changed();
        if(spiderCluster){
            const spiderFeatures = layersArr['spider'].getSource().getFeatures();
            for(let f in spiderFeatures){
                if(spiderFeatures.hasOwnProperty(f) && spiderFeatures[f].get('features')[0].get('occid') === Number(c.value)){
                    const style = (spiderFeatures[f].get('features') ? setClusterSymbol(spiderFeatures[f]) : setSymbol(spiderFeatures[f]));
                    spiderFeatures[f].setStyle(style);
                }
            }
        }
        adjustSelectionsTab();
    }
}

function removeSelectionRecord(sel){
    const selDivId = "sel" + sel;
    if(document.getElementById(selDivId)){
        const selDiv = document.getElementById(selDivId);
        selDiv.parentNode.removeChild(selDiv);
    }
}

function removeServerLayer(id){
    map.removeLayer(layersArr[id]);
    const imageIndex = id + 'Image';
    if(layersArr.hasOwnProperty(imageIndex)){
        delete layersArr[imageIndex];
    }
    delete layersArr[id];
}

function removeUserLayer(layerID,raster){
    const layerDivId = "layer-" + layerID;
    if(document.getElementById(layerDivId)){
        const layerDiv = document.getElementById(layerDivId);
        layerDiv.parentNode.removeChild(layerDiv);
    }
    if(layerID === 'select'){
        selectInteraction.getFeatures().clear();
        layersArr[layerID].getSource().clear(true);
        shapeActive = false;
    }
    else if(layerID === 'pointv'){
        clearSelections();
        adjustSelectionsTab();
        removeDateSlider();
        pointvectorsource.clear(true);
        layersArr['heat'].setVisible(false);
        clustersource = '';
        $('#criteriatab').tabs({active: 0});
        $("#sidepanel-accordion").accordion("option","active",0);
        pointActive = false;
    }
    else{
        if(layerID === 'dragdrop1' || layerID === 'dragdrop2' || layerID === 'dragdrop3'){
            layersArr[layerID].setSource(blankdragdropsource);
            const sourceIndex = dragDropTarget + 'Source';
            delete layersArr[sourceIndex];
            if(layerID === 'dragdrop1') {
                dragDrop1 = false;
            }
            else if(layerID === 'dragdrop2') {
                dragDrop2 = false;
            }
            else if(layerID === 'dragdrop3') {
                dragDrop3 = false;
            }
        }
        else if(layerID === 'dragdrop4' || layerID === 'dragdrop5' || layerID === 'dragdrop6') {
            map.removeLayer(layersArr[layerID]);
            layersArr[layerID].setSource(null);
            const sourceIndex = dragDropTarget + 'Source';
            const imageIndex = dragDropTarget + 'Image';
            delete layersArr[sourceIndex];
            delete layersArr[imageIndex];
            if(layerID === 'dragdrop4') {
                dragDrop4 = false;
            }
            else if(layerID === 'dragdrop5') {
                dragDrop5 = false;
            }
            else if(layerID === 'dragdrop6') {
                dragDrop6 = false;
            }
            removeRasterLayerFromTargetList(layerID);
        }
    }
    document.getElementById("selectlayerselect").value = 'none';
    removeLayerToSelList(layerID);
    setActiveLayer();
    toggleLayerDisplayMessage();
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
    layersArr['pointv'].getSource().changed();
    if(spiderCluster){
        const spiderFeatures = layersArr['spider'].getSource().getFeatures();
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

function runQuerySelectorQuery(layerId,fieldValue,operatorValue,singleVal,doubleVal1,doubleVal2) {
    const addFeatures = [];
    const layerFeatures = layersArr[layerId].getSource().getFeatures();
    for(let f in layerFeatures){
        if(layerFeatures.hasOwnProperty(f) && layerFeatures[f].get(fieldValue)){
            let add = false;
            const featureValue = layerFeatures[f].get(fieldValue);
            if(operatorValue === 'equals' && featureValue.toString().toLowerCase() === singleVal.toString().toLowerCase()){
                add = true;
            }
            else if(operatorValue === 'contains' && featureValue.toString().toLowerCase().includes(singleVal.toString().toLowerCase())){
                add = true;
            }
            else if(operatorValue === 'greaterThan' && !isNaN(featureValue) && Number(featureValue) > Number(singleVal)){
                add = true;
            }
            else if(operatorValue === 'lessThan' && !isNaN(featureValue) && Number(featureValue) < Number(singleVal)){
                add = true;
            }
            else if(operatorValue === 'between' && !isNaN(featureValue) && Number(featureValue) >= Number(doubleVal1) && Number(featureValue) <= Number(doubleVal2)){
                add = true;
            }
            if(add){
                const featureClone = layerFeatures[f].clone();
                addFeatures.push(featureClone);
            }
        }
    }
    selectsource.addFeatures(addFeatures);
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

function setActiveLayer(){
    const selectDropDown = document.getElementById("selectlayerselect");
    activeLayer = selectDropDown.options[selectDropDown.selectedIndex].value;
}

function setBaseLayerSource(urlTemplate){
    return new ol.source.TileImage({
        tileUrlFunction: function(tileCoord) {
            const z = tileCoord[0];
            let x = tileCoord[1];
            const y = -tileCoord[2] - 1;
            const n = Math.pow(2, z + 1); // 2 tiles at z=0
            x = x % n;
            if (x * n < 0) {
                x = x + n;
            }
            return urlTemplate.replace('{z}', z.toString())
                .replace('{y}', y.toString())
                .replace('{x}', x.toString());
        },
        projection: 'EPSG:4326',
        tileGrid: new ol.tilegrid.TileGrid({
            origin: ol.extent.getTopLeft(projectionExtent),
            resolutions: resolutions,
            tileSize: 512
        }),
        crossOrigin: 'anonymous'
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

function setCopySearchUrlDiv(){
    const stArrJson = encodeURIComponent(JSON.stringify(searchTermsArr));
    if(document.getElementById('copySearchUrlDiv')){
        if(stArrJson.length <= 1800){
            document.getElementById("copySearchUrlDiv").style.display = "block";
        }
        else{
            document.getElementById("copySearchUrlDiv").style.display = "none";
        }
    }
}

function setDownloadFeatures(features){
    const fixedFeatures = [];
    for(let i in features){
        if(features.hasOwnProperty(i)){
            const clone = features[i].clone();
            const geoType = clone.getGeometry().getType();
            if(geoType === 'Circle'){
                const geoJSONFormat = new ol.format.GeoJSON();
                const geometry = clone.getGeometry();
                const fixedgeometry = geometry.transform(mapProjection, wgs84Projection);
                const center = fixedgeometry.getCenter();
                const radius = fixedgeometry.getRadius();
                const edgeCoordinate = [center[0] + radius, center[1]];
                let groundRadius = ol.sphere.getDistance(
                    ol.proj.transform(center, 'EPSG:4326', 'EPSG:4326'),
                    ol.proj.transform(edgeCoordinate, 'EPSG:4326', 'EPSG:4326')
                );
                groundRadius = groundRadius/1000;
                const turfCircle = getWGS84CirclePoly(center, groundRadius);
                const circpoly = geoJSONFormat.readFeature(turfCircle);
                circpoly.getGeometry().transform(wgs84Projection,mapProjection);
                fixedFeatures.push(circpoly);
            }
            else{
                fixedFeatures.push(clone);
            }
        }
    }
    return fixedFeatures;
}

function setDragDropTarget(){
    dragDropTarget = '';
    if(!dragDrop1){
        dragDrop1 = true;
        dragDropTarget = 'dragdrop1';
        return true;
    }
    else if(!dragDrop2){
        dragDrop2 = true;
        dragDropTarget = 'dragdrop2';
        return true;
    }
    else if(!dragDrop3){
        dragDrop3 = true;
        dragDropTarget = 'dragdrop3';
        return true;
    }
    else{
        alert('You may only have 3 uploaded vector layers at a time. Please remove one of the currently uploaded layers to upload more.');
        return false;
    }
}

function setLayersOrder() {
    const layersArrKeys = Object.keys(layersArr);
    const layersArrLength = layersArrKeys.length;
    for(let i in layerOrderArr){
        if(layerOrderArr.hasOwnProperty(i)){
            const index = (layerOrderArr.indexOf(layerOrderArr[i])) + 1;
            layersArr[layerOrderArr[i]].setZIndex(index);
            const sortingScrollerId = 'layerOrder-' + layerOrderArr[i];
            $( ('#' + sortingScrollerId) ).spinner( "value", index );
            $( ('#' + sortingScrollerId) ).spinner( "option", "max", layerOrderArr.length );
        }
    }
    layersArr['base'].setZIndex(0);
    if(layersArr.hasOwnProperty('uncertainty')){
        layersArr['uncertainty'].setZIndex((layersArrLength - 4));
    }
    if(layersArr.hasOwnProperty('select')){
        layersArr['select'].setZIndex((layersArrLength - 3));
    }
    if(layersArr.hasOwnProperty('pointv')){
        layersArr['pointv'].setZIndex((layersArrLength - 2));
    }
    if(layersArr.hasOwnProperty('heat')){
        layersArr['heat'].setZIndex((layersArrLength - 1));
    }
    if(layersArr.hasOwnProperty('spider')){
        layersArr['spider'].setZIndex(layersArrLength);
    }
    if(layersArr.hasOwnProperty('radius')){
        layersArr['radius'].setZIndex((layersArrLength - 1));
    }
    if(layersArr.hasOwnProperty('vector')){
        layersArr['vector'].setZIndex(layersArrLength);
    }
}

function setRasterDragDropTarget(){
    dragDropTarget = '';
    if(!dragDrop4){
        dragDrop4 = true;
        dragDropTarget = 'dragdrop4';
        return true;
    }
    else if(!dragDrop5){
        dragDrop5 = true;
        dragDropTarget = 'dragdrop5';
        return true;
    }
    else if(!dragDrop6){
        dragDrop6 = true;
        dragDropTarget = 'dragdrop6';
        return true;
    }
    else{
        alert('You may only have 3 uploaded raster layers at a time. Please remove one of the currently uploaded layers to upload more.');
        return false;
    }
}

function setDSAnimation(){
    dsAnimDuration = document.getElementById("datesliderinterduration").value;
    dsAnimTime = document.getElementById("datesliderintertime").value;
    if(dsAnimDuration && dsAnimTime){
        dsAnimStop = false;
        dsAnimDuration = dsAnimDuration*365.25;
        dsAnimTime = dsAnimTime*1000;
        dsAnimImageSave = document.getElementById("dateslideranimimagesave").checked;
        dsAnimReverse = document.getElementById("dateslideranimreverse").checked;
        dsAnimDual = document.getElementById("dateslideranimdual").checked;
        const lowDate = document.getElementById("datesliderearlydate").value;
        const highDate = document.getElementById("datesliderlatedate").value;
        dsAnimLow = new Date(lowDate);
        dsAnimLow = new Date(dsAnimLow.setTime(dsAnimLow.getTime()+86400000));
        dsAnimHigh = new Date(highDate);
        dsAnimHigh = new Date(dsAnimHigh.setTime(dsAnimHigh.getTime()+86400000));
        let lowDateVal = dsAnimLow;
        let highDateVal = dsAnimHigh;
        if(dsAnimReverse){
            if(dsAnimDual) lowDateVal = highDateVal;
        }
        else{
            highDateVal = lowDateVal;
        }
        tsOldestDate = lowDateVal;
        tsNewestDate = highDateVal;
        const lowDateValStr = getISOStrFromDateObj(lowDateVal);
        const highDateValStr = getISOStrFromDateObj(highDateVal);
        $("#sliderdiv").slider('values',0,tsOldestDate.getTime());
        $("#sliderdiv").slider('values',1,tsNewestDate.getTime());
        $("#custom-label-min").text(lowDateValStr);
        $("#custom-label-max").text(highDateValStr);
        document.getElementById("datesliderearlydate").value = lowDateValStr;
        document.getElementById("datesliderlatedate").value = highDateValStr;
        layersArr['pointv'].getSource().changed();
        if(dsAnimImageSave){
            zipFile = new JSZip();
            zipFolder = zipFile.folder("images");
        }
        animateDS();
    }
    else{
        dsAnimDuration = '';
        dsAnimTime = '';
        alert("Please enter an interval duration and interval time.");
    }
}

function setDSValues(){
    const lowDate = document.getElementById("datesliderearlydate").value;
    tsOldestDate = new Date(lowDate);
    tsOldestDate = new Date(tsOldestDate.setTime(tsOldestDate.getTime()+86400000));
    const hLowDateStr = getISOStrFromDateObj(tsOldestDate);
    const highDate = document.getElementById("datesliderlatedate").value;
    tsNewestDate = new Date(highDate);
    tsNewestDate = new Date(tsNewestDate.setTime(tsNewestDate.getTime()+86400000));
    const hHighDateStr = getISOStrFromDateObj(tsNewestDate);
    $("#sliderdiv").slider('values',0,tsOldestDate.getTime());
    $("#sliderdiv").slider('values',1,tsNewestDate.getTime());
    $("#custom-label-min").text(hLowDateStr);
    $("#custom-label-max").text(hHighDateStr);
    layersArr['pointv'].getSource().changed();
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

function setLayersController(){
    const http = new XMLHttpRequest();
    const url = "rpc/getlayersconfig.php";
    //console.log(url);
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function() {
        if(http.readyState == 4 && http.status == 200) {
            if(http.responseText){
                const layerArrObject = JSON.parse(http.responseText);
                if(layerArrObject.hasOwnProperty('layerConfig')){
                    const layerArr = layerArrObject['layerConfig'];
                    for(let i in layerArr){
                        if(layerArr[i]['type'] === 'layer'){
                            layerArr[i]['removable'] = false;
                            layerArr[i]['sortable'] = true;
                            layerArr[i]['symbology'] = true;
                            layerArr[i]['query'] = true;
                            processAddLayerControllerElement(layerArr[i],document.getElementById("confLayers"),false);
                        }
                        if(layerArr[i]['type'] === 'layerGroup'){
                            processAddLayerControllerGroup(layerArr[i],document.getElementById("confLayers"));
                        }
                    }
                }
            }
        }
        toggleLayerDisplayMessage();
    };
    http.send();
}

function setRecordsTab(){
    if(queryRecCnt > 0){
        document.getElementById("recordsHeader").style.display = "block";
        document.getElementById("recordstab").style.display = "block";
        document.getElementById("pointToolsNoneDiv").style.display = "none";
        document.getElementById("pointToolsDiv").style.display = "block";
    }
    else{
        document.getElementById("recordsHeader").style.display = "none";
        document.getElementById("recordstab").style.display = "none";
        document.getElementById("pointToolsNoneDiv").style.display = "block";
        document.getElementById("pointToolsDiv").style.display = "none";
    }
}

function setSpatialParamBox(){
    const selectionCnt = selectInteraction.getFeatures().getArray().length;
    if(selectionCnt > 0){
        document.getElementById("noshapecriteria").style.display = "none";
        document.getElementById("shapecriteria").style.display = "block";
    }
    else{
        document.getElementById("noshapecriteria").style.display = "block";
        document.getElementById("shapecriteria").style.display = "none";
    }
}

function setSymbol(feature){
    let fill;
    let color;
    let showPoint = true;
    if(dateSliderActive){
        showPoint = validateFeatureDate(feature);
    }
    let style;
    let stroke;
    let selected = false;
    const cKey = feature.get(clusterKey);
    let recType = feature.get('CollType');
    if(!recType) recType = 'observation';
    if(selections.length > 0){
        const occid = Number(feature.get('occid'));
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

function setTransformHandleStyle(){
    if(!transformInteraction instanceof ol.interaction.Transform){
        return;
    }
    const circle = new ol.style.RegularShape({
        fill: new ol.style.Fill({color:[255,255,255,0.01]}),
        stroke: new ol.style.Stroke({width:1, color:[0,0,0,0.01]}),
        radius: 8,
        points: 10
    });
    transformInteraction.setStyle ('rotate', new ol.style.Style({
        text: new ol.style.Text ({
            text:'\uf0e2',
            font:"16px Fontawesome",
            textAlign: "left",
            fill:new ol.style.Fill({color:'red'})
        }),
        image: circle
    }));
    transformInteraction.setStyle ('rotate0', new ol.style.Style({
        text: new ol.style.Text ({
            text:'\uf0e2',
            font:"20px Fontawesome",
            fill: new ol.style.Fill({ color:'red' }),
            stroke: new ol.style.Stroke({ width:1, color:'red' })
        }),
    }));
    transformInteraction.setStyle('translate', new ol.style.Style({
        text: new ol.style.Text ({
            text:'\uf047',
            font:"20px Fontawesome",
            fill: new ol.style.Fill({ color:'red' }),
            stroke: new ol.style.Stroke({ width:1, color:'red' })
        })
    }));
    transformInteraction.setStyle ('scaleh1', new ol.style.Style({
        text: new ol.style.Text ({
            text:'\uf07d',
            font:"20px Fontawesome",
            fill: new ol.style.Fill({ color:'red' }),
            stroke: new ol.style.Stroke({ width:1, color:'red' })
        })
    }));
    transformInteraction.style.scaleh3 = transformInteraction.style.scaleh1;
    transformInteraction.setStyle('scalev', new ol.style.Style({
        text: new ol.style.Text ({
            text:'\uf07e',
            font:"20px Fontawesome",
            fill: new ol.style.Fill({ color:'red' }),
            stroke: new ol.style.Stroke({ width:1, color:'red' })
        })
    }));
    transformInteraction.style.scalev2 = transformInteraction.style.scalev;
    transformInteraction.set('translate', transformInteraction.get('translate'));
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

function showFeature(feature){
    let featureStyle;
    if(feature.get('features')){
        featureStyle = setClusterSymbol(feature);
    }
    else{
        featureStyle = setSymbol(feature);
    }
    feature.setStyle(featureStyle);
}

function spiderifyPoints(features){
    let style;
    let cf;
    let p;
    let a;
    let max;
    spiderCluster = 1;
    spiderFeature = '';
    const spiderFeatures = [];
    for(let f in features){
        if(features.hasOwnProperty(f)){
            const feature = features[f];
            hideFeature(feature);
            hiddenClusters.push(feature);
            if(feature.get('features')){
                const addFeatures = feature.get('features');
                for(let i in addFeatures){
                    if(addFeatures.hasOwnProperty(i)){
                        spiderFeatures.push(addFeatures[i]);
                    }
                }
            }
            else{
                spiderFeatures.push(feature);
            }
        }
    }

    const source = layersArr['spider'].getSource();
    source.clear();

    const center = features[0].getGeometry().getCoordinates();
    const pix = map.getView().getResolution();
    let r = pix * 12 * (0.5 + spiderFeatures.length / 4);
    if (spiderFeatures.length <= 10){
        max = Math.min(spiderFeatures.length, 10);
        for(let i in spiderFeatures){
            if(spiderFeatures.hasOwnProperty(i)){
                a = 2*Math.PI*i/max;
                if (max === 2 || max === 4) {
                    a += Math.PI/4;
                }
                p = [center[0]+r*Math.sin(a), center[1]+r*Math.cos(a)];
                cf = new ol.Feature({
                    'features':[spiderFeatures[i]],
                    geometry: new ol.geom.Point(p)
                });
                style = setClusterSymbol(cf);
                cf.setStyle(style);
                source.addFeature(cf);
            }
        }
    }
    else{
        a = 0;
        let radius;
        const d = 30;
        Math.min(60, spiderFeatures.length);
        for(let i in spiderFeatures){
            if(spiderFeatures.hasOwnProperty(i)){
                radius = d/2 + d*a/(2*Math.PI);
                a = a + (d+0.1)/radius;
                const dx = pix * radius * Math.sin(a);
                const dy = pix * radius * Math.cos(a);
                p = [center[0] + dx, center[1] + dy];
                cf = new ol.Feature({
                    'features': [spiderFeatures[i]],
                    geometry: new ol.geom.Point(p)
                });
                style = setClusterSymbol(cf);
                cf.setStyle(style);
                source.addFeature(cf);
            }
        }
    }
}

function stopDSAnimation(){
    dsAnimStop = true;
    /*tsOldestDate = dsAnimLow;
    tsNewestDate = dsAnimHigh;
    var lowDateValStr = getISOStrFromDateObj(dsAnimLow);
    var highDateValStr = getISOStrFromDateObj(dsAnimHigh);
    $("#sliderdiv").slider('values',0,tsOldestDate.getTime());
    $("#sliderdiv").slider('values',1,tsNewestDate.getTime());
    $("#custom-label-min").text(lowDateValStr);
    $("#custom-label-max").text(highDateValStr);
    document.getElementById("datesliderearlydate").value = lowDateValStr;
    document.getElementById("datesliderlatedate").value = highDateValStr;
    layersArr['pointv'].getSource().changed();*/
    dsAnimDuration = '';
    dsAnimTime = '';
    dsAnimImageSave = false;
    dsAnimReverse = false;
    dsAnimDual = false;
    dsAnimLow = '';
    dsAnimHigh = '';
    dsAnimation = '';
}

function toggleDateSlider(){
    dateSliderActive = document.getElementById("datesliderswitch").checked;
    if(dateSliderActive){
        if(dsOldestDate && dsNewestDate){
            if(dsOldestDate !== dsNewestDate){
                if(!clusterPoints){
                    //var dual = document.getElementById("dsdualtype").checked;
                    createDateSlider(true);
                }
                else{
                    returnClusters = true;
                    document.getElementById("clusterswitch").checked = false;
                    changeClusterSetting();
                    createDateSlider(true);
                }
            }
            else{
                alert('The current records on the map do not have a range of dates for the Date Slider to populate.');
            }
        }
        else{
            document.getElementById("datesliderswitch").checked = false;
            dateSliderActive = false;
            alert('Points must be loaded onto the map to use the Date Slider.');
        }
    }
    else{
        removeDateSlider();
    }
}

function toggleHeatMap(){
    showHeatMap = document.getElementById("heatmapswitch").checked;
    if(showHeatMap){
        layersArr['pointv'].setVisible(false);
        layersArr['heat'].setVisible(true);
    }
    else{
        if(returnClusters && !dateSliderActive){
            returnClusters = false;
            document.getElementById("clusterswitch").checked = true;
            changeClusterSetting();
        }
        layersArr['heat'].setVisible(false);
        layersArr['pointv'].setVisible(true);
    }
}

function toggleLayerDisplayMessage(){
    const core = document.getElementById("coreLayers").childNodes.length;
    const dragDrop = document.getElementById("dragDropLayers").childNodes.length;
    const conf = document.getElementById("confLayers").childNodes.length;
    if(core > 0 || dragDrop > 0 || conf > 0){
        document.getElementById("nolayermessage").style.display = "none";
    }
    else{
        $('#addLayers').popup('hide');
        document.getElementById("nolayermessage").style.display = "block";
    }
}

function toggleLayerQuerySelector(layerId) {
    primeLayerQuerySelectorFields(layerId);
    document.getElementById('spatialQuerySelectorLayerId').value = layerId;
    $('#addLayers').popup('hide');
    $('#layerqueryselector').popup('show');
}

function toggleLayerSymbology(layerID){
    const symbologyDivID = 'layerSymbology-' + layerID;
    if(document.getElementById(symbologyDivID).style.display === 'flex'){
        document.getElementById(symbologyDivID).style.display = 'none';
    }
    else{
        document.getElementById(symbologyDivID).style.display = 'flex';
    }
}

function toggleServerLayerVisibility(id,name,file,visible){
    const sortingScrollerDivId = 'layerOrderDiv-' + id;
    const symbologyButtonId = 'layerSymbologyButton-' + id;
    const queryButtonId = 'layerQueryButton-' + id;
    if(visible === true){
        if(document.getElementById(sortingScrollerDivId)){
            document.getElementById(sortingScrollerDivId).style.display = 'flex';
        }
        if(document.getElementById(symbologyButtonId)){
            document.getElementById(symbologyButtonId).style.display = 'block';
        }
        if(document.getElementById(queryButtonId)){
            document.getElementById(queryButtonId).style.display = 'block';
        }
        loadServerLayer(id,file);
        addLayerToSelList(id,name,false);
        addLayerToLayerOrderArr(id);
    }
    else{
        if(document.getElementById(sortingScrollerDivId)){
            document.getElementById(sortingScrollerDivId).style.display = 'none';
        }
        if(document.getElementById(symbologyButtonId)){
            document.getElementById(symbologyButtonId).style.display = 'none';
        }
        if(document.getElementById(queryButtonId)){
            document.getElementById(queryButtonId).style.display = 'none';
        }
        removeServerLayer(id);
        removeLayerToSelList(id);
        removeLayerFromLayerOrderArr(id);
    }
}

function toggleUserLayerVisibility(id,name,visible){
    let layerId = id;
    if(id === 'pointv' && showHeatMap) {
        layerId = 'heat';
    }
    if(visible === true){
        layersArr[layerId].setVisible(true);
        addLayerToSelList(id,name,false);
        if(!coreLayers.includes(id)){
            addLayerToLayerOrderArr(id);
        }
    }
    else{
        layersArr[layerId].setVisible(false);
        removeLayerToSelList(id);
        if(!coreLayers.includes(id)){
            removeLayerFromLayerOrderArr(id);
        }
    }
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
        const mouseOverLabel = "openOccidInfoBox(" + seloccid + ",'" + infoArr['collector'] + "');";
        let labelHTML = '<a href="#" onmouseover="' + mouseOverLabel + '" onmouseout="closeOccidInfoBox();" onclick="openIndPopup(' + seloccid + '); return false;">';
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

function validateFeatureDate(feature){
    let valid = false;
    if(feature.get('coll_year')){
        const fyear = Number(feature.get('coll_year'));
        if(fyear.toString().length === 4 && fyear > 1500){
            const fmonth = (feature.get('coll_month') ? Number(feature.get('coll_month')) : 1);
            const fday = (feature.get('coll_day') ? Number(feature.get('coll_day')) : 1);
            const fDate = new Date();
            fDate.setFullYear(fyear, fmonth - 1, fday);
            if(fDate > tsOldestDate && fDate < tsNewestDate){
                valid = true;
            }
        }
    }
    return valid;
}

function vectorizeRaster(){
    showWorking();
    setTimeout(function() {
        let selectedClone = null;
        let shapeCount = 0;
        const turfFeatureArr = [];
        const targetRaster = document.getElementById("targetrasterselect").value;
        const valLow = document.getElementById("vectorizeRasterValueLow").value;
        const valHigh = document.getElementById("vectorizeRasterValueHigh").value;
        const resolutionVal = document.getElementById("vectorizeRasterResolution").value;
        if(targetRaster === ''){
            alert("Please select a target raster layer.");
        }
        else if(resolutionVal === '' || isNaN(resolutionVal)){
            alert("Please enter a number for the resolution in kilometers in which to vectorize the raster.");
        }
        else if(valLow === '' || isNaN(valLow) || valHigh === '' || isNaN(valHigh)){
            alert("Please enter high and low numbers for the value range.");
        }
        else{
            selectInteraction.getFeatures().forEach((feature) => {
                selectedClone = feature.clone();
                const geoType = selectedClone.getGeometry().getType();
                if(geoType === 'Polygon' || geoType === 'MultiPolygon' || geoType === 'Circle'){
                    shapeCount++;
                }
            });
            if(shapeCount === 1){
                const geoJSONFormat = new ol.format.GeoJSON();
                const selectiongeometry = selectedClone.getGeometry();
                selectiongeometry.transform(mapProjection, wgs84Projection);
                const geojsonStr = geoJSONFormat.writeGeometry(selectiongeometry);
                const featCoords = JSON.parse(geojsonStr).coordinates;
                const extentBBox = turf.bbox(turf.polygon(featCoords));
                const gridPoints = turf.pointGrid(extentBBox, resolutionVal, {units: 'kilometers',mask: turf.polygon(featCoords)});
                const gridPointFeatures = geoJSONFormat.readFeatures(gridPoints);
                const imageIndex = targetRaster + 'Image';
                const image = layersArr[imageIndex];
                const meta = image.getFileDirectory();
                const x_min = meta.ModelTiepoint[3];
                const x_max = x_min + meta.ModelPixelScale[0] * meta.ImageWidth;
                const y_min = meta.ModelTiepoint[4];
                const y_max = y_min - meta.ModelPixelScale[1] * meta.ImageLength;
                const bands = image.readRasters();
                const canvasElement = document.createElement('canvas');
                const minValue = 0;
                const maxValue = 1200;
                const plot = new plotty.plot({
                    canvas: canvasElement,
                    data: bands[0],
                    width: image.getWidth(),
                    height: image.getHeight(),
                    domain: [minValue, maxValue],
                    colorScale: 'earth'
                });
                gridPointFeatures.forEach(function(feature){
                    const coords = feature.getGeometry().getCoordinates();
                    const x = Math.floor(image.getWidth()*(coords[0] - x_min)/(x_max - x_min));
                    const y = image.getHeight()-Math.ceil(image.getHeight()*(coords[1] - y_max)/(y_min - y_max));
                    if(coords[0] >= x_min && coords[0] <= x_max && coords[1] <= y_min && coords[1] >= y_max){
                        const rasterValue = plot.atPoint(x,y);
                        if(Number(rasterValue) >= Number(valLow) && Number(rasterValue) <= Number(valHigh)){
                            turfFeatureArr.push(turf.point(coords));
                        }
                    }
                });
                const turfFeatureCollection = turf.featureCollection(turfFeatureArr);
                let concavepoly = '';
                try{
                    const maxEdgeVal = Number(resolutionVal) + (Number(resolutionVal) / 2);
                    const options = {units: 'kilometers', maxEdge: maxEdgeVal};
                    concavepoly = turf.concave(turfFeatureCollection,options);
                }
                catch(e){
                    alert('Concave polygon was not able to be calculated. Perhaps try using a larger value for the maximum edge length.');
                }
                if(concavepoly){
                    const cnvepoly = geoJSONFormat.readFeature(concavepoly);
                    cnvepoly.getGeometry().transform(wgs84Projection,mapProjection);
                    selectsource.addFeature(cnvepoly);
                }
                hideWorking();
            }
            else{
                hideWorking();
                alert('You must have one polygon or circle, and only one polygon or circle, selected in your Shapes layer to serve as the bounds of the vectorization.');
            }
        }
    }, 50);
}

function verifyCollForm(){
    const f = document.getElementById("spatialcollsearchform");
    let formVerified = false;
    for(let h=0; h<f.length; h++){
        if(f.elements[h].name === "db[]" && f.elements[h].checked){
            formVerified = true;
            break;
        }
        if(f.elements[h].name === "cat[]" && f.elements[h].checked){
            formVerified = true;
            break;
        }
    }
    if(formVerified){
        for(let i=0; i<f.length; i++){
            if(f.elements[i].name === "cat[]" && f.elements[i].checked){
                const childrenEle = document.getElementById('cat-' + f.elements[i].value).children;
                for(let j=0; j<childrenEle.length; j++){
                    if(childrenEle[j].tagName === "DIV"){
                        const divChildren = childrenEle[j].children;
                        for(let k=0; k<divChildren.length; k++){
                            const divChildren2 = divChildren[k].children;
                            for(let l=0; l<divChildren2.length; l++){
                                if(divChildren2[l].tagName === "INPUT"){
                                    divChildren2[l].checked = false;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return formVerified;
}

function writeMySQLWktString(type,geocoords) {
    let long;
    let lat;
    let wktStr = '';
    let coordStr = '';
    if(type === 'Polygon'){
        for(let i in geocoords){
            if(geocoords.hasOwnProperty(i)){
                coordStr += '(';
                for(let c in geocoords[i]) {
                    if(geocoords[i].hasOwnProperty(c)){
                        lat = geocoords[i][c][1];
                        long = geocoords[i][c][0];
                        coordStr += lat+' '+long+',';
                    }
                }
                coordStr = coordStr.substring(0,coordStr.length-1);
                coordStr += '),';
            }
        }
        coordStr = coordStr.substring(0,coordStr.length-1);
        wktStr = 'POLYGON('+coordStr+')';
    }
    else if(type === 'MultiPolygon'){
        for(let i in geocoords){
            if(geocoords.hasOwnProperty(i)){
                coordStr += '(';
                for(let r in geocoords[i]){
                    if(geocoords[i].hasOwnProperty(r)){
                        coordStr += '(';
                        for(let c in geocoords[i][r]) {
                            if(geocoords[i][r].hasOwnProperty(c)){
                                lat = geocoords[i][r][c][1];
                                long = geocoords[i][r][c][0];
                                coordStr += lat+' '+long+',';
                            }
                        }
                        coordStr = coordStr.substring(0,coordStr.length-1);
                        coordStr += '),';
                    }
                }
                coordStr = coordStr.substring(0,coordStr.length-1);
                coordStr += '),';
            }
        }
        coordStr = coordStr.substring(0,coordStr.length-1);
        wktStr = 'MULTIPOLYGON('+coordStr+')';
    }

    return wktStr;
}

function zoomToSelections(){
    const extent = ol.extent.createEmpty();
    for(let i in selections){
        if(selections.hasOwnProperty(i)){
            let point = '';
            if(clusterPoints){
                const cluster = findOccCluster(selections[i]);
                point = findOccPointInCluster(cluster,selections[i]);
            }
            else{
                point = findOccPoint(selections[i]);
            }
            if(point){
                ol.extent.extend(extent, point.getGeometry().getExtent());
            }
        }
    }
    map.getView().fit(extent, map.getSize());
}

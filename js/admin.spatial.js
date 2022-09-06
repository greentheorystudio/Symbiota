function buildLayerListElement(lArr){
    const layerLiId = 'layer-' + lArr['id'];
    const layerLi = document.createElement('li');
    layerLi.setAttribute("id",layerLiId);
    layerLi.setAttribute("class","layer");
    const layerContentDiv = document.createElement('div');
    layerContentDiv.setAttribute("class","layerContent");
    const layerHeaderDiv = document.createElement('div');
    layerHeaderDiv.setAttribute("class","layer-header");
    const layerTitleDiv = document.createElement('div');
    const layerTitleB = document.createElement('b');
    layerTitleB.innerHTML = lArr['layerName'];
    layerTitleDiv.appendChild(layerTitleB);
    layerHeaderDiv.appendChild(layerTitleDiv);
    const layerEditIconDiv = document.createElement('div');
    const layerEditIconA = document.createElement('a');
    const layerEditIconAOnclickVal = "openLayerEditWindow('" + lArr['id'] + "');";
    layerEditIconA.setAttribute("href","#");
    layerEditIconA.setAttribute("onclick",layerEditIconAOnclickVal);
    const layerEditIconI = document.createElement('i');
    layerEditIconI.setAttribute("style","width:20px;height:20px;");
    layerEditIconI.setAttribute("title","Edit layer");
    layerEditIconI.setAttribute("class","fas fa-edit");
    layerEditIconA.appendChild(layerEditIconI);
    layerEditIconDiv.appendChild(layerEditIconA);
    layerHeaderDiv.appendChild(layerEditIconDiv);
    layerContentDiv.appendChild(layerHeaderDiv);
    if(lArr.hasOwnProperty('layerDescription') && lArr['layerDescription']){
        const layerDescDiv = document.createElement('div');
        layerDescDiv.innerHTML = lArr['layerDescription'];
        layerContentDiv.appendChild(layerDescDiv);
    }
    if(lArr.hasOwnProperty('providedBy') || lArr.hasOwnProperty('sourceURL')){
        layerContentDiv.appendChild(buildLayerControllerLayerProvidedByElement(lArr));
    }
    if(lArr.hasOwnProperty('dateAquired') || lArr.hasOwnProperty('dateUploaded')){
        layerContentDiv.appendChild(buildLayerControllerLayerDateElement(lArr));
    }
    const layerFileDiv = document.createElement('div');
    layerFileDiv.innerHTML = '<b>File:</b> ' + lArr['file'];
    layerContentDiv.appendChild(layerFileDiv);
    layerLi.appendChild(layerContentDiv);
    return layerLi;
}

function buildNewLayerBlockObjFromData(id,dataArr){
    const blockObj = {};
    blockObj['id'] = id;
    blockObj['type'] = 'layer';
    blockObj['file'] = dataArr['file'];
    blockObj['fileType'] = dataArr['fileType'];
    blockObj['layerName'] = dataArr['layerName'];
    if(dataArr['layerDescription'] !== ''){
        blockObj['layerDescription'] = dataArr['layerDescription'];
    }
    if(dataArr['providedBy'] !== ''){
        blockObj['providedBy'] = dataArr['providedBy'];
    }
    if(dataArr['sourceURL'] !== ''){
        blockObj['sourceURL'] = dataArr['sourceURL'];
    }
    if(dataArr['dateAquired'] !== ''){
        blockObj['dateAquired'] = dataArr['dateAquired'];
    }
    if(dataArr['dateUploaded'] !== ''){
        blockObj['dateUploaded'] = dataArr['dateUploaded'];
    }
    blockObj['opacity'] = dataArr['opacity'];
    if(dataArr['fileType'] === 'tif'){
        blockObj['colorScale'] = dataArr['colorScale'];
    }
    else{
        blockObj['fillColor'] = dataArr['fillColor'];
        blockObj['borderColor'] = dataArr['borderColor'];
        blockObj['borderWidth'] = dataArr['borderWidth'];
        blockObj['pointRadius'] = dataArr['pointRadius'];
    }
    return blockObj;
}

function clearAddForms() {
    document.getElementById('addLayerGroupName').value = '';
    document.getElementById('addLayerFile').value = '';
    document.getElementById('addLayerName').value = '';
    document.getElementById('addLayerDescription').value = '';
    document.getElementById('addLayerProvidedBy').value = '';
    document.getElementById('addLayerSourceURL').value = '';
    document.getElementById('addLayerDateAquired').value = '';
}

function clearEditWindows() {
    document.getElementById('editLayerGroupName').value = '';
    document.getElementById('editLayerGroupId').value = '';
    document.getElementById('editLayerName').value = '';
    document.getElementById('editLayerDescription').value = '';
    document.getElementById('editLayerProvidedBy').value = '';
    document.getElementById('editLayerSourceURL').value = '';
    document.getElementById('editLayerDateAquired').value = '';
    document.getElementById('editLayerDateUploaded').innerHTML = '';
    document.getElementById('editLayerFile').innerHTML = '';
    document.getElementById('editLayerId').value = '';
    document.getElementById('editLayerColorScale').value = 'autumn';
    document.getElementById('editLayerBorderColor').color.fromString('ffffff');
    document.getElementById('editLayerFillColor').color.fromString('ffffff');
    document.getElementById('editVectorSymbology').style.display = "none";
    document.getElementById('editRasterSymbology').style.display = "none";
    document.getElementById('layerFileUpdate').value = '';
}

function closePopup(id) {
    $('#'+id).popup('hide');
    clearEditWindows();
}

function createLayer(layerName,filename){
    const newLayerId = Date.now();
    const date = new Date();
    let fileType = filename.split('.').pop();
    if(fileType === 'tiff'){
        fileType = 'tif';
    }
    layerData[newLayerId] = {};
    layerData[newLayerId]['id'] = newLayerId;
    layerData[newLayerId]['type'] = 'layer';
    layerData[newLayerId]['file'] = filename;
    layerData[newLayerId]['fileType'] = fileType;
    layerData[newLayerId]['layerName'] = layerName;
    layerData[newLayerId]['layerDescription'] = document.getElementById('addLayerDescription').value;
    layerData[newLayerId]['providedBy'] = document.getElementById('addLayerProvidedBy').value;
    layerData[newLayerId]['sourceURL'] = document.getElementById('addLayerSourceURL').value;
    layerData[newLayerId]['dateAquired'] = document.getElementById('addLayerDateAquired').value;
    layerData[newLayerId]['dateUploaded'] = date.toISOString().split('T')[0];
    layerData[newLayerId]['opacity'] = dragDropOpacity;
    if(fileType === 'tif'){
        layerData[newLayerId]['colorScale'] = dragDropRasterColorScale;
    }
    else{
        layerData[newLayerId]['fillColor'] = dragDropFillColor;
        layerData[newLayerId]['borderColor'] = dragDropBorderColor;
        layerData[newLayerId]['borderWidth'] = dragDropBorderWidth;
        layerData[newLayerId]['pointRadius'] = dragDropPointRadius;
    }
    const layerLiId = 'layer-' + newLayerId;
    const layerLi = document.createElement('li');
    layerLi.setAttribute("id",layerLiId);
    layerLi.setAttribute("class","layer");
    const layerContentDiv = document.createElement('div');
    layerContentDiv.setAttribute("class","layerContent");
    const layerHeaderDiv = document.createElement('div');
    layerHeaderDiv.setAttribute("class","layer-header");
    const layerTitleDiv = document.createElement('div');
    const layerTitleB = document.createElement('b');
    layerTitleB.innerHTML = layerName;
    layerTitleDiv.appendChild(layerTitleB);
    layerHeaderDiv.appendChild(layerTitleDiv);
    const layerEditIconDiv = document.createElement('div');
    const layerEditIconA = document.createElement('a');
    const layerEditIconAOnclickVal = "openLayerEditWindow('" + newLayerId + "');";
    layerEditIconA.setAttribute("href","#");
    layerEditIconA.setAttribute("onclick",layerEditIconAOnclickVal);
    const layerEditIconI = document.createElement('i');
    layerEditIconI.setAttribute("style","width:20px;height:20px;");
    layerEditIconI.setAttribute("title","Edit layer");
    layerEditIconI.setAttribute("class","fas fa-edit");
    layerEditIconA.appendChild(layerEditIconI);
    layerEditIconDiv.appendChild(layerEditIconA);
    layerHeaderDiv.appendChild(layerEditIconDiv);
    layerContentDiv.appendChild(layerHeaderDiv);
    if(layerData[newLayerId]['layerDescription']){
        const layerDescDiv = document.createElement('div');
        layerDescDiv.innerHTML = layerData[newLayerId]['layerDescription'];
        layerContentDiv.appendChild(layerDescDiv);
    }
    if(layerData[newLayerId]['providedBy'] || layerData[newLayerId]['sourceURL']){
        layerContentDiv.appendChild(buildLayerControllerLayerProvidedByElement(layerData[newLayerId]));
    }
    layerContentDiv.appendChild(buildLayerControllerLayerDateElement(layerData[newLayerId]));
    const layerFileDiv = document.createElement('div');
    layerFileDiv.innerHTML = '<b>File:</b> ' + filename;
    layerContentDiv.appendChild(layerFileDiv);
    layerLi.appendChild(layerContentDiv);
    document.getElementById("layerList").insertBefore(layerLi, document.getElementById("layerList").firstChild);
    hideAddBoxes();
    clearAddForms();
    saveLayerConfigChanges();
}

function createLayerGroup(){
    const groupName = document.getElementById("addLayerGroupName").value;
    if(groupName !== ''){
        const newGroupId = Date.now();
        layerData[newGroupId] = {};
        layerData[newGroupId]['id'] = newGroupId;
        layerData[newGroupId]['type'] = 'layerGroup';
        layerData[newGroupId]['name'] = groupName;
        const layerGroupdLiId = 'layerGroup-' + newGroupId;
        const layerGroupContainerId = 'layerGroupList-' + newGroupId;
        const layerGroupLi = document.createElement('li');
        layerGroupLi.setAttribute("id", layerGroupdLiId);
        layerGroupLi.setAttribute("class", "group");
        const layerGroupHeaderDiv = document.createElement('div');
        layerGroupHeaderDiv.setAttribute("class","layer-group-header");
        const layerGroupTitleDiv = document.createElement('div');
        layerGroupTitleDiv.setAttribute("style","display:flex;gap:15px;justify-content:flex-start;align-items:center;");
        const layerGroupTitleB = document.createElement('b');
        layerGroupTitleB.innerHTML = groupName;
        layerGroupTitleDiv.appendChild(layerGroupTitleB);
        const layerGroupShowIconI = document.createElement('i');
        const layerGroupShowIconIId = 'showLayerGroupButton-' + newGroupId;
        const layerGroupShowIconIOnclickVal = "showLayerGroup('" + newGroupId + "');";
        layerGroupShowIconI.setAttribute("id",layerGroupShowIconIId);
        layerGroupShowIconI.setAttribute("style","display:none;width:15px;height:15px;cursor:pointer;");
        layerGroupShowIconI.setAttribute("title","Show layers");
        layerGroupShowIconI.setAttribute("class","fas fa-plus");
        layerGroupShowIconI.setAttribute("onclick",layerGroupShowIconIOnclickVal);
        layerGroupTitleDiv.appendChild(layerGroupShowIconI);
        const layerGroupHideIconI = document.createElement('i');
        const layerGroupHideIconIId = 'hideLayerGroupButton-' + newGroupId;
        const layerGroupHideIconIOnclickVal = "hideLayerGroup('" + newGroupId + "');";
        layerGroupHideIconI.setAttribute("id",layerGroupHideIconIId);
        layerGroupHideIconI.setAttribute("style","width:15px;height:15px;cursor:pointer;");
        layerGroupHideIconI.setAttribute("title","Hide layers");
        layerGroupHideIconI.setAttribute("class","fas fa-minus");
        layerGroupHideIconI.setAttribute("onclick",layerGroupHideIconIOnclickVal);
        layerGroupTitleDiv.appendChild(layerGroupHideIconI);
        layerGroupHeaderDiv.appendChild(layerGroupTitleDiv);
        const layerGroupEditIconDiv = document.createElement('div');
        const layerGroupEditIconI = document.createElement('i');
        const layerGroupEditIconIOnclickVal = "openLayerGroupEditWindow('" + newGroupId + "');";
        layerGroupEditIconI.setAttribute("style","width:20px;height:20px;cursor:pointer;margin-right:10px");
        layerGroupEditIconI.setAttribute("title","Edit layer group");
        layerGroupEditIconI.setAttribute("class","fas fa-edit");
        layerGroupEditIconI.setAttribute("onclick",layerGroupEditIconIOnclickVal);
        layerGroupEditIconDiv.appendChild(layerGroupEditIconI);
        layerGroupHeaderDiv.appendChild(layerGroupEditIconDiv);
        layerGroupLi.appendChild(layerGroupHeaderDiv);
        const layerGroupContainerOl = document.createElement('ol');
        layerGroupContainerOl.setAttribute("id", layerGroupContainerId);
        layerGroupContainerOl.setAttribute("class", "layer-group-container");
        layerGroupLi.appendChild(layerGroupContainerOl);
        document.getElementById("layerList").insertBefore(layerGroupLi, document.getElementById("layerList").firstChild);
        hideAddBoxes();
        clearAddForms();
        saveLayerConfigChanges();
    }
    else{
        alert("You need to enter a Group Name before adding a layer group.");
    }
}

function deleteLayer() {
    const layerId = Number(document.getElementById('editLayerId').value);
    if(confirm("Are you sure you want to delete this layer? This will delete the layer data file from the server and cannot be undone.")){
        const http = new XMLHttpRequest();
        const url = "rpc/mapServerConfigurationController.php";
        const filename = layerData[layerId]['file'].replaceAll('&','%<amp>%');
        const params = 'action=deleteMapDataFile&filename='+filename;
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                if(Number(http.responseText) !== 1){
                    document.getElementById("statusStr").innerHTML = 'Error deleting data file';
                    setTimeout(function () {
                        document.getElementById("statusStr").innerHTML = '';
                    }, 5000);
                }
                else{
                    const layerElementId = 'layer-' + layerId;
                    document.getElementById(layerElementId).remove();
                    $('#layereditwindow').popup('hide');
                    clearEditWindows();
                    saveLayerConfigChanges();
                }
            }
        };
        http.send(params);
    }
}

function deleteLayerGroup() {
    const groupId = Number(document.getElementById('editLayerGroupId').value);
    const layerGroupContainerId = 'layerGroupList-' + groupId;
    const layerGroupBlocks = document.getElementById(layerGroupContainerId).querySelectorAll('li');
    if(layerGroupBlocks.length > 0){
        alert('Please move all layers out of the layer group before deleting the group.');
    }
    else if(confirm("Are you sure you want to delete this layer group? This cannot be undone.")){
        const layerGroupElementId = 'layerGroup-' + groupId;
        document.getElementById(layerGroupElementId).remove();
        $('#layergroupeditwindow').popup('hide');
        clearEditWindows();
        saveLayerConfigChanges();
    }
}

function formatPath(path){
    if(path.charAt(path.length - 1) === '/'){
        path = path.substring(0, path.length - 1);
    }
    if(path.charAt(0) !== '/'){
        path = '/' + path;
    }
    return path;
}

function hideAddBoxes() {
    document.getElementById('addLayerDiv').style.display = "none";
    document.getElementById('addLayerGroupDiv').style.display = "none";
}

function hideAddLayer() {
    document.getElementById('addLayerDiv').style.display = "none";
}

function hideAddLayerGroup() {
    document.getElementById('addLayerGroupDiv').style.display = "none";
}

function hideLayerGroup(layerId) {
    const groupId = 'layerGroupList-' + layerId;
    const hideButtonId = 'hideLayerGroupButton-' + layerId;
    const showButtonId = 'showLayerGroupButton-' + layerId;
    document.getElementById(groupId).style.display = "none";
    document.getElementById(hideButtonId).style.display = "none";
    document.getElementById(showButtonId).style.display = "block";
}

function openLayerEditWindow(id) {
    document.getElementById('editLayerName').value = layerData[id]['layerName'];
    document.getElementById('editLayerDescription').value = layerData[id]['layerDescription'];
    document.getElementById('editLayerProvidedBy').value = layerData[id]['providedBy'];
    document.getElementById('editLayerSourceURL').value = layerData[id]['sourceURL'];
    document.getElementById('editLayerDateAquired').value = layerData[id]['dateAquired'];
    document.getElementById('editLayerDateUploaded').innerHTML = layerData[id]['dateUploaded'];
    document.getElementById('editLayerFile').innerHTML = layerData[id]['file'];
    document.getElementById('editLayerId').value = id;
    if(layerData[id]['fileType'] === 'tif'){
        document.getElementById('editLayerColorScale').value = layerData[id]['colorScale'];
        document.getElementById('editRasterSymbology').style.display = "block";
    }
    else{
        jscolor.init();
        document.getElementById('editLayerBorderColor').color.fromString(layerData[id]['borderColor']);
        document.getElementById('editLayerFillColor').color.fromString(layerData[id]['fillColor']);
        $( '#editLayerOpacity' ).spinner({
            step: 0.1,
            min: 0,
            max: 1,
            numberFormat: "n"
        });
        $( '#editLayerBorderWidth' ).spinner({
            step: 1,
            min: 0,
            numberFormat: "n"
        });
        $( '#editLayerPointRadius' ).spinner({
            step: 1,
            min: 0,
            numberFormat: "n"
        });
        $( '#editLayerOpacity' ).spinner( "value", Number(layerData[id]['opacity']) );
        $( '#editLayerBorderWidth' ).spinner( "value", Number(layerData[id]['borderWidth']) );
        $( '#editLayerPointRadius' ).spinner( "value", Number(layerData[id]['pointRadius']) );
        document.getElementById('editVectorSymbology').style.display = "block";
    }
    $('#layereditwindow').popup('show');
}

function openLayerGroupEditWindow(id) {
    document.getElementById('editLayerGroupName').value = layerData[id]['name'];
    document.getElementById('editLayerGroupId').value = id;
    $('#layergroupeditwindow').popup('show');
}

function openUpdateFileUpload() {
    document.getElementById('updateLayerFileBox').style.display = "block";
}

function processAddLayerListElement(lArr, parentElement) {
    const layerLiId = 'layer-' + lArr['id'];
    if (!document.getElementById(layerLiId)) {
        const layerLi = buildLayerListElement(lArr);
        parentElement.appendChild(layerLi);
    }
}

function processAddLayerListGroup(lArr, parentElement) {
    const layerGroupdLiId = 'layerGroup-' + lArr['id'];
    if (!document.getElementById(layerGroupdLiId)) {
        const layersArr = lArr['layers'];
        const layerGroupContainerId = 'layerGroupList-' + lArr['id'];
        const layerGroupLi = document.createElement('li');
        layerGroupLi.setAttribute("id", layerGroupdLiId);
        layerGroupLi.setAttribute("class", "group");
        const layerGroupHeaderDiv = document.createElement('div');
        layerGroupHeaderDiv.setAttribute("class","layer-group-header");
        const layerGroupTitleDiv = document.createElement('div');
        layerGroupTitleDiv.setAttribute("style","display:flex;gap:15px;justify-content:flex-start;align-items:center;");
        const layerGroupTitleB = document.createElement('b');
        layerGroupTitleB.innerHTML = lArr['name'];
        layerGroupTitleDiv.appendChild(layerGroupTitleB);
        const layerGroupShowIconI = document.createElement('i');
        const layerGroupShowIconIId = 'showLayerGroupButton-' + lArr['id'];
        const layerGroupShowIconIOnclickVal = "showLayerGroup('" + lArr['id'] + "');";
        layerGroupShowIconI.setAttribute("id",layerGroupShowIconIId);
        layerGroupShowIconI.setAttribute("style","display:none;width:15px;height:15px;cursor:pointer;");
        layerGroupShowIconI.setAttribute("title","Show layers");
        layerGroupShowIconI.setAttribute("class","fas fa-plus");
        layerGroupShowIconI.setAttribute("onclick",layerGroupShowIconIOnclickVal);
        layerGroupTitleDiv.appendChild(layerGroupShowIconI);
        const layerGroupHideIconI = document.createElement('i');
        const layerGroupHideIconIId = 'hideLayerGroupButton-' + lArr['id'];
        const layerGroupHideIconIOnclickVal = "hideLayerGroup('" + lArr['id'] + "');";
        layerGroupHideIconI.setAttribute("id",layerGroupHideIconIId);
        layerGroupHideIconI.setAttribute("style","width:15px;height:15px;cursor:pointer;");
        layerGroupHideIconI.setAttribute("title","Hide layers");
        layerGroupHideIconI.setAttribute("class","fas fa-minus");
        layerGroupHideIconI.setAttribute("onclick",layerGroupHideIconIOnclickVal);
        layerGroupTitleDiv.appendChild(layerGroupHideIconI);
        layerGroupHeaderDiv.appendChild(layerGroupTitleDiv);
        const layerGroupEditIconDiv = document.createElement('div');
        const layerGroupEditIconI = document.createElement('i');
        const layerGroupEditIconIOnclickVal = "openLayerGroupEditWindow('" + lArr['id'] + "');";
        layerGroupEditIconI.setAttribute("style","width:20px;height:20px;cursor:pointer;margin-right:10px");
        layerGroupEditIconI.setAttribute("title","Edit layer group");
        layerGroupEditIconI.setAttribute("class","fas fa-edit");
        layerGroupEditIconI.setAttribute("onclick",layerGroupEditIconIOnclickVal);
        layerGroupEditIconDiv.appendChild(layerGroupEditIconI);
        layerGroupHeaderDiv.appendChild(layerGroupEditIconDiv);
        layerGroupLi.appendChild(layerGroupHeaderDiv);
        const layerGroupContainerOl = document.createElement('ol');
        layerGroupContainerOl.setAttribute("id", layerGroupContainerId);
        layerGroupContainerOl.setAttribute("class", "layer-group-container");
        layerGroupLi.appendChild(layerGroupContainerOl);
        parentElement.appendChild(layerGroupLi);
        for (let i in layersArr) {
            if (layersArr.hasOwnProperty(i)) {
                const layerId = layersArr[i]['id'];
                const layerType = layersArr[i]['type'];
                layerData[layerId] = {};
                layerData[layerId]['type'] = layerType;
                processLayerDataFromLayerArr(layersArr[i],layerId);
                processAddLayerListElement(layersArr[i], layerGroupContainerOl)
            }
        }
    }
}

function processLayerDataFromLayerArr(lArr,id) {
    layerData[id]['file'] = lArr['file'];
    layerData[id]['fileType'] = lArr['fileType'];
    layerData[id]['layerName'] = lArr['layerName'];
    layerData[id]['layerDescription'] = lArr.hasOwnProperty('layerDescription') ? lArr['layerDescription'] : '';
    layerData[id]['providedBy'] = lArr.hasOwnProperty('providedBy') ? lArr['providedBy'] : '';
    layerData[id]['sourceURL'] = lArr.hasOwnProperty('sourceURL') ? lArr['sourceURL'] : '';
    layerData[id]['dateAquired'] = lArr.hasOwnProperty('dateAquired') ? lArr['dateAquired'] : '';
    layerData[id]['dateUploaded'] = lArr.hasOwnProperty('dateUploaded') ? lArr['dateUploaded'] : '';
    layerData[id]['opacity'] = (lArr.hasOwnProperty('opacity') && lArr['opacity']) ? lArr['opacity'] : dragDropOpacity;
    if(lArr['fileType'] === 'tif'){
        layerData[id]['colorScale'] = (lArr.hasOwnProperty('colorScale') && lArr['colorScale']) ? lArr['colorScale'] : dragDropRasterColorScale;
    }
    else{
        layerData[id]['fillColor'] = (lArr.hasOwnProperty('fillColor') && lArr['fillColor']) ? lArr['fillColor'] : dragDropFillColor;
        layerData[id]['borderColor'] = (lArr.hasOwnProperty('borderColor') && lArr['borderColor']) ? lArr['borderColor'] : dragDropBorderColor;
        layerData[id]['borderWidth'] = (lArr.hasOwnProperty('borderWidth') && lArr['borderWidth']) ? lArr['borderWidth'] : dragDropBorderWidth;
        layerData[id]['pointRadius'] = (lArr.hasOwnProperty('pointRadius') && lArr['pointRadius']) ? lArr['pointRadius'] : dragDropPointRadius;
    }
}

function processSaveDisplaySettings(){
    const data = {};
    const baseLayerValue = document.getElementById('base-map').value;
    const zoomValue = map.getView().getZoom();
    const centerPoint = map.getView().getCenter();
    const centerPointFixed = ol.proj.transform(centerPoint, 'EPSG:3857', 'EPSG:4326');
    const centerPointValue = '[' + centerPointFixed.toString() + ']';
    data['SPATIAL_INITIAL_BASE_LAYER'] = baseLayerValue;
    data['SPATIAL_INITIAL_ZOOM'] = zoomValue;
    data['SPATIAL_INITIAL_CENTER'] = centerPointValue;
    const jsonData = JSON.stringify(data);
    const http = new XMLHttpRequest();
    const url = "rpc/configurationModelController.php";
    let params = 'action=update&data='+jsonData;
    //console.log(url+'?'+params);
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function () {
        if (http.readyState === 4 && http.status === 200) {
            document.getElementById("statusStr").innerHTML = 'Settings saved!';
            setTimeout(function () {
                document.getElementById("statusStr").innerHTML = '';
            }, 5000);
        }
    };
    http.send(params);
}

function processSaveSymbologySettings(){
    const data = {};
    const pointsClusterValue = document.getElementById('pointsCluster').checked;
    const pointsClusterDistanceValue = $('#pointsClusterDistance').spinner( "value" );
    const pointsDisplayHeatMapValue = document.getElementById('pointsDisplayHeatMap').checked;
    const pointsHeatMapRadiusValue = $('#pointsHeatMapRadius').spinner( "value" );
    const pointsHeatMapBlurValue = $('#pointsHeatMapBlur').spinner( "value" );
    const pointsBorderColorValue = document.getElementById('pointsBorderColor').value;
    const pointsFillColorValue = document.getElementById('pointsFillColor').value;
    const pointsBorderWidthValue = $('#pointsBorderWidth').spinner( "value" );
    const pointsPointRadiusValue = $('#pointsPointRadius').spinner( "value" );
    const pointsSelectionsBorderColorValue = document.getElementById('pointsSelectionsBorderColor').value;
    const pointsSelectionsBorderWidthValue = $('#pointsSelectionsBorderWidth').spinner( "value" );
    const shapesBorderColorValue = document.getElementById('shapesBorderColor').value;
    const shapesFillColorValue = document.getElementById('shapesFillColor').value;
    const shapesBorderWidthValue = $('#shapesBorderWidth').spinner( "value" );
    const shapesPointRadiusValue = $('#shapesPointRadius').spinner( "value" );
    const shapesOpacityValue = $('#shapesOpacity').spinner( "value" );
    const shapesSelectionsBorderColorValue = document.getElementById('shapesSelectionsBorderColor').value;
    const shapesSelectionsFillColorValue = document.getElementById('shapesSelectionsFillColor').value;
    const shapesSelectionsBorderWidthValue = $('#shapesSelectionsBorderWidth').spinner( "value" );
    const shapesSelectionsOpacityValue = $('#shapesSelectionsOpacity').spinner( "value" );
    const dragDropBorderColorValue = document.getElementById('dragDropBorderColor').value;
    const dragDropFillColorValue = document.getElementById('dragDropFillColor').value;
    const dragDropBorderWidthValue = $('#dragDropBorderWidth').spinner( "value" );
    const dragDropPointRadiusValue = $('#dragDropPointRadius').spinner( "value" );
    const dragDropOpacityValue = $('#dragDropOpacity').spinner( "value" );
    const dragDropRasterColorScaleValue = document.getElementById('dragDropRasterColorScale').value;
    data['SPATIAL_POINT_CLUSTER'] = '';
    data['SPATIAL_POINT_CLUSTER_DISTANCE'] = '';
    data['SPATIAL_POINT_DISPLAY_HEAT_MAP'] = '';
    data['SPATIAL_POINT_HEAT_MAP_RADIUS'] = '';
    data['SPATIAL_POINT_HEAT_MAP_BLUR'] = '';
    data['SPATIAL_POINT_FILL_COLOR'] = '';
    data['SPATIAL_POINT_BORDER_COLOR'] = '';
    data['SPATIAL_POINT_BORDER_WIDTH'] = '';
    data['SPATIAL_POINT_POINT_RADIUS'] = '';
    data['SPATIAL_POINT_SELECTIONS_BORDER_COLOR'] = '';
    data['SPATIAL_POINT_SELECTIONS_BORDER_WIDTH'] = '';
    data['SPATIAL_SHAPES_BORDER_COLOR'] = '';
    data['SPATIAL_SHAPES_FILL_COLOR'] = '';
    data['SPATIAL_SHAPES_BORDER_WIDTH'] = '';
    data['SPATIAL_SHAPES_POINT_RADIUS'] = '';
    data['SPATIAL_SHAPES_OPACITY'] = '';
    data['SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR'] = '';
    data['SPATIAL_SHAPES_SELECTIONS_FILL_COLOR'] = '';
    data['SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH'] = '';
    data['SPATIAL_SHAPES_SELECTIONS_OPACITY'] = '';
    data['SPATIAL_DRAGDROP_BORDER_COLOR'] = '';
    data['SPATIAL_DRAGDROP_FILL_COLOR'] = '';
    data['SPATIAL_DRAGDROP_BORDER_WIDTH'] = '';
    data['SPATIAL_DRAGDROP_POINT_RADIUS'] = '';
    data['SPATIAL_DRAGDROP_OPACITY'] = '';
    data['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] = '';
    const deleteJsonData = JSON.stringify(data);
    const http = new XMLHttpRequest();
    const url = "rpc/configurationModelController.php";
    let params = 'action=delete&data='+deleteJsonData;
    //console.log(url+'?'+params);
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function() {
        if(http.readyState === 4 && http.status === 200) {
            data['SPATIAL_POINT_CLUSTER'] = pointsClusterValue;
            data['SPATIAL_POINT_CLUSTER_DISTANCE'] = pointsClusterDistanceValue;
            data['SPATIAL_POINT_DISPLAY_HEAT_MAP'] = pointsDisplayHeatMapValue;
            data['SPATIAL_POINT_HEAT_MAP_RADIUS'] = pointsHeatMapRadiusValue;
            data['SPATIAL_POINT_HEAT_MAP_BLUR'] = pointsHeatMapBlurValue;
            data['SPATIAL_POINT_FILL_COLOR'] = pointsFillColorValue;
            data['SPATIAL_POINT_BORDER_COLOR'] = pointsBorderColorValue;
            data['SPATIAL_POINT_BORDER_WIDTH'] = pointsBorderWidthValue;
            data['SPATIAL_POINT_POINT_RADIUS'] = pointsPointRadiusValue;
            data['SPATIAL_POINT_SELECTIONS_BORDER_COLOR'] = pointsSelectionsBorderColorValue;
            data['SPATIAL_POINT_SELECTIONS_BORDER_WIDTH'] = pointsSelectionsBorderWidthValue;
            data['SPATIAL_SHAPES_BORDER_COLOR'] = shapesBorderColorValue;
            data['SPATIAL_SHAPES_FILL_COLOR'] = shapesFillColorValue;
            data['SPATIAL_SHAPES_BORDER_WIDTH'] = shapesBorderWidthValue;
            data['SPATIAL_SHAPES_POINT_RADIUS'] = shapesPointRadiusValue;
            data['SPATIAL_SHAPES_OPACITY'] = shapesOpacityValue;
            data['SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR'] = shapesSelectionsBorderColorValue;
            data['SPATIAL_SHAPES_SELECTIONS_FILL_COLOR'] = shapesSelectionsFillColorValue;
            data['SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH'] = shapesSelectionsBorderWidthValue;
            data['SPATIAL_SHAPES_SELECTIONS_OPACITY'] = shapesSelectionsOpacityValue;
            data['SPATIAL_DRAGDROP_BORDER_COLOR'] = dragDropBorderColorValue;
            data['SPATIAL_DRAGDROP_FILL_COLOR'] = dragDropFillColorValue;
            data['SPATIAL_DRAGDROP_BORDER_WIDTH'] = dragDropBorderWidthValue;
            data['SPATIAL_DRAGDROP_POINT_RADIUS'] = dragDropPointRadiusValue;
            data['SPATIAL_DRAGDROP_OPACITY'] = dragDropOpacityValue;
            data['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] = dragDropRasterColorScaleValue;
            const addJsonData = JSON.stringify(data);
            let params = 'action=add&data=' + addJsonData;
            //console.log(url+'?'+params);
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function () {
                if (http.readyState === 4 && http.status === 200) {
                    document.getElementById("statusStr").innerHTML = 'Settings saved!';
                    setTimeout(function () {
                        document.getElementById("statusStr").innerHTML = '';
                    }, 5000);
                }
            };
            http.send(params);
        }
    };
    http.send(params);
}

function processSetDefaultSettings() {
    document.getElementById('pointsCluster').checked = true;
    $('#pointsClusterDistance').spinner("value", 50);
    document.getElementById('pointsDisplayHeatMap').checked = false;
    $('#pointsHeatMapRadius').spinner("value", 5);
    $('#pointsHeatMapBlur').spinner("value", 15);
    document.getElementById('pointsBorderColor').value = '000000';
    document.getElementById('pointsFillColor').value = 'E69E67';
    $('#pointsBorderWidth').spinner("value", 1);
    $('#pointsPointRadius').spinner("value", 7);
    document.getElementById('pointsSelectionsBorderColor').value = '10D8E6';
    $('#pointsSelectionsBorderWidth').spinner("value", 2);
    document.getElementById('shapesBorderColor').value = '3399CC';
    document.getElementById('shapesFillColor').value = 'FFFFFF';
    $('#shapesBorderWidth').spinner("value", 2);
    $('#shapesPointRadius').spinner("value", 5);
    $('#shapesOpacity').spinner("value", 0.4);
    document.getElementById('shapesSelectionsBorderColor').value = '0099FF';
    document.getElementById('shapesSelectionsFillColor').value = 'FFFFFF';
    $('#shapesSelectionsBorderWidth').spinner("value", 5);
    $('#shapesSelectionsOpacity').spinner("value", 0.5);
    document.getElementById('dragDropBorderColor').value = '000000';
    document.getElementById('dragDropFillColor').value = 'AAAAAA';
    $('#dragDropBorderWidth').spinner("value", 2);
    $('#dragDropPointRadius').spinner("value", 5);
    $('#dragDropOpacity').spinner("value", 0.3);
    document.getElementById('dragDropRasterColorScale').value = 'earth';
    processSaveSymbologySettings();
}

function saveLayerConfigChanges(){
    const newLayerConfigArr = setNewLayerConfigArr();
    if(newLayerConfigArr.length > 0){
        const newLayerConfig = {};
        newLayerConfig['layerConfig'] = newLayerConfigArr;
        const http = new XMLHttpRequest();
        const url = "rpc/mapServerConfigurationController.php";
        const jsonData = JSON.stringify(newLayerConfig).replaceAll('&','%<amp>%');
        const params = 'action=saveMapServerConfig&data='+jsonData;
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                if(Number(http.responseText) !== 1){
                    document.getElementById("statusStr").innerHTML = 'Error saving changes';
                    setTimeout(function () {
                        document.getElementById("statusStr").innerHTML = '';
                    }, 5000);
                }
                else{
                    document.getElementById("statusStr").innerHTML = 'Configuration saved';
                    setTimeout(function () {
                        document.getElementById("statusStr").innerHTML = '';
                    }, 5000);
                    setLayersList();
                }
            }
        };
        http.send(params);
    }
}

function saveLayerEdits() {
    const layerId = Number(document.getElementById('editLayerId').value);
    const newLayerName = document.getElementById('editLayerName').value;
    if(newLayerName !== ''){
        layerData[layerId]['layerName'] = newLayerName;
        layerData[layerId]['layerDescription'] = document.getElementById('editLayerDescription').value;
        layerData[layerId]['providedBy'] = document.getElementById('editLayerProvidedBy').value;
        layerData[layerId]['sourceURL'] = document.getElementById('editLayerSourceURL').value;
        layerData[layerId]['dateAquired'] = document.getElementById('editLayerDateAquired').value;
        if(layerData[layerId]['fileType'] === 'tif'){
            layerData[layerId]['colorScale'] = document.getElementById('editLayerColorScale').value;
        }
        else{
            layerData[layerId]['borderColor'] = document.getElementById('editLayerBorderColor').value;
            layerData[layerId]['fillColor'] = document.getElementById('editLayerFillColor').value;
            layerData[layerId]['opacity'] = $( '#editLayerOpacity' ).spinner( "value" );
            layerData[layerId]['borderWidth'] = $( '#editLayerBorderWidth' ).spinner( "value" );
            layerData[layerId]['pointRadius'] = $( '#editLayerPointRadius' ).spinner( "value" );
        }
        $('#layereditwindow').popup('hide');
        clearEditWindows();
        saveLayerConfigChanges();
    }
    else{
        alert('Please enter a Layer Name to save edits.');
    }
}

function saveLayerGroupEdits() {
    const groupId = Number(document.getElementById('editLayerGroupId').value);
    const newGroupName = document.getElementById('editLayerGroupName').value;
    if(newGroupName !== ''){
        layerData[groupId]['name'] = newGroupName;
        $('#layergroupeditwindow').popup('hide');
        clearEditWindows();
        saveLayerConfigChanges();
    }
    else{
        alert('Please enter a Group Name to save edits.');
    }
}

function setLayersList() {
    document.getElementById("layerList").innerHTML = '';
    const http = new XMLHttpRequest();
    const url = "../spatial/rpc/getlayersconfig.php";
    //console.log(url);
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function () {
        if (http.readyState == 4 && http.status == 200) {
            if (http.responseText) {
                serverayerArrObject = JSON.parse(http.responseText);
                if (serverayerArrObject.hasOwnProperty('layerConfig')) {
                    layerArr = serverayerArrObject['layerConfig'];
                    for (let i in layerArr) {
                        if(layerArr.hasOwnProperty(i)){
                            const layerId = layerArr[i]['id'];
                            const layerType = layerArr[i]['type'];
                            if(!layerData.hasOwnProperty(layerId)){
                                layerData[layerId] = {};
                            }
                            layerData[layerId]['type'] = layerType;
                            if(layerType === 'layer'){
                                processLayerDataFromLayerArr(layerArr[i],layerId);
                                processAddLayerListElement(layerArr[i],document.getElementById("layerList"));
                            }
                            else if(layerType === 'layerGroup'){
                                layerData[layerId]['name'] = layerArr[i]['name'];
                                processAddLayerListGroup(layerArr[i],document.getElementById("layerList"));
                            }
                        }
                    }
                }
            }
        }
    };
    http.send();
}

function setNewLayerConfigArr(){
    const newLayerConfigArr = [];
    const layerBlocks = document.getElementById('layerList').querySelectorAll('li');
    layerBlocks.forEach((block) => {
        const blockObj = {};
        const blockIdArr = block.id.split("-");
        const type = blockIdArr[0];
        const id = Number(blockIdArr[1]);
        const dataArr = layerData[id];
        if(type === 'layer'){
            newLayerConfigArr.push(buildNewLayerBlockObjFromData(id,dataArr));
        }
        else if(type === 'layerGroup'){
            const newLayerGroupArr = [];
            const layerGroupContainerId = 'layerGroupList-' + id;
            const layerGroupBlocks = document.getElementById(layerGroupContainerId).querySelectorAll('li');
            blockObj['id'] = id;
            blockObj['type'] = type;
            blockObj['name'] = dataArr['name'];
            layerGroupBlocks.forEach((groupBlock) => {
                const blockObj = {};
                const blockIdArr = groupBlock.id.split("-");
                const type = blockIdArr[0];
                const id = Number(blockIdArr[1]);
                const dataArr = layerData[id];
                newLayerGroupArr.push(buildNewLayerBlockObjFromData(id,dataArr));
            });
            blockObj['layers'] = newLayerGroupArr;
            newLayerConfigArr.push(blockObj);
        }
    });
    return newLayerConfigArr;
}

function showAddLayer() {
    document.getElementById('addLayerDiv').style.display = "block";
    document.getElementById('addLayerGroupDiv').style.display = "none";
}

function showAddLayerGroup() {
    document.getElementById('addLayerGroupDiv').style.display = "block";
    document.getElementById('addLayerDiv').style.display = "none";
}

function showLayerGroup(layerId) {
    const groupId = 'layerGroupList-' + layerId;
    const hideButtonId = 'hideLayerGroupButton-' + layerId;
    const showButtonId = 'showLayerGroupButton-' + layerId;
    document.getElementById(groupId).style.display = "block";
    document.getElementById(hideButtonId).style.display = "block";
    document.getElementById(showButtonId).style.display = "none";
}

function uploadLayerFile(){
    showWorking();
    const file = document.getElementById('addLayerFile').files[0];
    const layerName = document.getElementById('addLayerName').value;
    if(file && layerName !== ''){
        const http = new XMLHttpRequest();
        const url = "rpc/mapServerConfigurationController.php";
        const formData = new FormData();
        formData.append('addLayerFile', file);
        formData.append('action', 'uploadMapDataFile');
        http.open("POST", url, true);
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                if(http.responseText && http.responseText !== ''){
                    createLayer(layerName,http.responseText);
                }
            }
            hideWorking();
        };
        http.send(formData);
    }
    else{
        hideWorking();
        alert("You need to upload a data file and enter a Layer Name before adding a layer.");
    }
}

function uploadLayerUpdateFile() {
    showWorking();
    const layerId = Number(document.getElementById('editLayerId').value);
    const file = document.getElementById('layerFileUpdate').files[0];
    const http = new XMLHttpRequest();
    const url = "rpc/mapServerConfigurationController.php";
    const filename = layerData[layerId]['file'].replaceAll('&','%<amp>%');
    const params = 'action=deleteMapDataFile&filename='+filename;
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function() {
        if(http.readyState === 4 && http.status === 200) {
            if(Number(http.responseText) !== 1){
                hideWorking();
                document.getElementById("statusStr").innerHTML = 'Error deleting original data file';
                setTimeout(function () {
                    document.getElementById("statusStr").innerHTML = '';
                }, 5000);
            }
            else{
                const formData = new FormData();
                formData.append('addLayerFile', file);
                formData.append('action', 'uploadMapDataFile');
                http.open("POST", url, true);
                http.onreadystatechange = function() {
                    if(http.readyState === 4 && http.status === 200) {
                        if(http.responseText && http.responseText !== ''){
                            const date = new Date();
                            let fileType = http.responseText.split('.').pop();
                            if(fileType === 'tiff'){
                                fileType = 'tif';
                            }
                            layerData[layerId]['file'] = http.responseText;
                            layerData[layerId]['fileType'] = fileType;
                            layerData[layerId]['dateUploaded'] = date.toISOString().split('T')[0];
                            $('#layereditwindow').popup('hide');
                            clearEditWindows();
                            saveLayerConfigChanges();
                        }
                    }
                    hideWorking();
                };
                http.send(formData);
            }
        }
    };
    http.send(params);
}

function validateFileUpload(ele){
    let input;
    if(ele === 'add'){
        input = document.getElementById('addLayerFile');
    }
    else{
        input = document.getElementById('layerFileUpdate');
    }
    const file = input.files[0];
    const fileType = file.name.split('.').pop().toLowerCase();
    if(fileType !== 'geojson' && fileType !== 'kml' && fileType !== 'zip' && fileType !== 'tif' && fileType !== 'tiff'){
        alert("The file you are trying to upload is a type that is not supported. Only GeoJSON, KML, shapefile, and TIF file formats are supported.");
        input.value = '';
    }
    else if(Number(file.size) > (maxUploadSizeMB * 1000 * 1000)){
        alert("The file you are trying to upload is larger than the maximum upload size of " + maxUploadSizeMB + "MB");
        input.value = '';
    }
}

function validateSourceURL(ele){
    let input;
    if(ele === 'add'){
        input = document.getElementById('addLayerSourceURL');
    }
    else{
        input = document.getElementById('editLayerSourceURL');
    }
    if(!input.value.startsWith("http")){
        alert("Please enter a valid URL for Source URL.");
        input.value = '';
    }
}

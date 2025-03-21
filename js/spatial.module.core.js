let spatialModuleInitialising = false;
const coreLayers = ['base','uncertainty','select','pointv','heat','spider','radius','vector'];
let inputResponseData = {};
let geoPolyArr = [];
let geoCircleArr = [];
let geoBoundingBoxArr = {};
let geoPointArr = [];
let layersObj = {};
let layersArr = [];
let rasterLayersArr = [];
let layerOrderArr = [];
let mouseCoords = [];
let selections = [];
let queryRecCnt = 0;
let draw;
let clustersource;
let loadPointsEvent = false;
let loadPointsError = false;
let rasterLayersLoaded = false;
let toggleSelectedPoints = false;
let lazyLoadCnt = 20000;
let maxFeatureCount;
let currentResolution;
let activeLayer = 'none';
let shapeActive = false;
let pointActive = false;
let spiderCluster;
let spiderFeature;
let hiddenClusters = [];
let clickedFeatures = [];
let selectedPolyError = false;
let dragDrop1 = false;
let dragDrop2 = false;
let dragDrop3 = false;
let dragDrop4 = false;
let dragDrop5 = false;
let dragDrop6 = false;
let dragDropTarget = '';
let returnClusters = false;
let transformStartAngle = 0;
let transformD = [0,0];
let transformFirstPoint = false;
let spinnerSpin = false;
let rasterColorScales = [
    {value: 'autumn', label: 'Autumn'},
    {value: 'blackbody', label: 'Blackbody'},
    {value: 'bluered', label: 'Bluered'},
    {value: 'bone', label: 'Bone'},
    {value: 'cool', label: 'Cool'},
    {value: 'copper', label: 'Copper'},
    {value: 'earth', label: 'Earth'},
    {value: 'electric', label: 'Electric'},
    {value: 'greens', label: 'Greens'},
    {value: 'greys', label: 'Greys'},
    {value: 'hot', label: 'Hot'},
    {value: 'hsv', label: 'Hsv'},
    {value: 'inferno', label: 'Inferno'},
    {value: 'jet', label: 'Jet'},
    {value: 'magma', label: 'Magma'},
    {value: 'picnic', label: 'Picnic'},
    {value: 'plasma', label: 'Plasma'},
    {value: 'portland', label: 'Portland'},
    {value: 'rainbow', label: 'Rainbow'},
    {value: 'rdbu', label: 'Rdbu'},
    {value: 'spring', label: 'Spring'},
    {value: 'summer', label: 'Summer'},
    {value: 'turbo', label: 'Turbo'},
    {value: 'viridis', label: 'Viridis'},
    {value: 'winter', label: 'Winter'},
    {value: 'ylgnbu', label: 'Ylgnbu'},
    {value: 'ylorrd', label: 'Ylorrd'}
];

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

layersObj['base'] = new ol.layer.Tile({
    zIndex: 0
});
layersArr.push(layersObj['base']);

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

function addRasterLayerToTargetList(layerId,title){
    let shapeCount = 0;
    let selectionList = document.getElementById("targetrasterselect").innerHTML;
    const newOption = '<option value="' + layerId + '">' + title + '</option>';
    selectionList += newOption;
    document.getElementById("targetrasterselect").innerHTML = selectionList;
    document.getElementById("targetrasterselect").value = '';
    rasterLayersArr.push(layerId);
    if(rasterLayersArr.length > 0){
        selectInteraction.getFeatures().forEach(function(feature){
            selectedClone = feature.clone();
            const geoType = selectedClone.getGeometry().getType();
            if(geoType === 'Polygon' || geoType === 'MultiPolygon' || geoType === 'Circle'){
                shapeCount++;
            }
        });
        rasterLayersLoaded = true;
        document.getElementById("vectorizeRasterByGridTargetPolyDisplayButton").disabled = false;
        if(shapeCount === 1){
            document.getElementById("dataRasterVectorizeButton").disabled = false;
            document.getElementById("dataRasterVectorizeWarning").style.display = "none";
        }
    }
}

function adjustSelectionsTab(){
    if(selections.length > 0){
        document.getElementById("selectionstab").style.display = "block";
    }
    else{
        document.getElementById("selectionstab").style.display = "none";
        const activeTab = $('#recordstab').tabs("option", "active");
        if(activeTab == 1){
            buildCollKey();
            $('#recordstab').tabs({active:0});
        }
    }
}

function buildLayerControllerLayerDateElement(lArr){
    const layerAquiredDiv = document.createElement('div');
    let innerHtml = '';
    if(lArr.hasOwnProperty('dateAquired') && lArr['dateAquired']){
        innerHtml += '<span style="font-weight:bold;">Date aquired: </span>' + lArr['dateAquired'] + ' ';
    }
    if(lArr.hasOwnProperty('dateUploaded') && lArr['dateUploaded']){
        innerHtml += '<span style="font-weight:bold;">Date uploaded: </span>' + lArr['dateUploaded'];
    }
    layerAquiredDiv.innerHTML = innerHtml;
    return layerAquiredDiv;
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
        layerMainDiv.appendChild(buildLayerControllerLayerProvidedByElement(lArr));
    }
    if(lArr.hasOwnProperty('dateAquired') || lArr.hasOwnProperty('dateUploaded')){
        layerMainDiv.appendChild(buildLayerControllerLayerDateElement(lArr));
    }
    if(lArr['symbology']){
        if(raster){
            layerMainDiv.appendChild(buildLayerControllerLayerRasterSymbologyElement(lArr));
        }
        else{
            layerMainDiv.appendChild(buildLayerControllerLayerVectorSymbologyElement(lArr));
        }
    }
    const layerMainBottomDiv = document.createElement('div');
    layerMainBottomDiv.setAttribute("style","font-size:14px;font-weight:bold;width:100%;display:flex;justify-content:space-between;align-items:flex-end;margin-top:5px;");
    const dataTypeImageDiv = document.createElement('div');
    dataTypeImageDiv.setAttribute("style","width:30px;height:30px;border:1px solid black;margin:0 5px;display:flex;justify-content:center;align-items:center;");
    const dataTypeIcon = document.createElement('i');
    dataTypeIcon.setAttribute("style","height:20px;width:20px;");
    if(lArr['fileType'] === 'tif' || lArr['fileType'] === 'tiff'){
        dataTypeIcon.setAttribute("class","fas fa-border-all");
    }
    else{
        dataTypeIcon.setAttribute("class","fas fa-vector-square");
    }
    dataTypeImageDiv.appendChild(dataTypeIcon);
    layerMainBottomDiv.appendChild(dataTypeImageDiv);
    const layerControlsDiv = document.createElement('div');
    layerControlsDiv.setAttribute("style","display:flex;justify-content:flex-end;align-items:flex-end;");
    if(lArr['sortable']){
        layerControlsDiv.appendChild(buildLayerControllerLayerSortElement(lArr,active));
    }
    if(lArr['symbology']){
        layerControlsDiv.appendChild(buildLayerControllerLayerSymbologyButtonElement(lArr,active));
    }
    if(lArr['query'] && !raster){
        layerControlsDiv.appendChild(buildLayerControllerLayerQueryButtonElement(lArr,active));
    }
    if(lArr['removable']){
        layerControlsDiv.appendChild(buildLayerControllerLayerRemoveButtonElement(lArr));
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
    layerControlsDiv.appendChild(visibilityCheckbox);
    layerMainBottomDiv.appendChild(layerControlsDiv);
    layerMainDiv.appendChild(layerMainBottomDiv);
    layerDiv.appendChild(layerMainDiv);
    return layerDiv;
}

function buildLayerControllerLayerProvidedByElement(lArr){
    const layerProvidedDiv = document.createElement('div');
    let innerHtml = '';
    if(lArr.hasOwnProperty('providedBy') && lArr['providedBy']){
        innerHtml += '<span style="font-weight:bold;">Provided by: </span>' + lArr['providedBy'] + ' ';
    }
    if(lArr.hasOwnProperty('sourceURL') && lArr['sourceURL']){
        innerHtml += '<span style="font-weight:bold;"><a href="' + lArr['sourceURL'] + '" target="_blank">(Go to source)</a></span>';
    }
    layerProvidedDiv.innerHTML = innerHtml;
    return layerProvidedDiv;
}

function buildLayerControllerLayerQueryButtonElement(lArr,active){
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
    return queryButton;
}

function buildLayerControllerLayerRasterSymbologyElement(lArr){
    const layerSymbologyDivId = 'layerSymbology-' + lArr['id'];
    const layerSymbologyDiv = document.createElement('div');
    const layerColorScale = (lArr.hasOwnProperty('colorScale') && lArr['colorScale'] !== '') ? lArr['colorScale'] : SPATIAL_DRAGDROP_RASTER_COLOR_SCALE;
    layerSymbologyDiv.setAttribute("id",layerSymbologyDivId);
    layerSymbologyDiv.setAttribute("style","border:1px solid black;padding:5px;margin-top:5px;display:none;flex-direction:column;width:60%;margin-left:auto;margin-right:auto;");
    const symbologyTopRow = document.createElement('div');
    symbologyTopRow.setAttribute("style","display:flex;justify-content:space-evenly;");
    const symbologyColorScaleDiv = document.createElement('div');
    symbologyColorScaleDiv.setAttribute("style","display:flex;align-items:center;");
    const symbologyColorScaleSpan = document.createElement('span');
    symbologyColorScaleSpan.setAttribute("style","font-weight:bold;margin-right:10px;font-size:12px;");
    symbologyColorScaleSpan.innerHTML = 'Color scale: ';
    symbologyColorScaleDiv.appendChild(symbologyColorScaleSpan);
    const symbologyColorScaleInputId = 'rasterColorScale-' + lArr['id'];
    const symbologyColorScaleOnchangeVal = "changeRasterColorScale('" + lArr['id'] + "',this.value);";
    const symbologyColorScaleInput = document.createElement('select');
    symbologyColorScaleInput.setAttribute("data-role","none");
    symbologyColorScaleInput.setAttribute("id",symbologyColorScaleInputId);
    symbologyColorScaleInput.setAttribute("onchange",symbologyColorScaleOnchangeVal);
    for(let i in rasterColorScales){
        if(rasterColorScales.hasOwnProperty(i)){
            const symbologyColorScaleOption = document.createElement('option');
            symbologyColorScaleOption.setAttribute("value",rasterColorScales[i]['value']);
            symbologyColorScaleOption.innerHTML = rasterColorScales[i]['label'];
            if(layerColorScale === rasterColorScales[i]['value']){
                symbologyColorScaleOption.setAttribute("selected",true);
            }
            symbologyColorScaleInput.appendChild(symbologyColorScaleOption);
        }
    }
    symbologyColorScaleDiv.appendChild(symbologyColorScaleInput);
    symbologyTopRow.appendChild(symbologyColorScaleDiv);
    layerSymbologyDiv.appendChild(symbologyTopRow);
    return layerSymbologyDiv;
}

function buildLayerControllerLayerRemoveButtonElement(lArr){
    const removeButton = document.createElement('button');
    const removeOnclickVal = "removeUserLayer('" + lArr['id'] + "');";
    removeButton.setAttribute("type","button");
    removeButton.setAttribute("style","margin:0 5px;padding:2px;height:25px;width:25px;");
    removeButton.setAttribute("title","Delete Layer");
    removeButton.setAttribute("onclick",removeOnclickVal);
    const removeIcon = document.createElement('i');
    removeIcon.setAttribute("style","height:15px;width:15px;");
    removeIcon.setAttribute("class","far fa-trash-alt");
    removeButton.appendChild(removeIcon);
    return removeButton;
}

function buildLayerControllerLayerSortElement(lArr,active){
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
    return sortingScrollerDiv;
}

function buildLayerControllerLayerSymbologyButtonElement(lArr,active){
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
    return symbologyButton;
}

function buildLayerControllerLayerVectorSymbologyElement(lArr){
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
    symbologyOpacitySpan.innerHTML = 'Fill Opacity: ';
    symbologyOpacityDiv.appendChild(symbologyOpacitySpan);
    const symbologyOpacityInputId = 'opacity-' + lArr['id'];
    const symbologyOpacityInput = document.createElement('input');
    symbologyOpacityInput.setAttribute("id",symbologyOpacityInputId);
    symbologyOpacityInput.setAttribute("style","width:25px;");
    symbologyOpacityInput.setAttribute("value",lArr['opacity']);
    symbologyOpacityDiv.appendChild(symbologyOpacityInput);
    symbologyBottomRow.appendChild(symbologyOpacityDiv);
    layerSymbologyDiv.appendChild(symbologyBottomRow);
    return layerSymbologyDiv;
}

function changeBaseMap(){
    let blsource;
    const selection = document.getElementById('base-map').value;
    const baseLayer = map.getLayers().getArray()[0];
    if(selection === 'googleroadmap'){
        blsource = new ol.source.XYZ({
            url: 'https://mt0.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'googlealteredroadmap'){
        blsource = new ol.source.XYZ({
            url: 'https://mt0.google.com/vt/lyrs=r&hl=en&x={x}&y={y}&z={z}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'googleterrain'){
        blsource = new ol.source.XYZ({
            url: 'https://mt0.google.com/vt/lyrs=p&hl=en&x={x}&y={y}&z={z}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'googlehybrid'){
        blsource = new ol.source.XYZ({
            url: 'https://mt0.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'googlesatellite'){
        blsource = new ol.source.XYZ({
            url: 'https://mt0.google.com/vt/lyrs=s&hl=en&x={x}&y={y}&z={z}',
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
            url: 'https://services.arcgisonline.com/arcgis/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'ocean'){
        blsource = new ol.source.XYZ({
            url: 'https://services.arcgisonline.com/arcgis/rest/services/Ocean_Basemap/MapServer/tile/{z}/{y}/{x}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'ngstopo'){
        blsource = new ol.source.XYZ({
            url: 'https://services.arcgisonline.com/arcgis/rest/services/USA_Topo_Maps/MapServer/tile/{z}/{y}/{x}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'natgeoworld'){
        blsource = new ol.source.XYZ({
            url: 'https://services.arcgisonline.com/arcgis/rest/services/NatGeo_World_Map/MapServer/tile/{z}/{y}/{x}',
            crossOrigin: 'anonymous'
        });
    }
    if(selection === 'esristreet'){
        blsource = new ol.source.XYZ({
            url: 'https://services.arcgisonline.com/arcgis/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
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
        layersObj[layerId].setStyle(style);
    }
}

function changeBorderWidth(layerId,value) {
    if(document.getElementById(('layerVisible-' + layerId)).checked === true){
        const borderColor = document.getElementById(('borderColor-' + layerId)).value;
        const fillColor = document.getElementById(('fillColor-' + layerId)).value;
        const pointRadius = document.getElementById(('pointRadius-' + layerId)).value;
        const opacity = document.getElementById(('opacity-' + layerId)).value;
        const style = getVectorLayerStyle(fillColor, borderColor, value, pointRadius, opacity);
        layersObj[layerId].setStyle(style);
    }
}

function changeClusterDistance(){
    if(clusterDistance !== $('#setclusterdistance').spinner( "value" )){
        clusterDistance = $('#setclusterdistance').spinner( "value" );
        if(clusterPoints && layersObj['pointv'].getSource().getFeatures().length > 0){
            clustersource.setDistance(clusterDistance);
        }
    }
}

function changeClusterSetting(){
    clusterPoints = document.getElementById("clusterswitch").checked;
    if(clusterPoints && layersObj['pointv'].getSource().getFeatures().length > 0){
        loadPointsLayer();
    }
    else{
        layersObj['pointv'].setSource(pointvectorsource);
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
                infoArr['fillColor'] = SPATIAL_SHAPES_FILL_COLOR;
                infoArr['borderColor'] = SPATIAL_SHAPES_BORDER_COLOR;
                infoArr['borderWidth'] = SPATIAL_SHAPES_BORDER_WIDTH;
                infoArr['pointRadius'] = SPATIAL_SHAPES_POINT_RADIUS;
                infoArr['opacity'] = SPATIAL_SHAPES_OPACITY;
                infoArr['removable'] = true;
                infoArr['sortable'] = false;
                infoArr['symbology'] = false;
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
        layersObj[layerId].setStyle(style);
    }
}

function changeHeatMapBlur(){
    if(heatMapBlur !== $('#heatmapblur').spinner( "value" )){
        heatMapBlur = $('#heatmapblur').spinner( "value" );
        layersObj['heat'].setBlur(parseInt(heatMapBlur, 10));
    }
}

function changeHeatMapRadius(){
    if(heatMapRadius !== $('#heatmapradius').spinner( "value" )){
        heatMapRadius = $('#heatmapradius').spinner( "value" );
        layersObj['heat'].setRadius(parseInt(heatMapRadius, 10));
    }
}

function changeLayerOpacity(layerId,value) {
    if(document.getElementById(('layerVisible-' + layerId)).checked === true){
        const borderColor = document.getElementById(('borderColor-' + layerId)).value;
        const fillColor = document.getElementById(('fillColor-' + layerId)).value;
        const borderWidth = document.getElementById(('borderWidth-' + layerId)).value;
        const pointRadius = document.getElementById(('pointRadius-' + layerId)).value;
        const style = getVectorLayerStyle(fillColor, borderColor, borderWidth, pointRadius, value);
        layersObj[layerId].setStyle(style);
    }
}

function changeLayerOrder(layerId, value) {
    const scrollerId = 'layerOrder-' + layerId;
    const currentIndex = layerOrderArr.indexOf(layerId);
    layerOrderArr.splice(currentIndex,1);
    layerOrderArr.splice((value - 1),0,layerId);
    setLayersOrder();
}

function changePointRadius(layerId,value) {
    if(document.getElementById(('layerVisible-' + layerId)).checked === true){
        const borderColor = document.getElementById(('borderColor-' + layerId)).value;
        const fillColor = document.getElementById(('fillColor-' + layerId)).value;
        const borderWidth = document.getElementById(('borderWidth-' + layerId)).value;
        const opacity = document.getElementById(('opacity-' + layerId)).value;
        const style = getVectorLayerStyle(fillColor, borderColor, borderWidth, value, opacity);
        layersObj[layerId].setStyle(style);
    }
}

function changeRasterColorScale(layerId,value){
    map.removeLayer(layersObj[layerId]);
    layersObj[layerId].setSource(null);
    const sourceIndex = layerId + 'Source';
    const dataIndex = layerId + 'Data';
    delete layersObj[sourceIndex];
    const canvasElement = document.createElement('canvas');
    const box = [layersObj[dataIndex]['bbox'][0],layersObj[dataIndex]['bbox'][1] - (layersObj[dataIndex]['bbox'][3] - layersObj[dataIndex]['bbox'][1]), layersObj[dataIndex]['bbox'][2], layersObj[dataIndex]['bbox'][1]];
    const plot = new plotty.plot({
        canvas: canvasElement,
        data: layersObj[dataIndex]['data'],
        width: layersObj[dataIndex]['imageWidth'],
        height: layersObj[dataIndex]['imageHeight'],
        domain: [layersObj[dataIndex]['minValue'], layersObj[dataIndex]['maxValue']],
        colorScale: value
    });
    plot.render();
    layersObj[sourceIndex] = new ol.source.ImageStatic({
        url: canvasElement.toDataURL("image/png"),
        imageExtent: box,
        projection: 'EPSG:4326'
    });
    layersObj[layerId].setSource(layersObj[sourceIndex]);
    map.addLayer(layersObj[layerId]);
    setLayersOrder();
}

function changeRecordPage(page){
    let params;
    document.getElementById("queryrecords").innerHTML = '<div>Loading...<span style="margin-left:15px;">' + getSmallWorkingSpinnerHtml(12) + '</span></div>';
    const selJson = JSON.stringify(selections);
    const http = new XMLHttpRequest();
    const url = "../api/search/changemaprecordpage.php";
    const jsonStarr = encodeURIComponent(JSON.stringify(searchTermsArr));
    if(SOLR_MODE){
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

function checkPointToolSource(selector){
    if(!(selections.length >= 3)){
        document.getElementById(selector).value = 'all';
        alert('There must be at least 3 selected points on the map.');
    }
}

function cleanSelectionsLayer(){
    const selLayerFeatures = layersObj['select'].getSource().getFeatures();
    const currentlySelected = selectInteraction.getFeatures().getArray();
    for(let i in selLayerFeatures){
        if(selLayerFeatures.hasOwnProperty(i) && currentlySelected.indexOf(selLayerFeatures[i]) === -1){
            layersObj['select'].getSource().removeFeature(selLayerFeatures[i]);
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

function clearSelections(resetToggle){
    const selpoints = selections;
    selections = [];
    for(let i in selpoints){
        if(selpoints.hasOwnProperty(i)){
            const checkboxid = 'ch' + selpoints[i];
            let point = '';
            if(clusterPoints){
                const cluster = findRecordCluster(Number(selpoints[i]));
                point = findRecordPointInCluster(cluster,Number(selpoints[i]));
            }
            else{
                point = findRecordPoint(Number(selpoints[i]));
            }
            const style = setSymbol(point);
            point.setStyle(style);
            if(document.getElementById(checkboxid)){
                document.getElementById(checkboxid).checked = false;
            }
        }
    }
    document.getElementById("toggleselectedswitch").checked = false;
    if(resetToggle){
        processToggleSelectedChange();
    }
    adjustSelectionsTab();
    document.getElementById("selectiontbody").innerHTML = '';
}

function closeRecordInfoBox(){
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
    else{
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

function createPolyDifference(){
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
        alert('The selected shapes do not intersect');
    }
}

function createPolysFromPolyArr(polyArr, selected){
    const wktFormat = new ol.format.WKT();
    for(let i in polyArr){
        if(polyArr.hasOwnProperty(i)){
            let wktStr = '';
            if(SOLR_MODE){
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
        layersObj['select'].getSource().removeFeature(feature);
    });
    selectInteraction.getFeatures().clear();
    if(layersObj['select'].getSource().getFeatures().length < 1){
        removeUserLayer('select');
    }
}

function displayVectorizeRasterByGridTargetPolygon(){
    rasteranalysissource.clear();
    rasterAnalysisInteraction.getFeatures().clear();
    let polyOffset = 0;
    const resolutionVal = Number(document.getElementById("vectorizeRasterByGridResolution").value);
    if(resolutionVal === 0.025){
        polyOffset = 10000;
    }
    else if(resolutionVal === 0.05){
        polyOffset = 25000;
    }
    else if(resolutionVal === 0.1){
        polyOffset = 55000;
    }
    else if(resolutionVal === 0.25){
        polyOffset = 145000;
    }
    else if(resolutionVal === 0.5){
        polyOffset = 295000;
    }
    const geoJSONFormat = new ol.format.GeoJSON();
    const mapCenterPoint = map.getView().getCenter();
    const highLong = mapCenterPoint[0] + polyOffset;
    const lowLong = mapCenterPoint[0] - polyOffset;
    const highLat = mapCenterPoint[1] + polyOffset;
    const lowLat = mapCenterPoint[1] - polyOffset;
    const line = turf.lineString([[lowLong, lowLat], [lowLong, highLat], [highLong, highLat]]);
    const bbox = turf.bbox(line);
    const bboxPolygon = turf.bboxPolygon(bbox);
    const newPoly = geoJSONFormat.readFeature(bboxPolygon);
    rasteranalysissource.addFeature(newPoly);
    rasterAnalysisInteraction.getFeatures().push(newPoly);
    document.getElementById("vectorizeRasterByGridTargetPolyDisplayButton").style.display = "none";
    document.getElementById("vectorizeRasterByGridTargetPolyHideButton").style.display = "block";
    document.getElementById("gridRasterVectorizeButton").disabled = false;
    document.getElementById("gridRasterVectorizeWarning").style.display = "none";
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
    const features = layersObj['select'].getSource().getFeatures();
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

function findRecordCluster(id){
    const clusters = layersObj['pointv'].getSource().getFeatures();
    for(let c in clusters){
        if(clusters.hasOwnProperty(c)){
            const clusterindex = clusters[c].get('identifiers');
            if(clusterindex.indexOf(Number(id)) !== -1){
                return clusters[c];
            }
        }
    }
}

function findRecordClusterPosition(id){
    if(spiderCluster){
        const spiderPoints = layersObj['spider'].getSource().getFeatures();
        for(let p in spiderPoints){
            if(spiderPoints.hasOwnProperty(p) && Number(spiderPoints[p].get('features')[0].get('id')) === id){
                return spiderPoints[p].getGeometry().getCoordinates();
            }
        }
    }
    else if(clusterPoints){
        const clusters = layersObj['pointv'].getSource().getFeatures();
        for(let c in clusters){
            if(clusters.hasOwnProperty(c)){
                const clusterindex = clusters[c].get('identifiers');
                if(clusterindex.indexOf(id) !== -1){
                    return clusters[c].getGeometry().getCoordinates();
                }
            }
        }
    }
    else{
        const features = layersObj['pointv'].getSource().getFeatures();
        for(let f in features){
            if(features.hasOwnProperty(f) && Number(features[f].get('id')) === id){
                return features[f].getGeometry().getCoordinates();
            }
        }
    }
}

function findRecordPoint(id){
    const features = layersObj['pointv'].getSource().getFeatures();
    for(let f in features){
        if(features.hasOwnProperty(f) && Number(features[f].get('id')) === id){
            return features[f];
        }
    }
}

function findRecordPointInCluster(cluster,id){
    const cFeatures = cluster.get('features');
    for (let f in cFeatures) {
        if(cFeatures.hasOwnProperty(f) && Number(cFeatures[f].get('id')) === id){
            return cFeatures[f];
        }
    }
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
                if(SOLR_MODE) {
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
        const jsonPolyArr = JSON.stringify(geoPolyArr);
        if(jsonPolyArr.length < 5000000){
            setSearchTermsArrKeyValue('polyArr',jsonPolyArr);
            selectedPolyError = false;
        }
        else{
            selectedPolyError = true;
        }
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

function getSearchRecCnt(callback){
    let params;
    let url;
    let http;
    queryRecCnt = 0;
    const jsonStarr = encodeURIComponent(JSON.stringify(searchTermsArr));
    if(SOLR_MODE){
        let qStr = '';
        http = new XMLHttpRequest();
        url = "../api/search/SOLRConnector.php";
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
        url = "../api/search/MYSQLConnector.php";
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

function getRasterXYFromDataIndex(index,rasterWidth){
    let xyArr = [];
    if(index < rasterWidth){
        xyArr.push(index);
        xyArr.push(0);
    }
    else{
        let y = Math.trunc(index / rasterWidth);
        let x = ((index - (y * rasterWidth)) - 1);
        xyArr.push(x);
        xyArr.push(y);
    }
    return xyArr;
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
        const clusters = layersObj['pointv'].getSource().getFeatures();
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
        const features = layersObj['pointv'].getSource().getFeatures();
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
                const cluster = findRecordCluster(selections[i]);
                point = findRecordPointInCluster(cluster,selections[i]);
            }
            else{
                point = findRecordPoint(selections[i]);
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
                color: getRgbaStrFromHexOpacity((fillColor),opacity)
            }),
            stroke: new ol.style.Stroke({
                color: (borderColor),
                width: borderWidth
            }),
            image: new ol.style.Circle({
                radius: pointRadius,
                fill: new ol.style.Fill({
                    color: getRgbaStrFromHexOpacity((fillColor),opacity)
                }),
                stroke: new ol.style.Stroke({
                    color: (borderColor),
                    width: borderWidth
                })
            })
        })
    }
    else{
        return new ol.style.Style({
            fill: new ol.style.Fill({
                color: getRgbaStrFromHexOpacity((fillColor),opacity)
            }),
            image: new ol.style.Circle({
                radius: pointRadius,
                fill: new ol.style.Fill({
                    color: getRgbaStrFromHexOpacity((fillColor),opacity)
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

function hideVectorizeRasterByGridTargetPolygon(){
    rasteranalysissource.clear();
    rasterAnalysisInteraction.getFeatures().clear();
    document.getElementById("vectorizeRasterByGridTargetPolyDisplayButton").style.display = "block";
    document.getElementById("vectorizeRasterByGridTargetPolyHideButton").style.display = "none";
    document.getElementById("gridRasterVectorizeButton").disabled = true;
    document.getElementById("gridRasterVectorizeWarning").style.display = "block";
}

function lazyLoadPoints(index,finalIndex,callback){
    showWorking();
    let params;
    let url;
    let startindex = 0;
    if(index > 0) {
        startindex = index * lazyLoadCnt;
    }
    const http = new XMLHttpRequest();
    const jsonStarr = encodeURIComponent(JSON.stringify(searchTermsArr));
    if(SOLR_MODE){
        url = "../api/search/SOLRConnector.php";
        params = 'starr=' + jsonStarr + '&rows='+lazyLoadCnt+'&start='+startindex+'&fl='+SOLRFields+'&wt=geojson';
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(loadPointsEvent && http.readyState === 4) {
                if(http.status === 200) {
                    callback(http.responseText,index,finalIndex);
                }
                else{
                    loadPointsError = true;
                    alert('An error occurred while loading records');
                    loadPointsPostrender();
                }
            }
        };
        http.send(params);
    }
    else{
        url = "../api/search/MYSQLConnector.php";
        params = 'starr=' + jsonStarr + '&rows=' + lazyLoadCnt + '&start=' + startindex + '&type=geoquery';
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(loadPointsEvent && http.readyState === 4) {
                if(http.status === 200) {
                    callback(http.responseText,index,finalIndex);
                }
                else{
                    loadPointsError = true;
                    alert('An error occurred while loading records');
                    loadPointsPostrender();
                }
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

function loadPointsLayer(){
    loadPointsEvent = true;
    loadPointsError = false;
    pointvectorsource.clear(true);
    let processed = 0;
    let index = 0;
    const finalIndex = queryRecCnt > lazyLoadCnt ? Math.ceil(queryRecCnt / lazyLoadCnt) : 0;
    do{
        lazyLoadPoints(index, finalIndex, function(res,index,finalIndex){
            const format = new ol.format.GeoJSON();
            let features = format.readFeatures(res, {
                featureProjection: 'EPSG:3857'
            });
            if(toggleSelectedPoints){
                features = features.filter(function (feature){
                    const id = Number(feature.get('id'));
                    return (selections.indexOf(id) !== -1);
                });
            }
            primeSymbologyData(features);
            pointvectorsource.addFeatures(features);
            if(index === finalIndex){
                const pointextent = pointvectorsource.getExtent();
                map.getView().fit(pointextent,map.getSize());
            }
        });
        processed = processed + lazyLoadCnt;
        index++;
    }
    while(processed < queryRecCnt && !loadPointsError);

    clustersource = new ol.source.PropertyCluster({
        distance: clusterDistance,
        source: pointvectorsource,
        clusterkey: clusterKey,
        indexkey: 'id',
        geometryFunction: function(feature){
            return feature.getGeometry();
        }
    });

    layersObj['pointv'].setStyle(getPointStyle);
    if(clusterPoints){
        layersObj['pointv'].setSource(clustersource);
    }
    else{
        layersObj['pointv'].setSource(pointvectorsource);
    }
    layersObj['heat'].setSource(pointvectorsource);
    if(showHeatMap){
        layersObj['heat'].setVisible(true);
    }
}

function loadServerLayer(id,name,file){
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
        layersObj[id] = new ol.layer.Vector({
            source: new ol.source.Vector({
                wrapX: true
            }),
            zIndex: zIndex,
            style: getVectorLayerStyle(fillColor, borderColor, borderWidth, pointRadius, opacity)
        });
    }
    else{
        layersObj[id] = new ol.layer.Image({
            zIndex: zIndex,
        });
    }
    if(fileType === 'geojson'){
        layersObj[id].setSource(new ol.source.Vector({
            url: ('../content/spatial/' + file),
            format: new ol.format.GeoJSON(),
            wrapX: true
        }));
        layersObj[id].getSource().on('addfeature', function(evt) {
            map.getView().fit(layersObj[id].getSource().getExtent());
        });
        layersObj[id].on('postrender', function(evt) {
            hideWorking();
        });
    }
    else if(fileType === 'kml'){
        layersObj[id].setSource(new ol.source.Vector({
            url: ('../content/spatial/' + file),
            format: new ol.format.KML({
                extractStyles: false,
            }),
            wrapX: true
        }));
        layersObj[id].getSource().on('addfeature', function(evt) {
            map.getView().fit(layersObj[id].getSource().getExtent());
        });
        layersObj[id].on('postrender', function(evt) {
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
                        layersObj[id].setSource(new ol.source.Vector({
                            features: features,
                            wrapX: true
                        }));
                        map.getView().fit(layersObj[id].getSource().getExtent());
                        layersObj[id].on('postrender', function(evt) {
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
                    const dataIndex = id + 'Data';
                    const rawBox = image.getBoundingBox();
                    const box = [rawBox[0],rawBox[1] - (rawBox[3] - rawBox[1]), rawBox[2], rawBox[1]];
                    const bands = image.readRasters();
                    const meta = image.getFileDirectory();
                    const x_min = meta.ModelTiepoint[3];
                    const x_max = x_min + meta.ModelPixelScale[0] * meta.ImageWidth;
                    const y_min = meta.ModelTiepoint[4];
                    const y_max = y_min - meta.ModelPixelScale[1] * meta.ImageLength;
                    const imageWidth = image.getWidth();
                    const imageHeight = image.getHeight();
                    const colorScale = document.getElementById(('rasterColorScale-' + id)).value;
                    let minValue = 0;
                    let maxValue = 0;
                    bands[0].forEach(function(item, index) {
                        if(item < minValue && ((minValue - item) < 5000)){
                            minValue = item;
                        }
                        if(item > maxValue){
                            maxValue = item;
                        }
                    });
                    layersObj[dataIndex] = {};
                    layersObj[dataIndex]['data'] = bands[0];
                    layersObj[dataIndex]['bbox'] = rawBox;
                    layersObj[dataIndex]['resolution'] = (Number(meta.ModelPixelScale[0]) * 100) * 1.6;
                    layersObj[dataIndex]['x_min'] = x_min;
                    layersObj[dataIndex]['x_max'] = x_max;
                    layersObj[dataIndex]['y_min'] = y_min;
                    layersObj[dataIndex]['y_max'] = y_max;
                    layersObj[dataIndex]['imageWidth'] = imageWidth;
                    layersObj[dataIndex]['imageHeight'] = imageHeight;
                    layersObj[dataIndex]['minValue'] = minValue;
                    layersObj[dataIndex]['maxValue'] = maxValue;
                    const canvasElement = document.createElement('canvas');
                    const plot = new plotty.plot({
                        canvas: canvasElement,
                        data: bands[0],
                        width: imageWidth,
                        height: imageHeight,
                        domain: [minValue, maxValue],
                        colorScale: colorScale
                    });
                    plot.render();
                    layersObj[id].setSource(new ol.source.ImageStatic({
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
                    addRasterLayerToTargetList(id,name);
                    hideWorking();
                });
            });
        });
    }
    map.addLayer(layersObj[id]);
    toggleLayerDisplayMessage();
}

function openRecordInfoBox(id,label){
    closeRecordInfoBox();
    finderpopuptimeout = null;
    const idpos = findRecordClusterPosition(id);
    finderpopupcontent.innerHTML = label;
    finderpopupoverlay.setPosition(idpos);
    map.getView().setCenter(idpos);
    finderpopuptimeout = setTimeout(function() {
        closeRecordInfoBox();
    }, 2000 );
}

function primeLayerQuerySelectorFields(layerId) {
    const fieldArr = [];
    const fieldSelector = document.getElementById('spatialQueryFieldSelector');
    const layerFeatures = layersObj[layerId].getSource().getFeatures();
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
        blankSelectorOption.innerHTML = 'Select attribute';
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

function processAddLayerControllerElement(lArr,parentElement,active){
    const layerDivId = 'layer-' + lArr['id'];
    const raster = (lArr['fileType'] === 'tif' || lArr['fileType'] === 'tiff');
    if(!document.getElementById(layerDivId)){
        const layerDiv = buildLayerControllerLayerElement(lArr,active);
        if(lArr['id'] === 'pointv'){
            parentElement.insertBefore(layerDiv, parentElement.firstChild);
        }
        else{
            parentElement.appendChild(layerDiv);
        }
        if(lArr['symbology'] && !raster){
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
                    spinnerSpin = true;
                },
                change: function( event ) {
                    if(!spinnerSpin){
                        changeLayerOpacity(lArr['id'], $( symbologyOpacityId ).spinner( "value" ));
                    }
                    spinnerSpin = false;
                }
            });
            $( symbologyBorderWidthId ).spinner({
                step: 1,
                min: 0,
                numberFormat: "n",
                spin: function( event, ui ) {
                    changeBorderWidth(lArr['id'], ui.value);
                    spinnerSpin = true;
                },
                change: function( event ) {
                    if(!spinnerSpin){
                        changeBorderWidth(lArr['id'], $( symbologyBorderWidthId ).spinner( "value" ));
                    }
                    spinnerSpin = false;
                }
            });
            $( symbologyPointRadiusId ).spinner({
                step: 1,
                min: 0,
                numberFormat: "n",
                spin: function( event, ui ) {
                    changePointRadius(lArr['id'], ui.value);
                    spinnerSpin = true;
                },
                change: function( event ) {
                    if(!spinnerSpin){
                        changePointRadius(lArr['id'], $( symbologyPointRadiusId ).spinner( "value" ));
                    }
                    spinnerSpin = false;
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
    else if(active){
        document.getElementById("selectlayerselect").value = lArr['id'];
        setActiveLayer();
    }
    toggleLayerDisplayMessage();
}

function processAddLayerControllerGroup(lArr,parentElement){
    const layerGroupdDivId = 'layerGroup-' + lArr['id'] + '-accordion';
    if(!document.getElementById(layerGroupdDivId)){
        const layersObj = lArr['layers'];
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
        for(let i in layersObj){
            if(layersObj.hasOwnProperty(i)){
                layersObj[i]['removable'] = false;
                layersObj[i]['sortable'] = true;
                layersObj[i]['symbology'] = true;
                layersObj[i]['query'] = true;
                processAddLayerControllerElement(layersObj[i],layerGroupContainerDiv,false)
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
            if($('.reccheck:checked').length === $('.reccheck').length){
                document.getElementById("selectallcheck").checked = true;
            }
        }
        selections.push(Number(c.value));
        updateSelections(Number(c.value),false);
    }
    else if(c.checked === false){
        activeTab = $('#recordstab').tabs("option", "active");
        if(activeTab === 1){
            document.getElementById("selectallcheck").checked = false;
        }
        const index = selections.indexOf(Number(c.value));
        selections.splice(index, 1);
        removeSelectionRecord(Number(c.value));
    }
    let point = '';
    if(clusterPoints){
        const cluster = findRecordCluster(Number(c.value));
        point = findRecordPointInCluster(cluster,Number(c.value));
    }
    else{
        point = findRecordPoint(Number(c.value));
    }
    const style = setSymbol(point);
    point.setStyle(style);
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
            const featureProps = feature.getProperties();
            const selectedClone = feature.clone();
            const geoType = selectedClone.getGeometry().getType();
            const wktFormat = new ol.format.WKT();
            const geoJSONFormat = new ol.format.GeoJSON();
            if(geoType === 'MultiPolygon' || geoType === 'Polygon') {
                const boxType = (featureProps.hasOwnProperty('geoType') && featureProps['geoType'] === 'Box');
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
                    if(SOLR_MODE || INPUTTOOLSARR.includes('wkt')) {
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
    const id = Number(feature.get('id'));
    const checkboxid = 'ch' + id;
    if(selections.indexOf(id) < 0){
        selections.push(id);
        const infoArr = getPointInfoArr(sFeature);
        updateSelections(id,infoArr);
        if(document.getElementById(checkboxid)){
            document.getElementById(checkboxid).checked = true;
        }
    }
    else{
        const index = selections.indexOf(id);
        selections.splice(index, 1);
        removeSelectionRecord(id);
        if(document.getElementById(checkboxid)){
            document.getElementById(checkboxid).checked = false;
        }
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
        loadPointsLayer();
    }
    else{
        layersObj['pointv'].setSource(pointvectorsource);
    }
}

function processVectorInteraction(){
    if(!spatialModuleInitialising){
        let featureCount = 0;
        let polyCount = 0;
        selectInteraction.getFeatures().forEach(function(feature){
            selectedClone = feature.clone();
            const geoType = selectedClone.getGeometry().getType();
            if(geoType === 'Polygon' || geoType === 'MultiPolygon' || geoType === 'Circle'){
                polyCount++;
            }
            featureCount++;
        });
        if(document.getElementById("dataRasterVectorizeButton")){
            if(polyCount === 1 && rasterLayersLoaded){
                document.getElementById("dataRasterVectorizeButton").disabled = false;
                document.getElementById("dataRasterVectorizeWarning").style.display = "none";
            }
            else{
                document.getElementById("dataRasterVectorizeButton").disabled = true;
                document.getElementById("dataRasterVectorizeWarning").style.display = "block";
            }
        }
        if(featureCount >= 1){
            document.getElementById("bufferPolyButton").disabled = false;
            document.getElementById("bufferPolyWarning").style.display = "none";
        }
        else{
            document.getElementById("bufferPolyButton").disabled = true;
            document.getElementById("bufferPolyWarning").style.display = "block";
        }
        if(polyCount === 2){
            document.getElementById("differencePolyButton").disabled = false;
            document.getElementById("intersectPolyButton").disabled = false;
            document.getElementById("differencePolyWarning").style.display = "none";
            document.getElementById("intersectPolyWarning").style.display = "none";
        }
        else{
            document.getElementById("differencePolyButton").disabled = true;
            document.getElementById("intersectPolyButton").disabled = true;
            document.getElementById("differencePolyWarning").style.display = "block";
            document.getElementById("intersectPolyWarning").style.display = "block";
        }
        if(polyCount >= 2){
            document.getElementById("unionPolyButton").disabled = false;
            document.getElementById("unionPolyWarning").style.display = "none";
        }
        else{
            document.getElementById("unionPolyButton").disabled = true;
            document.getElementById("unionPolyWarning").style.display = "block";
        }
        if(!INPUTWINDOWMODE){
            setSpatialParamBox();
            getGeographyParams();
        }
        else{
            processInputSelections();
        }
    }
}

function processVectorizeRasterByGridResolutionChange(){
    if(document.getElementById("vectorizeRasterByGridTargetPolyDisplayButton").style.display === "none"){
        displayVectorizeRasterByGridTargetPolygon();
    }
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

function removeLayerFromSelList(layer){
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
        rasterLayersLoaded = false;
        hideVectorizeRasterByGridTargetPolygon();
        document.getElementById("dataRasterVectorizeButton").disabled = true;
        document.getElementById("gridRasterVectorizeButton").disabled = true;
        document.getElementById("vectorizeRasterByGridTargetPolyDisplayButton").disabled = true;
        document.getElementById("dataRasterVectorizeWarning").style.display = "block";
        document.getElementById("gridRasterVectorizeWarning").style.display = "block";
    }
}

function removeSelection(c){
    const id = c.value;
    const chbox = 'ch' + id;
    removeSelectionRecord(id);
    if(document.getElementById(chbox)){
        document.getElementById(chbox).checked = false;
    }
    const index = selections.indexOf(Number(id));
    selections.splice(index, 1);
    if(spiderCluster){
        const spiderFeatures = layersObj['spider'].getSource().getFeatures();
        for(let f in spiderFeatures){
            if(spiderFeatures.hasOwnProperty(f) && spiderFeatures[f].get('features')[0].get('id') === Number(c.value)){
                const style = (spiderFeatures[f].get('features') ? setClusterSymbol(spiderFeatures[f]) : setSymbol(spiderFeatures[f]));
                spiderFeatures[f].setStyle(style);
            }
        }
    }
    let point = '';
    if(clusterPoints){
        const cluster = findRecordCluster(Number(id));
        point = findRecordPointInCluster(cluster,Number(id));
    }
    else{
        point = findRecordPoint(Number(id));
    }
    const style = setSymbol(point);
    point.setStyle(style);
    adjustSelectionsTab();
}

function removeSelectionRecord(sel){
    const selDivId = "sel" + sel;
    if(document.getElementById(selDivId)){
        const selDiv = document.getElementById(selDivId);
        selDiv.parentNode.removeChild(selDiv);
    }
}

function removeServerLayer(id){
    map.removeLayer(layersObj[id]);
    const dataIndex = id + 'Data';
    if(layersObj.hasOwnProperty(dataIndex)){
        removeRasterLayerFromTargetList(id);
        delete layersObj[dataIndex];
    }
    delete layersObj[id];
}

function removeUserLayer(layerID,raster){
    const layerDivId = "layer-" + layerID;
    if(document.getElementById(layerDivId)){
        const layerDiv = document.getElementById(layerDivId);
        layerDiv.parentNode.removeChild(layerDiv);
    }
    if(layerID === 'select'){
        selectInteraction.getFeatures().clear();
        layersObj[layerID].getSource().clear(true);
        shapeActive = false;
    }
    else if(layerID === 'pointv'){
        clearSelections(false);
        adjustSelectionsTab();
        pointvectorsource.clear(true);
        layersObj['heat'].setVisible(false);
        clustersource = '';
        $('#criteriatab').tabs({active: 0});
        $("#sidepanel-accordion").accordion("option","active",0);
        pointActive = false;
    }
    else{
        if(layerID === 'dragDrop1' || layerID === 'dragDrop2' || layerID === 'dragDrop3'){
            layersObj[layerID].setSource(blankdragdropsource);
            const sourceIndex = dragDropTarget + 'Source';
            delete layersObj[sourceIndex];
            if(layerID === 'dragDrop1') {
                dragDrop1 = false;
            }
            else if(layerID === 'dragDrop2') {
                dragDrop2 = false;
            }
            else if(layerID === 'dragDrop3') {
                dragDrop3 = false;
            }
        }
        else if(layerID === 'dragDrop4' || layerID === 'dragDrop5' || layerID === 'dragDrop6') {
            map.removeLayer(layersObj[layerID]);
            layersObj[layerID].setSource(null);
            const sourceIndex = dragDropTarget + 'Source';
            const dataIndex = dragDropTarget + 'Data';
            delete layersObj[sourceIndex];
            delete layersObj[dataIndex];
            if(layerID === 'dragDrop4') {
                dragDrop4 = false;
            }
            else if(layerID === 'dragDrop5') {
                dragDrop5 = false;
            }
            else if(layerID === 'dragDrop6') {
                dragDrop6 = false;
            }
            removeRasterLayerFromTargetList(layerID);
        }
    }
    document.getElementById("selectlayerselect").value = 'none';
    removeLayerFromSelList(layerID);
    setActiveLayer();
    toggleLayerDisplayMessage();
}

function runQuerySelectorQuery(layerId,fieldValue,operatorValue,singleVal,doubleVal1,doubleVal2) {
    const addFeatures = [];
    const layerFeatures = layersObj[layerId].getSource().getFeatures();
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
        dragDropTarget = 'dragDrop1';
        return true;
    }
    else if(!dragDrop2){
        dragDrop2 = true;
        dragDropTarget = 'dragDrop2';
        return true;
    }
    else if(!dragDrop3){
        dragDrop3 = true;
        dragDropTarget = 'dragDrop3';
        return true;
    }
    else{
        alert('You may only have 3 uploaded vector layers at a time. Please remove one of the currently uploaded layers to upload more.');
        return false;
    }
}

function setLayersOrder() {
    const layersObjKeys = Object.keys(layersObj);
    const layersObjLength = layersObjKeys.length;
    for(let i in layerOrderArr){
        if(layerOrderArr.hasOwnProperty(i)){
            const index = Number(layerOrderArr.indexOf(layerOrderArr[i])) + 1;
            layersObj[layerOrderArr[i]].setZIndex(index);
            const sortingScrollerId = 'layerOrder-' + layerOrderArr[i];
            $( ('#' + sortingScrollerId) ).spinner( "option", "max", layerOrderArr.length );
            $( ('#' + sortingScrollerId) ).spinner( "value", index );
        }
    }
    layersObj['base'].setZIndex(0);
    if(layersObj.hasOwnProperty('uncertainty')){
        layersObj['uncertainty'].setZIndex((layersObjLength - 4));
    }
    if(layersObj.hasOwnProperty('select')){
        layersObj['select'].setZIndex((layersObjLength - 3));
    }
    if(layersObj.hasOwnProperty('pointv')){
        layersObj['pointv'].setZIndex((layersObjLength - 2));
    }
    if(layersObj.hasOwnProperty('heat')){
        layersObj['heat'].setZIndex((layersObjLength - 1));
    }
    if(layersObj.hasOwnProperty('spider')){
        layersObj['spider'].setZIndex(layersObjLength);
    }
    if(layersObj.hasOwnProperty('radius')){
        layersObj['radius'].setZIndex((layersObjLength - 1));
    }
    if(layersObj.hasOwnProperty('vector')){
        layersObj['vector'].setZIndex(layersObjLength);
    }
}

function setRasterDragDropTarget(){
    dragDropTarget = '';
    if(!dragDrop4){
        dragDrop4 = true;
        dragDropTarget = 'dragDrop4';
        return true;
    }
    else if(!dragDrop5){
        dragDrop5 = true;
        dragDropTarget = 'dragDrop5';
        return true;
    }
    else if(!dragDrop6){
        dragDrop6 = true;
        dragDropTarget = 'dragDrop6';
        return true;
    }
    else{
        alert('You may only have 3 uploaded raster layers at a time. Please remove one of the currently uploaded layers to upload more.');
        return false;
    }
}

function setLayersController(){
    const http = new XMLHttpRequest();
    const url = "../api/spatial/getlayersconfig.php";
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
        document.getElementById("concavePolyButton").disabled = false;
        document.getElementById("convexPolyButton").disabled = false;
        document.getElementById("concavePolyNoPoints").style.display = "none";
        document.getElementById("convexPolyNoPoints").style.display = "none";
    }
    else{
        document.getElementById("recordsHeader").style.display = "none";
        document.getElementById("recordstab").style.display = "none";
        document.getElementById("concavePolyButton").disabled = true;
        document.getElementById("convexPolyButton").disabled = true;
        document.getElementById("concavePolyNoPoints").style.display = "block";
        document.getElementById("convexPolyNoPoints").style.display = "block";
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

    const source = layersObj['spider'].getSource();
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

function toggleHeatMap(){
    showHeatMap = document.getElementById("heatmapswitch").checked;
    if(showHeatMap){
        layersObj['pointv'].setVisible(false);
        layersObj['heat'].setVisible(true);
    }
    else{
        if(returnClusters){
            returnClusters = false;
            document.getElementById("clusterswitch").checked = true;
            changeClusterSetting();
        }
        layersObj['heat'].setVisible(false);
        layersObj['pointv'].setVisible(true);
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
        loadServerLayer(id,name,file);
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
        removeLayerFromSelList(id);
        removeLayerFromLayerOrderArr(id);
    }
}

function toggleUserLayerVisibility(id,name,visible){
    let layerId = id;
    const sortingScrollerDivId = 'layerOrderDiv-' + id;
    const symbologyButtonId = 'layerSymbologyButton-' + id;
    const queryButtonId = 'layerQueryButton-' + id;
    if(id === 'pointv' && showHeatMap) {
        layerId = 'heat';
    }
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
        layersObj[layerId].setVisible(true);
        addLayerToSelList(id,name,false);
        if(!coreLayers.includes(id)){
            addLayerToLayerOrderArr(id);
        }
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
        layersObj[layerId].setVisible(false);
        removeLayerFromSelList(id);
        if(!coreLayers.includes(id)){
            removeLayerFromLayerOrderArr(id);
        }
    }
}

function vectorizeRasterByData(){
    let selectedClone = null;
    const turfFeatureArr = [];
    const targetRaster = document.getElementById("targetrasterselect").value;
    const valLow = document.getElementById("vectorizeRasterByDataValueLow").value;
    const valHigh = document.getElementById("vectorizeRasterByDataValueHigh").value;
    if(targetRaster === ''){
        alert("Please select a target raster layer.");
    }
    else if(valLow === '' || isNaN(valLow) || valHigh === '' || isNaN(valHigh)){
        alert("Please enter high and low numbers for the value range.");
    }
    else{
        showWorking();
        setTimeout(function() {
            selectInteraction.getFeatures().forEach(function(feature){
                selectedClone = feature.clone();
                const geoType = selectedClone.getGeometry().getType();
                if(geoType === 'Polygon' || geoType === 'MultiPolygon' || geoType === 'Circle'){
                    const geoJSONFormat = new ol.format.GeoJSON();
                    const selectiongeometry = selectedClone.getGeometry();
                    selectiongeometry.transform(mapProjection, wgs84Projection);
                    const geojsonStr = geoJSONFormat.writeGeometry(selectiongeometry);
                    const featCoords = JSON.parse(geojsonStr).coordinates;
                    const turfPoly = turf.polygon(featCoords);
                    const dataIndex = targetRaster + 'Data';
                    const dataObj = layersObj[dataIndex];
                    const box = [dataObj['bbox'][0],dataObj['bbox'][1] - (dataObj['bbox'][3] - dataObj['bbox'][1]), dataObj['bbox'][2], dataObj['bbox'][1]];
                    dataObj['data'].forEach(function(item, index) {
                        if(Number(item) >= Number(valLow) && Number(item) <= Number(valHigh)){
                            const xyArr = getRasterXYFromDataIndex(index,dataObj['imageWidth']);
                            const lat = box[3] - (((box[3] - box[1]) / dataObj['imageHeight']) * xyArr[1]);
                            const long = box[0] + (((box[2] - box[0]) / dataObj['imageWidth']) * xyArr[0]);
                            const turfPoint = turf.point([long,lat]);
                            if(turf.booleanPointInPolygon(turfPoint, turfPoly)){
                                turfFeatureArr.push(turfPoint);
                            }
                        }
                    });
                    const turfFeatureCollection = turf.featureCollection(turfFeatureArr);
                    let concavepoly = '';
                    try{
                        const options = {units: 'kilometers', maxEdge: dataObj['resolution']};
                        concavepoly = turf.concave(turfFeatureCollection,options);
                    }
                    catch(e){}
                    if(concavepoly){
                        const cnvepoly = geoJSONFormat.readFeature(concavepoly);
                        cnvepoly.getGeometry().transform(wgs84Projection,mapProjection);
                        selectsource.addFeature(cnvepoly);
                    }
                    hideWorking();
                }
            });
        }, 50);
    }
}

function vectorizeRasterByGrid(){
    let selectedClone;
    const turfFeatureArr = [];
    const targetRaster = document.getElementById("targetrasterselect").value;
    const valLow = document.getElementById("vectorizeRasterByGridValueLow").value;
    const valHigh = document.getElementById("vectorizeRasterByGridValueHigh").value;
    const resolutionVal = Number(document.getElementById("vectorizeRasterByGridResolution").value);
    if(targetRaster === ''){
        alert("Please select a target raster layer.");
    }
    else if(valLow === '' || isNaN(valLow) || valHigh === '' || isNaN(valHigh)){
        alert("Please enter high and low numbers for the value range.");
    }
    else{
        showWorking();
        setTimeout(function() {
            rasterAnalysisInteraction.getFeatures().forEach((feature) => {
                selectedClone = feature.clone();
            });
            if(selectedClone){
                const geoJSONFormat = new ol.format.GeoJSON();
                const selectiongeometry = selectedClone.getGeometry();
                selectiongeometry.transform(mapProjection, wgs84Projection);
                const geojsonStr = geoJSONFormat.writeGeometry(selectiongeometry);
                const featCoords = JSON.parse(geojsonStr).coordinates;
                const extentBBox = turf.bbox(turf.polygon(featCoords));
                const gridPoints = turf.pointGrid(extentBBox, resolutionVal, {units: 'kilometers',mask: turf.polygon(featCoords)});
                const gridPointFeatures = geoJSONFormat.readFeatures(gridPoints);
                const dataIndex = targetRaster + 'Data';
                gridPointFeatures.forEach(function(feature){
                    const coords = feature.getGeometry().getCoordinates();
                    const x = Math.floor(layersObj[dataIndex]['imageWidth']*(coords[0] - layersObj[dataIndex]['x_min'])/(layersObj[dataIndex]['x_max'] - layersObj[dataIndex]['x_min']));
                    const y = layersObj[dataIndex]['imageHeight']-Math.ceil(layersObj[dataIndex]['imageHeight']*(coords[1] - layersObj[dataIndex]['y_max'])/(layersObj[dataIndex]['y_min'] - layersObj[dataIndex]['y_max']));
                    const rasterDataIndex = (Number(layersObj[dataIndex]['imageWidth']) * y) + x;
                    if(coords[0] >= layersObj[dataIndex]['x_min'] && coords[0] <= layersObj[dataIndex]['x_max'] && coords[1] <= layersObj[dataIndex]['y_min'] && coords[1] >= layersObj[dataIndex]['y_max']){
                        if(Number(layersObj[dataIndex]['data'][rasterDataIndex]) >= Number(valLow) && Number(layersObj[dataIndex]['data'][rasterDataIndex]) <= Number(valHigh)){
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
                catch(e){}
                if(concavepoly){
                    const cnvepoly = geoJSONFormat.readFeature(concavepoly);
                    cnvepoly.getGeometry().transform(wgs84Projection,mapProjection);
                    selectsource.addFeature(cnvepoly);
                }
                hideWorking();
            }
            else{
                hideWorking();
                alert('Click the Show Target button and then click and drag the Target to the area you would like to vectorize. Then click the Grid-Based Vectorize button.');
            }
        }, 50);
    }
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
                const cluster = findRecordCluster(selections[i]);
                point = findRecordPointInCluster(cluster,selections[i]);
            }
            else{
                point = findRecordPoint(selections[i]);
            }
            if(point){
                ol.extent.extend(extent, point.getGeometry().getExtent());
            }
        }
    }
    map.getView().fit(extent, map.getSize());
}

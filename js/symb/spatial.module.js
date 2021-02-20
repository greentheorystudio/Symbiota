let spatialModuleInitialising = false;
let inputResponseData = {};
let geoPolyArr = [];
let geoCircleArr = [];
let geoBoundingBoxArr = {};
let geoPointArr = [];
let layersArr = [];
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
let dragDropTarget = '';
let dsOldestDate = '';
let dsNewestDate = '';
let tsOldestDate = '';
let tsNewestDate = '';
let dateSliderActive = false;
let sliderdiv = '';
let loadingTimer = 0;
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
const dragDropStyle = {
    'Point': new ol.style.Style({
        image: new ol.style.Circle({
            fill: new ol.style.Fill({
                color: 'rgba(255,255,0,0.5)'
            }),
            radius: 5,
            stroke: new ol.style.Stroke({
                color: '#ff0',
                width: 1
            })
        })
    }),
    'LineString': new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: '#f00',
            width: 3
        })
    }),
    'Polygon': new ol.style.Style({
        fill: new ol.style.Fill({
            color: 'rgba(170,170,170,0.3)'
        }),
        stroke: new ol.style.Stroke({
            color: '#000000',
            width: 1
        })
    }),
    'MultiPoint': new ol.style.Style({
        image: new ol.style.Circle({
            fill: new ol.style.Fill({
                color: 'rgba(255,0,255,0.5)'
            }),
            radius: 5,
            stroke: new ol.style.Stroke({
                color: '#f0f',
                width: 1
            })
        })
    }),
    'MultiLineString': new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: '#0f0',
            width: 3
        })
    }),
    'MultiPolygon': new ol.style.Style({
        fill: new ol.style.Fill({
            color: 'rgba(170,170,170,0.3)'
        }),
        stroke: new ol.style.Stroke({
            color: '#000000',
            width: 1
        })
    })
};

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
    source: new ol.source.XYZ({
        url: 'http://mt0.google.com/vt/lyrs=p&hl=en&x={x}&y={y}&z={z}',
        crossOrigin: 'anonymous'
    })
});

function addLayerToSelList(layer,title){
    const origValue = document.getElementById("selectlayerselect").value;
    let selectionList = document.getElementById("selectlayerselect").innerHTML;
    const optionId = "lsel-" + layer;
    const newOption = '<option id="lsel-' + optionId + '" value="' + layer + '">' + title + '</option>';
    selectionList += newOption;
    document.getElementById("selectlayerselect").innerHTML = selectionList;
    if(layer !== 'select'){
        document.getElementById("selectlayerselect").value = layer;
        setActiveLayer();
    }
    else{
        document.getElementById("selectlayerselect").value = origValue;
    }
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

function buildLayerTableRow(lArr,removable){
    let trfragment = '';
    const layerID = lArr['Name'];
    const layerType = lArr['layerType'];
    const addLayerFunction = 'editVectorLayers';
    const divid = "lay-" + layerID;
    if(!document.getElementById(divid)){
        trfragment += '<td style="width:30px;">';
        const onchange = (removable ? "toggleUploadLayer(this,'" + lArr['Title'] + "');" : addLayerFunction + "(this,'" + lArr['Title'] + "');");
        trfragment += '<input type="checkbox" value="'+layerID+'" onchange="'+onchange+'" '+(removable?'checked ':'')+'/>';
        trfragment += '</td>';
        trfragment += '<td style="width:170px;">';
        trfragment += '<b>'+lArr['Title']+'</b>';
        trfragment += '</td>';
        trfragment += '<td style="width:330px;">';
        trfragment += lArr['Abstract'];
        trfragment += '</td>';
        trfragment += '<td style="width:50px;background-color:black">';
        trfragment += '<img src="../images/'+(layerType === 'vector'?'button_wfs.png':'button_wms.png')+'" style="width:20px;margin-left:8px;">';
        trfragment += '</td>';
        trfragment += '<td style="width:50px;">';
        if(removable){
            const onclick = "removeUserLayer('" + layerID + "');";
            trfragment += '<input type="image" style="margin-left:5px;" src="../images/del.png" onclick="'+onclick+'" title="Remove layer">';
        }
        trfragment += '</td>';
        const layerTable = document.getElementById("layercontroltable");
        const newLayerRow = (removable ? layerTable.insertRow(0) : layerTable.insertRow());
        newLayerRow.id = 'lay-'+layerID;
        newLayerRow.innerHTML = trfragment;
        if(removable) addLayerToSelList(layerID,lArr['Title']);
    }
    else{
        document.getElementById("selectlayerselect").value = layerID;
        setActiveLayer();
    }
    toggleLayerTable();
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
                infoArr['Name'] = 'select';
                infoArr['Title'] = 'Shapes';
                infoArr['layerType'] = 'vector';
                infoArr['Abstract'] = '';
                infoArr['DefaultCRS'] = '';
                buildLayerTableRow(infoArr,true);
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

function changeHeatMapBlur(){
    heatMapBlur = document.getElementById("heatmapblur").value;
    layersArr['heat'].setBlur(parseInt(heatMapBlur, 10));
}

function changeHeatMapRadius(){
    heatMapRadius = document.getElementById("heatmapradius").value;
    layersArr['heat'].setRadius(parseInt(heatMapRadius, 10));
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

function changeRecordPage(page){
    let params;
    document.getElementById("queryrecords").innerHTML = "<p>Loading...</p>";
    const selJson = JSON.stringify(selections);
    const http = new XMLHttpRequest();
    const url = "rpc/changemaprecordpage.php";
    const jsonStarr = JSON.stringify(searchTermsArr);
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
            taxaSymbology[i]['color'] = "E69E67";
            const keyName = 'taxaColor' + i;
            if(document.getElementById(keyName)){
                document.getElementById(keyName).color.fromString("E69E67");
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
        exportStr = exportStr.replace(/<kml xmlns="http:\/\/www.opengis.net\/kml\/2.2" xmlns:gx="http:\/\/www.google.com\/kml\/ext\/2.2" xmlns:xsi="http:\/\/www.w3.org\/2001\/XMLSchema-instance" xsi:schemaLocation="http:\/\/www.opengis.net\/kml\/2.2 https:\/\/developers.google.com\/kml\/schema\/kml22gx.xsd">/g,'<kml xmlns="http://www.opengis.net/kml/2.2"><Document id="root_doc"><Folder><name>shapes_export</name>');
        exportStr = exportStr.replace(/<Placemark>/g,'<Placemark><Style><LineStyle><color>ff000000</color><width>1</width></LineStyle><PolyStyle><color>4DAAAAAA</color><fill>1</fill></PolyStyle></Style>');
        exportStr = exportStr.replace(/<Polygon>/g,'<Polygon><altitudeMode>clampToGround</altitudeMode>');
        exportStr = exportStr.replace(/<\/kml>/g,'</Folder></Document></kml>');
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
    map.once('rendercomplete', function() {
        const currentCanvas = document.getElementsByTagName('canvas').item(0);
        let mapCanvas = document.createElement('canvas');
        mapCanvas.width = currentCanvas.width;
        mapCanvas.height = currentCanvas.height;
        const mapContext = mapCanvas.getContext('2d');
        Array.prototype.forEach.call(document.querySelectorAll('.ol-layer canvas'), function(canvas) {
            if (canvas.width > 0) {
                const opacity = canvas.parentNode.style.opacity;
                mapContext.globalAlpha = opacity === '' ? 1 : Number(opacity);
                const transform = canvas.style.transform;
                const matrix = transform.match(/^matrix\(([^(]*)\)$/)[1].split(',').map(Number);
                CanvasRenderingContext2D.prototype.setTransform.apply(mapContext, matrix);
                mapContext.drawImage(canvas, 0, 0);
            }
        });
        if(zip){
            const image = mapCanvas.toDataURL('image/png', 1.0);
            mapCanvas = '';
            zipFolder.file(filename, image.substr(image.indexOf(',')+1), {base64: true});
            if(dsAnimImageSave && dsAnimStop){
                zipFile.generateAsync({type:"blob"}).then(function(content) {
                    const zipfilename = 'dateanimationimages_' + getDateTimeString() + '.zip';
                    saveAs(content,zipfilename);
                });
            }
        }
        else if(navigator.msSaveBlob) {
            navigator.msSaveBlob(mapCanvas.msToBlob(),filename);
            mapCanvas = '';
        }
        else{
            mapCanvas.toBlob(function(blob) {
                saveAs(blob,filename);
                mapCanvas = '';
            });
        }
    });
    map.renderSync();
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

function getDragDropStyle(feature, resolution) {
    const featureStyleFunction = feature.getStyleFunction();
    if(featureStyleFunction) {
        return featureStyleFunction.call(feature, resolution);
    }
    else{
        return dragDropStyle[feature.getGeometry().getType()];
    }
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
    const jsonStarr = JSON.stringify(searchTermsArr);
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
    const jsonStarr = JSON.stringify(searchTermsArr);
    if(SOLRMODE){
        url = "rpc/SOLRConnector.php";
        params = 'starr=' + jsonStarr + '&rows='+lazyLoadCnt+'&start='+startindex+'&fl='+SOLRFields+'&wt=geojson';
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                loadingComplete = false;
                setTimeout(checkLoading,loadingTimer);
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
                setTimeout(checkLoading,loadingTimer);
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
        pointvectorsource = new ol.source.Vector({
            wrapX: false
        });
        layersArr['pointv'].setSource(pointvectorsource);
        getQueryRecCnt(function() {
            if(queryRecCnt > 0){
                loadPointsEvent = true;
                setCopySearchUrlDiv();
                setLoadingTimer();
                loadPointWFSLayer(0);
                //cleanSelectionsLayer();
                setRecordsTab();
                changeRecordPage(1);
                $('#recordstab').tabs({active: 1});
                $("#accordion").accordion("option","active",1);
                //selectInteraction.getFeatures().clear();
                if(!pointActive){
                    const infoArr = [];
                    infoArr['Name'] = 'pointv';
                    infoArr['layerType'] = 'vector';
                    infoArr['Title'] = 'Points';
                    infoArr['Abstract'] = '';
                    infoArr['DefaultCRS'] = '';
                    buildLayerTableRow(infoArr,true);
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

function openOccidInfoBox(occid,label){
    const occpos = findOccClusterPosition(occid);
    finderpopupcontent.innerHTML = label;
    finderpopupoverlay.setPosition(occpos);
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
            const color = 'e69e67';
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
            namestring = namestring.replace(" ","");
            namestring = namestring.toLowerCase();
            namestring = namestring.replace(/[^A-Za-z0-9 ]/g,'');
            if(!collSymbology[collName]){
                collSymbology[collName] = [];
                collSymbology[collName]['collid'] = collid;
                collSymbology[collName]['color'] = color;
            }
            if(!taxaSymbology[namestring]){
                taxaCnt++;
                taxaSymbology[namestring] = [];
                taxaSymbology[namestring]['sciname'] = sciname;
                taxaSymbology[namestring]['tidinterpreted'] = tidinterpreted;
                taxaSymbology[namestring]['family'] = family;
                taxaSymbology[namestring]['color'] = color;
                taxaSymbology[namestring]['count'] = 1;
            }
            else{
                taxaSymbology[namestring]['count'] = taxaSymbology[namestring]['count'] + 1;
            }
            features[f].set('namestring',namestring,true);
        }
    }
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
            const centerCoords = ol.proj.fromLonLat([decLat, decLong]);
            const circle = new ol.geom.Circle(centerCoords);
            circle.setRadius(Number(openerRadius));
            const circleFeature = new ol.Feature(circle);
            uncertaintycirclesource.addFeature(circleFeature);
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
            opener.document.getElementById('pointlat').dispatchEvent(changeEvent);
        }
        if(opener.document.getElementById('pointlong')){
            opener.document.getElementById('pointlong').value = inputResponseData['circleArr'][0]['pointlong'];
            opener.document.getElementById('pointlong').dispatchEvent(changeEvent);
        }
        if(opener.document.getElementById('radiusunits')){
            opener.document.getElementById('radiusunits').value = 'km';
        }
        if(opener.document.getElementById('radiustemp')){
            opener.document.getElementById('radiustemp').value = inputResponseData['circleArr'][0]['groundradius'];
        }
        if(opener.document.getElementById('radius')){
            opener.document.getElementById('radius').value = inputResponseData['circleArr'][0]['radius'];
            opener.document.getElementById('radius').dispatchEvent(changeEvent);
        }
        if(opener.document.getElementById('groundradius')){
            opener.document.getElementById('groundradius').value = inputResponseData['circleArr'][0]['groundradius'];
            opener.document.getElementById('groundradius').dispatchEvent(changeEvent);
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

function removeLayerToSelList(layer){
    const selectobject = document.getElementById("selectlayerselect");
    for (let i = 0; i<selectobject.length; i++){
        if(selectobject.options[i].value === layer) selectobject.remove(i);
    }
    setActiveLayer();
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

function removeUserLayer(layerID){
    const layerDivId = "lay-" + layerID;
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
        pointvectorsource = new ol.source.Vector({
            wrapX: false
        });
        layersArr['pointv'].setSource(pointvectorsource);
        layersArr['heat'].setSource(pointvectorsource);
        layersArr['heat'].setVisible(false);
        clustersource = '';
        $('#criteriatab').tabs({active: 0});
        $("#accordion").accordion("option","active",0);
        pointActive = false;
    }
    else{
        layersArr[layerID].setSource(blankdragdropsource);
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
    document.getElementById("selectlayerselect").value = 'none';
    removeLayerToSelList(layerID);
    setActiveLayer();
    toggleLayerTable();
}

function resetMainSymbology(){
    for(let i in collSymbology){
        if(collSymbology.hasOwnProperty(i)){
            collSymbology[i]['color'] = "E69E67";
            const keyName = 'keyColor' + i;
            if(document.getElementById(keyName)){
                document.getElementById(keyName).color.fromString("E69E67");
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
            if(size < 10) radius = 10;
            else if(size < 100) radius = 15;
            else if(size < 1000) radius = 20;
            else if(size < 10000) radius = 25;
            else if(size < 100000) radius = 30;
            else radius = 35;

            if(selected) {
                stroke = new ol.style.Stroke({color: '#10D8E6', width: 2})
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
    const stArrJson = JSON.stringify(searchTermsArr);
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
        alert('You may only have 3 uploaded layers at a time. Please remove one of the currently uploaded layers to upload more.');
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
    if(searchTermsArr.hasOwnProperty('hasimages')){
        document.getElementById("hasimages").checked = true;
    }
    if(searchTermsArr.hasOwnProperty('hasgenetic')){
        document.getElementById("hasgenetic").checked = true;
    }
    if(searchTermsArr.hasOwnProperty('upperlat') || searchTermsArr.hasOwnProperty('pointlat') || searchTermsArr.hasOwnProperty('circleArr') || searchTermsArr.hasOwnProperty('polyArr')){
        document.getElementById("noshapecriteria").style.display = "none";
        document.getElementById("shapecriteria").style.display = "block";
    }
}

function setLayersTable(){
    const http = new XMLHttpRequest();
    const url = "rpc/getlayersarr.php";
    //console.log(url+'?'+params);
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function() {
        if(http.readyState === 4 && http.status === 200) {
            let layerArr;
            const jsonReturn = false;
            try{
                layerArr = JSON.parse(http.responseText);
            }catch(e){
                return false;
            }
            for(let i in layerArr){
                if(layerArr.hasOwnProperty(i) && String(layerArr[i])){
                    buildLayerTableRow(layerArr[i],false);
                }
            }
        }
        toggleLayerTable();
    };
    http.send();
}

function setLoadingTimer(){
    loadingTimer = 20000;
    if(queryRecCnt < 200000) loadingTimer = 13000;
    if(queryRecCnt < 150000) loadingTimer = 10000;
    if(queryRecCnt < 100000) loadingTimer = 7000;
    if(queryRecCnt < 50000) loadingTimer = 5000;
    if(queryRecCnt < 10000) loadingTimer = 3000;
    if(queryRecCnt < 5000) loadingTimer = 1000;
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
            stroke = new ol.style.Stroke({color: '#10D8E6', width: 2});
        }
        else {
            stroke = new ol.style.Stroke({color: 'black', width: 1});
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
                radius: 7
            })
        });
    }
    else{
        style = new ol.style.Style({
            image: new ol.style.Circle({
                radius: 7,
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

function toggleLayerTable(layerID){
    const tableRows = document.getElementById("layercontroltable").rows.length;
    if(tableRows > 0){
        document.getElementById("nolayermessage").style.display = "none";
        document.getElementById("layercontroltable").style.display = "block";
    }
    else{
        $('#addLayers').popup('hide');
        document.getElementById("nolayermessage").style.display = "block";
        document.getElementById("layercontroltable").style.display = "none";
    }
}

function toggleUploadLayer(c,title){
    let layer = c.value;
    if(layer === 'pointv' && showHeatMap) layer = 'heat';
    if(c.checked === true){
        layersArr[layer].setVisible(true);
        addLayerToSelList(c.value,title);
    }
    else{
        layersArr[layer].setVisible(false);
        removeLayerToSelList(c.value);
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

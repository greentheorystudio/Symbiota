const spatialControlPanel = {
    template: `
        <template v-if="(windowWidth >= 875 && !inputWindowMode) || (windowWidth >= 600 && inputWindowMode)">
            <template v-if="windowWidth >= 1220 && !inputWindowMode">
                <div class="absolute-top full-width row justify-center">
                    <div class="z-top map-control-panel-inner-container-wide map-info-window-container animate__animated animate__slow" :class="(!mapSettings.showSidePanel && !mapSettings.showControlPanelLeft && mapSettings.showControlPanelTop) ? 'animate__slideInDown' : 'animate__slideOutUp'">
                        <div class="q-pt-xs q-px-sm">
                            <div class="row justify-around items-center q-gutter-sm">
                                <div class="col-1(wider)">
                                    <spatial-draw-tool-selector></spatial-draw-tool-selector>
                                </div>
                                <div class="col-3">
                                    <spatial-base-layer-selector @change-base-layer="processChangeBaseLayer"></spatial-base-layer-selector>
                                </div>
                                <div class="col-3">
                                    <spatial-active-layer-selector :selected-active-layer="mapSettings.activeLayer"></spatial-active-layer-selector>
                                </div>
                                <template v-if="!inputWindowMode">
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="updateMapSettings('showMapSettings', true);" label="Settings" dense />
                                    </div>
                                </template>
                                <div>
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="updateMapSettings('showLayerController', true);" label="Layers" dense />
                                </div>
                                <template v-if="!inputWindowMode">
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="setQueryPopupDisplay(true);" icon="search" label="Search" dense />
                                    </div>
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" icon="home" dense @click="goHome();">
                                            <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Go to homepage
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </template>
                                <div>
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" icon="photo_camera" dense @click="exportMapPNG();">
                                        <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Download Map Image
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                                <template v-if="!inputWindowMode">
                                    <div>
                                        <q-btn class="map-info-window-container control-panel text-bold" size="md" icon="far fa-question-circle" stretch flat dense ripple="false" @click="openTutorialWindow('../tutorial/spatial/index.php');">
                                            <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Open Tutorial Window
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </template>
                            </div>
                            <template v-if="inputWindowMode">
                                <div class="q-mt-xs row justify-around items-center q-gutter-sm">
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="updateMapSettings('showInfoWindow', true);" label="Info" dense />
                                    </div>
                                    <div>
                                        <q-btn :disabled="mapSettings.submitButtonDisabled" color="grey-4" text-color="black" class="black-border" size="md" @click="processInputSubmit();" :label="mapSettings.submitButtonText" dense />
                                    </div>
                                    <template v-if="inputWindowToolsArr.includes('uncertainty') || inputWindowToolsArr.includes('radius')">
                                        <div>
                                            <q-input type="number" bg-color="white" outlined v-model="mapSettings.uncertaintyRadiusValue" class="col-5" :label="mapSettings.uncertaintyRadiusText" min="0" dense @update:model-value="changeInputPointUncertainty" />
                                        </div>
                                        <div v-if="inputWindowToolsArr.includes('radius')">
                                            <selector-input-element :options="radiusUnitOptions" :value="mapSettings.radiusUnits" @update:value="(value) => updateRadiusUnits(value)"></selector-input-element>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div class="row justify-center cursor-pointer" @click="updateMapSettings('showControlPanelTop', false);">
                            <q-icon color="white" size="sm" name="fas fa-caret-up"></q-icon>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="absolute-top full-width row justify-center">
                    <div class="z-top column map-control-panel-inner-container-med map-info-window-container animate__animated animate__slow" :class="(!mapSettings.showSidePanel && !mapSettings.showControlPanelLeft && mapSettings.showControlPanelTop) ? 'animate__slideInDown' : 'animate__slideOutUp'">
                        <div class="q-pt-xs q-px-sm">
                            <template v-if="inputWindowMode">
                                <div class="row justify-around items-center q-gutter-sm">
                                    <div class="col-1(wider)">
                                        <spatial-draw-tool-selector></spatial-draw-tool-selector>
                                    </div>
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="updateMapSettings('showInfoWindow', true);" label="Info" dense />
                                    </div>
                                    <div>
                                        <q-btn :disabled="mapSettings.submitButtonDisabled" color="grey-4" text-color="black" class="black-border" size="md" @click="processInputSubmit();" :label="mapSettings.submitButtonText" dense />
                                    </div>
                                </div>
                                <template v-if="inputWindowToolsArr.includes('uncertainty') || inputWindowToolsArr.includes('radius')">
                                    <div class="q-mt-xs row justify-center items-center q-gutter-sm">
                                        <div>
                                            <q-input type="number" bg-color="white" outlined v-model="mapSettings.uncertaintyRadiusValue" class="col-5" :label="mapSettings.uncertaintyRadiusText" min="0" dense @update:model-value="changeInputPointUncertainty" />
                                        </div>
                                        <div v-if="inputWindowToolsArr.includes('radius')">
                                            <selector-input-element :options="radiusUnitOptions" :value="mapSettings.radiusUnits" label="Radius units" @update:value="(value) => updateRadiusUnits(value)"></selector-input-element>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <template v-else>
                                <div class="row justify-between items-center q-gutter-sm">
                                    <div class="col-1(wider)">
                                        <spatial-draw-tool-selector></spatial-draw-tool-selector>
                                    </div>
                                    <div class="col-4">
                                        <spatial-base-layer-selector @change-base-layer="processChangeBaseLayer"></spatial-base-layer-selector>
                                    </div>
                                    <div class="col-4">
                                        <spatial-active-layer-selector :selected-active-layer="mapSettings.activeLayer"></spatial-active-layer-selector>
                                    </div>
                                </div>
                                <div class="q-mt-xs row justify-between items-center q-gutter-sm">
                                    <div v-if="!inputWindowMode">
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="updateMapSettings('showMapSettings', true);" label="Settings" dense />
                                    </div>
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="updateMapSettings('showLayerController', true);" label="Layers" dense />
                                    </div>
                                    <template v-if="!inputWindowMode">
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="setQueryPopupDisplay(true);" icon="search" label="Search" dense />
                                        </div>
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="md" icon="home" dense @click="goHome();">
                                                <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Go to homepage
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </template>
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" icon="photo_camera" dense @click="exportMapPNG();">
                                            <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Download Map Image
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                    <div>
                                        <q-btn class="map-info-window-container control-panel text-bold" size="md" icon="far fa-question-circle" stretch flat dense ripple="false" @click="openTutorialWindow('../tutorial/spatial/index.php');"></q-btn>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="row justify-center cursor-pointer" @click="updateMapSettings('showControlPanelTop', false);">
                            <q-icon color="white" size="sm" name="fas fa-caret-up"></q-icon>
                        </div>
                    </div>
                </div>
            </template>
            <spatial-control-panel-top-show-button></spatial-control-panel-top-show-button>
        </template>
        <template v-if="(windowWidth < 875 && !inputWindowMode) || inputWindowMode">
            <div class="z-top side-panel-container row animate__animated animate__slow" :class="(!mapSettings.showSidePanel && mapSettings.showControlPanelLeft) ? 'animate__slideInLeft' : 'animate__slideOutLeft'">
                <div class="map-side-panel-inner-container">
                    <div class="map-side-panel-content q-pa-md">
                        <div class="q-gutter-sm">
                            <template v-if="!inputWindowMode || windowWidth < 600">
                                <div>
                                    <spatial-draw-tool-selector></spatial-draw-tool-selector>
                                </div>
                            </template>
                            <div>
                                <spatial-base-layer-selector @change-base-layer="processChangeBaseLayer"></spatial-base-layer-selector>
                            </div>
                            <div>
                                <spatial-active-layer-selector></spatial-active-layer-selector>
                            </div>
                            <template v-if="!inputWindowMode">
                                <div>
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="setQueryPopupDisplay(true);" icon="search" label="Search" dense />
                                </div>
                                <div>
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" icon="home" dense @click="goHome();">
                                        <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Go to homepage
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                                <div>
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="updateMapSettings('showMapSettings', true);" label="Settings" dense />
                                </div>
                            </template>
                            <div>
                                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="updateMapSettings('showLayerController', true);" label="Layers" dense />
                            </div>
                            <div>
                                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="exportMapPNG();" label="Download Map Image" dense />
                            </div>
                            <template v-if="!inputWindowMode">
                                <div>
                                    <q-btn class="control-panel text-bold" size="md" icon="far fa-question-circle" stretch flat dense ripple="false" @click="openTutorialWindow('../tutorial/spatial/index.php');"></q-btn>
                                </div>
                            </template>
                            <template v-if="inputWindowMode && windowWidth < 600">
                                <div>
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="updateMapSettings('showInfoWindow', true);" label="Info" dense />
                                </div>
                                <div>
                                    <q-btn :disabled="mapSettings.submitButtonDisabled" color="grey-4" text-color="black" class="black-border" size="md" @click="processInputSubmit();" :label="mapSettings.submitButtonText" dense />
                                </div>
                                <template v-if="inputWindowToolsArr.includes('uncertainty') || inputWindowToolsArr.includes('radius')">
                                    <div>
                                        <q-input type="number" bg-color="white" outlined v-model="mapSettings.uncertaintyRadiusValue" class="col-5" :label="mapSettings.uncertaintyRadiusText" min="0" dense @update:model-value="changeInputPointUncertainty" />
                                    </div>
                                    <div v-if="inputWindowToolsArr.includes('radius')">
                                        <selector-input-element :options="radiusUnitOptions" :value="mapSettings.radiusUnits" label="Radius units" @update:value="(value) => updateRadiusUnits(value)"></selector-input-element>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="col-grow column justify-center items-center cursor-pointer map-side-panel-close-bar" @click="updateMapSettings('showControlPanelLeft', false);">
                    <q-icon color="white" size="sm" name="fas fa-caret-left"></q-icon>
                </div>
            </div>
        </template>
        <spatial-map-settings-popup></spatial-map-settings-popup>
        <spatial-info-window-popup></spatial-info-window-popup>
    `,
    components: {
        'selector-input-element': selectorInputElement,
        'spatial-active-layer-selector': spatialActiveLayerSelector,
        'spatial-base-layer-selector': spatialBaseLayerSelector,
        'spatial-control-panel-top-show-button': spatialControlPanelTopShowButton,
        'spatial-draw-tool-selector': spatialDrawToolSelector,
        'spatial-info-window-popup': spatialInfoWindowPopup,
        'spatial-map-settings-popup': spatialMapSettingsPopup
    },
    setup() {
        const { openTutorialWindow } = useCore();
        const baseStore = useBaseStore();
        const searchStore = useSearchStore();

        const clientRoot = baseStore.getClientRoot;
        const inputWindowMode = Vue.inject('inputWindowMode');
        const inputWindowToolsArr = Vue.inject('inputWindowToolsArr');
        const map = Vue.inject('map');
        const mapSettings = Vue.inject('mapSettings');
        const radiusUnitOptions = Vue.ref([
            {value: 'km', label: 'Kilometers'},
            {value: 'mi', label: 'Miles'}
        ]);
        const selectInteraction = Vue.inject('selectInteraction');
        const windowWidth = Vue.inject('windowWidth');

        const processInputSubmit = Vue.inject('processInputSubmit');
        const processInputPointUncertaintyChange = Vue.inject('processInputPointUncertaintyChange');
        const setQueryPopupDisplay = Vue.inject('setQueryPopupDisplay');
        const updateMapSettings = Vue.inject('updateMapSettings');

        function changeBaseMap(){
            let blsource;
            const baseLayer = map.value.getLayers().getArray()[0];
            if(mapSettings.selectedBaseLayer === 'googleroadmap'){
                blsource = new ol.source.XYZ({
                    url: 'https://mt0.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'googlealteredroadmap'){
                blsource = new ol.source.XYZ({
                    url: 'https://mt0.google.com/vt/lyrs=r&hl=en&x={x}&y={y}&z={z}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'googleterrain'){
                blsource = new ol.source.XYZ({
                    url: 'https://mt0.google.com/vt/lyrs=p&hl=en&x={x}&y={y}&z={z}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'googlehybrid'){
                blsource = new ol.source.XYZ({
                    url: 'https://mt0.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'googlesatellite'){
                blsource = new ol.source.XYZ({
                    url: 'https://mt0.google.com/vt/lyrs=s&hl=en&x={x}&y={y}&z={z}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'worldtopo'){
                blsource = new ol.source.XYZ({
                    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'openstreet'){
                blsource = new ol.source.OSM();
            }
            else if(mapSettings.selectedBaseLayer === 'stamentoner'){
                blsource = new ol.source.StadiaMaps({ layer: 'stamen_toner' });
            }
            else if(mapSettings.selectedBaseLayer === 'stamentonerlite'){
                blsource = new ol.source.StadiaMaps({ layer: 'stamen_toner_lite' });
            }
            else if(mapSettings.selectedBaseLayer === 'stamenterrain'){
                blsource = new ol.source.StadiaMaps({ layer: 'stamen_terrain' });
            }
            else if(mapSettings.selectedBaseLayer === 'stamenalidade'){
                blsource = new ol.source.StadiaMaps({ layer: 'alidade_smooth' });
            }
            else if(mapSettings.selectedBaseLayer === 'stamenoutdoors'){
                blsource = new ol.source.StadiaMaps({ layer: 'outdoors' });
            }
            else if(mapSettings.selectedBaseLayer === 'worldimagery'){
                blsource = new ol.source.XYZ({
                    url: 'https://services.arcgisonline.com/arcgis/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'ocean'){
                blsource = new ol.source.XYZ({
                    url: 'https://services.arcgisonline.com/arcgis/rest/services/Ocean_Basemap/MapServer/tile/{z}/{y}/{x}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'ngstopo'){
                blsource = new ol.source.XYZ({
                    url: 'https://services.arcgisonline.com/arcgis/rest/services/USA_Topo_Maps/MapServer/tile/{z}/{y}/{x}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'natgeoworld'){
                blsource = new ol.source.XYZ({
                    url: 'https://services.arcgisonline.com/arcgis/rest/services/NatGeo_World_Map/MapServer/tile/{z}/{y}/{x}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'esristreet'){
                blsource = new ol.source.XYZ({
                    url: 'https://services.arcgisonline.com/arcgis/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'opentopo'){
                blsource = new ol.source.XYZ({
                    url: 'https://tile.opentopomap.org/{z}/{x}/{y}.png',
                    crossOrigin: 'anonymous'
                });
            }
            baseLayer.setSource(blsource);
        }

        function changeInputPointUncertainty(val) {
            updateMapSettings('uncertaintyRadiusValue', val);
            if(inputWindowToolsArr.includes('uncertainty')){
                processInputPointUncertaintyChange();
            }
            else if(inputWindowToolsArr.includes('radius')){
                updateRadius();
            }
        }

        function exportMapPNG(){
            const filename = 'map_' + searchStore.getDateTimeString + '.png';
            let mapCanvas = document.createElement('canvas');
            const size = map.value.getSize();
            mapCanvas.width = size[0];
            mapCanvas.height = size[1];
            const mapContext = mapCanvas.getContext('2d');
            Array.prototype.forEach.call(
                map.value.getViewport().querySelectorAll('.ol-layer canvas, canvas.ol-layer'),
                (canvas) => {
                    if(canvas.width > 0){
                        const opacity = canvas.parentNode.style.opacity || canvas.style.opacity;
                        mapContext.globalAlpha = opacity === '' ? 1 : Number(opacity);
                        const backgroundColor = canvas.parentNode.style.backgroundColor;
                        if(backgroundColor){
                            mapContext.fillStyle = backgroundColor;
                            mapContext.fillRect(0, 0, canvas.width, canvas.height);
                        }
                        let matrix;
                        const transform = canvas.style.transform;
                        if(transform){
                            matrix = transform
                                .match(/^matrix\(([^\(]*)\)$/)[1]
                                .split(',')
                                .map(Number);
                        }
                        else{
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
            mapCanvas.toBlob((blob) => {
                saveAs(blob,filename);
                mapCanvas = '';
            });
        }

        function goHome() {
            window.location.href = (clientRoot + '/index.php');
        }

        function processChangeBaseLayer(value) {
            updateMapSettings('selectedBaseLayer', value);
            changeBaseMap();
        }

        function updateRadius(){
            const selectFeatures = mapSettings.selectSource.getFeatures();
            if(selectFeatures.length > 0){
                if(selectFeatures[0]){
                    const geoType = selectFeatures[0].getGeometry().getType();
                    if(geoType === 'Circle'){
                        let radius;
                        if(mapSettings.radiusUnits === 'km'){
                            radius = (Number(mapSettings.uncertaintyRadiusValue) * 1000);
                        }
                        else if(mapSettings.radiusUnits === 'mi'){
                            radius = ((Number(mapSettings.uncertaintyRadiusValue) * 1.609344) * 1000);
                        }
                        else{
                            radius = Number(mapSettings.uncertaintyRadiusValue);
                        }
                        selectFeatures[0].getGeometry().setRadius(radius);
                    }
                }
            }
            const selectInteractionFeatures = selectInteraction.value.getFeatures().getArray();
            if(selectInteractionFeatures.length > 0){
                selectInteraction.value.getFeatures().clear();
                mapSettings.selectedFeatures.push(selectFeatures[0]);
            }
        }

        function updateRadiusUnits(value){
            updateMapSettings('radiusUnits', value);
            updateRadius();
        }

        return {
            inputWindowMode,
            inputWindowToolsArr,
            mapSettings,
            radiusUnitOptions,
            windowWidth,
            processInputSubmit,
            changeBaseMap,
            changeInputPointUncertainty,
            exportMapPNG,
            goHome,
            openTutorialWindow,
            processChangeBaseLayer,
            setQueryPopupDisplay,
            updateMapSettings,
            updateRadiusUnits
        }
    }
};

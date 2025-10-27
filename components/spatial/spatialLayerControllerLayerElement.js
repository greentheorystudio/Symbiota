const spatialLayerControllerLayerElement = {
    props: {
        layer: {
            type: Object,
            default: null
        },
        query: {
            type: Boolean,
            default: false
        },
        removable: {
            type: Boolean,
            default: false
        },
        sortable: {
            type: Boolean,
            default: false
        },
        symbology: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-card class="layer-controller-element">
            <q-card-section>
                <div class="column">
                    <div class="text-bold">
                        {{ layer.layerName }}
                    </div>
                    <template v-if="layer.layerDescription && layer.layerDescription.length > 0">
                        <div class="q-mt-xs">
                            {{ layer.layerDescription }}
                        </div>
                    </template>
                    <template v-if="layer.providedBy || layer.sourceURL">
                        <div>
                            <template v-if="layer.providedBy">
                                <span class="text-bold">Provided by: </span> {{ layer.providedBy + (layer.sourceURL ? ' ' : '') }}
                            </template>
                            <template v-if="layer.sourceURL">
                                <span class="text-bold"><a :href="layer.sourceURL" target="_blank">(Go to source)</a></span>
                            </template>
                        </div>
                    </template>
                    <template v-if="layer.dateAquired || layer.dateUploaded">
                        <div>
                            <template v-if="layer.dateAquired">
                                <span class="text-bold">Date aquired: </span> {{ layer.dateAquired + (layer.dateUploaded ? ' ' : '') }}
                            </template>
                            <template v-if="layer.dateUploaded">
                                <span class="text-bold">Date uploaded: </span> {{ layer.dateUploaded }}
                            </template>
                        </div>
                    </template>
                    <template v-if="layer.active && symbology">
                        <template v-if="mapDataType === 'raster'">
                            <div class="row justify-start q-mt-xs">
                                <div class="col-4">
                                    <spatial-raster-color-scale-select :selected-color-scale="layer.colorScale" @raster-color-scale-change="(value) => changeRasterColorScale(layer.id, value)"></spatial-raster-color-scale-select>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="row justify-evenly q-mt-xs q-gutter-sm">
                                <div class="row justify-start self-center">
                                    <div class="text-bold">
                                        Border color
                                    </div>
                                    <div class="q-ml-sm">
                                        <color-picker :color-value="layer.borderColor" @update:color-picker="(value) => changeElementStyling(layer.id, 'borderColor', value)"></color-picker>
                                    </div>
                                </div>
                                <div class="row justify-start self-center">
                                    <div class="text-bold">
                                        Fill color
                                    </div>
                                    <div class="q-ml-sm">
                                        <color-picker :color-value="layer.fillColor" @update:color-picker="(value) => changeElementStyling(layer.id, 'fillColor', value)"></color-picker>
                                    </div>
                                </div>
                                <div :class="windowWidth < 875 ? 'col-4(wider)' : ''">
                                    <text-field-input-element :clearable="false" data-type="int" label="Border width (px) [0-9]" :value="layer.borderWidth" min-value="0" @update:value="(value) => changeElementStyling(layer.id, 'borderWidth', value)"></text-field-input-element>
                                </div>
                                <div :class="windowWidth < 875 ? 'col-4(wider)' : ''">
                                    <text-field-input-element :clearable="false" data-type="int" label="Point radius (px)" :value="layer.pointRadius" min-value="0" @update:value="(value) => changeElementStyling(layer.id, 'pointRadius', value)"></text-field-input-element>
                                </div>
                                <div :class="windowWidth < 875 ? 'col-4(wider)' : ''">
                                    <text-field-input-element :clearable="false" data-type="increment" label="Fill opacity" :value="layer.opacity" min-value="0" max-value="1" step=".1" @update:value="(value) => changeElementStyling(layer.id, 'opacity', value)"></text-field-input-element>
                                </div>
                            </div>
                        </template>
                    </template>
                    <template v-if="windowWidth >= 875">
                        <div class="row justify-between q-mt-sm">
                            <div class="self-center">
                                <template v-if="mapDataType === 'raster'">
                                    <q-icon name="fas fa-border-all" color="black" class="layer-type-icon"></q-icon>
                                </template>
                                <template v-else>
                                    <q-icon name="fas fa-vector-square" color="black" class="layer-type-icon"></q-icon>
                                </template>
                            </div>
                            <div class="row justify-end items-center self-center q-mt-xs q-gutter-sm">
                                <template v-if="layer.active && sortable">
                                    <div class="col-3">
                                        <text-field-input-element :clearable="false" data-type="int" label="Order" :value="layer.layerOrder" min-value="1" :max-value="(layerOrderArr.length > 0 ? layerOrderArr.length : 1)" @update:value="(value) => changeLayerOrder(layer.id, value)"></text-field-input-element>
                                    </div>
                                </template>
                                <template v-if="layer.active && query && mapDataType === 'vector'">
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="toggleLayerQuerySelector(layer.id);" label="Query Selector" />
                                    </div>
                                </template>
                                <template v-if="removable">
                                    <div>
                                        <q-btn padding="xs" color="grey-4" text-color="black" class="black-border" icon="far fa-trash-alt" @click="removeUserLayer(layer.id);"></q-btn>
                                    </div>
                                </template>
                                <template v-if="layer.type === 'userLayer'">
                                    <div>
                                        <q-checkbox v-model="layer.active" @update:model-value="(value) => toggleUserLayerVisibility(layer.id, layer.layerName, value)"></q-checkbox>
                                    </div>
                                </template>
                                <template v-else>
                                    <div>
                                        <q-checkbox v-model="layer.active" @update:model-value="(value) => toggleServerLayerVisibility(layer.id, layer.layerName, layer.file, value)"></q-checkbox>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <template v-if="layer.active && ((query && mapDataType === 'vector') || sortable)">
                            <div class="row justify-between q-mt-sm">
                                <template v-if="query && mapDataType === 'vector'">
                                    <div class="self-center">
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="toggleLayerQuerySelector(layer.id);" label="Query Selector" />
                                    </div>
                                </template>
                                <template v-if="sortable">
                                    <div class="self-center col-3">
                                        <text-field-input-element :clearable="false" data-type="int" label="Order" :value="layer.layerOrder" min-value="1" :max-value="(layerOrderArr.length > 0 ? layerOrderArr.length : 1)" @update:value="(value) => changeLayerOrder(layer.id, value)"></text-field-input-element>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <div class="row justify-between  q-mt-sm">
                            <div class="self-center">
                                <template v-if="mapDataType === 'raster'">
                                    <q-icon name="fas fa-border-all" color="black" class="layer-type-icon"></q-icon>
                                </template>
                                <template v-else>
                                    <q-icon name="fas fa-vector-square" color="black" class="layer-type-icon"></q-icon>
                                </template>
                            </div>
                            <div class="row justify-end self-center q-gutter-sm">
                                <template v-if="removable">
                                    <div>
                                        <q-btn padding="xs" color="grey-4" text-color="black" class="black-border" icon="far fa-trash-alt" @click="removeUserLayer(layer.id);"></q-btn>
                                    </div>
                                </template>
                                <template v-if="layer.type === 'userLayer'">
                                    <div>
                                        <q-checkbox v-model="layer.active" @update:model-value="(value) => toggleUserLayerVisibility(layer.id, layer.layerName, value)"></q-checkbox>
                                    </div>
                                </template>
                                <template v-else>
                                    <div>
                                        <q-checkbox v-model="layer.active" @update:model-value="(value) => toggleServerLayerVisibility(layer.id, layer.layerName, layer.file, value)"></q-checkbox>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </q-card-section>
        </q-card>
    `,
    components: {
        'color-picker': colorPicker,
        'spatial-raster-color-scale-select': spatialRasterColorScaleSelect,
        'text-field-input-element': textFieldInputElement
    },
    setup(props) {
        const coreLayers = Vue.inject('coreLayers');
        const layerOrderArr = Vue.inject('layerOrderArr');
        const layersInfoObj = Vue.inject('layersInfoObj');
        const layersObj = Vue.inject('layersObj');
        const map = Vue.inject('map');
        const mapDataType = Vue.computed(() => {
            let returnVal = '';
            if(props.layer){
                returnVal = (props.layer.file.endsWith('.tif') || props.layer.file.endsWith('.tiff')) ? 'raster' : 'vector';
            }
            return returnVal;
        });
        const mapSettings = Vue.inject('mapSettings');
        const windowWidth = Vue.inject('windowWidth');

        const addLayerToActiveLayerOptions = Vue.inject('addLayerToActiveLayerOptions');
        const addLayerToLayersObj = Vue.inject('addLayerToLayersObj');
        const addLayerToRasterLayersArr = Vue.inject('addLayerToRasterLayersArr');
        const getArrayBuffer = Vue.inject('getArrayBuffer');
        const getVectorLayerStyle = Vue.inject('getVectorLayerStyle');
        const removeLayerFromActiveLayerOptions = Vue.inject('removeLayerFromActiveLayerOptions');
        const removeLayerFromLayersObj = Vue.inject('removeLayerFromLayersObj');
        const removeLayerFromRasterLayersArr = Vue.inject('removeLayerFromRasterLayersArr');
        const removeUserLayer = Vue.inject('removeUserLayer');
        const setLayersOrder = Vue.inject('setLayersOrder');
        const updateMapSettings = Vue.inject('updateMapSettings');
        const { hideWorking, showNotification, showWorking } = useCore();

        function addLayerToLayerOrderArr(layerId) {
            layerOrderArr.push(layerId);
            setLayersOrder();
        }

        function changeElementStyling(layerId, property, value) {
            if(property === 'borderWidth' && Number(value) > 9){
                value = 9;
            }
            layersInfoObj[layerId][property] = value;
            if(layersInfoObj[layerId]['active']){
                const style = getVectorLayerStyle(layersInfoObj[layerId]['fillColor'], layersInfoObj[layerId]['borderColor'], layersInfoObj[layerId]['borderWidth'], layersInfoObj[layerId]['pointRadius'], layersInfoObj[layerId]['opacity']);
                layersObj[layerId].setStyle(style);
            }
        }

        function changeLayerOrder(layerId, value){
            const currentIndex = layerOrderArr.indexOf(layerId);
            layerOrderArr.splice(currentIndex,1);
            layerOrderArr.splice((value - 1),0,layerId);
            setLayersOrder();
        }

        function changeRasterColorScale(layerId, value){
            layersInfoObj[layerId]['colorScale'] = value;
            map.value.removeLayer(layersObj[layerId]);
            layersObj[layerId].setSource(null);
            const sourceIndex = layerId + 'Source';
            const dataIndex = layerId + 'Data';
            removeLayerFromLayersObj(sourceIndex);
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
            addLayerToLayersObj(sourceIndex, new ol.source.ImageStatic({
                url: canvasElement.toDataURL("image/png"),
                imageExtent: box,
                projection: 'EPSG:4326'
            }));
            layersObj[layerId].setSource(layersObj[sourceIndex]);
            map.value.addLayer(layersObj[layerId]);
            setLayersOrder();
        }

        function loadServerLayer(id, name, file){
            showWorking('Loading...');
            const zIndex = layerOrderArr.length + 1;
            const filenameParts = file.split('.');
            const fileType = filenameParts.pop();
            if(fileType === 'geojson' || fileType === 'kml' || fileType === 'zip'){
                addLayerToLayersObj(id, new ol.layer.Vector({
                    source: new ol.source.Vector({
                        wrapX: true
                    }),
                    zIndex: zIndex,
                    style: getVectorLayerStyle(layersInfoObj[id]['fillColor'], layersInfoObj[id]['borderColor'], layersInfoObj[id]['borderWidth'], layersInfoObj[id]['pointRadius'], layersInfoObj[id]['opacity'])
                }));
            }
            else{
                addLayerToLayersObj(id, new ol.layer.Image({
                    zIndex: zIndex,
                }));
            }
            if(fileType === 'geojson'){
                layersObj[id].setSource(new ol.source.Vector({
                    url: ('/content/spatial/' + file),
                    format: new ol.format.GeoJSON(),
                    wrapX: true
                }));
                layersObj[id].getSource().on('addfeature', () => {
                    map.value.getView().fit(layersObj[id].getSource().getExtent());
                });
                layersObj[id].on('postrender', () => {
                    hideWorking();
                });
            }
            else if(fileType === 'kml'){
                layersObj[id].setSource(new ol.source.Vector({
                    url: ('/content/spatial/' + file),
                    format: new ol.format.KML({
                        extractStyles: false,
                    }),
                    wrapX: true
                }));
                layersObj[id].getSource().on('addfeature', () => {
                    map.value.getView().fit(layersObj[id].getSource().getExtent());
                });
                layersObj[id].on('postrender', () => {
                    hideWorking();
                });
            }
            else if(fileType === 'zip'){
                fetch(('/content/spatial/' + file)).then((fileFetch) => {
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
                                map.value.getView().fit(layersObj[id].getSource().getExtent());
                                layersObj[id].on('postrender', () => {
                                    hideWorking();
                                });
                            });
                        });
                    });
                });
            }
            else if(fileType === 'tif' || fileType === 'tiff'){
                fetch(('/content/spatial/' + file)).then((fileFetch) => {
                    fileFetch.blob().then((blob) => {
                        blob.arrayBuffer().then((data) => {
                            const tiff = GeoTIFF.parse(data);
                            const image = tiff.getImage();
                            try {
                                if(image.getWidth()){
                                    const bands = image.readRasters();
                                    const extent = ol.extent.createEmpty();
                                    const dataIndex = id + 'Data';
                                    const rawBox = image.getBoundingBox();
                                    const box = [rawBox[0],rawBox[1] - (rawBox[3] - rawBox[1]), rawBox[2], rawBox[1]];
                                    const meta = image.getFileDirectory();
                                    const x_min = meta.ModelTiepoint[3];
                                    const x_max = x_min + meta.ModelPixelScale[0] * meta.ImageWidth;
                                    const y_min = meta.ModelTiepoint[4];
                                    const y_max = y_min - meta.ModelPixelScale[1] * meta.ImageLength;
                                    const imageWidth = image.getWidth();
                                    const imageHeight = image.getHeight();
                                    let minValue = 0;
                                    let maxValue = 0;
                                    bands[0].forEach((item) => {
                                        if(item < minValue && ((minValue - item) < 5000)){
                                            minValue = item;
                                        }
                                        if(item > maxValue){
                                            maxValue = item;
                                        }
                                    });
                                    addLayerToLayersObj(dataIndex, {});
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
                                        colorScale: layersInfoObj[id]['colorScale']
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
                                    map.value.getView().fit(extent, map.value.getSize());
                                    addLayerToRasterLayersArr(id, name);
                                }
                            }
                            catch(err) {
                                showNotification('negative','That layer cannot be loaded correctly.');
                            }
                            hideWorking();
                        });
                    });
                });
            }
            map.value.addLayer(layersObj[id]);
        }

        function removeLayerFromLayerOrderArr(layerId) {
            const index = layerOrderArr.indexOf(layerId);
            layerOrderArr.splice(index,1);
            setLayersOrder();
        }

        function removeServerLayer(id) {
            map.value.removeLayer(layersObj[id]);
            const dataIndex = id + 'Data';
            if(layersObj.hasOwnProperty(dataIndex)){
                removeLayerFromRasterLayersArr(id);
                removeLayerFromLayersObj(dataIndex);
            }
            removeLayerFromLayersObj(id);
        }

        function toggleLayerQuerySelector(layerId) {
            updateMapSettings('layerQuerySelectorId', layerId);
            updateMapSettings('showLayerController', false);
            updateMapSettings('showLayerQuerySelector', true);
        }

        function toggleServerLayerVisibility(id, name, file, visible){
            if(visible === true){
                loadServerLayer(id,name,file);
                addLayerToActiveLayerOptions(id,name,false);
                addLayerToLayerOrderArr(id);
            }
            else{
                removeServerLayer(id);
                removeLayerFromActiveLayerOptions(id);
                removeLayerFromLayerOrderArr(id);
            }
        }

        function toggleUserLayerVisibility(id, name, visible) {
            let layerId = id;
            if(id === 'pointv' && mapSettings.showHeatMap) {
                layerId = 'heat';
            }
            if(visible === true){
                layersObj[layerId].setVisible(true);
                addLayerToActiveLayerOptions(id,name,false);
                if(!coreLayers.includes(id)){
                    addLayerToLayerOrderArr(id);
                }
            }
            else{
                layersObj[layerId].setVisible(false);
                removeLayerFromActiveLayerOptions(id);
                if(!coreLayers.includes(id)){
                    removeLayerFromLayerOrderArr(id);
                }
            }
        }
        
        return {
            layerOrderArr,
            mapDataType,
            windowWidth,
            changeElementStyling,
            changeLayerOrder,
            changeRasterColorScale,
            removeUserLayer,
            toggleLayerQuerySelector,
            toggleServerLayerVisibility,
            toggleUserLayerVisibility
        }
    }
};

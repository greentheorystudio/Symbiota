const spatialVectorToolsTab = {
    template: `
        <div class="q-pa-sm column">
            <div class="q-mb-sm">
                <div class="text-bold row justify-center">Total area of selected features (sq/km):</div>
                <div class="text-h6 text-bold row justify-center">{{ mapSettings.polyArea }}</div>
            </div>
            <q-separator ></q-separator>
            <div class="q-py-sm row justify-center">
                <div>
                    <q-btn color="grey-4" size="md" text-color="black" class="black-border" @click="deleteSelections();" label="Delete Selected Features" dense />
                </div>
            </div>
            <q-separator ></q-separator>
            <div class="q-py-sm row justify-between q-gutter-sm">
                <div class="col-6">
                    <q-select bg-color="white" outlined v-model="selectedDownloadType" :options="downloadTypeOptions" :option-value="value" :option-label="label" label="Download Type" popup-content-class="z-max" behavior="menu" dense options-dense />
                </div>
                <div class="self-center">
                    <q-btn color="grey-4" size="md" text-color="black" class="black-border" icon="fas fa-download" @click="downloadShapesLayer();" dense />
                </div>
            </div>
            <q-separator ></q-separator>
            <div class="q-my-sm column">
                <div>
                    <span class="text-bold">Buffer:</span> Creates a buffer polygon of the entered width in km around each of the selected features.
                    <template v-if="mapSettings.polyCount < 1">
                        <span class="text-red"> At least one feature in the Shapes layer needs to be selected to use this tool.</span>
                    </template> 
                </div>
                <div class="row justify-between q-gutter-sm">
                    <div>
                        <q-input type="number" outlined v-model="bufferWidthValue" min="0" class="col-3" label="Buffer Width (km)" @update:model-value="processBufferWidthChange" dense />
                    </div>
                    <div class="self-center">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="createBuffers();" label="Create Buffer" :disabled="mapSettings.featureCount < 1" dense />
                    </div>
                </div>
            </div>
            <q-separator ></q-separator>
            <div class="q-my-sm column">
                <div>
                    <span class="text-bold">Difference:</span> Creates a new polygon with the area of the polygon, box, or circle selected first, 
                    excluding the area of the polygon, box, or circle selected second.
                    <template v-if="mapSettings.polyCount !== 2">
                        <span class="text-red"> Two features in the Shapes layer need to be selected to use this tool.</span>
                    </template> 
                </div>
                <div class="row justify-end q-gutter-sm">
                    <div class="self-center">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="createPolyDifference();" label="Create Difference" :disabled="mapSettings.polyCount !== 2" dense />
                    </div>
                </div>
            </div>
            <q-separator ></q-separator>
            <div class="q-my-sm column">
                <div>
                    <span class="text-bold">Intersect:</span> Creates a new polygon with the overlapping area of two selected polygons, boxes, or circles.
                    <template v-if="mapSettings.polyCount !== 2">
                        <span class="text-red"> Two features in the Shapes layer need to be selected to use this tool.</span>
                    </template> 
                </div>
                <div class="row justify-end q-gutter-sm">
                    <div class="self-center">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="createPolyIntersect();" label="Create Intersect" :disabled="mapSettings.polyCount !== 2" dense />
                    </div>
                </div>
            </div>
            <q-separator ></q-separator>
            <div class="q-my-sm column">
                <div>
                    <span class="text-bold">Union:</span> Creates a new polygon with the combined area of two or more selected polygons, boxes, 
                    or circles. *Note the new polygon will replace all selected shapes.
                    <template v-if="mapSettings.polyCount < 2">
                        <span class="text-red"> At least two features in the Shapes layer need to be selected to use this tool.</span>
                    </template> 
                </div>
                <div class="row justify-end q-gutter-sm">
                    <div class="self-center">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="createPolyUnion();" label="Create Union" :disabled="mapSettings.polyCount < 2" dense />
                    </div>
                </div>
            </div>
            <q-separator ></q-separator>
        </div>
    `,
    setup() {
        const bufferWidthValue = Vue.ref(null);
        const downloadTypeOptions = [
            {value: 'kml', label: 'KML'},
            {value: 'geojson', label: 'GeoJSON'}
        ];
        const layersObj = Vue.inject('layersObj');
        const mapProjection = new ol.proj.Projection({
            code: 'EPSG:3857'
        });
        const mapSettings = Vue.inject('mapSettings');
        const selectedDownloadType = Vue.ref(null);
        const selectInteraction = Vue.inject('selectInteraction');
        const wgs84Projection = new ol.proj.Projection({
            code: 'EPSG:4326',
            units: 'degrees'
        });

        const removeUserLayer = Vue.inject('removeUserLayer');
        const updateMapSettings = Vue.inject('updateMapSettings');
        const { showNotification } = useCore();

        function createBuffers() {
            if(!bufferWidthValue.value || Number(bufferWidthValue.value) === 0) {
                showNotification('negative','Please enter a number greater than zero for the Buffer Width.');
            }
            else{
                selectInteraction.value.getFeatures().forEach((feature) => {
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
                        else if(geoType === 'MultiPoint'){
                            turfFeature = turf.point(featCoords[0]);
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
                            groundRadius = groundRadius / 1000;
                            turfFeature = getWGS84CirclePoly(center,groundRadius);
                        }
                        const buffered = turf.buffer(turfFeature, bufferWidthValue.value, {units: 'kilometers'});
                        const buffpoly = geoJSONFormat.readFeature(buffered);
                        buffpoly.getGeometry().transform(wgs84Projection, mapProjection);
                        mapSettings.selectSource.addFeature(buffpoly);
                    }
                });
                bufferWidthValue.value = 0;
            }
        }

        function createPolyDifference() {
            const features = [];
            const geoJSONFormat = new ol.format.GeoJSON();
            selectInteraction.value.getFeatures().forEach((feature) => {
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
                        groundRadius = groundRadius / 1000;
                        features.push(getWGS84CirclePoly(center,groundRadius));
                    }
                }
            });
            const difference = turf.difference(features[0], features[1]);
            if(difference){
                const diffpoly = geoJSONFormat.readFeature(difference);
                diffpoly.getGeometry().transform(wgs84Projection, mapProjection);
                mapSettings.selectSource.addFeature(diffpoly);
            }
        }

        function createPolyIntersect() {
            const featuresOne = [];
            const featuresTwo = [];
            let pass = 1;
            let intersection;
            const geoJSONFormat = new ol.format.GeoJSON();
            selectInteraction.value.getFeatures().forEach((feature) => {
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
                            featCoords.forEach((coords) => {
                                if(pass === 1){
                                    featuresOne.push(turf.polygon(coords));
                                }
                                else{
                                    featuresTwo.push(turf.polygon(coords));
                                }
                            });
                        }
                        else if(geoType === 'Circle'){
                            const center = fixedselectgeometry.getCenter();
                            const radius = fixedselectgeometry.getRadius();
                            const edgeCoordinate = [center[0] + radius, center[1]];
                            let groundRadius = ol.sphere.getDistance(
                                ol.proj.transform(center, 'EPSG:4326', 'EPSG:4326'),
                                ol.proj.transform(edgeCoordinate, 'EPSG:4326', 'EPSG:4326')
                            );
                            groundRadius = groundRadius / 1000;
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
            featuresOne.forEach((feat1) => {
                featuresTwo.forEach((feat2) => {
                    const tempPoly = turf.intersect(feat1, feat2);
                    if(tempPoly){
                        if(intersection){
                            intersection = turf.union(intersection,tempPoly);
                        }
                        else{
                            intersection = tempPoly;
                        }
                    }
                });
            });
            if(intersection){
                const interpoly = geoJSONFormat.readFeature(intersection);
                interpoly.getGeometry().transform(wgs84Projection, mapProjection);
                mapSettings.selectSource.addFeature(interpoly);
            }
            else{
                showNotification('negative','The selected shapes do not intersect.');
            }
        }

        function createPolyUnion() {
            const features = [];
            const geoJSONFormat = new ol.format.GeoJSON();
            selectInteraction.value.getFeatures().forEach((feature) => {
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
                        groundRadius = groundRadius / 1000;
                        features.push(getWGS84CirclePoly(center, groundRadius));
                    }
                }
            });
            let union = turf.union(features[0], features[1]);
            features.forEach((feat, index) => {
                if(index > 1){
                    union = turf.union(union, feat);
                }
            });
            if(union){
                deleteSelections();
                const unionpoly = geoJSONFormat.readFeature(union);
                unionpoly.getGeometry().transform(wgs84Projection, mapProjection);
                mapSettings.selectSource.addFeature(unionpoly);
                updateMapSettings('activeLayer', 'select');
            }
        }

        function deleteSelections() {
            selectInteraction.value.getFeatures().forEach((feature) => {
                layersObj['select'].getSource().removeFeature(feature);
            });
            selectInteraction.value.getFeatures().clear();
            if(layersObj['select'].getSource().getFeatures().length < 1){
                removeUserLayer('select');
            }
        }

        function downloadShapesLayer() {
            let filetype;
            let format = null;
            if(!selectedDownloadType){
                showNotification('negative','Please select a Download Type.');
                return;
            }
            else if(selectedDownloadType.value.value === 'kml'){
                format = new ol.format.KML();
                filetype = 'application/vnd.google-earth.kml+xml';
            }
            else if(selectedDownloadType.value.value === 'geojson'){
                format = new ol.format.GeoJSON();
                filetype = 'application/vnd.geo+json';
            }
            if(format){
                const features = layersObj['select'].getSource().getFeatures();
                const fixedFeatures = setDownloadFeatures(features);
                let exportStr = format.writeFeatures(fixedFeatures, {
                    'dataProjection': wgs84Projection,
                    'featureProjection': mapProjection
                });
                if(selectedDownloadType.value.value === 'kml'){
                    exportStr = exportStr.replaceAll(/<kml xmlns="http:\/\/www.opengis.net\/kml\/2.2" xmlns:gx="http:\/\/www.google.com\/kml\/ext\/2.2" xmlns:xsi="http:\/\/www.w3.org\/2001\/XMLSchema-instance" xsi:schemaLocation="http:\/\/www.opengis.net\/kml\/2.2 https:\/\/developers.google.com\/kml\/schema\/kml22gx.xsd">/g,'<kml xmlns="http://www.opengis.net/kml/2.2"><Document id="root_doc"><Folder><name>shapes_export</name>');
                    exportStr = exportStr.replaceAll(/<Placemark>/g,'<Placemark><Style><LineStyle><color>ff000000</color><width>1</width></LineStyle><PolyStyle><color>4DAAAAAA</color><fill>1</fill></PolyStyle></Style>');
                    exportStr = exportStr.replaceAll(/<Polygon>/g,'<Polygon><altitudeMode>clampToGround</altitudeMode>');
                    exportStr = exportStr.replaceAll(/<\/kml>/g,'</Folder></Document></kml>');
                }
                const filename = 'shapes_' + getDateTimeString() + '.' + selectedDownloadType.value.value;
                const blob = new Blob([exportStr], {type: filetype});
                const elem = window.document.createElement('a');
                elem.href = window.URL.createObjectURL(blob);
                elem.download = filename;
                document.body.appendChild(elem);
                elem.click();
                document.body.removeChild(elem);
            }
        }

        function getWGS84CirclePoly(center, radius) {
            let turfFeature = '';
            const ciroptions = {steps: 200, units: 'kilometers'};
            turfFeature = turf.circle(center,radius,ciroptions);
            return turfFeature;
        }

        function processBufferWidthChange(val) {
            if(Number(val) < 0){
                bufferWidthValue.value = 0;
                showNotification('negative','Buffer Width must be greater than zero.');
            }
        }

        function setDownloadFeatures(features) {
            const fixedFeatures = [];
            features.forEach((feat) => {
                const clone = feat.clone();
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
                    groundRadius = groundRadius / 1000;
                    const turfCircle = getWGS84CirclePoly(center, groundRadius);
                    const circpoly = geoJSONFormat.readFeature(turfCircle);
                    circpoly.getGeometry().transform(wgs84Projection, mapProjection);
                    fixedFeatures.push(circpoly);
                }
                else{
                    fixedFeatures.push(clone);
                }
            });
            return fixedFeatures;
        }
        
        return {
            bufferWidthValue,
            downloadTypeOptions,
            mapSettings,
            selectedDownloadType,
            createBuffers,
            createPolyDifference,
            createPolyIntersect,
            createPolyUnion,
            deleteSelections,
            downloadShapesLayer,
            processBufferWidthChange
        }
    }
};

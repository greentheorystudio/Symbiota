const spatialAnalysisPopup = {
    props: {
        bottomLat: {
            type: Number,
            default: null
        },
        circleArr: {
            type: Array,
            default: null
        },
        coordinateUncertaintyInMeters: {
            type: Number,
            default: null
        },
        decimalLatitude: {
            type: Number,
            default: null
        },
        decimalLongitude: {
            type: Number,
            default: null
        },
        footprintWkt: {
            type: String,
            default: null
        },
        leftLong: {
            type: Number,
            default: null
        },
        pointLat: {
            type: Number,
            default: null
        },
        pointLong: {
            type: Number,
            default: null
        },
        polyArr: {
            type: Array,
            default: null
        },
        radius: {
            type: Number,
            default: null
        },
        radiusUnits: {
            type: String,
            default: null
        },
        rightLong: {
            type: Number,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        },
        upperLat: {
            type: Number,
            default: null
        },
        windowType: {
            type: String,
            default: ''
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <spatial-analysis-module ref="spatialModuleRef" :input-window-mode="true" :input-window-tools-arr="inputWindowToolsArr" @update:spatial-data="emitSpatialData" @close:spatial-popup="closePopup();"></spatial-analysis-module>
            </q-card>
        </q-dialog>
    `,
    components: {
        'spatial-analysis-module': spatialAnalysisModule
    },
    setup(props, context) {
        const inputWindowToolsArr = Vue.shallowReactive([]);
        const propsRefs = Vue.toRefs(props);
        const spatialModuleRef = Vue.ref(null);

        Vue.watch(propsRefs.bottomLat, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.circleArr, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.coordinateUncertaintyInMeters, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.decimalLatitude, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.decimalLongitude, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.footprintWkt, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.leftLong, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.pointLat, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.pointLong, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.polyArr, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.radius, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.radiusUnits, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.rightLong, () => {
            loadInputParentParams();
        });

        Vue.watch(propsRefs.upperLat, () => {
            loadInputParentParams();
        });

        Vue.watch(spatialModuleRef, () => {
            processWindowTypeInputTools();
            loadInputParentParams();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function emitSpatialData(data) {
            context.emit('update:spatial-data', data);
            closePopup();
        }

        function loadInputParentParams() {
            if(props.upperLat && inputWindowToolsArr.includes('box')){
                processInputParentBoxParams();
            }
            if((props.pointLat || props.radiusUnits) && inputWindowToolsArr.includes('circle')){
                processInputParentPointRadiusParams();
            }
            if(props.polyArr && inputWindowToolsArr.length === 0){
                processInputParentPolyArrParams();
            }
            if(props.circleArr && inputWindowToolsArr.length === 0){
                processInputParentCircleArrParams();
            }
            if((props.coordinateUncertaintyInMeters && inputWindowToolsArr.includes('uncertainty')) || (props.decimalLatitude && props.decimalLongitude && inputWindowToolsArr.includes('point'))){
                processInputParentPointParams();
            }
            if(props.footprintWkt && inputWindowToolsArr.includes('polygon') && inputWindowToolsArr.includes('wkt')){
                processInputParentPolyWKTParams();
            }
            spatialModuleRef.value.zoomToShapesLayer();
        }

        function processInputParentBoxParams() {
            const boundingBox = {};
            boundingBox.upperlat = props.upperLat;
            boundingBox.bottomlat = props.bottomLat;
            boundingBox.leftlong = props.leftLong;
            boundingBox.rightlong = props.rightLong;
            if(boundingBox.upperlat && boundingBox.bottomlat && boundingBox.leftlong && boundingBox.rightlong){
                spatialModuleRef.value.createPolygonFromBoundingBox(boundingBox, true);
            }
        }

        function processInputParentCircleArrParams() {
            if(Array.isArray(props.circleArr)){
                spatialModuleRef.value.createCirclesFromCircleArr(props.circleArr, true);
            }
        }

        function processInputParentPointParams() {
            let openerRadius = 0;
            if(props.coordinateUncertaintyInMeters && inputWindowToolsArr.includes('uncertainty')){
                openerRadius = props.coordinateUncertaintyInMeters;
                spatialModuleRef.value.updateMapSettings('uncertaintyRadiusValue', openerRadius);
            }
            if(props.decimalLatitude && props.decimalLongitude){
                if(openerRadius > 0){
                    const pointRadius = {};
                    pointRadius.pointlat = Number(props.decimalLatitude);
                    pointRadius.pointlong = Number(props.decimalLongitude);
                    pointRadius.radius = Number(openerRadius);
                    spatialModuleRef.value.createUncertaintyCircleFromPointRadius(pointRadius);
                }
                spatialModuleRef.value.createPointFromPointParams(props.decimalLatitude, props.decimalLongitude);
            }
        }

        function processInputParentPointRadiusParams() {
            const pointRadius = {};
            pointRadius.pointlat = props.pointLat;
            pointRadius.pointlong = props.pointLong;
            pointRadius.radius = props.radius;
            pointRadius.radiusunits = props.radiusUnits;
            if(pointRadius.radiusunits){
                spatialModuleRef.value.updateMapSettings('radiusUnits', pointRadius.radiusunits);
            }
            if(pointRadius.pointlat && pointRadius.pointlong && pointRadius.radius){
                spatialModuleRef.value.updateMapSettings('uncertaintyRadiusValue', pointRadius.radius);
                spatialModuleRef.value.createCircleFromPointRadius(pointRadius, true);
            }
        }

        function processInputParentPolyArrParams() {
            if(Array.isArray(props.polyArr)){
                spatialModuleRef.value.createPolysFromPolyArr(props.polyArr, true);
            }
        }

        function processInputParentPolyWKTParams() {
            if(props.footprintWkt && (props.footprintWkt.startsWith("POLYGON") || props.footprintWkt.startsWith("MULTIPOLYGON"))){
                spatialModuleRef.value.createPolysFromFootprintWKT(props.footprintWkt);
            }
        }

        function processWindowTypeInputTools() {
            if(props.windowType.startsWith('input')){
                if(props.windowType.includes('-')){
                    spatialModuleRef.value.updateMapSettings('submitButtonText', 'Submit Coordinates');
                    const windowTypeArr = props.windowType.split('-');
                    if(windowTypeArr.length > 0){
                        const windowToolsArr = windowTypeArr[1].split(',');
                        windowToolsArr.forEach((tool) => {
                            inputWindowToolsArr.push(tool);
                        });
                    }
                }
                else{
                    spatialModuleRef.value.updateMapSettings('submitButtonText', 'Submit Criteria');
                }
            }
            if(inputWindowToolsArr.includes('uncertainty')){
                spatialModuleRef.value.updateMapSettings('uncertaintyRadiusText', 'Coordinate uncertainty (m)');
            }
            else if(inputWindowToolsArr.includes('radius')){
                spatialModuleRef.value.updateMapSettings('uncertaintyRadiusText', 'Radius');
            }
        }

        return {
            inputWindowToolsArr,
            spatialModuleRef,
            closePopup,
            emitSpatialData
        }
    }
};

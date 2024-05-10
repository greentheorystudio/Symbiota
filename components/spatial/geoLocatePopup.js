const geoLocatePopup = {
    props: {
        country: {
            type: String,
            default: null
        },
        county: {
            type: String,
            default: null
        },
        locality: {
            type: String,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        },
        state: {
            type: String,
            default: null
        },
        verbatimCoordinates: {
            type: String,
            default: null
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="xl-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="iframeRef" class="fit overflow-auto">
                    <template v-if="geolocateUrl && iframeStyle">
                        <iframe :src="geolocateUrl" :style="iframeStyle"></iframe>
                    </template>
                </div>
            </q-card>
        </q-dialog>
    `,
    setup(props, context) {
        const geolocateUrl = Vue.ref(null);
        const iframeRef = Vue.ref(null);
        const iframeStyle = Vue.ref(null);
        const propsRefs = Vue.toRefs(props);
        const urlPrefix = '//www.geo-locate.org/web/WebGeoreflight.aspx?v=1&georef=run&tab=locality';
        const urlVars = Vue.ref('');

        Vue.watch(propsRefs.country, () => {
            buildUrl();
        });

        Vue.watch(propsRefs.county, () => {
            buildUrl();
        });

        Vue.watch(iframeRef, () => {
            setIframeStyle();
        });

        Vue.watch(propsRefs.locality, () => {
            buildUrl();
        });

        Vue.watch(propsRefs.state, () => {
            buildUrl();
        });

        Vue.watch(propsRefs.verbatimCoordinates, () => {
            buildUrl();
        });

        function buildUrl() {
            geolocateUrl.value = null;
            urlVars.value = '';
            if(props.country){
                urlVars.value += '&country=' + encodeURI(props.country);
            }
            if(props.state){
                urlVars.value += '&state=' + encodeURI(props.state);
            }
            if(props.county){
                urlVars.value += '&county=' + encodeURI(props.county);
            }
            if(props.locality && props.verbatimCoordinates){
                urlVars.value += '&locality=' + encodeURI(props.locality) + '; ' + encodeURI(props.verbatimCoordinates);
            }
            else if(props.locality && !props.verbatimCoordinates){
                urlVars.value += '&locality=' + encodeURI(props.locality);
            }
            else if(!props.locality && props.verbatimCoordinates){
                urlVars.value += '&locality=' + encodeURI(props.verbatimCoordinates);
            }
            if(urlVars.value !== ''){
                geolocateUrl.value = urlPrefix + urlVars.value;
            }
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function setIframeStyle() {
            iframeStyle.value = null;
            if(iframeRef.value){
                iframeStyle.value = 'height: ' + (iframeRef.value.clientHeight - 30) + 'px;width: ' + iframeRef.value.clientWidth + 'px;';
            }
        }

        function transferData(evt) {
            const returnData = {};
            const receivedDataArr = evt.data.split("|");
            returnData['decimalLatitude'] = receivedDataArr[0].toString() !== '' ? receivedDataArr[0] : null;
            returnData['decimalLongitude'] = receivedDataArr[1].toString() !== '' ? receivedDataArr[1] : null;
            returnData['coordinateUncertaintyInMeters'] = (receivedDataArr[2].toString() !== '' && receivedDataArr[2].toString() !== 'Unavailable') ? receivedDataArr[2] : null;
            returnData['footprintWkt'] = (receivedDataArr[3].toString() !== '' && receivedDataArr[3].toString() !== 'Unavailable') ? receivedDataArr[3] : null;
            context.emit('update:geolocate-data', returnData);
        }

        Vue.onMounted(() => {
            window.addEventListener('message', transferData);
            buildUrl();
        });
        
        return {
            geolocateUrl,
            iframeRef,
            iframeStyle,
            closePopup
        }
    }
};

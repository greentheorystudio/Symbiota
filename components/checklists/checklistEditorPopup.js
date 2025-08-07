const checklistEditorPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" v-if="!showSpatialPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <template v-if="Number(checklistId) > 0">
                            <q-tabs v-model="tab" content-class="bg-grey-3" active-bg-color="grey-4" align="justify">
                                <q-tab name="details" label="Info" no-caps></q-tab>
                                <q-tab name="admin" label="Admin" no-caps></q-tab>
                            </q-tabs>
                            <q-separator></q-separator>
                            <q-tab-panels v-model="tab" :style="tabStyle">
                                <q-tab-panel class="q-pa-none" name="details">
                                    <checklist-field-module @open:spatial-popup="openSpatialPopup" @close:popup="closePopup();"></checklist-field-module>
                                </q-tab-panel>
                                <q-tab-panel class="q-pa-none" name="admin">
                                    
                                </q-tab-panel>
                            </q-tab-panels>
                        </template>
                        <template v-else>
                            <checklist-field-module @open:spatial-popup="openSpatialPopup" @close:popup="closePopup();"></checklist-field-module>
                        </template>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <template v-if="showSpatialPopup">
            <spatial-analysis-popup
                :decimal-latitude="decimalLatitudeValue"
                :decimal-longitude="decimalLongitudeValue"
                :footprint-wkt="footprintWktValue"
                :show-popup="showSpatialPopup"
                :window-type="popupWindowType"
                @update:spatial-data="processSpatialData"
                @close:popup="closeSpatialPopup();"
            ></spatial-analysis-popup>
        </template>
    `,
    components: {
        'checklist-field-module': checklistFieldModule,
        'spatial-analysis-popup': spatialAnalysisPopup
    },
    setup(_, context) {
        const checklistStore = useChecklistStore();

        const checklistData = Vue.computed(() => checklistStore.getChecklistData);
        const checklistId = Vue.computed(() => checklistStore.getChecklistID);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const decimalLatitudeValue = Vue.ref(null);
        const decimalLongitudeValue = Vue.ref(null);
        const footprintWktValue = Vue.ref(null);
        const popupWindowType = Vue.ref(null);
        const showSpatialPopup = Vue.ref(false);
        const tab = Vue.ref('details');
        const tabStyle = Vue.ref(null);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function clearSpatialInputValues() {
            decimalLatitudeValue.value = null;
            decimalLongitudeValue.value = null;
            footprintWktValue.value = null;
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function closeSpatialPopup() {
            popupWindowType.value = null;
            showSpatialPopup.value = false;
            clearSpatialInputValues();
        }

        function openSpatialPopup(type) {
            setSpatialInputValues();
            popupWindowType.value = type;
            showSpatialPopup.value = true;
        }

        function processSpatialData(data) {
            if(popupWindowType.value.includes('point') && data.hasOwnProperty('decimalLatitude') && data.hasOwnProperty('decimalLongitude')){
                const latDecimalPlaces = (checklistData.value.hasOwnProperty('decimallatitude') && checklistData.value['decimallatitude']) ? checklistData.value['decimallatitude'].toString().split('.')[1].length : null;
                const longDecimalPlaces = (checklistData.value.hasOwnProperty('decimallongitude') && checklistData.value['decimallongitude']) ? checklistData.value['decimallongitude'].toString().split('.')[1].length : null;
                if(!latDecimalPlaces || Number(checklistData.value['decimallatitude']) !== Number(Number(data['decimalLatitude']).toFixed(latDecimalPlaces))){
                    checklistStore.updateChecklistEditData('latcentroid', data['decimalLatitude']);
                }
                if(!longDecimalPlaces || Number(checklistData.value['decimallongitude']) !== Number(Number(data['decimalLongitude']).toFixed(longDecimalPlaces))){
                    checklistStore.updateChecklistEditData('longcentroid', data['decimalLongitude']);
                }
            }
            else if(popupWindowType.value.includes('wkt') && data.hasOwnProperty('footprintWKT')){
                checklistStore.updateChecklistEditData('footprintwkt', data['footprintWKT']);
                if(data.hasOwnProperty('centroid')){
                    checklistStore.updateChecklistEditData('latcentroid', data['centroid']['decimalLatitude']);
                    checklistStore.updateChecklistEditData('longcentroid', data['centroid']['decimalLongitude']);
                }
            }
        }

        function setContentStyle() {
            contentStyle.value = null;
            tabStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
                tabStyle.value = 'height: ' + (contentRef.value.clientHeight - 90) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setSpatialInputValues() {
            decimalLatitudeValue.value = checklistData.value['latcentroid'];
            decimalLongitudeValue.value = checklistData.value['longcentroid'];
            footprintWktValue.value = checklistData.value['footprintwkt'];
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            checklistId,
            contentRef,
            contentStyle,
            decimalLatitudeValue,
            decimalLongitudeValue,
            footprintWktValue,
            popupWindowType,
            showSpatialPopup,
            tab,
            tabStyle,
            closePopup,
            closeSpatialPopup,
            openSpatialPopup,
            processSpatialData
        }
    }
};

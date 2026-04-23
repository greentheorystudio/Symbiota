const institutionsEditorPopup = {
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
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <template v-if="Number(institutionsId) > 0">
                            <institutions-field-module @close:popup="closePopup();"></institutions-field-module>
                            </q-tab-panels>
                        </template>
                        <template v-else>
                            <institutions-field-module @close:popup="closePopup();"></institutions-field-module>
                        </template>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'institutions-field-module': institutionsFieldModule,
    },
    setup(_, context) {
        const baseStore = useBaseStore();
        const appEnabled = baseStore.getAppEnabled;
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const popupWindowType = Vue.ref(null);
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

        function setContentStyle() {
            contentStyle.value = null;
            tabStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
                tabStyle.value = 'height: ' + (contentRef.value.clientHeight - 90) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setSpatialInputValues() {
            decimalLatitudeValue.value = institutionsData.value['latcentroid'];
            decimalLongitudeValue.value = institutionsData.value['longcentroid'];
            footprintWktValue.value = institutionsData.value['footprintwkt'];
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            appEnabled,
            contentRef,
            contentStyle,
            popupWindowType,
            tab,
            tabStyle,
            closePopup
        }
    }
};

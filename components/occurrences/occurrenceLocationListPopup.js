const occurrenceLocationListPopup = {
    props: {
        locationArr: {
            type: Array,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="md-square-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div v-if="locationArr.length" class="q-pa-md column q-gutter-md">
                            <q-card v-for="location in locationArr">
                                <q-card-section class="q-pa-md column">
                                    <div v-if="location.locationcode || location.locationname">
                                        <template v-if="location.locationcode">
                                            <span>
                                                {{ location.locationcode }}
                                            </span>
                                        </template>
                                        <template v-if="location.locationname">
                                            <span>
                                                {{ (location.locationcode ? ' - ' : '') + location.locationname }}
                                            </span>
                                        </template>
                                    </div>
                                    <div>
                                        <template v-if="location.country">
                                            <span>
                                                {{ location.country + '; ' }}
                                            </span>
                                        </template>
                                        <template v-if="location.stateprovince">
                                            <span>
                                                {{ location.stateprovince + '; ' }}
                                            </span>
                                        </template>
                                        <template v-if="location.county">
                                            <span>
                                                {{ location.county + '; ' }}
                                            </span>
                                        </template>
                                        <template v-if="location.locality">
                                            <span>
                                                {{ location.locality }}
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span>
                                                Locality data empty
                                            </span>
                                        </template>
                                    </div>
                                    <template v-if="location.decimallatitude || location.verbatimcoordinates">
                                        <div>
                                            <template v-if="location.decimallatitude">
                                                <span>
                                                    {{ location.decimallatitude + ', ' + location.decimallongitude }}
                                                </span>
                                                <span v-if="location.coordinateuncertaintyinmeters">
                                                    {{ ' +-' + location.coordinateuncertaintyinmeters + 'm.' }}
                                                </span>
                                                <span v-if="location.geodeticdatum">
                                                    {{ ' (' + location.geodeticdatum + ')' }}
                                                </span>
                                            </template>
                                            <template v-if="location.verbatimcoordinates">
                                                <span :class="location.decimallatitude ? 'q-ml-md' : ''">
                                                    {{ location.verbatimcoordinates }}
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                    <div v-if="location.locationremarks">
                                        <span>
                                            {{ location.locationremarks }}
                                        </span>
                                    </div>
                                    <div v-if="location.minimumelevationinmeters || location.maximumelevationinmeters || location.verbatimelevation">
                                        <template v-if="location.minimumelevationinmeters">
                                            <span>
                                                {{ location.minimumelevationinmeters + (location.maximumelevationinmeters ? ('-' + location.maximumelevationinmeters) : '') + ' meters' }}
                                            </span>
                                        </template>
                                        <template v-if="location.verbatimelevation">
                                            <span>
                                                {{ (location.minimumelevationinmeters ? '; ' : '') + 'Verbatim elevation: ' + location.verbatimelevation }}
                                            </span>
                                        </template>
                                    </div>
                                    <div class="q-mt-md q-pl-md row justify-start q-gutter-md">
                                        <q-btn color="primary" @click="processLocationSelection(location.locationid);" label="Select Location" dense />
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    setup(props, context) {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        
        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function processLocationSelection(locationid) {
            occurrenceStore.setCurrentLocationRecord(locationid);
            context.emit('close:popup');
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
        });

        return {
            contentRef,
            contentStyle,
            closePopup,
            processLocationSelection
        }
    }
};

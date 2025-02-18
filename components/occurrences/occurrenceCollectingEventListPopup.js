const occurrenceCollectingEventListPopup = {
    props: {
        eventArr: {
            type: Array,
            default: null
        },
        popupType: {
            type: String,
            default: 'occurrence'
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
                        <div v-if="eventArr.length" class="q-pa-md column q-gutter-md">
                            <q-card v-for="event in eventArr">
                                <q-card-section class="q-pa-md column">
                                    <div>
                                        <span>
                                            {{ event.recordedby ? event.recordedby : 'Collector/Observer field empty' }}
                                        </span>
                                        <template v-if="event.recordnumber">
                                            <span class="q-ml-xl">
                                                {{ event.recordnumber }}
                                            </span>
                                        </template>
                                        <template v-if="event.eventdate">
                                            <span class="q-ml-xl">
                                                {{ event.eventdate }}
                                            </span>
                                        </template>
                                        <template v-else-if="event.verbatimeventdate">
                                            <span class="q-ml-xl">
                                                {{ event.verbatimeventdate }}
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span class="q-ml-xl">
                                                Date field empty
                                            </span>
                                        </template>
                                        <template v-if="event.eventtime">
                                            <span class="q-ml-xl">
                                                {{ event.eventtime }}
                                            </span>
                                        </template>
                                        <template v-if="event.associatedcollectors">
                                            <span class="q-ml-lg">
                                                Assoc. Collectors: {{ event.associatedcollectors }}
                                            </span>
                                        </template>
                                    </div>
                                    <div v-if="event.eventtype || event.eventremarks || event.repcount">
                                        <template v-if="event.eventtype">
                                            <span>
                                                {{ event.eventtype }}
                                            </span>
                                        </template>
                                        <template v-if="event.repcount">
                                            <span>
                                                {{ (event.eventtype ? '; ' : '') + event.repcount + ' Reps' }}
                                            </span>
                                        </template>
                                        <template v-if="event.eventremarks">
                                            <span>
                                                {{ ((event.eventtype || event.repcount) ? '; ' : '') + event.eventremarks }}
                                            </span>
                                        </template>
                                    </div>
                                    <div v-if="event.minimumdepthinmeters || event.maximumdepthinmeters || event.verbatimdepth">
                                        <template v-if="event.minimumdepthinmeters">
                                            <span>
                                                {{ event.minimumdepthinmeters + (event.maximumdepthinmeters ? ('-' + event.maximumdepthinmeters) : '') + ' meters' }}
                                            </span>
                                        </template>
                                        <template v-if="event.verbatimdepth">
                                            <span>
                                                {{ (event.minimumdepthinmeters ? '; ' : '') + 'Verbatim depth: ' + event.verbatimdepth }}
                                            </span>
                                        </template>
                                    </div>
                                    <div v-if="event.habitat">
                                        <span>
                                            {{ event.habitat }}
                                        </span>
                                    </div>
                                    <div v-if="event.substrate">
                                        <span>
                                            {{ event.substrate }}
                                        </span>
                                    </div>
                                    <div v-if="event.fieldnumber || event.fieldnotes">
                                        <template v-if="event.fieldnumber">
                                            <span>
                                                {{ event.fieldnumber }}
                                            </span>
                                        </template>
                                        <template v-if="event.fieldnotes">
                                            <span>
                                                {{ (event.fieldnumber ? '; ' : '') + event.fieldnotes }}
                                            </span>
                                        </template>
                                    </div>
                                    <div v-if="popupType === 'occurrence'">
                                        <template v-if="event.country">
                                            <span>
                                                {{ event.country + '; ' }}
                                            </span>
                                        </template>
                                        <template v-if="event.stateprovince">
                                            <span>
                                                {{ event.stateprovince + '; ' }}
                                            </span>
                                        </template>
                                        <template v-if="event.county">
                                            <span>
                                                {{ event.county + '; ' }}
                                            </span>
                                        </template>
                                        <template v-if="event.locality">
                                            <span>
                                                {{ event.locality }}
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span>
                                                Locality data empty
                                            </span>
                                        </template>
                                    </div>
                                    <template v-if="event.decimallatitude || event.verbatimcoordinates">
                                        <div>
                                            <template v-if="event.decimallatitude">
                                                <span>
                                                    {{ event.decimallatitude + ', ' + event.decimallongitude }}
                                                </span>
                                                <span v-if="event.coordinateuncertaintyinmeters">
                                                    {{ ' +-' + event.coordinateuncertaintyinmeters + 'm.' }}
                                                </span>
                                                <span v-if="event.geodeticdatum">
                                                    {{ ' (' + event.geodeticdatum + ')' }}
                                                </span>
                                            </template>
                                            <template v-if="event.verbatimcoordinates">
                                                <span :class="event.decimallatitude ? 'q-ml-md' : ''">
                                                    {{ event.verbatimcoordinates }}
                                                </span>
                                            </template>
                                        </div>
                                        <div v-if="event.georeferenceprotocol || event.georeferencesources || event.georeferenceremarks">
                                            <template v-if="event.georeferenceprotocol">
                                                <span>
                                                    {{ event.georeferenceprotocol }}
                                                </span>
                                            </template>
                                            <template v-if="event.georeferencesources">
                                                <span>
                                                    {{ (event.georeferenceprotocol ? '; ' : '') + event.georeferencesources }}
                                                </span>
                                            </template>
                                            <template v-if="event.georeferenceremarks">
                                                <span>
                                                    {{ ((event.georeferenceprotocol || event.georeferencesources) ? '; ' : '') + event.georeferenceremarks }}
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                    <div v-if="event.minimumelevationinmeters || event.maximumelevationinmeters || event.verbatimelevation">
                                        <template v-if="event.minimumelevationinmeters">
                                            <span>
                                                {{ event.minimumelevationinmeters + (event.maximumelevationinmeters ? ('-' + event.maximumelevationinmeters) : '') + ' meters' }}
                                            </span>
                                        </template>
                                        <template v-if="event.verbatimelevation">
                                            <span>
                                                {{ (event.minimumelevationinmeters ? '; ' : '') + 'Verbatim elevation: ' + event.verbatimelevation }}
                                            </span>
                                        </template>
                                    </div>
                                    <div class="q-mt-md q-pl-md row justify-start q-gutter-md">
                                        <template v-if="popupType === 'occurrence'">
                                            <q-btn color="primary" @click="processMergeEventData(event, false);" label="Merge All Data" dense />
                                            <q-btn color="primary" @click="processMergeEventData(event);" label="Merge Missing Data Only" dense />
                                        </template>
                                        <template v-else-if="popupType === 'location'">
                                            <q-btn color="primary" @click="processEventSelection(event);" label="Select Event" dense />
                                        </template>
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
        const occurrenceStore = useOccurrenceStore();

        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        
        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function processEventSelection(event) {
            context.emit('update:event', event);
            context.emit('close:popup');
        }

        function processMergeEventData(data, missingOnly = true) {
            context.emit('merge:event', {event: data, missing: !missingOnly});
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
            window.addEventListener('resize', setContentStyle);
        });

        return {
            contentRef,
            contentStyle,
            closePopup,
            processEventSelection,
            processMergeEventData
        }
    }
};

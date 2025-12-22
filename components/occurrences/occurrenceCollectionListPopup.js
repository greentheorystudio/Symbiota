const occurrenceCollectionListPopup = {
    props: {
        collectionArr: {
            type: Array,
            default: null
        },
        duplicateDisplay: {
            type: Boolean,
            default: false
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
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div v-if="duplicateDisplay" class="q-pa-md text-body1">
                            The record(s) below have the same identifier you just entered. Click on the edit icon for any record 
                            to view it in a separate editor window.
                        </div>
                        <div v-if="collectionArr.length" class="q-pa-md column q-gutter-md">
                            <q-card v-for="collection in collectionArr">
                                <q-card-section class="q-pa-md column">
                                    <div v-if="duplicateDisplay" class="q-mb-xs row justify-end">
                                        <q-btn role="link" color="grey-4" text-color="black" class="black-border" size="sm" :href="(clientRoot + '/collections/editor/occurrenceeditor.php?occid=' + collection.occid + '&collid=' + collection.collid)" target="_blank" icon="fas fa-edit" dense aria-label="View in occurrence editor - Opens in separate tab" tabindex="0">
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                View in occurrence editor
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                    <div v-if="collection.catalognumber || collection.othercatalognumbers">
                                        <template v-if="collection.catalognumber">
                                            <span class="text-bold">{{ 'catalognumber: ' }}</span>
                                            {{ collection.catalognumber + '; ' }}
                                        </template>
                                        <template v-if="collection.othercatalognumbers">
                                            <span class="text-bold">{{ 'othercatalognumbers: ' }}</span>
                                            {{ collection.othercatalognumbers + '; ' }}
                                        </template>
                                    </div>
                                    <div v-if="duplicateDisplay">
                                        <span>
                                            {{ collection.recordedby ? collection.recordedby : 'Collector/Observer field empty' }}
                                        </span>
                                        <template v-if="collection.recordnumber">
                                            <span class="q-ml-xl">
                                                {{ collection.recordnumber }}
                                            </span>
                                        </template>
                                        <template v-if="collection.eventdate">
                                            <span class="q-ml-xl">
                                                {{ collection.eventdate }}
                                            </span>
                                        </template>
                                        <template v-else-if="collection.verbatimeventdate">
                                            <span class="q-ml-xl">
                                                {{ collection.verbatimeventdate }}
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span class="q-ml-xl">
                                                Date field empty
                                            </span>
                                        </template>
                                        <template v-if="collection.eventtime">
                                            <span class="q-ml-xl">
                                                {{ collection.eventtime }}
                                            </span>
                                        </template>
                                        <template v-if="collection.associatedcollectors">
                                            <span class="q-ml-lg">
                                                Assoc. Collectors: {{ collection.associatedcollectors }}
                                            </span>
                                        </template>
                                    </div>
                                    <div>
                                        <template v-if="collection.identificationqualifier">
                                            <span>
                                                {{ collection.identificationqualifier + ' ' }}
                                            </span>
                                        </template>
                                        <span class="text-italic">
                                            {{ collection.sciname }}
                                        </span>
                                        <template v-if="collection.family">
                                            <span class="q-ml-xl">
                                                {{ collection.family }}
                                            </span>
                                        </template>
                                    </div>
                                    <div v-if="collection.identificationremarks">
                                        <span class="text-bold">{{ 'identificationremarks: ' }}</span>
                                        {{ collection.identificationremarks }}
                                    </div>
                                    <div v-if="duplicateDisplay && (collection.minimumdepthinmeters || collection.maximumdepthinmeters || collection.verbatimdepth)">
                                        <template v-if="collection.minimumdepthinmeters">
                                            <span>
                                                {{ collection.minimumdepthinmeters + (collection.maximumdepthinmeters ? ('-' + collection.maximumdepthinmeters) : '') + ' meters' }}
                                            </span>
                                        </template>
                                        <template v-if="collection.verbatimdepth">
                                            <span>
                                                {{ (collection.minimumdepthinmeters ? '; ' : '') + 'Verbatim depth: ' + collection.verbatimdepth }}
                                            </span>
                                        </template>
                                    </div>
                                    <div v-if="duplicateDisplay">
                                        <template v-if="collection.country">
                                            <span>
                                                {{ collection.country + '; ' }}
                                            </span>
                                        </template>
                                        <template v-if="collection.stateprovince">
                                            <span>
                                                {{ collection.stateprovince + '; ' }}
                                            </span>
                                        </template>
                                        <template v-if="collection.county">
                                            <span>
                                                {{ collection.county + '; ' }}
                                            </span>
                                        </template>
                                        <template v-if="collection.locality">
                                            <span>
                                                {{ collection.locality }}
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span>
                                                Locality data empty
                                            </span>
                                        </template>
                                    </div>
                                    <template v-if="duplicateDisplay && (collection.decimallatitude || collection.verbatimcoordinates)">
                                        <div>
                                            <template v-if="collection.decimallatitude">
                                                <span>
                                                    {{ collection.decimallatitude + ', ' + collection.decimallongitude }}
                                                </span>
                                                <span v-if="collection.coordinateuncertaintyinmeters">
                                                    {{ ' +-' + collection.coordinateuncertaintyinmeters + 'm.' }}
                                                </span>
                                                <span v-if="collection.geodeticdatum">
                                                    {{ ' (' + collection.geodeticdatum + ')' }}
                                                </span>
                                            </template>
                                            <template v-if="collection.verbatimcoordinates">
                                                <span :class="collection.decimallatitude ? 'q-ml-md' : ''">
                                                    {{ collection.verbatimcoordinates }}
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                    <div v-if="duplicateDisplay && (collection.minimumelevationinmeters || collection.maximumelevationinmeters || collection.verbatimelevation)">
                                        <template v-if="collection.minimumelevationinmeters">
                                            <span>
                                                {{ collection.minimumelevationinmeters + (collection.maximumelevationinmeters ? ('-' + collection.maximumelevationinmeters) : '') + ' meters' }}
                                            </span>
                                        </template>
                                        <template v-if="collection.verbatimelevation">
                                            <span>
                                                {{ (collection.minimumelevationinmeters ? '; ' : '') + 'Verbatim elevation: ' + collection.verbatimelevation }}
                                            </span>
                                        </template>
                                    </div>
                                    <div v-if="!duplicateDisplay && (collection.individualcount || collection.lifestage || collection.sex)">
                                        <template v-if="collection.individualcount">
                                            <span class="text-bold">{{ 'individualcount: ' }}</span>
                                            {{ collection.individualcount + '; ' }}
                                        </template>
                                        <template v-if="collection.lifestage">
                                            <span class="text-bold">{{ 'lifestage: ' }}</span>
                                            {{ collection.lifestage + '; ' }}
                                        </template>
                                        <template v-if="collection.sex">
                                            <span class="text-bold">{{ 'sex: ' }}</span>
                                            {{ collection.sex + '; ' }}
                                        </template>
                                    </div>
                                    <div v-if="!duplicateDisplay && collection.associatedtaxa">
                                        <span class="text-bold">{{ 'associatedtaxa: ' }}</span>
                                        {{ collection.associatedtaxa }}
                                    </div>
                                    <div v-if="!duplicateDisplay && collection.typestatus">
                                        <span class="text-bold">{{ 'typestatus: ' }}</span>
                                        {{ collection.typestatus }}
                                    </div>
                                    <div v-if="!duplicateDisplay && collection.occurrenceremarks">
                                        <span class="text-bold">{{ 'occurrenceremarks: ' }}</span>
                                        {{ collection.occurrenceremarks }}
                                    </div>
                                    <div v-if="!duplicateDisplay && collection.reproductivecondition">
                                        <span class="text-bold">{{ 'reproductivecondition: ' }}</span>
                                        {{ collection.reproductivecondition }}
                                    </div>
                                    <div v-if="!duplicateDisplay && collection.establishmentmeans">
                                        <span class="text-bold">{{ 'establishmentmeans: ' }}</span>
                                        {{ collection.establishmentmeans }}
                                    </div>
                                    <div v-if="!duplicateDisplay && collection.dynamicproperties">
                                        <span class="text-bold">{{ 'dynamicproperties: ' }}</span>
                                        {{ collection.dynamicproperties }}
                                    </div>
                                    <div v-if="!duplicateDisplay && collection.verbatimattributes">
                                        <span class="text-bold">{{ 'verbatimattributes: ' }}</span>
                                        {{ collection.verbatimattributes }}
                                    </div>
                                    <div v-if="collection.basisofrecord">
                                        <span class="text-bold">{{ 'basisofrecord: ' }}</span>
                                        {{ collection.basisofrecord }}
                                    </div>
                                    <div v-if="!duplicateDisplay" class="q-mt-md q-pl-md row justify-start q-gutter-md">
                                        <q-btn color="primary" @click="processCollectionSelection(collection.occid);" label="Select Collection" dense tabindex="0" />
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
        const baseStore = useBaseStore();
        const occurrenceStore = useOccurrenceStore();

        const clientRoot = baseStore.getClientRoot;
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        
        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function processCollectionSelection(occid) {
            occurrenceStore.setCurrentOccurrenceRecord(occid);
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
            clientRoot,
            contentRef,
            contentStyle,
            closePopup,
            processCollectionSelection
        }
    }
};

const occurrenceCollectionListPopup = {
    props: {
        collectionArr: {
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
                        <div v-if="collectionArr.length" class="q-pa-md column q-gutter-md">
                            <q-card v-for="collection in collectionArr">
                                <q-card-section class="q-pa-md column">
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
                                    <div v-if="collection.individualcount || collection.lifestage || collection.sex">
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
                                    <div v-if="collection.associatedtaxa">
                                        <span class="text-bold">{{ 'associatedtaxa: ' }}</span>
                                        {{ collection.associatedtaxa }}
                                    </div>
                                    <div v-if="collection.typestatus">
                                        <span class="text-bold">{{ 'typestatus: ' }}</span>
                                        {{ collection.typestatus }}
                                    </div>
                                    <div v-if="collection.occurrenceremarks">
                                        <span class="text-bold">{{ 'occurrenceremarks: ' }}</span>
                                        {{ collection.occurrenceremarks }}
                                    </div>
                                    <div v-if="collection.reproductivecondition">
                                        <span class="text-bold">{{ 'reproductivecondition: ' }}</span>
                                        {{ collection.reproductivecondition }}
                                    </div>
                                    <div v-if="collection.establishmentmeans">
                                        <span class="text-bold">{{ 'establishmentmeans: ' }}</span>
                                        {{ collection.establishmentmeans }}
                                    </div>
                                    <div v-if="collection.dynamicproperties">
                                        <span class="text-bold">{{ 'dynamicproperties: ' }}</span>
                                        {{ collection.dynamicproperties }}
                                    </div>
                                    <div v-if="collection.verbatimattributes">
                                        <span class="text-bold">{{ 'verbatimattributes: ' }}</span>
                                        {{ collection.verbatimattributes }}
                                    </div>
                                    <div v-if="collection.basisofrecord">
                                        <span class="text-bold">{{ 'basisofrecord: ' }}</span>
                                        {{ collection.basisofrecord }}
                                    </div>
                                    <div class="q-mt-md q-pl-md row justify-start q-gutter-md">
                                        <q-btn color="primary" @click="processCollectionSelection(collection.occid);" label="Select Collection" dense />
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
            contentRef,
            contentStyle,
            closePopup,
            processCollectionSelection
        }
    }
};

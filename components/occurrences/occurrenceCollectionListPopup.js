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
                                        <span>
                                            {{ collection.identificationremarks }}
                                        </span>
                                    </div>
                                    <div v-if="collection.individualcount || collection.lifestage || collection.sex">
                                        <template v-if="collection.individualcount">
                                            <span>
                                                {{ collection.individualcount }}
                                            </span>
                                        </template>
                                        <template v-if="collection.lifestage">
                                            <span>
                                                {{ (collection.individualcount ? '; ' : '') + collection.lifestage }}
                                            </span>
                                        </template>
                                        <template v-if="collection.sex">
                                            <span>
                                                {{ ((collection.individualcount || collection.lifestage) ? '; ' : '') + collection.sex }}
                                            </span>
                                        </template>
                                    </div>
                                    <div v-if="collection.associatedtaxa">
                                        <span>
                                            {{ collection.associatedtaxa }}
                                        </span>
                                    </div>
                                    <div v-if="collection.typestatus">
                                        <span>
                                            {{ collection.typestatus }}
                                        </span>
                                    </div>
                                    <div v-if="collection.occurrenceremarks">
                                        <span>
                                            {{ collection.occurrenceremarks }}
                                        </span>
                                    </div>
                                    <div v-if="collection.reproductivecondition">
                                        <span>
                                            {{ collection.reproductivecondition }}
                                        </span>
                                    </div>
                                    <div v-if="collection.establishmentmeans">
                                        <span>
                                            {{ collection.establishmentmeans }}
                                        </span>
                                    </div>
                                    <div v-if="collection.dynamicproperties">
                                        <span>
                                            {{ collection.dynamicproperties }}
                                        </span>
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

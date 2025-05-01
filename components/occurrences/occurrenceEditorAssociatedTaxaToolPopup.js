const occurrenceEditorAssociatedTaxaToolPopup = {
    props: {
        associatedTaxaValue: {
            type: String,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    components: {
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
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
                    <div :style="contentStyle" class="overflow-auto q-pa-md">
                        <q-card flat bordered class="fit">
                            <q-card-section class="column q-col-gutter-sm">
                                <div class="text-body1">
                                    Enter scientific names into the Scientific Name box to enable auto-completetion (for taxa 
                                    in the Taxonomic Thesaurus only). Then click the Add Taxon button to append scientific names 
                                    to the Associated Taxa value. Once all of the associated taxa have been added, click the close 
                                    button in the top right corner of this box to return to the occurrence editor.
                                </div>
                                <div class="row justify-between q-col-gutter-lg">
                                    <div class="col-12 col-sm-9 self-center">
                                        <single-scientific-common-name-auto-complete :sciname="scientificNameStr" label="Scientific Name" @update:sciname="updateScientificNameValue"></single-scientific-common-name-auto-complete>
                                    </div>
                                    <div class="col-12 col-sm-3 self-center">
                                        <div><q-btn color="primary" @click="addTaxon();" label="Add Taxon" dense /></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-grow">
                                        <q-input outlined v-model="associatedTaxaStr" label="Associated Taxa" @update:model-value="processStrValueChange" autogrow dense></q-input>
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    setup(props, context) {
        const occurrenceStore = useOccurrenceStore();

        const associatedTaxaStr = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const propsRefs = Vue.toRefs(props);
        const scientificNameStr = Vue.ref(null);
        
        Vue.watch(propsRefs.associatedTaxaValue, () => {
            if(associatedTaxaStr.value !== props.associatedTaxaValue){
                associatedTaxaStr.value = props.associatedTaxaValue;
            }
        });

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addTaxon() {
            if(scientificNameStr.value){
                let tempValue = associatedTaxaStr.value ? associatedTaxaStr.value : '';
                if(tempValue.length > 0){
                    tempValue += ', ';
                }
                tempValue += scientificNameStr.value;
                occurrenceStore.updateOccurrenceEditData('associatedtaxa', tempValue);
                scientificNameStr.value = null;
            }
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function processStrValueChange(value) {
            occurrenceStore.updateOccurrenceEditData('associatedtaxa', value);
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function updateScientificNameValue(taxon) {
            if(taxon){
                scientificNameStr.value = taxon.sciname;
            }
            else{
                scientificNameStr.value = null
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            associatedTaxaStr.value = props.associatedTaxaValue;
        });

        return {
            associatedTaxaStr,
            contentRef,
            contentStyle,
            scientificNameStr,
            addTaxon,
            closePopup,
            processStrValueChange,
            updateScientificNameValue
        }
    }
};

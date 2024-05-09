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
            <q-card class="md-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div class="fit">
                    <div class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-md">
                            <div class="q-mt-xl row">
                                <div class="col-grow">
                                    <q-input outlined v-model="associatedTaxaStr" label="Associated Taxa" @update:model-value="processStrValueChange" autogrow dense></q-input>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-lg">
                                <div class="col-12 col-sm-9 self-center">
                                    <single-scientific-common-name-auto-complete :sciname="scientificNameStr" label="Scientific Name" @update:sciname="updateScientificNameValue"></single-scientific-common-name-auto-complete>
                                </div>
                                <div class="col-12 col-sm-3 self-center">
                                    <div><q-btn color="primary" @click="addTaxon();" label="Add Taxon" dense /></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    setup(props, context) {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const associatedTaxaStr = Vue.ref(null);
        const propsRefs = Vue.toRefs(props);
        const scientificNameStr = Vue.ref(null);
        
        Vue.watch(propsRefs.associatedTaxaValue, () => {
            if(associatedTaxaStr.value !== props.associatedTaxaValue){
                associatedTaxaStr.value = props.associatedTaxaValue;
            }
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

        function updateScientificNameValue(taxon) {
            if(taxon){
                scientificNameStr.value = taxon.sciname;
            }
            else{
                scientificNameStr.value = null
            }
        }

        Vue.onMounted(() => {
            associatedTaxaStr.value = props.associatedTaxaValue;
        });

        return {
            associatedTaxaStr,
            scientificNameStr,
            addTaxon,
            closePopup,
            processStrValueChange,
            updateScientificNameValue
        }
    }
};

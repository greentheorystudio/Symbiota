const taxonEditorStatusTab = {
    template: `
        <div class="column q-gutter-md">
            <q-card flat bordered>
                <q-card-section class="column">
                    <div><span class="text-subtitle1 text-bold">Status: <span class="text-red">{{ (isAccepted ? 'Accepted' : 'Not Accepted') }}</span></span></div>
                    <div><span class="text-subtitle1 text-bold">Family: </span>{{ (isAccepted ? taxon['family'] : acceptedTaxon['family']) }}</div>
                    <div v-if="Number(taxon['rankid']) > 10" class="row justify-start no-wrap">
                        <div class="col-4">
                            <single-scientific-common-name-auto-complete :sciname="parentTaxonVal" label="Parent Taxon" :limit-to-options="true" :disabled="!isAccepted" @update:sciname="processParentTaxonNameChange"></single-scientific-common-name-auto-complete>
                        </div>
                        <div v-if="isAccepted" class="q-ml-sm">
                            <div>
                                <q-btn color="primary" @click="processParentTaxonChange();" label="Change Parent Taxon" :disabled="!parentTaxonChangeValid" tabindex="0" />
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <q-card v-if="synonymArr.length > 0" flat bordered>
                <q-card-section class="column q-gutter-sm">
                    <div class="text-subtitle1 text-bold">Synonyms Of This Taxon</div>
                    <div class="q-mt-xs q-pl-sm column q-gutter-xs">
                        <template v-for="synonym in synonymArr">
                            <div class="row no-wrap">
                                <div class="text-subtitle1 text-italic">{{ synonym['sciname'] }}</div>
                                <div class="q-ml-sm">
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="setTaxonData(synonym['tid']);" icon="fas fa-edit" dense aria-label="Edit taxon" tabindex="0">
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Edit taxon
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                            </div>
                        </template>
                    </div>
                </q-card-section>
            </q-card>
            <q-card flat bordered>
                <q-card-section class="column q-gutter-sm">
                    <div class="text-subtitle1 text-bold">Change Acceptance</div>
                    <div class="row">
                        <div class="col-grow">
                            <single-scientific-common-name-auto-complete :sciname="acceptedTaxonVal" label="Accepted Taxon" :limit-to-options="true" @update:sciname="processAcceptedTaxonNameChange"></single-scientific-common-name-auto-complete>
                        </div>
                    </div>
                    <div class="row justify-end q-gutter-sm">
                        <div>
                            <q-btn color="primary" @click="processAcceptedTaxonChange();" label="Change Accepted Taxon" :disabled="!acceptedTaxonChangeValid" tabindex="0" />
                        </div>
                        <div v-if="!isAccepted">
                            <q-btn color="primary" @click="processMakeAccepted();" label="Change Status to Accepted" tabindex="0" />
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
    `,
    components: {
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
    },
    setup() {
        const { showNotification } = useCore();
        const taxaStore = useTaxaStore();

        const acceptedTaxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);
        const acceptedTaxonChangeValid = Vue.computed(() => {
            return (Number(acceptedTaxonTid.value) > 0 && Number(taxon.value['tidaccepted']) !== Number(acceptedTaxonTid.value));
        });
        const acceptedTaxonKingdomId = Vue.ref(null);
        const acceptedTaxonTid = Vue.ref(null);
        const acceptedTaxonVal = Vue.ref(null);
        const isAccepted = Vue.computed(() => taxaStore.getAccepted);
        const origAcceptedTaxonKingdomId = Vue.ref(null);
        const origParentTaxonKingdomId = Vue.ref(null);
        const parentTaxonChangeValid = Vue.computed(() => {
            return (Number(parentTaxonTid.value) > 0 && Number(taxon.value['parentTaxon']['tid']) !== Number(parentTaxonTid.value));
        });
        const parentTaxonKingdomId = Vue.ref(null);
        const parentTaxonTid = Vue.ref(null);
        const parentTaxonVal = Vue.ref(null);
        const synonymArr = Vue.computed(() => taxaStore.getTaxaSynonyms);
        const taxon = Vue.computed(() => taxaStore.getTaxaData);

        function processAcceptedTaxonChange() {

        }

        function processAcceptedTaxonNameChange(taxonData) {
            if(taxonData){
                acceptedTaxonVal.value = taxonData['sciname'];
                acceptedTaxonTid.value = taxonData['tid'];
                acceptedTaxonKingdomId.value = taxonData['kingdomid'];
            }
            else{
                acceptedTaxonVal.value = null;
                acceptedTaxonTid.value = null;
                acceptedTaxonKingdomId.value = null;
            }
            if(acceptedTaxonKingdomId.value && Number(acceptedTaxonKingdomId.value) !== Number(origAcceptedTaxonKingdomId.value)) {
                showNotification('negative', 'The Accepted Taxon you entered is in a different kingdom than the current taxon. Please ensure it is correct.');
            }
        }

        function processMakeAccepted() {

        }

        function processParentTaxonChange() {

        }

        function processParentTaxonNameChange(taxonData) {
            if(taxonData){
                parentTaxonVal.value = taxonData['sciname'];
                parentTaxonTid.value = taxonData['tid'];
                parentTaxonKingdomId.value = taxonData['kingdomid'];
            }
            else{
                parentTaxonVal.value = null;
                parentTaxonTid.value = null;
                parentTaxonKingdomId.value = null;
            }
            if(parentTaxonKingdomId.value && Number(parentTaxonKingdomId.value) !== Number(origParentTaxonKingdomId.value)) {
                showNotification('negative', 'The Parent Taxon you entered is in a different kingdom than the current taxon. Please ensure it is correct.');
            }
        }

        function setTaxonData(tid) {
            taxaStore.setTaxon(tid, true);
        }

        function setTaxonVals() {
            if(Number(taxon.value['tid']) > 0){
                if(isAccepted.value){
                    acceptedTaxonTid.value = taxon.value['tid'];
                    acceptedTaxonKingdomId.value = taxon.value['kingdomid'];
                    origAcceptedTaxonKingdomId.value = taxon.value['kingdomid'];
                    if(Number(taxon.value['rankid']) > 10){
                        parentTaxonVal.value = taxon.value['parentTaxon']['sciname'];
                        parentTaxonTid.value = taxon.value['parentTaxon']['tid'];
                        parentTaxonKingdomId.value = taxon.value['parentTaxon']['kingdomid'];
                        origParentTaxonKingdomId.value = taxon.value['parentTaxon']['kingdomid'];
                    }
                }
                else{
                    acceptedTaxonVal.value = taxon.value['acceptedTaxon']['sciname'];
                    acceptedTaxonTid.value = taxon.value['acceptedTaxon']['tid'];
                    acceptedTaxonKingdomId.value = taxon.value['acceptedTaxon']['kingdomid'];
                    origAcceptedTaxonKingdomId.value = taxon.value['acceptedTaxon']['kingdomid'];
                    if(Number(taxon.value['acceptedTaxon']['rankid']) > 10){
                        parentTaxonVal.value = taxon.value['acceptedTaxon']['parentTaxon']['sciname'];
                        parentTaxonTid.value = taxon.value['acceptedTaxon']['parentTaxon']['tid'];
                    }
                }
            }
        }

        Vue.onMounted(() => {
            setTaxonVals();
        });
        
        return {
            acceptedTaxon,
            acceptedTaxonChangeValid,
            acceptedTaxonVal,
            isAccepted,
            parentTaxonChangeValid,
            parentTaxonVal,
            synonymArr,
            taxon,
            processAcceptedTaxonChange,
            processAcceptedTaxonNameChange,
            processMakeAccepted,
            processParentTaxonChange,
            processParentTaxonNameChange,
            setTaxonData
        }
    }
};

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
            <q-card v-if="taxaChildren.length > 0" flat bordered>
                <q-card-section class="column q-gutter-sm">
                    <div class="text-subtitle1 text-bold">Child Taxa Of This Taxon</div>
                    <div class="q-mt-xs q-pl-sm column q-gutter-xs">
                        <template v-for="child in taxaChildren">
                            <div class="row no-wrap">
                                <div class="text-subtitle1 text-italic">{{ child['sciname'] }}</div>
                                <div class="q-ml-sm">
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="setTaxonData(child['tid']);" icon="fas fa-edit" dense aria-label="Edit taxon" tabindex="0">
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
                    <template v-if="isAccepted && hasAcceptedChildren">
                        <div class="text-subtitle1 text-bold text-red">
                            This taxon has child taxa with a current taxonomic status of Accepted and therefore cannot have its acceptance changed.
                        </div>
                    </template>
                    <template v-else>
                        <div class="row">
                            <div class="col-grow">
                                <single-scientific-common-name-auto-complete :sciname="acceptedTaxonVal" label="Accepted Taxon" :limit-to-options="true" @update:sciname="processAcceptedTaxonNameChange" :disabled="isAccepted && hasAcceptedChildren"></single-scientific-common-name-auto-complete>
                            </div>
                        </div>
                        <div class="row justify-end q-gutter-sm">
                            <div>
                                <q-btn color="primary" @click="processAcceptedTaxonChange();" label="Change Accepted Taxon" :disabled="!acceptedTaxonChangeValid" tabindex="0" />
                            </div>
                            <div v-if="!isAccepted">
                                <q-btn color="primary" @click="changeUnacceptedToAccepted();" label="Change Status to Accepted" tabindex="0" />
                            </div>
                        </div>
                    </template>
                </q-card-section>
            </q-card>
        </div>
    `,
    components: {
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
    },
    setup() {
        const { hideWorking, showNotification, showWorking } = useCore();
        const taxaStore = useTaxaStore();

        const acceptedTaxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);
        const acceptedTaxonChangeValid = Vue.computed(() => {
            return ((isAccepted.value && Number(acceptedTaxonTid.value) > 0) || (!isAccepted.value && !acceptedTaxonTid.value) || (!isAccepted.value && Number(taxon.value['tidaccepted']) !== Number(acceptedTaxonTid.value)));
        });
        const acceptedTaxonFamily = Vue.ref(null);
        const acceptedTaxonKingdomId = Vue.ref(null);
        const acceptedTaxonTid = Vue.ref(null);
        const acceptedTaxonVal = Vue.ref(null);
        const hasAcceptedChildren = Vue.computed(() => taxaStore.getHasAcceptedChildren);
        const isAccepted = Vue.computed(() => taxaStore.getAccepted);
        const origAcceptedTaxonKingdomId = Vue.ref(null);
        const origParentTaxonKingdomId = Vue.ref(null);
        const parentTaxonChangeValid = Vue.computed(() => {
            return (Number(parentTaxonTid.value) > 0 && Number(taxon.value['parentTaxon']['tid']) !== Number(parentTaxonTid.value));
        });
        const parentTaxonFamily = Vue.ref(null);
        const parentTaxonKingdomId = Vue.ref(null);
        const parentTaxonTid = Vue.ref(null);
        const parentTaxonVal = Vue.ref(null);
        const synonymArr = Vue.computed(() => taxaStore.getTaxaSynonyms);
        const taxaChildren = Vue.computed(() => taxaStore.getTaxaChildren);
        const taxon = Vue.computed(() => taxaStore.getTaxaData);

        const setTaxonData = Vue.inject('setTaxonData');

        function changeAcceptedTaxon() {
            const remove = (isAccepted.value && Number(acceptedTaxonTid.value) > 0);
            taxaStore.updateTaxonEditData('kingdomid', acceptedTaxonKingdomId.value);
            taxaStore.updateTaxonEditData('tidaccepted', acceptedTaxonTid.value);
            taxaStore.updateTaxonEditData('family', acceptedTaxonFamily.value);
            if(taxaStore.getTaxaEditsExist){
                taxaStore.updateTaxonRecord((res) => {
                    if(res === 1){
                        showNotification('positive','Accepted taxon changed.');
                        quietSetTaxonData(taxon.value['tid']);
                        if(remove){
                            taxaStore.removeTaxonFromHierarchyData();
                        }
                    }
                    else{
                        showNotification('negative', 'There was an error changing the accepted taxon.');
                    }
                });
            }
        }

        function changeUnacceptedToAccepted() {
            taxaStore.updateTaxonEditData('tidaccepted', taxon.value['tid']);
            if(taxaStore.getTaxaEditsExist){
                taxaStore.updateTaxonRecord((res) => {
                    if(res === 1){
                        acceptedTaxonVal.value = null;
                        acceptedTaxonTid.value = null;
                        showNotification('positive','Taxon acceptance changed.');
                        quietSetTaxonData(taxon.value['tid']);
                        taxaStore.populateTaxonHierarchyData(taxon.value['tid']);
                    }
                    else{
                        showNotification('negative', 'There was an error changing the taxon acceptance.');
                    }
                });
            }
        }

        function processAcceptedTaxonChange() {
            if(!isAccepted.value && (!acceptedTaxonTid.value || Number(acceptedTaxonTid.value) === 0)){
                changeUnacceptedToAccepted();
            }
            else{
                changeAcceptedTaxon();
            }
        }

        function processAcceptedTaxonNameChange(taxonData) {
            if(taxonData){
                acceptedTaxonVal.value = taxonData['sciname'];
                acceptedTaxonTid.value = taxonData['tid'];
                acceptedTaxonFamily.value = taxonData['family'];
                acceptedTaxonKingdomId.value = taxonData['kingdomid'];
            }
            else{
                acceptedTaxonVal.value = null;
                acceptedTaxonTid.value = null;
                acceptedTaxonFamily.value = null;
                acceptedTaxonKingdomId.value = null;
            }
            if(acceptedTaxonKingdomId.value && Number(acceptedTaxonKingdomId.value) !== Number(origAcceptedTaxonKingdomId.value)) {
                showNotification('negative', 'The Accepted Taxon you entered is in a different kingdom than the current taxon. Please ensure it is correct.');
            }
        }

        function processParentTaxonChange() {
            showWorking();
            taxaStore.updateTaxonParent(parentTaxonTid.value, parentTaxonKingdomId.value, parentTaxonFamily.value, (res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Parent taxon saved.');
                    quietSetTaxonData(taxon.value['tid']);
                }
                else{
                    showNotification('negative', 'There was an error changing the parent taxon.');
                }
            });
        }

        function processParentTaxonNameChange(taxonData) {
            if(taxonData){
                parentTaxonVal.value = taxonData['sciname'];
                parentTaxonTid.value = taxonData['tid'];
                parentTaxonFamily.value = taxonData['family'];
                parentTaxonKingdomId.value = taxonData['kingdomid'];
            }
            else{
                parentTaxonVal.value = null;
                parentTaxonTid.value = null;
                parentTaxonFamily.value = null;
                parentTaxonKingdomId.value = null;
            }
            if(parentTaxonKingdomId.value && Number(parentTaxonKingdomId.value) !== Number(origParentTaxonKingdomId.value)) {
                showNotification('negative', 'The Parent Taxon you entered is in a different kingdom than the current taxon. Please ensure it is correct.');
            }
        }

        function quietSetTaxonData(tid) {
            taxaStore.setTaxon(tid, true);
        }

        function setTaxonVals() {
            if(Number(taxon.value['tid']) > 0){
                if(isAccepted.value){
                    acceptedTaxonTid.value = taxon.value['tid'];
                    acceptedTaxonFamily.value = taxon.value['family'];
                    acceptedTaxonKingdomId.value = taxon.value['kingdomid'];
                    origAcceptedTaxonKingdomId.value = taxon.value['kingdomid'];
                    if(Number(taxon.value['rankid']) > 10){
                        parentTaxonVal.value = taxon.value['parentTaxon']['sciname'];
                        parentTaxonTid.value = taxon.value['parentTaxon']['tid'];
                        parentTaxonFamily.value = taxon.value['parentTaxon']['family'];
                        parentTaxonKingdomId.value = taxon.value['parentTaxon']['kingdomid'];
                        origParentTaxonKingdomId.value = taxon.value['parentTaxon']['kingdomid'];
                    }
                }
                else{
                    acceptedTaxonVal.value = taxon.value['acceptedTaxon']['sciname'];
                    acceptedTaxonTid.value = taxon.value['acceptedTaxon']['tid'];
                    acceptedTaxonFamily.value = taxon.value['acceptedTaxon']['family'];
                    acceptedTaxonKingdomId.value = taxon.value['acceptedTaxon']['kingdomid'];
                    origAcceptedTaxonKingdomId.value = taxon.value['acceptedTaxon']['kingdomid'];
                    if(Number(taxon.value['acceptedTaxon']['rankid']) > 10){
                        parentTaxonVal.value = taxon.value['acceptedTaxon']['parentTaxon']['sciname'];
                        parentTaxonTid.value = taxon.value['acceptedTaxon']['parentTaxon']['tid'];
                        parentTaxonFamily.value = taxon.value['acceptedTaxon']['parentTaxon']['family'];
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
            hasAcceptedChildren,
            isAccepted,
            parentTaxonChangeValid,
            parentTaxonVal,
            synonymArr,
            taxaChildren,
            taxon,
            changeUnacceptedToAccepted,
            processAcceptedTaxonChange,
            processAcceptedTaxonNameChange,
            processParentTaxonChange,
            processParentTaxonNameChange,
            setTaxonData
        }
    }
};

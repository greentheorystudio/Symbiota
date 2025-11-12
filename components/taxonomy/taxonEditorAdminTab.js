const taxonEditorAdminTab = {
    template: `
        <div class="column q-gutter-md">
            <q-card flat bordered>
                <q-card-section class="column q-gutter-sm">
                    <div class="text-subtitle1 text-bold">Remap Resources to Another Taxon</div>
                    <div class="row">
                        <div class="col-grow">
                            <single-scientific-common-name-auto-complete :sciname="remapTaxonVal" label="Taxon" :limit-to-options="true" @update:sciname="processRemapTaxonNameChange"></single-scientific-common-name-auto-complete>
                        </div>
                    </div>
                    <div class="row justify-end q-gutter-sm">
                        <div>
                            <q-btn color="primary" @click="processRemap();" label="Remap Resources" :disabled="Number(remapTaxonTid) > 0" tabindex="0" />
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <q-card v-if="taxonUseData.hasOwnProperty('children')" flat bordered>
                <q-card-section class="column q-gutter-sm">
                    <div class="text-subtitle1 text-bold">Delete Taxon</div>
                    <div class="q-mt-xs q-pl-sm column q-gutter-sm">
                        <div v-if="!deleteValid" class="text-subtitle1 text-bold">
                            Taxon cannot be deleted until all child taxa, synonyms, checklists, images, media, common names, taxon descriptions, 
                            occurrences, and occurrence determinations have been removed.
                        </div>
                        <template v-if="taxonUseData['children'].length > 0">
                            <div class="column">
                                <div class="text-subtitle1 text-red text-bold">Children taxa exist for this taxon. They must be remapped before this taxon can be removed</div>
                                <template v-for="child in taxonUseData['children']">
                                    <div class="q-ml-md row no-wrap">
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
                        </template>
                        <template v-if="synonymArr.length > 0">
                            <div class="column">
                                <div class="text-subtitle1 text-red text-bold">Synonym links exist for this taxon. They must be remapped before this taxon can be removed</div>
                                <template v-for="synonym in synonymArr">
                                    <div class="q-ml-md row no-wrap">
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
                        </template>
                        <template v-if="taxonUseData['checklists'].length > 0">
                            <div class="column">
                                <div class="text-subtitle1 text-red text-bold">Linked checklists exist for this taxon. They must be unlinked before this taxon can be removed</div>
                                <template v-for="checklist in taxonUseData['checklists']">
                                    <div class="q-ml-md text-body1">
                                        <a :href="(clientRoot + '/checklists/checklist.php?clid=' + checklist.clid)" target="_blank" :aria-label="('Go to ' + checklist.name + ' - Opens in separate tab')" tabindex="0">
                                            {{ checklist.name }}
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template v-if="Number(taxonUseData['images']) > 0">
                            <div class="text-subtitle1 text-red text-bold">{{ taxonUseData['images'] }} images linked to this taxon</div>
                        </template>
                        <template v-if="Number(taxonUseData['media']) > 0">
                            <div class="text-subtitle1 text-red text-bold">{{ taxonUseData['media'] }} media records linked to this taxon</div>
                        </template>
                        <template v-if="Number(taxonUseData['vernacular']) > 0">
                            <div class="text-subtitle1 text-red text-bold">{{ taxonUseData['vernacular'] }} common names linked to this taxon</div>
                        </template>
                        <template v-if="Number(taxonUseData['description']) > 0">
                            <div class="text-subtitle1 text-red text-bold">{{ taxonUseData['description'] }} taxon descriptions linked to this taxon</div>
                        </template>
                        <template v-if="Number(taxonUseData['occurrences']) > 0">
                            <div class="text-subtitle1 text-red text-bold">{{ taxonUseData['occurrences'] }} occurrence records linked to this taxon</div>
                        </template>
                        <template v-if="Number(taxonUseData['determinations']) > 0">
                            <div class="text-subtitle1 text-red text-bold">{{ taxonUseData['determinations'] }} occurrence determination records linked to this taxon</div>
                        </template>
                        <div class="q-mt-md column">
                            <div class="row justify-end q-gutter-md">
                                <div>
                                    <q-btn color="negative" @click="deleteTaxon();" label="Delete Taxon" :disabled="!deleteValid" tabindex="0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
    },
    setup() {
        const { hideWorking, showNotification, showWorking } = useCore();
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const clientRoot = baseStore.getClientRoot;
        const confirmationPopupRef = Vue.ref(null);
        const deleteValid = Vue.computed(() => {
            return (
                taxonUseData.value['children'].length === 0 &&
                synonymArr.value.length === 0 &&
                taxonUseData.value['checklists'].length === 0 &&
                Number(taxonUseData.value['images']) === 0 &&
                Number(taxonUseData.value['media']) === 0 &&
                Number(taxonUseData.value['vernacular']) === 0 &&
                Number(taxonUseData.value['description']) === 0 &&
                Number(taxonUseData.value['occurrences']) === 0 &&
                Number(taxonUseData.value['determinations']) === 0
            );
        });
        const remapTaxonTid = Vue.ref(null);
        const remapTaxonVal = Vue.ref(null);
        const synonymArr = Vue.computed(() => taxaStore.getTaxaSynonyms);
        const taxon = Vue.computed(() => taxaStore.getTaxaData);
        const taxonUseData = Vue.computed(() => taxaStore.getTaxaUseData);

        function deleteTaxon() {
            const confirmText = 'Are you sure you want to delete this taxon? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    taxaStore.deleteTaxonRecord((res) => {
                        if(res === 1){
                            showNotification('positive','Taxon has been deleted.');
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the taxon.');
                        }
                    });
                }
            }});
        }

        function quietSetTaxonData(tid) {
            taxaStore.setTaxon(tid, true);
        }

        function processRemap() {
            showWorking();
            taxaStore.remapTaxonResources(remapTaxonTid.value, (res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Recources remapped successfully.');
                    taxaStore.setTaxaUseData();
                    quietSetTaxonData(taxon.value['tid']);
                }
                else{
                    showNotification('negative', 'There was an error remapping the resources.');
                }
            });
        }

        function processRemapTaxonNameChange(taxonData) {
            if(taxonData){
                remapTaxonVal.value = taxonData['sciname'];
                remapTaxonTid.value = taxonData['tid'];
            }
            else{
                remapTaxonVal.value = null;
                remapTaxonTid.value = null;
            }
            if(remapTaxonTid.value && Number(taxonData['kingdomid']) !== Number(taxon.value['kingdomid'])) {
                showNotification('negative', 'The taxon you entered is in a different kingdom than the current taxon. Please ensure it is correct.');
            }
        }

        function setTaxonData(tid) {
            taxaStore.setTaxon(tid, true);
        }
        
        return {
            clientRoot,
            confirmationPopupRef,
            deleteValid,
            remapTaxonVal,
            synonymArr,
            taxonUseData,
            deleteTaxon,
            processRemap,
            processRemapTaxonNameChange,
            setTaxonData
        }
    }
};

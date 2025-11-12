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
                            <q-btn color="primary" @click="processRemap();" label="Remap Resources" :disabled="!remapTaxonVal" tabindex="0" />
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <q-card v-if="taxonUseData.hasOwnProperty('children')" flat bordered>
                <q-card-section class="column q-gutter-sm">
                    <div class="text-subtitle1 text-bold">Delete Taxon</div>
                    <div class="q-mt-xs q-pl-sm column q-gutter-sm">
                        <template v-if="taxonUseData['children'].length > 0">
                            <div class="column">
                                <div class="text-subtitle1 text-red text-bold">Warning: children taxa exist for this taxon. They must be remapped before this taxon can be removed</div>
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
                        <template v-else>
                            <div class="text-subtitle1 text-green text-bold">Approved: no children taxa are linked to this taxon</div>
                        </template>
                        <template v-if="synonymArr.length > 0">
                            <div class="column">
                                <div class="text-subtitle1 text-red text-bold">Warning: synonym links exist for this taxon. They must be remapped before this taxon can be removed</div>
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
                        <template v-else>
                            <div class="text-subtitle1 text-green text-bold">Approved: no synonyms are linked to this taxon</div>
                        </template>
                        <template v-if="taxonUseData['checklists'].length > 0">
                            <div class="column">
                                <div class="text-subtitle1 text-red text-bold">Warning: linked checklists exist for this taxon. They must be unlinked before this taxon can be removed</div>
                                <template v-for="checklist in taxonUseData['checklists']">
                                    <div class="q-ml-md text-body1">
                                        <a :href="(clientRoot + '/checklists/checklist.php?clid=' + checklist.clid)" target="_blank" :aria-label="('Go to ' + checklist.name + ' - Opens in separate tab')" tabindex="0">
                                            {{ checklist.name }}
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template v-else>
                            <div class="text-subtitle1 text-green text-bold">Approved: no checklists linked to this taxon</div>
                        </template>
                        <template v-if="Number(taxonUseData['images']) > 0">
                            <div class="text-subtitle1 text-red text-bold">Warning: {{ taxonUseData['images'] }} images linked to this taxon</div>
                        </template>
                        <template v-else>
                            <div class="text-subtitle1 text-green text-bold">Approved: no images linked to this taxon</div>
                        </template>
                        <template v-if="Number(taxonUseData['media']) > 0">
                            <div class="text-subtitle1 text-red text-bold">Warning: {{ taxonUseData['media'] }} media records linked to this taxon</div>
                        </template>
                        <template v-else>
                            <div class="text-subtitle1 text-green text-bold">Approved: no media records linked to this taxon</div>
                        </template>
                        <template v-if="Number(taxonUseData['vernacular']) > 0">
                            <div class="text-subtitle1 text-red text-bold">Warning: {{ taxonUseData['vernacular'] }} common names linked to this taxon</div>
                        </template>
                        <template v-else>
                            <div class="text-subtitle1 text-green text-bold">Approved: no common names linked to this taxon</div>
                        </template>
                        <template v-if="Number(taxonUseData['description']) > 0">
                            <div class="text-subtitle1 text-red text-bold">Warning: {{ taxonUseData['description'] }} taxon descriptions linked to this taxon</div>
                        </template>
                        <template v-else>
                            <div class="text-subtitle1 text-green text-bold">Approved: no taxon descriptions linked to this taxon</div>
                        </template>
                        <template v-if="Number(taxonUseData['occurrences']) > 0">
                            <div class="text-subtitle1 text-red text-bold">Warning: {{ taxonUseData['occurrences'] }} occurrence records linked to this taxon</div>
                        </template>
                        <template v-else>
                            <div class="text-subtitle1 text-green text-bold">Approved: no occurrence records linked to this taxon</div>
                        </template>
                        <template v-if="Number(taxonUseData['determinations']) > 0">
                            <div class="text-subtitle1 text-red text-bold">Warning: {{ taxonUseData['determinations'] }} occurrence determination records linked to this taxon</div>
                        </template>
                        <template v-else>
                            <div class="text-subtitle1 text-green text-bold">Approved: no occurrence determination records linked to this taxon</div>
                        </template>
                        <div class="q-mt-md column">
                            <div v-if="!deleteValid" class="text-subtitle1 text-red text-bold">
                                Taxon cannot be deleted until all child taxa, synonyms, checklists, images, media, common names, taxon descriptions, 
                                occurrences, and occurrence determinations have been removed.
                            </div>
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
        const { showNotification } = useCore();
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

        function processRemap() {

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

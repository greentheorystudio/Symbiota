const taxonProfileEditorVernacularTab = {
    template: `
        <div class="column q-gutter-sm">
            <div class="full-width row justify-end items-center">
                <div>
                    <q-btn color="primary" @click="openVernacularEditorPopup(0);" label="Add Common Name" tabindex="0" />
                </div>
            </div>
            <template v-if="vernacularArr.length > 0">
                <template v-for="vernacular in vernacularArr">
                    <q-card>
                        <q-card-section class="column q-gutter-sm">
                            <div class="row justify-between">
                                <div class="column">
                                    <div class="text-subtitle1 text-bold">{{ vernacular['vernacularname'] }}</div>
                                    <div><span class="text-subtitle1 text-bold">Language: </span>{{ vernacular['language'] }}</div>
                                    <div><span class="text-subtitle1 text-bold">Notes: </span>{{ vernacular['notes'] }}</div>
                                    <div><span class="text-subtitle1 text-bold">Source: </span>{{ vernacular['source'] }}</div>
                                </div>
                                <div class="column q-gutter-sm">
                                    <div class="row justify-end">
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openVernacularEditorPopup(vernacular['vid']);" icon="fas fa-edit" dense aria-label="Edit description block record" tabindex="0">
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Edit common name record
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                            </div>
                        </q-card-section>
                    </q-card>
                </template>
            </template>
            <template v-else>
                <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                <div class="q-pa-md row justify-center text-h6 text-bold">
                    There are no common names for this taxon yet, click the Add Description Block button above to add the first one
                </div>
            </template>
        </div>
        <template v-if="showVernacularEditorPopup">
            <taxon-profile-editor-vernacular-editor-popup
                :vernacular-id="editVernacularId"
                :show-popup="showVernacularEditorPopup"
                @close:popup="showVernacularEditorPopup = false"
            ></taxon-profile-editor-vernacular-editor-popup>
        </template>
    `,
    components: {
        'taxon-profile-editor-vernacular-editor-popup': taxonProfileEditorVernacularEditorPopup
    },
    setup() {
        const taxaStore = useTaxaStore();

        const editVernacularId = Vue.ref(0);
        const showVernacularEditorPopup = Vue.ref(false);
        const vernacularArr = Vue.computed(() => taxaStore.getTaxaVernacularArr);
        
        function openVernacularEditorPopup(blockid) {
            editVernacularId.value = blockid;
            showVernacularEditorPopup.value = true;
        }

        return {
            editVernacularId,
            showVernacularEditorPopup,
            vernacularArr,
            openVernacularEditorPopup
        }
    }
};

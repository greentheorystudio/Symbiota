const spatialRecordsSymbologyExpansion = {
    template: `
        <div>
            <q-separator></q-separator>
            <q-tabs v-model="mapSettings.selectedRecordsSelectionsSymbologyTab" active-bg-color="grey-4" align="left" @update:model-value="(value) => changeSelectedTab(value)">
                <q-tab name="records" class="bg-grey-3" label="Records" no-caps />
                <template v-if="selections.length > 0">
                    <q-tab name="select" class="bg-grey-3" label="Selections" no-caps />
                </template>
                <q-tab name="symbology" class="bg-grey-3" label="Symbology" no-caps />
            </q-tabs>
            <q-separator></q-separator>
            <q-tab-panels v-model="mapSettings.selectedRecordsSelectionsSymbologyTab">
                <q-tab-panel name="records" class="q-pa-none">
                    <spatial-records-tab></spatial-records-tab>
                </q-tab-panel>
                <template v-if="selections.length > 0">
                    <q-tab-panel name="select" class="q-pa-none">
                        <spatial-selections-tab></spatial-selections-tab>
                    </q-tab-panel>
                </template>
                <q-tab-panel name="symbology">
                    <spatial-symbology-tab></spatial-symbology-tab>
                </q-tab-panel>
            </q-tab-panels>
        </div>
    `,
    components: {
        'spatial-records-tab': spatialRecordsTab,
        'spatial-selections-tab': spatialSelectionsTab,
        'spatial-symbology-tab': spatialSymbologyTab
    },
    setup() {
        const searchStore = useSearchStore();

        const mapSettings = Vue.inject('mapSettings');
        const selections = searchStore.getSelections;

        const updateMapSettings = Vue.inject('updateMapSettings');

        function changeSelectedTab(value) {
            updateMapSettings('selectedRecordsSelectionsSymbologyTab', value);
        }

        return {
            mapSettings,
            selections,
            changeSelectedTab
        }
    }
};

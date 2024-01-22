const spatialSearchCriteriaExpansion = {
    template: `
        <div>
            <q-separator ></q-separator>
            <q-tabs v-model="mapSettings.selectedSearchCriteriaTab" active-bg-color="grey-4" align="left" @update:model-value="(value) => changeSelectedTab(value)">
                <q-tab name="criteria" class="bg-grey-3" label="Criteria" no-caps />
                <q-tab name="collections" class="bg-grey-3" label="Collections" no-caps />
            </q-tabs>
            <q-separator></q-separator>
            <q-tab-panels v-model="mapSettings.selectedSearchCriteriaTab">
                <q-tab-panel name="criteria">
                    <spatial-search-criteria-tab></spatial-search-criteria-tab>
                </q-tab-panel>
                <q-tab-panel name="collections">
                    <spatial-search-collections-tab></spatial-search-collections-tab>
                </q-tab-panel>
            </q-tab-panels>
        </div>
    `,
    components: {
        'spatial-search-collections-tab': spatialSearchCollectionsTab,
        'spatial-search-criteria-tab': spatialSearchCriteriaTab
    },
    setup() {
        const searchStore = Vue.inject('searchStore');
        const mapSettings = Vue.inject('mapSettings');

        const updateMapSettings = Vue.inject('updateMapSettings');

        function changeSelectedTab(value) {
            updateMapSettings('selectedSearchCriteriaTab', value);
        }

        return {
            mapSettings,
            changeSelectedTab
        }
    }
};

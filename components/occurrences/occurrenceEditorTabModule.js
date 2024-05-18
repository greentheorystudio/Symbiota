const occurrenceEditorTabModule = {
    template: `
        <template v-if="isLocked">
            <q-card dark bordered class="bg-red-9">
                <q-card-section>
                    <div class="text-h6">This record is locked</div>
                    <div class="text-subtitle2">This record is locked for editing by another user. Once the user is done with the record, the lock will be removed. Records are locked for a minimum of 15 minutes.</div>
                </q-card-section>
            </q-card>
        </template>
        <template v-else>
            <div class="rounded-borders overflow-hidden">
                <q-tabs v-model="selectedTab" active-bg-color="grey-4" align="left" class="bg-grey-3" :style="tabPanelStyle">
                    <q-tab name="data" class="bg-grey-3" label="Occurrence Data" no-caps />
                    <template v-if="Object.keys(configuredDataFields).length > 0">
                        <q-tab name="configured" class="bg-grey-3" :label="configuredDataLabel" no-caps />
                    </template>
                    <q-tab name="determinations" class="bg-grey-3" label="Determination History" no-caps />
                    <q-tab name="images" class="bg-grey-3" label="Images" no-caps />
                    <q-tab name="media" class="bg-grey-3" label="Media" no-caps />
                    <q-tab name="resources" class="bg-grey-3" label="Linked Resources" no-caps />
                    <q-tab name="admin" class="bg-grey-3" label="Admin" no-caps />
                </q-tabs>
                <q-separator></q-separator>
                <q-tab-panels v-model="selectedTab">
                    <q-tab-panel name="data" class="q-pa-none">
                        <occurrence-editor-occurrence-data-module></occurrence-editor-occurrence-data-module>
                    </q-tab-panel>
                    <template v-if="Object.keys(configuredDataFields).length > 0">
                        <q-tab-panel name="configured">
                            <configured-data-field-module></configured-data-field-module>
                        </q-tab-panel>
                    </template>
                    <q-tab-panel name="determinations">
                        <occurrence-editor-determinations-tab></occurrence-editor-determinations-tab>
                    </q-tab-panel>
                    <q-tab-panel name="images">
                        <occurrence-editor-images-tab></occurrence-editor-images-tab>
                    </q-tab-panel>
                    <q-tab-panel name="media">
                        <occurrence-editor-media-tab></occurrence-editor-media-tab>
                    </q-tab-panel>
                    <q-tab-panel name="resources">
                        <occurrence-editor-resources-tab></occurrence-editor-resources-tab>
                    </q-tab-panel>
                    <q-tab-panel name="admin">
                        <occurrence-editor-admin-tab></occurrence-editor-admin-tab>
                    </q-tab-panel>
                </q-tab-panels>
            </div>
        </template>
    `,
    components: {
        'configured-data-field-module': configuredDataFieldModule,
        'occurrence-editor-admin-tab': occurrenceEditorAdminTab,
        'occurrence-editor-determinations-tab': occurrenceEditorDeterminationsTab,
        'occurrence-editor-images-tab': occurrenceEditorImagesTab,
        'occurrence-editor-media-tab': occurrenceEditorMediaTab,
        'occurrence-editor-occurrence-data-module': occurrenceEditorOccurrenceDataModule,
        'occurrence-editor-resources-tab': occurrenceEditorResourcesTab
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const collInfo = Vue.computed(() => occurrenceStore.getCollectionData);
        const configuredDataFields = Vue.computed(() => occurrenceStore.getConfiguredDataFields);
        const configuredDataLabel = Vue.computed(() => occurrenceStore.getConfiguredDataLabel);
        const containerWidth = Vue.inject('containerWidth');
        const isLocked = Vue.computed(() => occurrenceStore.getIsLocked);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const selectedTab = Vue.ref('data');
        const tabPanelStyle = Vue.ref('');

        Vue.watch(containerWidth, () => {
            setTabModuleWidth();
        });

        function setTabModuleWidth() {
            tabPanelStyle.value = 'width: ' + (containerWidth.value - 34) + 'px;';
        }

        Vue.onMounted(() => {
            setTabModuleWidth();
        });

        return {
            collInfo,
            configuredDataFields,
            configuredDataLabel,
            isLocked,
            occurrenceData,
            selectedTab,
            tabPanelStyle
        }
    }
};

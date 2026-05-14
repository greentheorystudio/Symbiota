const occurrenceEditorFormLocationElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <template v-if="occId > 0 && Object.keys(configuredDataFields).length > 0">
                    <div class="row justify-between">
                        <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                            Location
                        </div>
                        <div class="row justify-end q-gutter-sm">
                            <q-btn color="secondary" @click="showConfiguredDataEditorPopup = true" :label="configuredDataLabel" tabindex="0" />
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                        Location
                    </div>
                </template>
                <location-field-module :data="occurrenceData" :fields="occurrenceFields" :field-definitions="occurrenceFieldDefinitions" @update:location-data="(data) => updateOccurrenceData(data.key, data.value)"></location-field-module>
            </q-card-section>
        </q-card>
        <template v-if="showConfiguredDataEditorPopup">
            <mof-data-editor-popup
                data-type="location"
                :new-record="Number(locationId) === 0"
                :show-popup="showConfiguredDataEditorPopup"
                @close:popup="showConfiguredDataEditorPopup = false"
            ></mof-data-editor-popup>
        </template>
    `,
    components: {
        'location-field-module': locationFieldModule,
        'mof-data-editor-popup': mofDataEditorPopup
    },
    setup() {
        const occurrenceStore = useOccurrenceStore();

        const configuredDataFields = Vue.computed(() => occurrenceStore.getLocationMofDataFields);
        const configuredDataLabel = Vue.computed(() => occurrenceStore.getLocationMofDataLabel);
        const locationId = Vue.computed(() => occurrenceStore.getLocationID);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceFields = Vue.computed(() => occurrenceStore.getOccurrenceFields);
        const occurrenceFieldDefinitions = Vue.computed(() => occurrenceStore.getOccurrenceFieldDefinitions);
        const showConfiguredDataEditorPopup = Vue.ref(false);

        function updateOccurrenceData(key, value) {
            occurrenceStore.updateOccurrenceEditData(key, value);
        }

        return {
            configuredDataFields,
            configuredDataLabel,
            locationId,
            occId,
            occurrenceData,
            occurrenceFields,
            occurrenceFieldDefinitions,
            showConfiguredDataEditorPopup,
            updateOccurrenceData
        }
    }
};

const checklistEditorAppConfigTab = {
    template: `
        <div class="q-pa-md column q-gutter-sm">
            <div class="row justify-end q-gutter-sm">
                <div v-if="checklistData['appconfigjson'] && checklistData['appconfigjson'].hasOwnProperty('dataArchiveUrl') && checklistData['appconfigjson']['dataArchiveUrl']">
                    <q-btn color="negative" @click="deleteAppData();" label="Delete App Data" aria-label="Delete App Data" tabindex="0" />
                </div>
                <div>
                    <q-btn color="secondary" @click="prepareAppData();" label="Prepare/Update App Data" :disabled="!checklistValid" aria-label="Prepare and Update App Data" tabindex="0" />
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <taxon-description-source-tab-auto-complete :value="(checklistData['appconfigjson'] && checklistData['appconfigjson'].hasOwnProperty('descSourceTab')) ? checklistData['appconfigjson']['descSourceTab'] : null" @update:value="(value) => updateAppConfigData('descSourceTab', value)"></taxon-description-source-tab-auto-complete>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="int" label="Max Amount of Autoload Images Per Taxon" :value="(checklistData['appconfigjson'] && checklistData['appconfigjson'].hasOwnProperty('maxImagesPerTaxon')) ? checklistData['appconfigjson']['maxImagesPerTaxon'] : null" min-value="0"  @update:value="(value) => updateAppConfigData('maxImagesPerTaxon', value)"></text-field-input-element>
                </div>
            </div>
        </div>
    `,
    components: {
        'taxon-description-source-tab-auto-complete': taxonDescriptionSourceTabAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const checklistStore = useChecklistStore();

        const checklistData = Vue.computed(() => checklistStore.getChecklistData);

        function updateAppConfigData(key, value) {
            checklistStore.updateChecklistEditAppConfigData(key, value);
        }

        return {
            checklistData,
            updateAppConfigData
        }
    }
};

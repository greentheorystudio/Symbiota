const collectionDataUploadParametersFieldModule = {
    props: {
        disabled: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <div class="column q-col-gutter-sm">
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <selector-input-element :disabled="disabled" label="Upload Type" :options="uploadTypeOptions" :value="profileData.uploadtype" @update:value="(value) => updateData('uploadtype', value)"></selector-input-element>
                </div>
            </div>
            <div v-if="Number(profileData.uploadtype) === 8 || Number(profileData.uploadtype) === 10" class="row q-col-gutter-sm">
                <div class="col-grow">
                    <text-field-input-element :disabled="disabled" data-type="textarea" label="URL" :value="profileData.dwcpath" @update:value="(value) => updateData('dwcpath', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <selector-input-element :disabled="disabled" label="Existing Occurrence Records" :options="existingRecordOptions" :value="configurationData.existingRecords" @update:value="(value) => updateConfigurationData('existingRecords', value)"></selector-input-element>
                </div>
            </div>
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <selector-input-element :disabled="disabled" label="Existing Identification Records" :options="existingAssociatedDataOptions" :value="configurationData.existingDeterminationRecords" @update:value="(value) => updateConfigurationData('existingDeterminationRecords', value)"></selector-input-element>
                </div>
            </div>
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <selector-input-element :disabled="disabled" label="Existing Media Records" :options="existingAssociatedDataOptions" :value="configurationData.existingMediaRecords" @update:value="(value) => updateConfigurationData('existingMediaRecords', value)"></selector-input-element>
                </div>
            </div>
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <selector-input-element :disabled="disabled" label="Existing Measurement or Fact Records" :options="existingAssociatedDataOptions" :value="configurationData.existingMofRecords" @update:value="(value) => updateConfigurationData('existingMofRecords', value)"></selector-input-element>
                </div>
            </div>
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <checkbox-input-element :disabled="disabled" label="Save primary identifiers from source data" :value="configurationData.saveSourcePrimaryIdentifier" @update:value="(value) => updateConfigurationData('saveSourcePrimaryIdentifier', value)"></checkbox-input-element>
                </div>
            </div>
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <checkbox-input-element :disabled="disabled" label="Match by Catalog or Other Catalog Number" :value="configurationData.matchOnCatalogNumber" @update:value="(value) => updateConfigurationData('matchOnCatalogNumber', value)"></checkbox-input-element>
                </div>
            </div>
            <div v-if="configurationData.matchOnCatalogNumber" class="row q-col-gutter-sm">
                <div class="col-grow">
                    <selector-input-element :disabled="disabled" label="Match Field" :options="catalogNumberMatchOptions" :value="configurationData.catalogNumberMatchField" @update:value="(value) => updateConfigurationData('catalogNumberMatchField', value)"></selector-input-element>
                </div>
            </div>
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <checkbox-input-element :disabled="disabled" label="Remove Previous Records Not Included in Upload" :value="configurationData.removeUnmatchedRecords" @update:value="(value) => updateConfigurationData('removeUnmatchedRecords', value)"></checkbox-input-element>
                </div>
            </div>
        </div>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const collectionDataUploadParametersStore = useCollectionDataUploadParametersStore();

        const catalogNumberMatchOptions = [
            {value: 'catalognumber', label: 'Catalog Number'},
            {value: 'othercatalognumbers', label: 'Other Catalog Numbers'}
        ];
        const configurationData = Vue.computed(() => collectionDataUploadParametersStore.getConfigurations);
        const existingAssociatedDataOptions = [
            {value: 'merge', label: 'Import new records while leaving existing records'},
            {value: 'replace', label: 'Replace existing records with new records'}
        ];
        const existingRecordOptions = [
            {value: 'update', label: 'Update existing records (Replaces records with incoming records)'},
            {value: 'skip', label: 'Skip existing records (Do not update)'}
        ];
        const profileData = Vue.computed(() => collectionDataUploadParametersStore.getCollectionDataUploadParametersData);
        const uploadTypeOptions = Vue.computed(() => collectionDataUploadParametersStore.getUploadTypeOptions);

        function updateConfigurationData(key, value) {
            const config = Object.assign({}, configurationData.value);
            config[key] = value;
            collectionDataUploadParametersStore.updateCollectionDataUploadParametersEditData('configjson', config);
        }

        function updateData(key, value) {
            collectionDataUploadParametersStore.updateCollectionDataUploadParametersEditData(key, value);
        }

        return {
            catalogNumberMatchOptions,
            configurationData,
            existingAssociatedDataOptions,
            existingRecordOptions,
            profileData,
            uploadTypeOptions,
            updateConfigurationData,
            updateData
        }
    }
};

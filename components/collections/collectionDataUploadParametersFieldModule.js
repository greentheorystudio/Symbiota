const collectionDataUploadParametersFieldModule = {
    template: `
        <div class="column q-col-gutter-sm">
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <selector-input-element label="Upload Type" :options="uploadTypeOptions" :value="profileData.uploadtype" @update:value="(value) => updateData('uploadtype', value)"></selector-input-element>
                </div>
            </div>
            <div v-if="Number(profileData.uploadtype) === 8 || Number(profileData.uploadtype) === 10" class="row q-col-gutter-sm">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="URL" :value="profileData.dwcpath" @update:value="(value) => updateData('dwcpath', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <selector-input-element label="Existing Records" :options="existingRecordOptions" :value="configurationData.existingRecords" @update:value="(value) => updateConfigurationData('existingRecords', value)"></selector-input-element>
                </div>
            </div>
        </div>
    `,
    components: {
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const collectionDataUploadParametersStore = useCollectionDataUploadParametersStore();

        const configurationData = Vue.computed(() => collectionDataUploadParametersStore.getConfigurations);
        const existingRecordOptions = [
            {value: 'update', label: 'Update existing records (Replaces records with incoming records)'},
            {value: 'skip', label: 'Skip existing records (Do not update)'}
        ];
        const profileData = Vue.computed(() => collectionDataUploadParametersStore.getCollectionDataUploadParametersData);
        const uploadTypeOptions = Vue.computed(() => collectionDataUploadParametersStore.getUploadTypeOptions);

        function updateConfigurationData(key, value) {
            const config = Object.assign({}, configurationData.value);
            config[key] = value;
            collectionDataUploadParametersStore.updateCollectionDataUploadParametersEditData('configjson', JSON.stringify(config));
        }

        function updateData(key, value) {
            collectionDataUploadParametersStore.updateCollectionDataUploadParametersEditData(key, value);
        }

        return {
            configurationData,
            existingRecordOptions,
            profileData,
            uploadTypeOptions,
            updateConfigurationData,
            updateData
        }
    }
};

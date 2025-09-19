const collectionMediaUploadParametersFieldModule = {
    template: `
        <div class="column q-col-gutter-sm">
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <text-field-input-element label="Filename RegEx Pattern" :value="profileData.filenamepatternmatch" @update:value="(value) => updateData('filenamepatternmatch', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <selector-input-element label="Filename Identifier Field" :options="patternMatchFieldOptions" :value="profileData.patternmatchfield" @update:value="(value) => updateData('patternmatchfield', value)"></selector-input-element>
                </div>
            </div>
            <div class="row q-col-gutter-sm">
                <div class="col-grow">
                    <checkbox-input-element label="Create New Occurrence Record" :value="configurationData.createOccurrence" @update:value="(value) => updateConfigurationData('createOccurrence', value)"></checkbox-input-element>
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
        const collectionMediaUploadParametersStore = useCollectionMediaUploadParametersStore();

        const configurationData = Vue.computed(() => collectionMediaUploadParametersStore.getConfigurations);
        const patternMatchFieldOptions = [
            {value: 'catalognumber', label: 'Catalog Number'},
            {value: 'othercatalognumbers', label: 'Other Catalog Numbers'}
        ];
        const profileData = Vue.computed(() => collectionMediaUploadParametersStore.getCollectionMediaUploadParametersData);

        function updateConfigurationData(key, value) {
            const config = Object.assign({}, configurationData.value);
            config[key] = value;
            collectionMediaUploadParametersStore.updateCollectionMediaUploadParametersEditData('configjson', JSON.stringify(config));
        }

        function updateData(key, value) {
            collectionMediaUploadParametersStore.updateCollectionMediaUploadParametersEditData(key, value);
        }

        return {
            configurationData,
            patternMatchFieldOptions,
            profileData,
            updateConfigurationData,
            updateData
        }
    }
};

const spatialActiveLayerSelector = {
    template: `
        <q-select bg-color="white" outlined v-model="selectedOption" :options="dropdownOptions" :option-value="value" :option-label="label" label="Active Layer" popup-content-class="z-max" @update:model-value="changeActiveLayer" behavior="menu" dense options-dense />
    `,
    setup(props) {
        const activeLayerSelectorOptions = Vue.inject('activeLayerSelectorOptions');
        const dropdownOptions = Vue.ref([]);
        const mapSettings = Vue.inject('mapSettings');
        const selectedOption = Vue.ref('');

        const updateMapSettings = Vue.inject('updateMapSettings');

        Vue.watch(activeLayerSelectorOptions, () => {
            setOptions();
        });

        function changeActiveLayer(val) {
            updateMapSettings('activeLayer', val.value);
        }

        function setOptions() {
            dropdownOptions.value = [];
            activeLayerSelectorOptions.forEach(option => {
                dropdownOptions.value.push(option);
            });
            setSelectedOption();
        }

        function setSelectedOption() {
            selectedOption.value = activeLayerSelectorOptions.find(opt => opt['value'] === mapSettings.activeLayer);
        }

        Vue.onMounted(() => {
            setOptions();
        });

        return {
            dropdownOptions,
            selectedOption,
            changeActiveLayer
        }
    }
};

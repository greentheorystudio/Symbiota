const occurrenceEntryFormatSelector = {
    props: {
        selectedFormat: {
            type: String,
            default: 'specimen'
        },
        tabindex: {
            type: Number,
            default: 1
        }
    },
    template: `
        <q-select class="selector-input-spacer" bg-color="white" outlined v-model="selectedOption" :options="formatSelectorOptions" option-value="value" option-label="label" label="Entry Format" popup-content-class="z-max" @update:model-value="changeEntryFormat" behavior="menu" :tabindex="tabindex" dense options-dense />
    `,
    setup(props, context) {
        const formatSelectorOptions = [
            {value: 'specimen', label: 'Specimen'},
            {value: 'observation', label: 'Observation'},
            {value: 'skeletal', label: 'Skeletal'},
            {value: 'lot', label: 'Lot'},
            {value: 'benthic', label: 'Benthic'}
        ];
        const propsRefs = Vue.toRefs(props);
        const selectedOption = Vue.ref({});

        Vue.watch(propsRefs.selectedFormat, () => {
            setSelectedOption();
        });

        function changeEntryFormat(val) {
            selectedOption.value = val;
            context.emit('change-occurrence-entry-format', val.value);
        }

        function setSelectedOption() {
            selectedOption.value = formatSelectorOptions.find(opt => opt['value'] === props.selectedFormat);
        }

        Vue.onMounted(() => {
            setSelectedOption();
        });

        return {
            formatSelectorOptions,
            selectedOption,
            changeEntryFormat
        }
    }
};
